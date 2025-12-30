<x-guest-layout>
    <p class="login-box-msg">Lupa password?</p>
    <p class="text-sm text-muted">
        Masukkan email terdaftar dan kami akan mengirimkan tautan untuk mengatur ulang password Anda.
    </p>

    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="input-group mb-3">
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus placeholder="Email" />
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
        </div>
        <x-input-error :messages="$errors->get('email')" class="mb-2" />

        <x-primary-button class="btn-block">
            Kirim tautan reset
        </x-primary-button>
    </form>
</x-guest-layout>
