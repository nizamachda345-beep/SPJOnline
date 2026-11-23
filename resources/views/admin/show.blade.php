{{-- View: admin/show --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid min-vh-100 text-light py-2">

    <div class="card glass-card-dark border-0 rounded-4 p-3 mb-3">
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
            <div class="d-flex align-items-center">
                    <div class="rounded-3 me-3 d-flex align-items-center justify-content-center border border-primary border-opacity-50" style="width: 44px; height: 44px; background: rgba(37, 99, 235, .16);">
                        <i class="fas fa-user text-primary fs-5"></i>
                    </div>
                <div>
                    <h4 class="fw-bold mb-1 tracking-tight text-white">Detail Monitoring Berkas</h4>
                    <p class="text-light opacity-50 small mb-0">ID Pengajuan: #{{ $submission->id }} • Sistem Manajemen Arsip</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
          <span class="badge rounded-pill border py-2 px-3 tracking-wider fw-bold x-small
    {{ $submission->status == 'disetujui' ? 'bg-white text-success border-white' :
       ($submission->status == 'proses verifikasi' || $submission->status == 'verified' ? 'bg-info bg-opacity-10 text-info border-info border-opacity-25' :
       ($submission->status == 'proses validasi' ? 'bg-info bg-opacity-10 text-info border-info border-opacity-25' :
       ($submission->status == 'reset' ? 'bg-danger bg-opacity-10 text-danger border-danger border-opacity-25' :
       'bg-warning bg-opacity-10 text-warning border-warning border-opacity-25'))) }}">

    <i class="fas {{ $submission->status == 'reset' ? 'fa-undo-alt' : 'fa-circle fa-fade' }} me-1 fa-xs"></i>

    {{ $submission->status == 'disetujui' ? 'DISAHKAN' :

       ($submission->status == 'proses validasi' ? 'VALIDASI' :
       strtoupper($submission->status)) }}
</span>
                <a href="{{ route('admin.monitoring') }}" class="btn btn-outline-light btn-sm rounded-pill px-4">
                    <i class="fas fa-arrow-left me-1.5"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4 align-items-stretch mb-4">

        <div class="col-xl-7">
            <div class="card glass-card-dark rounded-4 overflow-hidden h-100 d-flex flex-column">
                <div class="card-header glass-header-dark d-flex justify-content-between align-items-center py-3 flex-shrink-0">
                    <h6 class="mb-0 fw-bold text-white"><i class="fas fa-file-pdf me-2 text-danger fs-5"></i>Lampiran Dokumen</h6>
                    <span class="badge bg-white text-danger border border-danger border-opacity-25 rounded-pill x-small fw-bold px-3 py-2">
                        {{ $submission->documents->count() }} DOKUMEN
                    </span>
                </div>

                <div class="card-body p-0 flex-grow-1 overflow-auto" style="max-height: 380px;">
                    <div class="list-group list-group-flush bg-transparent">
                        @forelse($submission->documents as $doc)
                            <div class="bg-transparent d-flex justify-content-between align-items-center py-3 px-4 transition-all hover-glow {{ !$loop->last ? 'border-bottom border-white border-opacity-10' : '' }}">
                                <div class="d-flex align-items-center me-2 overflow-hidden">
                                    <div class="file-icon bg-danger p-2 rounded-3 me-3 text-white flex-shrink-0">
                                        <i class="far fa-file-pdf fs-5 text-white"></i>
                                    </div>
                                    <div class="text-truncate">
                                        <div class="fw-bold mb-0 small text-light text-truncate">{{ strtoupper($doc->jenis_dokumen) }}</div>
                                        <small class="extra-small text-white opacity-40"> {{ $doc->updated_at->timezone('Asia/Makassar')->format('d M Y, H:i') }} {{ $doc->updated_at->timezone('Asia/Makassar')->offsetHours == 7 ? 'WIB' : ($doc->updated_at->timezone('Asia/Makassar')->offsetHours == 8 ? 'WITA' : 'WIT') }}</small>
                                    </div>
                                </div>
                                    <button type="button"
                                            class="btn btn-glass-primary btn-sm rounded-pill px-3 fw-bold shadow-sm"
                                            data-url="{{ asset('storage/' . $doc->file_path) }}"
                                            data-title="{{ strtoupper(str_replace('_', ' ', $doc->jenis_dokumen)) }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalPreview">
                                            <i class="fas fa-eye me-2"></i> Lihat
                                    </button>
                            </div>
                        @empty
                            <div class="p-5 text-center text-white opacity-40 small fst-italic h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-folder-open d-block fs-2 mb-2 opacity-50 text-danger"></i>Tidak ada dokumen dilampirkan.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="card glass-card-dark border-0 rounded-4 overflow-hidden h-100 d-flex flex-column">
                <div class="card-header glass-header-dark border-0 py-3 flex-shrink-0">
                    <h6 class="mb-0 fw-bold text-white"><i class="fas fa-info-circle me-2 text-primary"></i>Detail Pengajuan</h6>
                </div>

                <div class="card-body flex-grow-1 p-3 p-lg-4">
                    <div class="mb-3 p-3 rounded-4" style="background: rgba(255,255,255,0.025); border: 1px solid rgba(255,255,255,0.07);">
                        <label class="text-uppercase text-primary fw-bold mb-1 d-block tracking-wider" style="font-size: 0.65rem;">Instansi Pengaju</label>
                        <div class="fw-bold text-white">{{ $submission->department->nama_dinas ?? '-' }}</div>
                    </div>

                    <section class="info-section mb-2">
                        <div class="info-section-title text-light"><i class="fas fa-file-alt me-2"></i>Data Pengajuan</div>
                        <div class="info-row">
                            <span class="info-label">Jenis Pencairan</span>
                            <span class="info-value"><span class="jenis-badge">{{ strtoupper($submission->jenis_pencairan ?? '-') }}</span></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Nomor SPP/SPM</span>
                            <span class="info-value">{{ $submission->nomor_spp ?? '-' }} / {{ $submission->nomor_spm ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Tanggal Berkas</span>
                            <span class="info-value">{{ $submission->tanggal ? \Carbon\Carbon::parse($submission->tanggal)->translatedFormat('d F Y') : '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Tanggal Pengajuan</span>
                            <span class="info-value">{{ $submission->created_at?->translatedFormat('d F Y, H:i') ?? '-' }}</span>
                        </div>
                    </section>

                    <section class="info-section mb-2">
                        <div class="info-section-title text-light"><i class="fas fa-tasks me-2"></i>Informasi Kegiatan</div>
                        <div class="info-row">
                            <span class="info-label">Kegiatan</span>
                            <span class="info-value">{{ $submission->judul_kegiatan ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Sub Kegiatan</span>
                            <span class="info-value">{{ $submission->judul_sub_kegiatan ?? '-' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Perihal</span>
                            <span class="info-value">{{ $submission->perihal ?? '-' }}</span>
                        </div>
                    </section>

                    <section class="info-section mb-0">
                        <div class="info-section-title text-light"><i class="fas fa-money-bill-wave me-2"></i>Informasi Keuangan</div>
                        <div class="info-row">
                            <span class="info-label">Sumber Dana</span>
                            <span class="info-value">{{ $submission->sumber_dana ?? '-' }}</span>
                        </div>
                        <div class="info-total">
                            <div><span class="info-total-label">Total Permintaan</span><small>Nilai pengajuan</small></div>
                            <strong>Rp {{ number_format($submission->total_pencairan ?? 0, 0, ',', '.') }}</strong>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">

    <div class="col-xl-7">
        <div class="card glass-card-dark border-0 rounded-4 overflow-hidden h-100">
            <div class="card-header glass-header-dark border-0 py-3">
                <h6 class="mb-0 fw-bold text-white"><i class="fas fa-history me-2 text-warning"></i>Riwayat Pengajuan</h6>
            </div>
            <div class="card-body p-4">
                @php
                    $timelineEvents = [
                        [
                            'at' => $submission->created_at,
                            'title' => 'Berkas Diajukan',
                            'actor' => $submission->user->name ?? 'System',
                            'marker' => 'bg-info',
                            'icon' => 'fas fa-plus',
                            'icon_color' => 'text-dark',
                            'title_color' => 'text-white',
                        ],
                        [
                            'at' => $submission->revisi_at,
                            'title' => 'Dikembalikan',
                            'actor' => $submission->penolak->name ?? 'Sistem / Verifikator',
                            'marker' => 'bg-dark',
                            'icon' => 'fas fa-exclamation-triangle',
                            'icon_color' => 'text-danger',
                            'title_color' => 'text-light opacity-75',
                        ],
                        [
                            'at' => $submission->revised_at,
                            'title' => 'Direvisi',
                            'actor' => $submission->user->name ?? 'Sistem',
                            'marker' => 'bg-warning',
                            'icon' => 'fas fa-file-pen',
                            'icon_color' => 'text-dark',
                            'title_color' => 'text-warning tracking-wider text-uppercase',
                        ],
                        [
                            'at' => $submission->verified_at,
                            'title' => 'DIVERIFIKASI',
                            'actor' => $submission->verified_by_name,
                            'marker' => 'bg-success',
                            'icon' => 'fas fa-check',
                            'icon_color' => 'text-white',
                            'title_color' => 'text-light',
                        ],
                        [
                            'at' => $submission->validated_at,
                            'title' => 'Disahkan',
                            'actor' => $submission->validated_by_name,
                            'marker' => 'bg-primary',
                            'icon' => 'fas fa-file-signature',
                            'icon_color' => 'text-white',
                            'title_color' => 'text-white tracking-wider text-uppercase',
                        ],
                        [
                            'at' => $submission->rejected_at,
                            'title' => 'Ditolak',
                            'actor' => $submission->penolakKabid->name ?? 'Sistem',
                            'marker' => 'bg-danger',
                            'icon' => 'fas fa-times',
                            'icon_color' => 'text-white',
                            'title_color' => 'text-danger tracking-wider text-uppercase',
                            'note' => $submission->catatan_kabid,
                        ],
                        [
                            'at' => $submission->reset_at,
                            'title' => 'Direset Sistem',
                            'marker' => 'bg-dark',
                            'icon' => 'fas fa-undo',
                            'icon_color' => 'text-danger',
                            'title_color' => 'text-white-50',
                            'description' => 'Status dikembalikan paksa ke antrean awal.',
                        ],
                    ];
                    $timelineEvents = array_values(array_filter($timelineEvents, fn ($event) => $event['at'] !== null));
                    usort($timelineEvents, fn ($left, $right) => $left['at']->getTimestamp() <=> $right['at']->getTimestamp());
                @endphp

                <div class="simple-timeline position-relative">

                    <div class="timeline-line" style="position: absolute; left: 11px; top: 10px; bottom: 10px; width: 2px; background: rgba(255,255,255,0.08); z-index: 1;"></div>

                    @foreach($timelineEvents as $event)
                    <div class="position-relative z-2 {{ $loop->last ? '' : 'mb-4' }}">
                        <div class="d-flex align-items-start">
                            <div class="rounded-circle {{ $event['marker'] }} d-flex align-items-center justify-content-center shadow-sm border border-2 border-dark" style="width: 24px; height: 24px; min-width: 24px;">
                                <i class="{{ $event['icon'] }} {{ $event['icon_color'] }}" style="font-size: 0.55rem;"></i>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <div class="mb-1 d-flex align-items-center flex-wrap gap-2">
                                    <h6 class="mb-0 fw-bold {{ $event['title_color'] }} small">{{ $event['title'] }}</h6>
                                    <small class="text-light opacity-50" style="font-size: 0.65rem;">
                                        {{ $event['at']->timezone('Asia/Makassar')->format('d M Y, H:i') }} WITA
                                    </small>
                                </div>
                            @if(!empty($event['actor']))
                                <p class="text-light opacity-50 mb-{{ !empty($event['note']) ? '1' : '0' }} x-small">Oleh: <span class="fw-semibold text-white">{{ $event['actor'] }}</span></p>
                            @endif
                            @if(!empty($event['description']))
                                <p class="text-light opacity-50 mb-0 x-small">{{ $event['description'] }}</p>
                            @endif
                            @if(!empty($event['note']))
                                <div class="border border-danger text-light rounded-3 p-2 mt-1 extra-small">
                                    <strong class="text-danger">Catatan:</strong> {{ $event['note'] }}
                                </div>
                            @endif
                            </div>
                        </div>
                    </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="card glass-header-dark border-0 rounded-4 overflow-hidden shadow-lg position-relative h-100">
            <div class="position-absolute top-0 end-0 p-3 opacity-10">
                <i class="fas fa-user-shield fa-5x text-danger"></i>
            </div>
            <div class="card-body p-4 text-center d-flex flex-column justify-content-center align-items-center position-relative" style="z-index: 2; min-height: 240px;">
                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1.5 x-small fw-bold tracking-wider mb-2">
                    Otoritas Super Admin
                </span>
                <h6 class="fw-bold text-white mb-2" style="font-size: 0.95rem;">PANEL KONTROL DESTRUKTIF</h6>
                <p class="extra-small text-white opacity-50 mb-4 mx-auto style-lh" style="max-width: 90%;">Gunakan tombol berikut untuk membatalkan semua verifikasi dan mengembalikan berkas ke antrean awal jika terdapat kekeliruan struktural.</p>

                <form action="{{ route('monitoring.reset', $submission->id) }}" method="POST" class="w-100" data-confirm-delete="Reset status berkas <strong>{{ $submission->nomor_spp_spm ?? $submission->nomor_spp }}</strong> ke Pending? Semua proses verifikasi sebelumnya akan dihapus permanen.">
                    @csrf
                    <button type="submit" class="btn btn-danger rounded-pill w-100 fw-bold py-2 shadow-sm shadow-glow-danger transition-all hover-glow-danger">
                        <i class="fas fa-undo-alt me-1.5"></i> Reset Pengajuan
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
</div>

{{-- Modal: Pratinjau dokumen pengajuan --}}
<div class="modal fade" id="modalPreview" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 95vw;">
        <div class="modal-content border-white border-opacity-10 bg-dark shadow-lg rounded-4 overflow-hidden text-white" style="background: rgba(20, 24, 33, 0.98) !important; backdrop-filter: blur(10px); height: 90vh;">
            <div class="modal-body p-0 d-flex flex-column flex-lg-row h-100">

                <div class="flex-grow-1 bg-black bg-opacity-40 position-relative h-100">
                    <div id="loader" class="position-absolute top-50 start-50 translate-middle text-center">
                        <div class="spinner-border text-primary border-3"></div>
                    </div>
                    <iframe id="pdfFrame" src="" frameborder="0" style="width: 100%; height: 100%; display: none;"></iframe>
                </div>

                <div class="p-3 d-flex flex-column border-start border-white border-opacity-10 bg-dark bg-opacity-5 shadow-sm h-100" style="width: 280px; min-width: 280px; overflow-y: auto;">

                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="fw-black text-white text-uppercase my-2 tracking-tight text-truncate" id="docTitle" style="font-size: 0.85rem;">PRATINJAU</h6>
                        <button type="button" class="btn-close btn-close-white p-1" style="font-size: 0.75rem;" data-bs-dismiss="modal"></button>
                    </div>

                    <hr class="border-white border-opacity-50 my-1.5">

                    <div class="mb-3 d-flex flex-column gap-2">
                        <div>
                            <label class="text-white-50 text-uppercase tracking-wider fw-semibold mb-0" style="font-size: 0.6rem;">Nomor SPP / SPM</label>
                            <div class="fw-bold text-white text-break" style="font-size: 0.75rem;">{{ $submission->nomor_spp ?? '-' }} / {{ $submission->nomor_spm  ?? '-' }}</div>
                        </div>
                        <div>
                            <label class="text-white-50 text-uppercase tracking-wider fw-semibold mb-0" style="font-size: 0.6rem;">Kegiatan</label>
                            <div class="text-white text-break" style="line-height: 1.2; font-size: 0.75rem;">{{ $submission->judul_kegiatan ?? '-' }}</div>
                        </div>
                        <div>
                            <label class="text-white-50 text-uppercase tracking-wider fw-semibold mb-0" style="font-size: 0.6rem;">Sub Kegiatan</label>
                            <div class="text-white text-break" style="line-height: 1.2; font-size: 0.75rem;">{{ $submission->judul_sub_kegiatan ?? '-' }}</div>
                        </div>
                        <div>
                            <label class="text-white-50 text-uppercase tracking-wider fw-semibold mb-0" style="font-size: 0.6rem;">Perihal</label>
                            <div class="text-white-50 text-break" style="line-height: 1.2; font-size: 0.75rem;">{{ $submission->perihal ?? '-' }}</div>
                        </div>

                        <div class="p-2 rounded-3 mt-1" style="background: rgba(34, 197, 94, 0.06); border: 1px solid rgba(34, 197, 94, 0.12);">
                            <label class="text-success text-uppercase tracking-wider fw-bold mb-0" style="font-size: 0.55rem; letter-spacing: 0.5px;">Total Nilai Pencairan</label>
                            <div class="fw-black text-white tracking-tight" style="font-size: 1.1rem;">
                                Rp {{ number_format($submission->total_pencairan ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-primary bg-primary bg-opacity-5 border-0 text-white-50 rounded-3 mb-3 p-2" style="line-height: 1.3; font-size: 0.68rem;">
                        <i class="fas fa-info-circle me-1 text-light"></i> Verifikasi kecocokan tanda tangan, stempel, serta nominal dengan sistem.
                    </div>

                    <div class="mt-auto gap-1.5 d-grid pt-2">
                        <a id="btnDownload" href="" download class="btn btn-glass-action rounded-pill fw-bold btn-xs p-2 text-light mb-3 " style="font-size: 0.75rem;">
                            <i class="fas fa-download me-2 text-primary"></i>Unduh Berkas
                        </a>
                        <button class="btn btn-primary rounded-pill fw-bold btn-xs p-2" style="font-size: 0.75rem;" data-bs-dismiss="modal">Selesai Membaca</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    let loadingTimeout;

    const modalElement = document.getElementById('modalPreview');
    const iframe = document.getElementById('pdfFrame');
    const loader = document.getElementById('loader');
    const downloadBtn = document.getElementById('btnDownload');
    const docTitle = document.getElementById('docTitle');

    if (modalElement) {
        modalElement.addEventListener('show.bs.modal', function (event) {
            const targetBtn = event.relatedTarget;

            if (targetBtn) {
                const currentUrl = targetBtn.getAttribute('data-url');
                const currentTitle = targetBtn.getAttribute('data-title');

                if (docTitle) docTitle.innerText = titleCase(currentTitle);
                if (downloadBtn) downloadBtn.href = currentUrl;

                iframe.style.display = 'none';
                loader.style.display = 'block';

                initiatePreview(currentUrl);
            }
        });

        modalElement.addEventListener('hidden.bs.modal', resetModalState);
    }

    // Function: Membuka pratinjau dokumen pengajuan
    function initiatePreview(url) {
        if (loadingTimeout) clearTimeout(loadingTimeout);

        iframe.src = '';

        iframe.src = url;

        loadingTimeout = setTimeout(() => {
            loader.style.display = 'none';
            iframe.style.display = 'block';
        }, 600);
    }

    // Function: Mengatur ulang keadaan modal pratinjau
    function resetModalState() {
        if (loadingTimeout) clearTimeout(loadingTimeout);
        iframe.style.display = 'none';
        iframe.src = '';
        loader.style.display = 'none';
    }

    // Function: Mengubah teks menjadi format title case
    function titleCase(str) {
        return str ? str.toUpperCase() : 'PRATINJAU DOKUMEN';
    }
});
</script>
@endsection
