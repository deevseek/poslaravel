<x-app-layout title="Daftar Service">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Service</h1>
            <p class="text-gray-600">Pantau progres service dan status terbaru.</p>
        </div>
        <a href="{{ route('services.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">
            ➕
            <span>Service Baru</span>
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Perangkat</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Biaya Jasa</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Dibuat</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse ($services as $service)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $service->customer->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $service->device }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">Rp {{ number_format($service->service_fee, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">{{ ucfirst($service->status) }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $service->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 text-right">
                            <a href="{{ route('services.show', $service) }}" class="rounded-lg border border-gray-200 px-3 py-1 text-xs font-semibold text-gray-700 hover:bg-gray-50">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data service.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $services->links() }}
    </div>
</x-app-layout>
