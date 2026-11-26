{{-- View: bpk/monitoring --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="card glass-card-dark border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ url()->current() }}" method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="small fw-bold text-uppercase text-light ">Thn Anggaran</label>
                    <select name="tahun" class="form-select border-secondary shadow-none form-select-sm">
                        @php $currentYear = date('Y'); @endphp
                        @for($i = $currentYear; $i >= $currentYear - 5; $i--)
                            <option value="{{ $i }}" {{ request('tahun', $currentYear) == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="small fw-bold text-uppercase text-light ">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control border-secondary shadow-none form-select-sm" value="{{ request('tanggal') }}">
                </div>

                <div class="col-md-2">
                    <label class="small fw-bold text-uppercase text-light ">Nomor SPP / SPM</label>
                    <input type="text" name="nomor" class="form-control border-secondary shadow-none form-select-sm" placeholder="Cari..." value="{{ request('nomor') }}">
                </div>

                <div class="col-md-2">
                    <label class="small fw-bold text-uppercase text-light">SKPD / DINAS</label>
                    <select name="dinas" class="form-select border-secondary shadow-none form-select-sm">
                        <option value="">-- Semua Dinas --</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}" {{ request('dinas') == $d->id ? 'selected' : '' }}>{{ $d->nama_dinas }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="small fw-bold text-uppercase text-light">Min. Nominal</label>
                    <input type="number" name="min_nominal" class="form-control text-white border-secondary shadow-none form-select-sm" placeholder="Contoh: 1000000" value="{{ request('min_nominal') }}">
                </div>

                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-outline-light btn-sm btn-pill w-100"><i class="fas fa-search me-1"></i> Cari</button>
                    <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-light btn-pill w-100"><i class="fas fa-sync-alt"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div id="monitoring-container" class="card glass-card-dark border-0 shadow-sm">
        <div class="card-header py-3">
            <h5 class="mb-0 fw-bold text-white"><i class="fas fa-layer-group me-2"></i>Audit Pengajuan</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-3 text-white">
                <thead class="glass-header-dark">
                    <tr>
                        <th class="ps-4" style="width: 60px;">No</th>
                        <th>SKPD</th>
                        <th>Tanggal</th>
                        <th>Nomor SPP/SPM</th>
                        <th>Total Pencairan</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $index => $s)
                    <tr>

                        <td class="ps-4 text-white-50 small">
                            {{ ($submissions->currentPage() - 1) * $submissions->perPage() + $index + 1 }}
                        </td>
                        <td class="fw-bold">{{ $s->department->nama_dinas }}</td>
                        <td>{{ $s->created_at->format('d/m/Y') }}</td>
                        <td><code class="text-info">{{ $s->nomor_spp }} / {{ $s->nomor_spm }}</code></td>
                        <td>Rp {{ number_format($s->total_pencairan, 0, ',', '.') }}</td>
                        <td>
                            @php
                                $status = strtolower($s->status);
                                $sp2dLengkap = $s->documents->contains(function ($document) {
                                    return str_contains(strtolower($document->jenis_dokumen ?? ''), 'sp2d')
                                        && in_array($document->status_dokumen, ['verified', 'approved'], true);
                                });
                                $style = $sp2dLengkap
                                    ? ['color' => 'text-success', 'icon' => 'fa-file-circle-check', 'label' => 'Lengkap SP2D']
                                    : match($status) {
                                    'disetujui'       => ['color' => 'text-success', 'icon' => 'fa-check-circle',    'label' => 'Disetujui'],
                                    'approved'        => ['color' => 'text-success', 'icon' => 'fa-check-circle',    'label' => 'Disetujui'],
                                    default           => ['color' => 'text-muted',   'icon' => 'fa-question-circle','label' => ucfirst($s->status)],
                                };
                            @endphp
                            <i class="fas {{ $style['icon'] }} {{ $style['color'] }} me-2"></i>
                            {{ $style['label'] }}
                        </td>
                        <td class="text-center">
                            <a href="{{ route('bpk.show', $s->id) }}" class="btn btn-sm btn-outline-light btn-pill">
                                <i class="fas fa-search"></i> Audit
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-white-50">Data tidak ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($submissions->hasPages())
            <div class="mb-4 d-flex justify-content-center">
                <div class="glass-pagination bg-opacity-75 bg-white px-4 py-1 rounded-pill shadow-sm border">
                    {{ $submissions->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
