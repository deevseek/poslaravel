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
                width: 148mm;
            }

            .receipt-sheet {
                min-height: 210mm;
                padding: 12mm 12mm;
            }
        }

        .receipt-sheet {
            font-size: 12px;
            line-height: 1.5;
            margin: 0 auto;
            background: linear-gradient(180deg, #f8fafc 0%, #ffffff 35%);
        }

        .receipt-label {
            width: 130px;
        }

        .receipt-dotline {
            border-bottom: 1px dotted #9ca3af;
            height: 20px;
        }

        .receipt-watermark {
            opacity: 0.08;
        }

        .receipt-chip {
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }

        .receipt-card {
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            background: #ffffff;
            padding: 16px;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
        }

        .receipt-section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.28em;
            color: #64748b;
        }

        .receipt-highlight {
            background: #eff6ff;
            border-radius: 14px;
            border: 1px solid #bfdbfe;
        }
    </style>

    <div class="mx-auto max-w-3xl space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Tanda Terima Service</h1>
                <p class="text-gray-600">{{ $store['name'] }} • No. Service svc/{{ $service->created_at->format('Y') }}/{{ $service->id }}</p>
            </div>
            <div class="receipt-actions flex flex-wrap items-center gap-2">
                <button type="button" data-receipt-format="standard"
                    class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                    Preview Printer Biasa (A5)
                </button>
                <button type="button" data-receipt-format="thermal"
                    class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">
                    Preview Thermal 80mm
                </button>
                <button type="button" data-receipt-format="standard" data-receipt-print="true"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">
                    Cetak Printer Biasa (A5)
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
                <div class="receipt-sheet relative overflow-hidden rounded-2xl border border-gray-200 bg-white text-gray-700 shadow-sm">
                    <div class="pointer-events-none absolute inset-0 flex items-center justify-center">
                        <p class="receipt-watermark text-5xl font-semibold uppercase tracking-[0.35em] text-gray-400">{{ $store['name'] }}</p>
                    </div>

                    <div class="relative space-y-6">
                        <div class="receipt-card space-y-4">
                            <div class="flex flex-wrap items-start justify-between gap-6">
                                <div class="flex items-start gap-4">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-700">
                                        @if ($logoUrl)
                                            <img src="{{ $logoUrl }}" alt="{{ $store['name'] }}" class="h-12 w-12 rounded-2xl object-cover" />
                                        @else
                                            <span class="text-xl font-semibold">S</span>
                                        @endif
                                    </div>
                                    <div class="space-y-1">
                                        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-gray-900">{{ $store['name'] }}</p>
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
                                <div class="space-y-2 text-right">
                                    <div class="receipt-chip inline-flex bg-blue-600 text-white">Tanda Terima Servis</div>
                                    <p class="text-xs text-gray-500">Dokumen resmi penerimaan barang servis</p>
                                    <p class="text-xs font-semibold text-gray-700">No. svc/{{ $service->created_at->format('Y') }}/{{ $service->id }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-3 rounded-xl border border-blue-100 bg-blue-50/70 px-4 py-3 text-xs text-gray-700 md:grid-cols-3">
                                <div class="space-y-1">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-blue-500">Tanggal Terima</p>
                                    <p class="font-semibold text-gray-900">{{ $service->created_at->format('d M Y') }}</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-blue-500">Jam</p>
                                    <p>{{ $service->created_at->format('H:i') }}</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-blue-500">Admin</p>
                                    <p>{{ optional(auth()->user())->name ?? 'Admin' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="receipt-card space-y-4">
                                <p class="receipt-section-title">Informasi Pelanggan</p>
                                <div class="space-y-2 text-xs">
                                    <div class="flex">
                                        <span class="receipt-label text-gray-500">Nama Pelanggan</span>
                                        <span class="font-semibold text-gray-900 capitalize">{{ $service->customer->name }}</span>
                                    </div>
                                    @if ($service->customer->phone)
                                        <div class="flex">
                                            <span class="receipt-label text-gray-500">Telepon/WA</span>
                                            <span><x-wa-link :phone="$service->customer->phone" class="text-gray-700" /></span>
                                        </div>
                                    @endif
                                    <div class="flex">
                                        <span class="receipt-label text-gray-500">Status Servis</span>
                                        <span class="capitalize">{{ $service->status }}</span>
                                    </div>
                                </div>
                                <div class="receipt-highlight px-4 py-3 text-xs">
                                    <div class="flex items-center justify-between">
                                        <span class="text-gray-600">Estimasi Biaya</span>
                                        <span class="font-semibold text-gray-900">Rp {{ number_format($service->service_fee, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="mt-2 flex items-center justify-between">
                                        <span class="text-gray-600">DP/Uang Muka</span>
                                        <span>Rp {{ number_format($service->deposit ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="mt-2 flex items-center justify-between">
                                        <span class="text-gray-600">Garansi</span>
                                        <span>{{ $service->warranty_days }} hari</span>
                                    </div>
                                </div>
                            </div>

                            <div class="receipt-card space-y-4">
                                <p class="receipt-section-title">Data Barang Servis</p>
                                <div class="space-y-2 text-xs">
                                    <div class="flex">
                                        <span class="receipt-label text-gray-500">Nama Barang</span>
                                        <span class="font-semibold text-gray-900">{{ $service->device }}</span>
                                    </div>
                                    <div class="flex">
                                        <span class="receipt-label text-gray-500">Model/Seri</span>
                                        <span>{{ $service->model ?? '-' }}</span>
                                    </div>
                                    <div class="flex">
                                        <span class="receipt-label text-gray-500">Nomor Serial</span>
                                        <span>{{ $service->serial_number ?? '-' }}</span>
                                    </div>
                                    <div class="flex">
                                        <span class="receipt-label text-gray-500">Kelengkapan</span>
                                        <span>{{ $service->accessories ?? '-' }}</span>
                                    </div>
                                    <div class="flex">
                                        <span class="receipt-label text-gray-500">Kerusakan</span>
                                        <span>{{ $service->complaint }}</span>
                                    </div>
                                </div>
                                <div class="flex items-start gap-3 text-xs">
                                    <span class="receipt-label text-gray-500">Keterangan Lain-Lain</span>
                                    <div class="flex-1 space-y-2">
                                        <div class="receipt-dotline"></div>
                                        <div class="receipt-dotline"></div>
                                        <div class="receipt-dotline"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="receipt-card grid grid-cols-1 gap-5 text-xs text-gray-600 md:grid-cols-2">
                            <div class="space-y-3 text-center">
                                <img src="{{ $progressQrUrl }}" alt="QR progres service" class="mx-auto h-24 w-24 rounded-2xl bg-white p-2 shadow-sm" />
                                <p class="font-semibold uppercase tracking-[0.2em] text-gray-800">Scan Update Status &amp; Pengambilan</p>
                            </div>
                            <div class="space-y-3 text-center">
                                <img src="{{ $trackingQrUrl ?? $progressQrUrl }}" alt="QR tracking service" class="mx-auto h-24 w-24 rounded-2xl bg-white p-2 shadow-sm" />
                                <p class="font-semibold uppercase tracking-[0.2em] text-gray-800">Scan Cek Status (Tracking)</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-4 text-[11px] text-gray-600 md:grid-cols-[1.4fr_0.6fr]">
                            <div class="receipt-card space-y-2">
                                <p>*Tanda Terima Servis ini silahkan dibawa saat pengambilan barang servis.</p>
                                <p>*Download Aplikasi scanner Android untuk tracking di https://dataservisonline.com/scanner.apk</p>
                                <p class="pt-2 font-semibold uppercase">Syarat &amp; Ketentuan:</p>
                                <ol class="list-decimal space-y-1 pl-4">
                                    <li>Jika barang tidak diambil lebih dari 3 bulan setelah konfirmasi pengambilan barang sudah bukan tanggung jawab kami.</li>
                                    <li>Garansi tidak berlaku jika nota servis hilang.</li>
                                </ol>
                            </div>
                            <div class="receipt-card space-y-6 text-right text-[11px]">
                                <div>
                                    <p>Penerima</p>
                                    <div class="mt-6 border-b border-gray-300"></div>
                                </div>
                                <div>
                                    <p>Pemilik</p>
                                    <div class="mt-6 border-b border-gray-300"></div>
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
                    <p class="text-sm font-semibold uppercase tracking-[0.25em]">{{ $store['name'] }}</p>
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
