<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Progress Service svc/{{ $service->created_at->format('Y') }}/{{ $service->id }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gray-900">
    <main class="min-h-screen bg-slate-50 px-4 py-10">
        <div class="mx-auto max-w-2xl space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <p class="text-sm font-semibold text-blue-600">Progress Service</p>
                        <h1 class="text-2xl font-semibold text-gray-900">svc/{{ $service->created_at->format('Y') }}/{{ $service->id }}</h1>
                        <p class="text-sm text-gray-500">{{ $service->created_at->format('d M Y H:i') }}</p>
                    </div>
                    <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700 capitalize">
                        {{ $service->status }}
                    </span>
                </div>

                <div class="mt-6 grid gap-4 text-sm text-gray-700 sm:grid-cols-2">
                    <div class="space-y-1">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Customer</p>
                        <p class="font-semibold text-gray-900">{{ $service->customer->name }}</p>
                        @if ($service->customer->phone)
                            <x-wa-link :phone="$service->customer->phone" class="text-gray-500" />
                        @endif
                        @if ($service->customer->address)
                            <p class="text-gray-500">{{ $service->customer->address }}</p>
                        @endif
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Perangkat</p>
                        <p class="font-semibold text-gray-900">{{ $service->device }}</p>
                        <p class="text-gray-500">SN: {{ $service->serial_number ?? '-' }}</p>
                        <p class="text-gray-500">Kelengkapan: {{ $service->accessories ?? '-' }}</p>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 rounded-xl border border-slate-100 bg-slate-50 p-4 text-sm text-gray-700 sm:grid-cols-2">
                    <div class="space-y-1">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Keluhan</p>
                        <p class="font-medium text-gray-900">{{ $service->complaint }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Diagnosa</p>
                        <p class="font-medium text-gray-900">{{ $service->diagnosis ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Catatan Pengerjaan</p>
                        <p class="font-medium text-gray-900">{{ $service->notes ?? '-' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Biaya & Garansi</p>
                        <p class="font-medium text-gray-900">Biaya Jasa: Rp {{ number_format($service->service_fee, 0, ',', '.') }}</p>
                        <p class="text-gray-500">Garansi: {{ $service->warranty_days }} hari</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h2 class="text-base font-semibold text-gray-900">Riwayat Progress</h2>
                <div class="mt-4 space-y-4">
                    @forelse ($service->logs as $log)
                        <div class="flex gap-3">
                            <div class="mt-1 h-2.5 w-2.5 rounded-full bg-blue-600"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $log->message }}</p>
                                <div class="text-xs text-gray-500">
                                    <span>{{ $log->created_at->format('d M Y H:i') }}</span>
                                    @if ($log->user)
                                        <span>• {{ $log->user->name }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Belum ada catatan progress.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-2xl border border-blue-100 bg-blue-50 p-5 text-sm text-blue-900">
                <p class="font-semibold">Butuh bantuan?</p>
                <p class="text-blue-700">
                    Hubungi kami di
                    <x-wa-link :phone="$store['phone'] ?? null" class="text-blue-700 underline" fallback="kontak toko" />
                    untuk informasi lebih lanjut.
                </p>
            </div>
        </div>
    </main>
</body>
</html>
