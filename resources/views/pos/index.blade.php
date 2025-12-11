<x-app-layout title="Point of Sale">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Point of Sale</h1>
            <p class="text-gray-600">Tambah produk ke keranjang dan selesaikan pembayaran.</p>
        </div>
        <a href="{{ route('products.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Kelola Produk</a>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-4">
            <form method="GET" action="{{ route('pos.index') }}" class="flex items-center gap-2">
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk atau SKU..." class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                    <button type="submit" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">🔍</button>
                </div>
            </form>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @forelse ($products as $product)
                    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $product->name }}</h3>
                                <p class="text-sm text-gray-500">SKU: {{ $product->sku }}</p>
                            </div>
                            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">Stok: {{ $product->stock }}</span>
                        </div>
                        <p class="mt-3 text-xl font-semibold text-blue-600">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        <form method="POST" action="{{ route('pos.cart.add') }}" class="mt-4 space-y-3">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}" />
                            <label class="flex items-center justify-between text-sm text-gray-700" for="quantity-{{ $product->id }}">
                                <span>Jumlah</span>
                                <input type="number" id="quantity-{{ $product->id }}" name="quantity" value="1" min="1" class="w-24 rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                            </label>
                            <button type="submit" class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Tambah ke Keranjang</button>
                        </form>
                    </div>
                @empty
                    <p class="col-span-3 text-sm text-gray-500">Produk tidak ditemukan.</p>
                @endforelse
            </div>

            <div>
                {{ $products->links() }}
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Keranjang</h2>
                        <p class="text-sm text-gray-500">Atur jumlah dan checkout.</p>
                    </div>
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">{{ count($cartItems) }} Item</span>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse ($cartItems as $item)
                        <div class="px-4 py-3">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $item['product']->name }}</p>
                                    <p class="text-xs text-gray-500">Rp {{ number_format($item['product']->price, 0, ',', '.') }} x {{ $item['quantity'] }}</p>
                                    <p class="text-sm font-semibold text-blue-600">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <form method="POST" action="{{ route('pos.cart.update', $item['product']) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="w-16 rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                                        <button type="submit" class="rounded-lg bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-700 hover:bg-gray-200">Update</button>
                                    </form>
                                    <form method="POST" action="{{ route('pos.cart.remove', $item['product']) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg bg-red-50 px-2 py-1 text-xs font-semibold text-red-700 hover:bg-red-100">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="px-4 py-6 text-center text-sm text-gray-500">Keranjang masih kosong.</p>
                    @endforelse
                </div>

                <div class="border-t border-gray-200 px-4 py-3 space-y-2 text-sm">
                    <div class="flex items-center justify-between font-semibold text-gray-900">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('pos.checkout') }}" class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm space-y-4">
                @csrf
                <div>
                    <label for="discount" class="text-sm font-semibold text-gray-700">Diskon</label>
                    <input type="number" step="0.01" name="discount" id="discount" value="{{ old('discount', 0) }}" min="0" class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                </div>

                <div>
                    <label for="payment_method" class="text-sm font-semibold text-gray-700">Metode Pembayaran</label>
                    <select id="payment_method" name="payment_method" class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach ($paymentMethods as $value => $label)
                            <option value="{{ $value }}" @selected(old('payment_method') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="paid_amount" class="text-sm font-semibold text-gray-700">Jumlah Bayar</label>
                    <input type="number" step="0.01" name="paid_amount" id="paid_amount" value="{{ old('paid_amount', $subtotal) }}" min="0" class="mt-1 w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500" />
                </div>

                <button type="submit" class="w-full rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-green-700">Selesaikan Pembayaran</button>
            </form>
        </div>
    </div>
</x-app-layout>
