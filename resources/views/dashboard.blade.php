@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Breadcrumbs --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-light rounded-3 px-3 py-2">
            <li class="breadcrumb-item active" aria-current="page">
                <i class="fas fa-home me-1"></i> Dashboard
            </li>
        </ol>
    </nav>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1">Dashboard iDJ</h2>
            <p class="text-muted mb-0">Ringkasan aktivitas pengajuan dan status saat ini.</p>
        </div>
        <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-2">Sistem Informasi Arsip Digital</span>
    </div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3 mb-4">
        <div class="col">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="flex-shrink-0 bg-accent-primary text-primary p-3 rounded-3">
                        <i class="fas fa-file-alt fa-2x"></i>
                    </div>
                    <div>
                        <span class="data-label">Total Pengajuan</span>
                        <h3 class="fw-bold mb-0">{{ $stats['total_berkas'] }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="flex-shrink-0 bg-accent-warning text-warning p-3 rounded-3">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <div>
                        <span class="data-label">Menunggu Verifikasi</span>
                        <h3 class="fw-bold mb-0">{{ $stats['pending'] }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="flex-shrink-0 bg-accent-warning text-warning p-3 rounded-3">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                    <div>
                        <span class="data-label">Menunggu Validasi</span>
                        <h3 class="fw-bold mb-0">{{ $stats['proses_validasi'] }}</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="flex-shrink-0 bg-accent-success text-success p-3 rounded-3">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <div>
                        <span class="data-label">Selesai / Disetujui</span>
                        <h3 class="fw-bold mb-0">{{ $stats['disetujui'] }}</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.monthly-chart')

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <h5 class="fw-semibold">Selamat Datang, <strong>{{ auth()->user()->name }}</strong>!</h5>
            <p class="text-muted mb-0">Anda masuk sebagai <strong>{{ auth()->user()->role_label }}</strong>. Gunakan menu di samping untuk mulai mengelola berkas.</p>
        </div>
    </div>
</div>
@endsection