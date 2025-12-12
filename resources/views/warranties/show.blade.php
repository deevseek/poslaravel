<x-app-layout :title="'Detail Garansi #' . $warranty->id">
    <div class="space-y-6">
        @if (session('success'))
            <div class="rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Garansi #{{ $warranty->id }}</h1>
                <p class="text-gray-600">{{ ucfirst($warranty->type) }} • Ref ID: {{ $warranty->reference_id }}</p>
            </div>
            <a href="{{ route('warranties.edit', $warranty) }}" class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-black">Edit Garansi</a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Garansi</h2>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <dt class="text-gray-500">Customer</dt>
                            <dd class="font-medium text-gray-900">{{ $warranty->customer?->name ?? '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Status</dt>
                            <dd>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $warranty->status === 'active' ? 'bg-green-50 text-green-700' : ($warranty->status === 'claimed' ? 'bg-yellow-50 text-yellow-700' : 'bg-gray-100 text-gray-700') }}">{{ ucfirst($warranty->status) }}</span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Mulai</dt>
                            <dd class="font-medium text-gray-900">{{ $warranty->start_date->format('d M Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-gray-500">Berakhir</dt>
                            <dd class="font-medium text-gray-900">{{ $warranty->end_date->format('d M Y') }}</dd>
                        </div>
                        <div class="sm:col-span-2">
                            <dt class="text-gray-500">Deskripsi</dt>
                            <dd class="font-medium text-gray-900">{{ $warranty->description ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Klaim Garansi</h2>
                            <p class="text-gray-600 text-sm">Catat proses klaim dan resolusinya.</p>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-lg border border-gray-100">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Tanggal Klaim</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Catatan Teknisi</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Resolusi</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @forelse ($warranty->claims as $claim)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-800">{{ $claim->claim_date->format('d M Y') }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $claim->technician_notes }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ ucfirst($claim->status) }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $claim->resolution ?? '-' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            <form action="{{ route('warranty-claims.update', $claim) }}" method="POST" class="space-y-2">
                                                @csrf
                                                @method('PUT')
                                                <div class="grid grid-cols-1 gap-2">
                                                    <input type="date" name="claim_date" value="{{ $claim->claim_date->format('Y-m-d') }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" />
                                                    <textarea name="technician_notes" rows="2" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">{{ $claim->technician_notes }}</textarea>
                                                    <select name="status" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                                                        @foreach ($claimStatuses as $status)
                                                            <option value="{{ $status }}" @selected($claim->status === $status)>{{ ucfirst($status) }}</option>
                                                        @endforeach
                                                    </select>
                                                    <textarea name="resolution" rows="2" class="w-full rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Resolusi">{{ $claim->resolution }}</textarea>
                                                </div>
                                                <button type="submit" class="rounded-lg bg-gray-900 px-3 py-2 text-xs font-semibold text-white shadow hover:bg-black">Update</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">Belum ada klaim garansi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Tambah Klaim</h2>
                    <form action="{{ route('warranty-claims.store', $warranty) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Klaim</label>
                            <input type="date" name="claim_date" value="{{ old('claim_date', now()->toDateString()) }}" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" />
                            @error('claim_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Teknisi</label>
                            <textarea name="technician_notes" rows="3" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Detail pengecekan">{{ old('technician_notes') }}</textarea>
                            @error('technician_notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Simpan Klaim</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
