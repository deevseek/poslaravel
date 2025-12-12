@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Tambah Pembelian</h1>
            <p class="text-gray-600">Catat pembelian, tambah stok otomatis, dan pantau status pembayaran.</p>
        </div>

        <a href="{{ route('purchases.index') }}"
            class="inline-flex items-center rounded-lg bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-800 shadow hover:bg-gray-200">
            Kembali ke Riwayat
        </a>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
            Terdapat kesalahan input. Silakan periksa kembali formulir di bawah.
        </div>
    @endif

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-900">Form Input Pembelian</h2>
        <p class="text-sm text-gray-600 mb-4">Masukkan detail pembelian dari supplier.</p>

        <form action="{{ route('purchases.store') }}" method="POST" class="space-y-4" id="purchase-form">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700" for="supplier_id">Supplier</label>
                <select id="supplier_id" name="supplier_id" required
                    class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Pilih supplier</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>
                            {{ $supplier->name }}</option>
                    @endforeach
                </select>
                @error('supplier_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-3 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="purchase_date">Tanggal</label>
                    <input type="date" id="purchase_date" name="purchase_date" value="{{ old('purchase_date', now()->toDateString()) }}"
                        required class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('purchase_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="payment_status">Status Pembayaran</label>
                    <select id="payment_status" name="payment_status" required
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih status</option>
                        <option value="{{ \App\Models\Purchase::STATUS_PAID }}" @selected(old('payment_status') === \App\Models\Purchase::STATUS_PAID)>
                            Lunas</option>
                        <option value="{{ \App\Models\Purchase::STATUS_DEBT }}" @selected(old('payment_status') === \App\Models\Purchase::STATUS_DEBT)>
                            Hutang</option>
                    </select>
                    @error('payment_status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Item Pembelian</label>
                <p class="text-sm text-gray-500 mb-2">Tambahkan produk, jumlah, dan harga beli.</p>
                @php
                    $oldItems = old('items', [['product_id' => '', 'quantity' => 1, 'price' => '']]);
                @endphp
                <div class="space-y-3" id="purchase-items">
                    @foreach ($oldItems as $index => $item)
                        <div class="rounded-lg border border-gray-200 p-3" data-item-row>
                            <div class="grid gap-3 md:grid-cols-3 md:items-end">
                                <div>
                                    <label class="block text-sm text-gray-700" for="items_{{ $index }}_product_id">Produk</label>
                                    <select id="items_{{ $index }}_product_id" name="items[{{ $index }}][product_id]" required
                                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Pilih produk</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" @selected($item['product_id'] == $product->id)>{{ $product->name }} (Stok: {{ $product->stock }})</option>
                                        @endforeach
                                    </select>
                                    @error('items.' . $index . '.product_id')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-700" for="items_{{ $index }}_quantity">Jumlah</label>
                                    <input type="number" id="items_{{ $index }}_quantity" name="items[{{ $index }}][quantity]" min="1" value="{{ $item['quantity'] }}"
                                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                    @error('items.' . $index . '.quantity')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-700" for="items_{{ $index }}_price">Harga Beli</label>
                                    <input type="number" step="0.01" id="items_{{ $index }}_price" name="items[{{ $index }}][price]" value="{{ $item['price'] }}" required
                                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('items.' . $index . '.price')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="flex justify-end mt-2">
                                <button type="button" class="text-sm text-red-600 hover:underline" data-remove-item>Hapus</button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="flex justify-end mt-3">
                    <button type="button" id="add-item" class="text-sm text-blue-600 font-semibold hover:underline">+ Tambah Item</button>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700" for="notes">Catatan (opsional)</label>
                <textarea id="notes" name="notes" rows="3" class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end">
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Simpan Pembelian</button>
            </div>
        </form>
    </div>

    <template id="purchase-item-template">
        <div class="rounded-lg border border-gray-200 p-3" data-item-row>
            <div class="grid gap-3 md:grid-cols-3 md:items-end">
                <div>
                    <label class="block text-sm text-gray-700">Produk</label>
                    <select name="items[__INDEX__][product_id]" required
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih produk</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} (Stok: {{ $product->stock }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Jumlah</label>
                    <input type="number" name="items[__INDEX__][quantity]" min="1" value="1" required
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm text-gray-700">Harga Beli</label>
                    <input type="number" step="0.01" name="items[__INDEX__][price]" value="0" required
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>
            <div class="flex justify-end mt-2">
                <button type="button" class="text-sm text-red-600 hover:underline" data-remove-item>Hapus</button>
            </div>
        </div>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const itemsContainer = document.querySelector('#purchase-items');
            const addButton = document.querySelector('#add-item');
            const template = document.querySelector('#purchase-item-template').innerHTML;

            let itemIndex = itemsContainer.querySelectorAll('[data-item-row]').length;

            addButton?.addEventListener('click', () => {
                const newRow = document.createElement('div');
                newRow.innerHTML = template.replace(/__INDEX__/g, itemIndex);
                itemIndex++;
                itemsContainer.appendChild(newRow.firstElementChild);
            });

            itemsContainer?.addEventListener('click', (event) => {
                if (event.target.matches('[data-remove-item]')) {
                    const row = event.target.closest('[data-item-row]');
                    if (row && itemsContainer.children.length > 1) {
                        row.remove();
                    }
                }
            });
        });
    </script>
@endsection
