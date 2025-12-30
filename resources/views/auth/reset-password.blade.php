<x-guest-layout>
    <p class="login-box-msg">Atur ulang password</p>
    <p class="text-sm text-muted">
        Masukkan email dan password baru untuk mengamankan akun Anda.
    </p>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="input-group mb-3">
            <x-text-input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" placeholder="Email" />
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
        </div>
        <x-input-error :messages="$errors->get('email')" class="mb-2" />

        <div class="input-group mb-3">
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Password Baru" />
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>
        <x-input-error :messages="$errors->get('password')" class="mb-2" />

        <div class="input-group mb-3">
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Konfirmasi Password" />
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>
        <x-input-error :messages="$errors->get('password_confirmation')" class="mb-2" />

        <x-primary-button class="btn-block">
            Reset password
        </x-primary-button>
    </form>
</x-guest-layout>
