<aside class="hidden md:block w-64 bg-white border-r border-gray-200">
    <div class="flex items-center gap-3 px-6 py-5 shadow-sm">
        <x-application-logo class="h-10 w-10" />
        <div>
            <p class="text-sm text-gray-500">POS Dealer</p>
            <p class="text-lg font-semibold text-gray-900">Komputer & Service</p>
        </div>
    </div>
    <nav class="px-4 py-6 space-y-1">
        @permission('dashboard.view')
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-blue-50 {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">📊</span>
                <span>Dashboard</span>
            </a>
        @endpermission
        @permission('customer.manage')
            <a href="{{ route('customers.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-blue-50 {{ request()->routeIs('customers.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">👤</span>
                <span>Customers</span>
            </a>
        @endpermission
        @permission('supplier.manage')
            <a href="{{ route('suppliers.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-blue-50 {{ request()->routeIs('suppliers.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">🚚</span>
                <span>Suppliers</span>
            </a>
        @endpermission
        @permission('inventory.view')
            <a href="{{ route('categories.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-blue-50 {{ request()->routeIs('categories.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">🏷️</span>
                <span>Kategori</span>
            </a>
            <a href="{{ route('products.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-blue-50 {{ request()->routeIs('products.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">🛒</span>
                <span>Produk</span>
            </a>
        @endpermission
        @permission(['purchase.view', 'purchase.create'])
            <a href="{{ route('purchases.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-blue-50 {{ request()->routeIs('purchases.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">🧾</span>
                <span>Pembelian</span>
            </a>
        @endpermission
        @permission('inventory.view')
            <a href="{{ route('stock-movements.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-blue-50 {{ request()->routeIs('stock-movements.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">📦</span>
                <span>Pergerakan Stok</span>
            </a>
        @endpermission
        @permission('finance.view')
            <a href="{{ route('finances.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-blue-50 {{ request()->routeIs('finances.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">💰</span>
                <span>Keuangan</span>
            </a>
        @endpermission

        @permission('pos.access')
            <a href="{{ route('pos.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-blue-50 {{ request()->routeIs('pos.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">🧾</span>
                <span>Point of Sales</span>
            </a>
        @endpermission

        @permission('service.access')
            <a href="{{ route('services.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-blue-50 {{ request()->routeIs('services.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">🛠️</span>
                <span>Servis</span>
            </a>
        @endpermission
        @permission('settings.view')
            <a href="{{ route('settings.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-blue-50 {{ request()->routeIs('settings.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">⚙️</span>
                <span>Pengaturan</span>
            </a>
        @endpermission

        @permission('tenant.manage')
            <a href="{{ route('tenants.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-blue-50 {{ request()->routeIs('tenants.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">🏢</span>
                <span>Tenant</span>
            </a>
        @endpermission

        @permission(['warranty.view', 'warranty.claim'])
            <div class="pt-2">
                <p class="px-3 pb-1 text-xs font-semibold uppercase tracking-wide text-gray-500">Garansi</p>
                @permission('warranty.view')
                    <a href="{{ route('warranties.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-blue-50 {{ request()->routeIs('warranties.index') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">🛡️</span>
                        <span>Daftar Garansi</span>
                    </a>
                    <a href="{{ route('warranties.reminder') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-blue-50 {{ request()->routeIs('warranties.reminder') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">⏰</span>
                        <span>Reminder Garansi</span>
                    </a>
                @endpermission
                @permission('warranty.claim')
                    <a href="{{ route('warranties.create') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-blue-50 {{ request()->routeIs('warranties.create') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">📝</span>
                        <span>Klaim Garansi</span>
                    </a>
                @endpermission
            </div>
        @endpermission

        @permission(['whatsapp.broadcast', 'whatsapp.template_manage', 'whatsapp.log_view'])
            <div class="pt-2">
                <p class="px-3 pb-1 text-xs font-semibold uppercase tracking-wide text-gray-500">WhatsApp</p>
                @permission('whatsapp.broadcast')
                    <a href="{{ route('wa.broadcast') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-blue-50 {{ request()->routeIs('wa.broadcast') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">📣</span>
                        <span>Broadcast</span>
                    </a>
                @endpermission
                @permission('whatsapp.template_manage')
                    <a href="{{ route('wa-templates.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-blue-50 {{ request()->routeIs('wa-templates.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">🧩</span>
                        <span>Template Pesan</span>
                    </a>
                @endpermission
                @permission('whatsapp.log_view')
                    <a href="{{ route('wa.logs') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-blue-50 {{ request()->routeIs('wa.logs') ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-blue-100 text-blue-600">📜</span>
                        <span>Log Pengiriman</span>
                    </a>
                @endpermission
            </div>
        @endpermission
    </nav>
</aside>
