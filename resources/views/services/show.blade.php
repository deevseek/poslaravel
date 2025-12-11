<x-app-layout :title="'Detail Service #' . $service->id">
    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">Informasi Service</h2>
                            <p class="text-gray-600">Detail keluhan dan pelanggan.</p>
                        </div>
                        <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">{{ ucfirst($service->status) }}</span>
                    </div>
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-gray-500">Customer</dt>
                            <dd class="font-medium text-gray-900">{{ $service->customer->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Perangkat</dt>
                            <dd class="font-medium text-gray-900">{{ $service->device }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Keluhan</dt>
                            <dd class="font-medium text-gray-900">{{ $service->complaint }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Biaya Jasa</dt>
                            <dd class="font-medium text-gray-900">Rp {{ number_format($service->service_fee, 0, ',', '.') }}</dd>
                        </div>
                        <div class="md:col-span-2">
                            <dt class="text-gray-500">Diagnosa Teknisi</dt>
                            <dd class="font-medium text-gray-900">{{ $service->diagnosis ?? '-' }}</dd>
                        </div>
                        <div class="md:col-span-2">
                            <dt class="text-gray-500">Catatan Pengerjaan</dt>
                            <dd class="font-medium text-gray-900">{{ $service->notes ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900">Sparepart Digunakan</h2>
                            <p class="text-gray-600">Penggunaan sparepart akan otomatis mengurangi stok.</p>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Produk</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Qty</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Harga</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse ($service->items as $item)
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $item->product->name }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">{{ $item->quantity }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-600">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-3 text-center text-sm text-gray-500">Belum ada sparepart digunakan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <form action="{{ route('services.items.store', $service) }}" method="POST" class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Sparepart</label>
                            <select name="product_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Pilih produk</option>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} (Stok: {{ $product->stock }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Qty</label>
                            <input type="number" name="quantity" min="1" value="1" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Tambah Sparepart</button>
                        </div>
                    </form>
                </div>

                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Tracking Log</h2>
                    <div class="space-y-3">
                        @forelse ($service->logs as $log)
                            <div class="rounded border border-gray-100 bg-gray-50 px-4 py-3">
                                <div class="flex items-center justify-between text-xs text-gray-500">
                                    <span>{{ $log->created_at->format('d M Y H:i') }}</span>
                                    <span>{{ $log->user?->name ?? 'Sistem' }}</span>
                                </div>
                                <p class="mt-1 text-sm text-gray-800">{{ $log->message }}</p>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Belum ada aktivitas.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Update Diagnosa</h2>
                    <form action="{{ route('services.update', $service) }}" method="POST" class="space-y-3">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Diagnosa Teknisi</label>
                            <textarea name="diagnosis" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">{{ old('diagnosis', $service->diagnosis) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Pengerjaan</label>
                            <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">{{ old('notes', $service->notes) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Jasa</label>
                            <input type="number" step="0.01" name="service_fee" value="{{ old('service_fee', $service->service_fee) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Simpan Diagnosa</button>
                    </form>
                </div>

                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Update Status</h2>
                    <form action="{{ route('services.status', $service) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}" @selected($service->status === $status)>{{ ucfirst($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-gray-900">Perbarui Status</button>
                        @if ($service->transaction)
                            <p class="text-xs text-gray-500">Transaksi: {{ $service->transaction->invoice_number }}</p>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
