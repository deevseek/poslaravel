<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Services\FaceRecognitionService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function __construct(private readonly FaceRecognitionService $faceRecognition)
    {
    }

    public function index(): View
    {
        $attendances = Attendance::with('employee')
            ->orderByDesc('attendance_date')
            ->orderByDesc('check_in_time')
            ->paginate(10);

        return view('attendances.index', compact('attendances'));
    }

    public function create(): View
    {
        $employees = Employee::where('is_active', true)->orderBy('name')->get();

        return view('attendances.create', compact('employees'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => [
                'nullable',
                'exists:employees,id',
            ],
            'attendance_date' => ['required', 'date'],
            'check_in_time' => ['required', 'date_format:H:i'],
            'check_out_time' => ['nullable', 'date_format:H:i', 'after_or_equal:check_in_time'],
            'note' => ['nullable', 'string', 'max:500'],
            'face_recognition_snapshot' => ['required', 'string', 'regex:/^data:image\\/(png|jpeg|jpg|webp);base64,/'],
        ]);

        $temporarySnapshotPath = $this->storeTemporarySnapshot($validated['face_recognition_snapshot']);

        if (! $temporarySnapshotPath) {
            return back()
                ->withErrors(['face_recognition_snapshot' => 'Snapshot wajah tidak valid. Silakan ulangi pemindaian.'])
                ->withInput();
        }

        $temporarySnapshotFullPath = Storage::disk('local')->path($temporarySnapshotPath);

        try {
            $employee = $validated['employee_id']
                ? Employee::find($validated['employee_id'])
                : $this->matchEmployeeByFaceSnapshot($temporarySnapshotFullPath);

            if (! $employee) {
                return back()
                    ->withErrors(['face_recognition_snapshot' => 'Pengenalan wajah tidak cocok dengan data terdaftar.'])
                    ->withInput();
            }

            if (! $employee->face_recognition_scan_path || ! $employee->face_recognition_registered_at) {
                return back()
                    ->withErrors(['employee_id' => 'Pengenalan wajah karyawan belum terdaftar. Silakan daftar terlebih dahulu di profil karyawan.'])
                    ->withInput();
            }

            $referencePath = Storage::disk('public')->path($employee->face_recognition_scan_path);
            if (! $this->faceRecognition->matchSnapshot($referencePath, $employee->face_recognition_signature, $temporarySnapshotFullPath)) {
                return back()
                    ->withErrors(['face_recognition_snapshot' => 'Pengenalan wajah tidak cocok dengan data terdaftar.'])
                    ->withInput();
            }
        } finally {
            Storage::disk('local')->delete($temporarySnapshotPath);
        }

        $existingAttendance = Attendance::where('employee_id', $employee->id)
            ->where('attendance_date', $validated['attendance_date'])
            ->exists();

        if ($existingAttendance) {
            return back()
                ->withErrors(['employee_id' => 'Absensi untuk karyawan dan tanggal tersebut sudah ada.'])
                ->withInput();
        }

        $checkIn = Carbon::createFromFormat('H:i', $validated['check_in_time']);
        $status = $checkIn->greaterThan(Carbon::createFromFormat('H:i', '09:00')) ? 'Terlambat' : 'Hadir';

        Attendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => $validated['attendance_date'],
            'check_in_time' => $validated['check_in_time'],
            'check_out_time' => $validated['check_out_time'] ?? null,
            'method' => 'face_recognition',
            'status' => $status,
            'note' => $validated['note'] ?? null,
        ]);

        return redirect()->route('attendances.index')->with('success', 'Absensi berhasil dicatat dengan metode face recognition via webcam.');
    }

    public function show(Attendance $attendance): View
    {
        $attendance->load('employee');

        return view('attendances.show', compact('attendance'));
    }

    public function identify(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'face_recognition_snapshot' => ['required', 'string', 'regex:/^data:image\\/(png|jpeg|jpg|webp);base64,/'],
            'attendance_date' => ['nullable', 'date'],
        ]);

        $temporarySnapshotPath = $this->storeTemporarySnapshot($validated['face_recognition_snapshot']);

        if (! $temporarySnapshotPath) {
            return response()->json(['message' => 'Snapshot wajah tidak valid. Silakan ulangi pemindaian.'], 422);
        }

        $temporarySnapshotFullPath = Storage::disk('local')->path($temporarySnapshotPath);

        try {
            $employee = $this->matchEmployeeByFaceSnapshot($temporarySnapshotFullPath);

            if (! $employee) {
                return response()->json(['message' => 'Pengenalan wajah tidak cocok dengan data terdaftar.'], 404);
            }

            $alreadyAttended = false;
            if (! empty($validated['attendance_date'])) {
                $alreadyAttended = Attendance::where('employee_id', $employee->id)
                    ->where('attendance_date', $validated['attendance_date'])
                    ->exists();
            }

            return response()->json([
                'employee' => [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'position' => $employee->position,
                ],
                'already_attended' => $alreadyAttended,
            ]);
        } finally {
            Storage::disk('local')->delete($temporarySnapshotPath);
        }
    }

    protected function matchEmployeeByFaceSnapshot(string $snapshotPath): ?Employee
    {
        $employees = Employee::query()
            ->where('is_active', true)
            ->whereNotNull('face_recognition_scan_path')
            ->whereNotNull('face_recognition_registered_at')
            ->get();

        foreach ($employees as $employee) {
            $referencePath = Storage::disk('public')->path($employee->face_recognition_scan_path);
            if ($this->faceRecognition->matchSnapshot($referencePath, $employee->face_recognition_signature, $snapshotPath)) {
                return $employee;
            }
        }

        return null;
    }

    private function storeTemporarySnapshot(string $snapshot): ?string
    {
        if (! preg_match('/^data:image\\/(png|jpeg|jpg|webp);base64,/', $snapshot)) {
            return null;
        }

        $extension = match (true) {
            str_contains($snapshot, 'image/jpeg') => 'jpg',
            str_contains($snapshot, 'image/jpg') => 'jpg',
            str_contains($snapshot, 'image/webp') => 'webp',
            default => 'png',
        };

        $encodedImage = substr($snapshot, strpos($snapshot, ',') + 1);
        $decodedImage = base64_decode($encodedImage, true);

        if ($decodedImage === false) {
            return null;
        }

        $filename = 'face-recognition-temp/' . Str::uuid() . '.' . $extension;
        Storage::disk('local')->put($filename, $decodedImage);

        return $filename;
    }
}
