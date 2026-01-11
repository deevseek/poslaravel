<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? true);

        if (! empty($validated['retina_scan_code'])) {
            $validated['retina_signature'] = Hash::make($validated['retina_scan_code']);
            $validated['retina_registered_at'] = now();
        }

        unset($validated['retina_scan_code']);

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
            'reset_retina' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        if (! empty($validated['reset_retina'])) {
            $validated['retina_signature'] = null;
            $validated['retina_registered_at'] = null;
        } elseif (! empty($validated['retina_scan_code'])) {
            $validated['retina_signature'] = Hash::make($validated['retina_scan_code']);
            $validated['retina_registered_at'] = now();
        }

        unset($validated['retina_scan_code'], $validated['reset_retina']);

        $employee->update($validated);

        return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $employee->delete();

        return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil dihapus.');
    }
}
