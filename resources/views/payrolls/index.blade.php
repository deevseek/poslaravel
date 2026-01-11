<x-app-layout title="Payroll">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Payroll</h1>
            <p class="text-gray-600">Catat gaji karyawan dan sinkron ke keuangan.</p>
        </div>
        @permission('payroll.manage')
            <a href="{{ route('payrolls.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">
                ➕
                <span>Catat Payroll</span>
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
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Karyawan</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Periode</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Tanggal Bayar</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Total</th>
                    @permission('payroll.manage')
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                    @endpermission
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse ($payrolls as $payroll)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $payroll->employee?->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $payroll->period_start->format('d M Y') }} - {{ $payroll->period_end->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $payroll->pay_date->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900 font-semibold">Rp {{ number_format($payroll->total, 0, ',', '.') }}</td>
                        @permission('payroll.manage')
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('payrolls.show', $payroll) }}" class="rounded-lg border border-gray-200 px-3 py-1 text-xs font-semibold text-gray-700 hover:bg-gray-50">Detail</a>
                                    <form action="{{ route('payrolls.destroy', $payroll) }}" method="POST" onsubmit="return confirm('Hapus data payroll ini?')">
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
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data payroll.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $payrolls->links() }}
    </div>
</x-app-layout>
