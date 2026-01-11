<x-app-layout title="Detail Payroll">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Detail Payroll</h1>
            <p class="text-gray-600">Ringkasan pembayaran gaji karyawan.</p>
        </div>
        <a href="{{ route('payrolls.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Kembali ke daftar</a>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm space-y-4">
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <p class="text-sm text-gray-500">Karyawan</p>
                <p class="text-lg font-semibold text-gray-900">{{ $payroll->employee?->name ?? '-' }}</p>
                <p class="text-sm text-gray-600">{{ $payroll->employee?->position ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Tanggal Bayar</p>
                <p class="text-lg font-semibold text-gray-900">{{ $payroll->pay_date->format('d M Y') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div>
                <p class="text-sm text-gray-500">Periode</p>
                <p class="text-gray-700">{{ $payroll->period_start->format('d M Y') }} - {{ $payroll->period_end->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Gaji Pokok</p>
                <p class="text-gray-900 font-semibold">Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Total Dibayarkan</p>
                <p class="text-gray-900 font-semibold">Rp {{ number_format($payroll->total, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <p class="text-sm text-gray-500">Tunjangan</p>
                <p class="text-gray-700">Rp {{ number_format($payroll->allowance, 0, ',', '.') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Potongan</p>
                <p class="text-gray-700">Rp {{ number_format($payroll->deduction, 0, ',', '.') }}</p>
            </div>
        </div>

        <div>
            <p class="text-sm text-gray-500">Catatan</p>
            <p class="text-gray-700">{{ $payroll->note ?? '-' }}</p>
        </div>
    </div>
</x-app-layout>
