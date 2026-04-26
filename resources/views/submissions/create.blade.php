@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-file-upload me-2"></i>Form Pengajuan
                    </h5>
                </div>
                <div class="card-body p-4">
                    
                    {{-- Alert Error Umum --}}
                    @if (session('error'))
                        <div class="alert alert-warning alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

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
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Nomor SPP/SPM (Wajib 5 Digit Angka)</label>
                                    <input type="text" name="nomor_spp_spm" 
                                           class="form-control @error('nomor_spp_spm') is-invalid @enderror" 
                                           placeholder="Contoh: 00001" 
                                           value="{{ old('nomor_spp_spm') }}" required maxlength="5">
                                    @error('nomor_spp_spm')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Jenis Pencairan</label>
                                    <select name="jenis_pencairan" class="form-select @error('jenis_pencairan') is-invalid @enderror" required>
                                        <option value="" disabled selected>-- Pilih Jenis Pencairan --</option>
                                        @foreach(['LS', 'GU', 'TU', 'LS Nihil', 'GU Nihil', 'TU Nihil'] as $jenis)
                                            <option value="{{ $jenis }}" {{ old('jenis_pencairan') == $jenis ? 'selected' : '' }}>{{ $jenis }}</option>
                                        @endforeach
                                    </select>
                                    @error('jenis_pencairan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Tanggal Pengajuan</label>
                                    <input type="date" name="tanggal" 
                                           class="form-control @error('tanggal') is-invalid @enderror" 
                                           value="{{ old('tanggal', date('Y-m-d')) }}" required>
                                    @error('tanggal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 ps-md-4">
                                <h6 class="fw-bold mb-3 text-secondary">Detail Anggaran</h6>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Judul Kegiatan</label>
                                    <input type="text" name="judul_kegiatan" 
                                           class="form-control @error('judul_kegiatan') is-invalid @enderror" 
                                           value="{{ old('judul_kegiatan') }}" placeholder="Nama Kegiatan Utama" required>
                                    @error('judul_kegiatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Judul Sub-Kegiatan</label>
                                    <input type="text" name="judul_sub_kegiatan" 
                                           class="form-control @error('judul_sub_kegiatan') is-invalid @enderror" 
                                           value="{{ old('judul_sub_kegiatan') }}" placeholder="Nama Sub-Kegiatan" required>
                                    @error('judul_sub_kegiatan')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Perihal / Keterangan</label>
                                    <textarea name="perihal" class="form-control @error('perihal') is-invalid @enderror" 
                                              rows="2" placeholder="Contoh: Pencairan Dana ATK..." required>{{ old('perihal') }}</textarea>
                                    @error('perihal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Total Pencairan (Rp)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">Rp</span>
                                        <input type="number" name="total_pencairan" 
                                               class="form-control @error('total_pencairan') is-invalid @enderror" 
                                               value="{{ old('total_pencairan') }}" placeholder="0" required>
                                        @error('total_pencairan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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
                                <div class="p-3 border rounded {{ $errors->has('files.'.$key) ? 'border-danger bg-soft-danger' : 'bg-light' }}">
                                    <label class="form-label small fw-bold text-uppercase">{{ $label }}</label>
                                    <input type="file" name="files[{{ $key }}]" 
                                           class="form-control form-control-sm @error('files.'.$key) is-invalid @enderror" 
                                           accept="application/pdf" required>
                                    @error('files.'.$key)
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="mt-4 border-top pt-4 text-end">
                            <button type="submit" class="btn btn-primary px-5 shadow">
                                <i class="fas fa-paper-plane me-2"></i>Proses Pengajuan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection