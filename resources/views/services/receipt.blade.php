<x-app-layout :title="'Tanda Terima Service svc/' . $service->created_at->format('Y') . '/' . $service->id">
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

            .receipt-layout[data-format="standard"] {
                width: 210mm;
            }

            .receipt-sheet {
                min-height: 148mm;
                padding: 14mm 14mm;
            }
        }

        .receipt-sheet {
            font-size: 12px;
            line-height: 1.5;
            margin: 0 auto;
        }

        .receipt-label {
            width: 130px;
        }

        .receipt-dotline {
            border-bottom: 1px dotted #9ca3af;
            height: 20px;
        }

        .receipt-watermark {
            opacity: 0.12;
        }
    </style>

    <div class="mx-auto max-w-5xl space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Tanda Terima Service</h1>
                <p class="text-gray-600">{{ $store['name'] }} • No. Service svc/{{ $service->created_at->format('Y') }}/{{ $service->id }}</p>
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

        @php
            $logoUrl = $store['logo']
                ? (\Illuminate\Support\Str::startsWith($store['logo'], ['http://', 'https://']) ? $store['logo'] : asset($store['logo']))
                : null;
        @endphp
        <div class="receipt-layout rounded-lg border border-gray-200 bg-white shadow-sm" data-receipt-layout data-format="standard">
            <div class="receipt-standard space-y-6">
                <div class="receipt-sheet relative overflow-hidden rounded-lg border border-gray-200 bg-white text-gray-700">
                    <div class="pointer-events-none absolute inset-0 flex items-center justify-center">
                        <p class="receipt-watermark text-6xl font-semibold uppercase tracking-[0.3em] text-gray-400">{{ $store['name'] }}</p>
                    </div>

                    <div class="relative space-y-4">
                        <div class="flex flex-wrap items-start justify-between gap-6">
                            <div class="flex items-start gap-3">
                                @if ($logoUrl)
                                    <img src="{{ $logoUrl }}" alt="{{ $store['name'] }}" class="h-10 w-10 rounded-lg object-cover" />
                                @endif
                                <div class="space-y-1">
                                    <p class="text-sm font-semibold uppercase tracking-[0.2em] text-gray-900">{{ $store['name'] }}</p>
                                    @if ($store['hours'])
                                        <p class="text-xs uppercase text-gray-500">{{ $store['hours'] }}</p>
                                    @endif
                                    @if ($store['address'])
                                        <p class="text-xs leading-snug text-gray-600">{{ $store['address'] }}</p>
                                    @endif
                                    <div class="text-xs text-gray-600">
                                        @if ($store['phone'])
                                            <span>Telp. <x-wa-link :phone="$store['phone']" class="text-gray-600" /></span>
                                            <span class="mx-1">|</span>
                                            <span>WA <x-wa-link :phone="$store['phone']" class="text-gray-600" /></span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-semibold uppercase tracking-[0.35em] text-gray-900">Tanda Terima Servis</p>
                                <p class="text-xs text-gray-500">Dokumen resmi penerimaan barang servis</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-xs text-gray-700 md:grid-cols-3">
                            <div class="space-y-1">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-gray-500">No. Servis</p>
                                <p class="font-semibold text-gray-900">svc/{{ $service->created_at->format('Y') }}/{{ $service->id }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-gray-500">Tanggal Terima</p>
                                <p>{{ $service->created_at->format('d M Y') }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-gray-500">Admin</p>
                                <p>{{ optional(auth()->user())->name ?? 'Admin' }}</p>
                            </div>
                        </div>

                        <div class="border-y border-gray-300 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-gray-600">
                            Informasi Pelanggan
                        </div>

                        <div class="grid grid-cols-1 gap-4 text-xs md:grid-cols-[1.2fr_0.8fr]">
                            <div class="space-y-2">
                                <div class="flex">
                                    <span class="receipt-label">Nama Pelanggan</span>
                                    <span class="font-semibold text-gray-900 capitalize">{{ $service->customer->name }}</span>
                                </div>
                                @if ($service->customer->phone)
                                    <div class="flex">
                                        <span class="receipt-label">Telepon/WA</span>
                                        <span><x-wa-link :phone="$service->customer->phone" class="text-gray-700" /></span>
                                    </div>
                                @endif
                                <div class="flex">
                                    <span class="receipt-label">Status Servis</span>
                                    <span class="capitalize">{{ $service->status }}</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex">
                                    <span class="receipt-label">Estimasi Biaya</span>
                                    <span class="font-semibold text-gray-900">Rp {{ number_format($service->service_fee, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex">
                                    <span class="receipt-label">DP/Uang Muka</span>
                                    <span>Rp {{ number_format($service->deposit ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex">
                                    <span class="receipt-label">Garansi</span>
                                    <span>{{ $service->warranty_days }} hari</span>
                                </div>
                            </div>
                        </div>

                        <div class="border-y border-gray-300 py-2 text-xs font-semibold uppercase tracking-[0.3em] text-gray-600">
                            Data Barang Servis
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-[1.4fr_0.6fr] text-xs">
                            <div class="space-y-2">
                                <div class="flex">
                                    <span class="receipt-label">Nama Barang</span>
                                    <span class="font-semibold text-gray-900">{{ $service->device }}</span>
                                </div>
                                <div class="flex">
                                    <span class="receipt-label">Model/Seri</span>
                                    <span>{{ $service->model ?? '-' }}</span>
                                </div>
                                <div class="flex">
                                    <span class="receipt-label">Nomor Serial</span>
                                    <span>{{ $service->serial_number ?? '-' }}</span>
                                </div>
                                <div class="flex">
                                    <span class="receipt-label">Kelengkapan</span>
                                    <span>{{ $service->accessories ?? '-' }}</span>
                                </div>
                                <div class="flex">
                                    <span class="receipt-label">Kerusakan</span>
                                    <span>{{ $service->complaint }}</span>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-center gap-2">
                                    <span class="receipt-label">Keterangan Lain-Lain</span>
                                    <div class="flex-1 space-y-2">
                                        <div class="receipt-dotline"></div>
                                        <div class="receipt-dotline"></div>
                                        <div class="receipt-dotline"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-b border-gray-300"></div>

                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <div class="space-y-3 text-center text-xs text-gray-600">
                                <img src="{{ $progressQrUrl }}" alt="QR progres service" class="mx-auto h-28 w-28 rounded bg-white p-1" />
                                <p class="font-semibold uppercase tracking-[0.2em] text-gray-800">Scan Untuk Update Status &amp; Pengambilan Barang Servis</p>
                            </div>
                            <div class="space-y-3 text-center text-xs text-gray-600">
                                <img src="{{ $trackingQrUrl ?? $progressQrUrl }}" alt="QR tracking service" class="mx-auto h-28 w-28 rounded bg-white p-1" />
                                <p class="font-semibold uppercase tracking-[0.2em] text-gray-800">Scan Untuk Cek Status (Tracking) Servis</p>
                            </div>
                        </div>

                        <div class="border-b border-gray-300"></div>

                        <div class="grid grid-cols-1 gap-4 text-[11px] text-gray-600 md:grid-cols-[1.4fr_0.6fr]">
                            <div class="space-y-1">
                                <p>*Tanda Terima Servis ini silahkan dibawa saat pengambilan barang servis.</p>
                                <p>*Download Aplikasi scanner Android untuk tracking di https://dataservisonline.com/scanner.apk</p>
                                <p class="pt-2 font-semibold uppercase">Syarat &amp; Ketentuan:</p>
                                <ol class="list-decimal space-y-1 pl-4">
                                    <li>Jika barang tidak diambil lebih dari 3 bulan setelah konfirmasi pengambilan barang sudah bukan tanggung jawab kami.</li>
                                    <li>Garansi tidak berlaku jika nota servis hilang.</li>
                                </ol>
                            </div>
                            <div class="space-y-6 text-right text-[11px]">
                                <div>
                                    <p>Penerima</p>
                                    <div class="mt-6 border-b border-gray-400"></div>
                                </div>
                                <div>
                                    <p>Pemilik</p>
                                    <div class="mt-6 border-b border-gray-400"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="receipt-thermal text-[11px] font-mono text-gray-900">
                <div class="space-y-1 text-center">
                    @if ($logoUrl)
                        <img src="{{ $logoUrl }}" alt="{{ $store['name'] }}" class="mx-auto h-8 w-8 rounded object-cover" />
                    @endif
                    <p class="text-sm font-semibold uppercase">{{ $store['name'] }}</p>
                    <p>No. Service: svc/{{ $service->created_at->format('Y') }}/{{ $service->id }}</p>
                    @if ($store['address'])
                        <p>{{ $store['address'] }}</p>
                    @endif
                    @if ($store['phone'])
                        <p>Telp: <x-wa-link :phone="$store['phone']" class="text-gray-700" /></p>
                    @endif
                </div>

                <div class="my-3 border-t border-dashed border-gray-400"></div>

                <div class="space-y-1">
                    <div class="flex items-center justify-between">
                        <span>No. Service</span>
                        <span>svc/{{ $service->created_at->format('Y') }}/{{ $service->id }}</span>
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
                            <span><x-wa-link :phone="$service->customer->phone" class="text-gray-900" /></span>
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
