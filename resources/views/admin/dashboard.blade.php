@extends('layouts.app')

@section('content')
<style>
.card-hover {
    transition: all 0.2s ease-in-out;
}
.card-hover:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}
.btn-transition {
    transition: all 0.2s ease-in-out;
}
.text-slate {
    color: #6c757d !important; /* Slate color */
}
.bg-navy {
    background-color: #003366 !important; /* Deep Navy */
}
</style>
<div class="container-fluid">
    {{-- Breadcrumbs --}}
    <!-- <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-light rounded-3 px-3 py-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">
                    <i class="fas fa-home me-1"></i> Dashboard
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Admin</li>
        </ol>
    </nav> -->

<div class="mb-4">
    <div class="d-flex align-items-center justify-content-between w-100 bg-light shadow-sm bg-opacity-25 rounded p-3 border border-primary-subtle">
        {{-- Sisi Kiri: Judul & Deskripsi --}}
        <div class="d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 p-3 rounded-3 d-none d-md-block">
                <i class="fas fa-tachometer-alt text-primary fs-4"></i>
            </div>
            <div>
                <h4 class="fw-bold text-uppercase mb-0 text-primary" style="letter-spacing: -0.5px;">Dashboard</h4>
                <p class="text-muted small mb-0">Ringkasan sistem iDJ Kabupaten Bolaang Mongondow.</p>
                {{-- Info Waktu WITA --}}
                <div class="text-muted mt-1" style="font-size: 0.75rem;">
                    <i class="far fa-clock me-1 text-primary-emphasis"></i>
                    <span id="liveDateTimeWITA" class="fw-medium text-uppercase">Memuat Waktu WITA...</span>
                </div>
            </div>
        </div>

        {{-- Sisi Kanan: Tombol Monitoring --}}
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.monitoring') }}" class="btn btn-primary rounded-pill shadow-sm px-4 py-2 fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                <i class="fas fa-search me-2"></i> Monitoring Global
            </a>
        </div>
    </div>
</div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3 mb-3">
        <div class="col">
            <div class="card border-0 shadow-sm rounded-4 card-hover">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between gap-3">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold">Total Berkas</span>
                            <h2 class="text-dark fw-semibold mb-0">{{ $stats['total_berkas'] ?? 0 }}</h2>
                        </div>
                        <span class="text-warning fs-3"><i class="fas fa-file-invoice-dollar"></i></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card border-0 shadow-sm rounded-4 card-hover">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between gap-3">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold">Dalam Proses</span>
                            <h2 class="text-dark fw-semibold mb-0">{{ $stats['pending'] ?? 0 }}</h2>
                        </div>
                        <span class="text-primary fs-3"><i class="fas fa-hourglass-half"></i></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card border-0 shadow-sm rounded-4 card-hover">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between gap-3">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold">Total User</span>
                            <h2 class="text-dark fw-semibold mb-0">{{ $stats['user_total'] ?? 0 }}</h2>
                        </div>
                        <span class="text-success fs-3"><i class="fas fa-users"></i></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card border-0 shadow-sm rounded-4 card-hover">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between gap-3">
                        <div>
                            <span class="text-muted small text-uppercase fw-bold">SKPD/Dinas</span>
                            <h2 class="text-dark fw-semibold mb-0">{{ $stats['dinas'] ?? 0 }}</h2>
                        </div>
                        <span class="text-info fs-3"><i class="fas fa-building"></i></span>
                    </div>
                </div>
            </div>
        </div>

        
    </div>
    @include('components.monthly-chart')
</div>
@endsection