<x-app-layout title="Service Baru">
    <div class="max-w-3xl">
        <h1 class="text-2xl font-semibold text-gray-900">Input Service Masuk</h1>
        <p class="text-gray-600 mb-6">Catat perangkat dan keluhan pelanggan.</p>

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <form action="{{ route('services.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                    <select name="customer_id" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih customer</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>{{ $customer->name }}</option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Perangkat</label>
                    <input type="text" name="device" value="{{ old('device') }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Contoh: Laptop Asus TUF" />
                    @error('device')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keluhan</label>
                    <textarea name="complaint" rows="4" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Tuliskan keluhan yang disampaikan pelanggan">{{ old('complaint') }}</textarea>
                    @error('complaint')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Perkiraan Biaya Jasa</label>
                    <input type="number" step="0.01" name="service_fee" value="{{ old('service_fee', 0) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                    @error('service_fee')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Masa Garansi Service (hari)</label>
                    <input type="number" name="warranty_days" min="0" value="{{ old('warranty_days', 0) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                    @error('warranty_days')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-4">
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Simpan</button>
                    <a href="{{ route('services.index') }}" class="ml-2 text-sm text-gray-600 hover:text-gray-800">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
