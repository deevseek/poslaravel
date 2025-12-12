<x-app-layout title="Buat Template WhatsApp">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Buat Template</h1>
        <p class="text-gray-600">Template dapat digunakan untuk notifikasi status service atau broadcast.</p>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <form action="{{ route('wa-templates.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700" for="code">Kode</label>
                <input type="text" id="code" name="code" value="{{ old('code') }}" required
                    class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <p class="mt-1 text-xs text-gray-500">Gunakan kode seperti <code>service_menunggu</code>, <code>service_selesai</code>, atau kode bebas untuk promo.</p>
                @error('code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700" for="title">Judul</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                    class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700" for="message">Pesan</label>
                <textarea id="message" name="message" rows="5" required
                    class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('message') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Placeholder yang tersedia: <code>{{ '{{nama}}' }}</code>, <code>{{ '{{device}}' }}</code>, <code>{{ '{{status}}' }}</code>, <code>{{ '{{nama_toko}}' }}</code>.</p>
                @error('message')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" id="is_active" name="is_active" value="1" class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" {{ old('is_active', true) ? 'checked' : '' }}>
                <label for="is_active" class="text-sm text-gray-700">Aktif</label>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('wa-templates.index') }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</a>
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Simpan</button>
            </div>
        </form>
    </div>
</x-app-layout>
