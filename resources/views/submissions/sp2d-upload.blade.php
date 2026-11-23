{{-- View: submissions/sp2d-upload --}}
@extends('layouts.app')

@section('content')
<style>
    .sp2d-upload-page .sp2d-upload-header,
    .sp2d-upload-page .sp2d-upload-panel {
        background: linear-gradient(135deg, rgba(15, 23, 42, .96), rgba(31, 41, 55, .94));
        border: 1px solid rgba(255, 255, 255, .08);
    }

    .sp2d-upload-page .sp2d-upload-header {
        min-height: 82px;
        padding: 1rem 1.25rem !important;
        backdrop-filter: blur(12px);
    }

    .sp2d-upload-page .sp2d-upload-panel {
        border-radius: 1rem;
        backdrop-filter: blur(12px);
    }

    .sp2d-upload-page .sp2d-upload-table {
        width: 100%;
        table-layout: fixed;
        --bs-table-bg: rgba(31, 41, 55, .58);
        --bs-table-color: #f1f5f9;
        --bs-table-hover-color: #fff;
        --bs-table-hover-bg: rgba(59, 130, 246, .12);
        --bs-table-border-color: rgba(255, 255, 255, .08);
        margin-bottom: 0;
    }

    .sp2d-upload-page .sp2d-upload-table thead th {
        background: rgba(15, 23, 42, .96);
        color: #cbd5e1;
        font-size: .72rem;
        letter-spacing: .05em;
        text-transform: uppercase;
        white-space: nowrap;
        padding-top: .85rem;
        padding-bottom: .85rem;
    }

    .sp2d-upload-page .sp2d-upload-table td,
    .sp2d-upload-page .sp2d-upload-table th {
        border-color: rgba(255, 255, 255, .08);
        padding: .68rem .55rem;
        font-size: .76rem;
        overflow-wrap: anywhere;
    }

    .sp2d-upload-page .sp2d-upload-table tbody tr {
        transition: background-color .18s ease;
    }

    .sp2d-upload-page .sp2d-upload-table tbody tr:hover {
        background: rgba(59, 130, 246, .1);
    }

    .sp2d-upload-page .sp2d-upload-table tbody tr:hover td {
        color: #f8fafc;
    }

    .sp2d-upload-page .sp2d-upload-table tbody tr:hover .text-white-50 {
        color: rgba(248, 250, 252, .82) !important;
    }

    .sp2d-upload-page .sp2d-upload-table tbody tr:hover .sp2d-number-label {
        color: #cbd5e1;
    }

    .sp2d-upload-page .sp2d-upload-table tbody tr:hover .sp2d-type-badge {
        color: #eff6ff;
        border-color: rgba(147, 197, 253, .48);
        background-color: rgba(59, 130, 246, .18);
    }

    .sp2d-upload-page .sp2d-upload-table tbody tr:hover .sp2d-row-number,
    .sp2d-upload-page .sp2d-upload-table tbody tr:hover .sp2d-number-value,
    .sp2d-upload-page .sp2d-upload-table tbody tr:hover .sp2d-activity-title {
        color: #f8fafc;
        font-weight: 500;
    }

    .sp2d-upload-page .sp2d-upload-table tbody tr:hover .sp2d-action-btn i {
        color: #fff;
        opacity: 1;
    }

    .sp2d-upload-page .sp2d-upload-table .sp2d-row-number {
        display: inline;
        width: auto;
        height: auto;
        border: 0;
        border-radius: 0;
        color: #cbd5e1;
        background: transparent;
        font-size: .75rem;
        font-weight: 400;
    }

    .sp2d-upload-page .sp2d-number-label {
        color: #94a3b8;
        font-size: .58rem;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .sp2d-upload-page .sp2d-number-value,
    .sp2d-upload-page .sp2d-activity-title {
        font-size: .76rem;
        font-weight: 500;
        line-height: 1.35;
    }

    .sp2d-upload-page .sp2d-description { font-size: .68rem; line-height: 1.35; }

    .sp2d-upload-page .sp2d-type-badge {
        min-width: 42px;
        padding: .4rem .65rem;
        border-radius: 999px;
        color: #dbeafe;
        background: rgba(59, 130, 246, .12);
        border: 1px solid rgba(96, 165, 250, .22);
        font-size: .64rem;
        font-weight: 600;
    }

    .sp2d-upload-page .sp2d-amount {
        color: #e2e8f0;
        font-variant-numeric: tabular-nums;
        font-weight: 600;
        white-space: nowrap;
    }

    .sp2d-upload-page .sp2d-action-btn {
        min-width: 0;
        max-width: 100%;
        min-height: 30px;
        padding: .28rem .55rem !important;
        border-radius: .6rem !important;
        white-space: nowrap;
        font-weight: 600;
        font-size: .69rem;
        line-height: 1.1;
    }

    .sp2d-upload-page .sp2d-status-badge {
        display: inline-flex;
        max-width: 100%;
        align-items: center;
        justify-content: center;
        gap: .3rem;
        padding: .38rem .55rem !important;
        border: 1px solid rgba(251,191,36,.3) !important;
        border-radius: 999px;
        color: #fbbf24 !important;
        background: rgba(245,158,11,.1) !important;
        font-size: .62rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .sp2d-upload-page .sp2d-status-badge.is-revision {
        color: #fb7185 !important;
        border-color: rgba(251,113,133,.32) !important;
        background: rgba(244,63,94,.1) !important;
    }

    @media (max-width: 767.98px) {
        .sp2d-upload-page {
            padding-left: .5rem;
            padding-right: .5rem;
        }

        .sp2d-upload-page .sp2d-upload-table {
            min-width: 850px;
        }
    }

    .sp2d-upload-page .sp2d-modal-content {
        color: #f8fafc;
        background: linear-gradient(145deg, rgba(15, 23, 42, .98), rgba(31, 41, 55, .98));
        border: 1px solid rgba(255, 255, 255, .12);
        backdrop-filter: blur(16px);
    }

    .sp2d-upload-page .sp2d-modal-content .form-control {
        color: #f8fafc;
        background: rgba(255, 255, 255, .08);
        border-color: rgba(255, 255, 255, .15);
    }
</style>

<div class="container-fluid py-3 sp2d-upload-page">
    <div class="sp2d-upload-header rounded-4 p-4 mb-3 d-flex align-items-center justify-content-between gap-3 flex-wrap shadow-sm">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center text-danger bg-danger bg-opacity-10 border border-danger border-opacity-25 flex-shrink-0" style="width:46px;height:46px;">
                <i class="fas fa-cloud-arrow-up fs-5 text-danger"></i>
            </div>
            <div>
                <h4 class="fw-bold text-white mb-1">Upload SP2D</h4>
                <p class="text-light opacity-75 mb-0 small">Pengajuan disetujui yang menunggu unggahan atau perbaikan SP2D.</p>
            </div>
        </div>
        <span class="badge rounded-pill bg-light text-dark px-3 py-2 shadow-sm">
            <i class="fas fa-list-ul text-danger me-1"></i>{{ number_format($submissions->total()) }} pengajuan
        </span>
    </div>

    <div class="sp2d-upload-panel rounded-4 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table sp2d-upload-table align-middle">
                <thead>
                    <tr>
                        <th class="text-center" style="width:5%;">No.</th>
                        <th style="width:15%;">Nomor SPP / SPM</th>
                        <th style="width:20%;">Kegiatan / Perihal</th>
                        <th class="text-center" style="width:8%;">Jenis</th>
                        <th class="text-center" style="width:9%;">Sumber Dana</th>
                        <th class="text-end" style="width:14%;">Nilai Permintaan</th>
                        <th class="text-center" style="width:15%;">Status SP2D</th>
                        <th class="text-center" style="width:14%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $submission)
                        @php
                            $sp2dDocument = $submission->documents->first(
                                fn ($document) => stripos($document->jenis_dokumen, 'sp2d') !== false
                            );
                            $needsRevision = $sp2dDocument && $sp2dDocument->status_dokumen === 'revisi';
                        @endphp
                        <tr>
                            <td class="text-center"><span class="sp2d-row-number">{{ $submissions->firstItem() + $loop->index }}</span></td>
                            <td>
                                <div class="d-flex flex-column gap-1">
                                    <div><span class="sp2d-number-label me-1">SPP</span><span class="sp2d-number-value text-white">{{ $submission->nomor_spp ?: '-' }}</span></div>
                                    <div><span class="sp2d-number-label me-1">SPM</span><span class="text-white-50">{{ $submission->nomor_spm ?: '-' }}</span></div>
                                </div>
                            </td>
                            <td>
                                <div class="sp2d-activity-title text-white mb-1">{{ $submission->judul_kegiatan ?: '-' }}</div>
                                <small class="sp2d-description text-white-50">{{ $submission->perihal ?? '-' }}</small>
                            </td>
                            <td class="text-center">
                                <span class="sp2d-type-badge">{{ $submission->jenis_pencairan }}</span>
                            </td>
                            <td class="text-center">
                                <span class="sp2d-type-badge">{{ $submission->sumber_dana }}</span>
                            </td>
                            <td class="text-end"><span class="sp2d-amount"><span class="text-white-50 fw-normal">Rp</span> {{ number_format($submission->total_pencairan ?? 0, 0, ',', '.') }}</span></td>
                            <td class="text-center">
                                @if($needsRevision)
                                    <span class="badge sp2d-status-badge is-revision"><i class="fas fa-rotate me-1"></i>Perlu Perbaikan</span>
                                @else
                                    <span class="badge sp2d-status-badge"><i class="fas fa-clock me-1"></i>Belum Diunggah</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm {{ $needsRevision ? 'btn-warning' : 'btn-danger' }} sp2d-action-btn" title="{{ $needsRevision ? 'Perbaiki SP2D' : 'Upload SP2D' }}" aria-label="{{ $needsRevision ? 'Perbaiki SP2D' : 'Upload SP2D' }}" data-bs-toggle="modal" data-bs-target="#modalUploadSp2d" data-upload-url="{{ route('submissions.uploadSp2d', $submission->id) }}" data-spm="{{ $submission->nomor_spm }}" data-needs-revision="{{ $needsRevision ? '1' : '0' }}" data-revision-note="{{ $needsRevision ? ($sp2dDocument->catatan ?? '') : '' }}">
                                    <i class="fas {{ $needsRevision ? 'fa-rotate' : 'fa-cloud-arrow-up' }} me-1" aria-hidden="true"></i>SP2D
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-circle-check fa-2x text-success mb-3"></i>
                                <div class="fw-semibold text-white">Semua SP2D sudah diunggah</div>
                                <small class="text-white-50">Tidak ada pengajuan yang perlu diunggah atau diperbaiki saat ini.</small>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($submissions->hasPages())
            <div class="p-3 border-top border-white border-opacity-10">
                {{ $submissions->links() }}
            </div>
        @endif
    </div>

    <div class="modal fade" id="modalUploadSp2d" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content sp2d-modal-content rounded-4">
                <form id="formUploadSp2d" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header border-bottom border-white border-opacity-10">
                        <div>
                            <h5 class="modal-title fw-bold" id="modalUploadSp2dTitle">Upload SP2D</h5>
                            <small class="text-white-50" id="modalUploadSp2dSpm"></small>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-danger bg-danger bg-opacity-10 border-danger border-opacity-25 text-light small d-none" id="modalUploadSp2dNote"></div>
                        <label for="sp2d_file" class="form-label fw-semibold">File SP2D (PDF, maksimal {{ $systemSettings['submission.sp2d_max_size_mb'] ?? 2 }} MB)</label>
                        <input type="file" class="form-control" id="sp2d_file" name="sp2d_file" accept=".pdf,application/pdf" required>
                    </div>
                    <div class="modal-footer border-top border-white border-opacity-10">
                        <button type="button" class="btn btn-outline-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="fas fa-upload me-2"></i>Simpan SP2D</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('modalUploadSp2d')?.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const form = document.getElementById('formUploadSp2d');
        const needsRevision = button.dataset.needsRevision === '1';
        const note = document.getElementById('modalUploadSp2dNote');

        form.action = button.dataset.uploadUrl;
        document.getElementById('modalUploadSp2dTitle').textContent = needsRevision ? 'Perbaiki SP2D' : 'Upload SP2D';
        document.getElementById('modalUploadSp2dSpm').textContent = `SPM ${button.dataset.spm || '-'}`;
        note.textContent = button.dataset.revisionNote ? `Catatan perbaikan: ${button.dataset.revisionNote}` : '';
        note.classList.toggle('d-none', !button.dataset.revisionNote);
        form.reset();
    });
</script>
@endsection
