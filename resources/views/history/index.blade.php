@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    {{-- Header Section: Compact & Informative --}}
    <div class="d-flex justify-content-between align-items-center pt-2 mb-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-white shadow-sm px-3 py-1 rounded-pill mb-1" style="width: fit-content;">
                    <li class="breadcrumb-item small"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-muted">Dashboard</a></li>
                    <li class="breadcrumb-item small active text-success fw-bold" aria-current="page">Arsip Disetujui</li>
                </ol>
            </nav>
            <h4 class="fw-bold text-dark m-0">
                <i class="fas fa-clipboard-check text-success me-2"></i>Arsip Berkas Selesai
            </h4>
            <p class="text-muted small mb-0">Daftar seluruh berkas yang telah melewati proses validasi dan pengesahan.</p>
        </div>
    </div>

    {{-- Advanced Filter Bar --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('history.index') }}" method="GET" class="row g-2">
                <div class="col-md-5">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-light-subtle" 
                               placeholder="Cari No. SPP/SPM, Nama Kegiatan, atau Dinas..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-0"><i class="fas fa-calendar-alt text-muted"></i></span>
                        <input type="date" name="date" class="form-control border-light-subtle" value="{{ request('date') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark btn-sm w-100 rounded-pill shadow-sm">
                        <i class="fas fa-filter me-1"></i> Filter Data
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('history.index') }}" class="btn btn-outline-secondary btn-sm w-100 rounded-pill">
                        <i class="fas fa-undo me-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Main Table Section --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small uppercase fw-bold">
                        <tr>
                            <th class="ps-4 py-3" width="30%">Dinas & Kegiatan</th>
                            <th class="py-3">No. SPP/SPM</th>
                            <th class="py-3">Tgl Pengesahan</th>
                            <th class="py-3">Total Nominal</th>
                            <th class="py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($histories as $h)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark mb-1">{{ $h->department->nama_dinas }}</div>
                                <div class="text-muted small text-truncate" title="{{ $h->judul_kegiatan }}" style="max-width: 300px;">
                                    <i class="fas fa-folder me-1"></i> {{ $h->judul_kegiatan }}
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-white text-dark border border-light-subtle px-3 py-2 rounded-3 shadow-sm font-monospace">
                                    {{ $h->nomor_spp_spm }}
                                </span>
                            </td>
                            <td>
                                <div class="text-dark small fw-semibold">{{ $h->updated_at->format('d M Y') }}</div>
                                <div class="extra-small text-muted">{{ $h->updated_at->format('H:i') }} WITA</div>
                            </td>
                            <td>
                                <div class="fw-bold text-success fs-6">
                                    Rp {{ number_format($h->total_pencairan, 0, ',', '.') }}
                                </div>
                                <div class="extra-small text-muted uppercase fw-bold">Verified</div>
                            </td>
                            <td class="text-center pe-3">
                                <div class="btn-group btn-group-sm rounded-pill border bg-white px-1 shadow-sm">
                                    <a href="{{ route('history.show', $h->id) }}" class="btn btn-link text-primary px-3 py-2 text-decoration-none" title="Lihat Detail">
                                        <i class="fas fa-external-link-alt me-1"></i> Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="py-4 text-center">
                                    <img src="https://illustrations.popsy.co/blue/empty-box.svg" alt="Empty" style="width: 120px;" class="mb-3 opacity-50">
                                    <h6 class="text-dark fw-bold">Data Tidak Ditemukan</h6>
                                    <p class="text-muted small">Coba sesuaikan kata kunci atau filter tanggal Anda.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($histories->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $histories->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    .extra-small { font-size: 0.65rem; }
    .uppercase { text-transform: uppercase; letter-spacing: 0.5px; }
    .rounded-4 { border-radius: 0.9rem; }
    .table thead th { border: none; letter-spacing: 0.05em; }
    .btn-link:hover { transform: translateY(-1px); transition: 0.2s; }
    /* Memastikan UI tetap rapat ke atas */
    .breadcrumb-item + .breadcrumb-item::before { content: "›"; color: #ced4da; font-size: 1.1rem; vertical-align: middle; }
</style>
@endsection