<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Ini adalah area aman aplikasi. Harap konfirmasi kata sandi Anda sebelum melanjutkan.
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
        @csrf

        <div>
            <x-input-label for="password" value="Password" />
            <x-text-input id="password" class="block w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->confirmPassword->get('password') ?? []" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center">
            Konfirmasi
        </x-primary-button>
    </form>
</x-guest-layout>
