<x-app-layout title="Paket Langganan">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Paket Langganan</h1>
            <p class="text-gray-600">Kelola paket yang tersedia untuk tenant.</p>
        </div>
        <a href="{{ route('subscription-plans.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">
            ➕
            <span>Tambah Paket</span>
        </a>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Harga</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Siklus</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Fitur</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse ($plans as $plan)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $plan->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $plan->code }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            Rp {{ number_format($plan->price, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $plan->billing_cycle }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if (! empty($plan->feature_labels))
                                <ul class="list-disc space-y-1 pl-4 text-xs text-gray-600">
                                    @foreach ($plan->feature_labels as $feature)
                                        <li>{{ $feature }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('subscription-plans.edit', $plan) }}" class="rounded-lg border border-blue-200 px-3 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-50">Edit</a>
                                <form action="{{ route('subscription-plans.destroy', $plan) }}" method="POST" onsubmit="return confirm('Hapus paket ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-red-200 px-3 py-1 text-xs font-semibold text-red-700 hover:bg-red-50">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada paket langganan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $plans->links() }}
    </div>
</x-app-layout>
