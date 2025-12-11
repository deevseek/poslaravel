<x-app-layout title="Verifikasi Email">
    <div class="max-w-2xl space-y-6">
        <h1 class="text-2xl font-semibold text-gray-900">Verifikasi alamat email</h1>
        <p class="text-gray-700">Sebelum lanjut, kami telah mengirimkan tautan verifikasi ke email Anda. Jika belum menerima, silakan kirim ulang.</p>

        @if (session('status') === 'verification-link-sent')
            <div class="rounded-md bg-green-100 px-4 py-3 text-sm text-green-800">
                Tautan verifikasi baru telah dikirim ke email Anda.
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}" class="flex items-center gap-3">
            @csrf
            <x-primary-button>Kirim ulang email verifikasi</x-primary-button>
        </form>
    </div>
</x-app-layout>
