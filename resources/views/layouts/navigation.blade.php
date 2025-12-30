<nav class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/80 shadow-sm backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between gap-6">
            <div class="flex items-center gap-3 md:hidden">
                <x-application-logo class="h-10 w-10" />
                <div class="leading-tight">
                    <p class="text-xs uppercase tracking-wide text-slate-400">POS Dealer</p>
                    <p class="text-lg font-semibold text-slate-900">Komputer & Service</p>
                </div>
            </div>

            <div class="flex flex-1 items-center gap-6">
                <div class="hidden space-x-6 md:flex">
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

            <div class="flex items-center gap-4">
                <div class="hidden text-right sm:block">
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
