<x-app-layout :title="'Slip Gaji #' . $payroll->id">
    <style>
        @media print {
            .slip-actions {
                display: none !important;
            }

            .slip-card {
                box-shadow: none !important;
                border: none !important;
            }
        }
    </style>

    <div class="mx-auto max-w-4xl space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Slip Gaji</h1>
                <p class="text-gray-600">Periode {{ $payroll->period_start->format('d M Y') }} - {{ $payroll->period_end->format('d M Y') }}</p>
            </div>
            <div class="slip-actions flex flex-wrap items-center gap-2">
                <a href="{{ route('payrolls.show', $payroll) }}" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">Kembali</a>
                <button type="button" onclick="window.print()" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Cetak Slip</button>
            </div>
        </div>

        <div class="slip-card rounded-lg border border-gray-200 bg-white p-6 shadow-sm space-y-6">
            <div class="flex flex-wrap items-start justify-between gap-4 border-b border-gray-100 pb-4">
                <div class="flex items-start gap-4">
                    @if ($store['logo'])
                        <img src="{{ asset($store['logo']) }}" alt="{{ $store['name'] }}" class="h-14 w-14 rounded-md object-cover" />
                    @endif
                    <div class="space-y-1">
                        <p class="text-lg font-semibold text-gray-900">{{ $store['name'] }}</p>
                        @if ($store['address'])
                            <p class="text-sm text-gray-600">{{ $store['address'] }}</p>
                        @endif
                        @if ($store['phone'])
                            <p class="text-sm text-gray-600">Telp: <x-wa-link :phone="$store['phone']" class="text-gray-600" /></p>
                        @endif
                    </div>
                </div>
                <div class="text-right text-sm text-gray-600 space-y-1">
                    <p class="text-sm font-semibold text-gray-900">No. Slip</p>
                    <p>#{{ str_pad((string) $payroll->id, 6, '0', STR_PAD_LEFT) }}</p>
                    <p>Tanggal Bayar: {{ $payroll->pay_date->format('d M Y') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 text-sm text-gray-700">
                <div class="space-y-2">
                    <p class="font-semibold text-gray-900">Informasi Karyawan</p>
                    <p>{{ $payroll->employee?->name ?? '-' }}</p>
                    <p class="text-gray-600">{{ $payroll->employee?->position ?? '-' }}</p>
                </div>
                <div class="space-y-2">
                    <p class="font-semibold text-gray-900">Ringkasan Periode</p>
                    <p>{{ $payroll->period_start->format('d M Y') }} - {{ $payroll->period_end->format('d M Y') }}</p>
                    <p class="text-gray-600">Hari bayar: {{ $payroll->pay_date->format('d M Y') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 text-sm text-gray-700">
                <div class="space-y-3">
                    <p class="font-semibold text-gray-900">Pendapatan</p>
                    <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                        <span>Gaji Pokok</span>
                        <span class="font-medium text-gray-900">Rp {{ number_format($payroll->base_salary, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                        <span>Tunjangan</span>
                        <span class="font-medium text-gray-900">Rp {{ number_format($payroll->allowance, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-base font-semibold text-gray-900">
                        <span>Total Pendapatan</span>
                        <span>Rp {{ number_format($payroll->base_salary + $payroll->allowance, 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="space-y-3">
                    <p class="font-semibold text-gray-900">Potongan</p>
                    <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                        <span>Potongan</span>
                        <span class="font-medium text-gray-900">Rp {{ number_format($payroll->deduction, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-base font-semibold text-gray-900">
                        <span>Total Potongan</span>
                        <span>Rp {{ number_format($payroll->deduction, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                <div class="flex items-center justify-between text-base font-semibold">
                    <span>Total Diterima</span>
                    <span>Rp {{ number_format($payroll->total, 0, ',', '.') }}</span>
                </div>
                <p class="mt-1 text-xs text-emerald-700">Total bersih setelah pendapatan dan potongan.</p>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 text-sm text-gray-700">
                <div>
                    <p class="font-semibold text-gray-900">Catatan</p>
                    <p class="text-gray-600">{{ $payroll->note ?? '-' }}</p>
                </div>
                <div class="grid grid-cols-2 gap-4 text-center text-sm text-gray-600">
                    <div>
                        <p class="mb-10">Disetujui oleh</p>
                        <p class="font-semibold text-gray-900">Manajemen</p>
                    </div>
                    <div>
                        <p class="mb-10">Diterima oleh</p>
                        <p class="font-semibold text-gray-900">{{ $payroll->employee?->name ?? 'Karyawan' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
