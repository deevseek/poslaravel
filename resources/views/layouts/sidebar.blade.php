<aside class="hidden md:block w-64 bg-white border-r border-gray-200">
    <div class="flex items-center gap-3 px-6 py-5 shadow-sm">
        <x-application-logo class="h-10 w-10" />
        <div>
            <p class="text-sm text-gray-500">POS Dealer</p>
            <p class="text-lg font-semibold text-gray-900">Komputer & Service</p>
        </div>
    </div>
    <nav class="px-4 py-6 space-y-1">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-blue-50 {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">📊</span>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('customers.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-blue-50 {{ request()->routeIs('customers.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">👤</span>
            <span>Customers</span>
        </a>
        <a href="{{ route('suppliers.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-blue-50 {{ request()->routeIs('suppliers.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">🚚</span>
            <span>Suppliers</span>
        </a>
        <a href="{{ route('categories.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-blue-50 {{ request()->routeIs('categories.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">🏷️</span>
            <span>Kategori</span>
        </a>
        <a href="{{ route('products.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-blue-50 {{ request()->routeIs('products.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">🛒</span>
            <span>Produk</span>
        </a>
        <a href="{{ route('purchases.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-blue-50 {{ request()->routeIs('purchases.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">🧾</span>
            <span>Pembelian</span>
        </a>
        <a href="{{ route('stock-movements.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-blue-50 {{ request()->routeIs('stock-movements.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">📦</span>
            <span>Pergerakan Stok</span>
        </a>
        <a href="{{ route('finances.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-blue-50 {{ request()->routeIs('finances.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
            <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">💰</span>
            <span>Keuangan</span>
        </a>
    </nav>
</aside>
