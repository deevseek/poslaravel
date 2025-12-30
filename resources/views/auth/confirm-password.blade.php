<x-guest-layout>
    <p class="login-box-msg">Konfirmasi Password</p>
    <p class="text-sm text-muted">
        Ini adalah area aman aplikasi. Harap konfirmasi kata sandi Anda sebelum melanjutkan.
    </p>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="input-group mb-3">
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Password" />
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>
        <x-input-error :messages="$errors->confirmPassword->get('password') ?? []" class="mb-2" />

        <x-primary-button class="btn-block">
            Konfirmasi
        </x-primary-button>
    </form>
</x-guest-layout>
