<x-app-layout title="Edit Paket Langganan">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Edit Paket Langganan</h1>
            <p class="text-gray-600">Perbarui detail paket untuk tenant.</p>
        </div>
        <a href="{{ route('subscription-plans.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Kembali ke daftar</a>
    </div>

    <form action="{{ route('subscription-plans.update', $subscriptionPlan) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm space-y-4">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold text-gray-700" for="name">Nama Paket</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $subscriptionPlan->name) }}" required
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700" for="code">Kode</label>
                    <input type="text" id="code" name="code" value="{{ old('code', $subscriptionPlan->code) }}" required
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    @error('code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold text-gray-700" for="price">Harga</label>
                    <input type="number" id="price" name="price" value="{{ old('price', $subscriptionPlan->price) }}" min="0" step="0.01" required
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700" for="billing_cycle">Siklus Tagihan</label>
                    <input type="text" id="billing_cycle" name="billing_cycle" value="{{ old('billing_cycle', $subscriptionPlan->billing_cycle) }}" required
                        class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none">
                    @error('billing_cycle')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <p class="block text-sm font-semibold text-gray-700">Fitur</p>
                <div class="mt-2 grid gap-2 sm:grid-cols-2">
                    @foreach ($featureOptions as $feature)
                        <label class="flex items-center gap-2 rounded-lg border border-gray-200 px-3 py-2 text-sm text-gray-700">
                            <input type="checkbox" name="features[]" value="{{ $feature['value'] }}"
                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                {{ in_array($feature['value'], old('features', $selectedFeatures), true) ? 'checked' : '' }}>
                            <span>{{ $feature['label'] }}</span>
                        </label>
                    @endforeach
                </div>
                <p class="mt-1 text-xs text-gray-500">Pilih fitur sesuai modul yang tersedia.</p>
                @error('features')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('features.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('subscription-plans.index') }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</a>
            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Simpan Perubahan</button>
        </div>
    </form>
</x-app-layout>
