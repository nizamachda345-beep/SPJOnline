{{-- View: submissions/create --}}
@extends('layouts.app')

@section('content')
<style>
    .submission-amount-group {
        display: flex;
        width: 100%;
    }

    .submission-amount-group > .form-control {
        flex: 1 1 0%;
        width: 1%;
        min-width: 0;
        border-top-right-radius: var(--bs-border-radius) !important;
        border-bottom-right-radius: var(--bs-border-radius) !important;
    }
</style>

<div class="container-fluid"
     x-data="{
        jenisPencairan: '{{ old('jenis_pencairan') }}'
     }">

    <div class="row">
        <div class="col-md-12">
            <div class="card glass-card-dark shadow-sm border-0">

                <div class="card-header py-3">
                    <h5 class="mb-0 fw-bold text-light">
                        <i class="fas fa-file-upload me-2 text-danger"></i>
                        FORM PENGAJUAN SP2D
                    </h5>
                </div>

                <div class="card-body p-4">

                    @if (session('error'))
                        <div class="alert alert-warning alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button"
                                    class="btn-close"
                                    data-bs-dismiss="alert">
                            </button>
                        </div>
                    @endif

                    <form action="{{ route('submissions.store') }}"
                          method="POST"
                          enctype="multipart/form-data"
                          class="no-ajax">

                        @csrf

                        <div class="row">

                            <div class="col-md-6 border-end">

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-light">
                                        Instansi Pengirim
                                    </label>

                                    <div class="input-group">
                                        <span class="input-group-text bg-light">
                                            <i class="fas fa-building text-danger"></i>
                                        </span>

                                        <input type="text"
                                               class="form-control bg-light fw-bold"
                                               value="{{ $userDepartment->nama_dinas }}"
                                               readonly>
                                    </div>
                                </div>

                                <div class="row g-2 mb-3">

                                    <div class="col-6">
                                        <label for="nomorSpp" class="form-label text-light small fw-bold mb-1">
                                            Nomor SPP
                                        </label>

                                        <input id="nomorSpp" type="text"
                                               name="nomor_spp"
                                               class="form-control form-control-sm @error('nomor_spp') is-invalid @enderror"
                                               placeholder="00123"
                                               value="{{ old('nomor_spp') }}"
                                               required
                                               maxlength="5">

                                        @error('nomor_spp')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-6">
                                        <label for="tanggalSpp" class="form-label text-light small fw-bold mb-1">
                                            Tanggal SPP
                                        </label>

                                        <input id="tanggalSpp" type="date"
                                               name="tanggal_spp"
                                               class="form-control form-control-sm @error('tanggal_spp') is-invalid @enderror"
                                               value="{{ old('tanggal_spp') }}"
                                               required>

                                        @error('tanggal_spp')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-6">
                                        <label for="nomorSpm" class="form-label text-light small fw-bold mb-1">
                                            Nomor SPM
                                        </label>

                                        <input id="nomorSpm" type="text"
                                               name="nomor_spm"
                                               class="form-control form-control-sm @error('nomor_spm') is-invalid @enderror"
                                               placeholder="00456"
                                               value="{{ old('nomor_spm') }}"
                                               required
                                               maxlength="5">

                                        @error('nomor_spm')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="col-6">
                                        <label for="tanggalSpm" class="form-label text-light small fw-bold mb-1">
                                            Tanggal SPM
                                        </label>

                                        <input id="tanggalSpm" type="date"
                                               name="tanggal_spm"
                                               class="form-control form-control-sm @error('tanggal_spm') is-invalid @enderror"
                                               value="{{ old('tanggal_spm') }}"
                                               required>

                                        @error('tanggal_spm')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-md-6 mb-3">

                                        <label class="form-label small text-light fw-bold">
                                            Jenis Permintaan
                                        </label>

                                        <select name="jenis_pencairan"
                                                x-model="jenisPencairan"
                                                class="form-select @error('jenis_pencairan') is-invalid @enderror"
                                                required>

                                            <option value="" disabled>
                                                -- Pilih Permintaan --
                                            </option>

                                            @foreach([
                                                'LS',
                                                'GU',
                                                'TU',
                                                    'GU Nihil',
                                                'TU Nihil',
                                                'UP'
                                            ] as $jenis)

                                                <option value="{{ $jenis }}"
                                                    {{ old('jenis_pencairan') == $jenis ? 'selected' : '' }}>
                                                    {{ $jenis }}
                                                </option>

                                            @endforeach

                                        </select>

                                        @error('jenis_pencairan')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>

                                    <div class="col-md-6 mb-3">

                                        <label class="form-label small text-light fw-bold">
                                            Sumber Dana
                                        </label>

                                        <select name="sumber_dana"
                                                class="form-select @error('sumber_dana') is-invalid @enderror"
                                                required>

                                            <option value="" disabled>
                                                -- Pilih Sumber Dana --
                                            </option>

                                            @foreach([
                                                'DAU',
                                                'DBH',
                                                'PAD',
                                                'DAK'
                                            ] as $sumber)

                                                <option value="{{ $sumber }}"
                                                    {{ old('sumber_dana') == $sumber ? 'selected' : '' }}>
                                                    {{ $sumber }}
                                                </option>

                                            @endforeach

                                        </select>

                                        @error('sumber_dana')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror

                                    </div>

                                </div>

                                <div class="mb-3">

                                    <label class="form-label small text-light fw-bold">
                                        Tanggal Pengajuan
                                    </label>
                                    <small class="form-text text-light fst-italic">
                                        Sesuaikan dengan tanggal SPM
                                    </small>

                                    <input type="date"
                                           name="tanggal"
                                           class="form-control @error('tanggal') is-invalid @enderror"
                                           value="{{ old('tanggal', date('Y-m-d')) }}"
                                           required>

                                    @error('tanggal')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                            </div>

                            <div class="col-md-6 ps-md-4">

                                <div class="mb-3">

                                    <label class="form-label small text-light fw-bold">
                                        Kegiatan
                                    </label>

                                    <input type="text"
                                           name="judul_kegiatan"
                                           class="form-control @error('judul_kegiatan') is-invalid @enderror"
                                           value="{{ old('judul_kegiatan') }}"
                                           required>

                                    @error('judul_kegiatan')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                                <div class="mb-3">

                                    <label class="form-label small text-light fw-bold">
                                        Sub-Kegiatan
                                    </label>

                                    <input type="text"
                                           name="judul_sub_kegiatan"
                                           class="form-control @error('judul_sub_kegiatan') is-invalid @enderror"
                                           value="{{ old('judul_sub_kegiatan') }}"
                                           required>

                                    @error('judul_sub_kegiatan')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                                <div class="mb-3">

                                    <label class="form-label small text-light fw-bold">
                                        Perihal / Keterangan
                                    </label>

                                    <textarea name="perihal"
                                              class="form-control @error('perihal') is-invalid @enderror"
                                              rows="2"
                                              required>{{ old('perihal') }}</textarea>

                                    @error('perihal')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                                <div class="mb-3">

                                    <label class="form-label small text-light fw-bold">
                                        Jumlah Permintaan (Rp)
                                    </label>

                                    <div class="input-group submission-amount-group w-100">

                                        <span class="input-group-text">
                                            Rp
                                        </span>

                                        <input type="text"
                                               id="inputPencairan"
                                               name="total_pencairan_display"
                                               class="form-control flex-fill @error('total_pencairan') is-invalid @enderror"
                                               value="{{ old('total_pencairan') }}"
                                               required
                                               oninput="formatRupiah(this)">

                                        <input type="hidden"
                                               name="total_pencairan"
                                               id="total_pencairan_raw"
                                               value="{{ old('total_pencairan') }}">

                                    </div>

                                    @error('total_pencairan')
                                        <div class="invalid-feedback d-block">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                            </div>

                        </div>

                        <hr class="my-4 border-light">

                        <div class="mb-4">

                            <h6 class="fw-bold mb-2 text-light">
                                <i class="fas fa-paperclip me-2"></i>
                                Dokumen Lampiran
                            </h6>

                            <div class="small text-light mb-3">
                                Setiap form merupakan <strong>satu file PDF</strong>.
                                Maksimal {{ $systemSettings['submission.document_max_size_mb'] ?? 10 }} MB per file.
                            </div>

                            <template x-if="jenisPencairan === 'LS'">

                                <div class="row">

                                    <div class="col-md-6 mb-4">

                                        <div class="p-3 border rounded bg-light">

                                            <label class="form-label small fw-bold text-dark">
                                                Dokumen LS (SPP, Verifikasi SPP/SPM, SPTJM)
                                            </label>

                                            <input type="file"
                                                   name="files[ls_dokumen]"
                                                   class="form-control form-control-sm"
                                                   accept="application/pdf"
                                                   required>
                                        </div>

                                    </div>

                                    <div class="col-md-6 mb-4">

                                        <div class="p-3 border rounded bg-light">

                                            <label class="form-label small fw-bold text-dark">
                                                Dokumen SPJ
                                            </label>

                                            <input type="file"
                                                   name="files[spj]"
                                                   class="form-control form-control-sm"
                                                   accept="application/pdf"
                                                   required>

                                        </div>

                                    </div>

                                </div>

                            </template>

                            <template x-if="jenisPencairan === 'TU'">

                                <div class="row">

                                    <div class="col-md-6 mb-4">

                                        <div class="p-3 border rounded bg-light">

                                            <label class="form-label small fw-bold text-dark">
                                                Dokumen TU (SPP, Verifikasi SPP/SPM, SPTJM, Persetujuan BUD)
                                            </label>

                                            <input type="file"
                                                   name="files[tu_dokumen]"
                                                   class="form-control form-control-sm"
                                                   accept="application/pdf"
                                                   required>

                                        </div>

                                    </div>

                                </div>

                            </template>

                            <template x-if="jenisPencairan === 'GU'">

                                <div class="row">

                                    <div class="col-md-6 mb-4">

                                        <div class="p-3 border rounded bg-light">

                                            <label class="form-label small fw-bold text-dark">
                                                Dokumen GU (SPP, Verifikasi SPP/SPM, SPTJM, Persetujuan BUD)
                                            </label>

                                            <input type="file"
                                                   name="files[gu_dokumen]"
                                                   class="form-control form-control-sm"
                                                   accept="application/pdf"
                                                   required>

                                        </div>

                                    </div>

                                    <div class="col-md-6 mb-4">

                                        <div class="p-3 border rounded bg-light">

                                            <label class="form-label small fw-bold text-dark">
                                                Dokumen SPJ
                                            </label>

                                            <input type="file"
                                                   name="files[spj]"
                                                   class="form-control form-control-sm"
                                                   accept="application/pdf"
                                                   required>

                                        </div>

                                    </div>

                                </div>

                            </template>

                            <template x-if="jenisPencairan === 'UP'">

                                <div class="row">

                                    <div class="col-md-6 mb-4">

                                        <div class="p-3 border rounded bg-light">

                                            <label class="form-label small fw-bold text-dark">
                                                Dokumen UP (SPP, Verifikasi SPP/SPM, SPTJM)
                                            </label>

                                            <input type="file"
                                                   name="files[up_dokumen]"
                                                   class="form-control form-control-sm"
                                                   accept="application/pdf"
                                                   required>

                                        </div>

                                    </div>

                                </div>

                            </template>

                            <template x-if="jenisPencairan === 'TU Nihil'">

                                <div class="row">

                                    <div class="col-md-6 mb-4">

                                        <div class="p-3 border rounded bg-light">

                                            <label class="form-label small fw-bold text-dark">
                                                Dokumen SPJ
                                            </label>

                                            <input type="file"
                                                   name="files[spj]"
                                                   class="form-control form-control-sm"
                                                   accept="application/pdf"
                                                   required>

                                        </div>

                                    </div>

                                    <div class="col-md-6 mb-4">

                                        <div class="p-3 border rounded bg-light">

                                            <label class="form-label small fw-bold text-dark">
                                                Dokumen STS
                                                <span class="text-secondary">
                                                    (Optional)
                                                </span>
                                            </label>

                                            <input type="file"
                                                   name="files[sts]"
                                                   class="form-control form-control-sm"
                                                   accept="application/pdf">

                                        </div>

                                    </div>

                                    <div class="col-md-6 mb-4">

                                        <div class="p-3 border rounded bg-light">

                                            <label class="form-label small fw-bold text-dark">
                                                Dokumen SP2D
                                            </label>
                                            <small class="form-text text-muted fst-italic">
                                                Dokumen SP2D pada pengajuan TU Awal
                                            </small>

                                            <input type="file"
                                                   name="files[sp2d_tu]"
                                                   class="form-control form-control-sm"
                                                   accept="application/pdf"
                                                   required>

                                        </div>

                                    </div>

                                </div>

                            </template>

                            <template x-if="jenisPencairan === 'GU Nihil'">

                                <div class="row">

                                    <div class="col-md-6 mb-4">

                                        <div class="p-3 border rounded bg-light">

                                            <label class="form-label small fw-bold text-dark">
                                                Dokumen SPJ
                                            </label>

                                            <input type="file"
                                                   name="files[spj]"
                                                   class="form-control form-control-sm"
                                                   accept="application/pdf"
                                                   required>

                                        </div>

                                    </div>

                                    <div class="col-md-6 mb-4">

                                        <div class="p-3 border rounded bg-light">

                                            <label class="form-label small fw-bold text-dark">
                                                Dokumen STS
                                                <span class="text-secondary">
                                                    (Optional)
                                                </span>
                                            </label>

                                            <input type="file"
                                                   name="files[sts]"
                                                   class="form-control form-control-sm"
                                                   accept="application/pdf">

                                        </div>

                                    </div>

                                </div>

                            </template>

                            <template x-if="!jenisPencairan">

                                <div class="alert alert-secondary">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Silakan pilih <strong>Jenis Permintaan</strong>
                                    terlebih dahulu untuk menampilkan dokumen
                                    lampiran yang harus diunggah.
                                </div>

                            </template>

                        </div>

                        <div class="mt-4 border-top pt-4 text-end">

                            <button type="submit"
                                    class="btn btn-glass-primary px-5 shadow">

                                <i class="fas fa-paper-plane me-2"></i>
                                Proses Pengajuan

                            </button>

                        </div>

                    </form>

                </div>

            </div>
        </div>
    </div>

</div>

<script>

// Function: Memformat nilai input menjadi rupiah
function formatRupiah(element) {

    let value = element.value.replace(/[^0-9]/g, "");

    let rawInput =
        document.getElementById('total_pencairan_raw');

    rawInput.value = value;

    element.value =
        value !== ""
            ? new Intl.NumberFormat('id-ID').format(value)
            : "";
}

window.addEventListener('DOMContentLoaded', function () {

    let input =
        document.getElementById('inputPencairan');

    if (input && input.value) {

        formatRupiah(input);

    }

});

</script>

@endsection
