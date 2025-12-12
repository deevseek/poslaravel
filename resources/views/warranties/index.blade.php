<x-app-layout title="Daftar Garansi">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Garansi</h1>
            <p class="text-gray-600">Pantau garansi produk dan layanan.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('warranties.reminder') }}" class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100">Reminder</a>
            <a href="{{ route('warranties.create') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Tambah Garansi</a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-lg border border-gray-200 bg-white shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">ID</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Tipe</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Customer</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Periode</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse ($warranties as $warranty)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-900">#{{ $warranty->id }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ ucfirst($warranty->type) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $warranty->customer?->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $warranty->start_date->format('d M Y') }} - {{ $warranty->end_date->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-sm">
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $warranty->status === 'active' ? 'bg-green-50 text-green-700' : ($warranty->status === 'claimed' ? 'bg-yellow-50 text-yellow-700' : 'bg-gray-100 text-gray-700') }}">{{ ucfirst($warranty->status) }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-700">
                            <a href="{{ route('warranties.show', $warranty) }}" class="text-blue-600 hover:text-blue-700">Detail</a>
                            <span class="text-gray-300">|</span>
                            <a href="{{ route('warranties.edit', $warranty) }}" class="text-gray-700 hover:text-gray-900">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada data garansi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $warranties->links() }}
    </div>
</x-app-layout>
