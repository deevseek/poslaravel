<x-app-layout title="Detail Karyawan">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Detail Karyawan</h1>
            <p class="text-gray-600">Informasi lengkap dan riwayat payroll.</p>
        </div>
        <div class="flex items-center gap-3">
            @feature('payroll')
                <a href="{{ route('payrolls.create', ['employee_id' => $employee->id]) }}" class="rounded-lg border border-blue-200 px-4 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-50">Catat Payroll</a>
            @endfeature
            <a href="{{ route('employees.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Kembali ke daftar</a>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm space-y-3">
            <div>
                <p class="text-sm text-gray-500">Nama</p>
                <p class="font-semibold text-gray-900">{{ $employee->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Posisi</p>
                <p class="text-gray-700">{{ $employee->position ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Kontak</p>
                <p class="text-gray-700">{{ $employee->email ?? '-' }}</p>
                <p class="text-gray-700">{{ $employee->phone ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Alamat</p>
                <p class="text-gray-700">{{ $employee->address ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Tanggal Bergabung</p>
                <p class="text-gray-700">{{ $employee->join_date?->format('d M Y') ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Gaji Pokok</p>
                <p class="text-gray-900 font-semibold">Rp {{ number_format($employee->base_salary ?? 0, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Status</p>
                @if ($employee->is_active)
                    <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-700">Aktif</span>
                @else
                    <span class="rounded-full bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-600">Nonaktif</span>
                @endif
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900">Riwayat Payroll</h2>
                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Periode</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Tanggal Bayar</th>
                                <th class="px-4 py-2 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Total</th>
                                <th class="px-4 py-2 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($employee->payrolls->sortByDesc('pay_date') as $payroll)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-600">{{ $payroll->period_start->format('d M Y') }} - {{ $payroll->period_end->format('d M Y') }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-600">{{ $payroll->pay_date->format('d M Y') }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900 font-semibold">Rp {{ number_format($payroll->total, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-600 text-right">
                                        <a href="{{ route('payrolls.show', $payroll) }}" class="rounded-lg border border-gray-200 px-3 py-1 text-xs font-semibold text-gray-700 hover:bg-gray-50">Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-4 text-center text-sm text-gray-500">Belum ada payroll untuk karyawan ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
