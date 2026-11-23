{{-- View: validasi/show --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">

    @php

        // Function: Menyeragamkan jenis dokumen untuk validasi
        $normalizeDocumentType = function ($type) {
            $type = strtoupper(trim((string) $type));
            $type = str_replace(['-', '_', '/', '\\'], ' ', $type);
            $type = preg_replace('/\s+/', ' ', $type);
            return trim($type);
        };

        $jenisPencairan = strtoupper(trim((string) $submission->jenis_pencairan));
        $jenisPencairan = str_replace(['-', '_', '/', '\\'], ' ', $jenisPencairan);
        $jenisPencairan = preg_replace('/\s+/', ' ', $jenisPencairan);
        $jenisPencairan = trim($jenisPencairan);

        switch ($jenisPencairan) {
            case 'LS':
                $requiredKeys = ['DOKUMEN LS', 'DOKUMEN SPJ'];
                break;

            case 'TU':
                $requiredKeys = ['DOKUMEN TU'];
                break;

            case 'GU':
                $requiredKeys = ['DOKUMEN GU'];
                break;

            case 'UP':
                $requiredKeys = ['DOKUMEN UP', 'SP2D'];
                break;

            case 'TU NIHIL':
                $requiredKeys = ['DOKUMEN TU NIHIL', 'DOKUMEN SPJ', 'SP2D'];
                break;

            case 'GU NIHIL':
                $requiredKeys = ['DOKUMEN SPJ'];
                break;

            default:
                $requiredKeys = [];
                break;
        }

        $documentMeta = [];

        foreach ($submission->documents as $doc) {
            $rawDocumentType = $doc->jenis_dokumen ?? $doc->jenis_document ?? '';
            $normalized = $normalizeDocumentType($rawDocumentType);

            if ($normalized === 'DOKUMEN LS' || $normalized === 'LS') {
                $validationKey = 'DOKUMEN LS';
            } elseif ($normalized === 'DOKUMEN TU NIHIL' || $normalized === 'TU NIHIL') {
                $validationKey = 'DOKUMEN TU NIHIL';
            } elseif ($normalized === 'DOKUMEN TU' || $normalized === 'TU') {
                $validationKey = 'DOKUMEN TU';
            } elseif ($normalized === 'DOKUMEN GU NIHIL' || $normalized === 'GU NIHIL') {
                $validationKey = 'DOKUMEN GU NIHIL';
            } elseif ($normalized === 'DOKUMEN GU' || $normalized === 'GU') {
                $validationKey = 'DOKUMEN GU';
            } elseif ($normalized === 'DOKUMEN UP' || $normalized === 'UP') {
                $validationKey = 'DOKUMEN UP';
            } elseif ($normalized === 'DOKUMEN SPJ' || $normalized === 'SPJ') {
                $validationKey = 'DOKUMEN SPJ';
            } elseif ($normalized === 'DOKUMEN STS' || $normalized === 'STS') {
                $validationKey = 'STS';
            } elseif ($normalized === 'DOKUMEN SP2D' || $normalized === 'SP2D') {
                $validationKey = 'SP2D';
            } else {
                $validationKey = $normalized;
            }

            $documentMeta[$doc->id] = [
                'key'       => $validationKey,
                'required'  => in_array($validationKey, $requiredKeys, true),
                'optional'  => $validationKey === 'STS',
                'next_step' => (
                    $validationKey === 'SP2D' &&
                    in_array($jenisPencairan, ['LS', 'TU', 'GU'], true)
                ),
            ];
        }

        $documentsByValidationKey = collect();

        foreach ($submission->documents as $doc) {
            $meta = $documentMeta[$doc->id] ?? null;

            if (!$meta) {
                continue;
            }

            if (!$documentsByValidationKey->has($meta['key'])) {
                $documentsByValidationKey->put($meta['key'], $doc);
            }
        }

        $requiredDocuments = collect();

        foreach ($requiredKeys as $requiredKey) {
            if ($documentsByValidationKey->has($requiredKey)) {
                $requiredDocuments->push(
                    $documentsByValidationKey->get($requiredKey)
                );
            }
        }

        $totalRequired = $requiredDocuments->count();

        $verifiedRequired = $requiredDocuments
            ->where('status_dokumen', 'verified')
            ->count();

        $notVerifiedRequired = $requiredDocuments
            ->filter(fn ($doc) => $doc->status_dokumen !== 'verified')
            ->values();

        $percent = $totalRequired > 0
            ? round(($verifiedRequired / $totalRequired) * 100)
            : 0;

        $isAllValidated = (
            $totalRequired > 0 &&
            $verifiedRequired === $totalRequired
        );

        $optionalDocuments = collect();

        foreach ($submission->documents as $doc) {
            $meta = $documentMeta[$doc->id] ?? null;

            if ($meta && $meta['optional']) {
                $optionalDocuments->push($doc);
            }
        }

        $nextStepDocuments = collect();

        foreach ($submission->documents as $doc) {
            $meta = $documentMeta[$doc->id] ?? null;

            if ($meta && $meta['next_step']) {
                $nextStepDocuments->push($doc);
            }
        }

        $hasRevision = $submission->documents
            ->where('status_dokumen', 'revisi')
            ->count() > 0;

        $allDocumentsCount = $submission->documents->count();
    @endphp

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 glass-header-dark position-relative">

        <div class="glass-reflection"></div>

        <div class="card-body p-3 p-lg-4">

            <div class="row align-items-center">

                <div class="col-md-8">

                    <div class="d-flex align-items-center">

                        <div class="bg-primary-soft text-primary rounded-3
                                    d-flex align-items-center justify-content-center me-3"
                             style="width:48px;
                                height:48px;
                                background:rgba(34,42,197,.12);
                                border:1px solid rgba(34,42,197,.18);">

                            <i class="fas fa-file-circle-check fs-4"></i>

                        </div>

                        <div>

                            <h4 class="fw-black text-light mb-1 tracking-tight">
                                Detail Validasi Permintaan
                            </h4>

                            <nav aria-label="breadcrumb">

                                <ol class="breadcrumb mb-0 extra-small">

                                    <li class="breadcrumb-item">
                                        <a href="{{ route('validasi.index') }}"
                                           class="text-white-50 text-decoration-none">
                                            Validasi Permintaan
                                        </a>
                                    </li>

                                    <li class="breadcrumb-item active fw-medium text-primary">
                                        Detail Berkas
                                    </li>

                                </ol>

                            </nav>

                        </div>

                    </div>

                </div>

                <div class="col-md-4 text-md-end mt-3 mt-md-0">

                    <a href="{{ route('validasi.index') }}"
                       class="btn btn-glass-action rounded-pill px-3 py-2 text-light">

                        <i class="fas fa-arrow-left me-2"></i>
                        Kembali

                    </a>

                </div>

            </div>

        </div>

    </div>

    <div class="row g-4 mb-4">

        <div class="col-lg-8">

            <div class="card border-0 shadow-lg rounded-4
                        glass-card-dark overflow-hidden h-100">

                <div class="glass-header-dark border-0 py-3 px-4">

                    <div class="d-flex justify-content-between
                                align-items-start flex-wrap gap-3">

                        <div>

                            <div class="d-flex align-items-center mb-1">

                                <i class="fas fa-folder-open text-primary me-2"></i>

                                <h6 class="fw-bold text-white mb-0">
                                    Lampiran Dokumen Digital
                                </h6>

                            </div>

                            <p class="text-white-50 small mb-0">
                                Periksa kesesuaian dokumen sebelum melakukan validasi final.
                            </p>

                        </div>

                        <div class="text-end">

                            <div class="d-flex align-items-center gap-2">

                                <span class="badge rounded-pill
                                             bg-success bg-opacity-10
                                             border border-success border-opacity-25
                                             text-success px-3 py-2">

                                    <i class="fas fa-check-circle me-1"></i>

                                    {{ $verifiedRequired }}/{{ $totalRequired }}
                                    Wajib Terverifikasi

                                </span>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table class="table table-dark align-middle mb-0 document-table">

                            <thead>

                                <tr class="text-white-50 small"
                                    style="text-transform:uppercase;
                                        letter-spacing:1px;
                                        background:rgba(0,0,0,.25);">

                                    <th class="ps-4 py-3 border-0"
                                        style="width:43%;">

                                        Dokumen

                                    </th>

                                    <th class="text-center border-0"
                                        style="width:20%;">

                                        Sifat

                                    </th>

                                    <th class="text-center border-0"
                                        style="width:17%;">

                                        Pratinjau

                                    </th>

                                    <th class="text-center border-0 pe-4"
                                        style="width:20%;">

                                        Status

                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                @forelse($submission->documents as $doc)

                                    @php

                                        $meta = $documentMeta[$doc->id] ?? [
                                            'key'       => $normalizeDocumentType($doc->jenis_dokumen ?? $doc->jenis_document ?? ''),
                                            'required'  => false,
                                            'optional'  => false,
                                            'next_step' => false,
                                        ];

                                        $displayName = $meta['key'] ?? strtoupper(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $doc->jenis_dokumen ?? $doc->jenis_document ?? ''
                                            )
                                        );

                                        $isiDokumen = match ($meta['key'] ?? '') {
                                            'DOKUMEN LS' => 'SPP, Verifikasi SPP/SPM, SPTJM, Billing',
                                            'DOKUMEN TU' => 'SPP, Verifikasi SPP/SPM, SPTJM, Persetujuan BUD',
                                            'DOKUMEN GU' => 'SPP, Verifikasi SPP/SPM, SPTJM, Persetujuan BUD',
                                            'DOKUMEN UP' => 'SPP, Verifikasi SPP/SPM, SPTJM',
                                            'DOKUMEN TU NIHIL' => 'SPP, Verifikasi SPP/SPM, SPTJM, Persetujuan BUD',
                                            default => null,
                                        };

                                    @endphp

                                    <tr class="document-row border-bottom
                                               border-white border-opacity-10"
                                        id="doc-row-{{ $doc->id }}">

                                        <td class="ps-4 py-3">

                                            <div class="d-flex align-items-start">

                                                <div class="text-danger me-3 pt-1">

                                                    <i class="far fa-file-pdf fs-4"></i>

                                                </div>

                                                <div class="min-width-0">

                                                    <div class="fw-bold text-white mb-1"
                                                         style="font-size:.72rem;
                                                            line-height:1.35;">

                                                        {{ $displayName }}

                                                    </div>

                                                    @if($isiDokumen)
                                                        <div class="text-white-50 mb-1"
                                                             style="font-size:.62rem; line-height:1.45;">
                                                            {{ $isiDokumen }}
                                                        </div>
                                                    @endif

                                                    <div class="text-white-50"
                                                         style="font-size:.62rem;">

                                                        <i class="fas fa-file-pdf me-1"></i>
                                                        Format PDF Digital

                                                    </div>

                                                </div>

                                            </div>

                                        </td>

                                        <td class="text-center py-3">

                                            @if($meta['required'])

                                                <span class="badge rounded-pill
                                                             bg-primary bg-opacity-10
                                                             border border-primary
                                                             border-opacity-25
                                                             text-primary px-2 py-1"
                                                      style="font-size:.62rem;">

                                                    <i class="fas fa-asterisk me-1"></i>
                                                    WAJIB

                                                </span>

                                            @elseif($meta['optional'])

                                                <span class="badge rounded-pill
                                                             bg-warning bg-opacity-10
                                                             border border-warning
                                                             border-opacity-25
                                                             text-warning px-2 py-1"
                                                      style="font-size:.62rem;">

                                                    <i class="fas fa-circle-info me-1"></i>
                                                    OPSIONAL

                                                </span>

                                            @elseif($meta['next_step'])

                                                <span class="badge rounded-pill
                                                             bg-secondary bg-opacity-10
                                                             border border-secondary
                                                             border-opacity-25
                                                             text-white-50 px-2 py-1"
                                                      style="font-size:.58rem;">

                                                    <i class="fas fa-forward me-1"></i>
                                                    TAHAP BERIKUT

                                                </span>

                                            @else

                                                <span class="badge rounded-pill
                                                             bg-secondary bg-opacity-10
                                                             border border-secondary
                                                             border-opacity-25
                                                             text-white-50 px-2 py-1"
                                                      style="font-size:.62rem;">

                                                    INFORMASI

                                                </span>

                                            @endif

                                        </td>

                                        <td class="text-center py-3">

                                            <button type="button"
                                                    class="btn btn-glass-primary
                                                           btn-sm rounded-pill
                                                           px-3 fw-bold shadow-sm"
                                                    data-url="{{ route('validasi.document.preview', $doc->id) }}"
                                                    data-title="{{ $displayName }}"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalPreview">

                                                <i class="fas fa-eye me-1"></i>
                                                Lihat

                                            </button>

                                        </td>

                                        <td class="text-center py-3 pe-4">

                                            @if($doc->status_dokumen == 'verified')

                                                <span class="text-success small fw-semibold">

                                                    <i class="fas fa-check-circle me-1"></i>
                                                    Terverifikasi

                                                </span>

                                            @elseif($doc->status_dokumen == 'revisi')

                                                <span class="text-danger small fw-semibold">

                                                    <i class="fas fa-exclamation-circle me-1"></i>
                                                    Perbaikan

                                                </span>

                                            @elseif($doc->status_dokumen == 'approved')

                                                <span class="text-info small fw-semibold">

                                                    <i class="fas fa-check me-1"></i>
                                                    Disetujui

                                                </span>

                                            @else

                                                <span class="text-warning small fw-semibold">

                                                    <i class="far fa-clock me-1"></i>
                                                    Belum Diperiksa

                                                </span>

                                            @endif

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

                <div class="p-4 border-top border-white border-opacity-10">

                    @if($isAllValidated)

                        <div class="validation-info success">

                            <div class="validation-info-content">

                                <div class="status-icon bg-success
                                            bg-opacity-10 text-success">

                                    <i class="fas fa-check-double"></i>

                                </div>

                                <div class="validation-info-copy">

                                    <div class="validation-info-title success">
                                        Seluruh dokumen wajib telah terverifikasi
                                    </div>

                                    <div class="validation-info-description">
                                        Pengajuan ini telah memenuhi seluruh persyaratan
                                        dokumen untuk dilakukan validasi final.
                                    </div>

                                </div>

                            </div>

                        </div>

                    @else

                        <div class="validation-info warning">

                            <div class="validation-info-content">

                                <div class="status-icon bg-warning
                                            bg-opacity-10 text-warning">

                                    <i class="fas fa-triangle-exclamation"></i>

                                </div>

                                <div class="validation-info-copy">

                                    <div class="validation-info-title warning">

                                        Dokumen wajib belum lengkap

                                    </div>

                                    <div class="validation-info-description mb-2">

                                        Validasi final belum dapat dilakukan
                                        sebelum seluruh dokumen wajib terverifikasi.

                                    </div>

                                    @if($notVerifiedRequired->count() > 0)

                                        <div class="d-flex flex-wrap gap-2">

                                            @foreach($notVerifiedRequired as $missingDoc)

                                                <span class="badge bg-danger bg-opacity-10
                                                             text-danger border
                                                             border-danger
                                                             border-opacity-25"
                                                      style="font-size:.58rem;">

                                                    {{ $documentMeta[$missingDoc->id]['key'] ?? strtoupper(str_replace('_', ' ', $missingDoc->jenis_dokumen ?? $missingDoc->jenis_document ?? 'Dokumen')) }}

                                                </span>

                                            @endforeach

                                        </div>

                                    @endif

                                </div>

                            </div>

                        </div>

                    @endif

                    @if($optionalDocuments->count() > 0)

                        <div class="validation-note optional">

                            <i class="fas fa-circle-info text-warning"></i>

                            <small>

                                Dokumen <strong class="text-warning">STS</strong>
                                bersifat opsional dan tidak menghambat proses validasi.

                            </small>

                        </div>

                    @endif

                    @if(in_array($jenisPencairan, ['LS', 'TU', 'GU']))

                        <div class="validation-note next-step">

                            <i class="fas fa-forward text-info"></i>

                            <small>

                                Untuk jenis
                                <strong class="text-info">
                                    {{ $jenisPencairan }}
                                </strong>,
                                SP2D diproses pada tahap setelah pengesahan
                                dan tidak menjadi persyaratan validasi awal.

                            </small>

                        </div>

                    @endif

                </div>

            </div>

        </div>

        <div class="col-lg-4">

            <div class="card border-0 shadow-lg rounded-4
                        overflow-hidden glass-card-dark h-100">

                <div class="card-body py-3">

                    <div class="info-section mb-3 p-3 rounded-4">

                        <label class="section-label text-primary">
                            Instansi Pengaju
                        </label>

                        <div class="fw-bold text-white mb-2">

                            {{ $submission->department->nama_dinas ?? '-' }}

                        </div>

                        <div class="text-light small">

                            <i class="fas fa-calendar-day
                                      me-2 text-primary opacity-75"></i>

                            Diajukan:
                            {{ $submission->created_at
                                ? $submission->created_at
                                    ->timezone('Asia/Makassar')
                                    ->format('d M Y, H:i')
                                : '-' }}

                        </div>

                    </div>

                    <div class="info-section border-0 p-3 rounded-4">

                        <button type="button"
                                class="info-collapse-toggle collapsed"
                                data-bs-toggle="collapse"
                                data-bs-target="#collapseDataPengajuan"
                                aria-expanded="false"
                                aria-controls="collapseDataPengajuan">

                            <span class="info-section-title mb-0">
                                <i class="fas fa-file-alt me-2"></i>
                                Data Pengajuan
                            </span>

                            <i class="fas fa-chevron-down collapse-chevron"></i>

                        </button>

                        <div class="collapse" id="collapseDataPengajuan">

                       <div class="info-row mt-3">
                            <span class="info-label">
                                Jenis Permintaan
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

                        <div class="info-row">
                            <span class="info-label">
                                Status Pengajuan
                            </span>

                            <span class="info-value">

                                @php
                                    $status = strtolower(trim($submission->status ?? ''));

                                    $statusLabel = match ($status) {
                                        'pending' => 'Menunggu Verifikasi',
                                        'direvisi' => 'Menunggu Verifikasi Ulang',
                                        'revisi' => 'Perlu Perbaikan',
                                        'proses validasi' => 'Proses Validasi',
                                        'disetujui' => 'Disetujui',
                                        'selesai' => 'Selesai',
                                        'history' => 'Selesai',
                                        default => ucwords(str_replace('_', ' ', $submission->status ?? '-')),
                                    };

                                    $statusClass = match ($status) {
                                        'pending',
                                        'direvisi' => 'status-warning',

                                        'revisi' => 'status-danger',

                                        'proses validasi' => 'status-info',

                                        'disetujui',
                                        'selesai',
                                        'history' => 'status-success',

                                        default => 'status-neutral',
                                    };
                                @endphp

                                <span class="status-badge {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>

                            </span>
                        </div>

                        </div>

                    </div>

                    <div class="info-section border-0 p-3 rounded-4 mt-3">

                        <button type="button"
                                class="info-collapse-toggle collapsed"
                                data-bs-toggle="collapse"
                                data-bs-target="#collapseInformasiKegiatan"
                                aria-expanded="false"
                                aria-controls="collapseInformasiKegiatan">

                            <span class="info-section-title mb-0">
                                <i class="fas fa-tasks me-2"></i>
                                Informasi Kegiatan
                            </span>

                            <i class="fas fa-chevron-down collapse-chevron"></i>

                        </button>

                        <div class="collapse" id="collapseInformasiKegiatan">

                        <div class="info-row mt-3">

                            <span class="info-label">
                                Kegiatan
                            </span>

                            <span class="info-value">
                                {{ $submission->judul_kegiatan ?? '-' }}
                            </span>

                        </div>

                        <div class="info-row">

                            <span class="info-label">
                                Sub Kegiatan
                            </span>

                            <span class="info-value">
                                {{ $submission->judul_sub_kegiatan ?? '-' }}
                            </span>

                        </div>

                        <div class="info-row">

                            <span class="info-label">
                                Perihal
                            </span>

                            <span class="info-value">
                                {{ $submission->perihal ?? '-' }}
                            </span>

                        </div>

                        </div>

                    </div>

                    <div class="info-section border-0 p-3 rounded-4 my-3">

                        <div class="info-section-title">
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

                    <div class="d-flex flex-column gap-2">

                        <form action="{{ route('validasi.sahkan', $submission->id) }}"
                              method="POST"
                              id="formSahkan"
                              class="no-ajax m-0 w-100">

                            @csrf

                            <button type="submit"
                                    class="btn
                                           {{ $isAllValidated
                                                ? 'btn-neon-success'
                                                : 'btn-glass-action' }}
                                           btn-lg w-100 rounded-pill
                                           fw-black py-3
                                           tracking-wide text-uppercase"
                                    style="font-size:.85rem;"
                                    {{ !$isAllValidated ? 'disabled' : '' }}>

                                <i class="fas fa-check-double me-2"></i>

                                {{ $isAllValidated
                                    ? 'Validasi Permintaan'
                                    : 'Belum Dapat Divalidasi' }}

                            </button>

                        </form>

                        <button type="button"
                                class="btn btn-glass-danger btn-lg
                                       w-100 rounded-pill fw-bold py-3
                                       text-uppercase tracking-wider"
                                style="font-size:.85rem;"
                                data-bs-toggle="modal"
                                data-bs-target="#modalTolak">

                            <i class="fas fa-undo me-2"></i>

                            Tolak / Revisi

                        </button>

                    </div>

                    @if(!$isAllValidated)

                        <div class="text-center mt-3">

                            <small class="text-warning x-small">

                                <i class="fas fa-circle-exclamation me-1"></i>

                                Lengkapi dan verifikasi seluruh dokumen wajib
                                sebelum melakukan validasi.

                            </small>

                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>

    <div class="row mb-4">

        <div class="col-12">

            <div class="card border-0 shadow-lg rounded-4
                        overflow-hidden glass-card-dark">

                <div class="p-3 glass-header-dark bg-opacity-5">

                    <div class="d-flex align-items-center">

                        <i class="fas fa-stream text-primary me-2"></i>

                        <h6 class="fw-bold text-white mb-0
                                   x-small text-uppercase tracking-wider">

                            Riwayat Pengajuan

                        </h6>

                    </div>

                </div>

                <div class="card-body p-4">

                    <div class="position-relative
                                d-flex flex-column gap-3
                                timeline-vertical-container">

                        <div class="position-absolute timeline-main-line"></div>

                        @if($submission->reset_at)

                            <div class="position-relative z-2">

                                <div class="d-flex align-items-start">

                                    <div class="timeline-icon bg-success">

                                        <i class="fas fa-check text-white"></i>

                                    </div>

                                    <div class="ms-3 flex-grow-1">

                                        <div class="mb-1 d-flex
                                                    align-items-center
                                                    flex-wrap gap-2">

                                            <h6 class="mb-0 fw-bold text-light small">
                                                Diverifikasi
                                            </h6>

                                            <small class="text-light opacity-50 timeline-date">

                                                {{ $submission->reset_at
                                                    ->timezone('Asia/Makassar')
                                                    ->format('d M Y, H:i') }}
                                                WITA

                                            </small>

                                        </div>

                                        <p class="text-light opacity-50 mb-0 x-small">

                                            Oleh:
                                            <span class="fw-semibold text-white">

                                                {{ $submission->verified_by_name ?? 'Sistem' }}

                                            </span>

                                        </p>

                                    </div>

                                </div>

                            </div>

                        @endif

                        <div class="position-relative z-2">

                            <div class="d-flex align-items-start">

                                <div class="timeline-icon bg-info">

                                    <i class="fas fa-plus text-dark"></i>

                                </div>

                                <div class="ms-3 flex-grow-1">

                                    <div class="mb-1 d-flex
                                                align-items-center
                                                flex-wrap gap-2">

                                        <h6 class="mb-0 fw-bold text-white small">

                                            Berkas Diajukan

                                        </h6>

                                        <small class="text-light opacity-50 timeline-date">

                                            {{ $submission->created_at
                                                ->timezone('Asia/Makassar')
                                                ->format('d M Y, H:i') }}
                                            WITA

                                        </small>

                                    </div>

                                    <p class="text-light opacity-50 mb-0 x-small">

                                        Oleh:
                                        <span class="fw-semibold text-white">

                                            {{ $submission->user->name ?? 'System' }}

                                        </span>

                                    </p>

                                </div>

                            </div>

                        </div>

                        @if($submission->revisi_at)

                            <div class="position-relative z-2">

                                <div class="d-flex align-items-start">

                                    <div class="timeline-icon bg-danger">

                                        <i class="fas fa-exclamation-triangle text-white"></i>

                                    </div>

                                    <div class="ms-3 flex-grow-1">

                                        <div class="mb-1 d-flex
                                                    align-items-center
                                                    flex-wrap gap-2">

                                            <h6 class="mb-0 fw-bold text-danger
                                                       small text-uppercase">

                                                Dikembalikan

                                            </h6>

                                            <small class="text-light opacity-50 timeline-date">

                                                {{ $submission->revisi_at
                                                    ->timezone('Asia/Makassar')
                                                    ->format('d M Y, H:i') }}
                                                WITA

                                            </small>

                                        </div>

                                        <p class="text-light opacity-50 mb-0 x-small">

                                            Oleh:
                                            <span class="fw-semibold text-white">

                                                {{ $submission->penolak->name
                                                    ?? 'Sistem / Verifikator' }}

                                            </span>

                                        </p>

                                    </div>

                                </div>

                            </div>

                        @endif

                        @if($submission->revised_at)

                            <div class="position-relative z-2">

                                <div class="d-flex align-items-start">

                                    <div class="timeline-icon bg-warning">

                                        <i class="fas fa-file-pen text-dark"></i>

                                    </div>

                                    <div class="ms-3 flex-grow-1">

                                        <div class="mb-1 d-flex
                                                    align-items-center
                                                    flex-wrap gap-2">

                                            <h6 class="mb-0 fw-bold text-warning
                                                       small text-uppercase">

                                                Direvisi

                                            </h6>

                                            <small class="text-light opacity-50 timeline-date">

                                                {{ $submission->revised_at
                                                    ->timezone('Asia/Makassar')
                                                    ->format('d M Y, H:i') }}
                                                WITA

                                            </small>

                                        </div>

                                        <p class="text-light opacity-50 mb-0 x-small">

                                            Oleh:
                                            <span class="fw-semibold text-white">

                                                {{ $submission->user->name ?? 'Sistem' }}

                                            </span>

                                        </p>

                                    </div>

                                </div>

                            </div>

                        @endif

                        @if($submission->verified_at)

                            <div class="position-relative z-2">

                                <div class="d-flex align-items-start">

                                    <div class="timeline-icon bg-success">

                                        <i class="fas fa-check text-white"></i>

                                    </div>

                                    <div class="ms-3 flex-grow-1">

                                        <div class="mb-1 d-flex
                                                    align-items-center
                                                    flex-wrap gap-2">

                                            <h6 class="mb-0 fw-bold text-light small">

                                                Diverifikasi

                                            </h6>

                                            <small class="text-light opacity-50 timeline-date">

                                                {{ $submission->verified_at
                                                    ->timezone('Asia/Makassar')
                                                    ->format('d M Y, H:i') }}
                                                WITA

                                            </small>

                                        </div>

                                        <p class="text-light opacity-50 mb-0 x-small">

                                            Oleh:
                                            <span class="fw-semibold text-white">

                                                {{ $submission->verified_by_name
                                                    ?? 'Sistem' }}

                                            </span>

                                        </p>

                                    </div>

                                </div>

                            </div>

                        @endif

                        @if($submission->rejected_at)

                            <div class="position-relative z-2">

                                <div class="d-flex align-items-start">

                                    <div class="timeline-icon bg-danger">

                                        <i class="fas fa-times text-white"></i>

                                    </div>

                                    <div class="ms-3 flex-grow-1">

                                        <div class="mb-1 d-flex
                                                    align-items-center
                                                    flex-wrap gap-2">

                                            <h6 class="mb-0 fw-bold text-light small">

                                                Ditolak

                                            </h6>

                                            <small class="text-light opacity-50 timeline-date">

                                                {{ $submission->rejected_at
                                                    ->timezone('Asia/Makassar')
                                                    ->format('d M Y, H:i') }}
                                                WITA

                                            </small>

                                        </div>

                                        <p class="text-light opacity-50 mb-0 x-small">

                                            Oleh:
                                            <span class="fw-semibold text-white">

                                                {{ $submission->penolakKabid->name
                                                    ?? 'Sistem' }}

                                            </span>

                                        </p>

                                    </div>

                                </div>

                            </div>

                        @endif

                        <div class="position-relative z-2 timeline-current-stage">

                            <div class="d-flex align-items-start">

                                <div class="timeline-icon bg-primary
                                            shadow-glow">

                                    <span class="spinner-grow
                                                 spinner-grow-sm text-white"
                                          style="width:.4rem;
                                            height:.4rem;">
                                    </span>

                                </div>

                                <div class="ms-3 flex-grow-1 timeline-current-content">

                                    <div class="mb-2">

                                        <span class="badge bg-primary text-white
                                                     rounded-pill px-2 py-1
                                                     x-small fw-bold tracking-wider">

                                            TAHAP VALIDASI

                                        </span>

                                    </div>

                                    <div class="mb-1 fw-bold text-white small">

                                        Menunggu Validasi Anda

                                    </div>

                                    <p class="text-light opacity-75 mb-0 x-small"
                                       style="line-height:1.5;">

                                        Pastikan seluruh dokumen wajib telah
                                        diperiksa dan berstatus
                                        <strong class="text-success">
                                            Terverifikasi
                                        </strong>
                                        sebelum melakukan validasi final.

                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

{{-- Modal: Pratinjau dokumen untuk validasi --}}
<div class="modal fade"
     id="modalPreview"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-centered"
         style="max-width:95vw;">

        <div class="modal-content
                    border-white border-opacity-10
                    bg-dark shadow-lg rounded-4 overflow-hidden text-white"
             style="background:rgba(20,24,33,.98)!important;
                backdrop-filter:blur(10px);
                height:90vh;">

            <div class="modal-body p-0
                        d-flex flex-column flex-lg-row h-100">

                <div class="flex-grow-1 bg-black bg-opacity-40
                            position-relative h-100">

                    <div id="loader"
                         class="position-absolute
                                top-50 start-50
                                translate-middle text-center">

                        <div class="spinner-border
                                    text-primary border-3">
                        </div>

                        <div class="text-white-50 small mt-2">
                            Memuat dokumen...
                        </div>

                    </div>

                    <iframe id="pdfFrame"
                            src=""
                            frameborder="0"
                            style="width:100%;
                                height:100%;
                                display:none;">
                    </iframe>

                </div>

                <div class="p-3 d-flex flex-column
                            border-start border-white
                            border-opacity-10
                            bg-dark bg-opacity-5
                            shadow-sm h-100"
                     style="width:280px;
                        min-width:280px;
                        overflow-y:auto;">

                    <div class="d-flex
                                justify-content-between
                                align-items-center">

                        <h6 class="fw-black text-white
                                   text-uppercase my-2
                                   tracking-tight text-truncate"
                            id="docTitle"
                            style="font-size:.85rem;">

                            PRATINJAU

                        </h6>

                        <button type="button"
                                class="btn-close btn-close-white p-1"
                                data-bs-dismiss="modal">
                        </button>

                    </div>

                    <hr class="border-white border-opacity-50 my-1">

                    <div class="mb-3 d-flex flex-column gap-2">

                        <div>

                            <label class="modal-label">
                                Nomor SPP / SPM
                            </label>

                            <div class="fw-bold text-white text-break modal-value">

                                {{ $submission->nomor_spp ?? '-' }}
                                /
                                {{ $submission->nomor_spm ?? '-' }}

                            </div>

                        </div>

                        <div>

                            <label class="modal-label">
                                Kegiatan
                            </label>

                            <div class="text-white text-break modal-value">

                                {{ $submission->judul_kegiatan ?? '-' }}

                            </div>

                        </div>

                        <div>

                            <label class="modal-label">
                                Sub Kegiatan
                            </label>

                            <div class="text-white text-break modal-value">

                                {{ $submission->judul_sub_kegiatan ?? '-' }}

                            </div>

                        </div>

                        <div>

                            <label class="modal-label">
                                Perihal
                            </label>

                            <div class="text-white-50 text-break modal-value">

                                {{ $submission->perihal ?? '-' }}

                            </div>

                        </div>

                        <div class="p-2 rounded-3 mt-1"
                             style="background:rgba(34,197,94,.06);
                                border:1px solid rgba(34,197,94,.12);">

                            <label class="text-success
                                         text-uppercase
                                         tracking-wider
                                         fw-bold"
                                   style="font-size:.55rem;">

                                Total Nilai Permintaan

                            </label>

                            <div class="fw-black text-white
                                        tracking-tight"
                                 style="font-size:1.1rem;">

                                Rp
                                {{ number_format(
                                    $submission->total_pencairan ?? 0,
                                    0,
                                    ',',
                                    '.'
                                ) }}

                            </div>

                        </div>

                    </div>

                    <div class="alert alert-primary
                                bg-primary bg-opacity-5
                                border-0 text-white-50
                                rounded-3 mb-3 p-2"
                         style="line-height:1.3;
                            font-size:.68rem;">

                        <i class="fas fa-info-circle
                                  me-1 text-light"></i>

                        Periksa tanda tangan, stempel,
                        nomor dokumen, dan nominal.

                    </div>

                    <div class="mt-auto d-grid gap-2 pt-2">

                        <a id="btnDownload"
                           href=""
                           download
                           class="btn btn-glass-action
                                  rounded-pill fw-bold p-2 text-light"
                           style="font-size:.75rem;">

                            <i class="fas fa-download
                                      me-2 text-primary"></i>

                            Unduh Berkas

                        </a>

                        <button class="btn btn-primary
                                       rounded-pill fw-bold p-2"
                                style="font-size:.75rem;"
                                data-bs-dismiss="modal">

                            Selesai Membaca

                        </button>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

{{-- Modal: Penolakan dokumen pengajuan --}}
<div class="modal fade"
     id="modalTolak"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 shadow-lg
                    text-light rounded-4"
             style="background:#111827;
                border:1px solid rgba(255,255,255,.08)!important;">

            <div class="modal-header
                        border-bottom border-white
                        border-opacity-10
                        py-3 px-4">

                <h6 class="modal-title fw-bold m-0
                           text-danger d-flex
                           align-items-center gap-2">

                    <i class="fas fa-exclamation-triangle"></i>

                    TOLAK / KEMBALIKAN BERKAS

                </h6>

                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <form action="{{ route('validasi.tolak', $submission->id) }}"
                  method="POST"
                  id="formTolak">

                @csrf

                <div class="modal-body p-4">

                    <div class="mb-3">

                        <label class="form-label extra-small
                                      text-light fw-bold
                                      tracking-wide text-uppercase">

                            Alasan Penolakan / Catatan Revisi

                        </label>

                        <textarea name="catatan"
                                  class="form-control bg-dark
                                         border-white
                                         border-opacity-10
                                         text-white rounded-3
                                         py-2 custom-textarea"
                                  rows="4"
                                  placeholder="Contoh: Dokumen belum ditandatangani. Silakan unggah kembali dokumen yang sesuai."
                                  required></textarea>

                    </div>

                    <div class="text-light extra-small
                                d-flex align-items-start gap-2">

                        <i class="fas fa-circle text-danger mt-1"></i>

                        <span>

                            Berkas akan dikembalikan kepada Operator SKPD
                            untuk dilakukan perbaikan.

                        </span>

                    </div>

                </div>

                <div class="modal-footer
                            border-top border-white
                            border-opacity-10
                            bg-dark bg-opacity-40
                            py-3 px-4">

                    <button type="button"
                            class="btn btn-secondary btn-sm
                                   rounded-pill px-3
                                   bg-opacity-20 border-0 text-light"
                            data-bs-dismiss="modal">

                        Batal

                    </button>

                    <button type="submit"
                            class="btn btn-danger btn-sm
                                   rounded-pill px-4
                                   shadow-sm text-light fw-bold">

                        <i class="fas fa-paper-plane me-1"></i>

                        Kirim

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

document.addEventListener('show.bs.modal', function (event) {

    if (event.target.id !== 'modalPreview') {
        return;
    }

    const button = event.relatedTarget;

    if (!button) {
        return;
    }

    const fileUrl = button.getAttribute('data-url');
    const title   = button.getAttribute('data-title');

    const pdfFrame   = document.getElementById('pdfFrame');
    const docTitle   = document.getElementById('docTitle');
    const btnDownload = document.getElementById('btnDownload');
    const loader     = document.getElementById('loader');

    if (!fileUrl) {
        return;
    }

    const separator = fileUrl.includes('?')
        ? '&'
        : '?';

    const finalUrl =
        fileUrl +
        separator +
        't=' +
        new Date().getTime();

    if (pdfFrame) {

        pdfFrame.style.display = 'none';

        pdfFrame.setAttribute('src', '');

    }

    if (loader) {

        loader.style.display = 'block';

    }

    if (docTitle) {

        docTitle.textContent =
            title || 'Preview Dokumen';

    }

    if (btnDownload) {

        btnDownload.setAttribute(
            'href',
            fileUrl
        );

    }

    if (pdfFrame) {

        // Function: Menangani PDF setelah selesai dimuat
        pdfFrame.onload = function () {

            if (loader) {
                loader.style.display = 'none';
            }

            pdfFrame.style.display = 'block';

        };

        setTimeout(function () {

            pdfFrame.setAttribute(
                'src',
                finalUrl
            );

        }, 100);

        setTimeout(function () {

            if (
                loader &&
                loader.style.display !== 'none'
            ) {

                loader.style.display = 'none';

                pdfFrame.style.display = 'block';

            }

        }, 5000);

    }

});

document.addEventListener('hidden.bs.modal', function (event) {

    if (event.target.id !== 'modalPreview') {
        return;
    }

    const pdfFrame = document.getElementById('pdfFrame');
    const loader = document.getElementById('loader');

    if (pdfFrame) {

        pdfFrame.setAttribute('src', '');

        pdfFrame.style.display = 'none';

    }

    if (loader) {

        loader.style.display = 'block';

    }

});

document.addEventListener('submit', function (e) {

    const activeForm = e.target;

    if (activeForm.id === 'formSahkan') {

        const submitButton =
            activeForm.querySelector(
                'button[type="submit"]'
            );

        if (
            submitButton &&
            submitButton.disabled
        ) {

            e.preventDefault();

            return;

        }

        e.preventDefault();

        Swal.fire({

            title: 'Otorisasi Validasi?',

            text:
                'Dokumen ini akan divalidasi secara final dalam sistem.',

            icon: 'warning',

            iconColor: '#198754',

            showCancelButton: true,

            confirmButtonText:
                '<i class="fas fa-check-double me-2"></i> Ya, Validasi',

            cancelButtonText:
                'Batal',

            reverseButtons: true,

            background:
                'rgba(15,17,26,.95)',

            backdrop:
                'rgba(11,13,19,.7) blur(5px)',

            color:
                '#f8f9fa',

            customClass: {

                popup:
                    'border border-white border-opacity-10 rounded-4 shadow-lg',

                title:
                    'fw-bold text-white tracking-tight pt-3',

                htmlContainer:
                    'text-white-50 small px-3',

                confirmButton:
                    'btn btn-success rounded-pill px-4 py-2 mx-2 fw-bold',

                cancelButton:
                    'btn btn-glass-action rounded-pill px-4 py-2 mx-2 text-white'

            },

            buttonsStyling: false

        }).then(function (result) {

            if (result.isConfirmed) {

                if (submitButton) {

                    submitButton.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2"></span>Memvalidasi...';

                    submitButton.disabled = true;

                }

                activeForm.submit();

            }

        });

    }

    if (activeForm.id === 'formTolak') {

        e.preventDefault();

        Swal.fire({

            title: 'Kembalikan Berkas?',

            text:
                'Berkas akan dikembalikan kepada Operator SKPD untuk diperbaiki.',

            icon: 'error',

            iconColor: '#dc3545',

            showCancelButton: true,

            confirmButtonText:
                '<i class="fas fa-undo me-2"></i> Ya, Kembalikan',

            cancelButtonText:
                'Batal',

            reverseButtons: true,

            background:
                'rgba(15,17,26,.95)',

            backdrop:
                'rgba(11,13,19,.7) blur(5px)',

            color:
                '#f8f9fa',

            customClass: {

                popup:
                    'border border-white border-opacity-10 rounded-4 shadow-lg',

                title:
                    'fw-bold text-white tracking-tight pt-3',

                htmlContainer:
                    'text-white-50 small px-3',

                confirmButton:
                    'btn btn-danger rounded-pill px-4 py-2 mx-2 fw-bold',

                cancelButton:
                    'btn btn-glass-action rounded-pill px-4 py-2 mx-2 text-white'

            },

            buttonsStyling: false

        }).then(function (result) {

            if (result.isConfirmed) {

                const btn =
                    activeForm.querySelector(
                        'button[type="submit"]'
                    );

                if (btn) {

                    btn.innerHTML =
                        '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

                    btn.disabled = true;

                }

                activeForm.submit();

            }

        });

    }

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

        padding:16px 18px;

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

    .validation-info-content {
        display:flex;
        align-items:center;
        gap:14px;
    }

    .validation-info .status-icon {
        width:42px;
        height:42px;
        min-width:42px;
        border-radius:13px;
        font-size:.95rem;
    }

    .validation-info.success .status-icon {
        color:#6ee7b7 !important;
        background:rgba(16,185,129,.14) !important;
    }

    .validation-info.warning .status-icon {
        color:#fcd34d !important;
        background:rgba(245,158,11,.14) !important;
    }

    .validation-info-copy {
        min-width:0;
    }

    .validation-info-title {
        margin-bottom:4px;
        font-size:.76rem;
        font-weight:800;
        line-height:1.4;
    }

    .validation-info-title.success {
        color:#6ee7b7;
    }

    .validation-info-title.warning {
        color:#fcd34d;
    }

    .validation-info-description {
        color:rgba(241,245,249,.76);
        font-size:.69rem;
        line-height:1.55;
    }

    .validation-info.warning .badge {
        color:#fda4af !important;
    }

    .validation-note {
        display:flex;
        align-items:flex-start;
        gap:9px;
        margin-top:10px;
        padding:9px 12px;
        border:1px solid rgba(255,255,255,.07);
        border-radius:10px;
        background:rgba(255,255,255,.025);
    }

    .validation-note > i {
        flex:0 0 14px;
        margin-top:2px;
    }

    .validation-note small {
        color:rgba(241,245,249,.72);
        font-size:.66rem;
        line-height:1.5;
    }

    .validation-note.optional {
        border-color:rgba(245,158,11,.16);
        background:rgba(245,158,11,.035);
    }

    .validation-note.next-step {
        border-color:rgba(14,165,233,.16);
        background:rgba(14,165,233,.035);
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

        background:linear-gradient(
            180deg,
            rgba(13,110,253,.5),
            rgba(255,255,255,.08) 45%,
            rgba(255,255,255,.08)
        );

        left:11px;

        top:10px;

        bottom:16px;

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

        border:2px solid rgba(255,255,255,.6);

        box-shadow:
            0 2px 6px
            rgba(0,0,0,.2);

        font-size:.5rem;

        position:relative;

        z-index:2;

    }

    .timeline-vertical-container > .position-relative.z-2 {
        margin-left:38px;
        padding:14px 16px;
        border:1px solid rgba(255,255,255,.08);
        border-radius:14px;
        background:rgba(255,255,255,.035);
        transition:background .2s ease, border-color .2s ease, transform .2s ease;
    }

    .timeline-vertical-container > .position-relative.z-2:hover {
        background:rgba(255,255,255,.055);
        border-color:rgba(255,255,255,.14);
        transform:translateY(-1px);
    }

    .timeline-vertical-container > .position-relative.z-2 > .d-flex > .timeline-icon {
        position:absolute;
        top:14px;
        left:-38px;
        box-shadow:0 0 0 5px rgba(20,24,33,.92), 0 3px 10px rgba(0,0,0,.25);
    }

    .timeline-vertical-container > .position-relative.z-2 > .d-flex > .timeline-icon + div {
        width:100%;
        margin-left:0 !important;
    }

    .timeline-vertical-container > .timeline-current-stage {
        border-color:rgba(13,110,253,.5);
        background:linear-gradient(120deg, rgba(13,110,253,.17), rgba(13,110,253,.06));
        box-shadow:inset 3px 0 0 #0d6efd, 0 8px 20px rgba(0,0,0,.12);
    }

    .timeline-vertical-container > .timeline-current-stage:hover {
        border-color:rgba(13,110,253,.7);
        background:linear-gradient(120deg, rgba(13,110,253,.22), rgba(13,110,253,.08));
    }

    .timeline-current-content {
        margin:0 !important;
        padding:0;
        border:0;
        background:transparent;
    }

    .timeline-date {
        display:inline-block;
        padding:3px 8px;
        border:1px solid rgba(255,255,255,.08);
        border-radius:999px;
        background:rgba(255,255,255,.04);
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
