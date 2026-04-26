@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-1 align-items-center">
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
            <a href="{{ route('verifikasi.index') }}" class="btn btn-white btn-sm bg-dark shadow-sm border hover:bg-dark hover:text-white"> 
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row g-4">
    {{-- SISI KIRI: DAFTAR DOKUMEN --}}
    <div class="col-lg-8">
        @php
            $totalDocs = $submission->documents->count();
            $verifiedDocs = $submission->documents->where('status_dokumen', 'verified')->count();
            $percent = ($totalDocs > 0) ? ($verifiedDocs / $totalDocs) * 100 : 0;
            $isAllVerified = ($totalDocs > 0 && $totalDocs == $verifiedDocs);
        @endphp
        
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            {{-- Header Card: Minimalis --}}
            <div class="card-header bg-white border-0 py-3 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold text-dark mb-1">Lampiran Dokumen</h6>
                        <p class="text-muted mb-0">Silakan tinjau keabsahan berkas di bawah ini.</p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-outline-primary text-primary border px-3 py-2 rounded-pill fw-bold">
                            {{ $verifiedDocs }} / {{ $totalDocs }} Berhasil Diverifikasi
                        </span>
                    </div>
                </div>
                
                {{-- Progress Bar Subtle --}}
                <div class="progress mt-3" style="height: 6px; background-color: #f1f5f9;">
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: {{ $percent }}%; transition: width 1s ease;"></div>
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle m-0">
                        <thead class="bg-light">
                            <tr class="text-muted"text-transform: uppercase; letter-spacing: 1.2px;">
                                <th class="ps-4 py-3 border-0">Detail Dokumen</th>
                                <th class="text-center border-0">Pratinjau</th>
                                <th class="border-0">Status</th>
                                <th class="pe-4 text-end border-0">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($submission->documents as $doc)
                            <tr class="document-row" id="doc-row-{{ $doc->id }}">
    <td class="ps-4 py-3">
        <div class="fw-bold text-slate-800">{{ strtoupper(str_replace('_', ' ', $doc->jenis_dokumen)) }}</div>
        <div class="text-muted small">Format: PDF Digital</div>
    </td>
    <td class="text-center">
        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2 py-1" 
                onclick="openPdfModal('{{ asset('storage/' . $doc->file_path) }}', '{{ strtoupper($doc->jenis_dokumen) }}')">
            <i class="fas fa-eye me-1"></i> Tinjau
        </button>
    </td>
    {{-- TAMBAHKAN CLASS status-container --}}
    <td class="status-container">
        @if($doc->status_dokumen == 'verified')
            <span class="badge-status success"><i class="fas fa-check-circle me-1"></i> Disetujui</span>
        @elseif($doc->status_dokumen == 'revisi')
            <span class="badge-status danger"><i class="fas fa-exclamation-circle me-1"></i> Revisi</span>
        @else
            <span class="badge-status warning"><i class="far fa-clock me-1"></i> Menunggu</span>
        @endif
    </td>
    <td class="pe-4 text-end">
        <div class="btn-group btn-group-sm action-group">
            <button type="button" class="btn btn-action btn-setuju {{ $doc->status_dokumen == 'verified' ? 'active disabled' : '' }}" 
                    data-id="{{ $doc->id }}" title="Setujui">
                <i class="fas fa-check"></i>
            </button>
            {{-- Tambahkan data-docname untuk modal --}}
            <button type="button" class="btn btn-action btn-tolak {{ $doc->status_dokumen == 'revisi' ? 'active' : '' }}" 
                    data-bs-toggle="modal" data-bs-target="#modalTolak" 
                    data-docid="{{ $doc->id }}" 
                    data-docname="{{ strtoupper(str_replace('_', ' ', $doc->jenis_dokumen)) }}">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </td>
</tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            {{-- Footer: Tombol Utama --}}
            <div class="card-footer bg-white border-top-0 p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <p class="text-muted mb-0 small italic">
                        <i class="fas fa-info-circle me-1"></i> Pastikan semua berkas telah berstatus hijau sebelum mengirim.
                    </p>
                    <form action="{{ route('submissions.sendToKabid', $submission->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-navy px-3 py-2 fw-bold shadow-sm {{ !$isAllVerified ? 'disabled opacity-50' : '' }}">
                        VERIFIKASI
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- SISI KANAN: INFO CARD --}}
    <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white py-3 border-bottom-0">
                    <div class="d-flex align-items-center">
                        <div class="icon-shape bg-primary-subtle text-primary rounded-3 me-3 p-2">
                            <i class="fas fa-file-invoice fa-lg"></i>
                        </div>
                        <h6 class="m-0 fw-bold text-dark">Informasi Kegiatan</h6>
                    </div>
                </div>

            <div class="card-body pt-0">
            <div class="mb-4">
                <label class="text-uppercase text-muted fw-bold mb-2"etter-spacing: 1px;">Instansi Pengaju</label>
                <h5 class="fw-bold text-primary mb-1">{{ $submission->department->nama_dinas }}</h5>
                <p class="text-muted small"><i class="far fa-calendar-alt me-1"></i> Diajukan pada {{ $submission->created_at->format('d M Y, H:i') }}</p>
            </div>

            <hr class="opacity-50">

            <div class="vstack gap-3">
                {{-- Item: Nomor SPM --}}
                <div class="d-flex justify-content-between align-items-start">
                    <div class="text-muted small"><i class="fas fa-hashtag me-2"></i>Nomor SPP/SPM</div>
                    <div class="text-end fw-semibold text-dark">{{ $submission->nomor_spp_spm }}</div>
                </div>

                {{-- Item: Tanggal --}}
                <div class="d-flex justify-content-between align-items-start">
                    <div class="text-muted small"><i class="fas fa-calendar-day me-2"></i>Tanggal Berkas</div>
                    <div class="text-end fw-semibold text-dark">{{ $submission->tanggal->format('d M Y') }}</div>
                </div>

                {{-- Item: Kegiatan --}}
                <div>
                    <div class="text-muted small mb-1"><i class="fas fa-project-diagram me-2"></i>Kegiatan & Sub</div>
                    <div class="ps-4">
                        <div class="fw-semibold text-dark small">{{ $submission->judul_kegiatan }}</div>
                        <div class="text-muted"{{ $submission->judul_sub_kegiatan }}</div>
                    </div>
                </div>

                {{-- Item: Perihal --}}
                <div>
                    <div class="text-muted small mb-1"><i class="fas fa-info-circle me-2"></i>Perihal</div>
                    <div class="ps-4 text-dark small italic">
                        "{{ $submission->perihal }}"
                    </div>
                </div>
            </div>

                <div class="mt-4 p-3 rounded-3 bg-light border-start border-primary border-4">
                    <label class="text-muted d-block small mb-1">Total Pencairan</label>
                    <h4 class="fw-bold text-dark mb-0">
                        <span class="text-primary small fw-normal">Rp</span> {{ number_format($submission->total_pencairan, 0, ',', '.') }}
                    </h4>
                </div>
            </div>
        </div>
</div>
</div>

<div class="modal fade" id="modalTolak" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-undo me-2"></i>Minta Revisi Dokumen</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formTolak" action="" method="POST">
                @csrf
                <div class="modal-body py-4">
                    <p class="text-center mb-4">Berikan alasan mengapa berkas <br><strong class="text-dark" id="namaBerkasModal"></strong> memerlukan revisi:</p>
                    
                    {{-- NILAI ACTION DIUBAH KE REVISI --}}
                    <input type="hidden" name="action" value="revisi"> 
                    
                    <div class="form-group">
                        <textarea name="catatan" class="form-control border-danger-subtle focus-ring focus-ring-danger" rows="4" placeholder="Contoh: Tanda tangan tidak jelas, nominal tidak sesuai..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-link text-muted fw-bold text-decoration-none" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger px-4 fw-bold shadow-sm">Kirim ke Operator</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPreviewPDF" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content shadow-lg border-0">
            <div class="modal-header bg-dark text-white py-2">
                <h6 class="modal-title fw-bold">
                    <i class="fas fa-file-pdf me-2"></i> 
                    <span id="pdfTitle">Preview Dokumen</span> </h6>
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
$(document).ready(function() {
    // --- A. LOGIKA TOMBOL SETUJU (AJAX) ---
    $('.btn-setuju').on('click', function() {
        let btn = $(this);
        let docId = btn.data('id');
        let row = btn.closest('tr');
        let btnTolak = row.find('.btn-tolak');

        if(btn.hasClass('disabled')) return;

        btn.html('<i class="fas fa-spinner fa-spin"></i>').addClass('disabled');

        $.ajax({
            url: `/verifikasi/document/${docId}/action`,
            type: 'POST',
            data: { _token: '{{ csrf_token() }}', action: 'setuju' },
            success: function(response) {
                // Update UI Status
                row.find('.status-container').html('<span class="badge-status success"><i class="fas fa-check-circle me-1"></i> Disetujui</span>');
                row.addClass('verified-bg').removeClass('revisi-bg');
                
                // Update Buttons
                btn.html('<i class="fas fa-check"></i>').addClass('active disabled');
                btnTolak.removeClass('active disabled'); // Reset tombol tolak
                
                updateProgressBar();
            },
            error: function() {
                alert('Gagal memproses dokumen.');
                btn.html('<i class="fas fa-check"></i>').removeClass('disabled');
            }
        });
    });

    // --- B. LOGIKA TOMBOL TOLAK/REVISI (AJAX) ---
    $('#formTolak').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let actionUrl = form.attr('action');
        let btnSubmit = form.find('button[type="submit"]');
        let catatan = form.find('textarea').val();
        let docId = actionUrl.split('/').reverse()[1]; // Mengambil ID dari URL

        btnSubmit.html('<i class="fas fa-spinner fa-spin"></i>').addClass('disabled');

        $.ajax({
            url: actionUrl,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                action: 'revisi',
                catatan: catatan
            },
            success: function(response) {
                let row = $(`#doc-row-${docId}`);
                
                // Update UI Status
                row.find('.status-container').html('<span class="badge-status danger"><i class="fas fa-exclamation-circle me-1"></i> Revisi</span>');
                row.removeClass('verified-bg');

                // Update Buttons
                let btnSetuju = row.find('.btn-setuju');
                let btnTolak = row.find('.btn-tolak');
                
                btnTolak.addClass('active');
                btnSetuju.html('<i class="fas fa-check"></i>').removeClass('active disabled');

                bootstrap.Modal.getInstance($('#modalTolak')).hide();
                btnSubmit.html('Kirim ke Operator').removeClass('disabled');
                
                updateProgressBar();
            },
            error: function() {
                alert('Gagal mengirim permintaan revisi.');
                btnSubmit.html('Kirim ke Operator').removeClass('disabled');
            }
        });
    });

    // --- C. HITUNG ULANG PROGRESS ---
    function updateProgressBar() {
        let total = {{ $totalDocs }};
        // Hitung berdasarkan jumlah badge success yang ada di layar
        let verified = $('.badge-status.success').length;
        let percent = (verified / total) * 100;

        $('.progress-bar').css('width', percent + '%');
        $('.card-header .badge').text(`${verified} / ${total} Berhasil Diverifikasi`);

        if (verified === total) {
            $('.btn-navy').removeClass('disabled opacity-50').prop('disabled', false);
        } else {
            $('.btn-navy').addClass('disabled opacity-50').prop('disabled', true);
        }
    }
});
</script>

<script>
    function openPdfModal(pdfUrl, docName) {
        // Update Judul Modal
        document.getElementById('pdfTitle').innerText = 'Preview: ' + docName;
        
        // Update Source PDF
        const frame = document.getElementById('pdfFrame');
        frame.src = pdfUrl;
        
        // Tampilkan Modal
        const previewModal = new bootstrap.Modal(document.getElementById('modalPreviewPDF'));
        previewModal.show();
    }

    // Reset saat modal ditutup
    document.getElementById('modalPreviewPDF').addEventListener('hidden.bs.modal', function () {
        document.getElementById('pdfFrame').src = '';
        document.getElementById('pdfTitle').innerText = 'Preview Dokumen';
    });
</script>

<script>
    function openPdfModal(pdfUrl) {
        // Ambil elemen iframe
        const frame = document.getElementById('pdfFrame');
        
        // Set sumber PDF ke iframe
        frame.src = pdfUrl;
        
        // Inisialisasi dan tampilkan modal Bootstrap
        const previewModal = new bootstrap.Modal(document.getElementById('modalPreviewPDF'));
        previewModal.show();
    }

    // Membersihkan src iframe saat modal ditutup agar tidak membebani memori browser
    document.getElementById('modalPreviewPDF').addEventListener('hidden.bs.modal', function () {
        document.getElementById('pdfFrame').src = '';
    });
</script>

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

    // --- 2. LOGIKA MODAL PENOLAKAN (LENGKAP) ---
const modalTolak = document.getElementById('modalTolak');

if (modalTolak) {
    const formTolak = modalTolak.querySelector('#formTolak');
    const textarea = modalTolak.querySelector('textarea');
    const btnSubmit = modalTolak.querySelector('button[type="submit"]');

    // A. Saat Modal Mulai Dibuka (Persiapan Data)
    modalTolak.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const docId = button.getAttribute('data-docid');
        const docName = button.getAttribute('data-docname');
        
        // Isi nama berkas & ubah action form
        modalTolak.querySelector('#namaBerkasModal').textContent = docName;
        formTolak.action = `/verifikasi/document/${docId}/action`;
        
        // Reset textarea & disable tombol submit (karena alasan masih kosong)
        if (textarea) {
            textarea.value = '';
            if (btnSubmit) btnSubmit.disabled = true;
        }
    });

    // B. Saat Modal Selesai Tampil (Auto-Focus)
    modalTolak.addEventListener('shown.bs.modal', function () {
        if (textarea) {
            textarea.focus();
        }
    });

    // C. Validasi Real-time (Tombol Aktif jika Alasan Terisi)
    if (textarea && btnSubmit) {
        textarea.addEventListener('input', function() {
            // Aktifkan tombol jika panjang teks lebih dari 5 karakter
            btnSubmit.disabled = (this.value.trim().length < 5);
        });
    }
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

<style>
    /* Menghilangkan scrollbar pada modal body agar iframe maksimal */
    #modalPreviewPDF .modal-body {
        overflow: hidden;
    }
    
    /* Memberikan efek transparan pada background modal */
    #modalPreviewPDF {
        background-color: rgba(0, 0, 0, 0.4);
    }

    /* Base Colors & Backgrounds */
    :root {
        --navy: #0f172a;
        --slate-100: #f1f5f9;
        --slate-700: #334155;
        --slate-800: #1e293b;
    }

    .bg-navy { background-color: var(--navy); }
    .btn-navy { background-color: var(--navy); color: white; border-radius: 10px; font-size: 0.8rem; }
    .btn-navy:hover { background-color: #1e293b; color: white; }

    /* Typography */
    .document-row:hover { background-color: #f8fafc; }
    .verified-bg { background-color: #f0fdf4 !important; }
    
    /* Status Badges Subtle */
    .badge-status {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.6rem;
        font-weight: 500;
        display: inline-block;
    }
    .badge-status.success { background-color: #dcfce7; color: #15803d; }
    .badge-status.warning { background-color: #fef9c3; color: #a16207; }
    .badge-status.danger { background-color: #fee2e2; color: #b91c1c; }

    /* Button Action Group */
    .action-group {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: white;
    }
    .btn-action {
        background: white;
        color: #94a3b8;
        border: none;
        padding: 5px 12px;
        transition: all 0.2s ease;
    }
    .btn-setuju:hover, .btn-setuju.active { background-color: #22c55e; color: white; }
    .btn-tolak:hover, .btn-tolak.active { background-color: #ef4444; color: white; }

    /* Right Sidebar Detail */
    .info-item {
        border-bottom: 1px dashed #e2e8f0;
        padding-bottom: 10px;
    }
    .info-item label {
        display: block;
        text-transform: uppercase;
        font-size: 0.6rem;
        font-weight: 800;
        color: #94a3b8;
        letter-spacing: 0.5px;
        margin-bottom: 3px;
    }
    .info-item p {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--slate-800);
        margin-bottom: 0;
        line-height: 1.4;
    }
</style>

@endsection