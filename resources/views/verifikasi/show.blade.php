{{-- View: verifikasi/show --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 glass-header-dark position-relative">
        <div class="glass-reflection"></div>
        <div class="card-body p-3 p-lg-4">
            <div class="row align-items-center">

                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary-soft text-primary rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: rgba(34, 42, 197, 0.12); border: 1px solid rgba(34, 42, 197, 0.18);">
                            <i class="fas fa-file-circle-check fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-black text-light mb-1 tracking-tight">Detail Berkas</h4>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0 extra-small">
                                    <li class="breadcrumb-item"><a href="{{ route($backRouteName ?? 'verifikasi.index') }}" class="text-white-50 text-decoration-none">{{ $isSp2dVerificationMode ? 'Verifikasi SP2D' : 'Verifikasi Permintaan' }}</a></li>
                                    <li class="breadcrumb-item active fw-medium text-primary">Detail Berkas</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="{{ route($backRouteName ?? 'verifikasi.index') }}" class="btn btn-glass-action rounded-pill px-3 py-2 text-light">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">

        <div class="col-lg-8">
            @php
                $requiredDocumentIds = $requiredDocumentIds ?? [];

                $requiredDocuments = $submission->documents->whereIn('id', $requiredDocumentIds);

                $totalDocs = $requiredDocuments->count();

                $verifiedDocs = $requiredDocuments
                    ->where('status_dokumen', 'verified')
                    ->count();

                $revisionDocs = $requiredDocuments
                    ->whereIn('status_dokumen', ['revisi', 'direvisi'])
                    ->count();

                $waitingDocs = $totalDocs - $verifiedDocs - $revisionDocs;

                $percent = ($totalDocs > 0)
                    ? min(100, ($verifiedDocs / $totalDocs) * 100)
                    : 0;

                $isAllVerified = ($totalDocs > 0 && $verifiedDocs >= $totalDocs);
                $isSp2dVerificationMode = $isSp2dVerificationMode ?? false;

                $sp2dDocument = $submission->documents->first(function ($doc) {
                    $jenis = strtolower(str_replace(['_', '-', '/', '\\'], ' ', trim($doc->jenis_dokumen)));
                    return str_contains($jenis, 'sp2d');
                });

                $isSp2dVerified = $sp2dDocument && $sp2dDocument->status_dokumen === 'verified';

                $jenisPencairan = strtoupper(trim($submission->jenis_pencairan ?? ''));

                $statusPengajuan = strtolower(trim($submission->status ?? ''));

                $statusLabel = match ($statusPengajuan) {
                    'pending' => 'Menunggu Verifikasi',
                    'direvisi' => 'Menunggu Verifikasi Ulang',
                    'revisi' => 'Perlu Perbaikan',
                    'proses validasi' => 'Proses Validasi',
                    'disetujui' => 'Disetujui',
                    'selesai' => 'Selesai',
                    'history' => 'Selesai',
                    default => ucwords(str_replace('_', ' ', $submission->status ?? '-')),
                };

                $statusClass = match ($statusPengajuan) {
                    'pending' => 'status-warning',
                    'direvisi' => 'status-warning',
                    'revisi' => 'status-danger',
                    'proses validasi' => 'status-info',
                    'disetujui', 'selesai', 'history' => 'status-success',
                    default => 'status-neutral',
                };
            @endphp

            <div class="card border-0 shadow-lg rounded-4 glass-card-dark overflow-hidden h-100">
                <div class="glass-header-dark border-0 py-3 px-4">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                        <div>
                            <div class="d-flex align-items-center mb-1">
                                <i class="fas fa-folder-open text-primary me-2"></i>
                                <h6 class="fw-bold text-white mb-0">Lampiran Dokumen</h6>
                            </div>
                            <p class="text-white-50 small mb-0">Silakan tinjau keabsahan berkas di bawah ini.</p>
                        </div>
                        <div class="text-end">
                            @if($isSp2dVerificationMode)
                                <span class="badge rounded-pill bg-info bg-opacity-10 border border-info border-opacity-25 text-info px-3 py-2">
                                    <i class="fas fa-file-invoice-dollar me-1"></i> Verifikasi SP2D
                                </span>
                            @else
                                <span class="badge rounded-pill bg-success bg-opacity-10 border border-success border-opacity-25 text-success px-3 py-2">
                                    <i class="fas fa-check-circle me-1"></i> {{ $verifiedDocs }}/{{ $totalDocs }} Wajib Terverifikasi
                                </span>
                            @endif
                        </div>
                    </div>

                    @if(!$isSp2dVerificationMode)
                    <div class="progress mt-3" style="height: 6px; background: rgba(255,255,255,0.05); border-radius: 10px; overflow: visible;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-info"
                            role="progressbar"
                            style="width: {{ $percent }}%;
                                   transition: width 1s ease;
                                   box-shadow: 0 0 15px rgba(0, 212, 255, 0.5);
                                   background-image: linear-gradient(45deg, rgba(255,255,255,.15) 25%, transparent 25%, transparent 50%, rgba(255,255,255,.15) 50%, rgba(255,255,255,.15) 75%, transparent 75%, transparent);
                                   background-size: 1rem 1rem;">
                        </div>
                    </div>
                    @else
                    <div class="alert alert-info border-0 mb-0 mt-3 py-2 px-3 small" style="background: rgba(14,165,233,.10); color:#bae6fd;">
                        <i class="fas fa-info-circle me-1"></i> SP2D telah diunggah setelah pengajuan disetujui dan menunggu verifikasi akhir.
                    </div>
                    @endif
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
    <table class="table table-dark align-middle m-0 document-table">

        <thead>
            <tr class="text-white-50 small document-table-header">
                <th class="ps-4 py-3 border-0">
                    Dokumen
                </th>

                <th class="text-center border-0">
                    Pratinjau
                </th>

                <th class="border-0 text-center">
                    Status
                </th>

                <th class="pe-4 text-end border-0">
                    Tindakan
                </th>
            </tr>
        </thead>

        <tbody>

            @foreach($submission->documents as $doc)

                @php

                    $isRequired = in_array(
                        $doc->id,
                        $requiredDocumentIds
                    );

                    $normalizedDocType = strtolower(
                        str_replace(
                            ['_', '-', '/', '\\'],
                            ' ',
                            trim($doc->jenis_dokumen)
                        )
                    );

                    $isSTS = str_contains(
                        $normalizedDocType,
                        'sts'
                    );

                    $isSP2D = str_contains(
                        $normalizedDocType,
                        'sp2d'
                    );

                    $jenisDokumen = strtolower(
                        trim($doc->jenis_dokumen)
                    );

                    $namaDokumen = match ($jenisDokumen) {

                        'dokumen ls'
                            => 'Dokumen LS',

                        'dokumen tu'
                            => 'Dokumen TU',

                        'dokumen gu'
                            => 'Dokumen GU',

                        'dokumen up'
                            => 'Dokumen UP',

                        'dokumen tu nihil'
                            => 'Dokumen TU Nihil',

                        'dokumen gu nihil'
                            => 'Dokumen GU Nihil',

                        'dokumen spj'
                            => 'Dokumen SPJ',

                        'dokumen sts',
                        'sts'
                            => 'Dokumen STS',

                        'dokumen sp2d',
                        'sp2d'
                            => 'SP2D',

                        default
                            => strtoupper(
                                str_replace(
                                    '_',
                                    ' ',
                                    $doc->jenis_dokumen
                                )
                            ),
                    };

                    $keteranganDokumen = match ($jenisDokumen) {

                        'dokumen ls'
                            => 'SPP · Verifikasi SPP/SPM · SPTJM · Billing',

                        'dokumen tu'
                            => 'SPP · Verifikasi SPP/SPM · SPTJM · Persetujuan BUD',

                        'dokumen gu'
                            => 'SPP · Verifikasi SPP/SPM · SPTJM · Persetujuan BUD',

                        'dokumen up'
                            => 'SPP · Verifikasi SPP/SPM · SPTJM',

                        'dokumen tu nihil'
                            => 'SPP · Verifikasi SPP/SPM · SPTJM · Persetujuan BUD',

                        'dokumen gu nihil'
                            => 'Dokumen pertanggungjawaban',

                        'dokumen spj'
                            => 'Dokumen pertanggungjawaban',

                        'dokumen sts',
                        'sts'
                            => 'Surat Tanda Setoran',

                        'dokumen sp2d',
                        'sp2d'
                            => 'Surat Perintah Pencairan Dana',

                        default
                            => 'Dokumen pengajuan',

                    };

                @endphp

                <tr
                    class="document-row"
                    id="doc-row-{{ $doc->id }}"
                    data-doc-id="{{ $doc->id }}"
                    data-status="{{ $doc->status_dokumen }}"
                >

                    <td class="ps-4 py-3">

                        <div class="document-name-wrapper">

                            <div class="document-file-icon">
                                <i class="fas fa-file-pdf"></i>
                            </div>

                            <div class="document-information">

                                <div class="d-flex align-items-center gap-2 flex-wrap">

                                    <span class="document-name">
                                        {{ $namaDokumen }}
                                    </span>

                                    @if($isRequired)

                                        <span class="document-badge required">
                                            WAJIB
                                        </span>

                                    @elseif($isSTS)

                                        <span class="document-badge optional">
                                            OPSIONAL
                                        </span>

                                    @endif

                                </div>

                                <div class="document-description">
                                    {{ $keteranganDokumen }}
                                </div>

                            </div>

                        </div>

                    </td>

                    <td class="text-center">
<button
    type="button"
    class="btn btn-document-preview btn-glass-primary btn-sm rounded-pill px-3 fw-bold shadow-sm"
    onclick="openPdfModal(
        '{{ route('dokumen.file', $doc->id) }}',
        '{{ strtoupper($doc->jenis_dokumen) }}'
    )"
    data-bs-toggle="tooltip"
    data-bs-placement="top"
    title="Tinjau dokumen">

    <i class="fas fa-eye"></i>
    <span>Tinjau</span>
</button>

                    </td>

                    <td class="status-container text-center">

                        @if($doc->status_dokumen == 'verified')

                            <span class="document-status verified">
                                <i class="fas fa-check-circle"></i>
                                Diverifikasi
                            </span>

                        @elseif($doc->status_dokumen == 'revisi')

                            <span class="document-status revision">
                                <i class="fas fa-exclamation-circle"></i>
                                Revisi
                            </span>

                        @elseif($doc->status_dokumen == 'direvisi')

                            <span class="document-status revised">
                                <i class="fas fa-exclamation-circle"></i>
                                Direvisi
                            </span>

                        @else

                            <span class="document-status waiting">
                                <i class="far fa-clock"></i>
                                Menunggu
                            </span>

                        @endif

                    </td>

                    <td class="pe-4 text-end">

                        <div class="document-actions">

                            @if($isSp2dVerificationMode && $isSP2D)
                                <span data-bs-toggle="tooltip" data-bs-placement="top" title="Setujui SP2D">
                                    <button type="button" class="btn btn-action btn-sp2d-setuju {{ $doc->status_dokumen == 'verified' ? 'active disabled' : '' }}" data-id="{{ $doc->id }}" {{ $doc->status_dokumen == 'verified' ? 'disabled' : '' }}>
                                        <i class="fas fa-check"></i>
                                    </button>
                                </span>
                                <span data-bs-toggle="tooltip" data-bs-placement="top" title="Tolak / Revisi SP2D">
                                    <button type="button" class="btn btn-action btn-sp2d-tolak {{ $doc->status_dokumen == 'revisi' ? 'active' : '' }}" data-bs-toggle="modal" data-bs-target="#modalTolak" data-docid="{{ $doc->id }}" data-docname="SP2D">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </span>
                            @else
                            <span
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                title="Setujui">

                                <button
                                    type="button"
                                    class="btn btn-action btn-setuju
                                    {{ $doc->status_dokumen == 'verified' ? 'active' : '' }}"
                                    data-id="{{ $doc->id }}">

                                    <i class="fas fa-check"></i>

                                </button>

                            </span>

                            <span
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                title="Tolak / Revisi">

                                <button
                                    type="button"
                                    class="btn btn-action btn-tolak
                                    {{ $doc->status_dokumen == 'revisi' ? 'active' : '' }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalTolak"
                                    data-docid="{{ $doc->id }}"
                                    data-docname="{{ strtoupper(str_replace('_', ' ', $doc->jenis_dokumen)) }}">

                                    <i class="fas fa-times"></i>

                                </button>

                            </span>
                            @endif

                        </div>

                    </td>

                </tr>

            @endforeach

        </tbody>

    </table>
</div>
                </div>

                <div class="p-4 border-top border-white border-opacity-10">
                    @if($isSp2dVerificationMode)
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="text-white-50 mb-0 small fst-italic">
                                <i class="fas fa-info-circle me-1 text-info"></i> SP2D diverifikasi pada tahap akhir. Pilih Setujui atau Revisi pada dokumen SP2D.
                            </p>
                            <span class="badge rounded-pill {{ $isSp2dVerified ? 'bg-success bg-opacity-10 border border-success border-opacity-25 text-success' : 'bg-warning bg-opacity-10 border border-warning border-opacity-25 text-warning' }} px-3 py-2">
                                {{ $isSp2dVerified ? 'SP2D Terverifikasi' : 'Menunggu Verifikasi SP2D' }}
                            </span>
                        </div>
                    @else
                        <div class="d-flex justify-content-between align-items-center">
                            <p class="text-white-50 mb-0 small fst-italic">
                                <i class="fas fa-info-circle me-1 text-info"></i> Tombol Verifikasi aktif otomatis setelah semua dokumen Diverifikasi.
                            </p>
                            <form action="{{ route('verifikasi.sendToKabid', $submission->id) }}" method="POST" class="form-reload-page">
                                @csrf
                                <button type="submit" class="btn {{ $isAllVerified ? 'btn-neon-success' : 'btn-glass-action opacity-25' }} rounded-pill px-4 py-2 fw-bold shadow text-uppercase">
                                    <i class="fas fa-paper-plane me-2"></i> VERIFIKASI
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">

            <div class="card border-0 shadow-lg rounded-4 overflow-hidden glass-card-dark h-100">

                <div class="glass-header-dark border-0 py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="info-card-icon">
                            <i class="fas fa-file-invoice"></i>
                        </div>

                        <div>
                            <h6 class="mb-0 text-white">Informasi Pengajuan</h6>
                            <small class="text-light">
                                Detail administrasi dan informasi pengajuan
                            </small>
                        </div>
                    </div>
                </div>

                <div class="card-body py-3">

                    <div class="info-section mb-3 p-3 rounded-4 d-flex justify-content-between align-items-center gap-2">
                        <span class="info-label text-light fw-semibold"><i class="fas fa-spinner fa-spin text-light me-1"></i> STATUS PENGAJUAN</span>
                        <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
                    </div>

                    <div class="info-section mb-3 p-3 rounded-4">

                        <button type="button"
                                class="info-collapse-toggle collapsed"
                                data-bs-toggle="collapse"
                                data-bs-target="#verifikasiDataPengajuan"
                                aria-expanded="false"
                                aria-controls="verifikasiDataPengajuan">
                            <span class="info-section-title">
                                <i class="fas fa-file-alt me-2"></i>
                                Data Pengajuan
                            </span>
                            <i class="fas fa-chevron-down collapse-chevron"></i>
                        </button>

                        <div class="collapse" id="verifikasiDataPengajuan">
                        <div class="info-row mt-3">
                            <span class="info-label">
                                Instansi Pengaju
                            </span>

                            <span class="info-value">
                                {{ $submission->department->nama_dinas ?? '-' }}
                            </span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">
                                Jenis Pencairan
                            </span>

                            <span class="info-value">
                                <span class="jenis-badge">
                                    {{ strtoupper($submission->jenis_pencairan ?? '-') }}
                                </span>
                            </span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">
                                Nomor SPP/SPM
                            </span>

                            <span class="info-value">
                                {{ $submission->nomor_spp ?? '-' }} / {{ $submission->nomor_spm ?? '-' }}
                            </span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">
                                Tanggal Berkas
                            </span>

                            <span class="info-value">
                                {{ $submission->tanggal
                                    ? \Carbon\Carbon::parse($submission->tanggal)->translatedFormat('d F Y')
                                    : '-' }}
                            </span>
                        </div>

                        <div class="info-row">
                            <span class="info-label">
                                Tanggal Pengajuan
                            </span>

                            <span class="info-value">
                                {{ $submission->created_at
                                    ? $submission->created_at->translatedFormat('d F Y, H:i')
                                    : '-' }}
                            </span>
                        </div>

                        </div>
                    </div>

                    <div class="info-section mb-3 p-3 rounded-4">

                        <button type="button"
                                class="info-collapse-toggle collapsed"
                                data-bs-toggle="collapse"
                                data-bs-target="#verifikasiInformasiKegiatan"
                                aria-expanded="false"
                                aria-controls="verifikasiInformasiKegiatan">
                            <span class="info-section-title">
                                <i class="fas fa-tasks me-2"></i>
                                Informasi Kegiatan
                            </span>
                            <i class="fas fa-chevron-down collapse-chevron"></i>
                        </button>

                        <div class="collapse" id="verifikasiInformasiKegiatan">
                            <div class="activity-info-list mt-3">
                                <div class="activity-info-item">
                                    <span class="activity-info-label">Kegiatan</span>
                                    <span class="activity-info-value">{{ $submission->judul_kegiatan ?? '-' }}</span>
                                </div>
                                <div class="activity-info-item">
                                    <span class="activity-info-label">Sub Kegiatan</span>
                                    <span class="activity-info-value">{{ $submission->judul_sub_kegiatan ?? '-' }}</span>
                                </div>
                                <div class="activity-info-item">
                                    <span class="activity-info-label">Perihal</span>
                                    <span class="activity-info-value">{{ $submission->perihal ?? '-' }}</span>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="info-section mb-3 p-3 rounded-4">

                        <div class="info-section-title text-light">
                            <i class="fas fa-money-bill-wave me-2"></i>
                            Informasi Keuangan
                        </div>

                        <div class="info-row">

                            <span class="info-label">
                                Sumber Dana
                            </span>

                            <span class="info-value">
                                {{ $submission->sumber_dana ?? '-' }}
                            </span>

                        </div>

                        <div class="info-total">

                            <div>
                                <span class="info-total-label">
                                    Total Permintaan
                                </span>

                                <small>
                                    Nilai pengajuan
                                </small>
                            </div>

                            <strong>
                                Rp {{ number_format($submission->total_pencairan ?? 0, 0, ',', '.') }}
                            </strong>

                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

{{-- Modal: Penolakan dokumen oleh verifikator --}}
<div class="modal fade" id="modalTolak" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark shadow-lg rounded-4 border-white border-opacity-10 text-white overflow-hidden"
             style="background: rgba(20, 24, 33, .98) !important; backdrop-filter: blur(10px);">

            <div class="modal-header glass-header-dark border-0 py-3 px-4">
                <h6 class="modal-title fw-bold text-white d-flex align-items-center m-0">
                    <span class="rounded-3 text-center d-flex align-items-center justify-content-center me-3"
                          style="background: rgba(220, 53, 69, .12); color: #f87171; width: 32px; height: 32px; font-size: 0.9rem;">
                        <i class="fas fa-undo-alt"></i>
                    </span>
                    <span>Minta Revisi Dokumen</span>
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formTolak" action="" method="POST" class="no-ajax">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-center text-white-50 small mb-4" style="line-height: 1.6;">
                        Berikan alasan logis mengapa berkas <br>
                        <span class="d-inline-block my-1 px-3 py-1 rounded-pill fw-semibold border border-warning border-opacity-20"
                              id="namaBerkasModal"
                              style="color: #fde047; background: rgba(245, 158, 11, 0.08); font-size: 0.85rem;">
                        </span> <br> memerlukan langkah revisi kembali.
                    </p>

                    <input type="hidden" name="action" value="revisi">

                    <div class="form-group">
                        <label class="small text-white-50 fw-semibold mb-2 text-uppercase d-block" style="font-size: 0.65rem; letter-spacing: 0.05em;">Catatan / Alasan Pengembalian</label>
                        <textarea name="catatan"
                                  id="catatanTolak"
                                  class="form-control rounded-4 p-3 custom-glass-textarea"
                                  rows="4"
                                  placeholder="Contoh: Lampiran berkas SPTJM tidak terbaca jelas, nominal selisih pada rekapitulasi..."
                                  required></textarea>
                    </div>
                </div>

                <div class="modal-footer border-top border-white border-opacity-10 bg-dark bg-opacity-40 py-3 px-4">
                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3 bg-opacity-20 border-0 text-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnSubmitTolak" class="btn btn-danger btn-sm rounded-pill px-4 shadow-sm text-light fw-bold">
                        <i class="fas fa-paper-plane me-2"></i>Kirim ke Operator
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Pratinjau PDF dokumen --}}
<div class="modal fade" id="modalPreviewPDF" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark shadow-lg rounded-4 border-white border-opacity-10 text-white overflow-hidden" style="height: 90vh; background: rgba(20, 24, 33, .98) !important;">
            <div class="modal-header glass-header-dark border-0 text-white py-3 px-4">
                <h6 class="modal-title fw-bold">
                    <i class="fas fa-file-pdf me-2"></i>
                    <span id="pdfTitle">Preview Dokumen</span>
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="pdfFrame" src="" width="100%" height="750px" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Function: Membuka modal pratinjau PDF
    window.openPdfModal = function (pdfUrl, docName) {

        const modalElement = document.getElementById('modalPreviewPDF');
        const pdfFrame = document.getElementById('pdfFrame');
        const pdfTitle = document.getElementById('pdfTitle');

        if (!modalElement || !pdfFrame) {
            console.error('Elemen modal preview tidak ditemukan.');
            return;
        }

        pdfTitle.textContent = 'Preview: ' + (docName || 'Dokumen');

        pdfFrame.src = pdfUrl;

        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
        modal.show();
    };

    const modalTolak = document.getElementById('modalTolak');
    if (modalTolak) {
        modalTolak.addEventListener('hidden.bs.modal', function () {
            const form = document.getElementById('formTolak');
            if (form) { form.removeAttribute('data-sp2d-mode'); form.removeAttribute('data-current-doc-id'); }
        });
    }

    const previewModal = document.getElementById('modalPreviewPDF');

    if (previewModal) {

        previewModal.addEventListener('hidden.bs.modal', function () {

            const pdfFrame = document.getElementById('pdfFrame');

            if (pdfFrame) {
                pdfFrame.src = '';
            }

        });
    }

    document.addEventListener('click', function (event) {
        const btn = event.target.closest('.btn-sp2d-setuju');
        if (!btn) return;
        event.preventDefault();
        if (btn.disabled || btn.classList.contains('disabled')) return;

        const docId = btn.getAttribute('data-id');
        const row = btn.closest('tr');
        if (!docId || !row) return;

        btn.disabled = true;
        const originalHtml = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        const actionUrl = "{{ route('sp2d.verifikasi', ['id' => $submission->id]) }}";

        fetch(actionUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json'
            },
            body: new URLSearchParams({
                action_type: 'approve',
                document_id: docId
            })
        })
        .then(async response => {
            const data = await response.json().catch(() => ({}));
            if (!response.ok || data.success === false) {
                throw new Error(data.message || 'Gagal memverifikasi SP2D.');
            }
            return data;
        })
        .then(() => {
            row.setAttribute('data-status', 'verified');
            const status = row.querySelector('.status-container');
            if (status) status.innerHTML = '<span class="document-status verified"><i class="fas fa-check-circle"></i> Diverifikasi</span>';
            btn.innerHTML = '<i class="fas fa-check"></i>';
            btn.classList.add('active', 'disabled');
            btn.disabled = true;
            const revisionBtn = row.querySelector('.btn-sp2d-tolak');
            if (revisionBtn) { revisionBtn.disabled = true; revisionBtn.classList.add('disabled'); }
            const footerBadge = document.querySelector('.glass-header-dark .badge.bg-success');
            if (!footerBadge) window.location.reload();
            else footerBadge.textContent = 'SP2D Terverifikasi';
        })
        .catch(error => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            alert(error.message || 'Gagal memverifikasi SP2D.');
        });
    });

    document.addEventListener('click', function (event) {
        const btn = event.target.closest('.btn-sp2d-tolak');
        if (!btn) return;

        const docId = btn.getAttribute('data-docid');
        const form = document.getElementById('formTolak');
        const catatan = document.getElementById('catatanTolak');
        const nama = document.getElementById('namaBerkasModal');

        if (form) form.setAttribute('data-sp2d-mode', '1');
        if (form) form.setAttribute('data-current-doc-id', docId || '');
        if (nama) nama.textContent = 'SP2D';
        if (catatan) catatan.value = '';
    });

    document.addEventListener('click', function (event) {

        const btn = event.target.closest('.btn-setuju');

        if (!btn) {
            return;
        }

        event.preventDefault();

        if (
            btn.disabled ||
            btn.classList.contains('disabled')
        ) {
            return;
        }

        const docId = btn.getAttribute('data-id');

        if (!docId) {
            alert('ID dokumen tidak ditemukan.');
            return;
        }

        const row = btn.closest('tr');

        if (!row) {
            alert('Baris dokumen tidak ditemukan.');
            return;
        }

        const btnTolak = row.querySelector('.btn-tolak');

        const originalHtml = btn.innerHTML;

        btn.disabled = true;
        btn.classList.add('disabled');
        btn.innerHTML =
            '<i class="fas fa-spinner fa-spin"></i>';

        const actionUrl =
            "{{ route('verifikasi.document.action', ['id' => '__DOC_ID__']) }}"
            .replace('__DOC_ID__', docId);

        fetch(actionUrl, {

            method: 'POST',

            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                'Accept': 'application/json'
            },

            body: new URLSearchParams({
                action: 'setuju'
            })

        })

        .then(async response => {

            const data = await response.json().catch(() => ({}));

            if (!response.ok) {

                throw new Error(
                    data.message ||
                    'Gagal memproses verifikasi dokumen.'
                );
            }

            return data;
        })

        .then(response => {

            row.setAttribute('data-status', 'verified');

            const statusContainer =
                row.querySelector('.status-container');

            if (statusContainer) {

                statusContainer.innerHTML = `
                    <span class="document-status verified">
                        <i class="fas fa-check-circle"></i>
                        Diverifikasi
                    </span>
                `;
            }

            row.classList.add('verified-bg');
            row.classList.remove('revisi-bg');

            btn.innerHTML =
                '<i class="fas fa-check"></i>';

            btn.classList.add('active');
            btn.classList.add('disabled');
            btn.disabled = true;

            if (btnTolak) {

                btnTolak.classList.remove('disabled');
                btnTolak.disabled = false;
            }

            updateProgressBar();

        })

        .catch(error => {

            console.error(
                'Error verifikasi dokumen:',
                error
            );

            alert(
                error.message ||
                'Gagal memproses dokumen.'
            );

            btn.innerHTML = originalHtml;
            btn.classList.remove('disabled');
            btn.disabled = false;
        });

    });

    document.addEventListener('click', function (event) {

        const btn = event.target.closest('.btn-tolak');

        if (!btn) {
            return;
        }

        const docId =
            btn.getAttribute('data-docid');

        const docName =
            btn.getAttribute('data-docname');

        document.getElementById('namaBerkasModal').textContent =
            docName || 'Dokumen';

        const form =
            document.getElementById('formTolak');

        form.setAttribute(
            'data-current-doc-id',
            docId
        );

        document.getElementById('catatanTolak').value = '';

        document.getElementById('btnSubmitTolak').disabled = true;

    });

    const catatanTolak =
        document.getElementById('catatanTolak');

    const btnSubmitTolak =
        document.getElementById('btnSubmitTolak');

    if (catatanTolak && btnSubmitTolak) {

        catatanTolak.addEventListener('input', function () {

            btnSubmitTolak.disabled =
                this.value.trim().length < 5;

        });
    }

    const formTolak =
        document.getElementById('formTolak');

    if (formTolak) {

        formTolak.addEventListener('submit', function (event) {

            event.preventDefault();

            const form = this;

            if (form.getAttribute('data-sp2d-mode') === '1') {
                const docIdSp2d = form.getAttribute('data-current-doc-id');
                const catatanSp2d = document.getElementById('catatanTolak').value.trim();
                if (!docIdSp2d) { alert('ID SP2D tidak ditemukan.'); return; }
                if (catatanSp2d.length < 5) { alert('Catatan revisi minimal 5 karakter.'); return; }
                const submitBtnSp2d = document.getElementById('btnSubmitTolak');
                submitBtnSp2d.disabled = true;
                submitBtnSp2d.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
                fetch("{{ route('sp2d.verifikasi', ['id' => $submission->id]) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}",
                        'Accept': 'application/json'
                    },
                    body: new URLSearchParams({ action_type: 'reject', document_id: docIdSp2d, catatan: catatanSp2d })
                }).then(async response => {
                    const data = await response.json().catch(() => ({}));
                    if (!response.ok || data.success === false) throw new Error(data.message || 'Gagal mengirim revisi SP2D.');
                    return data;
                }).then(() => window.location.reload()).catch(error => {
                    alert(error.message || 'Gagal mengirim revisi SP2D.');
                    submitBtnSp2d.disabled = false;
                    submitBtnSp2d.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Kirim ke Operator';
                });
                return;
            }

            const docId =
                form.getAttribute('data-current-doc-id');

            const catatan =
                document.getElementById('catatanTolak')
                    .value
                    .trim();

            if (!docId) {

                alert('ID dokumen tidak ditemukan.');

                return;
            }

            if (catatan.length < 5) {

                alert(
                    'Catatan revisi minimal 5 karakter.'
                );

                return;
            }

            btnSubmitTolak.disabled = true;

            btnSubmitTolak.innerHTML =
                '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';

            const actionUrl =
                "{{ route('verifikasi.document.action', ['id' => '__DOC_ID__']) }}"
                .replace('__DOC_ID__', docId);

            fetch(actionUrl, {

                method: 'POST',

                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                    'X-CSRF-TOKEN': "{{ csrf_token() }}",
                    'Accept': 'application/json'
                },

                body: new URLSearchParams({
                    action: 'revisi',
                    catatan: catatan
                })

            })

            .then(async response => {

                const data =
                    await response.json().catch(() => ({}));

                if (!response.ok) {

                    throw new Error(
                        data.message ||
                        'Gagal mengirim revisi dokumen.'
                    );
                }

                return data;
            })

            .then(response => {

                const row =
                    document.getElementById(
                        'doc-row-' + docId
                    );

                if (row) {

                    row.setAttribute(
                        'data-status',
                        'revisi'
                    );

                    const statusContainer =
                        row.querySelector(
                            '.status-container'
                        );

                    if (statusContainer) {

                        statusContainer.innerHTML = `
                            <span class="document-status revision">
                                <i class="fas fa-exclamation-circle"></i>
                                Revisi
                            </span>
                        `;
                    }

                    const btnSetuju =
                        row.querySelector(
                            '.btn-setuju'
                        );

                    const btnTolak =
                        row.querySelector(
                            '.btn-tolak'
                        );

                    if (btnSetuju) {

                        btnSetuju.innerHTML =
                            '<i class="fas fa-check"></i>';

                        btnSetuju.classList.remove(
                            'active',
                            'disabled'
                        );

                        btnSetuju.disabled = false;
                    }

                    if (btnTolak) {

                        btnTolak.classList.add('active');
                    }
                }

                const modalElement =
                    document.getElementById(
                        'modalTolak'
                    );

                const modal =
                    bootstrap.Modal.getInstance(
                        modalElement
                    );

                if (modal) {
                    modal.hide();
                }

                catatanTolak.value = '';

                btnSubmitTolak.disabled = true;

                btnSubmitTolak.innerHTML =
                    '<i class="fas fa-paper-plane me-2"></i>Kirim ke Operator';

                updateProgressBar();

            })

            .catch(error => {

                console.error(
                    'Error revisi:',
                    error
                );

                alert(
                    error.message ||
                    'Gagal mengirim revisi dokumen.'
                );

                btnSubmitTolak.disabled = false;

                btnSubmitTolak.innerHTML =
                    '<i class="fas fa-paper-plane me-2"></i>Kirim ke Operator';
            });

        });
    }

    // Function: Memperbarui progres verifikasi dokumen
    window.updateProgressBar = function () {

        const requiredDocumentIds =
            @json($requiredDocumentIds ?? []);

        const total =
            requiredDocumentIds.length;

        let verified = 0;

        requiredDocumentIds.forEach(function (docId) {

            const row =
                document.getElementById(
                    'doc-row-' + docId
                );

            if (!row) {
                return;
            }

            const status =
                String(
                    row.getAttribute('data-status') || ''
                ).toLowerCase();

            if (status === 'verified') {
                verified++;
            }

        });

        const percent =
            total > 0
                ? (verified / total) * 100
                : 0;

        const progressBar =
            document.querySelector('.progress-bar');

        if (progressBar) {

            progressBar.style.width =
                percent + '%';
        }

        const badge =
            document.querySelector(
                '.glass-header-dark .badge-status.success'
            );

        if (badge) {

            badge.innerHTML = `
                <i class="fas fa-check-double me-1"></i>
                ${verified} / ${total} Terverifikasi
            `;
        }

        const btnKirimKabid =
            document.querySelector(
                '.form-reload-page button[type="submit"]'
            );

        if (!btnKirimKabid) {
            return;
        }

        if (
            total > 0 &&
            verified >= total
        ) {

            btnKirimKabid
                .classList
                .remove(
                    'disabled',
                    'opacity-25'
                );

            btnKirimKabid.disabled = false;

            btnKirimKabid.classList.add(
                'animate-pulse'
            );

        } else {

            btnKirimKabid
                .classList
                .add(
                    'disabled',
                    'opacity-25'
                );

            btnKirimKabid.disabled = true;

            btnKirimKabid.classList.remove(
                'animate-pulse'
            );
        }
    };

    const savedScrollPos =
        localStorage.getItem(
            'verifikasi_scroll_pos'
        );

    if (savedScrollPos) {

        setTimeout(function () {

            const alertContainer =
                document.getElementById(
                    'alert-container'
                );

            const offset =
                alertContainer &&
                alertContainer.offsetHeight > 0
                    ? alertContainer.offsetHeight
                    : 0;

            window.scrollTo({
                top:
                    parseInt(savedScrollPos) +
                    offset,
                behavior: 'instant'
            });

            localStorage.removeItem(
                'verifikasi_scroll_pos'
            );

        }, 50);
    }

    const reloadForms =
        document.querySelectorAll(
            '.form-reload-page'
        );

    reloadForms.forEach(function (form) {

        form.addEventListener(
            'submit',
            function () {

                localStorage.setItem(
                    'verifikasi_scroll_pos',
                    window.scrollY
                );

                const btn =
                    this.querySelector(
                        'button[type="submit"]'
                    );

                if (btn) {

                    const originalWidth =
                        btn.offsetWidth;

                    btn.style.width =
                        originalWidth + 'px';

                    btn.innerHTML = `
                        <span
                            class="spinner-border spinner-border-sm"
                            role="status">
                        </span>
                    `;

                    btn.classList.add(
                        'disabled'
                    );

                    btn.style.pointerEvents =
                        'none';
                }

            }
        );

    });

    updateProgressBar();

});
</script>


<style>

    .glass-card {
        background:rgba(255,255,255,.03)!important;
        backdrop-filter:blur(12px)!important;
        -webkit-backdrop-filter:blur(12px)!important;
    }

    .glass-card-inner {
        background:rgba(255,255,255,.02)!important;
        border:1px solid rgba(255,255,255,.05);
        transition:all .25s ease;
    }

    .glass-card-inner:hover {

        background:rgba(255,255,255,.05)!important;

        border-color:rgba(255,255,255,.15);

        transform:translateY(-2px);

    }

    .x-small {
        font-size:.7rem!important;
    }

    .fw-black {
        font-weight:900!important;
    }

    .tracking-tight {
        letter-spacing:-.25px;
    }

    .tracking-wide {
        letter-spacing:.35px;
    }

    .tracking-wider {
        letter-spacing:.7px;
    }

    /*
    |--------------------------------------------------------------------------
    | BUTTON
    |--------------------------------------------------------------------------
    */

    .btn-glass-action {

        background:rgba(255,255,255,.05);

        border:1px solid rgba(255,255,255,.1);

        color:#fff;

        transition:all .2s ease;

    }

    .btn-glass-action:hover {

        background:rgba(255,255,255,.12);

        border-color:rgba(255,255,255,.2);

        color:#fff;

    }

    .btn-glass-primary {

        background:rgba(13,110,253,.08);

        border:1px solid rgba(13,110,253,.25);

        color:#6ea8fe;

        transition:all .2s ease;

    }

    .btn-glass-primary:hover {

        background:rgba(13,110,253,.2);

        border-color:rgba(13,110,253,.45);

        color:#fff;

    }

    .btn-glass-danger {

        background:rgba(220,53,69,.05);

        border:1px solid rgba(220,53,69,.2);

        color:#ea868f;

        transition:all .2s ease;

    }

    .btn-glass-danger:hover {

        background:linear-gradient(135deg, #dc3545, #b02a37) !important;

        border-color:#dc3545;

        color:#fff !important;

        box-shadow:0 6px 18px rgba(220,53,69,.35);

        transform:translateY(-1px);

    }

    .btn-neon-success {

        background: linear-gradient(135deg, #198754, #20c997);

        border:none;

        color:#fff!important;

        box-shadow:
            0 4px 15px
            rgba(25,135,84,.3);

        transition:all .25s ease;

    }

    .btn-neon-success:hover {

        background: linear-gradient(135deg, #157347, #198754);

        box-shadow:
            0 6px 20px
            rgba(25,135,84,.5);

        transform:translateY(-2px);

    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION STATUS
    |--------------------------------------------------------------------------
    */

    .validation-status-card {

        border-radius:16px;

        border:1px solid rgba(255,255,255,.08);

        background:rgba(255,255,255,.025);

    }

    .validation-status-card.is-complete {

        border-color:
            rgba(25,135,84,.25);

        background:
            rgba(25,135,84,.05);

    }

    .validation-status-card.is-pending {

        border-color:
            rgba(255,193,7,.20);

        background:
            rgba(255,193,7,.04);

    }

    .status-icon {

        width:38px;

        height:38px;

        min-width:38px;

        border-radius:12px;

        display:flex;

        align-items:center;

        justify-content:center;

    }

    /*
    |--------------------------------------------------------------------------
    | COLLAPSIBLE INFORMATION SECTIONS
    |--------------------------------------------------------------------------
    */

    .info-collapse-toggle {

        width:100%;

        display:flex;

        align-items:center;

        justify-content:space-between;

        gap:12px;

        padding:0;

        margin:0;

        border:0;

        background:transparent;

        color:inherit;

        text-align:left;

        cursor:pointer;

    }

    .info-collapse-toggle .info-section-title {

        display:flex;

        align-items:center;

        color:#fff;

        font-size:.74rem;

        font-weight:800;

        letter-spacing:.2px;

    }

    .collapse-chevron {

        color:rgba(255,255,255,.55);

        font-size:.68rem;

        transition:transform .2s ease, color .2s ease;

        flex-shrink:0;

    }

    .info-collapse-toggle:hover .collapse-chevron {

        color:#fff;

    }

    .info-collapse-toggle:not(.collapsed) .collapse-chevron {

        transform:rotate(180deg);

        color:#6ea8fe;

    }

    .info-collapse-toggle:focus {

        outline:none;

        box-shadow:none;

    }

    .info-collapse-toggle:focus-visible {

        outline:2px solid rgba(13,110,253,.45);

        outline-offset:4px;

        border-radius:6px;

    }

    /*
    |--------------------------------------------------------------------------
    | INFO SECTION
    |--------------------------------------------------------------------------
    */

    .info-section {

        background:
            rgba(255,255,255,.03);

        border:
            1px solid
            rgba(255,255,255,.05);

    }

    .section-label {

        display:block;

        text-transform:uppercase;

        font-size:.63rem;

        font-weight:800;

        letter-spacing:.8px;

        margin-bottom:8px;

    }

    .info-row {

        display:flex;

        justify-content:space-between;

        align-items:flex-start;

        gap:20px;

        padding-bottom:12px;

        border-bottom:
            1px solid
            rgba(255,255,255,.08);

    }

    .info-label {

        color:
            rgba(255,255,255,.62);

        font-size:.72rem;

        white-space:nowrap;

    }

    .info-label i {

        width:16px;

        opacity:.5;

    }

    .info-value {

        color:#fff;

        font-size:.72rem;

        font-weight:600;

        text-align:right;

        word-break:break-word;

        max-width:62%;

    }

    .info-subvalue {

        color:
            rgba(255,255,255,.45);

        font-size:.62rem;

        font-weight:400;

        margin-top:2px;

    }

    .info-block {

        padding-bottom:12px;

        border-bottom:
            1px solid
            rgba(255,255,255,.08);

    }

    .perihal-box {

        margin-left:20px;

        padding-left:12px;

        color:
            rgba(255,255,255,.58);

        font-size:.72rem;

        font-style:italic;

        line-height:1.5;

        border-left:
            2px solid
            rgba(13,110,253,.6);

    }

    /*
    |--------------------------------------------------------------------------
    | NOMINAL
    |--------------------------------------------------------------------------
    */

    .nominal-card {

        background:
            linear-gradient(
                135deg,
                rgba(41,25,135,.20) 0%,
                rgba(36,25,135,.05) 100%
            );

        border:
            1px solid
            rgba(42,30,152,.25);

        box-shadow:
            0 8px 20px
            rgba(25,41,135,.15);

    }

    .label-caps {

        font-size:.7rem;

        font-weight:800;

        text-transform:uppercase;

        letter-spacing:.8px;

    }

    /*
    |--------------------------------------------------------------------------
    | VALIDATION INFORMATION
    |--------------------------------------------------------------------------
    */

    .validation-info {

        border-radius:14px;

        padding:14px;

        border:1px solid
            rgba(255,255,255,.07);

        background:
            rgba(255,255,255,.025);

    }

    .validation-info.success {

        border-color:
            rgba(25,135,84,.25);

        background:
            rgba(25,135,84,.04);

    }

    .validation-info.warning {

        border-color:
            rgba(255,193,7,.20);

        background:
            rgba(255,193,7,.035);

    }

    /*
    |--------------------------------------------------------------------------
    | DOCUMENT TABLE
    |--------------------------------------------------------------------------
    */

    .document-table tbody tr {

        transition:
            background .18s ease;

    }

    .document-table tbody tr:hover {

        background:
            rgba(255,255,255,.035);

    }

    .document-table td {

        vertical-align:middle;

    }

    /*
    |--------------------------------------------------------------------------
    | TIMELINE
    |--------------------------------------------------------------------------
    */

    .timeline-main-line {

        width:2px;

        background:
            rgba(255,255,255,.10);

        left:11px;

        top:10px;

        bottom:20px;

        z-index:1;

    }

    .timeline-icon {

        width:24px;

        height:24px;

        min-width:24px;

        border-radius:50%;

        display:flex;

        align-items:center;

        justify-content:center;

        border:
            2px solid

        box-shadow:
            0 2px 6px
            rgba(0,0,0,.2);

        font-size:.5rem;

        position:relative;

        z-index:2;

    }

    .timeline-date {

        font-size:.65rem;

    }

    /*
    |--------------------------------------------------------------------------
    | PDF MODAL
    |--------------------------------------------------------------------------
    */

    .modal-label {

        display:block;

        color:
            rgba(255,255,255,.5);

        text-transform:uppercase;

        letter-spacing:.6px;

        font-weight:600;

        font-size:.58rem;

        margin-bottom:2px;

    }

    .modal-value {

        font-size:.74rem;

        line-height:1.3;

    }

    /*
    |--------------------------------------------------------------------------
    | SHADOW
    |--------------------------------------------------------------------------
    */

    .shadow-glow {

        box-shadow:
            0 0 15px
            rgba(13,110,253,.15);

    }

    /*
    |--------------------------------------------------------------------------
    | BREADCRUMB
    |--------------------------------------------------------------------------
    */

    .breadcrumb-item
    + .breadcrumb-item::before {

        color:
            rgba(255,255,255,.15)!important;

    }

    /*
    |--------------------------------------------------------------------------
    | TEXTAREA
    |--------------------------------------------------------------------------
    */

    textarea:focus {

        outline:none!important;

        box-shadow:none!important;

    }

    .activity-info-list {
        display:flex;
        flex-direction:column;
        gap:12px;
    }

    .activity-info-item {
        display:flex;
        flex-direction:column;
        gap:4px;
    }

    .activity-info-label {
        color:rgba(255,255,255,.62);
        font-size:.7rem;
        font-weight:700;
    }

    .activity-info-value {
        display:block;
        max-width:none;
        color:rgba(255,255,255,.92);
        font-size:.72rem;
        font-weight:400;
        line-height:1.45;
        text-align:left;
        overflow-wrap:anywhere;
    }

    /*
    |--------------------------------------------------------------------------
    | RESPONSIVE
    |--------------------------------------------------------------------------
    */

    @media (max-width:991.98px) {

        .info-value {

            max-width:55%;

        }

    }

    @media (max-width:575.98px) {

        .info-row {

            flex-direction:column;

            gap:5px;

        }

        .info-value {

            max-width:100%;

            text-align:left;

        }

        .document-table {

            min-width:760px;

        }

    }

</style>

@endsection
