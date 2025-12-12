<x-app-layout title="Log WhatsApp">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Log WhatsApp</h1>
            <p class="text-gray-600">Riwayat semua pesan yang dikirim melalui gateway Baileys.</p>
        </div>
        <a href="{{ route('wa.broadcast') }}" class="text-sm font-semibold text-blue-700 hover:underline">Kirim Broadcast</a>
    </div>

    <div class="mt-6 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr class="text-left text-gray-600">
                        <th class="py-3">Waktu</th>
                        <th class="py-3">Tujuan</th>
                        <th class="py-3">Tipe</th>
                        <th class="py-3">Status</th>
                        <th class="py-3">Pesan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($logs as $log)
                        <tr>
                            <td class="py-3 text-gray-900">{{ $log->created_at?->format('d M Y H:i') }}</td>
                            <td class="py-3 text-gray-900">{{ $log->phone }}</td>
                            <td class="py-3 capitalize text-gray-900">{{ $log->type }}</td>
                            <td class="py-3">
                                @if ($log->status === 'sent')
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">Sent</span>
                                @else
                                    <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">Failed</span>
                                @endif
                            </td>
                            <td class="py-3 text-gray-800">{{ \Illuminate\Support\Str::limit($log->message, 100) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-gray-500">Belum ada log pengiriman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $logs->links() }}</div>
    </div>
</x-app-layout>
