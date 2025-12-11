<x-guest-layout>
    <div class="space-y-2 text-center">
        <h2 class="text-xl font-semibold text-gray-900">Masuk ke akun Anda</h2>
        <p class="text-sm text-gray-600">Gunakan email dan password terdaftar untuk mengakses dashboard.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Password" />
            <x-text-input id="password" class="block w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between text-sm">
            <label class="inline-flex items-center gap-2">
                <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                <span class="text-gray-700">Ingat saya</span>
            </label>

            <a class="text-blue-600 hover:underline" href="{{ route('password.request') }}">Lupa password?</a>
        </div>

        <div class="space-y-3">
            <x-primary-button class="w-full justify-center">Masuk</x-primary-button>
            <p class="text-sm text-center text-gray-600">Belum punya akun? <a href="{{ route('register') }}" class="text-blue-600 hover:underline">Daftar sekarang</a></p>
        </div>
    </form>
</x-guest-layout>
