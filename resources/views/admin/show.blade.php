@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Breadcrumbs --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb bg-light rounded-3 px-3 py-2">
            <li class="breadcrumb-item">
                <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">
                    <i class="fas fa-home me-1"></i> Dashboard
                </a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{ route('admin.monitoring') }}" class="text-decoration-none">Monitoring</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Detail Berkas #{{ $submission->id }}</li>
        </ol>
    </nav>

    {{-- Header & Tombol Kembali --}}
    <div class="row mb-4">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ route('admin.monitoring') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <h4 class="fw-bold m-0 text-dark">Detail Monitoring Berkas</h4>
            <div>
                <span class="badge rounded-pill {{ $submission->status == 'disetujui' ? 'bg-success' : ($submission->status == 'verified' ? 'bg-info' : 'bg-warning') }} px-3 py-2">
                    {{ strtoupper($submission->status) }}
                </span>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Kolom Kiri: Informasi Utama --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="m-0 fw-bold text-primary"><i class="fas fa-info-circle me-2"></i>Informasi Pengajuan</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted small uppercase">Nomor SPP/SPM</div>
                        <div class="col-sm-8 fw-bold text-dark">{{ $submission->nomor_spp_spm }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted small uppercase">Instansi Pengirim</div>
                        <div class="col-sm-8 text-dark">{{ $submission->department->nama_dinas }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted small uppercase">Total Pencairan</div>
                        <div class="col-sm-8 fw-bold text-primary fs-5">Rp {{ number_format($submission->total_pencairan, 0, ',', '.') }}</div>
                    </div>
                    <div class="row mb-0">
                        <div class="col-sm-4 text-muted small uppercase">Keterangan</div>
                        <div class="col-sm-8 text-dark">{{ $submission->keterangan ?? '-' }}</div>
                    </div>
                </div>
            </div>

            {{-- Log Perjalanan Berkas --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="m-0 fw-bold text-secondary"><i class="fas fa-history me-2"></i>Riwayat Berkas</h5>
                </div>
                <div class="card-body">
                    <div class="timeline-small">
                        <div class="d-flex mb-3">
                            <div class="me-3 text-center" style="width: 50px;">
                                <i class="fas fa-paper-plane text-primary"></i>
                            </div>
                            <div>
                                <div class="fw-bold small">Input Berkas (Operator)</div>
                                <div class="text-muted extra-small">{{ $submission->user->name }} • {{ $submission->created_at->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                        @if($submission->verified_at)
                        <div class="d-flex mb-3">
                            <div class="me-3 text-center" style="width: 50px;">
                                <i class="fas fa-check-circle text-info"></i>
                            </div>
                            <div>
                                <div class="fw-bold small">Terverifikasi (Kasie)</div>
                                <div class="text-muted extra-small">{{ $submission->verified_by_name ?? 'System' }} • {{ \Carbon\Carbon::parse($submission->verified_at)->format('d M Y, H:i') }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Daftar Dokumen & Aksi --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="m-0 fw-bold text-danger"><i class="fas fa-file-pdf me-2"></i>Lampiran Dokumen</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($submission->documents as $doc)
                            <a href="javascript:void(0)" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-3"
                               onclick="viewDocument('{{ asset('storage/' . $doc->file_path) }}', '{{ strtoupper($doc->jenis_dokumen) }}')">
                                <div>
                                    <div class="fw-bold mb-0 small text-dark">{{ strtoupper($doc->jenis_dokumen) }}</div>
                                    <span class="extra-small text-muted"><i class="fas fa-clock me-1"></i> {{ $doc->created_at->diffForHumans() }}</span>
                                </div>
                                <span class="btn btn-sm btn-light border rounded-pill px-3">
                                    <i class="fas fa-eye me-1 text-primary"></i> Preview
                                </span>
                            </a>
                        @empty
                            <div class="p-4 text-center text-muted small italic">Tidak ada dokumen dilampirkan.</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Panel Aksi --}}
            <div class="card border-0 shadow-sm bg-dark text-white">
                <div class="card-body p-4 text-center">
                    <h6 class="mb-3">Panel Kontrol Super Admin</h6>
                    <form action="{{ route('admin.submissions.reset', $submission->id) }}" method="POST" onsubmit="return confirm('Reset status berkas ini ke Pending?')">
                        @csrf
                        <button type="submit" class="btn btn-warning w-100 fw-bold shadow-sm mb-3">
                            <i class="fas fa-undo me-1"></i> Reset Status ke Pending
                        </button>
                    </form>
                    <p class="extra-small text-light-50 m-0">Gunakan tombol reset jika terdapat kesalahan verifikasi atau data perlu diperbaiki oleh OPD.</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL PREVIEW DOKUMEN --}}
<div class="modal fade" id="modalPreview" tabindex="-1" aria-labelledby="modalPreviewLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="modal-title fw-bold text-dark flex-grow-1" id="modalPreviewLabel">Preview Dokumen</h5>
                <div class="d-flex align-items-center gap-2">
                    <a href="" id="btnDownload" target="_blank" class="btn btn-danger btn-sm px-3">
                        <i class="fas fa-download me-1"></i> Download
                    </a>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-0 bg-light position-relative">
                {{-- Enhanced Loading Spinner with Progress --}}
                <div id="loadingSpinner" class="position-absolute top-50 start-50 translate-middle d-flex flex-column align-items-center justify-content-center w-100 h-100 bg-white" style="z-index: 10;">
                    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="text-center">
                        <p class="text-muted fw-semibold mb-2">Memuat dokumen...</p>
                        <small class="text-muted" id="loadingText">Mohon tunggu sebentar</small>
                    </div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="cancelLoading()">
                            <i class="fas fa-times me-1"></i> Batal
                        </button>
                    </div>
                </div>

                {{-- Error State --}}
                <div id="errorState" class="position-absolute top-50 start-50 translate-middle d-flex flex-column align-items-center justify-content-center w-100 h-100 bg-white d-none" style="z-index: 10;">
                    <div class="text-center">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                        <h6 class="text-muted mb-2">Gagal Memuat Dokumen</h6>
                        <p class="text-muted small mb-3" id="errorMessage">Dokumen tidak dapat dimuat. Silakan coba lagi atau download file.</p>
                        <div>
                            <button type="button" class="btn btn-primary btn-sm me-2" onclick="retryLoading()">
                                <i class="fas fa-redo me-1"></i> Coba Lagi
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i> Tutup
                            </button>
                        </div>
                    </div>
                </div>

                <iframe id="iframePreview" src="" width="100%" height="700px" style="border: none; display: none;" class="bg-white" loading="lazy"></iframe>
            </div>
            <div class="modal-footer bg-white border-top">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .extra-small { font-size: 0.75rem; }
    .uppercase { text-transform: uppercase; letter-spacing: 0.5px; }
    .timeline-small { border-left: 2px solid #e9ecef; margin-left: 24px; padding-left: 10px; }
    .modal-xl { max-width: 90%; }
</style>

<script>
    let loadingTimeout;
    let currentUrl = '';
    let currentTitle = '';

    function viewDocument(url, title) {
        const modal = new bootstrap.Modal(document.getElementById('modalPreview'));
        const iframe = document.getElementById('iframePreview');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const errorState = document.getElementById('errorState');
        const downloadBtn = document.getElementById('btnDownload');
        const loadingText = document.getElementById('loadingText');

        // Store current values
        currentUrl = url;
        currentTitle = title;

        // Reset states
        iframe.style.display = 'none';
        iframe.src = '';
        loadingSpinner.style.display = 'flex';
        errorState.classList.add('d-none');
        document.getElementById('modalPreviewLabel').innerText = title;
        downloadBtn.href = url;
        loadingText.innerText = 'Memuat dokumen PDF...';

        // Clear any existing timeout
        if (loadingTimeout) {
            clearTimeout(loadingTimeout);
        }

        const cacheBustedUrl = url + (url.includes('?') ? '&' : '?') + '_t=' + Date.now();
        iframe.src = cacheBustedUrl;
        modal.show();

        loadingTimeout = setTimeout(() => {
            showError('Waktu loading terlalu lama. File PDF mungkin terlalu besar atau koneksi lambat. Coba download file langsung.');
        }, 45000);

        let progressStep = 0;
        const progressInterval = setInterval(() => {
            progressStep = Math.min(progressStep + 5, 95);
            loadingText.innerText = `Memuat dokumen PDF... ${progressStep}%`;
            if (progressStep === 95) {
                clearInterval(progressInterval);
            }
        }, 1500);

        iframe.onload = function() {
            clearTimeout(loadingTimeout);
            clearInterval(progressInterval);
            loadingSpinner.style.display = 'none';
            errorState.classList.add('d-none');
            iframe.style.display = 'block';
            loadingText.innerText = 'Dokumen siap ditampilkan';
        };

        iframe.onerror = function() {
            clearTimeout(loadingTimeout);
            clearInterval(progressInterval);
            showError('Gagal memuat dokumen PDF. File mungkin rusak, terlalu besar, atau tidak tersedia. Silakan coba download.');
        };
    }

    function cancelLoading() {
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalPreview'));
        if (modal) {
            modal.hide();
        }
        if (loadingTimeout) {
            clearTimeout(loadingTimeout);
        }
        resetModalState();
    }

    function retryLoading() {
        if (currentUrl && currentTitle) {
            viewDocument(currentUrl, currentTitle);
        }
    }

    function showError(message) {
        const loadingSpinner = document.getElementById('loadingSpinner');
        const errorState = document.getElementById('errorState');
        const errorMessage = document.getElementById('errorMessage');

        loadingSpinner.style.display = 'none';
        errorState.classList.remove('d-none');
        errorMessage.innerText = message;
    }

    function resetModalState() {
        const iframe = document.getElementById('iframePreview');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const errorState = document.getElementById('errorState');
        const loadingText = document.getElementById('loadingText');

        iframe.style.display = 'none';
        iframe.src = '';
        loadingSpinner.style.display = 'none';
        errorState.classList.add('d-none');
        loadingText.innerText = 'Mohon tunggu sebentar';

        if (loadingTimeout) {
            clearTimeout(loadingTimeout);
        }
    }

    // Reset modal state when modal is hidden
    document.getElementById('modalPreview').addEventListener('hidden.bs.modal', function () {
        resetModalState();
    });
</script>
@endsection