@extends('layouts.app')

@section('header')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
        <div>
            <h1 class="h3 mb-1">Pendaftaran Tenant</h1>
            <p class="mb-0 text-muted">Verifikasi pendaftaran dan aktifkan tenant baru.</p>
        </div>
    </div>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->has('general'))
        <div class="alert alert-danger">{{ $errors->first('general') }}</div>
    @endif

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Nama Usaha</th>
                    <th>Subdomain</th>
                    <th>Paket</th>
                    <th>Status</th>
                    <th>Pembayaran</th>
                    <th>Kontak</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($registrations as $registration)
                    <tr>
                        <td>
                            <strong>{{ $registration->name }}</strong>
                            <div class="text-muted text-sm">Admin: {{ $registration->admin_name ?? '-' }}</div>
                        </td>
                        <td>{{ $registration->subdomain }}</td>
                        <td>{{ $registration->plan?->name ?? '-' }}</td>
                        <td>
                            <span class="badge badge-{{ $registration->status === 'pending' ? 'warning' : ($registration->status === 'approved' ? 'success' : 'danger') }}">
                                {{ ucfirst($registration->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="text-sm">{{ strtoupper($registration->payment_method) }}</div>
                            <div class="text-muted text-sm">Rp {{ number_format($registration->payment_amount, 0, ',', '.') }}</div>
                            @if ($registration->payment_reference)
                                <div class="text-muted text-sm">Ref: {{ $registration->payment_reference }}</div>
                            @endif
                            @if ($registration->paymentProofUrl())
                                <a href="{{ $registration->paymentProofUrl() }}" target="_blank" class="text-primary text-sm">Lihat Bukti</a>
                            @endif
                        </td>
                        <td>
                            <div class="text-sm">{{ $registration->email }}</div>
                            <div class="text-muted text-sm">{{ $registration->phone ?? '-' }}</div>
                        </td>
                        <td class="text-right">
                            @if ($registration->status === 'pending')
                                <form method="POST" action="{{ route('tenant-registrations.approve', $registration) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">Aktifkan</button>
                                </form>
                                <button type="button" class="btn btn-outline-danger btn-sm" data-toggle="modal" data-target="#rejectModal{{ $registration->id }}">Tolak</button>

                                <div class="modal fade" id="rejectModal{{ $registration->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Tolak Pendaftaran</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form method="POST" action="{{ route('tenant-registrations.reject', $registration) }}">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="admin_note_{{ $registration->id }}">Catatan (opsional)</label>
                                                        <textarea id="admin_note_{{ $registration->id }}" name="admin_note" class="form-control" rows="3"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-danger">Tolak</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">Belum ada pendaftaran.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $registrations->links() }}
    </div>
@endsection
