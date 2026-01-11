<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Services\FaceRecognitionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

        if (! empty($validated['face_recognition_snapshot'])) {
            $faceRecognitionPath = $this->storeFaceRecognitionSnapshot($validated['face_recognition_snapshot']);
            if ($faceRecognitionPath) {
                $snapshotPath = Storage::disk('public')->path($faceRecognitionPath);
                if (! $this->faceRecognition->hasFace($snapshotPath)) {
                    Storage::disk('public')->delete($faceRecognitionPath);

                    return back()
                        ->withErrors(['face_recognition_snapshot' => 'Wajah tidak terdeteksi pada foto. Silakan ulangi pemindaian.'])
                        ->withInput();
                }

                $signature = $this->faceRecognition->extractSignature($snapshotPath);

                $validated['face_recognition_scan_path'] = $faceRecognitionPath;
                $validated['face_recognition_signature'] = $signature ? json_encode($signature) : null;
                $validated['face_recognition_registered_at'] = now();
            }
        }

        unset($validated['face_recognition_snapshot']);

        Employee::create($validated);

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
            if ($employee->face_recognition_scan_path) {
                Storage::disk('public')->delete($employee->face_recognition_scan_path);
            }
            $validated['face_recognition_scan_path'] = null;
            $validated['face_recognition_signature'] = null;
            $validated['face_recognition_registered_at'] = null;
        }

        if ($resetFaceRecognition) {
            $validated['face_recognition_signature'] = null;
            $validated['face_recognition_registered_at'] = null;
        }

        if (! empty($validated['face_recognition_snapshot'])) {
            if ($employee->face_recognition_scan_path) {
                Storage::disk('public')->delete($employee->face_recognition_scan_path);
            }

            $faceRecognitionPath = $this->storeFaceRecognitionSnapshot($validated['face_recognition_snapshot']);
            if ($faceRecognitionPath) {
                $snapshotPath = Storage::disk('public')->path($faceRecognitionPath);
                if (! $this->faceRecognition->hasFace($snapshotPath)) {
                    Storage::disk('public')->delete($faceRecognitionPath);

                    return back()
                        ->withErrors(['face_recognition_snapshot' => 'Wajah tidak terdeteksi pada foto. Silakan ulangi pemindaian.'])
                        ->withInput();
                }

                $signature = $this->faceRecognition->extractSignature($snapshotPath);

                $validated['face_recognition_scan_path'] = $faceRecognitionPath;
                $validated['face_recognition_signature'] = $signature ? json_encode($signature) : null;
                $validated['face_recognition_registered_at'] = now();
            }
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

    private function storeFaceRecognitionSnapshot(string $snapshot): ?string
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

        $filename = 'face-recognition-scans/' . Str::uuid() . '.' . $extension;
        Storage::disk('public')->put($filename, $decodedImage);

        return $filename;
    }
}
