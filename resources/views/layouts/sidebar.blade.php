<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ route('dashboard') }}" class="brand-link">
        <x-application-logo class="brand-image img-circle elevation-3" />
        <span class="brand-text font-weight-light">POS Dealer</span>
    </a>

    <div class="sidebar">
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                @permission('dashboard.view')
                    <li class="nav-item">
                        <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-line"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                @endpermission

                @permission('pos.access')
                    <li class="nav-item">
                        <a href="{{ route('pos.index') }}" class="nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-receipt"></i>
                            <p>Point of Sales</p>
                        </a>
                    </li>
                @endpermission

                @permission('service.access')
                    <li class="nav-item">
                        <a href="{{ route('services.index') }}" class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tools"></i>
                            <p>Servis</p>
                        </a>
                    </li>
                @endpermission

                @permission('customer.manage')
                    <li class="nav-item">
                        <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user"></i>
                            <p>Customers</p>
                        </a>
                    </li>
                @endpermission
                @permission('supplier.manage')
                    <li class="nav-item">
                        <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-truck"></i>
                            <p>Suppliers</p>
                        </a>
                    </li>
                @endpermission

                @permission('inventory.view')
                    <li class="nav-item">
                        <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tags"></i>
                            <p>Kategori</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-boxes"></i>
                            <p>Produk</p>
                        </a>
                    </li>
                @endpermission

                @permission(['purchase.view', 'purchase.create'])
                    <li class="nav-item">
                        <a href="{{ route('purchases.index') }}" class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-shopping-cart"></i>
                            <p>Pembelian</p>
                        </a>
                    </li>
                @endpermission

                @permission('inventory.view')
                    <li class="nav-item">
                        <a href="{{ route('stock-movements.index') }}" class="nav-link {{ request()->routeIs('stock-movements.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-dolly"></i>
                            <p>Pergerakan Stok</p>
                        </a>
                    </li>
                @endpermission

                @permission('finance.view')
                    <li class="nav-item">
                        <a href="{{ route('finances.index') }}" class="nav-link {{ request()->routeIs('finances.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-coins"></i>
                            <p>Keuangan</p>
                        </a>
                    </li>
                @endpermission

                @permission(['user.manage', 'role.manage'])
                    <li class="nav-header">Pengguna</li>
                    @permission('user.manage')
                        <li class="nav-item">
                            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Users</p>
                            </a>
                        </li>
                    @endpermission
                    @permission('role.manage')
                        <li class="nav-item">
                            <a href="{{ route('roles.index') }}" class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-user-shield"></i>
                                <p>Roles & Permissions</p>
                            </a>
                        </li>
                    @endpermission
                @endpermission

                @permission('settings.view')
                    <li class="nav-item">
                        <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>Pengaturan</p>
                        </a>
                    </li>
                @endpermission

                @permission('tenant.manage')
                    <li class="nav-header">Tenant</li>
                    <li class="nav-item">
                        <a href="{{ route('tenants.index') }}" class="nav-link {{ request()->routeIs('tenants.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-building"></i>
                            <p>Daftar Tenant</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('tenant-registrations.index') }}" class="nav-link {{ request()->routeIs('tenant-registrations.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-signature"></i>
                            <p>Pendaftaran Tenant</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('subscription-plans.index') }}" class="nav-link {{ request()->routeIs('subscription-plans.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-box"></i>
                            <p>Manajemen Paket</p>
                        </a>
                    </li>
                @endpermission

                @permission(['warranty.view', 'warranty.claim'])
                    <li class="nav-header">Garansi</li>
                    @permission('warranty.view')
                        <li class="nav-item">
                            <a href="{{ route('warranties.index') }}" class="nav-link {{ request()->routeIs('warranties.index') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-shield-alt"></i>
                                <p>Daftar Garansi</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('warranties.reminder') }}" class="nav-link {{ request()->routeIs('warranties.reminder') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-bell"></i>
                                <p>Reminder Garansi</p>
                            </a>
                        </li>
                    @endpermission
                    @permission('warranty.claim')
                        <li class="nav-item">
                            <a href="{{ route('warranties.create') }}" class="nav-link {{ request()->routeIs('warranties.create') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-clipboard-check"></i>
                                <p>Klaim Garansi</p>
                            </a>
                        </li>
                    @endpermission
                @endpermission

                @permission(['whatsapp.broadcast', 'whatsapp.template_manage', 'whatsapp.log_view'])
                    <li class="nav-header">WhatsApp</li>
                    @permission('whatsapp.broadcast')
                        <li class="nav-item">
                            <a href="{{ route('wa.broadcast') }}" class="nav-link {{ request()->routeIs('wa.broadcast') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-bullhorn"></i>
                                <p>Broadcast</p>
                            </a>
                        </li>
                    @endpermission
                    @permission('whatsapp.template_manage')
                        <li class="nav-item">
                            <a href="{{ route('wa-templates.index') }}" class="nav-link {{ request()->routeIs('wa-templates.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-puzzle-piece"></i>
                                <p>Template Pesan</p>
                            </a>
                        </li>
                    @endpermission
                    @permission('whatsapp.log_view')
                        <li class="nav-item">
                            <a href="{{ route('wa.logs') }}" class="nav-link {{ request()->routeIs('wa.logs') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-file-alt"></i>
                                <p>Log Pengiriman</p>
                            </a>
                        </li>
                    @endpermission
                @endpermission
            </ul>
        </nav>
    </div>
</aside>
