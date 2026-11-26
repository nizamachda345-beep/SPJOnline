{{-- View: bpk/monitoring --}}
@extends('layouts.app')

@section('content')
<style>
    /* 1. Perbaikan Hover Tabel Dark Glassmorphism */
    #monitoring-container .table-dark-glass {
        --bs-table-bg: transparent;
        --bs-table-color: #f8f9fa;
        --bs-table-border-color: rgba(255, 255, 255, 0.08);
        --bs-table-hover-bg: rgba(56, 189, 248, 0.12);
        --bs-table-hover-color: #ffffff;
    }

    #monitoring-container .table-dark-glass tbody tr {
        transition: background-color 0.2s ease;
    }

    #monitoring-container .table-dark-glass tbody tr:hover > td,
    #monitoring-container .table-dark-glass tbody tr:hover > th {
        background-color: rgba(56, 189, 248, 0.12) !important;
        color: #ffffff !important;
        box-shadow: none !important;
    }

    /* 2. Styling Input Filter agar senada dengan tema gelap */
    .form-control-glass,
    .form-select-glass {
        background-color: rgba(15, 23, 42, 0.6) !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        color: #f8f9fa !important;
        color-scheme: dark;
    }

    .form-control-glass::placeholder {
        color: rgba(255, 255, 255, 0.45) !important;
    }

    .form-control-glass:focus,
    .form-select-glass:focus {
        background-color: rgba(15, 23, 42, 0.85) !important;
        border-color: #38bdf8 !important;
        box-shadow: 0 0 0 0.2rem rgba(56, 189, 248, 0.2) !important;
    }

    .form-select-glass option {
        background-color: #1e293b;
        color: #f8f9fa;
    }

    /* 3. Badge Status Soft-Dark */
    .badge-soft-success {
        background-color: rgba(34, 197, 94, 0.15);
        color: #4ade80;
        border: 1px solid rgba(34, 197, 94, 0.3);
    }

    .badge-soft-secondary {
        background-color: rgba(148, 163, 184, 0.15);
        color: #cbd5e1;
        border: 1px solid rgba(148, 163, 184, 0.3);
    }

    /* 4. Tombol Audit saat baris di-hover */
    #monitoring-container tbody tr:hover .btn-audit {
        background-color: #38bdf8;
        border-color: #38bdf8;
        color: #0f172a;
        font-weight: 600;
    }

    /* 5. Pagination Dark Glass */
    .glass-pagination .pagination {
        margin-bottom: 0;
        gap: 4px;
    }

    .glass-pagination .page-link {
        background-color: transparent;
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #e2e8f0;
        border-radius: 50rem !important;
        padding: 0.25rem 0.65rem;
        font-size: 0.85rem;
    }

    .glass-pagination .page-link:hover {
        background-color: rgba(56, 189, 248, 0.2);
        border-color: #38bdf8;
        color: #ffffff;
    }

    .glass-pagination .page-item.active .page-link {
        background-color: #38bdf8;
        border-color: #38bdf8;
        color: #0f172a;
        font-weight: 600;
    }

    .glass-pagination .page-item.disabled .page-link {
        background-color: transparent;
        border-color: rgba(255, 255, 255, 0.08);
        color: rgba(255, 255, 255, 0.3);
    }
</style>

<div class="container-fluid">

    {{-- Filter Card --}}
    <div class="card glass-card-dark border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ url()->current() }}" method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="small fw-bold text-uppercase text-white-50 mb-1">Thn Anggaran</label>
                    <select name="tahun" class="form-select form-select-sm form-select-glass">
                        @php $currentYear = date('Y'); @endphp
                        @for($i = $currentYear; $i >= $currentYear - 5; $i--)
                            <option value="{{ $i }}" {{ request('tahun', $currentYear) == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="small fw-bold text-uppercase text-white-50 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control form-control-sm form-control-glass" value="{{ request('tanggal') }}">
                </div>

                <div class="col-md-2">
                    <label class="small fw-bold text-uppercase text-white-50 mb-1">Nomor SPP / SPM</label>
                    <input type="text" name="nomor" class="form-control form-control-sm form-control-glass" placeholder="Cari nomor..." value="{{ request('nomor') }}">
                </div>

                <div class="col-md-2">
                    <label class="small fw-bold text-uppercase text-white-50 mb-1">SKPD / DINAS</label>
                    <select name="dinas" class="form-select form-select-sm form-select-glass">
                        <option value="">-- Semua Dinas --</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}" {{ request('dinas') == $d->id ? 'selected' : '' }}>{{ $d->nama_dinas }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="small fw-bold text-uppercase text-white-50 mb-1">Min. Nominal</label>
                    <input type="number" name="min_nominal" class="form-control form-control-sm form-control-glass" placeholder="Contoh: 1000000" value="{{ request('min_nominal') }}">
                </div>

                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-info btn-sm btn-pill w-100 fw-semibold text-dark">
                        <i class="fas fa-search me-1"></i> Cari
                    </button>
                    <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-light btn-pill px-3" title="Reset Filter">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Table Card --}}
    <div id="monitoring-container" class="card glass-card-dark border-0 shadow-sm">
        <div class="card-header py-3 d-flex justify-content-between align-items-center border-bottom border-secondary border-opacity-25">
            <h5 class="mb-0 fw-bold text-white">
                <i class="fas fa-layer-group me-2 text-info"></i>Audit Pengajuan
            </h5>
            <span class="badge bg-dark bg-opacity-50 text-white-50 border border-secondary border-opacity-25 px-3 py-2 rounded-pill">
                Total: {{ $submissions->total() }} Data
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-dark table-dark-glass table-hover align-middle mb-0">
                <thead class="glass-header-dark text-uppercase small text-white-50">
                    <tr>
                        <th class="ps-4 py-3" style="width: 60px;">No</th>
                        <th class="py-3">SKPD</th>
                        <th class="py-3">Tanggal</th>
                        <th class="py-3">Nomor SPP/SPM</th>
                        <th class="py-3 text-end">Total Pencairan</th>
                        <th class="py-3 text-center">Status</th>
                        <th class="py-3 text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $index => $s)
                    <tr>
                        <td class="ps-4 text-white-50 small">
                            {{ ($submissions->currentPage() - 1) * $submissions->perPage() + $index + 1 }}
                        </td>
                        <td class="fw-semibold text-white">{{ $s->department->nama_dinas }}</td>
                        <td class="text-white-50">{{ $s->created_at->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge bg-dark bg-opacity-75 border border-info border-opacity-25 text-info font-monospace px-2 py-1">
                                {{ $s->nomor_spp }} / {{ $s->nomor_spm }}
                            </span>
                        </td>
                        <td class="text-end font-monospace fw-semibold text-white">
                            Rp {{ number_format($s->total_pencairan, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            @php
                                $status = strtolower($s->status);
                                $sp2dLengkap = $s->documents->contains(function ($document) {
                                    return str_contains(strtolower($document->jenis_dokumen ?? ''), 'sp2d')
                                        && in_array($document->status_dokumen, ['verified', 'approved'], true);
                                });
                                $style = $sp2dLengkap
                                    ? ['badge' => 'badge-soft-success', 'icon' => 'fa-file-circle-check', 'label' => 'Lengkap SP2D']
                                    : match($status) {
                                        'disetujui', 'approved' => ['badge' => 'badge-soft-success',   'icon' => 'fa-check-circle',    'label' => 'Disetujui'],
                                        default                 => ['badge' => 'badge-soft-secondary', 'icon' => 'fa-question-circle', 'label' => ucfirst($s->status)],
                                    };
                            @endphp
                            <span class="badge rounded-pill px-3 py-2 fw-normal {{ $style['badge'] }}">
                                <i class="fas {{ $style['icon'] }} me-1"></i> {{ $style['label'] }}
                            </span>
                        </td>
                        <td class="text-center pe-4">
                            <a href="{{ route('bpk.show', $s->id) }}" class="btn btn-sm btn-outline-light btn-pill btn-audit px-3">
                                <i class="fas fa-search me-1"></i> Audit
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-white-50">
                            <i class="fas fa-folder-open fa-2x mb-2 d-block opacity-50"></i>
                            Data tidak ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($submissions->hasPages())
            <div class="py-3 d-flex justify-content-center border-top border-secondary border-opacity-25">
                <div class="glass-pagination bg-dark bg-opacity-50 px-3 py-2 rounded-pill border border-secondary border-opacity-25">
                    {{ $submissions->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection