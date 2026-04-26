@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-3">
    {{-- Top Header Section --}}
       <div class="card border-0 shadow-sm bg-light bg-opacity-25 rounded-4 p-4 mb-3 border-start border-primary border-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        {{-- Sisi Kiri: Judul & Deskripsi --}}
        <div class="header-title">
            <h3 class="fw-bold text-dark tracking-tight mb-1">
                <span class="text-primary border-start border-4 border-primary ps-3 me-2">Arsip Dokumen Juara</span>
            </h3>
            <p class="text-muted small mb-0">Kelola dan telusuri riwayat dokumen yang telah disahkan secara sistematis.</p>
        </div>

        @hasanyrole('super_admin|kasie_bud|kabid_perbendaharaan')
        <div class="header-actions">
            <a href="{{ route('history.export', request()->all()) }}" class="btn btn-outline-success rounded-pill px-3 shadow-sm me-2">
                <i class="fas fa-file-excel me-1"></i> Excel
            </a>
            <a href="{{ route('history.print', request()->all()) }}" target="_blank" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fas fa-print me-1"></i> Cetak Laporan
            </a>
        </div>
        @endhasanyrole
    </div>
</div>

    <!-- {{-- Stats Mini Cards (Fitur Baru untuk Insight) --}}
    <div class="row mb-4 g-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-bottom border-primary border-3">
                <div class="small text-muted mb-1">Total Nilai Arsip</div>
                <div class="h5 fw-bold mb-0 text-dark">Rp {{ number_format($histories->sum('total_pencairan'), 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white border-bottom border-success border-3">
                <div class="small text-muted mb-1">Dokumen Bulan Ini</div>
                <div class="h5 fw-bold mb-0 text-dark">{{ $histories->count() }} Berkas</div>
            </div>
        </div>
    </div> -->

    {{-- Advanced Filter Bar --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-4">
            <form action="{{ route('history.index') }}" method="GET" class="row align-items-end g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted uppercase">Pencarian Cepat</label>
                    <div class="input-group search-group">
                        <span class="input-group-text border-0 bg-light"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-0 bg-light rounded-end" 
                               placeholder="Cari No. SPP, Dinas, atau Kegiatan..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted uppercase">Filter Tanggal</label>
                    <input type="date" name="date" class="form-control border-0 bg-light" value="{{ request('date') }}">
                </div>
                <div class="col-md-5 d-flex gap-2">
                    <button type="submit" class="btn btn-dark px-4 rounded-pill flex-grow-1 shadow-sm">
                        <i class="fas fa-sliders-h me-2"></i> Terapkan Filter
                    </button>
                    <a href="{{ route('history.index') }}" class="btn btn-light px-4 rounded-pill border shadow-xs text-muted">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-dark text-white small uppercase">
                        <tr>
                            <th class="ps-4 py-3 border-0">Instansi & Nama Kegiatan</th>
                            <th class="py-3 border-0">Identitas Dokumen</th>
                            <th class="py-3 border-0">Waktu Pengesahan</th>
                            <th class="py-3 border-0">Nilai Pencairan</th>
                            <th class="py-3 border-0 text-center">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($histories as $h)
                        <tr>
                            <td class="ps-4 py-4">
                                <div class="d-flex align-items-center">
                                    <div class="dept-avatar bg-primary bg-opacity-10 text-primary me-3 fw-bold rounded-3">
                                        {{ substr($h->department->nama_dinas, 0, 1) }}
                                    </div>
                                    <div style="max-width: 250px;">
                                        <div class="fw-bold text-dark lh-sm mb-1">{{ $h->department->nama_dinas }}</div>
                                        <div class="text-muted small text-truncate" title="{{ $h->judul_kegiatan }}">
                                            {{ $h->judul_kegiatan }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="font-monospace text-primary fw-bold small">#{{ $h->nomor_spp_spm }}</div>
                                <span class="badge bg-secondary-subtle text-secondary extra-small border border-secondary-subtle">DOKUMEN RESMI</span>
                            </td>
                            <td>
                                <div class="text-dark fw-semibold small">{{ $h->updated_at->isoFormat('D MMMM Y') }}</div>
                                <div class="text-muted extra-small"><i class="far fa-clock me-1"></i> Pukul {{ $h->updated_at->format('H:i') }}</div>
                            </td>
                            <td>
                                <div class="text-success fw-bold">
                                    Rp {{ number_format($h->total_pencairan, 0, ',', '.') }}
                                </div>
                                <div class="extra-small text-muted"><i class="fas fa-shield-alt text-success me-1"></i>Telah Terverifikasi</div>
                            </td>
                            <td class="text-center pe-4">
                                <a href="{{ route('history.show', $h->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                    <i class="fas fa-eye me-1"></i> Rincian
                                </a>
                            </td>
                        </tr>
                        @empty
                        {{-- (Bagian @empty tetap seperti kode Anda, tapi buat lebih estetik) --}}
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($histories->hasPages())
        <div class="card-footer bg-white border-0 py-3 text-center">
            {{ $histories->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    :root {
        --bs-primary: #2563eb; /* Biru Royal Profesional */
        --bs-dark: #1e293b;    /* Slate Dark */
    }

    body { background-color: #f8fafc; }
    
    .tracking-tight { letter-spacing: -0.025em; }
    .extra-small { font-size: 0.7rem; }
    .uppercase { text-transform: uppercase; letter-spacing: 0.1em; }
    .rounded-4 { border-radius: 1rem; }
    
    /* Custom Search Input */
    .search-group .form-control:focus {
        background-color: #fff !important;
        box-shadow: none;
        border: 1px solid var(--bs-primary) !important;
    }

    /* Department Avatar Style */
    .dept-avatar {
        width: 42px;
        height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Table Elevation Effect */
    .table tbody tr:hover {
        background-color: #f1f5f9;
        transition: all 0.2s ease;
    }

    .table thead th {
        font-weight: 600;
        letter-spacing: 0.05rem;
    }

    .btn-white {
        background: #fff;
        border: 1px solid #e2e8f0;
    }

    .btn-white:hover {
        background: #f8fafc;
    }
</style>
@endsection