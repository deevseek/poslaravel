<x-app-layout title="HRD - Karyawan">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Karyawan</h1>
            <p class="text-gray-600">Kelola data karyawan dan status kepegawaian.</p>
        </div>
        @permission('hrd.manage')
            <a href="{{ route('employees.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">
                ➕
                <span>Tambah Karyawan</span>
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
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Posisi</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Telepon</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Gaji Pokok</th>
                    @permission('hrd.manage')
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                    @endpermission
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse ($employees as $employee)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $employee->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $employee->position ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $employee->phone ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if ($employee->is_active)
                                <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-700">Aktif</span>
                            @else
                                <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-600">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">Rp {{ number_format($employee->base_salary ?? 0, 0, ',', '.') }}</td>
                        @permission('hrd.manage')
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('employees.show', $employee) }}" class="rounded-lg border border-gray-200 px-3 py-1 text-xs font-semibold text-gray-700 hover:bg-gray-50">Detail</a>
                                    <a href="{{ route('employees.edit', $employee) }}" class="rounded-lg border border-blue-200 px-3 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-50">Edit</a>
                                    <form action="{{ route('employees.destroy', $employee) }}" method="POST" onsubmit="return confirm('Hapus data karyawan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg border border-red-200 px-3 py-1 text-xs font-semibold text-red-700 hover:bg-red-50">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        @endpermission
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data karyawan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $employees->links() }}
    </div>
</x-app-layout>
