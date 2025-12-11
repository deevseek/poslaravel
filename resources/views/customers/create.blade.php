<x-app-layout title="Tambah Customer">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Tambah Customer</h1>
        <p class="text-gray-600">Lengkapi informasi customer baru.</p>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <form action="{{ route('customers.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700" for="name">Nama</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                    class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="phone">Telepon</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700" for="address">Alamat</label>
                <textarea id="address" name="address" rows="3"
                    class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('address') }}</textarea>
                @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('customers.index') }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</a>
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</x-app-layout>
