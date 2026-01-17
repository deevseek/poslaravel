<x-app-layout title="Struk Pembayaran">
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

    <div class="mx-auto max-w-3xl space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Struk Pembayaran</h1>
                <p class="text-gray-600">Invoice {{ $transaction->invoice_number }}</p>
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
                @if ($transaction->customer)
                    <a href="{{ route('pos.receiver.print', $transaction) }}"
                        class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700 shadow-sm hover:bg-blue-100">
                        Cetak Penerima
                    </a>
                @endif
            </div>
        </div>

        @php
            $logoUrl = $store['logo']
                ? (\Illuminate\Support\Str::startsWith($store['logo'], ['http://', 'https://']) ? $store['logo'] : asset($store['logo']))
                : null;
        @endphp
        <div class="receipt-layout rounded-lg border border-gray-200 bg-white p-6 shadow-sm" data-receipt-layout data-format="standard">
            <div class="receipt-standard space-y-6">
            <div class="flex flex-wrap items-start justify-between gap-4 border-b border-gray-100 pb-4 text-sm text-gray-700">
                <div class="flex items-start gap-3">
                    @if ($logoUrl)
                        <img src="{{ $logoUrl }}" alt="{{ $store['name'] }}" class="h-14 w-14 rounded" />
                    @endif
                    <div class="space-y-1">
                        <p class="text-base font-semibold text-gray-900">{{ $store['name'] }}</p>
                        @if ($store['address'])
                            <p class="text-gray-600 leading-snug">{{ $store['address'] }}</p>
                        @endif
                        <div class="flex flex-col text-gray-600">
                            @if ($store['phone'])
                                <span>Telp: <x-wa-link :phone="$store['phone']" class="text-gray-600" /></span>
                            @endif
                            @if ($store['hours'])
                                <span>Jam Operasional: {{ $store['hours'] }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="text-right space-y-1">
                    <p class="text-sm font-semibold text-gray-900">Invoice</p>
                    <p class="text-gray-700">{{ $transaction->invoice_number }}</p>
                    <p class="text-gray-600">{{ $transaction->created_at->format('d M Y H:i') }}</p>
                    <p class="text-gray-600 uppercase">{{ str_replace('-', ' ', $transaction->payment_method) }}</p>
                    @if ($receiver)
                        <p class="text-gray-600">Penerima: {{ $receiver->name }}</p>
                    @endif
                </div>
            </div>

            @if ($transaction->customer)
                <div class="border-b border-gray-100 py-4 text-sm text-gray-700">
                    <p class="font-semibold text-gray-900">Customer</p>
                    <p>{{ $transaction->customer->name }}</p>
                </div>
            @endif

            <div class="py-4">
                <table class="min-w-full text-sm text-gray-700">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="py-2 text-left font-semibold">Produk</th>
                            <th class="py-2 text-center font-semibold">Qty</th>
                            <th class="py-2 text-right font-semibold">Harga</th>
                            <th class="py-2 text-right font-semibold">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($transaction->items as $item)
                            <tr>
                                <td class="py-2">{{ $item->product?->name ?? 'Produk' }}</td>
                                <td class="py-2 text-center">{{ $item->quantity }}</td>
                                <td class="py-2 text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="py-2 text-right font-semibold">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="space-y-2 border-t border-gray-200 pt-4 text-sm text-gray-700">
                <div class="flex items-center justify-between">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Diskon</span>
                    <span>Rp {{ number_format($transaction->discount, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between text-base font-semibold text-gray-900">
                    <span>Total</span>
                    <span>Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Jumlah Bayar</span>
                    <span>Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Kembalian</span>
                    <span>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="mt-6 rounded-lg bg-gray-50 p-4 text-sm text-gray-700">
                <p class="font-semibold text-gray-900">Terima kasih telah berbelanja!</p>
                <p class="text-gray-600">Simpan struk ini sebagai bukti pembayaran. Jika ada pertanyaan, hubungi kami di
                    <x-wa-link :phone="$store['phone'] ?? null" class="text-gray-600 underline" fallback="kontak toko" />.</p>
            </div>
        </div>

            <div class="receipt-thermal text-[11px] font-mono text-gray-900">
                <div class="space-y-1 text-center">
                    @if ($logoUrl)
                        <img src="{{ $logoUrl }}" alt="{{ $store['name'] }}" class="mx-auto h-10 w-10 rounded object-cover" />
                    @endif
                    <p class="text-sm font-semibold uppercase">{{ $store['name'] }}</p>
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
                        <span>Invoice</span>
                        <span>{{ $transaction->invoice_number }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Tanggal</span>
                        <span>{{ $transaction->created_at->format('d M Y H:i') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Bayar</span>
                        <span class="uppercase">{{ str_replace('-', ' ', $transaction->payment_method) }}</span>
                    </div>
                    @if ($receiver)
                        <div class="flex items-center justify-between">
                            <span>Penerima</span>
                            <span>{{ $receiver->name }}</span>
                        </div>
                    @endif
                    @if ($transaction->customer)
                        <div class="flex items-center justify-between">
                            <span>Customer</span>
                            <span>{{ $transaction->customer->name }}</span>
                        </div>
                    @endif
                </div>

                <div class="my-3 border-t border-dashed border-gray-400"></div>

                <div class="space-y-2">
                    @foreach ($transaction->items as $item)
                        <div>
                            <div class="flex items-center justify-between">
                                <span class="max-w-[60%] truncate">{{ $item->product?->name ?? 'Produk' }}</span>
                                <span>Rp {{ number_format($item->total, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex items-center justify-between text-gray-600">
                                <span>{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="my-3 border-t border-dashed border-gray-400"></div>

                <div class="space-y-1">
                    <div class="flex items-center justify-between">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Diskon</span>
                        <span>Rp {{ number_format($transaction->discount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between font-semibold">
                        <span>Total</span>
                        <span>Rp {{ number_format($transaction->total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Bayar</span>
                        <span>Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Kembali</span>
                        <span>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="my-3 border-t border-dashed border-gray-400"></div>

                <div class="space-y-1 text-center text-gray-700">
                    <p>Terima kasih telah berbelanja!</p>
                    <p>Simpan struk ini sebagai bukti pembayaran.</p>
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
