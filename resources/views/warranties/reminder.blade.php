<x-app-layout title="Reminder Garansi">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Reminder Garansi</h1>
            <p class="text-gray-600">Garansi yang akan habis dalam 7 hari.</p>
        </div>
        <a href="{{ route('warranties.index') }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Kembali</a>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">ID</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Customer</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Tipe</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Berakhir</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse ($warranties as $warranty)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-900">#{{ $warranty->id }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $warranty->customer?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ ucfirst($warranty->type) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $warranty->end_date->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-sm">
                            <a href="{{ route('warranties.show', $warranty) }}" class="text-blue-600 hover:text-blue-700">Lihat Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">Tidak ada garansi yang akan habis dalam 7 hari.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>
