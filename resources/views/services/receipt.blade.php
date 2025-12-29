<x-app-layout :title="'Tanda Terima Service #' . $service->id">
    <style>
        .receipt-layout[data-format="standard"] .receipt-thermal {
            display: none;
        }

        .receipt-layout[data-format="thermal"] .receipt-standard {
            display: none;
        }

        @media print {
            .receipt-actions {
                display: none !important;
            }

            .receipt-layout {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
            }

            .receipt-layout[data-format="thermal"] {
                width: 80mm;
            }
        }
    </style>

    <div class="mx-auto max-w-4xl space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Tanda Terima Service</h1>
                <p class="text-gray-600">{{ $store['name'] }} • No. Service #{{ $service->id }}</p>
            </div>
            <div class="receipt-actions flex flex-wrap items-center gap-2">
                <button type="button" data-receipt-format="standard"
                    class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                    Preview Printer Biasa
                </button>
                <button type="button" data-receipt-format="thermal"
                    class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                    Preview Thermal 80mm
                </button>
                <button type="button" data-receipt-format="standard" data-receipt-print="true"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">
                    Cetak Printer Biasa
                </button>
                <button type="button" data-receipt-format="thermal" data-receipt-print="true"
                    class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-emerald-700">
                    Cetak Thermal 80mm
                </button>
            </div>
        </div>

        <div class="receipt-layout rounded-lg border border-gray-200 bg-white p-6 shadow-sm" data-receipt-layout data-format="standard">
            <div class="receipt-standard space-y-6">
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

                <div class="grid grid-cols-1 gap-4 md:grid-cols-[1.2fr_0.8fr]">
                    <div class="rounded-lg bg-gray-50 p-4 text-sm text-gray-700">
                        <p class="font-semibold text-gray-900">Terima kasih telah mempercayakan servis kepada kami.</p>
                        <p class="text-gray-600">Harap simpan tanda terima ini. Untuk informasi lebih lanjut hubungi
                            {{ $store['phone'] ?? 'kontak toko' }} atau kunjungi kami pada jam operasional.</p>
                    </div>
                    <div class="flex items-center gap-4 rounded-lg border border-dashed border-gray-200 p-4">
                        <img src="{{ $progressQrUrl }}" alt="QR progres service" class="h-24 w-24 rounded bg-white p-1" />
                        <div class="text-sm text-gray-700">
                            <p class="font-semibold text-gray-900">Cek Progress Service</p>
                            <p class="text-gray-600">Scan QR ini untuk melihat status terbaru servis.</p>
                            <p class="mt-2 break-all text-xs text-gray-500">{{ $progressUrl }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="receipt-thermal text-[11px] font-mono text-gray-900">
                <div class="space-y-1 text-center">
                    <p class="text-sm font-semibold uppercase">{{ $store['name'] }}</p>
                    @if ($store['address'])
                        <p>{{ $store['address'] }}</p>
                    @endif
                    @if ($store['phone'])
                        <p>Telp: {{ $store['phone'] }}</p>
                    @endif
                </div>

                <div class="my-3 border-t border-dashed border-gray-400"></div>

                <div class="space-y-1">
                    <div class="flex items-center justify-between">
                        <span>No. Service</span>
                        <span>#{{ $service->id }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Tanggal</span>
                        <span>{{ $service->created_at->format('d M Y H:i') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Status</span>
                        <span class="capitalize">{{ $service->status }}</span>
                    </div>
                </div>

                <div class="my-3 border-t border-dashed border-gray-400"></div>

                <div class="space-y-1">
                    <div class="flex items-center justify-between">
                        <span>Customer</span>
                        <span class="max-w-[60%] truncate">{{ $service->customer->name }}</span>
                    </div>
                    @if ($service->customer->phone)
                        <div class="flex items-center justify-between">
                            <span>Telepon</span>
                            <span>{{ $service->customer->phone }}</span>
                        </div>
                    @endif
                    <div class="flex items-center justify-between">
                        <span>Perangkat</span>
                        <span class="max-w-[60%] truncate">{{ $service->device }}</span>
                    </div>
                    <div class="flex items-center justify-between text-gray-600">
                        <span>SN</span>
                        <span class="max-w-[60%] truncate">{{ $service->serial_number ?? '-' }}</span>
                    </div>
                    <div class="flex items-center justify-between text-gray-600">
                        <span>Kelengkapan</span>
                        <span class="max-w-[60%] truncate">{{ $service->accessories ?? '-' }}</span>
                    </div>
                </div>

                <div class="my-3 border-t border-dashed border-gray-400"></div>

                <div class="space-y-2">
                    <div>
                        <p class="font-semibold">Keluhan</p>
                        <p class="text-gray-600">{{ $service->complaint }}</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Estimasi Jasa</span>
                        <span>Rp {{ number_format($service->service_fee, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Garansi</span>
                        <span>{{ $service->warranty_days }} hari</span>
                    </div>
                </div>

                @if ($service->notes)
                    <div class="my-3 border-t border-dashed border-gray-400"></div>
                    <div class="space-y-1">
                        <p class="font-semibold">Catatan</p>
                        <p class="text-gray-600">{{ $service->notes }}</p>
                    </div>
                @endif

                <div class="my-3 border-t border-dashed border-gray-400"></div>

                <div class="space-y-2 text-center text-gray-700">
                    <img src="{{ $progressQrUrl }}" alt="QR progres service" class="mx-auto h-24 w-24 rounded bg-white p-1" />
                    <p class="font-semibold text-gray-900">Scan untuk cek progress</p>
                    <p class="text-[10px] text-gray-500 break-all">{{ $progressUrl }}</p>
                </div>

                <div class="my-3 border-t border-dashed border-gray-400"></div>

                <div class="space-y-1 text-center text-gray-700">
                    <p>Terima kasih telah mempercayakan servis.</p>
                    <p>Simpan tanda terima ini sebagai bukti.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const layout = document.querySelector('[data-receipt-layout]');
            const buttons = document.querySelectorAll('[data-receipt-format]');
            const previewButtons = document.querySelectorAll('[data-receipt-format]:not([data-receipt-print])');

            const setFormat = (format) => {
                layout.dataset.format = format;
                previewButtons.forEach((button) => {
                    const isActive = button.dataset.receiptFormat === format;
                    button.classList.toggle('bg-blue-600', isActive);
                    button.classList.toggle('text-white', isActive);
                    button.classList.toggle('border-blue-600', isActive);
                    button.classList.toggle('bg-white', !isActive);
                    button.classList.toggle('text-gray-700', !isActive);
                });
            };

            buttons.forEach((button) => {
                button.addEventListener('click', () => {
                    const format = button.dataset.receiptFormat;
                    setFormat(format);

                    if (button.dataset.receiptPrint) {
                        window.print();
                    }
                });
            });

            setFormat(layout.dataset.format || 'standard');
        });
    </script>
</x-app-layout>
