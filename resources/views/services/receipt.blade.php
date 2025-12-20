<x-app-layout :title="'Tanda Terima Service #' . $service->id">
    <div class="mx-auto max-w-4xl space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Tanda Terima Service</h1>
                <p class="text-gray-600">{{ $store['name'] }} • No. Service #{{ $service->id }}</p>
            </div>
            <button onclick="window.print()" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Cetak</button>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm space-y-4">
            <div class="flex flex-wrap items-start justify-between gap-4 border-b border-gray-100 pb-4 text-sm text-gray-700">
                <div class="flex items-start gap-3">
                    @if ($store['logo'])
                        <img src="{{ asset($store['logo']) }}" alt="{{ $store['name'] }}" class="h-14 w-14 rounded" />
                    @endif
                    <div class="space-y-1">
                        <p class="text-base font-semibold text-gray-900">{{ $store['name'] }}</p>
                        @if ($store['address'])
                            <p class="text-gray-600 leading-snug">{{ $store['address'] }}</p>
                        @endif
                        <div class="flex flex-col text-gray-600">
                            @if ($store['phone'])
                                <span>Telp: {{ $store['phone'] }}</span>
                            @endif
                            @if ($store['hours'])
                                <span>Jam Operasional: {{ $store['hours'] }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="text-right space-y-1">
                    <p class="text-sm font-semibold text-gray-900">No. Service</p>
                    <p class="text-gray-700">#{{ $service->id }}</p>
                    <p class="text-gray-600">{{ $service->created_at->format('d M Y H:i') }}</p>
                    <p class="text-gray-600 capitalize">Status: {{ $service->status }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                <div class="space-y-2">
                    <p class="font-semibold text-gray-900">Data Customer</p>
                    <p>{{ $service->customer->name }}</p>
                    @if ($service->customer->phone)
                        <p class="text-gray-600">{{ $service->customer->phone }}</p>
                    @endif
                </div>
                <div class="space-y-2">
                    <p class="font-semibold text-gray-900">Perangkat</p>
                    <p>{{ $service->device }}</p>
                    <p class="text-gray-600">SN: {{ $service->serial_number ?? '-' }}</p>
                    <p class="text-gray-600">Kelengkapan: {{ $service->accessories ?? '-' }}</p>
                </div>
            </div>

            <div class="space-y-2 text-sm text-gray-700">
                <p class="font-semibold text-gray-900">Keluhan</p>
                <p class="text-gray-800">{{ $service->complaint }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-700">
                <div class="space-y-1">
                    <p class="font-semibold text-gray-900">Perkiraan Biaya Jasa</p>
                    <p>Rp {{ number_format($service->service_fee, 0, ',', '.') }}</p>
                </div>
                <div class="space-y-1">
                    <p class="font-semibold text-gray-900">Masa Garansi</p>
                    <p>{{ $service->warranty_days }} hari</p>
                </div>
            </div>

            @if ($service->notes)
                <div class="space-y-2 text-sm text-gray-700">
                    <p class="font-semibold text-gray-900">Catatan</p>
                    <p class="text-gray-800">{{ $service->notes }}</p>
                </div>
            @endif

            <div class="rounded-lg bg-gray-50 p-4 text-sm text-gray-700">
                <p class="font-semibold text-gray-900">Terima kasih telah mempercayakan servis kepada kami.</p>
                <p class="text-gray-600">Harap simpan tanda terima ini. Untuk informasi lebih lanjut hubungi
                    {{ $store['phone'] ?? 'kontak toko' }} atau kunjungi kami pada jam operasional.</p>
            </div>
        </div>
    </div>
</x-app-layout>
