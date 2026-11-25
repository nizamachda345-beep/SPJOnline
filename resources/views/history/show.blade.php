{{-- View: history/show --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">

    @php

        // Function: Menyeragamkan label dokumen riwayat
        $normalize = function ($value) {
            $value = strtoupper(trim((string) $value));
            $value = str_replace(['-', '_', '/', '\\'], ' ', $value);
            $value = preg_replace('/\s+/', ' ', $value);
            return trim($value);
        };

        $jenisPencairan = $normalize($submission->jenis_pencairan ?? '');

        // Function: Menentukan label dokumen riwayat
        $documentLabel = function ($doc) use ($normalize) {
            $type = $normalize($doc->jenis_dokumen ?? $doc->jenis_document ?? '');

            return match ($type) {
                'LS', 'DOKUMEN LS' => 'DOKUMEN LS',
                'TU', 'DOKUMEN TU' => 'DOKUMEN TU',
                'GU', 'DOKUMEN GU' => 'DOKUMEN GU',
                'UP', 'DOKUMEN UP' => 'DOKUMEN UP',
                'TU NIHIL', 'DOKUMEN TU NIHIL' => 'DOKUMEN TU NIHIL',
                'GU NIHIL', 'DOKUMEN GU NIHIL' => 'DOKUMEN GU NIHIL',
                'SPJ', 'DOKUMEN SPJ' => 'DOKUMEN SPJ',
                'STS', 'DOKUMEN STS' => 'DOKUMEN STS',
                'SP2D', 'DOKUMEN SP2D' => 'SP2D',
                default => ucwords(strtolower(str_replace('_', ' ', $type ?: 'Dokumen'))),
            };
        };

        // Function: Menentukan deskripsi dokumen riwayat
        $documentDescription = function ($label) {
            return match ($label) {
                'DOKUMEN LS' =>
                    'SPP, Verifikasi SPP/SPM, SPTJM, Billing',
                'DOKUMEN TU',
                'DOKUMEN GU' =>
                    'SPP, Verifikasi SPP/SPM, SPTJM, Persetujuan BUD',
                'DOKUMEN UP' =>
                    'SPP, Verifikasi SPP/SPM, SPTJM',
                'DOKUMEN TU NIHIL' =>
                    'SPP, Verifikasi SPP/SPM, SPTJM, Persetujuan BUD',
                'DOKUMEN SPJ' =>
                    'Dokumen pertanggungjawaban/SPJ',
                'DOKUMEN STS' =>
                    'Surat Tanda Setoran',
                'SP2D' =>
                    'Surat Perintah Pencairan Dana',
                default => null,
            };
        };

        $sp2d = $submission->documents->first(function ($doc) use ($normalize) {
            return $normalize($doc->jenis_dokumen ?? $doc->jenis_document ?? '') === 'SP2D'
                || $normalize($doc->jenis_dokumen ?? $doc->jenis_document ?? '') === 'DOKUMEN SP2D';
        });

        $sp2dStatus = 'Menunggu Verifikasi';
        $sp2dStatusClass = 'status-info';
        $sp2dStatusIcon = 'fa-clock';

        if ($jenisPencairan === 'GU NIHIL') {
            $sp2dStatus = 'Tidak Diperlukan';
            $sp2dStatusClass = 'status-neutral';
            $sp2dStatusIcon = 'fa-minus-circle';
        } elseif (!$sp2d) {
            $sp2dStatus = 'Belum Diupload';
            $sp2dStatusClass = 'status-warning';
            $sp2dStatusIcon = 'fa-cloud-upload-alt';
        } elseif ($sp2d->status_dokumen === 'verified') {
            $sp2dStatus = 'Terverifikasi';
            $sp2dStatusClass = 'status-success';
            $sp2dStatusIcon = 'fa-check-circle';
        } elseif ($sp2d->status_dokumen === 'revisi') {
            $sp2dStatus = 'Revisi';
            $sp2dStatusClass = 'status-danger';
            $sp2dStatusIcon = 'fa-exclamation-circle';
        }

        $status = strtolower(trim($submission->status ?? ''));
        $statusLabel = match ($status) {
            'disetujui' => 'Disetujui',
            'selesai', 'history' => 'Selesai',
            'revisi', 'direvisi' => 'Perlu Perbaikan',
            'pending' => 'Menunggu Verifikasi',
            'proses validasi' => 'Proses Validasi',
            default => ucwords(str_replace('_', ' ', $submission->status ?? '-')),
        };

        $statusClass = match ($status) {
            'disetujui', 'selesai', 'history' => 'status-success',
            'revisi', 'direvisi' => 'status-danger',
            'pending' => 'status-warning',
            'proses validasi' => 'status-info',
            default => 'status-neutral',
        };
    @endphp

    <div class="card glass-card-dark border-0 rounded-4 p-4 mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center">
                <div class="icon-box bg-primary bg-opacity-50 p-2 rounded-4 me-3">
                    <i class="fas fa-file-invoice fs-4 text-white"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1 text-white tracking-tight">
                        Detail Arsip Dokumen
                    </h4>
                    <p class="text-light opacity-50 small mb-0">
                        SPP {{ $submission->nomor_spp ?? '-' }}
                        <span class="mx-1">•</span>
                        SPM {{ $submission->nomor_spm ?? '-' }}
                        <span class="mx-1">•</span>
                        {{ $submission->created_at ? $submission->created_at->timezone('Asia/Makassar')->format('d M Y') : '-' }}
                    </p>
                </div>
            </div>

            <a href="{{ route('history.index') }}"
               class="btn btn-outline-light btn-sm rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i>
                Kembali ke History
            </a>
        </div>
    </div>

    <div class="row g-4 align-items-stretch">

        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4 glass-card-dark overflow-hidden h-100">

                <div class="glass-header-dark border-0 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-between gap-3">
                        <div>
                            <div class="d-flex align-items-center mb-1">
                                <i class="fas fa-folder-open text-primary me-2"></i>
                                <h6 class="fw-bold text-white mb-0">
                                    Dokumen Digital
                                </h6>
                            </div>
                            <p class="text-white-50 small mb-0">
                                Daftar dokumen yang tersimpan pada arsip pengajuan.
                            </p>
                        </div>

                        <span class="badge rounded-pill bg-primary bg-opacity-10
                                     border border-primary border-opacity-25
                                     text-primary px-3 py-2">
                            <i class="fas fa-file-alt me-1"></i>
                            {{ $submission->documents->count() }} Dokumen
                        </span>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dark align-middle mb-0 document-table">
                            <thead>
                                <tr class="text-white-50 small text-uppercase"
                                    style="letter-spacing:.8px;background:rgba(0,0,0,.22);">
                                    <th class="ps-4 py-3 border-0" style="width:44%;">
                                        Dokumen
                                    </th>
                                    <th class="text-center border-0" style="width:16%;">
                                        Status
                                    </th>
                                    <th class="text-center border-0" style="width:13%;">
                                        Lihat
                                    </th>
                                    <th class="text-center border-0 pe-4" style="width:13%;">
                                        Unduh
                                    </th>
                                </tr>
                            </thead>

                           <tbody>
                                @forelse($submission->documents as $doc)

                                    @php

                                        $filePathUrl = route('storage.file', ['path' => $doc->file_path]);

                                        $isImageDoc = preg_match(
                                            '/\.(jpg|jpeg|png|webp|gif)$/i',
                                            $doc->file_path
                                        );

                                        $rawDocumentType = $doc->jenis_dokumen
                                            ?? $doc->jenis_document
                                            ?? '';

                                        $normalizedDocumentType = strtoupper(
                                            trim(
                                                preg_replace(
                                                    '/\s+/',
                                                    ' ',
                                                    str_replace(['_', '-'], ' ', $rawDocumentType)
                                                )
                                            )
                                        );

                                        $displayName = $normalizedDocumentType;

                                        if (str_contains($normalizedDocumentType, 'TU NIHIL')) {
                                            $displayName = 'DOKUMEN TU NIHIL';

                                        } elseif (str_contains($normalizedDocumentType, 'GU NIHIL')) {
                                            $displayName = 'DOKUMEN SPJ';

                                        } elseif (
                                            str_contains($normalizedDocumentType, 'DOKUMEN LS')
                                            || $normalizedDocumentType === 'LS'
                                        ) {
                                            $displayName = 'DOKUMEN LS';

                                        } elseif (
                                            str_contains($normalizedDocumentType, 'DOKUMEN TU')
                                            || $normalizedDocumentType === 'TU'
                                        ) {
                                            $displayName = 'DOKUMEN TU';

                                        } elseif (
                                            str_contains($normalizedDocumentType, 'DOKUMEN GU')
                                            || $normalizedDocumentType === 'GU'
                                        ) {
                                            $displayName = 'DOKUMEN GU';

                                        } elseif (
                                            str_contains($normalizedDocumentType, 'DOKUMEN UP')
                                            || $normalizedDocumentType === 'UP'
                                        ) {
                                            $displayName = 'DOKUMEN UP';

                                        } elseif (str_contains($normalizedDocumentType, 'SP2D')) {
                                            $displayName = 'SP2D';

                                        } elseif (str_contains($normalizedDocumentType, 'SPJ')) {
                                            $displayName = 'DOKUMEN SPJ';

                                        } elseif (str_contains($normalizedDocumentType, 'STS')) {
                                            $displayName = 'DOKUMEN STS';
                                        }

                                        $description = match ($displayName) {
                                            'DOKUMEN LS'
                                                => 'SPP, Verifikasi SPP/SPM, SPTJM, Billing',

                                            'DOKUMEN TU'
                                                => 'SPP, Verifikasi SPP/SPM, SPTJM, Persetujuan BUD',

                                            'DOKUMEN GU'
                                                => 'SPP, Verifikasi SPP/SPM, SPTJM, Persetujuan BUD',

                                            'DOKUMEN UP'
                                                => 'SPP, Verifikasi SPP/SPM, SPTJM',

                                            'DOKUMEN TU NIHIL'
                                                => 'SPP, Verifikasi SPP/SPM, SPTJM, Persetujuan BUD',

                                            'DOKUMEN SPJ'
                                                => 'Dokumen Surat Pertanggungjawaban',

                                            'DOKUMEN STS'
                                                => 'Surat Tanda Setoran',

                                            'SP2D'
                                                => 'Surat Perintah Pencairan Dana',

                                            default
                                                => null,
                                        };
                                    @endphp

                                    <tr class="document-row border-bottom border-white border-opacity-10">

                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-start">

                                                <div class="text-danger me-3 pt-1">
                                                    <i class="{{ $isImageDoc ? 'far fa-file-image' : 'far fa-file-pdf' }} fs-4"></i>
                                                </div>

                                                <div class="min-width-0">

                                                    <div class="fw-bold text-white mb-1 document-name">
                                                        {{ $displayName }}
                                                    </div>

                                                    @if($description)
                                                        <div class="text-white-50 document-description">
                                                            {{ $description }}
                                                        </div>
                                                    @endif

                                                </div>
                                            </div>
                                        </td>

                                        <td class="text-center py-3">

                                            @if($doc->status_dokumen === 'verified')

                                                <span class="status-text text-success">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    Terverifikasi
                                                </span>

                                            @elseif($doc->status_dokumen === 'revisi')

                                                <span class="status-text text-danger">
                                                    <i class="fas fa-exclamation-circle me-1"></i>
                                                    Revisi
                                                </span>

                                            @elseif($doc->status_dokumen === 'approved')

                                                <span class="status-text text-info">
                                                    <i class="fas fa-check me-1"></i>
                                                    Disetujui
                                                </span>

                                            @else

                                                <span class="status-text text-warning">
                                                    <i class="far fa-clock me-1"></i>
                                                    Menunggu
                                                </span>

                                            @endif

                                        </td>

                                        <td class="text-center py-3">

                                            <button type="button"
                                                    class="btn btn-sm btn-glass-primary rounded-circle btn-view-doc"
                                                    data-file-path="{{ $filePathUrl }}"
                                                    data-file-name="{{ $displayName }}"
                                                    title="Lihat dokumen">

                                                <i class="fas fa-eye"></i>

                                            </button>

                                        </td>

                                        <td class="text-center py-3 pe-4">

                                            <a href="{{ $filePathUrl }}"
                                            download
                                            class="btn btn-sm btn-outline-success"
                                            title="Unduh dokumen">

                                                <i class="fas fa-download"></i>

                                            </a>

                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="4"
                                            class="text-center py-5 text-white-50">

                                            <i class="fas fa-folder-open fs-2 mb-2 d-block"></i>

                                            Belum terdapat dokumen pada pengajuan ini.

                                        </td>
                                    </tr>

                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="px-4 py-3 border-top border-white border-opacity-10">
                    <div class="d-flex align-items-center text-white-50 small">
                        <i class="fas fa-shield-halved text-success me-2"></i>
                        Arsip ini bersifat <strong class="text-white mx-1">read-only</strong>.
                        Tidak ada perubahan data dari halaman History.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden glass-card-dark h-100">

                <div class="card-body px-4 py-3">

                    <div class="info-section p-3 rounded-4 mb-3">
                        <span class="section-label text-primary">
                            Instansi Pengaju
                        </span>

                        <div class="fw-bold text-white mb-2">
                            {{ $submission->department->nama_dinas ?? '-' }}
                        </div>

                        <div class="text-light small">
                            <i class="fas fa-calendar-day me-2 text-primary opacity-75"></i>
                            Diajukan:
                            {{ $submission->created_at
                                ? $submission->created_at->timezone('Asia/Makassar')->format('d M Y, H:i')
                                : '-' }}
                        </div>
                    </div>

                    <div class="card info-collapse-card mb-3">

                        <div class="card-header info-card-header p-0 border-0">
                            <button type="button"
                                    class="info-section-toggle w-100 collapsed"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#dataPengajuanCollapse"
                                    aria-expanded="false"
                                    aria-controls="dataPengajuanCollapse">

                                <div class="d-flex align-items-center">
                                    <div class="info-section-icon">
                                        <i class="fas fa-file-invoice"></i>
                                    </div>

                                    <div class="text-start">
                                        <div class="info-section-title">
                                            Data Pengajuan
                                        </div>
                                        <small class="text-white-50">
                                            Detail data pengajuan dan status dokumen
                                        </small>
                                    </div>
                                </div>

                                <i class="fas fa-chevron-down collapse-chevron"></i>

                            </button>
                        </div>

                        <div id="dataPengajuanCollapse"
                            class="collapse">

                            <div class="card-body info-card-body">

                                <div class="info-row">
                                    <span class="info-label">Jenis Pencairan</span>
                                    <span class="info-value">
                                        {{ $submission->jenis_pencairan ?? '-' }}
                                    </span>
                                </div>

                                <div class="info-row">
                                    <span class="info-label">Nomor SPP</span>
                                    <span class="info-value">
                                        {{ $submission->nomor_spp ?? '-' }}
                                    </span>
                                </div>

                                <div class="info-row">
                                    <span class="info-label">Nomor SPM</span>
                                    <span class="info-value">
                                        {{ $submission->nomor_spm ?? '-' }}
                                    </span>
                                </div>

                                <div class="info-row">
                                    <span class="info-label">Tanggal Berkas</span>
                                    <span class="info-value">
                                        @if($submission->tanggal_spp)
                                            {{ \Carbon\Carbon::parse($submission->tanggal_spp)->format('d/m/Y') }}
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>

                                <div class="info-row">
                                    <span class="info-label">Status Pengajuan</span>
                                    <span class="info-value">
                                        <span class="status-badge status-approved">
                                            <i class="fas fa-check-circle me-1"></i>
                                            Disetujui
                                        </span>
                                    </span>
                                </div>

                                <div class="info-row">
                                    <span class="info-label">Status SP2D</span>
                                    <span class="info-value">

                                        @if($jenisPencairan === 'GU NIHIL')

                                            <span class="status-badge status-neutral">
                                                Tidak Diperlukan
                                            </span>

                                        @elseif(!$sp2d)

                                            <span class="status-badge status-warning">
                                                <i class="fas fa-cloud-upload-alt me-1"></i>
                                                Belum Diupload
                                            </span>

                                        @elseif($sp2d->status_dokumen === 'verified')

                                            <span class="status-badge status-approved">
                                                <i class="fas fa-check-circle me-1"></i>
                                                Terverifikasi
                                            </span>

                                        @elseif($sp2d->status_dokumen === 'revisi')

                                            <span class="status-badge status-revision">
                                                <i class="fas fa-exclamation-circle me-1"></i>
                                                Revisi
                                            </span>

                                        @else

                                            <span class="status-badge status-info">
                                                <i class="fas fa-clock me-1"></i>
                                                Menunggu Verifikasi
                                            </span>

                                        @endif

                                    </span>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="card info-collapse-card mb-3">

                        <div class="card-header info-card-header p-0 border-0">
                            <button type="button"
                                    class="info-section-toggle w-100 collapsed"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#informasiKegiatanCollapse"
                                    aria-expanded="false"
                                    aria-controls="informasiKegiatanCollapse">

                                <div class="d-flex align-items-center">
                                    <div class="info-section-icon">
                                        <i class="fas fa-tasks"></i>
                                    </div>

                                    <div class="text-start">
                                        <div class="info-section-title">
                                            Informasi Kegiatan
                                        </div>
                                        <small class="text-white-50">
                                            Detail kegiatan dan perihal pengajuan
                                        </small>
                                    </div>
                                </div>

                                <i class="fas fa-chevron-down collapse-chevron"></i>

                            </button>
                        </div>

                        <div id="informasiKegiatanCollapse"
                            class="collapse">

                            <div class="card-body info-card-body">

                                <div class="info-row">
                                    <span class="info-label">Kegiatan</span>
                                    <span class="info-value text-break">
                                        {{ $submission->judul_kegiatan ?? '-' }}
                                    </span>
                                </div>

                                <div class="info-row">
                                    <span class="info-label">Sub Kegiatan</span>
                                    <span class="info-value text-break">
                                        {{ $submission->judul_sub_kegiatan ?? '-' }}
                                    </span>
                                </div>

                                <div class="info-row">
                                    <span class="info-label">Perihal</span>
                                    <span class="info-value text-break">
                                        {{ $submission->perihal ?? '-' }}
                                    </span>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="info-section p-3 rounded-4">
                        <div class="info-section-title mb-3">
                            <i class="fas fa-money-bill-wave me-2"></i>
                            Informasi Keuangan
                        </div>

                        <div class="info-row">
                            <span class="info-label">Sumber Dana</span>
                            <span class="info-value">
                                {{ $submission->sumber_dana ?? '-' }}
                            </span>
                        </div>

                        <div class="info-total">
                            <span class="info-total-label">
                                Total Permintaan
                            </span>
                            <strong>
                                Rp {{ number_format($submission->total_pencairan ?? 0, 0, ',', '.') }}
                            </strong>
                        </div>
                    </div>

                    @if(!empty($submission->validated_at))
                        <div class="mt-3 text-white-50 small">
                            <i class="fas fa-check-double text-success me-2"></i>
                            Disahkan:
                            {{ \Carbon\Carbon::parse($submission->validated_at)->timezone('Asia/Makassar')->format('d M Y, H:i') }}
                        </div>
                    @endif

                </div>
            </div>
        </div>

    </div>
</div>

{{-- Modal: Pratinjau dokumen riwayat pengajuan --}}
<div class="modal fade" id="documentViewerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width:95vw;">
        <div class="modal-content bg-dark text-white border-0 rounded-4 overflow-hidden"
             style="height:90vh;">

            <div class="modal-body p-0 d-flex flex-column flex-lg-row h-100">

                <div class="flex-grow-1 bg-black bg-opacity-50 position-relative
                            h-100 d-flex align-items-center justify-content-center overflow-hidden">

                    <div id="documentLoader"
                         class="position-absolute top-50 start-50 translate-middle text-center">
                        <div class="spinner-border text-primary"></div>
                        <div class="small text-white-50 mt-2">Memuat dokumen...</div>
                    </div>

                    <iframe id="documentFrame"
                            style="width:100%;height:100%;border:0;display:none;"
                            src=""></iframe>

                    <img id="imageFrame"
                         style="max-width:100%;max-height:100%;object-fit:contain;display:none;"
                         alt="Pratinjau dokumen">
                </div>

                <div class="preview-sidebar p-3 d-flex flex-column border-start
                            border-white border-opacity-10"
                     style="width:290px;min-width:290px;">

                    <div class="d-flex justify-content-between align-items-center">
                        <h6 id="documentViewerModalLabel"
                            class="fw-bold text-white text-uppercase mb-0 text-truncate">
                            PRATINJAU DOKUMEN
                        </h6>

                        <button type="button"
                                class="btn-close btn-close-white"
                                data-bs-dismiss="modal"></button>
                    </div>

                    <hr class="border-white border-opacity-10">

                    <div class="mb-3">
                        <span class="modal-label">Nomor SPP / SPM</span>
                        <div class="modal-value text-white">
                            {{ $submission->nomor_spp ?? '-' }}
                            /
                            {{ $submission->nomor_spm ?? '-' }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <span class="modal-label">Kegiatan</span>
                        <div class="modal-value text-white text-break">
                            {{ $submission->judul_kegiatan ?? '-' }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <span class="modal-label">Perihal</span>
                        <div class="modal-value text-white-50 text-break">
                            {{ $submission->perihal ?? '-' }}
                        </div>
                    </div>

                    <div class="p-3 rounded-3 mt-1 preview-total">
                        <span class="modal-label text-success">
                            Total Nilai Pencairan
                        </span>
                        <div class="fw-bold text-white fs-5">
                            Rp {{ number_format($submission->total_pencairan ?? 0, 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="alert alert-primary bg-primary bg-opacity-10
                                border-0 text-white-50 rounded-3 mt-3 small">
                        <i class="fas fa-info-circle text-primary me-1"></i>
                        Dokumen ditampilkan dalam mode baca.
                    </div>

                    <div class="mt-auto d-grid gap-2">
                        <a id="downloadLink"
                           href="#"
                           target="_blank"
                           class="btn btn-outline-success rounded-pill fw-bold btn-sm">
                            <i class="fas fa-download me-1"></i>
                            Buka / Unduh Dokumen
                        </a>

                        <button type="button"
                                class="btn btn-primary rounded-pill fw-bold btn-sm"
                                data-bs-dismiss="modal">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalElement = document.getElementById('documentViewerModal');
    if (!modalElement) return;

    const modal = new bootstrap.Modal(modalElement);
    const buttons = document.querySelectorAll('.btn-view-doc');

    const frame = document.getElementById('documentFrame');
    const image = document.getElementById('imageFrame');
    const loader = document.getElementById('documentLoader');
    const title = document.getElementById('documentViewerModalLabel');
    const download = document.getElementById('downloadLink');

    buttons.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();

            const filePath = this.dataset.filePath;
            const fileName = this.dataset.fileName || 'Dokumen';

            frame.src = '';
            image.src = '';
            frame.style.display = 'none';
            image.style.display = 'none';

            if (loader) loader.style.display = 'block';

            title.textContent = fileName;

            if (download) {
                download.href = filePath;
                download.setAttribute('download', fileName);
            }

            const isImage = /\.(jpg|jpeg|png|webp|gif)(\?.*)?$/i.test(filePath);

            if (isImage) {
                image.src = filePath;
                image.onload = function () {
                    if (loader) loader.style.display = 'none';
                    image.style.display = 'block';
                };
            } else {
                frame.src = filePath;
                frame.onload = function () {
                    if (loader) loader.style.display = 'none';
                    frame.style.display = 'block';
                };
            }

            modal.show();
        });
    });

    modalElement.addEventListener('hidden.bs.modal', function () {
        frame.src = '';
        image.src = '';
        frame.style.display = 'none';
        image.style.display = 'none';

        if (loader) loader.style.display = 'block';
    });
});
</script>





<style>

/* ==========================================================
   HEADER / GENERAL
   ========================================================== */

.glass-header-dark {
    background: rgba(255,255,255,.025);
    border-bottom: 1px solid rgba(255,255,255,.06);
}

.icon-box {
    width: 46px;
    height: 46px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 14px;
}

.icon-box-sm {
    width: 34px;
    height: 34px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.tracking-tight {
    letter-spacing: -.25px;
}

/* ==========================================================
   DOCUMENT TABLE
   ========================================================== */

.document-table tbody tr {
    transition: background .18s ease;
}

.document-table tbody tr:hover {
    background: rgba(255,255,255,.035);
}

.document-table td {
    vertical-align: middle;
}

.document-name {
    font-size: .75rem;
    line-height: 1.4;
}

.document-description {
    font-size: .64rem;
    line-height: 1.45;
}

.document-format {
    font-size: .61rem;
}

.status-text {
    font-size: .68rem;
    font-weight: 600;
    white-space: nowrap;
}

/* ==========================================================
   GLASS ACTION BUTTON
   ========================================================== */

.btn-glass-primary,
.btn-glass-success {
    width: 32px;
    height: 32px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    border-radius: 50%;

    transition: all .2s ease;
}

.btn-glass-primary {
    background: rgba(13,110,253,.08);
    border: 1px solid rgba(13,110,253,.25);
    color: #6ea8fe;
}

.btn-glass-primary:hover {
    background: rgba(13,110,253,.20);
    color: #fff;
    transform: translateY(-1px);
}

.btn-glass-success {
    background: rgba(25,135,84,.08);
    border: 1px solid rgba(25,135,84,.25);
    color: #75d6a5;
}

.btn-glass-success:hover {
    background: rgba(25,135,84,.20);
    color: #fff;
    transform: translateY(-1px);
}

/* ==========================================================
   BASIC INFORMATION SECTION
   ========================================================== */

.info-section {
    background: rgba(255,255,255,.03);
    border: 1px solid rgba(255,255,255,.05);
}

.section-label {
    display: block;

    text-transform: uppercase;

    font-size: .61rem;
    font-weight: 800;

    letter-spacing: .8px;

    margin-bottom: 7px;
}

/* ==========================================================
   INFORMATION CARD
   ========================================================== */

.info-collapse-card {
    background: rgba(255,255,255,.035);

    border: 1px solid rgba(255,255,255,.08);

    border-radius: 14px;

    overflow: hidden;

    box-shadow: 0 8px 24px rgba(0,0,0,.12);
}

.info-card-header {
    background: transparent !important;
    border: 0 !important;
    padding: 0 !important;
}

/* ==========================================================
   INFORMATION CARD TOGGLE
   ========================================================== */

.info-section-toggle {
    width: 100%;
    min-height: 68px;

    display: flex;
    align-items: center;
    justify-content: space-between;

    padding: 14px 17px;

    background: transparent;
    border: 0;

    color: #fff;

    text-align: left;

    cursor: pointer;

    transition:
        background-color .2s ease,
        color .2s ease;
}

.info-section-toggle:hover {
    background: rgba(255,255,255,.025);
}

.info-section-toggle:focus {
    outline: none;
    box-shadow: none;
}

.info-section-toggle:active {
    background: rgba(255,255,255,.035);
}

/* ==========================================================
   INFORMATION CARD ICON
   ========================================================== */

.info-section-icon {
    width: 36px;
    height: 36px;

    display: inline-flex;
    align-items: center;
    justify-content: center;

    margin-right: 11px;

    border-radius: 9px;

    background: rgba(13,110,253,.12);

    color: #4d9cff;

    font-size: .85rem;

    flex-shrink: 0;

    transition:
        background-color .2s ease,
        color .2s ease;
}

.info-section-toggle:hover .info-section-icon {
    background: rgba(13,110,253,.17);
    color: #6ea8fe;
}

/* ==========================================================
   INFORMATION CARD TITLE
   ========================================================== */

.info-section-title {
    display: block;

    color: #fff;

    font-size: .88rem;
    font-weight: 700;

    line-height: 1.2;

    letter-spacing: .1px;
}

.info-section-subtitle {
    margin-top: 3px;

    color: rgba(255,255,255,.42);

    font-size: .70rem;
    font-weight: 400;

    line-height: 1.3;
}

/* ==========================================================
   COLLAPSE CHEVRON
   ========================================================== */

.collapse-chevron {
    color: rgba(255,255,255,.50);

    font-size: .72rem;

    transition:
        transform .25s ease,
        color .2s ease;
}

/* COLLAPSED */
.info-section-toggle.collapsed .collapse-chevron {
    transform: rotate(0deg);
}

/* EXPANDED */
.info-section-toggle:not(.collapsed) .collapse-chevron {
    transform: rotate(180deg);
    color: #4d9cff;
}

/* ==========================================================
   INFORMATION CARD BODY
   ========================================================== */

.info-card-body {
    padding: 4px 17px 16px;

    border-top: 1px solid rgba(255,255,255,.06);
}

/* ==========================================================
   INFORMATION ROW
   ========================================================== */

.info-row {
    display: flex;

    align-items: flex-start;
    justify-content: space-between;

    gap: 20px;

    padding: 10px 2px;

    border-bottom: 1px solid rgba(255,255,255,.05);
}

.info-row:last-child {
    border-bottom: 0;
}

/* ==========================================================
   INFORMATION LABEL
   ========================================================== */

.info-label {
    color: rgba(255,255,255,.48);

    font-size: .75rem;
    font-weight: 400;

    line-height: 1.5;

    flex-shrink: 0;
}

/* ==========================================================
   INFORMATION VALUE
   ========================================================== */

.info-value {
    max-width: 65%;

    color: rgba(255,255,255,.90);

    font-size: .78rem;
    font-weight: 600;

    line-height: 1.5;

    text-align: right;

    word-break: break-word;
}

/* ==========================================================
   INFORMATION BLOCK
   ========================================================== */

.info-block {
    padding-bottom: 11px;

    border-bottom: 1px solid rgba(255,255,255,.07);
}

/* ==========================================================
   PERIHAL
   ========================================================== */

.perihal-box {
    padding-left: 11px;

    color: rgba(255,255,255,.62);

    font-size: .69rem;
    line-height: 1.5;

    border-left: 2px solid rgba(13,110,253,.6);
}

/* ==========================================================
   JENIS PENCAIRAN BADGE
   ========================================================== */

.jenis-badge {
    display: inline-block;

    padding: .35rem .6rem;

    border-radius: 999px;

    background: rgba(13,110,253,.10);

    border: 1px solid rgba(13,110,253,.25);

    color: #8bb8ff;

    font-size: .61rem;
    font-weight: 800;

    letter-spacing: .3px;
}

/* ==========================================================
   STATUS BADGE
   ========================================================== */

.status-badge {
    display: inline-flex;

    align-items: center;
    justify-content: center;

    min-height: 25px;

    padding: 4px 9px;

    border-radius: 6px;

    font-size: .68rem;
    font-weight: 600;

    line-height: 1.2;

    white-space: nowrap;

    border: 1px solid transparent;
}

/* STATUS APPROVED / VERIFIED */

.status-approved {
    color: #65d98b;

    background: rgba(25,135,84,.12);

    border-color: rgba(25,135,84,.20);
}

/* STATUS SUCCESS - dipertahankan untuk elemen lama */

.status-success {
    color: #75d6a5;

    background: rgba(25,135,84,.10);

    border-color: rgba(25,135,84,.22);
}

/* STATUS WARNING / BELUM UPLOAD */

.status-warning {
    color: #ffc107;

    background: rgba(255,193,7,.10);

    border-color: rgba(255,193,7,.18);
}

/* STATUS DANGER - dipertahankan untuk elemen lama */

.status-danger {
    color: #ff8f9b;

    background: rgba(220,53,69,.08);

    border-color: rgba(220,53,69,.20);
}

/* STATUS REVISION */

.status-revision {
    color: #ff6b6b;

    background: rgba(220,53,69,.10);

    border-color: rgba(220,53,69,.18);
}

/* STATUS INFO */

.status-info {
    color: #6ea8fe;

    background: rgba(13,110,253,.10);

    border-color: rgba(13,110,253,.18);
}

/* STATUS NEUTRAL */

.status-neutral {
    color: rgba(255,255,255,.55);

    background: rgba(255,255,255,.05);

    border-color: rgba(255,255,255,.10);
}

/* ==========================================================
   TOTAL INFORMATION
   ========================================================== */

.info-total {
    margin-top: 12px;

    padding: 13px;

    border-radius: 12px;

    background: linear-gradient(
        135deg,
        rgba(13,110,253,.12),
        rgba(13,110,253,.035)
    );

    border: 1px solid rgba(13,110,253,.18);

    display: flex;

    justify-content: space-between;
    align-items: center;

    gap: 12px;
}

.info-total-label {
    color: rgba(255,255,255,.55);

    text-transform: uppercase;

    letter-spacing: .7px;

    font-size: .60rem;
    font-weight: 800;
}

.info-total strong {
    color: #fff;

    font-size: 1rem;

    white-space: nowrap;
}

/* ==========================================================
   PREVIEW SIDEBAR
   ========================================================== */

.preview-sidebar {
    background: rgba(255,255,255,.025);
}

.preview-total {
    background: rgba(25,135,84,.06);

    border: 1px solid rgba(25,135,84,.14);
}

/* ==========================================================
   PREVIEW MODAL
   ========================================================== */

.modal-label {
    display: block;

    color: rgba(255,255,255,.48);

    text-transform: uppercase;

    letter-spacing: .6px;

    font-weight: 700;

    font-size: .57rem;

    margin-bottom: 3px;
}

.modal-value {
    font-size: .73rem;

    line-height: 1.35;
}

/* ==========================================================
   BOOTSTRAP COLLAPSE
   ========================================================== */

.info-collapse-card .collapse {
    transition: height .25s ease;
}

/* ==========================================================
   DESKTOP / TABLET
   ========================================================== */

@media (max-width: 991.98px) {

    .info-value {
        max-width: 58%;
    }

    .preview-sidebar {
        width: 100% !important;
        min-width: 100% !important;

        height: auto !important;

        max-height: 40%;

        border-left: 0 !important;

        border-top: 1px solid rgba(255,255,255,.08);
    }
}

/* ==========================================================
   MOBILE
   ========================================================== */

@media (max-width: 575.98px) {

    .info-section-toggle {
        min-height: 62px;

        padding: 13px 14px;
    }

    .info-section-icon {
        width: 33px;
        height: 33px;

        margin-right: 9px;

        font-size: .78rem;
    }

    .info-section-title {
        font-size: .84rem;
    }

    .info-section-subtitle {
        font-size: .66rem;
    }

    .info-card-body {
        padding: 4px 14px 14px;
    }

    .info-row {
        flex-direction: column;

        gap: 4px;

        padding: 9px 2px;
    }

    .info-label {
        font-size: .73rem;
    }

    .info-value {
        max-width: 100%;

        font-size: .77rem;

        text-align: left;
    }

    .status-badge {
        font-size: .66rem;
    }

    .document-table {
        min-width: 700px;
    }

    .card-body {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
}

</style>

@endsection
