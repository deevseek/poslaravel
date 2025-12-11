<x-app-layout title="Dashboard">
    <div class="grid gap-6 lg:grid-cols-3">
        <section class="lg:col-span-2 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Selamat datang</p>
                    <h1 class="text-2xl font-semibold text-gray-900">{{ auth()->user()->name }}</h1>
                    <p class="mt-2 text-gray-600">Pantau aktivitas POS dealer komputer & layanan service.</p>
                </div>
                <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-2xl">💻</span>
            </div>
        </section>

        <section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm space-y-3">
            <h2 class="text-lg font-semibold text-gray-900">Status Akses</h2>
            <div class="space-y-2 text-sm text-gray-700">
                <div class="flex items-center justify-between">
                    <span>Roles</span>
                    <span class="rounded-full bg-blue-50 px-3 py-1 text-blue-700">{{ auth()->user()->roles->count() }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span>Permissions</span>
                    <span class="rounded-full bg-green-50 px-3 py-1 text-green-700">{{ auth()->user()->allPermissions()->count() }}</span>
                </div>
            </div>
        </section>

        <section class="lg:col-span-3 rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900">Informasi cepat</h2>
            <p class="mt-2 text-gray-600">Gunakan menu di sidebar untuk mengelola master data, pengguna, dan akses.</p>
        </section>
    </div>
</x-app-layout>
