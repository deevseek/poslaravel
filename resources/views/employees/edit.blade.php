<x-app-layout title="Edit Karyawan">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Edit Karyawan</h1>
            <p class="text-gray-600">Perbarui data karyawan yang sudah ada.</p>
        </div>
        <a href="{{ route('employees.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Kembali ke daftar</a>
    </div>

    <form action="{{ route('employees.update', $employee) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm space-y-4">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold text-gray-700" for="name">Nama</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $employee->name) }}" required
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700" for="position">Posisi</label>
                    <input type="text" id="position" name="position" value="{{ old('position', $employee->position) }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    @error('position')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold text-gray-700" for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $employee->email) }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700" for="phone">Telepon</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $employee->phone) }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700" for="address">Alamat</label>
                <textarea id="address" name="address" rows="2"
                    class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">{{ old('address', $employee->address) }}</textarea>
                @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div>
                    <label class="block text-sm font-semibold text-gray-700" for="join_date">Tanggal Bergabung</label>
                    <input type="date" id="join_date" name="join_date" value="{{ old('join_date', optional($employee->join_date)->toDateString()) }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    @error('join_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700" for="base_salary">Gaji Pokok</label>
                    <input type="number" id="base_salary" name="base_salary" min="0" step="0.01" value="{{ old('base_salary', $employee->base_salary) }}"
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    @error('base_salary')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="is_active" name="is_active" value="1" class="h-4 w-4 rounded border-gray-300 text-blue-600" {{ old('is_active', $employee->is_active) ? 'checked' : '' }}>
                    <label class="text-sm font-semibold text-gray-700" for="is_active">Aktif</label>
                </div>
            </div>

            <div class="rounded-lg border border-blue-100 bg-blue-50 p-4">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <p class="text-sm font-semibold text-blue-700">Pendaftaran Retina</p>
                    @if ($employee->retina_registered_at)
                        <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-700">Terdaftar {{ $employee->retina_registered_at->format('d M Y H:i') }}</span>
                    @else
                        <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-600">Belum terdaftar</span>
                    @endif
                </div>
                <p class="mt-1 text-xs text-blue-600">Perbarui kode retina jika ada perekaman ulang. Centang reset untuk menghapus data retina lama.</p>
                <div class="mt-3 grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700" for="retina_scan_code">Kode Retina Baru (opsional)</label>
                        <input type="text" id="retina_scan_code" name="retina_scan_code" value="{{ old('retina_scan_code') }}"
                            class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                        @error('retina_scan_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="reset_retina" name="reset_retina" value="1" class="h-4 w-4 rounded border-gray-300 text-blue-600" {{ old('reset_retina') ? 'checked' : '' }}>
                        <label class="text-sm font-semibold text-gray-700" for="reset_retina">Reset data retina</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('employees.index') }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</a>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Simpan Perubahan</button>
        </div>
    </form>
</x-app-layout>
