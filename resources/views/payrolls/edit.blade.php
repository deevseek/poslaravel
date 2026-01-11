<x-app-layout title="Edit Payroll">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Edit Payroll</h1>
            <p class="text-gray-600">Perbarui data gaji dan sinkronkan ke keuangan.</p>
        </div>
        <a href="{{ route('payrolls.show', $payroll) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Kembali ke detail</a>
    </div>

    @php
        $selectedEmployee = $employees->firstWhere('id', old('employee_id', $payroll->employee_id));
        $defaultSalary = old('base_salary', $payroll->base_salary ?? ($selectedEmployee?->base_salary ?? 0));
    @endphp

    <form action="{{ route('payrolls.update', $payroll) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm space-y-4">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold text-gray-700" for="employee_id">Karyawan</label>
                    <select id="employee_id" name="employee_id" required
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                        <option value="">Pilih karyawan</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}" {{ (string) old('employee_id', $payroll->employee_id) === (string) $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }}{{ $employee->position ? ' - ' . $employee->position : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700" for="pay_date">Tanggal Bayar</label>
                    <input type="date" id="pay_date" name="pay_date" value="{{ old('pay_date', $payroll->pay_date->toDateString()) }}" required
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    @error('pay_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold text-gray-700" for="period_start">Periode Mulai</label>
                    <input type="date" id="period_start" name="period_start" value="{{ old('period_start', $payroll->period_start->toDateString()) }}" required
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    @error('period_start')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700" for="period_end">Periode Selesai</label>
                    <input type="date" id="period_end" name="period_end" value="{{ old('period_end', $payroll->period_end->toDateString()) }}" required
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    @error('period_end')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700" for="base_salary">Gaji Pokok</label>
                    <input type="number" id="base_salary" name="base_salary" min="0" step="0.01" value="{{ $defaultSalary }}" required
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    @error('base_salary')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700" for="allowance">Tunjangan</label>
                    <input type="number" id="allowance" name="allowance" min="0" step="0.01" value="{{ old('allowance', $payroll->allowance ?? 0) }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    @error('allowance')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700" for="deduction">Potongan</label>
                    <input type="number" id="deduction" name="deduction" min="0" step="0.01" value="{{ old('deduction', $payroll->deduction ?? 0) }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    @error('deduction')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700" for="note">Catatan</label>
                <textarea id="note" name="note" rows="2"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">{{ old('note', $payroll->note) }}</textarea>
                @error('note')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('payrolls.show', $payroll) }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</a>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Simpan Perubahan</button>
        </div>
    </form>
</x-app-layout>
