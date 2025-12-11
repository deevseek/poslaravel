<x-guest-layout>
    <div class="space-y-2">
        <h2 class="text-xl font-semibold text-gray-900">Lupa password?</h2>
        <p class="text-sm text-gray-600">Masukkan email terdaftar dan kami akan mengirimkan tautan untuk mengatur ulang password Anda.</p>

        <x-auth-session-status class="text-green-700" :status="session('status')" />
    </div>

    <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-6">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center">
            Kirim tautan reset
        </x-primary-button>
    </form>
</x-guest-layout>
