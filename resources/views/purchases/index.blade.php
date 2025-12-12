@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Riwayat Pembelian</h1>
            <p class="text-gray-600">Pantau pembelian dari supplier dan status pembayarannya.</p>
        </div>

        @permission('purchase.create')
            <a href="{{ route('purchases.create') }}"
                class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">
                Tambah Pembelian
            </a>
        @endpermission
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Riwayat Pembelian</h2>
            @permission('purchase.create')
                <a href="{{ route('purchases.create') }}" class="text-sm font-semibold text-blue-600 hover:underline">Tambah Pembelian</a>
            @endpermission
        </div>

        <div class="overflow-hidden rounded-lg border border-gray-100">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Invoice</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Supplier</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Item</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($purchases as $purchase)
                        <tr>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $purchase->invoice_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $purchase->purchase_date->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $purchase->supplier->name }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $purchase->payment_status === \App\Models\Purchase::STATUS_PAID ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ $purchase->payment_status === \App\Models\Purchase::STATUS_PAID ? 'Lunas' : 'Hutang' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">Rp {{ number_format($purchase->total_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                <ul class="list-disc space-y-1 pl-4">
                                    @foreach ($purchase->items as $item)
                                        <li>{{ $item->product->name }} ({{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }})</li>
                                    @endforeach
                                </ul>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $purchases->links() }}
        </div>
    </div>
@endsection
