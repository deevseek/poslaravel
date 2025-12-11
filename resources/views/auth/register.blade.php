<x-guest-layout>
    <div class="space-y-2 text-center">
        <h2 class="text-xl font-semibold text-gray-900">Buat akun baru</h2>
        <p class="text-sm text-gray-600">Daftar untuk mulai menggunakan POS dealer komputer & layanan service.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <div>
            <x-input-label for="name" value="Nama" />
            <x-text-input id="name" class="block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Password" />
            <x-text-input id="password" class="block w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Konfirmasi Password" />
            <x-text-input id="password_confirmation" class="block w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="space-y-3">
            <x-primary-button class="w-full justify-center">Daftar</x-primary-button>
            <p class="text-sm text-center text-gray-600">Sudah punya akun? <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Masuk sekarang</a></p>
        </div>
    </form>
</x-guest-layout>
