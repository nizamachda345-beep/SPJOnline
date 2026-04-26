@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-2 align-items-end">
        <div class="col-md-8">
            <h4 class="fw-bold text-slate-800 mb-1" style="letter-spacing: -0.5px;">Verifikasi Berkas</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.75rem;">
                    <li class="breadcrumb-item text-muted"><i class="fas fa-layer-group me-1"></i> Panel Kasie BUD</li>
                    <li class="breadcrumb-item active fw-medium text-primary">Daftar Verifikasi</li>
                </ol>
            </nav>
        </div>
        <div class="col-md-4 text-md-end">
            <div class="d-inline-flex align-items-center bg-white border border-slate-200 px-3 py-2 rounded-3 shadow-sm">
                <div class="me-3">
                    <div class="text-muted mb-0" style="font-size: 0.5rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Total Antrean</div>
                    <div class="fw-bold text-dark" style="font-size: 0.6rem; line-height: 1;">{{ $submissions->count() }} Dokumen</div>
                </div>
                <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                    <i class="fas fa-inbox small"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                {{-- text-nowrap memastikan teks tidak turun ke bawah --}}
                <table class="table table-hover align-middle mb-0 text-nowrap">
                    <thead class="bg-light border-bottom">
                        <tr class="text-muted" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px;">
                            <th class="ps-3 py-3 border-0 text-center">No</th>
                            <th class="py-3 border-0 text-center">SKPD / Instansi</th>
                            <th class="py-3 border-0 text-center">No. SPP</th>
                            <th class="py-3 border-0 text-center">Kat</th>
                            <th class="py-3 border-0 text-center">Total Pencairan</th>
                            <th class="py-3 border-0 text-center">Tanggal Masuk</th>
                            <th class="py-3 border-0 text-center">Status</th>
                            <th class="pe-4 border-0 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $index => $s)
                        <tr>
                            <td class="ps-3 text-muted small text-center">{{ $index + 1 }}</td>
                            <td class="fw-bold text-dark text-center">{{ $s->department->nama_dinas }}</td>
                            <td class="text-center">
                                <span class="badge bg-white text-dark border fw-medium px-2 py-1">
                                    {{ $s->nomor_spp_spm }}
                                </span>
                            </td>
                            <td class="text-center fw-bold">{{ $s->jenis_pencairan }}</td>
                            <td class="fw-bold text-primary text-center">
                                <span class="small fw-medium text-muted"> Rp</span> {{ number_format($s->total_pencairan, 0, ',', '.') }}
                            </td>
                            <td class="text-center">
                                <div class="small fw-medium">{{ $s->created_at->format('d M Y') }}</div>
                                <div class="text-muted" style="font-size: 0.65rem;">{{ $s->created_at->format('H:i') }} WIB</div>
                            </td>
                            <td class="text-center">
                                @if($s->status == 'pending')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-1 rounded-pill fw-bold" style="font-size: 0.7rem;">
                                        <i class="fas fa-spinner fa-spin me-1"></i> VERIFIKASI
                                    </span>
                                @elseif($s->status == 'direvisi')
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-1 rounded-pill fw-bold" style="font-size: 0.7rem;">
                                        <i class="fas fa-sync-alt fa-spin me-1"></i> DIREVISI
                                    </span>
                                @elseif($s->status == 'revisi')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 rounded-pill fw-bold" style="font-size: 0.7rem;">
                                        <i class="fas fa-sync-alt fa-spin me-1"></i> REVISI
                                    </span>
                                @elseif($s->status == 'proses validasi')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill fw-bold" style="font-size: 0.7rem;">
                                        VALIDASI
                                    </span>
                                @elseif($s->status == 'disetujui')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 rounded-pill fw-bold" style="font-size: 0.7rem;">
                                        DISETUJUI
                                    </span>
                                @endif
                            </td>
                            <td class="pe-4 text-center" style="width: 60px;">
                                <a href="{{ route('verifikasi.show', $s->id) }}" 
                                class="btn btn-primary btn-sm rounded-circle d-inline-flex align-items-center justify-content-center shadow-sm transition-all action-btn"
                                style="width: 32px; height: 32px; border: none;"
                                data-bs-toggle="tooltip" 
                                title="Lihat Detail">
                                    <i class="fas fa-search" style="font-size: 0.75rem;"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="py-4">
                                    <i class="fas fa-folder-open fa-3x text-light mb-3"></i>
                                    <h6 class="text-muted fw-normal">Tidak ada antrean verifikasi saat ini.</h6>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* Menghindari pembungkusan teks secara global di tabel */
    .table td, .table th {
        white-space: nowrap;
        vertical-align: middle;
    }

    .bg-primary-subtle { background-color: #eef2ff !important; }
    .bg-warning-subtle { background-color: #fffbeb !important; }
    .bg-danger-subtle { background-color: #fef2f2 !important; }
    .bg-info-subtle { background-color: #ecfeff !important; }
    .bg-success-subtle { background-color: #f0fdf4 !important; }

    .action-btn {
        font-size: 0.75rem;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
    }

    .table-hover tbody tr:hover {
        background-color: #f8fafc;
    }
    .action-btn {
    transition: all 0.2s ease-in-out;
}
.action-btn:hover {
    transform: translateY(-1px);
    filter: brightness(1.1);
}
</style>
@endsection