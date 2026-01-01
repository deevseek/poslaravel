<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'POS Dealer') }} - Solusi POS & Servis Multi Tenant</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100">
    <div class="relative overflow-hidden">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -left-48 top-0 h-96 w-96 rounded-full bg-blue-500/20 blur-[140px]"></div>
            <div class="absolute right-0 top-32 h-80 w-80 rounded-full bg-cyan-400/20 blur-[120px]"></div>
            <div class="absolute bottom-0 left-1/3 h-72 w-72 rounded-full bg-indigo-500/20 blur-[120px]"></div>
        </div>

        <header class="relative z-10 border-b border-white/10 bg-slate-950/80 backdrop-blur">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-500 via-indigo-500 to-cyan-400 text-sm font-bold text-white shadow-lg shadow-blue-500/30">PD</div>
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.3em] text-blue-200">{{ config('app.name', 'POS Dealer') }}</p>
                        <p class="text-xs text-slate-400">POS berlangganan siap pakai untuk bisnis modern</p>
                    </div>
                </div>
                <div class="hidden items-center gap-6 md:flex">
                    <a href="#fitur" class="text-sm font-medium text-slate-300 hover:text-white">Fitur</a>
                    <a href="#harga" class="text-sm font-medium text-slate-300 hover:text-white">Harga</a>
                    <a href="#daftar" class="text-sm font-medium text-slate-300 hover:text-white">Daftar</a>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('login') }}" class="hidden rounded-full border border-white/20 px-4 py-2 text-xs font-semibold text-white hover:border-white/40 md:inline-flex">Login</a>
                    <a href="#daftar" class="rounded-full bg-white px-4 py-2 text-xs font-semibold text-slate-950 shadow-lg shadow-white/20 hover:bg-slate-100">Coba Gratis</a>
                </div>
            </div>
        </header>

        <main class="relative z-10">
            <section class="mx-auto grid max-w-6xl items-center gap-12 px-6 py-16 lg:grid-cols-2">
                <div>
                    <p class="mb-6 inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-1 text-xs font-semibold uppercase tracking-widest text-blue-200">
                        <span class="inline-flex h-2 w-2 rounded-full bg-cyan-300"></span>
                        Solusi POS + servis multi-tenant
                    </p>
                    <h1 class="text-4xl font-semibold leading-tight text-white lg:text-5xl">Tampil profesional, operasional terkendali, dan bisnis siap bertumbuh.</h1>
                    <p class="mt-5 text-lg text-slate-300">Platform POS berlangganan dengan subdomain otomatis, modul servis lengkap, serta laporan real-time. Semua sudah termasuk domain, hosting, dan maintenance.</p>
                    <div class="mt-8 flex flex-wrap gap-4">
                        <a href="#daftar" class="rounded-xl bg-gradient-to-r from-blue-400 via-indigo-400 to-cyan-300 px-6 py-3 text-sm font-semibold text-slate-950 shadow-lg shadow-cyan-400/30">Mulai Sekarang</a>
                        <a href="#harga" class="rounded-xl border border-white/20 px-6 py-3 text-sm font-semibold text-white hover:border-white/40">Lihat Paket</a>
                    </div>
                    <div class="mt-8 grid gap-4 sm:grid-cols-3">
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <p class="text-xl font-semibold text-white">24/7</p>
                            <p class="text-xs text-slate-300">Monitoring server</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <p class="text-xl font-semibold text-white">10 menit</p>
                            <p class="text-xs text-slate-300">Setup & aktivasi</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/5 p-4">
                            <p class="text-xl font-semibold text-white">99.9%</p>
                            <p class="text-xs text-slate-300">Uptime infrastruktur</p>
                        </div>
                    </div>
                </div>
                <div class="rounded-[32px] border border-white/10 bg-white/5 p-8 shadow-2xl shadow-blue-500/20">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-[0.2em] text-blue-200">Dashboard Live</p>
                            <p class="text-xl font-semibold text-white">Ringkasan operasional</p>
                        </div>
                        <span class="rounded-full border border-white/10 bg-white/10 px-3 py-1 text-xs text-slate-200">Realtime</span>
                    </div>
                    <div class="mt-6 grid gap-4">
                        <div class="rounded-2xl border border-white/10 bg-slate-900/60 p-4">
                            <p class="text-xs text-slate-400">Pendapatan bulan ini</p>
                            <p class="text-2xl font-semibold text-white">Rp 245.000.000</p>
                            <p class="mt-2 text-xs text-emerald-300">+18% vs bulan lalu</p>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl border border-white/10 bg-slate-900/60 p-4">
                                <p class="text-xs text-slate-400">Servis aktif</p>
                                <p class="text-xl font-semibold text-white">128 unit</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-slate-900/60 p-4">
                                <p class="text-xs text-slate-400">Stok kritis</p>
                                <p class="text-xl font-semibold text-white">14 item</p>
                            </div>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-gradient-to-r from-blue-500/20 via-indigo-500/20 to-cyan-400/20 p-4">
                            <p class="text-sm font-semibold text-white">Semua terpusat dalam satu dashboard responsif.</p>
                            <p class="mt-2 text-xs text-slate-300">Dari transaksi kasir, servis, hingga laporan keuangan & stok.</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <section id="fitur" class="bg-slate-950 py-16">
        <div class="mx-auto max-w-6xl px-6">
            <div class="text-center">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-blue-200">Fitur unggulan</p>
                <h2 class="mt-3 text-3xl font-semibold text-white">Semua modul penting bisnis dalam satu platform.</h2>
                <p class="mt-3 text-slate-300">Dirancang untuk toko, servis, dan bisnis ritel yang ingin lebih rapi dan efisien.</p>
            </div>
            <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-2xl border border-white/10 bg-white/5 p-6">
                    <h3 class="text-lg font-semibold text-white">POS & Kasir</h3>
                    <p class="mt-2 text-sm text-slate-300">Transaksi cepat, multi metode pembayaran, dan cetak struk instan.</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-6">
                    <h3 class="text-lg font-semibold text-white">Manajemen Servis</h3>
                    <p class="mt-2 text-sm text-slate-300">Status servis terpantau dengan estimasi biaya dan notifikasi pelanggan.</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-6">
                    <h3 class="text-lg font-semibold text-white">Subdomain Otomatis</h3>
                    <p class="mt-2 text-sm text-slate-300">Tanpa domain sendiri, bisnis langsung online dengan subdomain aktif.</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-6">
                    <h3 class="text-lg font-semibold text-white">Laporan Keuangan</h3>
                    <p class="mt-2 text-sm text-slate-300">Laba rugi, arus kas, dan laporan pajak siap unduh.</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-6">
                    <h3 class="text-lg font-semibold text-white">Stok & Inventori</h3>
                    <p class="mt-2 text-sm text-slate-300">Pantau stok real-time, multi gudang, dan peringatan restock.</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-6">
                    <h3 class="text-lg font-semibold text-white">Hosting Dikelola</h3>
                    <p class="mt-2 text-sm text-slate-300">Server, backup, dan update aplikasi dikelola tim kami.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-slate-900 py-16">
        <div class="mx-auto grid max-w-6xl items-center gap-10 px-6 lg:grid-cols-2">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-200">Kenapa memilih kami</p>
                <h2 class="mt-3 text-3xl font-semibold text-white">Operasional bisnis lebih rapi dengan alur yang jelas.</h2>
                <p class="mt-3 text-slate-300">Langganan sudah termasuk setup teknis, pelatihan awal, dan support onboarding. Anda fokus pada penjualan, kami urus teknologinya.</p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                    <p class="text-lg font-semibold text-white">Onboarding cepat</p>
                    <p class="mt-2 text-sm text-slate-300">Tim kami bantu dari aktivasi hingga data awal siap.</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                    <p class="text-lg font-semibold text-white">Support responsif</p>
                    <p class="mt-2 text-sm text-slate-300">Chat & email support di jam kerja, cepat ditangani.</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                    <p class="text-lg font-semibold text-white">Multi lokasi</p>
                    <p class="mt-2 text-sm text-slate-300">Kelola beberapa cabang dalam satu dashboard.</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-5">
                    <p class="text-lg font-semibold text-white">Keamanan data</p>
                    <p class="mt-2 text-sm text-slate-300">Enkripsi & backup otomatis setiap hari.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="harga" class="bg-slate-950 py-16">
        <div class="mx-auto max-w-6xl px-6">
            <div class="text-center">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-blue-200">Paket berlangganan</p>
                <h2 class="mt-3 text-3xl font-semibold text-white">Harga transparan, tanpa biaya tersembunyi.</h2>
                <p class="mt-3 text-slate-300">Semua paket sudah termasuk domain, hosting, dan support.</p>
            </div>
            <div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($plans as $plan)
                    <div class="group relative rounded-3xl border border-white/10 bg-white/5 p-6 transition hover:-translate-y-1 hover:border-cyan-400/40 hover:bg-white/10">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-white">{{ $plan->name }}</h3>
                            <span class="rounded-full border border-white/10 px-3 py-1 text-xs text-slate-300">{{ $plan->billing_cycle }}</span>
                        </div>
                        <p class="mt-4 text-3xl font-semibold text-white">Rp {{ number_format($plan->price, 0, ',', '.') }}</p>
                        <ul class="mt-4 space-y-2 text-sm text-slate-300">
                            @forelse ($plan->features ?? [] as $feature)
                                <li>• {{ $feature }}</li>
                            @empty
                                <li>• Semua fitur utama</li>
                                <li>• Support onboarding</li>
                            @endforelse
                        </ul>
                        <a href="#daftar" class="mt-6 inline-flex w-full items-center justify-center rounded-xl bg-white px-4 py-2 text-sm font-semibold text-slate-950 shadow-lg shadow-white/10 transition group-hover:bg-slate-100">Pilih Paket</a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section id="daftar" class="bg-slate-900 py-16">
        <div class="mx-auto max-w-5xl px-6">
            <div class="grid gap-10 lg:grid-cols-2">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-cyan-200">Mulai berlangganan</p>
                    <h2 class="mt-3 text-3xl font-semibold text-white">Formulir pendaftaran yang simpel dan jelas.</h2>
                    <p class="mt-3 text-slate-300">Lengkapi data, pilih paket, dan lakukan pembayaran. Admin memverifikasi dan akun POS aktif dalam hitungan jam.</p>
                    <div class="mt-6 rounded-2xl border border-white/10 bg-white/5 p-5 text-sm text-slate-200">
                        <p class="font-semibold text-white">Alur aktivasi</p>
                        <ol class="mt-2 list-decimal space-y-1 pl-4">
                            <li>Isi formulir pendaftaran.</li>
                            <li>Lakukan pembayaran sesuai paket.</li>
                            <li>Admin memverifikasi di menu tenant.</li>
                            <li>Email notifikasi aktivasi dikirim ke Anda.</li>
                        </ol>
                    </div>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/5 p-6 shadow-lg shadow-blue-500/10">
                    @if (session('success'))
                        <div class="mb-4 rounded-xl border border-emerald-400/30 bg-emerald-400/10 p-3 text-sm text-emerald-200">
                            {{ session('success') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('tenant-registrations.store') }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="text-sm font-semibold text-slate-200">Nama usaha</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="mt-1 w-full rounded-xl border border-white/10 bg-slate-950/40 px-3 py-2 text-sm text-white focus:border-cyan-300 focus:outline-none" required>
                            @error('name')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-slate-200">Subdomain</label>
                            <div class="mt-1 flex items-center rounded-xl border border-white/10 bg-slate-950/40">
                                <input type="text" name="subdomain" value="{{ old('subdomain') }}" class="w-full rounded-xl bg-transparent px-3 py-2 text-sm text-white focus:outline-none" placeholder="nama-bisnis" required>
                                <span class="px-3 text-xs text-slate-400">.posdealer</span>
                            </div>
                            @error('subdomain')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-slate-200">Paket langganan</label>
                            <select name="plan_id" class="mt-1 w-full rounded-xl border border-white/10 bg-slate-950/40 px-3 py-2 text-sm text-white focus:border-cyan-300 focus:outline-none" required>
                                <option value="">Pilih paket</option>
                                @foreach ($plans as $plan)
                                    <option value="{{ $plan->id }}" @selected(old('plan_id') == $plan->id)>
                                        {{ $plan->name }} - Rp {{ number_format($plan->price, 0, ',', '.') }} / {{ $plan->billing_cycle }}
                                    </option>
                                @endforeach
                            </select>
                            @error('plan_id')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="text-sm font-semibold text-slate-200">Nama admin</label>
                                <input type="text" name="admin_name" value="{{ old('admin_name') }}" class="mt-1 w-full rounded-xl border border-white/10 bg-slate-950/40 px-3 py-2 text-sm text-white focus:border-cyan-300 focus:outline-none">
                                @error('admin_name')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-slate-200">Nomor telepon</label>
                                <input type="text" name="phone" value="{{ old('phone') }}" class="mt-1 w-full rounded-xl border border-white/10 bg-slate-950/40 px-3 py-2 text-sm text-white focus:border-cyan-300 focus:outline-none">
                                @error('phone')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-slate-200">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="mt-1 w-full rounded-xl border border-white/10 bg-slate-950/40 px-3 py-2 text-sm text-white focus:border-cyan-300 focus:outline-none" required>
                            @error('email')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="text-sm font-semibold text-slate-200">Password</label>
                                <input type="password" name="password" class="mt-1 w-full rounded-xl border border-white/10 bg-slate-950/40 px-3 py-2 text-sm text-white focus:border-cyan-300 focus:outline-none" required>
                                @error('password')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-slate-200">Konfirmasi password</label>
                                <input type="password" name="password_confirmation" class="mt-1 w-full rounded-xl border border-white/10 bg-slate-950/40 px-3 py-2 text-sm text-white focus:border-cyan-300 focus:outline-none" required>
                            </div>
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-slate-200">Metode pembayaran</label>
                            <select name="payment_method" class="mt-1 w-full rounded-xl border border-white/10 bg-slate-950/40 px-3 py-2 text-sm text-white focus:border-cyan-300 focus:outline-none" required>
                                <option value="">Pilih metode</option>
                                <option value="transfer" @selected(old('payment_method') === 'transfer')>Transfer Bank</option>
                                <option value="e-wallet" @selected(old('payment_method') === 'e-wallet')>E-Wallet</option>
                                <option value="cash" @selected(old('payment_method') === 'cash')>Tunai</option>
                            </select>
                            @error('payment_method')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-slate-200">Referensi pembayaran (opsional)</label>
                            <input type="text" name="payment_reference" value="{{ old('payment_reference') }}" class="mt-1 w-full rounded-xl border border-white/10 bg-slate-950/40 px-3 py-2 text-sm text-white focus:border-cyan-300 focus:outline-none" placeholder="No. transfer / bukti pembayaran">
                            @error('payment_reference')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="text-sm font-semibold text-slate-200">Upload bukti pembayaran (opsional)</label>
                            <input type="file" name="payment_proof" class="mt-1 w-full rounded-xl border border-white/10 bg-slate-950/40 px-3 py-2 text-sm text-slate-200 focus:border-cyan-300 focus:outline-none">
                            @error('payment_proof')<p class="mt-1 text-xs text-red-300">{{ $message }}</p>@enderror
                        </div>
                        <div class="flex items-start gap-2">
                            <input type="checkbox" name="agreement" value="1" class="mt-1 rounded border-white/20 bg-slate-950/40" @checked(old('agreement')) required>
                            <label class="text-xs text-slate-300">Saya menyetujui syarat dan ketentuan serta kebijakan pembayaran.</label>
                        </div>
                        @error('agreement')<p class="text-xs text-red-300">{{ $message }}</p>@enderror
                        <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-blue-400 via-indigo-400 to-cyan-300 px-4 py-3 text-sm font-semibold text-slate-950 shadow-lg shadow-cyan-400/30">Kirim Pendaftaran</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-slate-950 py-16">
        <div class="mx-auto max-w-6xl px-6">
            <div class="rounded-3xl border border-white/10 bg-gradient-to-r from-blue-500/20 via-indigo-500/20 to-cyan-400/20 p-8 text-center">
                <h2 class="text-3xl font-semibold text-white">Siap tampil profesional dengan POS yang rapi?</h2>
                <p class="mt-3 text-slate-200">Daftarkan bisnis Anda sekarang dan dapatkan setup cepat dari tim kami.</p>
                <a href="#daftar" class="mt-6 inline-flex rounded-full bg-white px-6 py-3 text-sm font-semibold text-slate-950 shadow-lg shadow-white/20">Mulai Berlangganan</a>
            </div>
        </div>
    </section>

    <footer class="border-t border-white/10 bg-slate-950">
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-3 px-6 py-6 text-sm text-slate-400 md:flex-row">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'POS Dealer') }}. Semua hak dilindungi.</p>
            <div class="flex gap-4">
                <a href="#daftar" class="hover:text-white">Daftar</a>
                <a href="{{ route('login') }}" class="hover:text-white">Login Admin</a>
            </div>
        </div>
    </footer>
</body>
</html>
