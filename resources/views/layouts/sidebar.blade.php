<aside class="hidden md:block w-64 bg-slate-900 border-r border-slate-800">
    <div class="flex items-center gap-3 px-6 py-5 shadow-sm">
        <x-application-logo class="h-10 w-10" />
        <div>
            <p class="text-sm text-slate-400">POS Dealer</p>
            <p class="text-lg font-semibold text-white">Komputer & Service</p>
        </div>
    </div>
    <nav class="px-4 py-6 space-y-1">
        @permission('dashboard.view')
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-slate-800/70 {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-white' : 'text-slate-200' }}">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-white">📊</span>
                <span>Dashboard</span>
            </a>
        @endpermission

        @permission('pos.access')
            <a href="{{ route('pos.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-slate-800/70 {{ request()->routeIs('pos.*') ? 'bg-slate-800 text-white' : 'text-slate-200' }}">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-white">🧾</span>
                <span>Point of Sales</span>
            </a>
        @endpermission

        @permission('service.access')
            <a href="{{ route('services.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-slate-800/70 {{ request()->routeIs('services.*') ? 'bg-slate-800 text-white' : 'text-slate-200' }}">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-white">🛠️</span>
                <span>Servis</span>
            </a>
        @endpermission
        
        @permission('customer.manage')
            <a href="{{ route('customers.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-slate-800/70 {{ request()->routeIs('customers.*') ? 'bg-slate-800 text-white' : 'text-slate-200' }}">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-white">👤</span>
                <span>Customers</span>
            </a>
        @endpermission
        @permission('supplier.manage')
            <a href="{{ route('suppliers.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-slate-800/70 {{ request()->routeIs('suppliers.*') ? 'bg-slate-800 text-white' : 'text-slate-200' }}">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-white">🚚</span>
                <span>Suppliers</span>
            </a>
        @endpermission
        @permission('inventory.view')
            <a href="{{ route('categories.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-slate-800/70 {{ request()->routeIs('categories.*') ? 'bg-slate-800 text-white' : 'text-slate-200' }}">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-white">🏷️</span>
                <span>Kategori</span>
            </a>
            <a href="{{ route('products.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-slate-800/70 {{ request()->routeIs('products.*') ? 'bg-slate-800 text-white' : 'text-slate-200' }}">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-white">🛒</span>
                <span>Produk</span>
            </a>
        @endpermission
        @permission(['purchase.view', 'purchase.create'])
            <a href="{{ route('purchases.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-slate-800/70 {{ request()->routeIs('purchases.*') ? 'bg-slate-800 text-white' : 'text-slate-200' }}">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-white">🧾</span>
                <span>Pembelian</span>
            </a>
        @endpermission
        @permission('inventory.view')
            <a href="{{ route('stock-movements.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-slate-800/70 {{ request()->routeIs('stock-movements.*') ? 'bg-slate-800 text-white' : 'text-slate-200' }}">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-white">📦</span>
                <span>Pergerakan Stok</span>
            </a>
        @endpermission
        @permission('finance.view')
            <a href="{{ route('finances.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-slate-800/70 {{ request()->routeIs('finances.*') ? 'bg-slate-800 text-white' : 'text-slate-200' }}">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-white">💰</span>
                <span>Keuangan</span>
            </a>
        @endpermission

        @permission(['user.manage', 'role.manage'])
            <div class="pt-2">
                <p class="px-3 pb-1 text-xs font-semibold uppercase tracking-wide text-slate-400">Pengguna</p>
                @permission('user.manage')
                    <a href="{{ route('users.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-slate-800/70 {{ request()->routeIs('users.*') ? 'bg-slate-800 text-white' : 'text-slate-200' }}">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-white">👥</span>
                        <span>Users</span>
                    </a>
                @endpermission
                @permission('role.manage')
                    <a href="{{ route('roles.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-slate-800/70 {{ request()->routeIs('roles.*') ? 'bg-slate-800 text-white' : 'text-slate-200' }}">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-white">🛡️</span>
                        <span>Roles & Permissions</span>
                    </a>
                @endpermission
            </div>
        @endpermission
        @permission('settings.view')
            <a href="{{ route('settings.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-slate-800/70 {{ request()->routeIs('settings.*') ? 'bg-slate-800 text-white' : 'text-slate-200' }}">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-white">⚙️</span>
                <span>Pengaturan</span>
            </a>
        @endpermission

        @permission('tenant.manage')
            <div class="pt-2">
                <p class="px-3 pb-1 text-xs font-semibold uppercase tracking-wide text-slate-400">Tenant</p>
                <a href="{{ route('tenants.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-slate-800/70 {{ request()->routeIs('tenants.*') ? 'bg-slate-800 text-white' : 'text-slate-200' }}">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-white">🏢</span>
                    <span>Daftar Tenant</span>
                </a>
                <a href="{{ route('subscription-plans.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-slate-800/70 {{ request()->routeIs('subscription-plans.*') ? 'bg-slate-800 text-white' : 'text-slate-200' }}">
                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-white">📦</span>
                    <span>Manajemen Paket</span>
                </a>
            </div>
        @endpermission

        @permission(['warranty.view', 'warranty.claim'])
            <div class="pt-2">
                <p class="px-3 pb-1 text-xs font-semibold uppercase tracking-wide text-slate-400">Garansi</p>
                @permission('warranty.view')
                    <a href="{{ route('warranties.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-slate-800/70 {{ request()->routeIs('warranties.index') ? 'bg-slate-800 text-white' : 'text-slate-200' }}">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-white">🛡️</span>
                        <span>Daftar Garansi</span>
                    </a>
                    <a href="{{ route('warranties.reminder') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-slate-800/70 {{ request()->routeIs('warranties.reminder') ? 'bg-slate-800 text-white' : 'text-slate-200' }}">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-white">⏰</span>
                        <span>Reminder Garansi</span>
                    </a>
                @endpermission
                @permission('warranty.claim')
                    <a href="{{ route('warranties.create') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-slate-800/70 {{ request()->routeIs('warranties.create') ? 'bg-slate-800 text-white' : 'text-slate-200' }}">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-white">📝</span>
                        <span>Klaim Garansi</span>
                    </a>
                @endpermission
            </div>
        @endpermission

        @permission(['whatsapp.broadcast', 'whatsapp.template_manage', 'whatsapp.log_view'])
            <div class="pt-2">
                <p class="px-3 pb-1 text-xs font-semibold uppercase tracking-wide text-slate-400">WhatsApp</p>
                @permission('whatsapp.broadcast')
                    <a href="{{ route('wa.broadcast') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-slate-800/70 {{ request()->routeIs('wa.broadcast') ? 'bg-slate-800 text-white' : 'text-slate-200' }}">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-white">📣</span>
                        <span>Broadcast</span>
                    </a>
                @endpermission
                @permission('whatsapp.template_manage')
                    <a href="{{ route('wa-templates.index') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-slate-800/70 {{ request()->routeIs('wa-templates.*') ? 'bg-slate-800 text-white' : 'text-slate-200' }}">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-white">🧩</span>
                        <span>Template Pesan</span>
                    </a>
                @endpermission
                @permission('whatsapp.log_view')
                    <a href="{{ route('wa.logs') }}" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium hover:bg-slate-800/70 {{ request()->routeIs('wa.logs') ? 'bg-slate-800 text-white' : 'text-slate-200' }}">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-white">📜</span>
                        <span>Log Pengiriman</span>
                    </a>
                @endpermission
            </div>
        @endpermission
    </nav>
</aside>
