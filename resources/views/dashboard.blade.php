<x-app-layout title="Dashboard">
    @php
        $formatCurrency = fn($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
    @endphp

    <div class="space-y-8">
        <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-indigo-900 to-blue-800 p-8 text-white shadow-2xl">
            <div class="absolute inset-0 opacity-80 bg-[radial-gradient(circle_at_20%_20%,rgba(255,255,255,0.14),transparent_28%),radial-gradient(circle_at_80%_0%,rgba(255,255,255,0.12),transparent_25%),linear-gradient(120deg,rgba(255,255,255,0.05),transparent_30%)]"></div>
            <div class="relative z-10 grid gap-8 lg:grid-cols-[1.2fr,1fr] lg:items-center">
                <div class="space-y-6">
                    <div class="flex flex-wrap items-center gap-3 text-xs uppercase tracking-[0.25em] text-white/70">
                        <span class="flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 font-semibold">Dashboard utama</span>
                        <span class="hidden h-[1px] w-12 bg-white/40 sm:block"></span>
                        <span class="rounded-full bg-white/10 px-3 py-1 font-semibold">{{ now()->translatedFormat('d M Y') }}</span>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <h1 class="text-3xl font-semibold leading-tight sm:text-4xl">Halo, {{ auth()->user()->name }}</h1>
                        <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-medium text-white">{{ auth()->user()->roles->count() }} Role · {{ auth()->user()->allPermissions()->count() }} Permission</span>
                    </div>
                    <p class="max-w-3xl text-base text-white/85">
                        Pantau performa kasir, layanan, dan stok dalam tata letak baru yang ringkas. Semua insight penting tampil tanpa ruang kosong yang mengganggu.
                    </p>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <a href="{{ route('pos.index') }}" class="flex items-center gap-3 rounded-2xl bg-white/10 px-4 py-3 ring-1 ring-white/10 transition hover:-translate-y-0.5 hover:bg-white/15">
                            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 text-xl">🧾</span>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-white/80">Buka POS</p>
                                <p class="text-lg font-semibold">Transaksi baru</p>
                                <p class="text-xs text-white/70">Catat penjualan tanpa menutup dashboard.</p>
                            </div>
                        </a>
                        <a href="{{ route('services.index') }}" class="flex items-center gap-3 rounded-2xl bg-white/10 px-4 py-3 ring-1 ring-white/10 transition hover:-translate-y-0.5 hover:bg-white/15">
                            <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 text-xl">🛠️</span>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-white/80">Service</p>
                                <p class="text-lg font-semibold">Pantau progres</p>
                                <p class="text-xs text-white/70">Lihat unit masuk dan yang menunggu diambil.</p>
                            </div>
                        </a>
                    </div>
                </div>

                <div class="grid gap-4 rounded-2xl bg-white/10 p-5 ring-1 ring-white/15 backdrop-blur-sm sm:grid-cols-2">
                    <div class="space-y-3 rounded-xl bg-white/10 p-4 shadow-inner">
                        <div class="flex items-center justify-between text-xs text-white/80">
                            <span class="uppercase tracking-wide">Penjualan hari ini</span>
                            <span class="rounded-full bg-emerald-300/20 px-2 py-0.5 text-[10px] font-semibold text-emerald-100">Realtime</span>
                        </div>
                        <p class="text-3xl font-semibold">{{ $formatCurrency($todaySales) }}</p>
                        <div class="h-2 rounded-full bg-white/20">
                            <div class="h-full w-[78%] rounded-full bg-emerald-300"></div>
                        </div>
                        <p class="text-xs text-white/80">Ringkasan omzet harian kasir.</p>
                    </div>
                    <div class="space-y-3 rounded-xl bg-white/10 p-4 shadow-inner">
                        <div class="flex items-center justify-between text-xs text-white/80">
                            <span class="uppercase tracking-wide">Transaksi hari ini</span>
                            <span class="rounded-full bg-sky-300/20 px-2 py-0.5 text-[10px] font-semibold text-sky-100">Invoice</span>
                        </div>
                        <p class="text-3xl font-semibold">{{ $transactionsToday }}</p>
                        <div class="h-2 rounded-full bg-white/20">
                            <div class="h-full w-[64%] rounded-full bg-sky-200"></div>
                        </div>
                        <p class="text-xs text-white/80">Jumlah transaksi selesai hingga saat ini.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div class="group relative overflow-hidden rounded-2xl border border-white/60 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="absolute right-2 top-2 h-10 w-10 rounded-full bg-blue-50 blur-xl"></div>
                <div class="flex items-start justify-between gap-3">
                    <div class="space-y-2">
                        <p class="text-xs uppercase tracking-wide text-blue-600">Penjualan hari ini</p>
                        <h3 class="text-2xl font-semibold text-gray-900">{{ $formatCurrency($todaySales) }}</h3>
                        <p class="text-xs text-gray-500">Nilai transaksi tercatat hingga sekarang.</p>
                    </div>
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-50 text-lg">💳</span>
                </div>
            </div>

            <div class="group relative overflow-hidden rounded-2xl border border-white/60 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="absolute right-2 top-2 h-10 w-10 rounded-full bg-indigo-50 blur-xl"></div>
                <div class="flex items-start justify-between gap-3">
                    <div class="space-y-2">
                        <p class="text-xs uppercase tracking-wide text-indigo-600">Penjualan bulan ini</p>
                        <h3 class="text-2xl font-semibold text-gray-900">{{ $formatCurrency($monthlySales) }}</h3>
                        <p class="text-xs text-gray-500">Akumulasi omzet sejak awal bulan.</p>
                    </div>
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-50 text-lg">📈</span>
                </div>
            </div>

            <div class="group relative overflow-hidden rounded-2xl border border-white/60 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="absolute right-2 top-2 h-10 w-10 rounded-full bg-emerald-50 blur-xl"></div>
                <div class="flex items-start justify-between gap-3">
                    <div class="space-y-2">
                        <p class="text-xs uppercase tracking-wide text-emerald-600">Transaksi hari ini</p>
                        <h3 class="text-2xl font-semibold text-gray-900">{{ $transactionsToday }}</h3>
                        <p class="text-xs text-gray-500">Total invoice selesai.</p>
                    </div>
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-50 text-lg">🧾</span>
                </div>
            </div>

            <div class="group relative overflow-hidden rounded-2xl border border-white/60 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <div class="absolute right-2 top-2 h-10 w-10 rounded-full bg-purple-50 blur-xl"></div>
                <div class="flex items-start justify-between gap-3">
                    <div class="space-y-2">
                        <p class="text-xs uppercase tracking-wide text-purple-600">Pelanggan aktif</p>
                        <h3 class="text-2xl font-semibold text-gray-900">{{ $customersCount }}</h3>
                        <p class="text-xs text-gray-500">Kontak pelanggan yang aktif tercatat.</p>
                    </div>
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-purple-50 text-lg">👥</span>
                </div>
            </div>
        </section>

        <section class="grid gap-4 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500">Inventaris & layanan</p>
                        <h2 class="text-xl font-semibold text-gray-900">Ringkasan operasional</h2>
                    </div>
                    <div class="flex items-center gap-2 rounded-full bg-blue-50 px-3 py-2 text-blue-700">
                        <span class="text-lg">🟢</span>
                        <span class="text-xs font-semibold uppercase tracking-wide">Status real-time</span>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="space-y-3 rounded-xl border border-blue-50 bg-blue-50/70 px-4 py-3">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-semibold uppercase tracking-wide text-blue-700">Produk</p>
                            <span class="rounded-full bg-white px-2 py-0.5 text-[10px] font-semibold text-blue-700">Stok</span>
                        </div>
                        <p class="text-2xl font-semibold text-gray-900">{{ $productsCount }}</p>
                        <p class="text-xs text-gray-600">SKU aktif di katalog.</p>
                    </div>
                    <div class="space-y-3 rounded-xl border border-amber-100 bg-amber-50 px-4 py-3">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Layanan berjalan</p>
                            <span class="rounded-full bg-white px-2 py-0.5 text-[10px] font-semibold text-amber-700">Antrian</span>
                        </div>
                        <p class="text-2xl font-semibold text-gray-900">{{ $activeServicesCount }}</p>
                        <p class="text-xs text-gray-600">Unit yang belum diambil pelanggan.</p>
                    </div>
                    <div class="space-y-3 rounded-xl border border-rose-100 bg-rose-50 px-4 py-3">
                        <div class="flex items-center justify-between">
                            <p class="text-xs font-semibold uppercase tracking-wide text-rose-700">Hutang pembelian</p>
                            <span class="rounded-full bg-white px-2 py-0.5 text-[10px] font-semibold text-rose-700">Kas</span>
                        </div>
                        <p class="text-2xl font-semibold text-gray-900">{{ $formatCurrency($outstandingPurchases) }}</p>
                        <p class="text-xs text-gray-600">Nilai invoice supplier berstatus hutang.</p>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2">
                    <div class="rounded-xl border border-dashed border-gray-200 bg-gradient-to-br from-gray-50 to-white px-4 py-3 text-sm text-gray-700">
                        <p class="font-semibold text-gray-900">Rekomendasi harian</p>
                        <p class="text-gray-600">Periksa stok produk laris, update status layanan, dan pastikan catatan kasir selesai sebelum penutupan.</p>
                    </div>
                    <div class="rounded-xl border border-dashed border-gray-200 bg-gradient-to-br from-gray-50 to-white px-4 py-3 text-sm text-gray-700">
                        <p class="font-semibold text-gray-900">Catatan kas</p>
                        <p class="text-gray-600">Pantau hutang pembelian untuk menjaga arus kas sehat dan jadwalkan pembayaran supplier tepat waktu.</p>
                    </div>
                </div>
            </div>

            <div class="space-y-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500">Navigasi</p>
                        <h2 class="text-lg font-semibold text-gray-900">Pintasan cepat</h2>
                    </div>
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-50">⚡</span>
                </div>
                <div class="space-y-3 text-sm">
                    <a href="{{ route('pos.index') }}" class="flex items-center justify-between rounded-xl border border-gray-200 px-4 py-3 transition hover:-translate-y-0.5 hover:border-blue-300 hover:bg-blue-50">
                        <div class="flex items-center gap-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-50 text-blue-700">🧾</span>
                            <div>
                                <p class="font-semibold text-gray-900">Transaksi baru</p>
                                <p class="text-gray-500">Buka POS untuk mencatat penjualan.</p>
                            </div>
                        </div>
                        <span class="text-blue-600">→</span>
                    </a>
                    <a href="{{ route('services.index') }}" class="flex items-center justify-between rounded-xl border border-gray-200 px-4 py-3 transition hover:-translate-y-0.5 hover:border-amber-300 hover:bg-amber-50">
                        <div class="flex items-center gap-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-amber-50 text-amber-700">🧰</span>
                            <div>
                                <p class="font-semibold text-gray-900">Kelola layanan</p>
                                <p class="text-gray-500">Perbarui status service device.</p>
                            </div>
                        </div>
                        <span class="text-amber-600">→</span>
                    </a>
                    <a href="{{ route('products.index') }}" class="flex items-center justify-between rounded-xl border border-gray-200 px-4 py-3 transition hover:-translate-y-0.5 hover:border-green-300 hover:bg-green-50">
                        <div class="flex items-center gap-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-green-50 text-green-700">📦</span>
                            <div>
                                <p class="font-semibold text-gray-900">Inventaris produk</p>
                                <p class="text-gray-500">Atur stok, harga, dan kategori.</p>
                            </div>
                        </div>
                        <span class="text-green-600">→</span>
                    </a>
                    <a href="{{ route('purchases.index') }}" class="flex items-center justify-between rounded-xl border border-gray-200 px-4 py-3 transition hover:-translate-y-0.5 hover:border-purple-300 hover:bg-purple-50">
                        <div class="flex items-center gap-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-purple-50 text-purple-700">🧮</span>
                            <div>
                                <p class="font-semibold text-gray-900">Pembelian supplier</p>
                                <p class="text-gray-500">Catat penerimaan dan pembayaran.</p>
                            </div>
                        </div>
                        <span class="text-purple-600">→</span>
                    </a>
                </div>
            </div>
        </section>

        <section class="grid gap-4 lg:grid-cols-2">
            <div class="space-y-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500">Transaksi</p>
                        <h2 class="text-lg font-semibold text-gray-900">Transaksi terbaru</h2>
                    </div>
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-50">📄</span>
                </div>
                <div class="space-y-3">
                    @forelse ($recentTransactions as $transaction)
                        <div class="flex items-center justify-between rounded-xl border border-gray-100 px-4 py-3 shadow-xs">
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
            </div>

            <div class="space-y-4 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500">Service</p>
                        <h2 class="text-lg font-semibold text-gray-900">Layanan terbaru</h2>
                    </div>
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-50">🧰</span>
                </div>
                <div class="space-y-3">
                    @forelse ($recentServices as $service)
                        <div class="flex items-center justify-between rounded-xl border border-gray-100 px-4 py-3 shadow-xs">
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
            </div>
        </section>
    </div>
</x-app-layout>
