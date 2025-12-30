<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-950">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased h-full text-slate-900">
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-indigo-100 px-6 py-12">
        <div class="mx-auto flex w-full max-w-5xl flex-col gap-8 lg:flex-row lg:items-center lg:justify-between">
            <div class="space-y-4">
                <div class="inline-flex items-center gap-3 rounded-2xl bg-white/80 px-4 py-3 shadow-sm ring-1 ring-slate-200/70 backdrop-blur">
                    <x-application-logo class="h-10 w-10" />
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-400">POS Dealer</p>
                        <p class="text-lg font-semibold text-slate-900">Komputer & Service</p>
                    </div>
                </div>
                <h1 class="text-3xl font-semibold text-slate-900">Kelola bisnis Anda dengan dashboard modern.</h1>
                <p class="max-w-xl text-sm text-slate-600">
                    Pantau penjualan, inventaris, layanan, dan pelanggan dalam satu tampilan admin yang profesional.
                </p>
            </div>
            <div class="w-full max-w-md rounded-3xl bg-white/90 p-8 shadow-xl ring-1 ring-slate-200/70 backdrop-blur space-y-6">
                <div class="text-center">
                    <h2 class="text-xl font-semibold text-slate-900">Masuk ke akun Anda</h2>
                    <p class="text-sm text-slate-500">Masukkan kredensial untuk melanjutkan.</p>
                </div>
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
