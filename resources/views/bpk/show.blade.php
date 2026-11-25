{{-- View: bpk/show --}}
@extends('layouts.app')

@section('content')
@php
    // Function: Menentukan label waktu untuk tanggal audit
    $getServerTzLabel = function($carbonDate) {
        if (!$carbonDate) return '';
        $tzName = $carbonDate->timezoneName;
        if (str_contains($tzName, 'Makassar') || str_contains($tzName, 'Singapore') || $carbonDate->offsetHours == 8) {
            return ' WITA';
        } elseif (str_contains($tzName, 'Jakarta') || str_contains($tzName, 'Bangkok') || $carbonDate->offsetHours == 7) {
            return ' WIB';
        } elseif (str_contains($tzName, 'Jayapura') || $carbonDate->offsetHours == 9) {
            return ' WIT';
        }
        return ' ' . $carbonDate->format('T');
    };
@endphp

<div class="container-fluid bpk-audit-page">

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 glass-header-dark position-relative bpk-audit-header">
        <div class="glass-reflection"></div>
        <div class="card-body p-3 p-lg-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center">
                        <div class="bpk-header-icon me-3">
                            <i class="fas fa-file-circle-check fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-black text-light mb-1 tracking-tight">Detail Berkas</h4>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0 extra-small">
                                    <li class="breadcrumb-item"><a href="{{ route('bpk.monitoring') }}" class="text-white-50 text-decoration-none">Audit BPK</a></li>
                                    <li class="breadcrumb-item active fw-medium text-warning">Detail Berkas</li>
                                </ol>
                            </nav>
                            <small class="text-white-50">SPP / SPM: <span class="text-info fw-semibold">{{ $submission->nomor_spp ?? '-' }} / {{ $submission->nomor_spm ?? '-' }}</span></small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 text-md-end mt-3 mt-md-0">
                    <a href="{{ route('bpk.monitoring') }}" class="btn btn-glass-action bpk-outline-button rounded-pill px-3 py-2 text-light dynamic-nav">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Monitoring
                    </a>
                </div>
            </div>
        </div>
    </div>

    @php
        $statusPengajuan = strtolower(trim($submission->status ?? ''));
        $statusPengajuanLabel = in_array($statusPengajuan, ['disetujui', 'approved', 'verified'], true)
            ? 'Disetujui'
            : ucwords(str_replace('_', ' ', $submission->status ?? '-'));
        $statusPengajuanClass = in_array($statusPengajuan, ['disetujui', 'approved', 'verified'], true)
            ? 'success'
            : 'secondary';
    @endphp

    <div class="row g-4 bpk-detail-layout">
        <div class="col-lg-8">
            <div class="card border-0 shadow-lg rounded-4 glass-card-dark overflow-hidden bpk-documents-card">
                <div class="glass-header-dark border-0 py-3 px-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <div class="d-flex align-items-center mb-1">
                                <i class="fas fa-folder-open text-warning me-2"></i>
                                <h6 class="fw-bold text-white mb-0">Lampiran Dokumen</h6>
                            </div>
                            <p class="text-white-50 small mb-0">Tinjau dokumen pengajuan sebelum menyimpan hasil pemeriksaan.</p>
                        </div>
                        <span class="badge rounded-pill bg-info bg-opacity-10 border border-info border-opacity-25 text-info px-3 py-2">
                            <i class="fas fa-file-lines me-1"></i>{{ $submission->documents->count() }} Dokumen
                        </span>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dark align-middle m-0 bpk-document-table">
                            <thead>
                                <tr class="text-white-50 small">
                                    <th class="ps-4 py-3 border-0">Dokumen</th>
                                    <th class="text-center border-0">Pratinjau</th>
                                    <th class="pe-4 text-center border-0">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($submission->documents as $doc)
                                    @php
                                        $documentStatus = strtolower($doc->status_dokumen ?? '');
                                        $documentStyle = match($documentStatus) {
                                            'verified', 'approved' => ['class' => 'success', 'icon' => 'fa-check-circle', 'label' => 'Terverifikasi'],
                                            'revisi', 'rejected' => ['class' => 'danger', 'icon' => 'fa-exclamation-circle', 'label' => 'Perlu Perbaikan'],
                                            'uploaded', 'direvisi', 'pending' => ['class' => 'warning', 'icon' => 'fa-clock', 'label' => 'Menunggu'],
                                            default => ['class' => 'secondary', 'icon' => 'fa-circle-question', 'label' => ucfirst($documentStatus ?: 'Belum Ada Status')],
                                        };
                                        $documentTitle = str_replace('_', ' ', $doc->jenis_dokumen);
                                        $previewMetadata = [
                                            'pengaju' => $submission->department->nama_dinas ?? '-',
                                            'perihal' => $submission->perihal,
                                            'nomor_spp' => $submission->nomor_spp ?? '-',
                                            'nomor_spm' => $submission->nomor_spm ?? '-',
                                            'total_pencairan' => $submission->total_pencairan ?? 0,
                                            'judul_kegiatan' => $submission->judul_kegiatan,
                                            'judul_sub_kegiatan' => $submission->judul_sub_kegiatan,
                                            'tanggal_diajukan' => $submission->created_at ? $submission->created_at->format('d/m/Y H:i') . $getServerTzLabel($submission->created_at) : '-',
                                            'tanggal_validasi' => $submission->validated_at ? $submission->validated_at->format('d/m/Y H:i') . $getServerTzLabel($submission->validated_at) : "<span class='text-white-50 small italic'>Belum Divalidasi</span>",
                                        ];
                                    @endphp
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <span class="bpk-document-icon"><i class="fas fa-file-pdf"></i></span>
                                                <div>
                                                    <div class="fw-bold text-white text-capitalize">{{ $documentTitle }}</div>
                                                    <small class="text-white-50">Berkas lampiran pengajuan</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                                <button type="button"
                                                    class="btn btn-document-preview btn-glass-primary bpk-outline-button bpk-preview-trigger btn-sm rounded-pill px-3 fw-bold shadow-sm"
                                                    data-file-url="{{ route('storage.file', ['path' => $doc->file_path]) }}"
                                                    data-doc-title="{{ $documentTitle }}"
                                                    data-metadata="{{ json_encode($previewMetadata) }}"
                                                    onclick="openPreviewModal(this.getAttribute('data-file-url'), this.getAttribute('data-doc-title'), JSON.parse(this.getAttribute('data-metadata')))">
                                                <i class="fas fa-eye me-1"></i>Tinjau
                                            </button>
                                        </td>
                                        <td class="pe-4 text-center">
                                            <span class="bpk-document-status {{ $documentStyle['class'] }}">
                                                <i class="fas {{ $documentStyle['icon'] }} me-1"></i>{{ $documentStyle['label'] }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-white-50 py-5">
                                            <i class="fas fa-folder-open fa-2x d-block mb-2"></i>
                                            Belum ada dokumen yang dilampirkan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden glass-card-dark h-100 bpk-info-card">
                <div class="glass-header-dark border-0 py-3 px-4">
                    <div class="d-flex align-items-center gap-2">
                        <div class="bpk-info-icon"><i class="fas fa-file-invoice"></i></div>
                        <div>
                            <h6 class="mb-0 text-white">Informasi Pengajuan</h6>
                            <small class="text-light">Detail administrasi dan informasi pengajuan</small>
                        </div>
                    </div>
                </div>
                <div class="card-body py-3">
                    <div class="bpk-audit-status-panel mb-3">
                        @if($submission->status_audit === 'dengan_catatan')
                            <span class="bpk-audit-result warning"><i class="fas fa-triangle-exclamation me-1"></i>Dengan Catatan</span>
                        @elseif($submission->status_audit === 'sesuai')
                            <span class="bpk-audit-result success"><i class="fas fa-check-circle me-1"></i>Audited / Clear</span>
                        @else
                            <span class="bpk-audit-result pending"><i class="fas fa-clock me-1"></i>Belum Diperiksa</span>
                        @endif
                    </div>

                    <div class="info-section mb-3 p-3 rounded-4 bpk-info-section">
                        <button type="button" class="info-collapse-toggle collapsed" data-bs-toggle="collapse" data-bs-target="#bpkDataPengajuan" aria-expanded="false" aria-controls="bpkDataPengajuan">
                            <span class="info-section-title"><i class="fas fa-file-alt me-2"></i>Data Pengajuan</span>
                            <i class="fas fa-chevron-down collapse-chevron"></i>
                        </button>
                        <div class="collapse" id="bpkDataPengajuan">
                            <div class="mt-3">
                                <div class="info-row"><span class="info-label">Instansi Pengaju</span><span class="info-value">{{ $submission->department->nama_dinas ?? '-' }}</span></div>
                                <div class="info-row mt-3"><span class="info-label">Jenis Pencairan</span><span class="info-value">{{ strtoupper($submission->jenis_pencairan ?? '-') }}</span></div>
                                <div class="info-row mt-3"><span class="info-label">Nomor SPP / SPM</span><span class="info-value">{{ $submission->nomor_spp ?? '-' }} / {{ $submission->nomor_spm ?? '-' }}</span></div>
                                <div class="info-row mt-3"><span class="info-label">Tanggal Berkas</span><span class="info-value">{{ $submission->tanggal ? \Carbon\Carbon::parse($submission->tanggal)->translatedFormat('d F Y') : '-' }}</span></div>
                                <div class="info-row mt-3"><span class="info-label">Tanggal Pengajuan</span><span class="info-value">{{ $submission->created_at?->translatedFormat('d F Y, H:i') ?? '-' }}</span></div>
                                <div class="info-row mt-3"><span class="info-label">Status Pengajuan</span><span class="info-value"><span class="bpk-submission-status {{ $statusPengajuanClass }}">{{ $statusPengajuanLabel }}</span></span></div>
                            </div>
                        </div>
                    </div>

                    <div class="info-section mb-3 p-3 rounded-4 bpk-info-section">
                        <button type="button" class="info-collapse-toggle collapsed" data-bs-toggle="collapse" data-bs-target="#bpkInfoKegiatan" aria-expanded="false" aria-controls="bpkInfoKegiatan">
                            <span class="info-section-title"><i class="fas fa-tasks me-2"></i>Informasi Kegiatan</span>
                            <i class="fas fa-chevron-down collapse-chevron"></i>
                        </button>
                        <div class="collapse" id="bpkInfoKegiatan">
                            <div class="activity-info-list mt-3">
                                <div class="activity-info-item"><span class="activity-info-label">Kegiatan</span><span class="activity-info-value">{{ $submission->judul_kegiatan ?? '-' }}</span></div>
                                <div class="activity-info-item"><span class="activity-info-label">Sub Kegiatan</span><span class="activity-info-value">{{ $submission->judul_sub_kegiatan ?? '-' }}</span></div>
                                <div class="activity-info-item"><span class="activity-info-label">Perihal</span><span class="activity-info-value">{{ $submission->perihal ?? '-' }}</span></div>
                            </div>
                        </div>
                    </div>

                    <div class="info-section mb-3 p-3 rounded-4 bpk-info-section">
                        <div class="info-section-title"><i class="fas fa-money-bill-wave me-2"></i>Informasi Keuangan</div>
                        <div class="info-row mt-3"><span class="info-label">Sumber Dana</span><span class="info-value">{{ $submission->sumber_dana ?? '-' }}</span></div>
                        <div class="bpk-financial-total mt-3">
                            <div>
                                <span class="d-block fw-bold text-white">Total Permintaan</span>
                                <small class="text-white-50">Nilai pengajuan</small>
                            </div>
                            <strong>Rp {{ number_format($submission->total_pencairan ?? 0, 0, ',', '.') }}</strong>
                        </div>
                    </div>

                    <div class="bpk-info-audit-actions">
                        <form action="{{ route('bpk.audit.submit', $submission->id) }}" method="POST" id="formAuditSesuai">
                            @csrf
                            <input type="hidden" name="status_audit" value="sesuai">
                            <button type="button" class="btn btn-success w-100 btn-pill fw-bold py-2 shadow-sm" onclick="confirmAuditSesuai()">
                                <i class="fas fa-check-double me-1"></i> AUDITED
                            </button>
                        </form>
                        <button type="button" class="btn btn-warning text-dark w-100 btn-pill fw-bold py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#catatanAuditModal">
                            <i class="fas fa-edit me-1"></i> Audit Dengan Catatan
                        </button>
                    </div>

                    <div class="d-flex align-items-center justify-content-between gap-2 mt-4 pt-3 border-top border-white border-opacity-10">
                        @if($next)
                            <a href="{{ route('bpk.show', $next->id) }}" class="btn btn-glass-action bpk-outline-button btn-sm rounded-pill px-3 dynamic-nav" title="Ke Pengajuan Lebih Baru"><i class="fas fa-chevron-left me-1"></i>Prev</a>
                        @else
                            <button class="btn btn-glass-action bpk-outline-button btn-sm rounded-pill px-3" disabled><i class="fas fa-chevron-left me-1"></i>Prev</button>
                        @endif
                        <span class="small text-white-50">ID #{{ $submission->id }}</span>
                        @if($previous)
                            <a href="{{ route('bpk.show', $previous->id) }}" class="btn btn-glass-action bpk-outline-button btn-sm rounded-pill px-3 dynamic-nav" title="Ke Pengajuan Lebih Lama">Next<i class="fas fa-chevron-right ms-1"></i></a>
                        @else
                            <button class="btn btn-glass-action bpk-outline-button btn-sm rounded-pill px-3" disabled>Next<i class="fas fa-chevron-right ms-1"></i></button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden glass-card-dark">
                <div class="card-header glass-header-dark border-0 py-3 px-4">
                    <h6 class="m-0 fw-bold text-light"><i class="fas fa-fingerprint me-2 text-warning"></i>Riwayat Autentikasi Digital</h6>
                </div>
                <div class="card-body p-0 pb-3">
                    @php
                        $history = [
                            ['label' => 'Diajukan', 'user' => $submission->user?->name, 'time' => $submission->created_at, 'color' => 'text-info', 'icon' => 'fa-plus'],
                            ['label' => 'Dikembalikan', 'user' => $submission->penolak?->name, 'time' => $submission->revisi_at, 'color' => 'text-danger', 'icon' => 'fa-undo-alt'],
                            ['label' => 'Direvisi', 'user' => $submission->user?->name, 'time' => $submission->revised_at, 'color' => 'text-warning', 'icon' => 'fa-edit'],
                            ['label' => 'Diverifikasi', 'user' => $submission->verified_by_name, 'time' => $submission->verified_at, 'color' => 'text-success', 'icon' => 'fa-shield-alt'],
                            ['label' => 'Ditolak', 'user' => $submission->penolakKabid?->name, 'time' => $submission->rejected_at, 'color' => 'text-danger', 'icon' => 'fa-times-circle'],
                            ['label' => 'Divalidasi', 'user' => $submission->validated_by_name, 'time' => $submission->validated_at, 'color' => 'text-primary', 'icon' => 'fa-check-double'],
                        ];
                    @endphp

                    @foreach($history as $key => $item)
                        @if($item['time'])
                            @php
                                $delay = $key * 0.15;
                                $borderClass = str_replace('text-', 'border-', $item['color']);
                            @endphp
                            <div class="ps-4 py-3 position-relative border-start border-1 border-secondary border-opacity-25 ms-4 my-0 timeline-fade-item" style="animation-delay: {{ $delay }}s;">
                                <div class="position-absolute start-0 translate-middle-x bg-dark border border-1 {{ $borderClass }} rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 20px; height: 20px; top: 20px; z-index: 2;">
                                    <i class="fas {{ $item['icon'] }} {{ $item['color'] }}" style="font-size: 0.5rem;"></i>
                                </div>
                                <div class="d-flex justify-content-between align-items-center gap-3 flex-wrap">
                                    <div>
                                        <small class="{{ $item['color'] }} d-block fw-bold text-uppercase" style="font-size: 0.7rem;">{{ $item['label'] }}</small>
                                        <span class="text-white fw-medium">Oleh: {{ $item['user'] ?? 'Sistem' }}</span>
                                    </div>
                                    <div class="text-end me-3 text-white-50 small">
                                        {{ $item['time']->format('d/m/Y H:i') . $getServerTzLabel($item['time']) }}
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal: Catatan audit BPK --}}
<div class="modal fade" id="catatanAuditModal" tabindex="-1" aria-labelledby="catatanAuditModalLabel" aria-hidden="true" style="backdrop-filter: blur(4px);">
    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content glass-card-dark text-white border border-secondary border-opacity-25 shadow-lg rounded-4" style="backdrop-filter: blur(15px); background: rgba(20, 24, 33, 0.8);">

            <div class="modal-header border-bottom border-secondary border-opacity-25 py-3 px-4">
                <h5 class="modal-title fw-bold text-warning d-flex align-items-center" id="catatanAuditModalLabel" style="font-size: 1.1rem; letter-spacing: .025em;">
                    <i class="fas fa-exclamation-triangle me-2 text-warning animate-pulse"></i> Catatan Auditor
                </h5>
                <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal" aria-label="Close" style="font-size: 0.8rem;"></button>
            </div>

            <form action="{{ route('bpk.audit.submit', $submission->id) }}" method="POST" id="formAuditCatatan">
                @csrf
                <input type="hidden" name="status_audit" value="dengan_catatan">

                <div class="modal-body py-4 px-4">
                    <div class="mb-2">
                        <label for="catatan_audit" class="form-label text-white-50 small fw-bold text-uppercase tracking-wider mb-2">
                            <i class="fas fa-edit me-1 text-info"></i> Tuliskan Rincian Catatan / Temuan Berkas
                        </label>

                        <textarea class="form-control bg-black bg-opacity-40 text-white border border-secondary border-opacity-50 rounded-3 p-3 small custom-textarea-glass"
                                  id="catatan_audit" name="catatan_audit" rows="5" required
                                  placeholder="Contoh: Berkas SPP lembar ke-3 tanda tangan bendahara kurang jelas atau nilai nominal selisih Rp..."
                                  style="resize: none; transition: all 0.2s ease-in-out; font-style: italic; line-height: 1.6;"></textarea>
                    </div>
                </div>

                <div class="modal-footer border-top border-secondary border-opacity-25 py-3 px-4 justify-content-end gap-2">
                    <button type="button" class="btn btn-sm btn-outline-light btn-pill px-3 py-1.5 opacity-75" data-bs-dismiss="modal" style="font-size: 0.85rem;">Batal</button>

                    <button type="submit" class="btn btn-sm btn-warning text-dark btn-pill px-4 py-1.5 fw-bold btn-submit-audit" style="font-size: 0.85rem; box-shadow: 0 4px 12px rgba(255, 193, 7, 0.15);">
                        <i class="fas fa-save me-1"></i> Simpan Hasil Audit
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

{{-- Modal: Pratinjau dokumen pengajuan --}}
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark text-white shadow-lg" style="backdrop-filter: blur(10px);">
            <div class="modal-header ">
                <h5 class="modal-title fw-bold text-white" id="previewModalLabel">
                    <i class="fas fa-file-invoice text-info me-2"></i>Preview Dokumen
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">
                    <div class="col-lg-4 border-end glass-header-dark bg-opacity-20 p-4" style="max-height: 75vh; overflow-y: auto;">
                        <h6 class="text-uppercase text-info fw-bold small mb-3 tracking-wider">Informasi Pengajuan</h6>
                        <div class="mb-3">
                            <label class="text-white-50 small fs-6 fw-bold d-block" id="metaPengaju">Dinas Pengaju</label>
                        </div>
                        <hr class="border-secondary opacity-50 my-2">
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="text-white-50 small d-block">Nomor SPP</label>
                                <code id="metaNoSPP" class="text-info fw-bold d-block">-</code>
                            </div>
                            <div class="col-6">
                                <label class="text-white-50 small d-block">Nomor SPM</label>
                                <code id="metaNoSPM" class="text-warning fw-bold d-block">-</code>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="text-white-50 small d-block">Total Pencairan</label>
                            <span id="metaTotalPencairan" class="fw-bold text-success fs-5">-</span>
                        </div>
                        <hr class="border-secondary opacity-50 my-2">
                        <div class="mb-3">
                            <label class="text-white-50 small d-block">Kegiatan</label>
                            <span id="metaKegiatan" class="text-light small d-block" style="text-align: justify;">-</span>
                        </div>
                        <div class="mb-3">
                            <label class="text-white-50 small d-block">Sub Kegiatan</label>
                            <span id="metaSubKegiatan" class="text-light small d-block" style="text-align: justify;">-</span>
                        </div>
                        <div class="mb-3">
                            <label class="text-white-50 small d-block">Perihal / Keperluan</label>
                            <span id="metaPerihal" class="text-light small d-block" style="text-align: justify;">-</span>
                        </div>
                        <hr class="border-secondary opacity-50 my-2">
                        <div class="row">
                            <div class="col-6 mb-2">
                                <label class="text-white-50 small d-block"><i class="fas fa-calendar-plus me-1 text-primary"></i> Diajukan</label>
                                <span id="metaTglDiajukan" class="small text-white">-</span>
                            </div>
                            <div class="col-6 mb-2">
                                <label class="text-white-50 small d-block"><i class="fas fa-calendar-check me-1 text-success"></i> Validasi</label>
                                <span id="metaTglValidasi" class="small text-white">-</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8 bg-black bg-opacity-40 d-flex flex-column align-items-center justify-content-center" style="height: 75vh; position: relative;">
                        <div id="loadingSpinner" class="d-none text-center" style="position: absolute; z-index: 5;">
                            <div class="spinner-border text-info mb-2" role="status" style="width: 2.5rem; height: 2.5rem;"></div>
                            <p class="small text-white-50 m-0">Memuat berkas PDF...</p>
                        </div>
                        <iframe id="previewIframe" src="" class="w-100 h-100 d-none" style="border: none;"></iframe>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-sm btn-outline-light btn-pill px-3" data-bs-dismiss="modal">Tutup</button>
                <a id="downloadLink" href="" target="_blank" class="btn btn-sm btn-primary btn-pill px-3">
                    <i class="fas fa-download me-1"></i> Unduh Berkas
                </a>
            </div>
        </div>
    </div>
</div>



<script>
if (typeof window.pdfBlobCache === 'undefined') {
    window.pdfBlobCache = {};
}

// Function: Meminta konfirmasi hasil audit sesuai
function confirmAuditSesuai() {
    Swal.fire({
        title: 'Konfirmasi Pemeriksaan',
        text: "Apakah Anda yakin pengajuan berkas ini sudah SESUAI secara keseluruhan?",
        icon: 'question',
        iconColor: '#198754',
        showCancelButton: true,
        confirmButtonText: 'Ya, Sesuai!',
        cancelButtonText: 'Batal',

        customClass: {
            popup: 'swal-glass-popup',
            title: 'swal-glass-title',
            htmlContainer: 'swal-glass-text',
            confirmButton: 'btn btn-success rounded-pill px-4 dynamic-nav-swal',
            cancelButton: 'btn btn-outline-danger text-dark dynamic-nav-swal rounded-pill px-4'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formAuditSesuai').submit();
        }
    });
}

// Function: Membuka pratinjau dokumen audit
function openPreviewModal(url, title, metadata = {}) {
    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    const iframe = document.getElementById('previewIframe');
    const spinner = document.getElementById('loadingSpinner');

    document.getElementById('previewModalLabel').innerHTML = `<i class="fas fa-file-invoice text-info me-2"></i>Preview: ${title}`;
    document.getElementById('downloadLink').href = url;

    const formatRupiah = (num) => {
        return 'Rp ' + new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(num || 0);
    };

    document.getElementById('metaPengaju').textContent = metadata.pengaju || '-';
    document.getElementById('metaPerihal').textContent = metadata.perihal || '-';
    document.getElementById('metaNoSPP').textContent = metadata.nomor_spp || '-';
    document.getElementById('metaNoSPM').textContent = metadata.nomor_spm || '-';
    document.getElementById('metaTotalPencairan').textContent = formatRupiah(metadata.total_pencairan);
    document.getElementById('metaKegiatan').textContent = metadata.judul_kegiatan || '-';
    document.getElementById('metaSubKegiatan').textContent = metadata.judul_sub_kegiatan || '-';
    document.getElementById('metaTglDiajukan').textContent = metadata.tanggal_diajukan;
    document.getElementById('metaTglValidasi').innerHTML = metadata.tanggal_validasi;

    modal.show();

    const loadIframeFromBlob = (blobUrl) => {
        spinner.classList.add('d-none');
        iframe.classList.remove('d-none');
        if (iframe.src !== blobUrl + '#toolbar=0') {
            iframe.src = blobUrl + '#toolbar=0';
        }
    };

    if (window.pdfBlobCache[url]) {
        loadIframeFromBlob(window.pdfBlobCache[url]);
        return;
    }

    spinner.classList.remove('d-none');
    iframe.classList.add('d-none');

    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error('Network error');
            return response.blob();
        })
        .then(blob => {
            const blobUrl = URL.createObjectURL(blob);
            window.pdfBlobCache[url] = blobUrl;
            loadIframeFromBlob(blobUrl);
        })
        .catch(error => {
            console.error('Gagal memuat cache PDF:', error);
            iframe.src = url + '#toolbar=0';
            iframe.onload = function() {
                spinner.classList.add('d-none');
                iframe.classList.remove('d-none');
            };
        });
}

document.addEventListener('keydown', function(event) {
    if (document.activeElement.tagName === 'TEXTAREA' || document.activeElement.tagName === 'INPUT') {
        return;
    }

    @if($previous)
    if (event.key === 'ArrowLeft') {
        window.location.href = "{{ route('bpk.show', $previous->id) }}";
    }
    @endif

    @if($next)
    if (event.key === 'ArrowRight') {
        window.location.href = "{{ route('bpk.show', $next->id) }}";
    }
    @endif
});

</script>


<style>
/* CSS LINE TIMELINE CENTER ALIGNMENT */
.timeline-badge-col::before {
    content: '';
    position: absolute;
    top: 6px;
    bottom: 0;
    left: 50%;
    width: 2px;
    background-color: rgba(255, 255, 255, 0.15);
    transform: translateX(-50%);
    z-index: 1;
}
.timeline-item.is-last .timeline-badge-col::before {
    display: none;
}
/* Styling Utama Popup SweetAlert2 Glassmorphism */
.swal-glass-popup {
    background: rgba(20, 24, 33, 0.75) !important; /* Warna gelap transparan */
    backdrop-filter: blur(12px) !important;
    -webkit-backdrop-filter: blur(12px) !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    border-radius: 20px !important;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5) !important;
}

/* Warna Teks Judul */
.swal-glass-title {
    color: #ffffff !important;
    font-weight: 600;
}

/* Warna Teks Deskripsi/Konten */
.swal-glass-text {
    color: rgba(255, 255, 255, 0.75) !important;
    font-size: 0.95rem;
}

/* Merapikan Jarak Antar Tombol */
.swal-glass-popup .swal2-actions {
    gap: 10px;
}

/* Efek Hover Khusus Tombol Konfirmasi Sukses agar Sinkron dengan Tombol Navigasi Anda sebelumnya */
.dynamic-nav-swal {
    transition: all 0.2s ease;
}
.dynamic-nav-swal:hover {
    background-color: #ffffff !important;
    color: #111111 !important;
    border-color: #ffffff !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;
}

.custom-textarea-glass::placeholder {
    color: rgba(255, 255, 255, 0.7) !important; /* Putih agak soft (70% opacity) agar lebih elegan */
    font-style: italic;
    opacity: 1; /* Diperlukan bagi browser Firefox agar opacity defaultnya tidak menimpa */
}

.bpk-audit-page {
    --bpk-surface: rgba(31, 39, 55, 0.92);
    --bpk-line: rgba(255, 255, 255, 0.09);
    --bpk-muted: rgba(226, 232, 240, 0.65);
}

.bpk-audit-header {
    background: rgba(23, 33, 54, 0.98) !important;
    border: 1px solid var(--bpk-line) !important;
    box-shadow: 0 14px 32px rgba(8, 12, 20, 0.16) !important;
    border-radius: 1.1rem;
}

.bpk-header-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 48px;
    color: #fbbf24;
    background: rgba(245, 158, 11, 0.12);
    border: 1px solid rgba(245, 158, 11, 0.2);
    border-radius: 0.85rem;
}

.bpk-detail-layout {
    align-items: stretch;
}

.bpk-detail-layout > .col-lg-8,
.bpk-detail-layout > .col-lg-4 {
    display: flex;
}

.bpk-documents-card,
.bpk-info-card {
    width: 100%;
    height: 100%;
}

.bpk-documents-card {
    display: flex;
    flex-direction: column;
}

.bpk-documents-card .card-body {
    flex: 1 1 auto;
}

.bpk-info-section {
    background: rgba(255, 255, 255, 0.03);
    border-color: var(--bpk-line) !important;
}

.bpk-info-section .info-collapse-toggle {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    padding: 0;
    border: 0;
    background: transparent;
    color: inherit;
    text-align: left;
    cursor: pointer;
}

.bpk-info-section .info-section-title {
    color: #fff;
    font-size: 0.76rem;
    font-weight: 800;
    letter-spacing: 0.02em;
}

.bpk-info-section .collapse-chevron {
    color: var(--bpk-muted);
    font-size: 0.7rem;
    transition: transform 0.2s ease, color 0.2s ease;
}

.bpk-info-section .info-collapse-toggle:not(.collapsed) .collapse-chevron {
    color: #6ea8fe;
    transform: rotate(180deg);
}

.bpk-info-section .activity-info-list {
    display: flex;
    flex-direction: column;
    gap: 0.85rem;
}

.bpk-info-section .activity-info-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.bpk-info-section .activity-info-label,
.bpk-info-section .info-label {
    color: var(--bpk-muted);
    font-size: 0.7rem;
    font-weight: 700;
}

.bpk-info-section .activity-info-value,
.bpk-info-section .info-value {
    max-width: none;
    color: rgba(255, 255, 255, 0.92);
    font-size: 0.74rem;
    font-weight: 400;
    line-height: 1.5;
    text-align: left;
    overflow-wrap: anywhere;
}

.bpk-info-section .info-row {
    display: flex;
    justify-content: space-between;
    gap: 0.8rem;
    padding-top: 0.85rem;
}

.bpk-document-table {
    --bs-table-bg: transparent;
    --bs-table-color: #f8fafc;
    --bs-table-border-color: rgba(255, 255, 255, 0.08);
}

.bpk-document-table thead th {
    background: rgba(12, 19, 34, 0.48);
    color: rgba(255, 255, 255, 0.58);
    font-size: 0.68rem;
    letter-spacing: 0.045em;
    text-transform: uppercase;
}

.bpk-document-table tbody td {
    padding-top: 0.85rem;
    padding-bottom: 0.85rem;
}

.bpk-outline-button {
    color: #e2e8f0 !important;
    border: 1px solid rgba(96, 165, 250, 0.62) !important;
    background: rgba(37, 99, 235, 0.08) !important;
    opacity: 1 !important;
    box-shadow: 0 0 0 1px rgba(96, 165, 250, 0.05);
    transition: color 0.18s ease, background-color 0.18s ease, border-color 0.18s ease, transform 0.18s ease;
}

.bpk-outline-button:hover,
.bpk-outline-button:focus-visible {
    color: #fff !important;
    border-color: rgba(147, 197, 253, 0.95) !important;
    background: rgba(37, 99, 235, 0.2) !important;
    transform: translateY(-1px);
}

.bpk-outline-button:disabled {
    color: rgba(226, 232, 240, 0.78) !important;
    border-color: rgba(148, 163, 184, 0.52) !important;
    background: rgba(148, 163, 184, 0.07) !important;
    cursor: not-allowed;
    transform: none;
}

.bpk-preview-trigger {
    color: #7dd3fc !important;
    border-color: rgba(56, 189, 248, 0.7) !important;
    background: rgba(14, 165, 233, 0.08) !important;
}

.bpk-preview-trigger:hover,
.bpk-preview-trigger:focus-visible {
    border-color: rgba(125, 211, 252, 0.95) !important;
    background: rgba(14, 165, 233, 0.2) !important;
}

.bpk-document-icon,
.bpk-info-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 2.4rem;
    width: 2.4rem;
    height: 2.4rem;
    border-radius: 0.75rem;
    color: #60a5fa;
    background: rgba(37, 99, 235, 0.14);
    border: 1px solid rgba(96, 165, 250, 0.16);
}

.bpk-document-icon {
    color: #f87171;
    background: rgba(239, 68, 68, 0.12);
    border-color: rgba(248, 113, 113, 0.15);
}

.bpk-document-status,
.bpk-submission-status,
.bpk-audit-result {
    display: inline-flex;
    align-items: center;
    gap: 0.2rem;
    padding: 0.35rem 0.65rem;
    border-radius: 999px;
    font-size: 0.68rem;
    font-weight: 700;
    white-space: nowrap;
}

.bpk-audit-status-panel {
    min-height: 3.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0.75rem;
    border: 1px solid var(--bpk-line);
    border-radius: 0.9rem;
    background: rgba(255, 255, 255, 0.025);
}

.bpk-document-status.success,
.bpk-submission-status.success,
.bpk-audit-result.success {
    color: #4ade80;
    background: rgba(34, 197, 94, 0.1);
    border: 1px solid rgba(74, 222, 128, 0.18);
}

.bpk-document-status.warning,
.bpk-submission-status.secondary,
.bpk-audit-result.pending {
    color: #facc15;
    background: rgba(234, 179, 8, 0.1);
    border: 1px solid rgba(250, 204, 21, 0.18);
}

.bpk-document-status.danger {
    color: #fca5a5;
    background: rgba(239, 68, 68, 0.1);
    border: 1px solid rgba(248, 113, 113, 0.18);
}

.bpk-audit-result.warning {
    color: #facc15;
    background: rgba(234, 179, 8, 0.1);
    border: 1px solid rgba(250, 204, 21, 0.18);
}

.bpk-document-status.secondary {
    color: #cbd5e1;
    background: rgba(148, 163, 184, 0.1);
    border: 1px solid rgba(203, 213, 225, 0.15);
}

.bpk-info-audit-actions {
    display: grid;
    gap: 0.65rem;
    padding-top: 0.25rem;
}

.bpk-info-audit-actions .btn {
    min-height: 2.6rem;
}

.bpk-audit-note {
    padding: 0.85rem 1rem;
    border: 1px solid rgba(250, 204, 21, 0.17);
    border-radius: 0.8rem;
    background: rgba(234, 179, 8, 0.07);
}

.bpk-financial-total {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    padding: 0.85rem;
    border: 1px solid var(--bpk-line);
    border-radius: 0.8rem;
    background: rgba(255, 255, 255, 0.035);
}

.bpk-financial-total strong {
    color: #34d399;
    font-size: 0.95rem;
    white-space: nowrap;
}

.bpk-info-section .info-row {
    align-items: flex-start;
    padding-bottom: 0.65rem;
    border-bottom: 1px solid var(--bpk-line);
}

.bpk-info-section .info-row:last-child {
    border-bottom: 0;
}

@media (max-width: 767.98px) {
    .bpk-audit-header .row {
        row-gap: 1rem;
    }

    .bpk-document-table {
        min-width: 600px;
    }
}

</style>

@endsection
