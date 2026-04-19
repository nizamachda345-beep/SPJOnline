@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-file-upload me-2"></i>Form Pengajuan Berkas SPP/SPM
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('submissions.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 border-end">
                                <h6 class="fw-bold mb-3 text-secondary">Data Instansi & Kegiatan</h6>
                                
                              <div class="mb-3">
    <label class="form-label small fw-bold text-muted">Instansi Pengirim</label>
    <div class="input-group">
        <span class="input-group-text bg-light"><i class="fas fa-building text-primary"></i></span>
        <input type="text" class="form-control bg-light fw-bold" value="{{ $userDepartment->nama_dinas }}" readonly>
    </div>
    <div class="form-text small text-info">Data instansi terkunci otomatis berdasarkan akun Anda.</div>
</div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Nomor SPP/SPM</label>
                                    <input type="text" name="nomor_spp_spm" class="form-control" placeholder="Contoh: 001/SPP/2026" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Tanggal Pengajuan</label>
                                    <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Judul Kegiatan</label>
                                    <input type="text" name="judul_kegiatan" class="form-control" placeholder="Nama Kegiatan Utama" required>
                                </div>
                            </div>

                            <div class="col-md-6 ps-md-4">
                                <h6 class="fw-bold mb-3 text-secondary">Detail Anggaran</h6>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Judul Sub-Kegiatan</label>
                                    <input type="text" name="judul_sub_kegiatan" class="form-control" placeholder="Nama Sub-Kegiatan" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Perihal / Keterangan</label>
                                    <textarea name="perihal" class="form-control" rows="2" placeholder="Contoh: Pencairan Dana ATK Triwulan I" required></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Total Pencairan (Rp)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">Rp</span>
                                        <input type="number" name="total_pencairan" class="form-control" placeholder="0" required>
                                    </div>
                                    <div class="form-text text-info small mt-1">
                                        <i class="fas fa-info-circle me-1"></i>Masukkan angka saja tanpa titik/koma.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="fw-bold mb-3 text-secondary"><i class="fas fa-paperclip me-2"></i>Dokumen Lampiran (Format PDF, Maks. 10MB)</h6>
                        
                        <div class="row">
                            @php
                                $docs = [
                                    'SPP/SPM' => 'Dokumen SPP/SPM',
                                    'Kwitansi' => 'Kwitansi/Bukti Bayar',
                                    'Rincian Kegiatan' => 'Rincian Belanja',
                                    'Laporan Kegiatan' => 'Laporan Kegiatan',
                                    'Foto Kegiatan' => 'Dokumentasi/Foto',
                                    'Dokumen Pendukung' => 'Dokumen Pendukung Lainnya'
                                ];
                            @endphp

                            @foreach($docs as $key => $label)
                            <div class="col-md-4 mb-4">
                                <div class="p-3 border rounded bg-light">
                                    <label class="form-label small fw-bold text-uppercase">{{ $label }}</label>
                                    <input type="file" name="files[{{ $key }}]" class="form-control form-control-sm" accept="application/pdf" required>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="mt-4 border-top pt-4 text-end">
                            <button type="reset" class="btn btn-light px-4 me-2">
                                <i class="fas fa-undo me-2"></i>Reset
                            </button>
                            <button type="submit" class="btn btn-primary px-5 shadow">
                                <i class="fas fa-paper-plane me-2"></i>Kirim Berkas Ke Verifikator
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection