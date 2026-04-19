@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('verifikasi.index') }}">Verifikasi</a></li>
                    <li class="breadcrumb-item active">Detail Berkas</li>
                </ol>
            </nav>
            <!-- <h3 class="fw-bold text-dark mb-0">Verifikasi Dokumen <span class="text-primary">#{{ $submission->nomor_spp_spm }}</span></h3> -->
            <p class="text-muted"><i class="fas fa-university me-1"></i> {{ $submission->department->nama_dinas }} • <i class="fas fa-calendar-alt ms-2 me-1"></i> {{ $submission->created_at->format('d M Y, H:i') }}</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('verifikasi.index') }}" class="btn btn-white btn-sm shadow-sm border"> 
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @php
                $totalDocs = $submission->documents->count();
                $verifiedDocs = $submission->documents->where('status_dokumen', 'verified')->count();
                $percent = ($totalDocs > 0) ? ($verifiedDocs / $totalDocs) * 100 : 0;
                $isAllVerified = ($totalDocs == $verifiedDocs);
            @endphp
            
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-dark"><i class="fas fa-tasks me-2 text-primary"></i>Item Pemeriksaan Dokumen</h6>
                    <span class="badge {{ $isAllVerified ? 'bg-success' : 'bg-primary' }} px-3 py-2">
                        Progres: {{ $verifiedDocs }} / {{ $totalDocs }} Berkas
                    </span>
                </div>
                
                <div class="progress rounded-0" style="height: 4px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percent }}%"></div>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle m-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4" width="30%">Jenis Dokumen</th>
                                    <th width="15%">Pratinjau</th>
                                    <th width="20%">Status Verifikasi</th>
                                    <th class="pe-4 text-center" width="35%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($submission->documents as $doc)
                                <tr class="{{ $doc->status_dokumen == 'rejected' ? 'table-light' : '' }}">
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ strtoupper(str_replace('_', ' ', $doc->jenis_dokumen)) }}</div>
                                        <!-- <small class="text-muted">ID Dokumen: #DOC-{{ $doc->id }}</small> -->
                                    </td>
                                    <td>
                                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill">
                                            <i class="fas fa-external-link-alt me-1"></i> Buka PDF
                                        </a>
                                    </td>
                                    <td>
                                        @if($doc->status_dokumen == 'verified')
                                            <div class="text-success small fw-bold"><i class="fas fa-check-circle me-1"></i> DISUTUJUI</div>
                                        @elseif($doc->status_dokumen == 'rejected')
                                            <div class="text-danger small fw-bold"><i class="fas fa-times-circle me-1"></i> DITOLAK</div>
                                            <div class="bg-white border rounded p-1 mt-1 x-small shadow-sm" style="font-size: 11px;">
                                                <strong>Alasan:</strong> {{ $doc->catatan }}
                                            </div>
                                        @else
                                            <div class="text-warning small fw-bold"><i class="fas fa-clock me-1"></i> Pending</div>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-center">
                                        <div class="btn-group shadow-sm rounded">
                                            <form action="{{ route('verifikasi.document.action', $doc->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="action" value="setuju">
                                                <button type="submit" class="btn btn-sm btn-success px-3 {{ $doc->status_dokumen == 'verified' ? 'active disabled' : '' }}" title="Setujui Berkas">
                                                    <i class="fas fa-check me-1"></i> Setuju
                                                </button>
                                            </form>

                                            <button type="button" class="btn btn-sm btn-danger px-3 {{ $doc->status_dokumen == 'rejected' ? 'active' : '' }}" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalTolak" 
                                                    data-docid="{{ $doc->id }}" 
                                                    data-docname="{{ strtoupper(str_replace('_', ' ', $doc->jenis_dokumen)) }}">
                                                <i class="fas fa-times me-1"></i> Tolak
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="card-footer bg-light border-0 py-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            @if($isAllVerified)
                                <div class="d-flex align-items-center text-success">
                                    <i class="fas fa-check-double fa-2x me-3"></i>
                                    <div>
                                        <h6 class="mb-0 fw-bold">Validasi Selesai</h6>
                                        <small>Seluruh dokumen telah diperiksa dan dinyatakan benar.</small>
                                    </div>
                                </div>
                            @else
                                <div class="d-flex align-items-center text-muted">
                                    <i class="fas fa-exclamation-circle fa-2x me-3 text-warning"></i>
                                    <div>
                                        <h6 class="mb-0 fw-bold">Menunggu Lengkap</h6>
                                        <small>Silakan verifikasi setiap baris dokumen sebelum meneruskan ke Kabid Perbendaharaan.</small>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-4 text-end">
                            <form action="{{ route('verifikasi.setuju', $submission->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold shadow {{ !$isAllVerified ? 'disabled opacity-50' : '' }}">
                                    <i class="fas fa-paper-plane me-2"></i> KIRIM KE KABID PERBENDAHARAAN
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTolak" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-exclamation-triangle me-2"></i>Tolak Dokumen</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTolak" action="" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-file-circle-exclamation fa-3x text-danger opacity-25"></i>
                    </div>
                    <p class="text-center mb-4">Berikan alasan mengapa berkas <br><strong class="text-dark" id="namaBerkasModal"></strong> Anda tolak:</p>
                    
                    <input type="hidden" name="action" value="tolak">
                    <div class="form-group">
                        <textarea name="catatan" class="form-control border-danger-subtle focus-ring focus-ring-danger" rows="4" placeholder="Contoh: Lampiran tidak jelas, Tanda tangan basah tidak ada, atau Nominal tidak sesuai SPP..." required autofocus></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-link text-muted fw-bold text-decoration-none" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger px-4 fw-bold shadow-sm">Kirim Penolakan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .btn-white { background: #fff; color: #333; }
    .x-small { font-size: 0.75rem; }
    .table-hover tbody tr:hover { background-color: rgba(26, 35, 126, 0.02); }
    .progress-bar { transition: width 0.6s ease; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- 1. LOGIKA KEMBALIKAN SCROLL (DENGAN KOMPENSASI ALERT) ---
    const savedScrollPos = localStorage.getItem('verifikasi_scroll_pos');
    
    if (savedScrollPos) {
        // Berikan sedikit jeda agar browser selesai merender Alert
        setTimeout(function() {
            const alertContainer = document.getElementById('alert-container');
            let offset = 0;

            // Jika ada alert yang muncul, hitung tingginya untuk menambah akurasi scroll
            if (alertContainer && alertContainer.offsetHeight > 0) {
                offset = alertContainer.offsetHeight;
            }

            window.scrollTo({
                top: parseInt(savedScrollPos) + offset,
                behavior: 'instant'
            });
            
            localStorage.removeItem('verifikasi_scroll_pos');
        }, 50); // Jeda minimal untuk stabilitas
    }

    // --- 2. LOGIKA MODAL PENOLAKAN ---
    const modalTolak = document.getElementById('modalTolak');
    if (modalTolak) {
        modalTolak.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const docId = button.getAttribute('data-docid');
            const docName = button.getAttribute('data-docname');
            
            modalTolak.querySelector('#namaBerkasModal').textContent = docName;
            const formTolak = modalTolak.querySelector('#formTolak');
            formTolak.action = `/verifikasi/document/${docId}/action`;
            
            const textarea = modalTolak.querySelector('textarea');
            if(textarea) textarea.value = '';
        });
    }

    // --- 3. EFEK LOADING & SIMPAN POSISI ---
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            // Catat posisi scroll murni sebelum halaman reload
            localStorage.setItem('verifikasi_scroll_pos', window.scrollY);

            const btn = this.querySelector('button[type="submit"]');
            if (btn) {
                const originalWidth = btn.offsetWidth;
                btn.style.width = originalWidth + 'px';
                btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';
                btn.classList.add('disabled');
                btn.style.pointerEvents = 'none';
            }
        });
    });
});
</script>
@endsection