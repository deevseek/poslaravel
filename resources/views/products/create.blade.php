<x-app-layout title="Tambah Produk">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Tambah Produk</h1>
        <p class="text-gray-600">Masukkan informasi produk baru beserta stok awal.</p>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <form action="{{ route('products.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="name">Nama</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="sku">SKU</label>
                    <input type="text" id="sku" name="sku" value="{{ old('sku') }}" readonly
                        placeholder="SKU akan dibuat otomatis"
                        class="mt-2 w-full rounded-lg border-gray-300 bg-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-sm text-gray-500">SKU akan dibuat otomatis saat produk disimpan.</p>
                    @error('sku')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="category_id">Kategori</label>
                    <select id="category_id" name="category_id" required
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="cost_price">HPP (Cost Price)</label>
                        <input type="number" step="0.01" id="cost_price" name="cost_price" value="{{ old('cost_price', '') }}" min="0"
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <p class="mt-1 text-xs text-gray-500">Biaya perolehan rata-rata yang menjadi dasar harga otomatis.</p>
                        @error('cost_price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700" for="stock">Stok Awal</label>
                        <input type="number" id="stock" name="stock" value="{{ old('stock', 0) }}" min="0" required
                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('stock')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="md:col-span-2">
                    <div class="rounded-lg border border-gray-200 p-4">
                        <p class="text-sm font-medium text-gray-800">Pengaturan Harga Jual</p>
                        <p class="mt-1 text-xs text-gray-500">Harga otomatis dihitung dari HPP menggunakan margin persentase.</p>

                        @php
                            $costPriceValue = old('cost_price');
                            $hasCostPrice = !is_null($costPriceValue) && $costPriceValue !== '' && (float) $costPriceValue > 0;
                            $selectedPricingMode = old('pricing_mode', \App\Models\Product::PRICING_MODE_MANUAL);
                        @endphp

                        <div class="mt-3 grid gap-4 md:grid-cols-2">
                            <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 p-3">
                                <input type="radio" name="pricing_mode" value="manual" class="mt-1" @checked($selectedPricingMode === \App\Models\Product::PRICING_MODE_MANUAL)>
                                <div>
                                    <span class="text-sm font-semibold text-gray-800">Manual</span>
                                    <p class="text-xs text-gray-500">Harga jual diisi langsung dan tidak mengikuti perubahan HPP.</p>
                                </div>
                            </label>
                            <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-gray-200 p-3" id="auto_pricing_option">
                                <input type="radio" name="pricing_mode" value="percentage" class="mt-1"
                                    @checked($selectedPricingMode === \App\Models\Product::PRICING_MODE_PERCENTAGE) @disabled(!$hasCostPrice)>
                                <div class="flex-1">
                                    <span class="text-sm font-semibold text-gray-800">Otomatis (HPP + %)</span>
                                    <p class="text-xs text-gray-500">Harga mengikuti HPP dengan margin persentase yang ditentukan.</p>
                                    <p class="mt-1 text-[11px] text-amber-600" id="auto_pricing_notice" @class(['hidden' => $hasCostPrice])>
                                        Belum ada HPP
                                    </p>
                                </div>
                            </label>
                        </div>

                        <div class="mt-4 grid gap-4 md:grid-cols-2">
                            <div id="manual_fields">
                                <label class="block text-sm font-medium text-gray-700" for="price">Harga Jual</label>
                                <input type="number" step="0.01" id="price" name="price" value="{{ old('price', 0) }}"
                                    class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div id="percentage_fields" class="hidden">
                                <div class="grid gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700" for="margin_percentage">Margin (%)</label>
                                        <input type="number" step="0.01" id="margin_percentage" name="margin_percentage" value="{{ old('margin_percentage') }}"
                                            class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        @error('margin_percentage')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700" for="preview_price">Preview Harga Jual</label>
                                        <input type="text" id="preview_price" value="" readonly
                                            class="mt-2 w-full rounded-lg border-gray-200 bg-gray-50 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <p class="mt-1 text-xs text-gray-500">Ditampilkan berdasarkan HPP dan margin yang diisi.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700" for="warranty_days">Masa Garansi (hari)</label>
                    <input type="number" id="warranty_days" name="warranty_days" value="{{ old('warranty_days', 0) }}" min="0"
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('warranty_days')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700" for="description">Deskripsi</label>
                <textarea id="description" name="description" rows="3"
                    class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('products.index') }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</a>
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const manualFields = document.getElementById('manual_fields');
            const percentageFields = document.getElementById('percentage_fields');
            const priceInput = document.getElementById('price');
            const marginInput = document.getElementById('margin_percentage');
            const previewInput = document.getElementById('preview_price');
            const costPriceInput = document.getElementById('cost_price');
            const autoRadio = document.querySelector('input[name="pricing_mode"][value="percentage"]');
            const manualRadio = document.querySelector('input[name="pricing_mode"][value="manual"]');
            const notice = document.getElementById('auto_pricing_notice');

            const toggleFields = (mode) => {
                if (mode === 'percentage') {
                    manualFields.classList.add('hidden');
                    percentageFields.classList.remove('hidden');
                    priceInput.required = false;
                    priceInput.disabled = true;
                    marginInput.required = true;
                    marginInput.disabled = false;
                    updatePreview();
                } else {
                    manualFields.classList.remove('hidden');
                    percentageFields.classList.add('hidden');
                    priceInput.required = true;
                    priceInput.disabled = false;
                    marginInput.required = false;
                    marginInput.disabled = true;
                    previewInput.value = '';
                }
            };

            const updatePreview = () => {
                const costPrice = parseFloat(costPriceInput.value);
                const margin = parseFloat(marginInput.value);

                if (!Number.isNaN(costPrice) && costPrice > 0 && !Number.isNaN(margin)) {
                    const calculatedPrice = costPrice + costPrice * (margin / 100);
                    previewInput.value = calculatedPrice.toFixed(2);
                } else {
                    previewInput.value = '';
                }
            };

            const syncAutoAvailability = () => {
                const costPrice = parseFloat(costPriceInput.value);
                const hasCostPrice = !Number.isNaN(costPrice) && costPrice > 0;

                autoRadio.disabled = !hasCostPrice;
                notice.classList.toggle('hidden', hasCostPrice);

                if (!hasCostPrice && autoRadio.checked) {
                    manualRadio.checked = true;
                    toggleFields('manual');
                }
            };

            document.querySelectorAll('input[name="pricing_mode"]').forEach((radio) => {
                radio.addEventListener('change', (event) => toggleFields(event.target.value));
            });

            marginInput.addEventListener('input', updatePreview);
            costPriceInput.addEventListener('input', () => {
                syncAutoAvailability();
                updatePreview();
            });

            syncAutoAvailability();
            const checked = document.querySelector('input[name="pricing_mode"]:checked');
            toggleFields(checked ? checked.value : 'manual');
            updatePreview();
        });
    </script>
</x-app-layout>
