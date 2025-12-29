<x-app-layout title="Kelola Tenant">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Kelola Tenant</h1>
        <p class="text-gray-600">Perbarui informasi dasar tenant dan status langganannya.</p>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        <form action="{{ route('tenants.update', $tenant) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="name">Nama Tenant</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $tenant->name) }}" required
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Subdomain</label>
                    <div class="mt-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-600">
                        {{ $tenant->subdomain }}.{{ config('app.domain', 'contoh.com') }}
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Subdomain tidak dapat diubah setelah provisioning.</p>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700" for="plan_id">Paket Langganan</label>
                    <select id="plan_id" name="plan_id"
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tanpa paket</option>
                        @foreach ($plans as $plan)
                            <option value="{{ $plan->id }}" @selected(old('plan_id', $tenant->plan_id) == $plan->id)>
                                {{ $plan->name }} - {{ number_format($plan->price, 0, ',', '.') }}/{{ $plan->billing_cycle }}
                            </option>
                        @endforeach
                    </select>
                    @error('plan_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="status">Status</label>
                    <select id="status" name="status" required
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="active" @selected(old('status', $tenant->status) === 'active')>Aktif</option>
                        <option value="suspended" @selected(old('status', $tenant->status) === 'suspended')>Ditangguhkan</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="subscription_end_date">Berakhir Pada</label>
                    <input type="date" id="subscription_end_date" name="subscription_end_date"
                        value="{{ old('subscription_end_date', $tenant->latestSubscription?->end_date?->format('Y-m-d')) }}"
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('subscription_end_date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Perpanjang Otomatis</label>
                    <div class="mt-3 flex items-center gap-2">
                        <input type="checkbox" id="auto_renew" name="auto_renew" value="1"
                            @checked(old('auto_renew', $tenant->latestSubscription?->auto_renew) == true)
                            class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="auto_renew" class="text-sm text-gray-600">Aktifkan perpanjangan saat masa habis</label>
                    </div>
                    @error('auto_renew')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Database</label>
                    <div class="mt-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-600">
                        {{ $tenant->database_name }}
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tanggal dibuat</label>
                    <div class="mt-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-600">
                        {{ $tenant->created_at?->format('d M Y H:i') }}
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('tenants.index') }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Kembali</a>
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</x-app-layout>
