<x-app-layout title="Catat Absensi">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Catat Absensi</h1>
            <p class="text-gray-600">Rekam kehadiran pegawai menggunakan pemindaian retina.</p>
        </div>
        <a href="{{ route('attendances.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Kembali ke daftar</a>
    </div>

    <form action="{{ route('attendances.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm space-y-4">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold text-gray-700" for="employee_id">Karyawan</label>
                    <select id="employee_id" name="employee_id" required
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                        <option value="">Pilih karyawan</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}" {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                {{ $employee->name }} {{ $employee->position ? ' - ' . $employee->position : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700" for="attendance_date">Tanggal</label>
                    <input type="date" id="attendance_date" name="attendance_date" value="{{ old('attendance_date', now()->toDateString()) }}" required
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    @error('attendance_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold text-gray-700" for="check_in_time">Jam Check-in</label>
                    <input type="time" id="check_in_time" name="check_in_time" value="{{ old('check_in_time', now()->format('H:i')) }}" required
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    @error('check_in_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700" for="check_out_time">Jam Check-out (opsional)</label>
                    <input type="time" id="check_out_time" name="check_out_time" value="{{ old('check_out_time') }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    @error('check_out_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700">Metode Absensi</label>
                <div class="mt-1 flex items-center gap-3 rounded-lg border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-700">
                    <span class="text-lg">👁️</span>
                    <div>
                        <p class="font-semibold">Scan Retina</p>
                        <p class="text-xs text-blue-600">Sistem akan memverifikasi identitas pegawai melalui pola retina.</p>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700" for="note">Catatan (opsional)</label>
                <textarea id="note" name="note" rows="3"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">{{ old('note') }}</textarea>
                @error('note')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('attendances.index') }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</a>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Simpan Absensi</button>
        </div>
    </form>
</x-app-layout>
