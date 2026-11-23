{{-- View: admin/settings/logs --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 text-white">
    <div class="glass-header-dark rounded-4 p-4 mb-3 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold mb-1"><i class="fas fa-clock-rotate-left me-2 text-info"></i>Riwayat Master Settings</h3>
            <p class="text-white-50 mb-0">Catatan perubahan konfigurasi aplikasi.</p>
        </div>
        <a href="{{ route('settings.index') }}" class="btn btn-outline-light rounded-pill"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
    </div>
    <div class="card glass-card-dark border-0 rounded-4 overflow-hidden">
        @if($logReadFailed)
            <div class="alert alert-warning rounded-0 mb-0" role="alert">
                Riwayat perubahan gagal dibaca. Data yang tampil belum tentu mencakup seluruh riwayat.
            </div>
        @endif
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead><tr><th class="ps-4">Waktu</th><th>Pengguna</th><th>Pengaturan</th><th>Nilai Sebelumnya</th><th>Nilai Baru</th></tr></thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="ps-4 text-nowrap">{{ $log->changed_at->format('d M Y H:i:s') }}</td>
                            <td>{{ $log->user?->name ?? 'Pengguna dihapus' }}</td>
                            <td><code>{{ $log->setting_key }}</code></td>
                            <td class="text-break">{{ $log->old_value ?: '—' }}</td>
                            <td class="text-break">{{ $log->new_value ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-white-50 py-4">Belum ada riwayat perubahan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())<div class="p-3">{{ $logs->links() }}</div>@endif
    </div>
</div>
@endsection
