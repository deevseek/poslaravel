<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class EmployeeController extends Controller
{
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
            'retina_scan_code' => ['nullable', 'string', 'max:255'],
            'retina_scan_snapshot' => ['nullable', 'string', 'regex:/^data:image\\/(png|jpeg|jpg|webp);base64,/'],
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? true);

        if (! empty($validated['retina_scan_snapshot'])) {
            $retinaScanPath = $this->storeRetinaSnapshot($validated['retina_scan_snapshot']);
            if ($retinaScanPath) {
                $validated['retina_scan_path'] = $retinaScanPath;
                if (empty($validated['retina_scan_code'])) {
                    $validated['retina_registered_at'] = now();
                }
            }
        }

        if (! empty($validated['retina_scan_code'])) {
            $validated['retina_signature'] = Hash::make($validated['retina_scan_code']);
            $validated['retina_registered_at'] = now();
        }

        unset($validated['retina_scan_code'], $validated['retina_scan_snapshot']);

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
            'retina_scan_code' => ['nullable', 'string', 'max:255'],
            'retina_scan_snapshot' => ['nullable', 'string', 'regex:/^data:image\\/(png|jpeg|jpg|webp);base64,/'],
            'reset_retina' => ['nullable', 'boolean'],
            'remove_retina_scan' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);
        $resetRetina = ! empty($validated['reset_retina']);
        $removeRetinaScan = ! empty($validated['remove_retina_scan']);

        if ($resetRetina || $removeRetinaScan) {
            if ($employee->retina_scan_path) {
                Storage::disk('public')->delete($employee->retina_scan_path);
            }
            $validated['retina_scan_path'] = null;
        }

        if ($resetRetina) {
            $validated['retina_signature'] = null;
            $validated['retina_registered_at'] = null;
        } elseif (! empty($validated['retina_scan_code'])) {
            $validated['retina_signature'] = Hash::make($validated['retina_scan_code']);
            $validated['retina_registered_at'] = now();
        }

        if (! empty($validated['retina_scan_snapshot'])) {
            if ($employee->retina_scan_path) {
                Storage::disk('public')->delete($employee->retina_scan_path);
            }

            $retinaScanPath = $this->storeRetinaSnapshot($validated['retina_scan_snapshot']);
            if ($retinaScanPath) {
                $validated['retina_scan_path'] = $retinaScanPath;
                if (empty($validated['retina_scan_code']) && empty($validated['retina_registered_at']) && ! $employee->retina_registered_at) {
                    $validated['retina_registered_at'] = now();
                }
            }
        }

        unset($validated['retina_scan_code'], $validated['retina_scan_snapshot'], $validated['reset_retina'], $validated['remove_retina_scan']);

        $employee->update($validated);

        return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil dihapus.');
    }

    private function storeRetinaSnapshot(string $snapshot): ?string
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

        $filename = 'retina-scans/' . Str::uuid() . '.' . $extension;
        Storage::disk('public')->put($filename, $decodedImage);

        return $filename;
    }
}
