<x-app-layout title="Dashboard">
    @php
        $formatCurrency = fn($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
    @endphp

    <div class="space-y-8">
        <section class="relative isolate overflow-hidden rounded-2xl bg-gradient-to-r from-blue-800 via-indigo-800 to-purple-800 p-6 text-white shadow-xl">
            <div class="absolute inset-0 bg-gradient-to-br from-white/15 via-white/5 to-transparent opacity-60"></div>
            <div class="absolute -right-16 -top-12 h-48 w-48 rounded-full bg-white/15 blur-3xl"></div>

            <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
                <div class="space-y-2 drop-shadow-sm">
                    <p class="text-xs uppercase tracking-[0.2em] text-white/80">Dashboard utama</p>
                    <h1 class="text-3xl font-semibold">Halo, {{ auth()->user()->name }}</h1>
                    <p class="max-w-2xl text-white/90">
                        Ringkasan performa penjualan, layanan, dan inventaris dalam satu panel yang bersih dan mudah dipindai.
                    </p>
                    <div class="flex flex-wrap gap-2 text-xs font-semibold text-white">
                        <span class="rounded-full bg-white/15 px-3 py-1">{{ auth()->user()->roles->count() }} Role</span>
                        <span class="rounded-full bg-white/15 px-3 py-1">{{ auth()->user()->allPermissions()->count() }} Permission</span>
                    </div>
                </div>
                <div class="grid w-full max-w-xl gap-3 sm:grid-cols-2">
                    <div class="rounded-xl bg-white/15 p-4 ring-1 ring-white/10 backdrop-blur">
                        <p class="text-xs uppercase tracking-wide text-white/80">Penjualan hari ini</p>
                        <p class="mt-2 text-2xl font-semibold">{{ $formatCurrency($todaySales) }}</p>
                        <p class="text-xs text-white/80">Monitor aktivitas kasir harian.</p>
                    </div>
                    <div class="rounded-xl bg-white/15 p-4 ring-1 ring-white/10 backdrop-blur">
                        <p class="text-xs uppercase tracking-wide text-white/80">Transaksi hari ini</p>
                        <p class="mt-2 text-2xl font-semibold">{{ $transactionsToday }}</p>
                        <p class="text-xs text-white/80">Invoice terproses sampai saat ini.</p>
                    </div>
                    <div class="rounded-xl bg-white/15 p-4 ring-1 ring-white/10 backdrop-blur">
                        <p class="text-xs uppercase tracking-wide text-white/80">Penjualan bulan ini</p>
                        <p class="mt-2 text-2xl font-semibold">{{ $formatCurrency($monthlySales) }}</p>
                        <p class="text-xs text-white/80">Akumulasi omzet periode berjalan.</p>
                    </div>
                    <div class="rounded-xl bg-white/15 p-4 ring-1 ring-white/10 backdrop-blur">
                        <p class="text-xs uppercase tracking-wide text-white/80">Pelanggan aktif</p>
                        <p class="mt-2 text-2xl font-semibold">{{ $customersCount }}</p>
                        <p class="text-xs text-white/80">Total akun yang terdaftar.</p>
                    </div>
                </div>
            </div>
        </section>

        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div class="rounded-xl border border-blue-100 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-blue-600">Penjualan hari ini</p>
                        <h3 class="mt-2 text-2xl font-semibold text-gray-900">{{ $formatCurrency($todaySales) }}</h3>
                    </div>
                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-50 text-lg">💰</span>
                </div>
                <p class="mt-3 text-sm text-gray-500">Total nilai transaksi yang tercatat sampai saat ini.</p>
            </div>

            <div class="rounded-xl border border-indigo-100 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-indigo-600">Penjualan bulan ini</p>
                        <h3 class="mt-2 text-2xl font-semibold text-gray-900">{{ $formatCurrency($monthlySales) }}</h3>
                    </div>
                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-indigo-50 text-lg">📈</span>
                </div>
                <p class="mt-3 text-sm text-gray-500">Akumulasi omzet sejak awal bulan berjalan.</p>
            </div>

            <div class="rounded-xl border border-emerald-100 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-emerald-600">Transaksi hari ini</p>
                        <h3 class="mt-2 text-2xl font-semibold text-gray-900">{{ $transactionsToday }}</h3>
                    </div>
                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-lg">🧾</span>
                </div>
                <p class="mt-3 text-sm text-gray-500">Jumlah invoice yang sudah diproses.</p>
            </div>

            <div class="rounded-xl border border-purple-100 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-purple-600">Pelanggan aktif</p>
                        <h3 class="mt-2 text-2xl font-semibold text-gray-900">{{ $customersCount }}</h3>
                    </div>
                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-purple-50 text-lg">👥</span>
                </div>
                <p class="mt-3 text-sm text-gray-500">Jumlah pelanggan tersimpan di sistem.</p>
            </div>
        </div>

        <div class="grid gap-4 lg:grid-cols-3">
            <section class="lg:col-span-2 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500">Inventaris & layanan</p>
                        <h2 class="text-xl font-semibold text-gray-900">Ringkasan operasional</h2>
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
                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-600">Layanan berjalan</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $activeServicesCount }}</p>
                        <p class="text-xs text-gray-500">Unit yang belum diambil pelanggan.</p>
                    </div>
                    <div class="rounded-lg border border-rose-50 bg-rose-50/60 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wide text-rose-600">Hutang pembelian</p>
                        <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $formatCurrency($outstandingPurchases) }}</p>
                        <p class="text-xs text-gray-500">Nilai invoice supplier berstatus hutang.</p>
                    </div>
                </div>
                <div class="mt-6 grid gap-3 md:grid-cols-2">
                    <div class="rounded-lg border border-dashed border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700">
                        <p class="font-semibold text-gray-900">Rekomendasi harian</p>
                        <p class="text-gray-600">Periksa stok produk laris dan pastikan status layanan diperbarui sebelum penutupan kasir.</p>
                    </div>
                    <div class="rounded-lg border border-dashed border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700">
                        <p class="font-semibold text-gray-900">Catatan kas</p>
                        <p class="text-gray-600">Pantau hutang pembelian untuk menjaga arus kas tetap sehat.</p>
                    </div>
                </div>
            </section>

            <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Pintasan cepat</h2>
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-50">⚡</span>
                </div>
                <div class="mt-4 space-y-3 text-sm">
                    <a href="{{ route('pos.index') }}" class="flex items-center justify-between rounded-lg border border-gray-200 px-4 py-3 transition hover:-translate-y-0.5 hover:border-blue-300 hover:bg-blue-50">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-50">🧾</span>
                            <div>
                                <p class="font-semibold text-gray-900">Transaksi baru</p>
                                <p class="text-gray-500">Buka POS untuk mencatat penjualan.</p>
                            </div>
                        </div>
                        <span class="text-blue-600">→</span>
                    </a>
                    <a href="{{ route('services.index') }}" class="flex items-center justify-between rounded-lg border border-gray-200 px-4 py-3 transition hover:-translate-y-0.5 hover:border-blue-300 hover:bg-blue-50">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-50">🧰</span>
                            <div>
                                <p class="font-semibold text-gray-900">Kelola layanan</p>
                                <p class="text-gray-500">Perbarui status service device.</p>
                            </div>
                        </div>
                        <span class="text-blue-600">→</span>
                    </a>
                    <a href="{{ route('products.index') }}" class="flex items-center justify-between rounded-lg border border-gray-200 px-4 py-3 transition hover:-translate-y-0.5 hover:border-blue-300 hover:bg-blue-50">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-green-50">📦</span>
                            <div>
                                <p class="font-semibold text-gray-900">Inventaris produk</p>
                                <p class="text-gray-500">Atur stok, harga, dan kategori.</p>
                            </div>
                        </div>
                        <span class="text-blue-600">→</span>
                    </a>
                    <a href="{{ route('purchases.index') }}" class="flex items-center justify-between rounded-lg border border-gray-200 px-4 py-3 transition hover:-translate-y-0.5 hover:border-blue-300 hover:bg-blue-50">
                        <div class="flex items-center gap-3">
                            <span class="flex h-8 w-8 items-center justify-center rounded-full bg-purple-50">🧮</span>
                            <div>
                                <p class="font-semibold text-gray-900">Pembelian supplier</p>
                                <p class="text-gray-500">Catat penerimaan dan pembayaran.</p>
                            </div>
                        </div>
                        <span class="text-blue-600">→</span>
                    </a>
                </div>
            </section>
        </div>

        <div class="grid gap-4 lg:grid-cols-2">
            <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Transaksi terbaru</h2>
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
                    <h2 class="text-lg font-semibold text-gray-900">Layanan terbaru</h2>
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
