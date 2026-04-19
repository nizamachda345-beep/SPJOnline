@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h4 class="fw-bold text-uppercase">Detail Pengajuan Berkas iDJ</h4>
            <p class="text-muted">Nomor SPM: {{ $submission->nomor_spp_spm }}</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('submissions.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-5">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">Informasi Pengajuan</h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%" class="text-muted">Instansi</td>
                            <td class="fw-bold">: {{ $submission->department->nama_dinas }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tanggal</td>
                            <td>: {{ \Carbon\Carbon::parse($submission->tanggal)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Total Pencairan</td>
                            <td class="text-success fw-bold">: Rp {{ number_format($submission->total_pencairan, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status</td>
                            <td>: 
                                @if($submission->status == 'pending')
                                    <span class="badge bg-warning">Menunggu Verifikasi</span>
                                @elseif($submission->status == 'proses verifikasi')
                                    <span class="badge bg-info text-dark">Proses Verifikasi</span>
                                @elseif($submission->status == 'proses validasi')
                                    <span class="badge bg-primary">Proses Validasi</span>
                                @elseif($submission->status == 'disetujui')
                                    <span class="badge bg-success">Selesai (Disetujui)</span>
                                @elseif($submission->status == 'ditolak')
                                    <span class="badge bg-danger">Ditolak / Perlu Perbaikan</span>
                                @elseif($submission->status == 'rejected')
                                    <span class="badge bg-danger">Menunggu Revisi</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                    <hr>
                    <h6><strong>Kegiatan :</strong></h6>
                    <p class="text-muted small">{{ $submission->judul_kegiatan }}</p>
                    <h6><strong>Sub-Kegiatan :</strong></h6>
                    <p class="text-muted small">{{ $submission->judul_sub_kegiatan }}</p>
                    <h6><strong>Perihal / Keterangan :</strong></h6>
                    <p class="text-muted small">{{ $submission->perihal }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">Berkas Lampiran (PDF)</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach($submission->documents as $doc)
                        <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <div>
                                <i class="fas fa-file-pdf text-danger me-2 fa-lg"></i>
                                <span class="fw-bold text-uppercase">{{ str_replace('_', ' ', $doc->jenis_dokumen) }}</span>
                            </div>
                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn btn-sm btn-primary">
                                <i class="fas fa-download me-1"></i> Lihat / Download
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection