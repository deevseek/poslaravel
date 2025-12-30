<x-guest-layout>
    <p class="login-box-msg">Buat akun baru</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="input-group mb-3">
            <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Nama" />
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user"></span>
                </div>
            </div>
        </div>
        <x-input-error :messages="$errors->get('name')" class="mb-2" />

        <div class="input-group mb-3">
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="Email" />
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
        </div>
        <x-input-error :messages="$errors->get('email')" class="mb-2" />

        <div class="input-group mb-3">
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Password" />
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

        <div class="row">
            <div class="col-12">
                <x-primary-button class="btn-block">Daftar</x-primary-button>
            </div>
        </div>

        <p class="mt-3 mb-0 text-center text-sm">
            Sudah punya akun?
            <a href="{{ route('login') }}">Masuk sekarang</a>
        </p>
    </form>
</x-guest-layout>
