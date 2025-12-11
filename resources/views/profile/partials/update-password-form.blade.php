<section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
    <header class="mb-4">
        <h2 class="text-lg font-semibold text-gray-900">Ubah Password</h2>
        <p class="text-sm text-gray-600">Pastikan password yang kuat untuk melindungi akun Anda.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        @method('put')

        <div>
            <x-input-label for="current_password" value="Password sekarang" />
            <x-text-input id="current_password" name="current_password" type="password" class="block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password') ?? []" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Password baru" />
            <x-text-input id="password" name="password" type="password" class="block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password') ?? []" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Konfirmasi password" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation') ?? []" class="mt-2" />
        </div>

        <div class="flex items-center gap-3">
            <x-primary-button>Update</x-primary-button>
            @if (session('status') === 'password-updated')
                <p class="text-sm text-green-600">Password diperbarui.</p>
            @endif
        </div>
    </form>
</section>
