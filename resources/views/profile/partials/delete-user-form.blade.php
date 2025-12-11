<section class="rounded-lg border border-red-200 bg-red-50 p-6 shadow-sm">
    <header class="mb-4">
        <h2 class="text-lg font-semibold text-red-700">Hapus Akun</h2>
        <p class="text-sm text-red-600">Sekali dihapus, akun Anda tidak dapat dipulihkan.</p>
    </header>

    <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4">
        @csrf
        @method('delete')

        <div>
            <x-input-label for="password" value="Konfirmasi password" />
            <x-text-input id="password" name="password" type="password" class="block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->userDeletion->get('password') ?? []" class="mt-2" />
        </div>

        <x-primary-button class="bg-red-600 hover:bg-red-700 focus-visible:outline-red-600">Hapus Akun</x-primary-button>
    </form>
</section>
