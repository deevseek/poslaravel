<x-app-layout title="Dashboard">
@php
    $formatCurrency = fn($v) => 'Rp ' . number_format((float)$v,0,',','.');
@endphp

<div class="space-y-8">

    {{-- ================= HEADER ================= --}}
    <div class="flex flex-col gap-2">
        <h1 class="text-2xl font-semibold text-gray-900">
            Dashboard
        </h1>
        <p class="text-sm text-gray-500">
            Ringkasan kinerja penjualan, layanan, dan inventaris hari ini.
        </p>
    </div>

    {{-- ================= KPI ================= --}}
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl bg-white p-5 shadow border">
            <p class="text-xs uppercase text-gray-500">Penjualan Hari Ini</p>
            <p class="mt-2 text-2xl font-semibold text-gray-900">
                {{ $formatCurrency($todaySales) }}
            </p>
        </div>

        <div class="rounded-xl bg-white p-5 shadow border">
            <p class="text-xs uppercase text-gray-500">Penjualan Bulan Ini</p>
            <p class="mt-2 text-2xl font-semibold text-gray-900">
                {{ $formatCurrency($monthlySales) }}
            </p>
        </div>

        <div class="rounded-xl bg-white p-5 shadow border">
            <p class="text-xs uppercase text-gray-500">Transaksi Hari Ini</p>
            <p class="mt-2 text-2xl font-semibold text-gray-900">
                {{ $transactionsToday }}
            </p>
        </div>

        <div class="rounded-xl bg-white p-5 shadow border">
            <p class="text-xs uppercase text-gray-500">Pelanggan Aktif</p>
            <p class="mt-2 text-2xl font-semibold text-gray-900">
                {{ $customersCount }}
            </p>
        </div>
    </div>

    {{-- ================= CHART ================= --}}
    <div class="grid gap-4 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-xl bg-white p-6 shadow border">
            <h2 class="mb-4 text-lg font-semibold text-gray-900">
                Tren Keuangan (7 Hari Terakhir)
            </h2>
            <canvas id="salesChart" height="120"></canvas>
        </div>

        <div class="rounded-xl bg-white p-6 shadow border space-y-4">
            <h2 class="text-lg font-semibold text-gray-900">
                Ringkasan Operasional
            </h2>

            <div>
                <p class="text-xs text-gray-500">Produk Aktif</p>
                <p class="text-xl font-semibold">{{ $productsCount }}</p>
            </div>

            <div>
                <p class="text-xs text-gray-500">Layanan Berjalan</p>
                <p class="text-xl font-semibold">{{ $activeServicesCount }}</p>
            </div>

            <div>
                <p class="text-xs text-gray-500">Hutang Pembelian</p>
                <p class="text-xl font-semibold">
                    {{ $formatCurrency($outstandingPurchases) }}
                </p>
            </div>
        </div>
    </div>

    {{-- ================= ACTIVITY ================= --}}
    <div class="grid gap-4 lg:grid-cols-2">
        <div class="rounded-xl bg-white p-6 shadow border">
            <h2 class="mb-4 text-lg font-semibold">Transaksi Terbaru</h2>

            @forelse ($recentTransactions as $t)
                <div class="flex justify-between border-b py-3 last:border-0">
                    <div>
                        <p class="font-medium">{{ $t->invoice_number ?? 'Invoice #'.$t->id }}</p>
                        <p class="text-xs text-gray-500">
                            {{ optional($t->customer)->name ?? 'Pelanggan umum' }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="font-medium">{{ $formatCurrency($t->total) }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $t->created_at->format('d M Y') }}
                        </p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Belum ada transaksi.</p>
            @endforelse
        </div>

        <div class="rounded-xl bg-white p-6 shadow border">
            <h2 class="mb-4 text-lg font-semibold">Layanan Terbaru</h2>

            @forelse ($recentServices as $s)
                <div class="flex justify-between border-b py-3 last:border-0">
                    <div>
                        <p class="font-medium">{{ $s->device }}</p>
                        <p class="text-xs text-gray-500">
                            {{ optional($s->customer)->name ?? 'Pelanggan umum' }}
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="font-medium">{{ ucfirst($s->status) }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $s->created_at->format('d M Y') }}
                        </p>
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Belum ada layanan.</p>
            @endforelse
        </div>
    </div>

</div>

{{-- ================= CHART.JS ================= --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('salesChart');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($financeChartLabels) !!},
        datasets: [
            {
                label: 'Pemasukan',
                data: {!! json_encode($financeChartIncome) !!},
                borderColor: '#16a34a',
                backgroundColor: 'rgba(22,163,74,0.12)',
                tension: 0.4,
                fill: true,
            },
            {
                label: 'Pengeluaran',
                data: {!! json_encode($financeChartExpense) !!},
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239,68,68,0.12)',
                tension: 0.4,
                fill: true,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: true }
        },
        scales: {
            y: {
                ticks: {
                    callback: value =>
                        'Rp ' + value.toLocaleString('id-ID')
                }
            }
        }
    }
});
</script>
</x-app-layout>
