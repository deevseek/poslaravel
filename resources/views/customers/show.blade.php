<x-app-layout title="Detail Customer">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Detail Customer</h1>
            <p class="text-gray-600">Informasi lengkap customer.</p>
        </div>
        <a href="{{ route('customers.edit', $customer) }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Edit</a>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm space-y-4">
        <div>
            <p class="text-xs uppercase text-gray-500">Nama</p>
            <p class="text-lg font-semibold text-gray-900">{{ $customer->name }}</p>
        </div>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <p class="text-xs uppercase text-gray-500">Email</p>
                <p class="text-sm text-gray-800">{{ $customer->email ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs uppercase text-gray-500">Telepon</p>
                <x-wa-link :phone="$customer->phone" class="text-sm text-gray-800" />
            </div>
        </div>
        <div>
            <p class="text-xs uppercase text-gray-500">Alamat</p>
            <p class="text-sm text-gray-800 whitespace-pre-line">{{ $customer->address ?? '-' }}</p>
        </div>
        <div class="flex items-center gap-3 pt-2">
            <a href="{{ route('customers.index') }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Kembali</a>
            <form action="{{ route('customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Hapus customer ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded-lg border border-red-200 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">Hapus</button>
            </form>
        </div>
    </div>
</x-app-layout>
