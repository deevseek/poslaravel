<x-app-layout title="Detail Template WhatsApp">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">{{ $template->title }}</h1>
            <p class="text-gray-600">Kode: {{ $template->code }}</p>
        </div>
        <a href="{{ route('wa-templates.edit', $template) }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Ubah Template</a>
    </div>

    <div class="mt-6 space-y-4 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex items-center gap-2">
            <span class="text-sm font-semibold text-gray-700">Status:</span>
            @if ($template->is_active)
                <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">Aktif</span>
            @else
                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">Nonaktif</span>
            @endif
        </div>

        <div>
            <h2 class="text-sm font-semibold text-gray-700">Pesan</h2>
            <div class="mt-2 whitespace-pre-line rounded-lg bg-gray-50 p-4 text-sm text-gray-800">{{ $template->message }}</div>
        </div>

        <div class="rounded-lg bg-blue-50 p-4 text-sm text-blue-900">
            Placeholder yang dapat digunakan: <code>{{ '{{nama}}' }}</code>, <code>{{ '{{device}}' }}</code>, <code>{{ '{{status}}' }}</code>, <code>{{ '{{nama_toko}}' }}</code>.
        </div>
    </div>
</x-app-layout>
