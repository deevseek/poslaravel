<x-app-layout title="Cetak Alamat Customer">
    <style>
        @media print {
            .print-actions {
                display: none !important;
            }

            .print-card {
                box-shadow: none !important;
                border: none !important;
            }
        }
    </style>

    <div class="mx-auto max-w-3xl space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-4 print-actions">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Cetak Alamat Customer</h1>
                <p class="text-gray-600">Gunakan untuk label pengiriman.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('customers.show', $customer) }}" class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50">Kembali</a>
                <button type="button" onclick="window.print()" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Cetak</button>
            </div>
        </div>

        <div class="print-card rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div class="space-y-3">
                    <p class="text-xs uppercase text-gray-500">Pengirim</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $store['name'] }}</p>
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $store['address'] ?? '-' }}</p>
                    <p class="text-sm text-gray-700">Telp: {{ $store['phone'] ?? '-' }}</p>
                </div>
                <div class="space-y-3">
                    <p class="text-xs uppercase text-gray-500">Penerima</p>
                    <p class="text-lg font-semibold text-gray-900">{{ $customer->name }}</p>
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $customer->address ?? '-' }}</p>
                    <p class="text-sm text-gray-700">Telp: {{ $customer->phone ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
