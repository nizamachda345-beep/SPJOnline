@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            {{-- Navigasi Kembali --}}
            <a href="{{ route('validasi.index') }}" class="btn btn-outline-secondary mb-3">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-file-alt me-2"></i> Detail Pengajuan: {{ $submission->department->nama_dinas }}
                    </h5>
                    <span class="badge bg-primary px-3 py-2">Status: {{ strtoupper($submission->status) }}</span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">Nama Operator</th>
                                    <td>: {{ $submission->user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Dinas / SKPD</th>
                                    <td>: {{ $submission->department->nama_dinas }}</td>
                                </tr>
                                <tr>
                                    <th>Nomor SPP/SPM</th>
                                    <td class="fw-bold text-primary">: {{ $submission->nomor_spp_spm }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="35%">Tanggal Masuk</th>
                                    <td>: {{ $submission->created_at->format('d M Y, H:i') }} WITA</td>
                                </tr>
                                <tr>
                                    <th>Total Dokumen</th>
                                    <td>: {{ $submission->documents->count() }} Berkas</td>
                                </tr>
                                <tr>
                                    <th>Total Pencairan</th>
                                    <td class="fw-bold text-success">: Rp {{ number_format($submission->total_pencairan, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 text-uppercase small text-muted"><i class="fas fa-list me-2"></i> Daftar Berkas Terlampir</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light text-center small text-uppercase">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Jenis Dokumen</th>
                                    <th width="20%">Status Verifikasi Kasie</th>
                                    <th width="20%">Lihat File</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($submission->documents as $index => $doc)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="fw-bold">{{ strtoupper($doc->jenis_dokumen) }}</td>
                                    <td class="text-center">
                                        @if($doc->status_dokumen == 'verified')
                                            <span class="badge bg-success-soft text-success border border-success px-3">
                                                <i class="fas fa-check-circle me-1"></i> TERVERIFIKASI
                                            </span>
                                        @else
                                            <span class="badge bg-warning text-dark px-3">{{ strtoupper($doc->status_dokumen) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-external-link-alt me-1"></i> Buka Berkas
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Footer dengan Aksi Final --}}
                <div class="card-footer bg-light py-4">
                    <div class="row justify-content-center">
                        <div class="col-md-10 text-center">
                            <p class="text-muted mb-4">
                                Teliti kembali berkas di atas. Gunakan tombol <strong>Tolak</strong> jika terdapat ketidaksesuaian total, atau tombol <strong>Sahkan</strong> untuk menyelesaikan proses.
                            </p>
                            
                            <div class="d-flex justify-content-center gap-3">
                                {{-- Tombol Tolak (Pemicu Modal) --}}
                                <button type="button" class="btn btn-danger btn-lg px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTolak">
                                    <i class="fas fa-times-circle me-2"></i> TOLAK BERKAS
                                </button>

                                {{-- Tombol Sahkan --}}
                                <form action="{{ route('validasi.sahkan', $submission->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-lg px-5 fw-bold shadow-sm" onclick="return confirm('Apakah Bapak/Ibu yakin ingin mengesahkan pengajuan ini secara final?')">
                                        <i class="fas fa-file-signature me-2"></i> SAHKAN SEKARANG
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Penolakan Kabid Perbendaharaan --}}
<div class="modal fade" id="modalTolak" tabindex="-1" aria-labelledby="modalTolakLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('validasi.tolak', $submission->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white border-0">
                    <h5 class="modal-title" id="modalTolakLabel fw-bold">Alasan Penolakan Kabid Perbendaharaan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan Penolakan:</label>
                        <textarea name="catatan" class="form-control" rows="4" placeholder="Jelaskan alasan pengembalian berkas secara detail agar operator dapat segera memperbaiki..." required></textarea>
                        <div class="form-text text-danger">Minimal 10 karakter.</div>
                    </div>
                    <div class="alert alert-warning border-0 small mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i> <strong>PENTING:</strong> Penolakan ini akan mengembalikan status pengajuan menjadi <strong>Ditolak</strong> dan mewajibkan perbaikan pada seluruh berkas terkait.
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger px-4">Kirim Penolakan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .bg-success-soft {
        background-color: rgba(40, 167, 69, 0.08);
    }
</style>
@endsection