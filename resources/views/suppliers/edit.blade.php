<x-app-layout title="Edit Supplier">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Edit Supplier</h1>
        <p class="text-gray-600">Perbarui informasi pemasok.</p>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <form action="{{ route('suppliers.update', $supplier) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700" for="name">Nama Supplier</label>
                <input type="text" id="name" name="name" value="{{ old('name', $supplier->name) }}" required
                    class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="contact_person">Kontak Person</label>
                    <input type="text" id="contact_person" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}"
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('contact_person')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="phone">Telepon</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $supplier->phone) }}"
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $supplier->email) }}"
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="address">Alamat</label>
                    <textarea id="address" name="address" rows="3"
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('address', $supplier->address) }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('suppliers.index') }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</a>
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</x-app-layout>
