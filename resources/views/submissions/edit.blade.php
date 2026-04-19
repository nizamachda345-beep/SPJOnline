@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px;">
                            <i class="fas fa-tools fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">Daftar Perbaikan Dokumen</h5>
                            <p class="text-muted small mb-0">Silakan perbaiki dokumen-dokumen yang ditolak di bawah ini.</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    {{-- Header Informasi Pengajuan --}}
                    <div class="row mb-4 pb-3 border-bottom">
                        <div class="col-sm-6">
                            <small class="text-muted d-block uppercase fw-bold" style="font-size: 0.7rem;">INSTANSI / DINAS</small>
                            <p class="fw-bold text-dark mb-0">{{ $submission->department->nama_dinas }}</p>
                        </div>
                        <div class="col-sm-6 text-sm-end mt-2 mt-sm-0">
                            <small class="text-muted d-block uppercase fw-bold" style="font-size: 0.7rem;">NOMOR SPP/SPM</small>
                            <p class="fw-bold text-primary mb-0">{{ $submission->nomor_spp_spm }}</p>
                        </div>
                    </div>

                    <form action="{{ route('submissions.update', $submission->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Judul List --}}
                        <div class="mb-3">
                            <h6 class="fw-bold text-secondary px-2"><i class="fas fa-list-ul me-2"></i>Berkas yang Harus Diupload Ulang:</h6>
                        </div>

                        {{-- Iterasi List Dokumen yang Ditolak --}}
                        <div class="list-group list-group-flush border rounded">
                            @foreach($submission->documents as $doc)
                            <div class="list-group-item p-4">
                                <div class="row align-items-start">
                                    {{-- Kolom Info Dokumen --}}
                                    <div class="col-lg-5 mb-3 mb-lg-0">
                                        <div class="d-flex align-items-center mb-2">
                                            <span class="badge bg-danger-soft text-danger me-2 p-2 rounded">
                                                <i class="fas fa-file-pdf fa-lg"></i>
                                            </span>
                                            <h6 class="fw-bold text-dark mb-0 text-uppercase">{{ $doc->jenis_dokumen }}</h6>
                                        </div>
                                        
                                        <div class="bg-light p-3 rounded border-start border-danger border-3">
                                            <small class="text-danger fw-bold d-block mb-1">Catatan Penolakan:</small>
                                            <span class="text-dark small">"{{ $doc->catatan }}"</span>
                                        </div>
                                    </div>

                                    {{-- Kolom Input Upload --}}
                                    <div class="col-lg-7">
                                        <div class="form-group">
                                            <label class="form-label small fw-bold text-muted">Klik untuk pilih file pengganti:</label>
                                            <div class="input-group">
                                                <input type="file" 
                                                       name="files[{{ $doc->id }}]" 
                                                       class="form-control @error('files.'.$doc->id) is-invalid @enderror" 
                                                       required>
                                            </div>
                                            <div class="d-flex justify-content-between mt-1">
                                                <span class="text-muted" style="font-size: 0.75rem;">Max: 2MB (PDF/JPG/PNG)</span>
                                                <span class="text-danger fw-bold" style="font-size: 0.75rem;">* Wajib</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="mt-4 pt-3 d-flex justify-content-between">
                            <a href="{{ route('submissions.index') }}" class="btn btn-light px-4 border">
                                <i class="fas fa-arrow-left me-2"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">
                                <i class="fas fa-cloud-upload-alt me-2"></i> Update Semua Berkas
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tambahan CSS Internal untuk mempercantik badge soft --}}
<style>
    .bg-danger-soft {
        background-color: rgba(220, 53, 69, 0.1);
    }
    .list-group-item:hover {
        background-color: #fafafa;
    }
</style>
@endsection