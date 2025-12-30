<x-app-layout title="Verifikasi Email">
    <div class="mw-100">
        <h1 class="h4 font-weight-bold mb-3">Verifikasi alamat email</h1>
        <p class="text-muted">Sebelum lanjut, kami telah mengirimkan tautan verifikasi ke email Anda. Jika belum menerima, silakan kirim ulang.</p>

        @if (session('status') === 'verification-link-sent')
            <div class="alert alert-success">
                Tautan verifikasi baru telah dikirim ke email Anda.
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}" class="d-flex align-items-center">
            @csrf
            <x-primary-button>Kirim ulang email verifikasi</x-primary-button>
        </form>
    </div>
</x-app-layout>
