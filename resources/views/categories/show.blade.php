<x-app-layout title="Detail Kategori">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Detail Kategori</h1>
        <p class="text-gray-600">Informasi detail kategori yang dipilih.</p>
    </div>

    <div class="grid gap-6 md:grid-cols-2">
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <dl class="space-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Nama</dt>
                    <dd class="mt-1 text-lg font-semibold text-gray-900">{{ $category->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Deskripsi</dt>
                    <dd class="mt-1 text-gray-700">{{ $category->description ?? 'Tidak ada deskripsi' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Jumlah Produk</dt>
                    <dd class="mt-1 text-gray-700">{{ $category->products_count }} produk</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Dibuat</dt>
                    <dd class="mt-1 text-gray-700">{{ $category->created_at->format('d M Y H:i') }}</dd>
                </div>
            </dl>

            <div class="mt-6 flex items-center gap-3">
                <a href="{{ route('categories.edit', $category) }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Edit</a>
                <a href="{{ route('categories.index') }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Kembali</a>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">Produk terkait</h2>
            <p class="text-sm text-gray-600 mb-4">Daftar singkat produk dalam kategori ini.</p>
            <ul class="space-y-3">
                @forelse ($category->products()->latest()->take(5)->get() as $product)
                    <li class="flex items-center justify-between rounded-lg border border-gray-100 px-4 py-3">
                        <div>
                            <p class="font-semibold text-gray-900">{{ $product->name }}</p>
                            <p class="text-sm text-gray-600">SKU: {{ $product->sku }}</p>
                        </div>
                        <a href="{{ route('products.show', $product) }}" class="text-sm font-semibold text-blue-600 hover:underline">Detail</a>
                    </li>
                @empty
                    <li class="rounded-lg border border-dashed border-gray-200 px-4 py-6 text-center text-sm text-gray-500">
                        Belum ada produk dalam kategori ini.
                    </li>
                @endforelse
            </ul>
        </div>
    </div>
</x-app-layout>
