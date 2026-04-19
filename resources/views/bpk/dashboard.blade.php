@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Breadcrumbs --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-light rounded-3 px-3 py-2">
            <li class="breadcrumb-item">
                <a href="{{ route('bpk.dashboard') }}" class="text-decoration-none">
                    <i class="fas fa-home me-1"></i> Dashboard
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">BPK Audit</li>
        </ol>
    </nav>

    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <div class="d-flex align-items-center mb-2">
                <img src="https://upload.wikimedia.org/wikipedia/commons/d/d2/BPK_insignia.svg" 
                     alt="Logo BPK" 
                     class="me-3" 
                     style="width: 60px; height: 60px; object-fit: contain;">
                <div>
                    <h4 class="fw-bold text-dark mb-1"><i class="fas fa-chart-line me-2"></i>Dashboard Audit - BPK</h4>
                    <p class="text-muted small mb-0">Pemantauan real-time jejak digital berkas SPP/SPM Pemkab Bolmong.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="{{ route('bpk.monitoring') }}" class="btn btn-soft btn-primary btn-pill shadow-sm px-4">
                <i class="fas fa-search me-2"></i> Menu Audit
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-start-gold">
                <div class="card-body py-4 px-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="data-label">Total Berkas</span>
                            <h2 class="fw-bold mb-0">{{ $stats['total_berkas'] }}</h2>
                        </div>
                        <span class="text-primary fs-3"><i class="fas fa-file-invoice-dollar"></i></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-start-navy">
                <div class="card-body py-4 px-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="data-label">Sedang Diproses</span>
                            <h2 class="fw-bold mb-0">{{ $stats['pending'] + $stats['proses_validasi'] }}</h2>
                        </div>
                        <span class="text-primary fs-3"><i class="fas fa-spinner"></i></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-start-gold">
                <div class="card-body py-4 px-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="data-label">Selesai/Sah</span>
                            <h2 class="fw-bold mb-0">{{ $stats['disetujui'] }}</h2>
                        </div>
                        <span class="text-success fs-3"><i class="fas fa-check-circle"></i></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 border-start border-start-navy">
                <div class="card-body py-4 px-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="data-label">Perlu Atensi</span>
                            <h2 class="fw-bold mb-0">{{ $stats['ditolak'] }}</h2>
                        </div>
                        <span class="text-danger fs-3"><i class="fas fa-exclamation-triangle"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm border-start border-start-gold">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold text-primary">10 Berkas Terbaru Masuk</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">Dinas</th>
                            <th>Nomor Berkas</th>
                            <th>Nominal</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSubmissions as $s)
                        <tr>
                            <td class="ps-3 fw-bold text-dark">{{ $s->department->nama_dinas }}</td>
                            <td><code class="text-dark">{{ $s->nomor_spp_spm }}</code></td>
                            <td class="text-primary fw-bold">Rp {{ number_format($s->total_pencairan, 0, ',', '.') }}</td>
                            <td>
                                @if($s->status == 'disetujui')
                                    <span class="badge bg-success">Selesai</span>
                                @else
                                    <span class="badge bg-warning text-dark">Proses</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('bpk.show', $s->id) }}" class="btn btn-sm btn-outline-dark btn-pill shadow-sm">
                                    <i class="fas fa-search me-1"></i> Audit
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="text-center py-5">
                                    <i class="fas fa-box-open fa-3x mb-3 text-muted"></i>
                                    <p class="mb-0 text-muted">Belum ada berkas audit yang tersedia saat ini. Silakan periksa kembali nanti atau gunakan filter lain.</p>
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
@endsection