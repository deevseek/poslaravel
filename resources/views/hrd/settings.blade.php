<x-app-layout title="Pengaturan HRD">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Pengaturan HRD</h1>
            <p class="text-gray-600">Atur jam kerja standar untuk perhitungan absensi karyawan.</p>
        </div>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <form action="{{ route('hrd-settings.update') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="{{ App\Models\Setting::HRD_WORK_START }}">Jam Masuk Kerja</label>
                    <input type="time" id="{{ App\Models\Setting::HRD_WORK_START }}"
                        name="{{ App\Models\Setting::HRD_WORK_START }}"
                        value="{{ old(App\Models\Setting::HRD_WORK_START, $settings[App\Models\Setting::HRD_WORK_START] ?? '09:00') }}"
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    @error(App\Models\Setting::HRD_WORK_START)
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="{{ App\Models\Setting::HRD_WORK_END }}">Jam Pulang Kerja</label>
                    <input type="time" id="{{ App\Models\Setting::HRD_WORK_END }}"
                        name="{{ App\Models\Setting::HRD_WORK_END }}"
                        value="{{ old(App\Models\Setting::HRD_WORK_END, $settings[App\Models\Setting::HRD_WORK_END] ?? '17:00') }}"
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    @error(App\Models\Setting::HRD_WORK_END)
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="rounded-lg border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-700">
                <p class="font-semibold">Integrasi Absensi</p>
                <p class="text-xs text-blue-600">Jam masuk menentukan status terlambat, sedangkan jam pulang dipakai untuk mendeteksi pulang cepat.</p>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Simpan Pengaturan HRD</button>
            </div>
        </form>
    </div>
</x-app-layout>
