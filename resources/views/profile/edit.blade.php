<x-app-layout title="Profil">
    <div class="max-w-4xl space-y-8">
        <header>
            <p class="text-sm font-semibold text-blue-600">Profil</p>
            <h1 class="mt-1 text-2xl font-semibold text-gray-900">Pengaturan akun</h1>
            <p class="text-gray-600">Kelola informasi profil, email, dan keamanan akun Anda.</p>
        </header>

        <div class="grid gap-6 lg:grid-cols-2">
            @include('profile.partials.update-profile-information-form')
            @include('profile.partials.update-password-form')
        </div>

        @include('profile.partials.delete-user-form')
    </div>
</x-app-layout>
