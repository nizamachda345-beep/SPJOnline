{{-- View: ba-rekon/archive --}}
@extends('layouts.app')

@section('content')
<style>
    .ba-rekon-archive .archive-heading,
    .ba-rekon-archive .archive-panel {
        background: linear-gradient(135deg, rgba(15, 23, 42, .96), rgba(31, 41, 55, .94));
        border: 1px solid rgba(255, 255, 255, .08);
    }

    .ba-rekon-archive .archive-icon {
        width: 48px;
        height: 48px;
        display: grid;
        place-items: center;
        color: #60a5fa;
        background: rgba(59, 130, 246, .15);
        border: 1px solid rgba(96, 165, 250, .2);
        border-radius: 14px;
        flex: 0 0 48px;
    }

    .ba-rekon-archive .archive-count {
        color: #bfdbfe;
        background: rgba(59, 130, 246, .12);
        border: 1px solid rgba(96, 165, 250, .2);
        border-radius: 999px;
        padding: .45rem .8rem;
        white-space: nowrap;
    }

    .ba-rekon-archive .form-label {
        color: #cbd5e1;
        font-size: .65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: .35rem;
    }

    .ba-rekon-archive .form-select {
        min-height: 36px;
        border-color: rgba(255, 255, 255, .12);
        border-radius: 9px;
        font-size: .75rem;
    }

    .ba-rekon-archive .archive-table {
        --bs-table-bg: rgba(31, 41, 55, .58);
        --bs-table-color: #f1f5f9;
        --bs-table-hover-color: #fff;
        --bs-table-hover-bg: rgba(148, 163, 184, .12);
        --bs-table-border-color: rgba(255, 255, 255, .08);
        margin: 0;
    }

    .ba-rekon-archive .archive-table thead th {
        background: rgba(15, 23, 42, .96);
        color: #cbd5e1;
        font-size: .68rem;
        font-weight: 800;
        letter-spacing: .06em;
        padding: .95rem .85rem;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .ba-rekon-archive .archive-table tbody td {
        padding: .9rem .85rem;
        vertical-align: middle;
    }

    .ba-rekon-archive .archive-table tbody tr {
        background: rgba(31, 41, 55, .5);
    }

    .ba-rekon-archive .archive-table tbody tr:hover td,
    .ba-rekon-archive .archive-table tbody tr:hover td .text-white,
    .ba-rekon-archive .archive-table tbody tr:hover td .text-light {
        color: #f8fafc !important;
        opacity: 1 !important;
        visibility: visible !important;
    }

    .ba-rekon-archive .archive-table tbody tr:hover .archive-subtext {
        color: #cbd5e1 !important;
        opacity: 1 !important;
        visibility: visible !important;
    }

    .ba-rekon-archive .archive-table tbody tr:hover .archive-period i,
    .ba-rekon-archive .archive-table tbody tr:hover .fas,
    .ba-rekon-archive .archive-table tbody tr:hover .far {
        opacity: 1 !important;
        visibility: visible !important;
        filter: none !important;
    }

    .ba-rekon-archive .archive-table tbody tr:hover .archive-period i {
        color: #93c5fd !important;
    }

    .ba-rekon-archive .archive-table tbody tr:hover .archive-status.success { color: #4ade80 !important; }
    .ba-rekon-archive .archive-table tbody tr:hover .archive-status.warning { color: #fbbf24 !important; }
    .ba-rekon-archive .archive-table tbody tr:hover .archive-status.danger { color: #fb7185 !important; }

    .ba-rekon-archive .archive-table tbody tr:hover .btn-outline-info {
        color: #7dd3fc !important;
        border-color: #38bdf8 !important;
        background: rgba(14, 165, 233, .12) !important;
    }

    .ba-rekon-preview-frame {
        display: block;
        width: 100%;
        height: min(72vh, 760px);
        border: 0;
        background: #fff;
    }

    .ba-rekon-preview-modal .modal-content {
        background: linear-gradient(135deg, rgba(15, 23, 42, .82), rgba(31, 41, 55, .78));
        border: 1px solid rgba(255, 255, 255, .16);
        box-shadow: 0 24px 70px rgba(0, 0, 0, .5);
        -webkit-backdrop-filter: blur(22px);
        backdrop-filter: blur(22px);
    }

    .ba-rekon-preview-modal .modal-header {
        color: #f8fafc;
        background: rgba(15, 23, 42, .58);
        border-color: rgba(255, 255, 255, .12) !important;
        -webkit-backdrop-filter: blur(18px);
        backdrop-filter: blur(18px);
    }

    .ba-rekon-archive .archive-period {
        color: #f8fafc;
        font-weight: 700;
    }

    .ba-rekon-archive .archive-period i,
    .ba-rekon-archive .archive-muted-icon {
        color: #64748b;
    }

    .ba-rekon-archive .archive-subtext {
        color: #94a3b8;
        font-size: .75rem;
    }

    .ba-rekon-archive .archive-status {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        border: 1px solid currentColor;
        border-radius: 999px;
        font-size: .7rem;
        font-weight: 700;
        padding: .38rem .65rem;
        white-space: nowrap;
    }

    .ba-rekon-archive .archive-status.success { color: #4ade80; background: rgba(34, 197, 94, .1); }
    .ba-rekon-archive .archive-status.warning { color: #fbbf24; background: rgba(245, 158, 11, .1); }
    .ba-rekon-archive .archive-status.danger { color: #fb7185; background: rgba(244, 63, 94, .1); }

    .ba-rekon-archive .archive-empty {
        color: #94a3b8;
        padding: 3.5rem 1rem !important;
    }

    .ba-rekon-archive .archive-empty-icon {
        width: 54px;
        height: 54px;
        display: grid;
        place-items: center;
        margin: 0 auto .9rem;
        border-radius: 16px;
        background: rgba(148, 163, 184, .1);
        color: #94a3b8;
        font-size: 1.25rem;
    }

    .ba-rekon-archive .pagination { margin-bottom: 0; }
    .ba-rekon-archive .page-link {
        color: #cbd5e1;
        background: rgba(15, 23, 42, .72);
        border-color: rgba(255, 255, 255, .1);
    }
    .ba-rekon-archive .page-item.active .page-link { color: #fff; background: #2563eb; border-color: #2563eb; }
    .ba-rekon-archive .page-item.disabled .page-link { color: #64748b; background: rgba(15, 23, 42, .45); }

    @media (max-width: 575.98px) {
        .ba-rekon-archive .archive-heading { padding: 1rem !important; }
        .ba-rekon-archive .archive-count { font-size: .7rem; padding: .35rem .6rem; }
        .ba-rekon-archive .archive-table tbody td { padding: .75rem .65rem; }
    }
</style>

<div class="container-fluid py-3 ba-rekon-archive">
    <div class="archive-heading rounded-4 p-4 mb-4 d-flex align-items-center justify-content-between gap-3 flex-wrap shadow-sm">
        <div class="d-flex align-items-center gap-3">
            <div class="archive-icon"><i class="fas fa-calendar-check"></i></div>
            <div>
                <h4 class="fw-bold text-white mb-1">Arsip BA Rekon</h4>
                <p class="text-light opacity-75 mb-0 small">Telusuri dokumen berdasarkan periode unggah.</p>
            </div>
        </div>
        <span class="archive-count small"><i class="fas fa-folder-open me-1"></i>{{ number_format($baRekons->total()) }} arsip</span>
    </div>

    <div class="glass-card-dark border-0 rounded-4 shadow-sm mb-3">
        <div class="card-body p-2">
            <form method="GET" action="{{ route('ba-rekon.archive') }}" class="row align-items-end g-2">
                <div class="col-6 col-md-4 {{ auth()->user()->hasRole('operator') ? 'col-lg-3' : 'col-lg-2' }}">
                    <label for="filterBulan" class="form-label small fw-bold text-light uppercase">Bulan</label>
                    <select id="filterBulan" name="bulan" class="form-select border-0 bg-light">
                        <option value="">Semua Bulan</option>
                        @foreach([1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'] as $number => $monthName)
                            <option value="{{ $number }}" @selected((string) request('bulan') === (string) $number)>{{ $monthName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-4 col-lg-2">
                    <label for="filterTahun" class="form-label small fw-bold text-light uppercase">Tahun</label>
                    <select id="filterTahun" name="tahun" class="form-select border-0 bg-light">
                        <option value="">Semua Tahun</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" @selected((string) request('tahun') === (string) $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-4 {{ auth()->user()->hasRole('operator') ? 'col-lg-3' : 'col-lg-2' }}">
                    <label for="filterStatus" class="form-label small fw-bold text-light uppercase">Status</label>
                    <select id="filterStatus" name="status" class="form-select border-0 bg-light">
                        <option value="">Semua Status</option>
                        <option value="pending" @selected(request('status') === 'pending')>Menunggu Verifikasi</option>
                        <option value="verified" @selected(request('status') === 'verified')>Disetujui</option>
                        <option value="rejected" @selected(request('status') === 'rejected')>Perlu Perbaikan</option>
                    </select>
                </div>
                @unless(auth()->user()->hasRole('operator'))
                    <div class="col-6 col-md-6 col-lg-3">
                        <label for="filterDinas" class="form-label small fw-bold text-light uppercase">Dinas</label>
                        <select id="filterDinas" name="dinas" class="form-select border-0 bg-light">
                            <option value="">Semua Dinas</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" @selected((string) request('dinas') === (string) $department->id)>
                                    {{ $department->nama_dinas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endunless
                <div class="col-6 col-md-6 {{ auth()->user()->hasRole('operator') ? 'col-lg-4' : 'col-lg-3' }} d-flex gap-2 flex-nowrap">
                    <button class="btn btn-dark rounded-pill px-3 flex-grow-1 shadow-sm d-flex align-items-center justify-content-center" type="submit"><i class="fas fa-sliders-h me-2"></i> Filter</button>
                    <a class="btn btn-light rounded-pill px-3 border shadow-sm d-flex align-items-center justify-content-center" href="{{ route('ba-rekon.archive') }}" title="Reset filter" aria-label="Reset filter"><i class="fas fa-sync-alt"></i></a>
                </div>
            </form>
        </div>
    </div>

    <div class="archive-panel card border-0 rounded-4 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle archive-table">
                <thead>
                    <tr>
                        <th class="ps-4">Periode BA</th>
                        @unless(auth()->user()->hasRole('operator'))
                            <th>SKPD / Dinas</th>
                        @endunless
                        <th>Pengunggah</th>
                        <th>Waktu Unggah</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Dokumen</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($baRekons as $baRekon)
                        @php
                            $monthNames = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
                            $statusInfo = match($baRekon->status) {
                                'verified' => ['Disetujui', 'success'],
                                'rejected' => ['Perlu Perbaikan', 'danger'],
                                default => ['Menunggu Verifikasi', 'warning'],
                            };
                        @endphp
                        <tr>
                            <td class="ps-4">
                                <div class="archive-period"><i class="far fa-calendar me-1"></i>{{ $monthNames[(int) $baRekon->bulan] ?? '-' }}</div>
                                <div class="archive-subtext">Tahun {{ $baRekon->tahun }}</div>
                            </td>
                            @unless(auth()->user()->hasRole('operator'))
                                <td>{{ $baRekon->department->nama_dinas ?? '-' }}</td>
                            @endunless
                            <td>
                                <div class="text-white fw-semibold">{{ $baRekon->user->name ?? '-' }}</div>
                                @if($baRekon->catatan && $baRekon->status === 'rejected')
                                    <div class="archive-subtext text-truncate" style="max-width: 220px;" title="{{ $baRekon->catatan }}">Catatan: {{ $baRekon->catatan }}</div>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                <div class="text-light">{{ $baRekon->uploaded_at ? \Carbon\Carbon::parse($baRekon->uploaded_at)->translatedFormat('d M Y') : '-' }}</div>
                                <div class="archive-subtext">{{ $baRekon->uploaded_at ? \Carbon\Carbon::parse($baRekon->uploaded_at)->format('H:i') . ' WITA' : '' }}</div>
                            </td>
                            <td><span class="archive-status {{ $statusInfo[1] }}"><i class="fas {{ $statusInfo[1] === 'success' ? 'fa-check-circle' : ($statusInfo[1] === 'danger' ? 'fa-exclamation-circle' : 'fa-clock') }}"></i>{{ $statusInfo[0] }}</span></td>
                            <td class="text-end pe-4">
                                <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3"
                                        data-bs-toggle="modal"
                                        data-bs-target="#baRekonPreviewModal"
                                        data-file-url="{{ route('ba-rekon.archive.file', $baRekon->id) }}"
                                        data-file-title="{{ ($monthNames[(int) $baRekon->bulan] ?? 'BA Rekon') . ' ' . $baRekon->tahun }}">
                                    <i class="fas fa-eye me-1"></i> Pratinjau
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->hasRole('operator') ? 5 : 6 }}" class="text-center archive-empty">
                                <div class="archive-empty-icon"><i class="far fa-folder-open"></i></div>
                                <div class="text-white fw-semibold mb-1">Arsip tidak ditemukan</div>
                                <div class="small">Belum ada BA Rekon yang cocok dengan filter ini.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($baRekons->hasPages())
            <div class="card-footer bg-transparent border-top border-white border-opacity-10 d-flex justify-content-center py-3">
                {{ $baRekons->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

<div class="modal fade ba-rekon-preview-modal" id="baRekonPreviewModal" tabindex="-1" aria-labelledby="baRekonPreviewTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content archive-panel rounded-4 overflow-hidden">
            <div class="modal-header border-bottom border-white border-opacity-10">
                <div>
                    <h5 class="modal-title text-white fw-bold" id="baRekonPreviewTitle">Pratinjau BA Rekon</h5>
                    <small class="text-light opacity-75" id="baRekonPreviewPeriod">Dokumen BA Rekon</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-0">
                <iframe class="ba-rekon-preview-frame" id="baRekonPreviewFrame" title="Pratinjau dokumen BA Rekon" src="about:blank"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
    (() => {
        const modal = document.getElementById('baRekonPreviewModal');
        const frame = document.getElementById('baRekonPreviewFrame');
        const period = document.getElementById('baRekonPreviewPeriod');

        if (!modal || !frame || !period) return;

        modal.addEventListener('show.bs.modal', (event) => {
            const trigger = event.relatedTarget;
            frame.src = trigger?.dataset.fileUrl || 'about:blank';
            period.textContent = trigger?.dataset.fileTitle || 'Dokumen BA Rekon';
        });

        modal.addEventListener('hidden.bs.modal', () => {
            frame.src = 'about:blank';
        });
    })();
</script>
@endsection
