<x-app-layout title="Daftar Supplier">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Suppliers</h1>
            <p class="text-gray-600">Kelola pemasok barang dan layanan.</p>
        </div>
        @permission('supplier.manage')
            <a href="{{ route('suppliers.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">
                ➕
                <span>Tambah Supplier</span>
            </a>
        @endpermission
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
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Kontak</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Telepon</th>
                    @permission('supplier.manage')
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                    @endpermission
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse ($suppliers as $supplier)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $supplier->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $supplier->contact_person ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $supplier->email ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <x-wa-link :phone="$supplier->phone" class="text-gray-600 hover:text-gray-800" />
                        </td>
                        @permission('supplier.manage')
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('suppliers.show', $supplier) }}" class="rounded-lg border border-gray-200 px-3 py-1 text-xs font-semibold text-gray-700 hover:bg-gray-50">Detail</a>
                                    <a href="{{ route('suppliers.edit', $supplier) }}" class="rounded-lg border border-blue-200 px-3 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-50">Edit</a>
                                    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" onsubmit="return confirm('Hapus supplier ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg border border-red-200 px-3 py-1 text-xs font-semibold text-red-700 hover:bg-red-50">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        @endpermission
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data supplier.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $suppliers->links() }}
    </div>
</x-app-layout>
