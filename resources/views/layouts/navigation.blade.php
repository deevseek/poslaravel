<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button">
                <i class="fas fa-bars"></i>
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                Dashboard
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                Customers
            </a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                Suppliers
            </a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#" role="button">
                <i class="far fa-user"></i>
                <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <div class="dropdown-item-text">
                    <div class="d-flex flex-column">
                        <strong>{{ auth()->user()->name }}</strong>
                        <span class="text-muted text-sm">{{ auth()->user()->email }}</span>
                    </div>
                </div>
                <div class="dropdown-divider"></div>
                <div class="px-3">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-block btn-outline-danger btn-sm">
                            <i class="fas fa-sign-out-alt mr-1"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </li>
    </ul>
</nav>
