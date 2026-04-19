@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">
    {{-- Header & Navigasi --}}
    <div class="row mb-4">
        <div class="col-md-8">
            <h4 class="fw-bold text-uppercase mb-1"><i class="fas fa-microscope me-2 text-primary"></i>Audit Detail Berkas</h4>
            <p class="text-muted">ID Pengajuan: <span class="fw-bold">#{{ $submission->id }}</span> | Nomor SPM: <span class="fw-bold text-dark">{{ $submission->nomor_spp_spm }}</span></p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('bpk.monitoring') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row g-3">
        {{-- Sisi Kiri: Informasi Teks --}}
        <div class="col-md-8">
            {{-- Card Status --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-body py-4">
                    <div class="text-center mb-4">
                        @if($submission->status == 'disetujui')
                            <div class="display-4 text-success"><i class="fas fa-check-circle"></i></div>
                            <h5 class="fw-bold mt-2">BERKAS SAH</h5>
                            {!! $submission->status_badge !!}
                        @else
                            <div class="display-4 text-warning"><i class="fas fa-clock"></i></div>
                            <h5 class="fw-bold mt-2">PROSES VERIFIKASI</h5>
                            {!! $submission->status_badge !!}
                        @endif
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="data-label">Nilai Transaksi</label>
                            <h4 class="text-primary fw-bold">Rp {{ number_format($submission->total_pencairan, 0, ',', '.') }}</h4>
                        </div>
                        <div class="col-md-6">
                            <label class="data-label">Instansi Pengirim</label>
                            <p class="fw-bold mb-0">{{ $submission->department->nama_dinas }}</p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="data-label">Tanggal Dokumen</label>
                            <p class="mb-0">{{ \Carbon\Carbon::parse($submission->tanggal)->format('d F Y') }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="data-label">Judul Kegiatan</label>
                            <p class="mb-0">{{ $submission->judul_kegiatan }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card Jejak Digital (Audit Trail) --}}
            <div class="card border-0 shadow-sm rounded-3 bg-dark text-white">
                <div class="card-header border-secondary py-3">
                    <h6 class="m-0 fw-bold"><i class="fas fa-fingerprint me-2 text-warning"></i>Autentikasi Digital</h6>
                </div>
                <div class="card-body p-0">
                    <div class="p-3 border-bottom border-secondary">
                        <small class="text-warning d-block fw-bold mb-1">Perekaman Input (Operator)</small>
                        <p class="small mb-0">{{ $submission->user->name }}</p>
                        <small class="text-muted">{{ $submission->created_at->format('d/m/Y H:i:s') }} WITA</small>
                    </div>
                    
                    @if($submission->verified_at)
                    <div class="p-3 border-bottom border-secondary">
                        <small class="text-info d-block fw-bold mb-1">Verifikasi (Kasie BUD)</small>
                        <p class="small mb-0 fw-bold">{{ $submission->verified_by_name ?? 'Nama Tidak Terdeteksi' }}</p>
                        <p class="small mb-0">Telah diperiksa kesesuaian administrasi.</p>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($submission->verified_at)->format('d/m/Y H:i:s') }} WITA</small>
                    </div>
                    @endif

                    @if($submission->validated_at)
                    <div class="p-3">
                        <small class="text-success d-block fw-bold mb-1">Pengesahan (Kabid)</small>
                        <p class="small mb-0 fw-bold">{{ $submission->validated_by_name ?? 'Nama Tidak Terdeteksi' }}</p>
                        <p class="small mb-0">Dokumen dinyatakan valid dan disetujui.</p>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($submission->validated_at)->format('d/m/Y H:i:s') }} WITA</small>
                    </div>
                    @endif
                </div>
                <div class="card-footer border-secondary text-center">
                    <small class="text-muted" style="font-size: 0.7rem;">Sistem iDJ Kabupaten Bolaang Mongondow</small>
                </div>
            </div>
        </div>

        {{-- Sisi Kanan: Daftar Dokumen --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-file-pdf me-2"></i>Lampiran Berkas Digital</h6>
                    <span class="badge bg-light text-dark border">{{ $submission->documents->count() }} File Terlampir</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($submission->documents as $doc)
                        <div class="list-group-item p-3">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-file-invoice-dollar text-danger fa-lg me-3"></i>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 fw-bold text-uppercase small">{{ str_replace('_', ' ', $doc->jenis_dokumen) }}</h6>
                                    <small class="text-muted">PDF | {{ $doc->created_at->format('d/m/Y H:i') }} WITA</small>
                                </div>
                            </div>
                            <button class="btn btn-primary btn-sm w-100 btn-pill" onclick="openPreviewModal('{{ asset('storage/' . $doc->file_path) }}', '{{ str_replace('_', ' ', $doc->jenis_dokumen) }}')">
                                <i class="fas fa-eye me-1"></i> Preview Dokumen
                            </button>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Preview --}}
<div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="previewModalLabel">Preview Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div id="loadingSpinner" class="d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Memuat preview dokumen...</p>
                </div>
                <iframe id="previewIframe" src="" style="width: 100%; height: 600px; border: none;" class="d-none"></iframe>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <a id="downloadLink" href="" target="_blank" class="btn btn-primary">Download File</a>
            </div>
        </div>
    </div>
</div>

<script>
function openPreviewModal(url, title) {
    document.getElementById('previewModalLabel').textContent = 'Preview: ' + title;
    document.getElementById('downloadLink').href = url;
    document.getElementById('loadingSpinner').classList.remove('d-none');
    document.getElementById('previewIframe').classList.add('d-none');
    document.getElementById('previewIframe').src = url + '#toolbar=0';
    
    var modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();
    
    document.getElementById('previewIframe').onload = function() {
        document.getElementById('loadingSpinner').classList.add('d-none');
        document.getElementById('previewIframe').classList.remove('d-none');
    };
}
</script>

<style>
    @media print {
        .btn, .modal, .card-header { display: none !important; }
        .card { border: 1px solid #ddd !important; box-shadow: none !important; }
        .col-md-8, .col-md-4 { width: 100% !important; }
    }
</style>
@endsection