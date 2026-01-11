<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Setting;
use App\Exceptions\FaceApiBadResponseException;
use App\Exceptions\FaceApiUnavailableException;
use App\Services\FaceRecognitionService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
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

        $workStart = Setting::getValue(Setting::HRD_WORK_START, '09:00');
        $workEnd = Setting::getValue(Setting::HRD_WORK_END, '17:00');

        return view('attendances.create', compact('employees', 'workStart', 'workEnd'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'attendance_type' => ['required', Rule::in(['checkin', 'checkout'])],
            'employee_id' => [
                'required',
                'exists:employees,id',
            ],
            'attendance_date' => ['required', 'date'],
            'check_in_time' => ['required_if:attendance_type,checkin', 'nullable', 'date_format:H:i'],
            'check_out_time' => ['required_if:attendance_type,checkout', 'nullable', 'date_format:H:i'],
            'note' => ['nullable', 'string', 'max:500'],
            'face_recognition_snapshot' => ['required', 'string', 'regex:/^data:image\\/(png|jpeg|jpg|webp);base64,/'],
        ]);

        try {
            $employee = Employee::find($validated['employee_id']);

            if (! $employee->face_recognition_registered_at) {
                return back()
                    ->withErrors(['employee_id' => 'Pengenalan wajah karyawan belum terdaftar. Silakan daftar terlebih dahulu di profil karyawan.'])
                    ->withInput();
            }

            if (! $this->faceRecognition->health()) {
                return back()
                    ->withErrors(['face_recognition_snapshot' => $this->mapFaceRecognitionError('unavailable')])
                    ->withInput();
            }

            $verification = $this->faceRecognition->verifyFace(
                $validated['face_recognition_snapshot'],
                (string) $employee->id,
            );

            if (! empty($verification['error'])) {
                return back()
                    ->withErrors(['face_recognition_snapshot' => $this->mapFaceRecognitionError($verification['error'])])
                    ->withInput();
            }

            if (empty($verification['matched'])) {
                return back()
                    ->withErrors(['face_recognition_snapshot' => $this->mapFaceRecognitionError('not_matched')])
                    ->withInput();
            }
        } catch (FaceApiUnavailableException $exception) {
            Log::warning('Face API unavailable during attendance verify.', [
                'exception' => $exception->getMessage(),
            ]);

            return back()
                ->withErrors(['face_recognition_snapshot' => $this->mapFaceRecognitionError('unavailable')])
                ->withInput();
        } catch (FaceApiBadResponseException $exception) {
            Log::warning('Face API bad response during attendance verify.', [
                'status' => $exception->statusCode(),
                'body' => $exception->responseBody(),
            ]);

            return back()
                ->withErrors(['face_recognition_snapshot' => $this->mapFaceRecognitionError(null, $exception->getMessage())])
                ->withInput();
        }

        $attendance = Attendance::where('employee_id', $employee->id)
            ->where('attendance_date', $validated['attendance_date'])
            ->first();

        $workStart = Setting::getValue(Setting::HRD_WORK_START, '09:00');
        $workEnd = Setting::getValue(Setting::HRD_WORK_END, '17:00');
        $workStartTime = Carbon::createFromFormat('H:i', $workStart);
        $workEndTime = Carbon::createFromFormat('H:i', $workEnd);

        if ($validated['attendance_type'] === 'checkout') {
            if (! $attendance) {
                return back()
                    ->withErrors(['employee_id' => 'Belum ada data check-in untuk karyawan dan tanggal tersebut.'])
                    ->withInput();
            }

            if ($attendance->check_out_time) {
                return back()
                    ->withErrors(['check_out_time' => 'Checkout untuk karyawan dan tanggal tersebut sudah tercatat.'])
                    ->withInput();
            }

            $checkIn = Carbon::parse($attendance->check_in_time);
            $checkOut = Carbon::parse($validated['check_out_time']);

            if ($checkOut->lessThan($checkIn)) {
                return back()
                    ->withErrors(['check_out_time' => 'Jam check-out harus setelah jam check-in.'])
                    ->withInput();
            }

            $attendance->update([
                'check_out_time' => $validated['check_out_time'],
                'status' => $this->resolveStatus($checkIn, $checkOut, $workStartTime, $workEndTime),
                'note' => $validated['note'] ?? $attendance->note,
            ]);

            return redirect()->route('attendances.index')->with('success', 'Checkout berhasil dicatat dengan metode face recognition via webcam.');
        }

        if ($attendance) {
            return back()
                ->withErrors(['employee_id' => 'Absensi untuk karyawan dan tanggal tersebut sudah ada.'])
                ->withInput();
        }

        $checkIn = Carbon::createFromFormat('H:i', $validated['check_in_time']);
        $workStart = Setting::getValue(Setting::HRD_WORK_START, '09:00');
        $workEnd = Setting::getValue(Setting::HRD_WORK_END, '17:00');
        $workStartTime = Carbon::createFromFormat('H:i', $workStart);
        $workEndTime = Carbon::createFromFormat('H:i', $workEnd);
        $checkOut = ! empty($validated['check_out_time'])
            ? Carbon::createFromFormat('H:i', $validated['check_out_time'])
            : null;

        if ($checkOut && $checkOut->lessThan($checkIn)) {
            return back()
                ->withErrors(['check_out_time' => 'Jam check-out harus setelah jam check-in.'])
                ->withInput();
        }

        $status = $this->resolveStatus($checkIn, $checkOut, $workStartTime, $workEndTime);

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

        try {
            $identification = $this->faceRecognition->identifyFace($validated['face_recognition_snapshot']);

            if (! empty($identification['error'])) {
                return response()->json([
                    'matched' => false,
                    'faces_detected' => $identification['faces_detected'] ?? 0,
                    'error' => $identification['error'],
                    'message' => $this->mapFaceRecognitionError($identification['error']),
                ], 422);
            }

            if (empty($identification['matched']) || empty($identification['user_id'])) {
                return response()->json([
                    'matched' => false,
                    'faces_detected' => $identification['faces_detected'] ?? 1,
                    'error' => 'face_not_matched',
                    'message' => 'Wajah terdeteksi tetapi tidak cocok dengan data karyawan.',
                ], 404);
            }

            $employee = Employee::find($identification['user_id']);
            if (! $employee) {
                return response()->json([
                    'matched' => false,
                    'faces_detected' => $identification['faces_detected'] ?? 1,
                    'error' => 'employee_not_found',
                    'message' => 'Data karyawan tidak ditemukan.',
                ], 404);
            }

            $alreadyAttended = false;
            $hasCheckedOut = false;
            if (! empty($validated['attendance_date'])) {
                $attendance = Attendance::where('employee_id', $employee->id)
                    ->where('attendance_date', $validated['attendance_date'])
                    ->first();
                $alreadyAttended = (bool) $attendance;
                $hasCheckedOut = $attendance ? (bool) $attendance->check_out_time : false;
            }

            return response()->json([
                'matched' => true,
                'confidence' => $identification['confidence'] ?? null,
                'faces_detected' => $identification['faces_detected'] ?? 1,
                'employee' => [
                    'id' => $employee->id,
                    'name' => $employee->name,
                    'position' => $employee->position,
                ],
                'already_attended' => $alreadyAttended,
                'has_checked_out' => $hasCheckedOut,
            ]);
        } catch (FaceApiUnavailableException $exception) {
            Log::warning('Face API unavailable during attendance identify.', [
                'exception' => $exception->getMessage(),
            ]);

            return response()->json([
                'matched' => false,
                'error' => 'unavailable',
                'message' => $this->mapFaceRecognitionError('unavailable', $exception->getMessage()),
            ], 502);
        } catch (FaceApiBadResponseException $exception) {
            Log::warning('Face API bad response during attendance identify.', [
                'status' => $exception->statusCode(),
                'body' => $exception->responseBody(),
            ]);

            return response()->json([
                'matched' => false,
                'error' => 'unavailable',
                'message' => $this->mapFaceRecognitionError('unavailable', $exception->getMessage()),
            ], 502);
        }
    }

    private function mapFaceRecognitionError(?string $error, ?string $fallback = null): string
    {
        return match ($error) {
            'no_face_detected' => 'Wajah tidak terdeteksi pada foto. Silakan ulangi pemindaian.',
            'not_matched' => 'Wajah terdeteksi tetapi tidak cocok dengan data terdaftar.',
            'face_not_matched' => 'Wajah terdeteksi tetapi tidak cocok dengan data terdaftar.',
            'unavailable' => 'Layanan face recognition tidak tersedia. Silakan coba beberapa saat lagi.',
            default => $fallback ?? 'Terjadi kesalahan saat memverifikasi wajah. Silakan ulangi.',
        };
    }

    private function resolveStatus(Carbon $checkIn, ?Carbon $checkOut, Carbon $workStartTime, Carbon $workEndTime): string
    {
        if ($checkIn->greaterThan($workStartTime)) {
            return 'Terlambat';
        }

        if ($checkOut) {
            return $checkOut->lessThan($workEndTime) ? 'Pulang cepat' : 'Hadir';
        }

        return 'Hadir';
    }
}
