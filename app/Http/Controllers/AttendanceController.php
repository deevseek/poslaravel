<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Exceptions\FaceApiBadResponseException;
use App\Exceptions\FaceApiUnavailableException;
use App\Services\FaceRecognitionService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

        return view('attendances.create', compact('employees'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'employee_id' => [
                'required',
                'exists:employees,id',
            ],
            'attendance_date' => ['required', 'date'],
            'check_in_time' => ['required', 'date_format:H:i'],
            'check_out_time' => ['nullable', 'date_format:H:i', 'after_or_equal:check_in_time'],
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
            if (! empty($validated['attendance_date'])) {
                $alreadyAttended = Attendance::where('employee_id', $employee->id)
                    ->where('attendance_date', $validated['attendance_date'])
                    ->exists();
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
}
