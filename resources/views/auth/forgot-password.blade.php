<x-guest-layout>
    <div class="text-sm text-gray-600">
        Lupa password? Masukkan email Anda untuk mendapatkan tautan reset password.
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
