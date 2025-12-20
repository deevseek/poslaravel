<x-app-layout title="Dashboard">
    @php
        $formatCurrency = fn($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
    @endphp

    <div class="space-y-6">
        <section class="rounded-2xl bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 p-6 text-white shadow-xl">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm uppercase tracking-wide text-blue-100">Selamat datang kembali</p>
                    <h1 class="text-3xl font-semibold">{{ auth()->user()->name }}</h1>
                    <p class="mt-2 text-blue-100">
                        Pantau performa penjualan, layanan, dan stok dalam satu tampilan profesional.
                    </p>
                </div>
                <div class="flex items-center gap-3 rounded-xl bg-white/10 px-4 py-3 backdrop-blur">
                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-white/20 text-2xl">💻</span>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-blue-100">Hak akses</p>
                        <div class="flex items-center gap-2 text-sm font-semibold">
                            <span class="rounded-full bg-white/20 px-3 py-1">{{ auth()->user()->roles->count() }} Role</span>
                            <span class="rounded-full bg-white/20 px-3 py-1">{{ auth()->user()->allPermissions()->count() }} Permission</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-blue-100 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Penjualan Hari Ini</p>
                        <h3 class="mt-2 text-2xl font-semibold text-gray-900">{{ $formatCurrency($todaySales) }}</h3>
                    </div>
                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-50 text-blue-600">💰</span>
                </div>
                <p class="mt-3 text-xs text-gray-500">Total nilai transaksi yang tercatat hari ini.</p>
            </div>

            <div class="rounded-xl border border-indigo-100 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Penjualan Bulan Ini</p>
                        <h3 class="mt-2 text-2xl font-semibold text-gray-900">{{ $formatCurrency($monthlySales) }}</h3>
                    </div>
                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-50 text-indigo-600">📈</span>
                </div>
                <p class="mt-3 text-xs text-gray-500">Akumulasi penjualan sejak awal bulan berjalan.</p>
            </div>

            <div class="rounded-xl border border-emerald-100 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Transaksi Hari Ini</p>
                        <h3 class="mt-2 text-2xl font-semibold text-gray-900">{{ $transactionsToday }}</h3>
                    </div>
                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">🧾</span>
                </div>
                <p class="mt-3 text-xs text-gray-500">Jumlah invoice yang diproses pada hari ini.</p>
            </div>

            <div class="rounded-xl border border-purple-100 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Pelanggan Aktif</p>
                        <h3 class="mt-2 text-2xl font-semibold text-gray-900">{{ $customersCount }}</h3>
                    </div>
                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-purple-50 text-purple-600">👥</span>
                </div>
                <p class="mt-3 text-xs text-gray-500">Jumlah pelanggan yang tercatat di sistem.</p>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            <section class="lg:col-span-2 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500">Inventaris & Layanan</p>
                        <h2 class="text-xl font-semibold text-gray-900">Kesehatan Operasional</h2>
                    </div>
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-50 text-blue-600">🛠️</span>
                </div>
                <div class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="rounded-lg border border-blue-50 bg-blue-50/50 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">Produk</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $productsCount }}</p>
                        <p class="text-xs text-gray-500">SKU aktif di katalog.</p>
                    </div>
                    <div class="rounded-lg border border-amber-50 bg-amber-50/60 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-600">Layanan Berjalan</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $activeServicesCount }}</p>
                        <p class="text-xs text-gray-500">Unit yang belum diambil pelanggan.</p>
                    </div>
                    <div class="rounded-lg border border-rose-50 bg-rose-50/60 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-rose-600">Hutang Pembelian</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $formatCurrency($outstandingPurchases) }}</p>
                        <p class="text-xs text-gray-500">Nilai invoice supplier berstatus hutang.</p>
                    </div>
                </div>
                <div class="mt-6 rounded-lg border border-dashed border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700">
                    Gunakan menu sidebar untuk menambah produk, mencatat pembelian, atau memperbarui status layanan.
                </div>
            </section>

            <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Aksi Cepat</h2>
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-50">⚡</span>
                </div>
                <div class="mt-4 space-y-3 text-sm">
                    <a href="{{ route('pos.index') }}" class="flex items-center justify-between rounded-lg border border-gray-200 px-4 py-3 hover:border-blue-300 hover:bg-blue-50">
                        <span>Transaksi Baru</span>
                        <span class="text-blue-600">→</span>
                    </a>
                    <a href="{{ route('services.index') }}" class="flex items-center justify-between rounded-lg border border-gray-200 px-4 py-3 hover:border-blue-300 hover:bg-blue-50">
                        <span>Kelola Layanan</span>
                        <span class="text-blue-600">→</span>
                    </a>
                    <a href="{{ route('products.index') }}" class="flex items-center justify-between rounded-lg border border-gray-200 px-4 py-3 hover:border-blue-300 hover:bg-blue-50">
                        <span>Inventaris Produk</span>
                        <span class="text-blue-600">→</span>
                    </a>
                    <a href="{{ route('purchases.index') }}" class="flex items-center justify-between rounded-lg border border-gray-200 px-4 py-3 hover:border-blue-300 hover:bg-blue-50">
                        <span>Pembelian Supplier</span>
                        <span class="text-blue-600">→</span>
                    </a>
                </div>
            </section>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Transaksi Terbaru</h2>
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-50">📄</span>
                </div>
                <div class="mt-4 space-y-3">
                    @forelse ($recentTransactions as $transaction)
                        <div class="flex items-center justify-between rounded-lg border border-gray-100 px-4 py-3">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $transaction->invoice_number ?? 'Invoice #' . $transaction->id }}</p>
                                <p class="text-xs text-gray-500">{{ optional($transaction->customer)->name ?? 'Pelanggan umum' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-900">{{ $formatCurrency($transaction->total) }}</p>
                                <p class="text-xs text-gray-500">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Belum ada transaksi tercatat.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Layanan Terbaru</h2>
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-50">🧰</span>
                </div>
                <div class="mt-4 space-y-3">
                    @forelse ($recentServices as $service)
                        <div class="flex items-center justify-between rounded-lg border border-gray-100 px-4 py-3">
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $service->device ?? 'Perangkat' }}</p>
                                <p class="text-xs text-gray-500">{{ optional($service->customer)->name ?? 'Pelanggan umum' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-900">{{ ucfirst($service->status) }}</p>
                                <p class="text-xs text-gray-500">{{ $service->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Belum ada layanan yang tercatat.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
