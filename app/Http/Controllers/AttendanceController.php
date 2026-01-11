<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AttendanceController extends Controller
{
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
            'face_recognition_code' => ['required', 'string', 'max:255'],
        ]);

        $employee = $validated['employee_id']
            ? Employee::find($validated['employee_id'])
            : $this->matchEmployeeByFaceCode($validated['face_recognition_code']);

        if (! $employee) {
            return back()
                ->withErrors(['face_recognition_code' => 'Pengenalan wajah tidak cocok dengan data terdaftar.'])
                ->withInput();
        }

        if (! $employee->face_recognition_signature || ! $employee->face_recognition_registered_at) {
            return back()
                ->withErrors(['employee_id' => 'Pengenalan wajah karyawan belum terdaftar. Silakan daftar terlebih dahulu di profil karyawan.'])
                ->withInput();
        }

        if (! Hash::check($validated['face_recognition_code'], $employee->face_recognition_signature)) {
            return back()
                ->withErrors(['face_recognition_code' => 'Pengenalan wajah tidak cocok dengan data terdaftar.'])
                ->withInput();
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
            'face_recognition_code' => ['required', 'string', 'max:255'],
            'attendance_date' => ['nullable', 'date'],
        ]);

        $employee = $this->matchEmployeeByFaceCode($validated['face_recognition_code']);

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
    }

    protected function matchEmployeeByFaceCode(string $faceCode): ?Employee
    {
        $employees = Employee::query()
            ->where('is_active', true)
            ->whereNotNull('face_recognition_signature')
            ->whereNotNull('face_recognition_registered_at')
            ->get();

        foreach ($employees as $employee) {
            if (Hash::check($faceCode, $employee->face_recognition_signature)) {
                return $employee;
            }
        }

        return null;
    }
}
