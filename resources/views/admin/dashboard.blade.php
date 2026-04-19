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
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-light rounded-3 px-3 py-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">
                    <i class="fas fa-home me-1"></i> Dashboard
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Admin</li>
        </ol>
    </nav>

    <div class="row pt-2 mb-2 align-items-center">
        <div class="col-md-8">
            <h4 class="fw-bold text-uppercase mb-1"><i class="fas fa-tachometer-alt me-2 text-primary"></i>Dashboard</h4>
            <p class="text-slate small mb-0">Ringkasan sistem iDJ Kabupaten Bolaang Mongondow.</p>
            
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="{{ route('admin.monitoring') }}" class="btn btn-primary btn-pill shadow-sm px-4 btn-transition">
                <i class="fas fa-search me-2"></i> Monitoring Global
            </a>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3">
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
</div>
@endsection