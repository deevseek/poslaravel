<x-app-layout title="Daftar Tenant">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Tenant</h1>
            <p class="text-gray-600">Kelola tenant yang terhubung dengan sistem POS.</p>
        </div>
        <div class="flex items-center gap-3">
            <form action="{{ route('tenants.sync-migrations') }}" method="POST" onsubmit="return confirm('Jalankan sinkronisasi tabel untuk semua tenant?')">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-blue-200 px-4 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-50">
                    🔄
                    <span>Sinkronkan Tabel Tenant</span>
                </button>
            </form>
            <a href="{{ route('tenants.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">
                ➕
                <span>Tambah Tenant</span>
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->has('general'))
        <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first('general') }}
        </div>
    @endif

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Subdomain</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Paket</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Berakhir</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Perpanjang</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Database</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Dibuat</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse ($tenants as $tenant)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $tenant->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $tenant->subdomain }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $tenant->plan->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if ($tenant->status === 'active')
                                <span class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">Aktif</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-yellow-50 px-3 py-1 text-xs font-semibold text-yellow-700">Ditangguhkan</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @php
                                $subscription = $tenant->latestSubscription;
                            @endphp
                            @if ($subscription?->end_date)
                                <div class="text-sm text-gray-700">
                                    {{ $subscription->end_date->format('d M Y') }}
                                </div>
                                @if ($subscription->end_date->isPast())
                                    <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-1 text-[11px] font-semibold text-red-700">Kadaluarsa</span>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if ($subscription)
                                @if ($subscription->auto_renew)
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">Otomatis</span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">Manual</span>
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $tenant->database_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $tenant->created_at?->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('tenants.edit', $tenant) }}" class="rounded-lg border border-blue-200 px-3 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-50">Kelola</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada tenant terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $tenants->links() }}
    </div>
</x-app-layout>
