<x-app-layout title="Daftar Customer">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900">Customers</h1>
            <p class="text-gray-600">Kelola data customer untuk transaksi dan layanan.</p>
        </div>
        @permission('customer.manage')
            <a href="{{ route('customers.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">
                ➕
                <span>Tambah Customer</span>
            </a>
        @endpermission
    </div>

    @if (session('success'))
        <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('csv_success'))
        <div class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('csv_success') }}
        </div>
    @endif

    @if (session('csv_errors'))
        <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-semibold">Beberapa baris gagal diimpor:</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach (session('csv_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @permission('customer.manage')
        <div class="mb-6 rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-900">Upload CSV Customer</h2>
            <p class="mt-1 text-xs text-gray-500">Format kolom: <span class="font-semibold">name</span>, <span class="font-semibold">email</span>, <span class="font-semibold">phone</span>, <span class="font-semibold">address</span> (opsional).</p>
            <form action="{{ route('customers.import') }}" method="POST" enctype="multipart/form-data" class="mt-3 flex flex-wrap items-center gap-3">
                @csrf
                <input type="file" name="csv" accept=".csv,text/csv" required class="w-full max-w-xs rounded-lg border border-gray-300 text-sm text-gray-700 file:mr-3 file:rounded-lg file:border-0 file:bg-gray-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-gray-700 hover:file:bg-gray-200" />
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Upload CSV</button>
            </form>
        </div>
    @endpermission

    <div class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Email</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Telepon</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Alamat</th>
                    @permission('customer.manage')
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Aksi</th>
                    @endpermission
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse ($customers as $customer)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $customer->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $customer->email ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <x-wa-link :phone="$customer->phone" class="text-gray-600 hover:text-gray-800" />
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $customer->address ?? '-' }}</td>
                        @permission('customer.manage')
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('customers.show', $customer) }}" class="rounded-lg border border-gray-200 px-3 py-1 text-xs font-semibold text-gray-700 hover:bg-gray-50">Detail</a>
                                    <a href="{{ route('customers.edit', $customer) }}" class="rounded-lg border border-blue-200 px-3 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-50">Edit</a>
                                    <form action="{{ route('customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Hapus customer ini?')">
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
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Belum ada data customer.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $customers->links() }}
    </div>
</x-app-layout>
