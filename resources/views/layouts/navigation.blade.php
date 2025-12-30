<nav class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-slate-200">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
            <div class="flex items-center gap-3 md:hidden">
                <x-application-logo class="h-10 w-10" />
                <div class="leading-tight">
                    <p class="text-sm text-slate-500">POS Dealer</p>
                    <p class="text-lg font-semibold text-slate-900">Komputer & Service</p>
                </div>
            </div>

            <div class="flex items-center">
                <div class="hidden space-x-8 md:-my-px md:ml-10 md:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link>
                    <x-nav-link :href="route('customers.index')" :active="request()->routeIs('customers.*')">
                        Customers
                    </x-nav-link>
                    <x-nav-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')">
                        Suppliers
                    </x-nav-link>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-sm font-semibold text-slate-900">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-slate-500">{{ auth()->user()->email }}</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-secondary-button type="submit">Logout</x-secondary-button>
                </form>
            </div>
        </div>
    </div>
</nav>
