<x-app-layout title="Broadcast WhatsApp">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Broadcast WhatsApp</h1>
            <p class="text-gray-600">Kirim promo atau pengumuman ke seluruh pelanggan dengan nomor HP.</p>
        </div>
        <a href="{{ route('wa-templates.index') }}" class="text-sm font-semibold text-blue-700 hover:underline">Kelola Template</a>
    </div>

    <div class="mt-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <form action="{{ route('wa.broadcast.send') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700" for="template_id">Pilih Template (Opsional)</label>
                <select id="template_id" name="template_id" class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">-- Pilih Template --</option>
                    @foreach ($templates as $template)
                        <option value="{{ $template->id }}" @selected(old('template_id') == $template->id)>{{ $template->title }} ({{ $template->code }})</option>
                    @endforeach
                </select>
                @error('template_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700" for="message">Pesan</label>
                <textarea id="message" name="message" rows="6" placeholder="Isi pesan atau biarkan kosong untuk menggunakan template"
                    class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('message') }}</textarea>
                <p class="mt-1 text-xs text-gray-500">Placeholder tersedia: <code>{{ '{{nama}}' }}</code>, <code>{{ '{{nama_toko}}' }}</code>. Template service juga mendukung <code>{{ '{{device}}' }}</code> dan <code>{{ '{{status}}' }}</code>.</p>
                @error('message')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="rounded-lg bg-yellow-50 p-4 text-sm text-yellow-900">
                Pastikan gateway WhatsApp aktif pada menu pengaturan sebelum mengirim broadcast.
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('wa.logs') }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Lihat Log</a>
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Kirim ke Pelanggan</button>
            </div>
        </form>
    </div>
</x-app-layout>
