<x-app-layout title="Template WhatsApp">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Template WhatsApp</h1>
            <p class="text-gray-600">Kelola pesan otomatis untuk pemberitahuan service dan promosi.</p>
        </div>
        <a href="{{ route('wa-templates.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Tambah Template</a>
    </div>

    <div class="mt-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr class="text-left text-gray-600">
                        <th class="py-3">Kode</th>
                        <th class="py-3">Judul</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($templates as $template)
                        <tr>
                            <td class="py-3 font-mono text-gray-900">{{ $template->code }}</td>
                            <td class="py-3 text-gray-900">{{ $template->title }}</td>
                            <td class="py-3">
                                @if ($template->is_active)
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">Aktif</span>
                                @else
                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">Nonaktif</span>
                                @endif
                            </td>
                            <td class="py-3 text-right">
                                <a href="{{ route('wa-templates.show', $template) }}" class="text-blue-600 hover:underline">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-4 text-center text-gray-500">Belum ada template.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $templates->links() }}</div>
    </div>
</x-app-layout>
