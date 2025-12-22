<x-app-layout title="Tambah Role">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Tambah Role</h1>
            <p class="text-gray-600">Buat role baru dan atur izin aksesnya.</p>
        </div>
        <a href="{{ route('roles.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Kembali ke daftar</a>
    </div>

    <form action="{{ route('roles.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm space-y-4">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Nama Role</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700">Slug (opsional)</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none" placeholder="otomatis dari nama jika kosong">
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700">Deskripsi</label>
                <textarea name="description" rows="3" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none" placeholder="Opsional">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Izin Akses</h2>
                    <p class="text-sm text-gray-600">Pilih izin yang dimiliki role ini.</p>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-3">
                @foreach ($permissions as $permission)
                    <label class="flex items-start gap-3 rounded-lg border border-gray-200 p-3 hover:bg-gray-50">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" class="mt-1 h-4 w-4 rounded border-gray-300 text-blue-600" {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">{{ $permission->name }}</p>
                            <p class="text-xs text-gray-500">{{ $permission->slug }}</p>
                        </div>
                    </label>
                @endforeach
            </div>
            @error('permissions')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end">
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Simpan Role</button>
        </div>
    </form>
</x-app-layout>
