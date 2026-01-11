<x-app-layout title="Detail Absensi">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Detail Absensi</h1>
            <p class="text-gray-600">Rekap absensi pegawai berbasis scan retina melalui kamera/webcam.</p>
        </div>
        <a href="{{ route('attendances.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Kembali ke daftar</a>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-500">Karyawan</p>
                <p class="text-lg font-semibold text-gray-900">{{ $attendance->employee?->name ?? '-' }}</p>
                <p class="text-sm text-gray-600">{{ $attendance->employee?->position ?? 'Posisi belum diisi' }}</p>
            </div>
            <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">Scan Retina</span>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                <p class="text-xs font-semibold uppercase text-gray-500">Tanggal</p>
                <p class="mt-1 text-sm font-semibold text-gray-900">{{ $attendance->attendance_date->format('d M Y') }}</p>
            </div>
            <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                <p class="text-xs font-semibold uppercase text-gray-500">Check-in</p>
                <p class="mt-1 text-sm font-semibold text-gray-900">{{ $attendance->check_in_time }}</p>
            </div>
            <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                <p class="text-xs font-semibold uppercase text-gray-500">Check-out</p>
                <p class="mt-1 text-sm font-semibold text-gray-900">{{ $attendance->check_out_time ?? '-' }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <span class="text-sm font-semibold text-gray-600">Status:</span>
            @if ($attendance->status === 'Terlambat')
                <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-700">Terlambat</span>
            @else
                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">{{ $attendance->status }}</span>
            @endif
        </div>

        @if ($attendance->note)
            <div class="rounded-lg border border-gray-100 bg-gray-50 p-4">
                <p class="text-xs font-semibold uppercase text-gray-500">Catatan</p>
                <p class="mt-1 text-sm text-gray-700">{{ $attendance->note }}</p>
            </div>
        @endif
    </div>
</x-app-layout>
