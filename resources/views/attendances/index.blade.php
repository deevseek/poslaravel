<x-app-layout title="HRD - Absensi">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Absensi Pegawai</h1>
            <p class="text-gray-600">Pantau kehadiran harian dengan metode scan retina melalui kamera/webcam.</p>
        </div>
        @permission('hrd.manage')
            <a href="{{ route('attendances.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">
                ➕
                <span>Catat Absensi</span>
            </a>
        @endpermission
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Tanggal</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Karyawan</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Check-in</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Check-out</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Metode</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse ($attendances as $attendance)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $attendance->attendance_date->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $attendance->employee?->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $attendance->check_in_time }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $attendance->check_out_time ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="rounded-full bg-blue-100 px-2 py-1 text-xs font-semibold text-blue-700">Scan Retina</span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if ($attendance->status === 'Terlambat')
                                <span class="rounded-full bg-orange-100 px-2 py-1 text-xs font-semibold text-orange-700">Terlambat</span>
                            @else
                                <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-700">{{ $attendance->status }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right text-sm text-gray-600">
                            <a href="{{ route('attendances.show', $attendance) }}" class="rounded-lg border border-gray-200 px-3 py-1 text-xs font-semibold text-gray-700 hover:bg-gray-50">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data absensi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $attendances->links() }}
    </div>
</x-app-layout>
