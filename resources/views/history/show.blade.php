@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm bg-light bg-opacity-25 rounded-4 p-3 mb-2 border-start border-warning border-4">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <div class="icon-box bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                <i class="fas fa-file-archive fa-2x text-primary"></i>
            </div>
            <div>
                <h4 class="fw-bold text-dark mb-1">Detail Riwayat Arsip</h4>
                <p class="text-muted small mb-0">Melihat rincian berkas yang telah diproses oleh sistem.</p>
            </div>
        </div>
        <a href="{{ route('history.index') }}" class="btn btn-outline-dark btn-sm rounded-pill px-3 shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1 fw-bold text-secondary"><i class="fas fa-file-alt me-2 text-primary"></i>Informasi Arsip</h5>
                        <p class="text-muted small mb-0">Rincian lengkap berkas dan metadata untuk referensi cepat.</p>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill py-2 px-3">Selesai</span>
                </div>
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-sm-6">
                            <p class="text-uppercase text-muted small mb-1">Nomor SPP/SPM</p>
                            <p class="fw-semibold mb-0">{{ $submission->nomor_spp_spm ?? '-' }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="text-uppercase text-muted small mb-1">Tanggal Upload</p>
                            <p class="fw-semibold mb-0">{{ $submission->created_at->format('d M Y, H:i') }} WITA</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="text-uppercase text-muted small mb-1">Dinas / Instansi</p>
                            <p class="mb-0">{{ $submission->department->nama_dinas }}</p>
                        </div>
                        <div class="col-sm-6">
                            <p class="text-uppercase text-muted small mb-1">Nilai Nominal</p>
                            <p class="fw-bold text-primary mb-0">Rp {{ number_format($submission->total_pencairan, 0, ',', '.') }}</p>
                        </div>
                        <div class="col-12">
                            <p class="text-uppercase text-muted small mb-1">Kegiatan</p>
                            <p class="mb-0">{{ $submission->judul_kegiatan }}</p>
                        </div>
                        <div class="col-12">
                            <p class="text-uppercase text-muted small mb-1">Sub-Kegiatan</p>
                            <p class="mb-0">{{ $submission->judul_sub_kegiatan }}</p>
                        </div>
                        <div class="col-12">
                            <p class="text-uppercase text-muted small mb-1">Perihal</p>
                            <p class="mb-0">{{ $submission->perihal ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-secondary"><i class="fas fa-paperclip me-2 text-primary"></i>Lampiran Dokumen</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                        <div>
                            <p class="text-muted small mb-1">Total Dokumen</p>
                            <p class="fw-semibold mb-0">{{ $submission->documents->count() }}</p>
                        </div>
                        <div class="text-muted small">Pastikan file dapat diunduh atau dilihat langsung.</div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama File</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($submission->documents as $doc)
                                <tr>
                                    <td>
                                        <i class="far fa-file-pdf text-danger me-2"></i> {{ $doc->jenis_dokumen }}
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary me-2 mb-2 btn-view-doc" data-file-path="{{ asset('storage/' . $doc->file_path) }}" data-file-name="{{ $doc->jenis_dokumen }}">
                                            <i class="fas fa-eye"></i> Lihat
                                        </button>
                                        <a href="{{ asset('storage/' . $doc->file_path) }}" download class="btn btn-sm btn-outline-success mb-2">
                                            <i class="fas fa-download"></i> Unduh
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-secondary"><i class="fas fa-chart-line me-2 text-primary"></i>Ringkasan Proses</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-uppercase text-muted small">Status</span>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill py-2 px-3">Selesai</span>
                        </div>
                        <p class="fw-semibold mb-1">Arsip Digital</p>
                        <div class="small text-muted">Rekam selesai dan tersimpan permanen.</div>
                    </div>
                    <div class="mb-3">
                        <p class="text-uppercase text-muted small mb-1">Pengirim</p>
                        <p class="mb-0">{{ $submission->user->name }}</p>
                    </div>
                    <div class="mb-3">
                        <p class="text-uppercase text-muted small mb-1">Dinas</p>
                        <p class="mb-0">{{ $submission->department->nama_dinas }}</p>
                    </div>
                    <div class="mb-3">
                        <p class="text-uppercase text-muted small mb-1">Tanggal Terakhir</p>
                        <p class="mb-0">{{ optional($submission->validated_at ?? $submission->verified_at ?? $submission->created_at)->format('d M Y, H:i') }} WITA</p>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-secondary"><i class="fas fa-signature me-2 text-primary"></i>Jejak Digital Verifikasi</h6>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item py-3">
                            <div class="d-flex w-100 justify-content-between align-items-center mb-2">
                                <div>
                                    <h6 class="mb-1 fw-semibold">Operator Upload</h6>
                                    <p class="small text-muted mb-0">Pengirim asli berkas</p>
                                </div>
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill">Selesai</span>
                            </div>
                            <p class="mb-1">{{ $submission->user->name }}</p>
                            <p class="text-muted small mb-0"><i class="far fa-clock me-1"></i> {{ $submission->created_at->format('d M Y, H:i') }} WITA</p>
                        </div>
                        <div class="list-group-item py-3 {{ $submission->verified_at ? '' : 'bg-light' }}">
                            <div class="d-flex w-100 justify-content-between align-items-center mb-2">
                                <div>
                                    <h6 class="mb-1 fw-semibold">Verifikasi Kasie</h6>
                                    <p class="small text-muted mb-0">Status pemeriksaan utama</p>
                                </div>
                                <span class="badge {{ $submission->verified_at ? 'bg-success bg-opacity-10 text-success' : 'bg-warning bg-opacity-10 text-warning' }} rounded-pill">
                                    {{ $submission->verified_at ? 'Selesai' : 'Menunggu' }}
                                </span>
                            </div>
                            <p class="mb-1">{{ $submission->verified_by_name ?? 'Belum ada verifikator' }}</p>
                            @if($submission->verified_at)
                                <p class="text-muted small mb-0"><i class="far fa-clock me-1"></i> {{ \Carbon\Carbon::parse($submission->verified_at)->format('d M Y, H:i') }} WITA</p>
                            @else
                                <p class="text-danger small mb-0">Belum diverifikasi</p>
                            @endif
                        </div>
                        <div class="list-group-item py-3 {{ $submission->validated_at ? '' : 'bg-light' }}">
                            <div class="d-flex w-100 justify-content-between align-items-center mb-2">
                                <div>
                                    <h6 class="mb-1 fw-semibold">Validasi Kabid</h6>
                                    <p class="small text-muted mb-0">Konfirmasi akhir sebelum arsip</p>
                                </div>
                                <span class="badge {{ $submission->validated_at ? 'bg-success bg-opacity-10 text-success' : 'bg-warning bg-opacity-10 text-warning' }} rounded-pill">
                                    {{ $submission->validated_at ? 'Selesai' : 'Menunggu' }}
                                </span>
                            </div>
                            <p class="mb-1">{{ $submission->validated_by_name ?? 'Belum ada validator' }}</p>
                            @if($submission->validated_at)
                                <p class="text-muted small mb-0"><i class="far fa-clock me-1"></i> {{ \Carbon\Carbon::parse($submission->validated_at)->format('d M Y, H:i') }} WITA</p>
                            @else
                                <p class="text-danger small mb-0">Belum divalidasi</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info border-0 shadow-sm rounded-4">
                <div class="d-flex align-items-start">
                    <div class="me-2 mt-1"><i class="fas fa-info-circle"></i></div>
                    <div>
                        <p class="mb-1 fw-semibold">Catatan Penting</p>
                        <p class="small mb-0 text-muted">Halaman ini menunjukkan rekam jejak proses berkas secara ringkas dan mudah dibaca.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal untuk melihat dokumen -->
<div class="modal fade" id="documentViewerModal" tabindex="-1" aria-labelledby="documentViewerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="documentViewerModalLabel">Tampilan Dokumen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="documentViewerContent" style="height: 600px; overflow: hidden;">
                <iframe id="documentFrame" style="width: 100%; height: 100%; border: none;" src=""></iframe>
            </div>
            <div class="modal-footer">
                <a id="downloadLink" href="#" download class="btn btn-sm btn-outline-success">
                    <i class="fas fa-download me-1"></i> Unduh
                </a>
                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const viewDocButtons = document.querySelectorAll('.btn-view-doc');
        const modal = new bootstrap.Modal(document.getElementById('documentViewerModal'));
        
        viewDocButtons.forEach(button => {
            button.addEventListener('click', function() {
                const filePath = this.getAttribute('data-file-path');
                const fileName = this.getAttribute('data-file-name');
                
                // Set modal title
                document.getElementById('documentViewerModalLabel').textContent = fileName;
                
                // Set iframe source
                document.getElementById('documentFrame').src = filePath;
                
                // Set download link
                document.getElementById('downloadLink').href = filePath;
                document.getElementById('downloadLink').download = fileName;
                
                // Show modal
                modal.show();
            });
        });
    });
</script>
@endsection