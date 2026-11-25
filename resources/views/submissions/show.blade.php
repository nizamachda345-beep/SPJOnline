{{-- View: submissions/show --}}
@extends('layouts.app')
@section('content')
<div class="container-fluid pb-3">

    <div class="glass-header-dark shadow-lg mb-3 d-flex justify-content-between align-items-center text-white rounded-4 p-3">

    <div class="d-flex align-items-center">
        <div class="bg-primary bg-opacity-10 p-3 rounded-4 me-3 d-none d-sm-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
            <i class="fas fa-file-invoice-dollar fa-lg text-primary"></i>
        </div>
        <div>
            <span class="label-caps text-info opacity-75 mb-1 d-block" style="font-size: 0.8rem; letter-spacing: 1.5px;">Detail Pengajuan</span>
            <h3 class="fw-bold mb-0" style="letter-spacing: -0.5px;">{{$submission->department->nama_dinas}}</h3>
        </div>
    </div>

    <div class="d-flex align-items-center gap-4">
        <div class="text-end d-none d-lg-block border-end pe-4 border-white border-opacity-10">
            <small class="text-white-50 d-block label-caps mb-1" style="font-size: 0.55rem;">Nomor SPP/SPM</small>
            <span class="fw-bold text-info font-monospace">{{$submission->nomor_spp }} / {{ $submission->nomor_spm }}</span>
        </div>

        <a href="{{ route('submissions.index') }}"
        class="btn btn-outline-light border-0 px-4 py-2 rounded-pill shadow-sm hover-lift d-flex align-items-center custom-btn-glass">
            <i class="fas fa-arrow-left me-2 small"></i>
            <span class="small fw-bold">Kembali</span>
        </a>
    </div>
</div>

    <div class="row g-4">

        <div class="col-lg-4">
            <div class="card glass-card-dark border-0 shadow-lg h-100">
                <div class="card-body p-4">

                    <div class="text-center mb-4">
                        <div class="label-caps mb-3">Progress Pengajuan</div>
                        @php
                            $statusColors = [
                                'pending' => 'bg-warning text-dark',
                                'proses validasi' => 'bg-info text-white',
                                'disetujui' => 'bg-success text-white',
                                'revisi' => 'bg-danger text-white',
                                'direvisi' => 'bg-danger text-white'
                            ];
                            $color = $statusColors[$submission->status] ?? 'bg-danger';
                        @endphp

                        <span class="badge {{ $color }} shadow-sm px-4 py-2 rounded-pill fw-bold" style="font-size: 0.7rem; letter-spacing: 1px; border: 1px solid rgba(255,255,255,0.1);">
                            <i class="fas fa-circle fa-fade me-2 small"></i>

                            @if($submission->status == 'pending')
                                VERIFIKASI
                            @else
                                {{ strtoupper($submission->status) }}
                            @endif
                        </span>
                    </div>

                    <div class="vstack gap-3">
                        <div class="info-box-minimal-dark">
                            <label class="label-caps d-block mb-1">Dinas / Instansi</label>
                            <span class="fw-bold text-white small">{{ $submission->department->nama_dinas }}</span>
                        </div>

                        <div class="info-box-minimal-dark">
                            <label class="label-caps d-block mb-1">Kegiatan</label>
                            <span class="fw-light text-white small lh-sm d-block">{{ $submission->judul_kegiatan }}</span>
                            <hr class="my-2 border-secondary opacity-10">
                            <label class="label-caps d-block mb-1">Sub-Kegiatan</label>
                            <span class="fw-light text-white small">{{ $submission->judul_sub_kegiatan }}</span>
                            <hr class="my-2 border-secondary opacity-10">
                            <label class="label-caps d-block mb-1">Perihal</label>
                            <span class="fw-light text-white small">{{ $submission->perihal }}</span>
                        </div>

                        <div class="info-box-minimal-dark bg-opacity-10 bg-info">
                            <label class="label-caps d-block mb-1">Keterangan</label>
                            <div class="d-flex flex-wrap gap-2 text-muted-show extra-small">
                            <span>
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    {{ $submission->validated_at ? \Carbon\Carbon::parse($submission->validated_at)->translatedFormat('d M Y') : '' }}
                                </span>

                                <span>
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $submission->validated_at ?
                                        \Carbon\Carbon::parse($submission->validated_at)->format('H:i') . ' ' .
                                        (\Carbon\Carbon::parse($submission->validated_at)->offsetHours == 8 ? 'WITA' :
                                        (\Carbon\Carbon::parse($submission->validated_at)->offsetHours == 9 ? 'WIT' : 'WIB'))
                                        : ''
                                    }}
                                </span>
                                <span class="text-info"><i class="fas fa-layer-group me-1"></i> {{ $submission->jenis_pencairan }}</span>
                            </div>
                        </div>

                        <div class="nominal-card">
                            <label class="label-caps text-info d-block mb-1">Total Permintaan</label>
                            <h3 class="fw-bold mb-0 text-white">
                                <small style="font-size: 0.6em; opacity: 0.6;">Rp</small>
                                {{ number_format($submission->total_pencairan, 0, ',', '.') }}
                            </h3>
                        </div>
                    </div>
                </div>

                @if(in_array(strtolower($submission->status), ['disahkan', 'disetujui']))
                <div class="card-footer bg-transparent border-0 pt-0 pb-4 px-4">
                    <a href="{{ route('submissions.download-bundle', $submission->id) }}"
                    class="btn btn-outline-info w-100 py-2 rounded-pill fw-bold d-flex align-items-center justify-content-center gap-2 custom-mini-btn">
                        <i class="fas fa-file-download"></i>
                        <span>Download Naskah Pengajuan</span>
                    </a>
                </div>
                @endif

            </div>
    </div>

    <div class="col-lg-8">
    <div class="card glass-card-dark shadow-lg h-100 overflow-hidden">

        <div class="card-header-dark d-flex justify-content-between align-items-center p-3">
            <h6 class="fw-bold text-white mb-0">
                <i class="fas fa-file me-2 text-primary fs-5"></i>Lampiran Dokumen
            </h6>
            <span class="badge text-primary rounded-pill  px-3 border border-white border-opacity-10">
                {{ $submission->documents->count() }} Dokumen
            </span>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table glass-table align-middle">
                    <thead>
                        <tr>
                            <th class="ps-4 py-3">Nama Dokumen</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="pe-4 py-3 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sortedDocuments = $submission->documents->sortBy(function ($doc) {
                                return stripos($doc->jenis_dokumen, 'sp2d') !== false ? 0 : 1;
                            });
                        @endphp

                        @foreach($sortedDocuments as $doc)

                        <tr class="align-middle border-bottom border-white border-opacity-5 transition-all row-hover-glass"
                            id="doc-row-{{ $doc->id }}"
                            style="background: transparent;">

                            <td class="ps-4 py-3 border-0" style="background: transparent;">
                                <div class="d-flex align-items-center">

                                    <div class="bg-danger bg-opacity-15 p-2.5 rounded-3 me-3 text-danger flex-shrink-0 border border-danger border-opacity-25 shadow-sm" style="width: 38px; height: 38px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-file-pdf fs-6 "></i>
                                    </div>
                                    <div class="overflow-hidden">

                                        <div class="fw-bold text-white mb-0 text-truncate small" style="letter-spacing: 0.3px;">
                                            @php
                                                $jenisDokumen = strtolower(trim($doc->jenis_dokumen));

                                                $documentLabels = [
                                                    'dokumen ls' => 'DOKUMEN LS (SPP, Verifikasi SPP/SPM, SPTJM, Billing)',

                                                    'dokumen tu' => 'DOKUMEN TU (SPP, Verifikasi SPP/SPM, SPTJM, Persetujuan BUD)',

                                                    'dokumen gu' => 'DOKUMEN GU (SPP, Verifikasi SPP/SPM, SPTJM, Persetujuan BUD)',

                                                    'dokumen up' => 'DOKUMEN UP (SPP, Verifikasi SPP/SPM, SPTJM)',

                                                    'dokumen tu nihil' => 'DOKUMEN TU NIHIL (SPP, Verifikasi SPP/SPM, SPTJM, Persetujuan BUD)',

                                                    'dokumen spj' => 'DOKUMEN SPJ',

                                                    'spj' => 'DOKUMEN SPJ',

                                                    'dokumen sts' => 'DOKUMEN STS',

                                                    'sts' => 'DOKUMEN STS',

                                                    'dokumen sp2d' => 'DOKUMEN SP2D (SP2D pada pengajuan TU)',

                                                    'sp2d' => 'DOKUMEN SP2D (SP2D pada pengajuan TU)',

                                                    'sp2d tu' => 'DOKUMEN SP2D (SP2D pada pengajuan TU)',
                                                ];
                                            @endphp

                                            {{ $documentLabels[$jenisDokumen] ?? strtoupper($doc->jenis_dokumen) }}
                                        </div>

                                        <span class="text-white-50 opacity-50 d-block mt-0.5" style="font-size: 0.7rem;">
                                            <i class="far fa-calendar-alt me-1"></i> PDF • {{ $doc->created_at->format('d M Y') }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <td class="text-center py-3 border-0" style="background: transparent;">
                                @php
                                    $isSp2d = stripos($doc->jenis_dokumen, 'sp2d') !== false;
                                @endphp

                                @if($doc->status_dokumen == 'verified')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2.5 py-1.5 small fw-medium">
                                        <i class="fas fa-check-circle me-1"></i> {{ $isSp2d ? 'Terverifikasi' : 'Disetujui' }}
                                    </span>
                                @elseif($doc->status_dokumen == 'revisi')
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-2.5 py-1.5 small fw-medium cursor-pointer"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Catatan: {{ $doc->catatan }}">
                                        <i class="fas fa-exclamation-circle me-1"></i> Revisi
                                    </span>
                                @elseif($doc->status_dokumen == 'uploaded')
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-2.5 py-1.5 small fw-medium">
                                        <i class="fas fa-hourglass-half me-1"></i> Menunggu Verifikasi
                                    </span>
                                @elseif($doc->status_dokumen == 'expired')
                                    <span class="badge bg-danger bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-2.5 py-1.5 small fw-medium">
                                        <i class="fas fa-repeat me-1"></i> Expired
                                    </span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-2.5 py-1.5 small fw-medium">
                                        <i class="fas fa-spinner fa-spin me-1"></i> Verifikasi
                                    </span>
                                @endif
                            </td>

                            <td class="pe-4 py-3 text-end border-0" style="background: transparent;">
                                <div class="d-flex justify-content-end gap-2">

                                    <span class="d-inline-block" data-bs-toggle="tooltip" data-bs-placement="top" title="Pratinjau">
                                        <button class="btn btn-outline-primary btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center border-opacity-25 transition-all btn-preview-pdf"
                                                style="width: 32px; height: 32px; background: rgba(13, 110, 253, 0.05);"
                                                data-url="{{ asset('storage/' . $doc->file_path) }}"
                                                data-title="{{ $documentLabels[$jenisDokumen] ?? strtoupper($doc->jenis_dokumen) }}"
                                                data-spp="{{ $submission->nomor_spp }}"
                                                data-spm="{{ $submission->nomor_spm }}"
                                                data-kegiatan="{{ $submission->judul_kegiatan }}"
                                                data-subkegiatan="{{ $submission->judul_sub_kegiatan }}"
                                                data-perihal="{{ $submission->perihal }}"
                                                data-nominal="{{ $submission->total_pencairan }}"
                                                data-tanggal="{{ $submission->created_at->format('d/m/Y') }}">
                                            <i class="fas fa-eye fa-xs text-primary"></i>
                                        </button>
                                    </span>

                                    @if($doc->status_dokumen == 'revisi')
                                    <span class="d-inline-block" data-bs-toggle="tooltip" data-bs-placement="top" title="Revisi Dokumen">
                                        <button class="btn btn-outline-warning btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center border-opacity-25 transition-all"
                                                style="width: 32px; height: 32px; background: rgba(255, 193, 7, 0.05);"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalUpdate{{ $doc->id }}">
                                            <i class="fas fa-upload fa-xs text-warning"></i>
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
    </div>
</div>
</div>
</div>

{{-- Modal: Pratinjau PDF pengajuan --}}
<div class="modal fade" id="modalPreviewPDF" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content glass-dark-modal">

            <div class="modal-header border-bottom border-glass-white pb-3">
                <div class="d-flex align-items-center">
                    <div class="icon-box-glass me-3">
                        <i class="fas fa-file-pdf text-danger fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-white letter-spacing-1" id="pdfModalTitle">Pratinjau Dokumen</h6>
                        <small class="text-light-50" id="modalSubtitle">Detail Informasi Dokumen</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-0">
                <div class="row g-0">

                    <div class="col-lg-9 border-end border-glass-white">
                        <div class="pdf-wrapper bg-black-20">
                            <iframe id="pdfFrame" src="" width="100%" height="100%" frameborder="0"></iframe>
                        </div>
                    </div>

                    <div class="col-lg-3 bg-glass-side">
                        <div class="p-4 info-panel">
                            <h5 class="text-info-light mb-4 fs-7 text-uppercase fw-bold ls-2">Detail Pengajuan</h5>

                            <div class="info-item mb-4">
                                <label class="text-light-50 d-block tiny-text text-uppercase fw-bold mb-1">Nomor SPP / SPM</label>
                                <div class="d-flex align-items-center text-light">
                                    <span class="fw-semibold" id="infoNomorSpp">-</span>
                                    <span class="mx-2 opacity-25">/</span>
                                    <span class="fw-semibold" id="infoNomorSpm">-</span>
                                </div>
                            </div>

                            <div class="info-item mb-4">
                                <label class="text-light-50 d-block tiny-text text-uppercase fw-bold mb-1">Judul Kegiatan</label>
                                <span class="text-light fw-normal lh-sm d-block" id="infoKegiatan">-</span>
                            </div>

                            <div class="info-item mb-4">
                                <label class="text-light-50 d-block tiny-text text-uppercase fw-bold mb-1">Sub Kegiatan</label>
                                <span class="text-light fw-normal lh-sm d-block" id="infoSubKegiatan">-</span>
                            </div>

                            <div class="info-item mb-4">
                                <label class="text-light-50 d-block tiny-text text-uppercase fw-bold mb-1">Perihal</label>
                                <span class="text-light fw-normal lh-sm d-block" id="infoPerihal">-</span>
                            </div>

                            <div class="info-item mb-4">
                                <label class="text-light-50 d-block tiny-text text-uppercase fw-bold mb-1">Total Permintaan</label>
                                <span class="text-info fw-bold" id="infoTotalPencairan">-</span>
                            </div>

                            <hr class="border-glass-white my-4 opacity-10">

                            <div class="info-item">
                                <label class="text-light-50 d-block tiny-text text-uppercase fw-bold mb-1">Tanggal Pengajuan</label>
                                <span class="text-light-75 fw-medium" id="infoTanggal">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top border-glass-white bg-black-20">
                <a id="downloadPdfLink" href="#" class="btn btn-glass-info btn-sm px-4" download>
                    <i class="fas fa-download me-2"></i> Download PDF
                </a>
                <button type="button" class="btn btn-glass-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const pdfModalElement = document.getElementById('modalPreviewPDF');
    const pdfModal = new bootstrap.Modal(pdfModalElement);

    const formatRupiah = (angka) => {
        if (isNaN(angka) || angka === null) return '-';
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(angka);
    };

    document.addEventListener('click', function(e) {
        const button = e.target.closest('.btn-preview-pdf');

        if (button) {
            e.preventDefault();

            const data = {
                url: button.getAttribute('data-url'),
                title: button.getAttribute('data-title'),
                spp: button.getAttribute('data-spp') || '-',
                spm: button.getAttribute('data-spm') || '-',
                kegiatan: button.getAttribute('data-kegiatan') || '-',
                sub: button.getAttribute('data-subkegiatan') || '-',
                perihal: button.getAttribute('data-perihal') || '-',
                nominal: button.getAttribute('data-nominal'),
                tanggal: button.getAttribute('data-tanggal') || '-'
            };

            document.getElementById('pdfFrame').src = data.url;
            document.getElementById('pdfModalTitle').innerText = data.title;
            document.getElementById('downloadPdfLink').href = data.url;

            document.getElementById('infoNomorSpp').innerText = data.spp;
            document.getElementById('infoNomorSpm').innerText = data.spm;
            document.getElementById('infoKegiatan').innerText = data.kegiatan;
            document.getElementById('infoSubKegiatan').innerText = data.sub;
            document.getElementById('infoPerihal').innerText = data.perihal;
            document.getElementById('infoTanggal').innerText = data.tanggal;
            document.getElementById('infoTotalPencairan').innerText = formatRupiah(data.nominal);

            pdfModal.show();
        }
    });

    pdfModalElement.addEventListener('hidden.bs.modal', function () {
        document.getElementById('pdfFrame').src = "";
    });
});
</script>



@foreach($submission->documents as $doc)
    @if($doc->status_dokumen == 'revisi' || $doc->status_dokumen == 'pending')
    {{-- Modal: Unggah ulang dokumen revisi --}}
    <div class="modal fade" id="modalUpdate{{ $doc->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">

            <div class="modal-content border-0 shadow-lg overflow-hidden"
                 style="background: rgba(20, 27, 45, 0.75);
                        backdrop-filter: blur(25px);
                        -webkit-backdrop-filter: blur(25px);
                        border: 1px solid rgba(255, 255, 255, 0.08) !important;
                        border-radius: 1.25rem;">

                <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex align-items-center justify-content-between">
                    <h6 class="modal-title fw-bold text-white mb-0 d-flex align-items-center" style="letter-spacing: 0.02em;">
                        <i class="fas fa-file-upload me-2.5 text-warning"></i>Perbaikan Dokumen
                    </h6>
                    <button type="button" class="btn-close btn-close-white opacity-50 small" data-bs-dismiss="modal" aria-label="Close" style="font-size: 0.75rem; box-shadow: none;"></button>
                </div>

                <form action="{{ route('submissions.update', $submission->id) }}" method="POST" enctype="multipart/form-data" class="no-ajax">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="nomor_spp" value="{{ $submission->nomor_spp }}">
                    <input type="hidden" name="nomor_spm" value="{{ $submission->nomor_spm }}">
                    <input type="hidden" name="total_pencairan" value="{{ $submission->total_pencairan }}">

                    <div class="modal-body p-4">

                        <div class="mb-4 p-3 rounded-3"
                             style="background: rgba(239, 68, 68, 0.06);
                                    border: 1px solid rgba(239, 68, 68, 0.15);
                                    box-shadow: inset 0 1px 1px rgba(255,255,255,0.02);">
                            <div class="text-danger fw-bold small uppercase-tracking mb-1" style="font-size: 0.72rem; letter-spacing: 0.8px;">
                                <i class="fas fa-exclamation-triangle me-1.5"></i>ALASAN REVISI / CATATAN:
                            </div>
                            <div class="text-white-50 small lh-base" style="font-size: 0.82rem;">
                                {{ $doc->catatan ?? 'Tidak ada catatan detail yang dilampirkan oleh verifikator.' }}
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label text-light-50 small mb-2" style="font-size: 0.8rem; opacity: 0.75;">
                                Unggah Salinan PDF Baru Untuk:
                            </label>
                            <div class="fw-semibold text-white mb-2 pb-2 border-bottom border-white border-opacity-5" style="font-size: 0.9rem; letter-spacing: 0.02em;">
                                <i class="far fa-file-alt me-1.5 text-info"></i>{{ strtoupper(str_replace('_', ' ', $doc->jenis_dokumen)) }}
                            </div>

                            <div class="position-relative custom-input-file-container">
                                <input type="file"
                                       name="files[{{ $doc->id }}]"
                                       class="form-control form-control-glass custom-file-upload-input"
                                       accept="application/pdf"
                                       required>
                                <div class="file-custom-placeholder text-white-50 opacity-40 mt-1">
                                    <i class="fas fa-cloud-upload-alt me-2"></i>Pilih berkas format PDF...
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer border-0 pt-0 pb-4 px-4 d-flex gap-2 justify-content-end">
                        <button type="button" class="btn btn-sm px-4 rounded-pill text-white border-white border-opacity-10 transition-all hover-bg-opacity"
                                data-bs-dismiss="modal"
                                style="background: rgba(255, 255, 255, 0.03); font-size: 0.78rem;">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-sm px-4 rounded-pill fw-medium text-white transition-all hover-glow"
                                style="background: linear-gradient(135deg, #eab308 0%, #ca8a04 100%);
                                       border: none;
                                       box-shadow: 0 4px 15px rgba(234, 179, 8, 0.2);
                                       font-size: 0.78rem;">
                            <i class="fas fa-check-circle me-1.5"></i>Kirim Perbaikan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
@endforeach


<style>
                    .custom-mini-btn {
                        font-size: 0.85rem;
                        border-color: rgba(13, 202, 240, 0.4);
                        background-color: rgba(13, 202, 240, 0.05);
                        transition: all 0.2s ease;
                    }
                    .custom-mini-btn:hover {
                        background-color: #0dcaf0;
                        color: #000 !important;
                        border-color: #0dcaf0;
                        transform: translateY(-1px);
                        box-shadow: 0 0.25rem 0.75rem rgba(13, 202, 240, 0.25);
                    }
                </style>

@endsection
