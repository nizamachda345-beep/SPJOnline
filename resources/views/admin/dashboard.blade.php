{{-- View: admin/dashboard --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">

<div class="mb-4">
    <div class="d-flex align-items-center justify-content-between w-100 glass-header-dark shadow-sm bg-opacity-25 rounded-4 p-3">

        <div class="d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 p-3 rounded-3 d-none d-md-block">
                <i class="fas fa-tachometer-alt text-primary fs-4"></i>
            </div>
            <div>
                <h4 class="fw-bold text-uppercase mb-0 text-primary dashboard-title">Dashboard Admin</h4>

                <div class="text-light mt-1" style="font-size: 0.75rem;">
                    <span id="liveDateTimePolicy" class="fw-medium text-uppercase">Memuat Waktu {{ app(\App\Services\SystemSettingsService::class)->timezoneLabel() }}...</span>
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.monitoring') }}" class="btn btn-primary rounded-pill shadow-sm px-4 py-2 fw-bold text-uppercase btn-monitoring">
                <i class="fas fa-search me-2"></i> Monitoring Global
            </a>
        </div>
    </div>
</div>

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3 mb-3">
        <div class="col">
            <div class="card glass-header-dark border-0 shadow-sm rounded-4 ">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between gap-3">
                        <div>
                            <span class="text-light small text-uppercase fw-bold">Total Pengajuan</span>
                            <h2 class="text-light fw-semibold mb-0">{{ $stats['total_berkas'] ?? 0 }}</h2>
                        </div>
                        <span class="text-warning fs-3"><i class="fas fa-file-invoice-dollar"></i></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card glass-header-dark border-0 shadow-sm rounded-4 ">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between gap-3">
                        <div>
                            <span class="text-light small text-uppercase fw-bold">Pengajuan Lengkap</span>
                            <h2 class="text-light fw-semibold mb-0">{{ $stats['total_pengajuan_lengkap'] ?? 0 }}</h2>
                        </div>
                        <span class="text-success fs-3"><i class="fas fa-circle-check"></i></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card glass-header-dark border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between gap-3">
                        <div class="min-w-0">
                            <span class="text-light small text-uppercase fw-bold">Belum Upload SP2D</span>
                            <h2 class="text-light fw-semibold mb-0">{{ $stats['total_belum_upload_sp2d'] ?? 0 }}</h2>
                        </div>
                        <span class="text-danger fs-3"><i class="fas fa-file-circle-exclamation"></i></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card glass-header-dark border-0 shadow-sm rounded-4 ">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between gap-3">
                        <div>
                            <span class="text-light small text-uppercase fw-bold">SKPD/Dinas</span>
                            <h2 class="text-light fw-semibold mb-0">{{ $stats['dinas'] ?? 0 }}</h2>
                        </div>
                        <span class="text-info fs-3"><i class="fas fa-building"></i></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card glass-header-dark border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between gap-3">
                        <div>
                            <span class="text-light small text-uppercase fw-bold">Pengajuan Disetujui</span>
                            <h2 class="text-light fw-semibold mb-0">{{ $stats['total_pengajuan_disetujui'] ?? 0 }}</h2>
                        </div>
                        <span class="text-success fs-3"><i class="fas fa-circle-check"></i></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card glass-header-dark border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between gap-3">
                        <div>
                            <span class="text-light small text-uppercase fw-bold">Expired</span>
                            <h2 class="text-light fw-semibold mb-0">{{ $stats['expired'] ?? 0 }}</h2>
                        </div>
                        <span class="text-danger fs-3"><i class="fas fa-clock"></i></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card glass-header-dark border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between gap-3">
                        <div>
                            <span class="text-light small text-uppercase fw-bold">Ditolak</span>
                            <h2 class="text-light fw-semibold mb-0">{{ $stats['rejected'] ?? 0 }}</h2>
                        </div>
                        <span class="text-danger fs-3"><i class="fas fa-ban"></i></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card glass-header-dark border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between gap-3">
                        <div>
                            <span class="text-light small text-uppercase fw-bold">Total User</span>
                            <h2 class="text-light fw-semibold mb-0">{{ $stats['user_total'] ?? 0 }}</h2>
                        </div>
                        <span class="text-success fs-3"><i class="fas fa-users"></i></span>
                    </div>
                </div>
            </div>
        </div>

    </div>
    @include('components.monthly-chart')
</div>
@endsection
