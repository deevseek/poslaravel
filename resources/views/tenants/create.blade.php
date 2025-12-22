<x-app-layout title="Tambah Tenant">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Tambah Tenant</h1>
        <p class="text-gray-600">Lengkapi informasi tenant baru dan admin pertamanya.</p>
    </div>

    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
        @if ($errors->has('general'))
            <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">{{ $errors->first('general') }}</div>
        @endif

        <form action="{{ route('tenants.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="name">Nama Tenant</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="subdomain">Subdomain</label>
                    <div class="mt-2 flex items-center gap-2">
                        <input type="text" id="subdomain" name="subdomain" value="{{ old('subdomain') }}" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <span class="text-sm text-gray-500">.{{ config('app.domain', 'contoh.com') }}</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Subdomain akan digunakan sebagai identitas database tenant.</p>
                    @error('subdomain')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="plan_id">Paket Langganan</label>
                    <select id="plan_id" name="plan_id"
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Pilih paket (opsional)</option>
                        @foreach ($plans as $plan)
                            <option value="{{ $plan->id }}" @selected(old('plan_id') == $plan->id)>
                                {{ $plan->name }} - {{ number_format($plan->price, 0, ',', '.') }}/{{ $plan->billing_cycle }}
                            </option>
                        @endforeach
                    </select>
                    @error('plan_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="admin_name">Nama Admin</label>
                    <input type="text" id="admin_name" name="admin_name" value="{{ old('admin_name') }}"
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <p class="mt-1 text-xs text-gray-500">Opsional, akan digunakan saat membuat akun admin awal.</p>
                    @error('admin_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="email">Email Admin</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="password">Kata Sandi</label>
                    <input type="password" id="password" name="password" required
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700" for="password_confirmation">Konfirmasi Kata Sandi</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="mt-2 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div class="flex items-end justify-end gap-3">
                    <a href="{{ route('tenants.index') }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</a>
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-blue-700">Simpan</button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
