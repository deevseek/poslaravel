<x-guest-layout>
    <p class="login-box-msg">Masuk ke akun Anda</p>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <x-auth-session-status class="mb-3" :status="session('status')" />

        <div class="input-group mb-3">
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="Email" />
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope"></span>
                </div>
            </div>
        </div>
        <x-input-error :messages="$errors->get('email')" class="mb-2" />

        <div class="input-group mb-3">
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Password" />
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>
        <x-input-error :messages="$errors->get('password')" class="mb-2" />

        <div class="row align-items-center mb-3">
            <div class="col-7">
                <div class="form-check">
                    <input type="checkbox" name="remember" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember">Ingat saya</label>
                </div>
            </div>
            <div class="col-5 text-right">
                <a class="text-sm" href="{{ route('password.request') }}">Lupa password?</a>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <x-primary-button class="btn-block">Masuk</x-primary-button>
            </div>
        </div>

        <p class="mt-3 mb-0 text-center text-sm">
            Belum punya akun?
            <a href="{{ route('register') }}">Daftar sekarang</a>
        </p>
    </form>
</x-guest-layout>
