<section class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
    <header class="mb-4">
        <h2 class="text-lg font-semibold text-gray-900">Informasi Profil</h2>
        <p class="text-sm text-gray-600">Perbarui nama dan email akun Anda.</p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" value="Nama" />
            <x-text-input id="name" name="name" type="text" class="block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" name="email" type="email" class="block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 text-sm text-gray-600">
                    Email Anda belum diverifikasi.
                    <x-primary-button type="submit" form="send-verification" class="mt-2">
                        Kirim ulang email verifikasi
                    </x-primary-button>
                </div>
                @if (session('status') === 'verification-link-sent')
                    <p class="mt-2 text-sm text-green-600">Tautan verifikasi baru telah dikirim.</p>
                @endif
            @endif
        </div>

        <div class="flex items-center gap-3">
            <x-primary-button>Simpan</x-primary-button>
            @if (session('status') === 'profile-updated')
                <p class="text-sm text-green-600">Perubahan tersimpan.</p>
            @endif
        </div>
    </form>

    <form id="send-verification" method="POST" action="{{ route('verification.send') }}">
        @csrf
    </form>
</section>
