<x-app-layout title="Pergerakan Stok">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Pergerakan Stok</h1>
            <p class="text-gray-600">Catat dan pantau riwayat perubahan stok produk.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-1 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">Catat Pergerakan Baru</h2>
            <p class="text-sm text-gray-600 mb-4">Tambahkan stok masuk atau keluar untuk produk.</p>

            <form action="{{ route('stock-movements.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700" for="product_id">Produk</label>
                    <select id="product_id" name="product_id" required
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih produk</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" @selected(old('product_id') == $product->id)>{{ $product->name }} (Stok: {{ $product->stock }})</option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="type">Jenis</label>
                        <select id="type" name="type" required
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Pilih jenis</option>
                            <option value="in" @selected(old('type') === 'in')>Stok Masuk</option>
                            <option value="out" @selected(old('type') === 'out')>Stok Keluar</option>
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="quantity">Jumlah</label>
                        <input type="number" id="quantity" name="quantity" min="1" value="{{ old('quantity', 1) }}" required
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('quantity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700" for="note">Catatan (opsional)</label>
                    <input type="text" id="note" name="note" value="{{ old('note') }}"
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('note')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end">
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>

        <div class="lg:col-span-2 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Riwayat Pergerakan</h2>
            </div>

            <div class="overflow-hidden rounded-lg border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Produk</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Sumber</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Jenis</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse ($movements as $movement)
                            <tr>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $movement->created_at->format('d M Y H:i') }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $movement->product->name }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="rounded-full bg-blue-100 px-2 py-0.5 text-[11px] font-semibold uppercase text-blue-700">{{ $movement->source ?? 'Manual' }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $movement->type === 'in' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $movement->type === 'in' ? 'Masuk' : 'Keluar' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $movement->quantity }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $movement->note ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada pergerakan stok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $movements->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
