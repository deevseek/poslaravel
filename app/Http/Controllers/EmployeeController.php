<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Exceptions\FaceApiBadResponseException;
use App\Exceptions\FaceApiUnavailableException;
use App\Services\FaceRecognitionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function __construct(private readonly FaceRecognitionService $faceRecognition)
    {
    }

    public function index(): View
    {
        $employees = Employee::orderBy('name')->paginate(10);

        return view('employees.index', compact('employees'));
    }

    public function create(): View
    {
        return view('employees.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:employees,email'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'join_date' => ['nullable', 'date'],
            'base_salary' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'face_recognition_snapshot' => ['nullable', 'string', 'regex:/^data:image\\/(png|jpeg|jpg|webp);base64,/'],
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? true);

        unset($validated['face_recognition_snapshot']);

        $employee = Employee::create($validated);

        if (! empty($request->input('face_recognition_snapshot'))) {
            try {
                $this->faceRecognition->registerFace($employee->id, (string) $request->input('face_recognition_snapshot'));
            } catch (FaceApiUnavailableException | FaceApiBadResponseException $exception) {
                $employee->delete();

                return back()
                    ->withErrors(['face_recognition_snapshot' => $this->mapFaceRecognitionError(
                        null,
                        $exception->getMessage(),
                    )])
                    ->withInput();
            }

            $employee->update([
                'face_recognition_scan_path' => null,
                'face_recognition_signature' => null,
                'face_recognition_registered_at' => now(),
            ]);
        }

        return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil ditambahkan.');
    }

    public function show(Employee $employee): View
    {
        $employee->load('payrolls');

        return view('employees.show', compact('employee'));
    }

    public function edit(Employee $employee): View
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:employees,email,' . $employee->id],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string'],
            'join_date' => ['nullable', 'date'],
            'base_salary' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'face_recognition_snapshot' => ['nullable', 'string', 'regex:/^data:image\\/(png|jpeg|jpg|webp);base64,/'],
            'reset_face_recognition' => ['nullable', 'boolean'],
            'remove_face_recognition_scan' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);
        $resetFaceRecognition = ! empty($validated['reset_face_recognition']);
        $removeFaceRecognitionScan = ! empty($validated['remove_face_recognition_scan']);

        if ($resetFaceRecognition || $removeFaceRecognitionScan) {
            $validated['face_recognition_scan_path'] = null;
            $validated['face_recognition_signature'] = null;
            $validated['face_recognition_registered_at'] = null;
        }

        if ($resetFaceRecognition) {
            $validated['face_recognition_signature'] = null;
            $validated['face_recognition_registered_at'] = null;
        }

        if (! empty($validated['face_recognition_snapshot'])) {
            try {
                $this->faceRecognition->registerFace($employee->id, $validated['face_recognition_snapshot']);
            } catch (FaceApiUnavailableException | FaceApiBadResponseException $exception) {
                return back()
                    ->withErrors(['face_recognition_snapshot' => $this->mapFaceRecognitionError(
                        null,
                        $exception->getMessage(),
                    )])
                    ->withInput();
            }

            $validated['face_recognition_scan_path'] = null;
            $validated['face_recognition_signature'] = null;
            $validated['face_recognition_registered_at'] = now();
        }

        unset($validated['face_recognition_snapshot'], $validated['reset_face_recognition'], $validated['remove_face_recognition_scan']);

        $employee->update($validated);

        return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil dihapus.');
    }

    private function mapFaceRecognitionError(?string $error, ?string $fallback = null): string
    {
        return match ($error) {
            'no_face_detected' => 'Wajah tidak terdeteksi pada foto. Silakan ulangi pemindaian.',
            'not_matched' => 'Wajah terdeteksi tetapi tidak cocok dengan data terdaftar.',
            'unavailable' => 'Layanan face recognition tidak tersedia. Silakan coba beberapa saat lagi.',
            default => $fallback ?? 'Terjadi kesalahan saat mendaftarkan wajah. Silakan ulangi.',
        };
    }
}
