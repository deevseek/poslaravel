<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'POS Dealer') }} - Solusi POS & Servis Multi Tenant</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-900">
    <header class="bg-white shadow-sm">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600 text-white font-bold">PD</div>
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-blue-600">{{ config('app.name', 'POS Dealer') }}</p>
                    <p class="text-xs text-slate-500">Platform POS & servis berbasis tenant</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="#harga" class="text-sm font-medium text-slate-600 hover:text-blue-600">Paket</a>
                <a href="#daftar" class="rounded-full bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Daftar</a>
                <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-blue-600">Login</a>
            </div>
        </div>
    </header>

    <main>
        <section class="mx-auto max-w-6xl px-6 py-16">
            <div class="grid items-center gap-10 lg:grid-cols-2">
                <div>
                    <p class="mb-4 inline-flex items-center rounded-full bg-blue-50 px-4 py-1 text-xs font-semibold uppercase tracking-wide text-blue-600">Multi-tenant siap pakai</p>
                    <h1 class="text-4xl font-bold leading-tight text-slate-900 lg:text-5xl">Kelola POS, servis, stok, dan laporan dalam satu aplikasi.</h1>
                    <p class="mt-4 text-lg text-slate-600">Bangun bisnis Anda lebih cepat dengan sistem POS Dealer yang terintegrasi. Pendaftaran otomatis tercatat sebagai tenant, pembayaran diverifikasi admin, dan akses aktif setelah disetujui.</p>
                    <div class="mt-6 flex flex-wrap gap-4">
                        <a href="#daftar" class="rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow hover:bg-blue-700">Mulai Berlangganan</a>
                        <a href="#fitur" class="rounded-xl border border-slate-200 px-6 py-3 text-sm font-semibold text-slate-700 hover:border-blue-200 hover:text-blue-600">Lihat Fitur</a>
                    </div>
                    <div class="mt-6 flex flex-wrap gap-6 text-sm text-slate-500">
                        <div>
                            <p class="text-lg font-semibold text-slate-900">99.9%</p>
                            <p>uptime sistem</p>
                        </div>
                        <div>
                            <p class="text-lg font-semibold text-slate-900">500+</p>
                            <p>transaksi harian</p>
                        </div>
                        <div>
                            <p class="text-lg font-semibold text-slate-900">24/7</p>
                            <p>monitoring tenant</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-3xl bg-white p-8 shadow-xl">
                    <h2 class="text-xl font-semibold text-slate-900">Apa yang Anda dapatkan?</h2>
                    <ul class="mt-6 space-y-4 text-sm text-slate-600">
                        <li class="flex items-start gap-3">
                            <span class="mt-1 h-2 w-2 rounded-full bg-blue-600"></span>
                            Dashboard penjualan, servis, garansi, dan keuangan real-time.
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="mt-1 h-2 w-2 rounded-full bg-blue-600"></span>
                            Subdomain tenant otomatis untuk tiap pelanggan.
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="mt-1 h-2 w-2 rounded-full bg-blue-600"></span>
                            Workflow verifikasi pembayaran sebelum akun aktif.
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="mt-1 h-2 w-2 rounded-full bg-blue-600"></span>
                            Notifikasi email otomatis saat tenant aktif.
                        </li>
                    </ul>
                    <div class="mt-8 rounded-2xl bg-blue-50 p-4">
                        <p class="text-sm font-semibold text-blue-700">Tidak perlu instalasi rumit.</p>
                        <p class="text-sm text-blue-600">Cukup daftar, lakukan pembayaran, dan tenant Anda langsung siap digunakan setelah disetujui admin.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="fitur" class="bg-white py-16">
            <div class="mx-auto max-w-6xl px-6">
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-slate-900">Fitur unggulan untuk operasional harian</h2>
                    <p class="mt-3 text-slate-600">Semua modul penting dalam satu platform agar bisnis tetap efisien.</p>
                </div>
                <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-6">
                        <h3 class="text-lg font-semibold">POS & Transaksi</h3>
                        <p class="mt-2 text-sm text-slate-600">Kelola transaksi dengan kasir, metode pembayaran, dan cetak struk.</p>
                    </div>
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-6">
                        <h3 class="text-lg font-semibold">Manajemen Servis</h3>
                        <p class="mt-2 text-sm text-slate-600">Pantau status servis, estimasi biaya, dan histori pelanggan.</p>
                    </div>
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-6">
                        <h3 class="text-lg font-semibold">Multi-tenant</h3>
                        <p class="mt-2 text-sm text-slate-600">Setiap klien memiliki database terpisah dan akses aman.</p>
                    </div>
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-6">
                        <h3 class="text-lg font-semibold">Laporan Keuangan</h3>
                        <p class="mt-2 text-sm text-slate-600">Pantau arus kas, laba rugi, dan transaksi harian.</p>
                    </div>
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-6">
                        <h3 class="text-lg font-semibold">Manajemen Stok</h3>
                        <p class="mt-2 text-sm text-slate-600">Pengaturan produk, kategori, stok masuk/keluar real-time.</p>
                    </div>
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-6">
                        <h3 class="text-lg font-semibold">Notifikasi Klien</h3>
                        <p class="mt-2 text-sm text-slate-600">Email otomatis untuk informasi aktivasi dan update penting.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="harga" class="py-16">
            <div class="mx-auto max-w-6xl px-6">
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-slate-900">Pilih paket langganan terbaik</h2>
                    <p class="mt-3 text-slate-600">Harga transparan, sesuai kebutuhan bisnis Anda.</p>
                </div>
                <div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($plans as $plan)
                        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                            <h3 class="text-lg font-semibold">{{ $plan->name }}</h3>
                            <p class="mt-2 text-sm text-slate-500">{{ $plan->billing_cycle }}</p>
                            <p class="mt-4 text-3xl font-bold text-slate-900">Rp {{ number_format($plan->price, 0, ',', '.') }}</p>
                            <ul class="mt-4 space-y-2 text-sm text-slate-600">
                                @forelse ($plan->features ?? [] as $feature)
                                    <li>• {{ $feature }}</li>
                                @empty
                                    <li>• Semua fitur utama</li>
                                    <li>• Support onboarding</li>
                                @endforelse
                            </ul>
                            <a href="#daftar" class="mt-6 inline-flex w-full items-center justify-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Pilih Paket</a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="daftar" class="bg-white py-16">
            <div class="mx-auto max-w-5xl px-6">
                <div class="grid gap-10 lg:grid-cols-2">
                    <div>
                        <h2 class="text-3xl font-bold text-slate-900">Formulir pendaftaran tenant</h2>
                        <p class="mt-3 text-slate-600">Lengkapi data Anda dan unggah bukti pembayaran. Admin akan memverifikasi sebelum tenant diaktifkan.</p>
                        <div class="mt-6 rounded-2xl border border-blue-100 bg-blue-50 p-4 text-sm text-blue-700">
                            <p class="font-semibold">Alur aktivasi</p>
                            <ol class="mt-2 list-decimal space-y-1 pl-4">
                                <li>Isi formulir pendaftaran.</li>
                                <li>Lakukan pembayaran sesuai paket.</li>
                                <li>Admin memverifikasi di menu tenant.</li>
                                <li>Email notifikasi aktivasi dikirim ke Anda.</li>
                            </ol>
                        </div>
                    </div>
                    <div class="rounded-3xl border border-slate-100 bg-slate-50 p-6">
                        @if (session('success'))
                            <div class="mb-4 rounded-xl bg-green-50 p-3 text-sm text-green-700">
                                {{ session('success') }}
                            </div>
                        @endif
                        <form method="POST" action="{{ route('tenant-registrations.store') }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div>
                                <label class="text-sm font-semibold text-slate-700">Nama usaha</label>
                                <input type="text" name="name" value="{{ old('name') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none" required>
                                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-slate-700">Subdomain</label>
                                <div class="mt-1 flex items-center rounded-xl border border-slate-200 bg-white">
                                    <input type="text" name="subdomain" value="{{ old('subdomain') }}" class="w-full rounded-xl px-3 py-2 text-sm focus:outline-none" placeholder="nama-bisnis" required>
                                    <span class="px-3 text-xs text-slate-400">.posdealer</span>
                                </div>
                                @error('subdomain')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-slate-700">Paket langganan</label>
                                <select name="plan_id" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none" required>
                                    <option value="">Pilih paket</option>
                                    @foreach ($plans as $plan)
                                        <option value="{{ $plan->id }}" @selected(old('plan_id') == $plan->id)>
                                            {{ $plan->name }} - Rp {{ number_format($plan->price, 0, ',', '.') }} / {{ $plan->billing_cycle }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('plan_id')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="text-sm font-semibold text-slate-700">Nama admin</label>
                                    <input type="text" name="admin_name" value="{{ old('admin_name') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                                    @error('admin_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-slate-700">Nomor telepon</label>
                                    <input type="text" name="phone" value="{{ old('phone') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                                    @error('phone')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-slate-700">Email</label>
                                <input type="email" name="email" value="{{ old('email') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none" required>
                                @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label class="text-sm font-semibold text-slate-700">Password</label>
                                    <input type="password" name="password" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none" required>
                                    @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label class="text-sm font-semibold text-slate-700">Konfirmasi password</label>
                                    <input type="password" name="password_confirmation" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none" required>
                                </div>
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-slate-700">Metode pembayaran</label>
                                <select name="payment_method" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none" required>
                                    <option value="">Pilih metode</option>
                                    <option value="transfer" @selected(old('payment_method') === 'transfer')>Transfer Bank</option>
                                    <option value="e-wallet" @selected(old('payment_method') === 'e-wallet')>E-Wallet</option>
                                    <option value="cash" @selected(old('payment_method') === 'cash')>Tunai</option>
                                </select>
                                @error('payment_method')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-slate-700">Referensi pembayaran (opsional)</label>
                                <input type="text" name="payment_reference" value="{{ old('payment_reference') }}" class="mt-1 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none" placeholder="No. transfer / bukti pembayaran">
                                @error('payment_reference')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-slate-700">Upload bukti pembayaran (opsional)</label>
                                <input type="file" name="payment_proof" class="mt-1 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                                @error('payment_proof')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div class="flex items-start gap-2">
                                <input type="checkbox" name="agreement" value="1" class="mt-1" @checked(old('agreement')) required>
                                <label class="text-xs text-slate-600">Saya menyetujui syarat dan ketentuan serta kebijakan pembayaran.</label>
                            </div>
                            @error('agreement')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                            <button type="submit" class="w-full rounded-xl bg-blue-600 px-4 py-3 text-sm font-semibold text-white hover:bg-blue-700">Kirim Pendaftaran</button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-slate-200 bg-white">
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-3 px-6 py-6 text-sm text-slate-500 md:flex-row">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'POS Dealer') }}. Semua hak dilindungi.</p>
            <div class="flex gap-4">
                <a href="#daftar" class="hover:text-blue-600">Daftar</a>
                <a href="{{ route('login') }}" class="hover:text-blue-600">Login Admin</a>
            </div>
        </div>
    </footer>
</body>
</html>
