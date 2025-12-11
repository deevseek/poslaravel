<x-app-layout title="Detail Produk">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Detail Produk</h1>
        <p class="text-gray-600">Informasi lengkap produk dan pergerakan stok terakhir.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <dl class="space-y-4">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nama</dt>
                        <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $product->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">SKU</dt>
                        <dd class="mt-1 text-gray-700">{{ $product->sku }}</dd>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Kategori</dt>
                        <dd class="mt-1 text-gray-700">{{ $product->category?->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Harga</dt>
                        <dd class="mt-1 text-gray-700">Rp {{ number_format($product->price, 0, ',', '.') }}</dd>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Stok</dt>
                        <dd class="mt-1 text-xl font-semibold text-gray-900">{{ $product->stock }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Dibuat</dt>
                        <dd class="mt-1 text-gray-700">{{ $product->created_at->format('d M Y H:i') }}</dd>
                    </div>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Deskripsi</dt>
                    <dd class="mt-1 text-gray-700">{{ $product->description ?? 'Tidak ada deskripsi' }}</dd>
                </div>
            </dl>

            <div class="mt-6 flex items-center gap-3">
                <a href="{{ route('products.edit', $product) }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Edit</a>
                <a href="{{ route('products.index') }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Kembali</a>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">Pergerakan Stok Terbaru</h2>
            <p class="text-sm text-gray-600 mb-4">Catatan stok terbaru untuk produk ini.</p>

            <ul class="space-y-3">
                @forelse ($product->stockMovements as $movement)
                    <li class="rounded-lg border border-gray-100 px-4 py-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-gray-900">{{ $movement->created_at->format('d M Y H:i') }}</span>
                            <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $movement->type === 'in' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $movement->type === 'in' ? 'Masuk' : 'Keluar' }}
                            </span>
                        </div>
                        <p class="mt-1 text-sm text-gray-700">Jumlah: {{ $movement->quantity }}</p>
                        @if ($movement->note)
                            <p class="text-sm text-gray-500">Catatan: {{ $movement->note }}</p>
                        @endif
                    </li>
                @empty
                    <li class="rounded-lg border border-dashed border-gray-200 px-4 py-6 text-center text-sm text-gray-500">
                        Belum ada pergerakan stok untuk produk ini.
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
</x-app-layout>
