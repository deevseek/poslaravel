<x-app-layout title="Daftar Produk">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Produk</h1>
            <p class="text-gray-600">Kelola data produk dan stok tersedia.</p>
        </div>
        <a href="{{ route('products.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">
            ➕
            <span>Tambah Produk</span>
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('csv_success'))
        <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('csv_success') }}
        </div>
    @endif

    @if (session('csv_errors'))
        <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-semibold">Beberapa baris gagal diimpor:</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach (session('csv_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <h2 class="text-sm font-semibold text-gray-900">Upload CSV Produk</h2>
        <p class="mt-1 text-xs text-gray-500">
            Format kolom: <span class="font-semibold">name</span>, <span class="font-semibold">category</span>, <span class="font-semibold">stock</span>,
            <span class="font-semibold">pricing_mode</span>, <span class="font-semibold">price</span>, <span class="font-semibold">cost_price</span>,
            <span class="font-semibold">margin_percentage</span>, <span class="font-semibold">sku</span>, <span class="font-semibold">warranty_days</span>, <span class="font-semibold">description</span>.
        </p>
        <form action="{{ route('products.import') }}" method="POST" enctype="multipart/form-data" class="mt-3 flex flex-wrap items-center gap-3">
            @csrf
            <input type="file" name="csv" accept=".csv,text/csv" required class="w-full max-w-xs rounded-lg border border-gray-300 text-sm text-gray-700 file:mr-3 file:rounded-lg file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-gray-700 hover:file:bg-gray-200" />
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Upload CSV</button>
        </form>
    </div>

    <div class="mb-4 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
        <p>ℹ️ HPP dihitung menggunakan metode rata-rata tertimbang dari pembelian supplier.</p>
        <p class="mt-1 text-xs text-blue-700">Harga otomatis dihitung dari HPP menggunakan margin persentase.</p>
    </div>

    <form action="{{ route('products.index') }}" method="GET" class="mb-4 flex flex-wrap items-center gap-3">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Cari nama atau SKU produk..."
            class="w-full max-w-xs rounded-lg border border-gray-300 text-sm text-gray-700 shadow-sm focus:border-blue-500 focus:ring-blue-500"
        />
        <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">
            Cari
        </button>
        @if (request('search'))
            <a href="{{ route('products.index') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-800">Reset</a>
        @endif
    </form>

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">SKU</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                        HPP (Rata-rata)
                        <span class="text-gray-400" title="HPP dihitung menggunakan metode rata-rata pembelian">ⓘ</span>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Harga</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Mode Harga</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Stok</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse ($products as $product)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $product->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $product->sku }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $product->category?->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if (!is_null($product->cost_price) && $product->cost_price > 0)
                                Rp {{ number_format($product->cost_price, 0, ',', '.') }}
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-500">
                                    Belum ada HPP
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <p>Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                            @if ($product->pricing_mode === \App\Models\Product::PRICING_MODE_PERCENTAGE && !is_null($product->margin_percentage))
                                <p class="text-xs text-gray-500">Margin: {{ number_format($product->margin_percentage, 2) }}%</p>
                            @else
                                <p class="text-xs text-gray-500">Harga manual</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if ($product->pricing_mode === \App\Models\Product::PRICING_MODE_PERCENTAGE)
                                <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">Persentase</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700">Manual</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $product->stock }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('products.show', $product) }}" class="rounded-lg border border-gray-200 px-3 py-1 text-xs font-semibold text-gray-700 hover:bg-gray-50">Detail</a>
                                <a href="{{ route('products.edit', $product) }}" class="rounded-lg border border-blue-200 px-3 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-50">Edit</a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Hapus produk ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-red-200 px-3 py-1 text-xs font-semibold text-red-700 hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data produk.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $products->links() }}
    </div>
</x-app-layout>
