<x-app-layout title="Struk Pembayaran">
    <div class="mx-auto max-w-3xl space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Struk Pembayaran</h1>
                <p class="text-gray-600">Invoice {{ $transaction->invoice_number }}</p>
            </div>
            <button onclick="window.print()" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Cetak</button>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
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
                    <p class="text-sm font-semibold text-gray-900">Invoice</p>
                    <p class="text-gray-700">{{ $transaction->invoice_number }}</p>
                    <p class="text-gray-600">{{ $transaction->created_at->format('d M Y H:i') }}</p>
                    <p class="text-gray-600 uppercase">{{ str_replace('-', ' ', $transaction->payment_method) }}</p>
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
                    {{ $store['phone'] ?? 'kontak toko' }}.</p>
            </div>
        </div>
    </div>
</x-app-layout>
