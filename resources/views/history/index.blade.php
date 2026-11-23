{{-- View: history/index --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid history-page">

    <div class="card glass-card-dark border-0 shadow-lg rounded-4 p-4 mb-3 position-relative overflow-hidden">
    <div class="d-flex justify-content-between align-items-center flex-wrap position-relative">
        <div class="header-title d-flex align-items-center">

            <div class="me-3 opacity-50">
                <i class="fas fa-align-right text-primary fs-4"></i>
            </div>

            <div class="border-start border-white border-opacity-10 ps-3">
                <div class="d-flex align-items-center mb-1">
                    <h3 class="fw-black text-white tracking-tighter mb-0">
                        Arsip Dokumen <span class="text-primary glow-text-blue">Juara</span>
                    </h3>
                </div>
                <p class="text-muted-show small mb-0 fw-medium opacity-75">
                    Kelola dan telusuri riwayat dokumen yang telah disahkan secara sistematis.
                </p>
            </div>
        </div>
        @hasanyrole('super_admin|kasie_bud|kabid_perbendaharaan')
            <div class="header-actions">

                <button type="button" class="btn btn-outline-success rounded-pill px-3 shadow-sm me-2 btn-sm" data-bs-toggle="modal" data-bs-target="#modalExport">
                    <i class="fas fa-file-excel me-1"></i> Export Excel
                </button>

                <button type="button" class="btn btn-outline-danger rounded-pill px-4 shadow-sm btn-sm" data-bs-toggle="modal" data-bs-target="#modalPrint">
                    <i class="fas fa-file-pdf me-1"></i> Cetak PDF
                </button>
            </div>
        @endhasanyrole
    </div>
</div>

<div class="card glass-card-dark border-0 shadow-sm bg-opacity-25 rounded-4 mb-4 bg-white">
    <div class="card-body p-3">
        <form action="{{ route('history.index') }}" method="GET" class="row align-items-end g-3">
            <div class="col-md-3">
                <label class="form-label small fw-bold text-light uppercase">Pencarian Cepat</label>
                <div class="input-group search-group">
                    <span class="input-group-text border-0 bg-light"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-0 bg-light rounded-end"
                        placeholder="No. SPP/SPM, Dinas, Kegiatan, atau Total..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-bold text-light uppercase">Filter Tanggal</label>
                <input type="date" name="date" class="form-control border-0 bg-light" value="{{ request('date') }}">
            </div>

            <div class="col-md-2">
                <label class="form-label small fw-bold text-light uppercase">Kategori Pencairan</label>
                <select name="jenis_pencairan" class="form-select border-0 bg-light">
                    <option value="">Semua Kategori</option>
                    @foreach(['LS', 'GU', 'TU', 'GU Nihil', 'TU Nihil', 'UP'] as $kat)
                        <option value="{{ $kat }}" {{ request('jenis_pencairan') == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label small fw-bold text-light uppercase">Status SP2D</label>
                <select name="status_sp2d" class="form-select border-0 bg-light">
                    <option value="">Semua Status SP2D</option>
                    <option value="not_required" {{ request('status_sp2d') === 'not_required' ? 'selected' : '' }}>Tidak Diperlukan</option>
                    <option value="not_uploaded" {{ request('status_sp2d') === 'not_uploaded' ? 'selected' : '' }}>Belum Diupload</option>
                    <option value="pending" {{ request('status_sp2d') === 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="revision" {{ request('status_sp2d') === 'revision' ? 'selected' : '' }}>Revisi</option>
                    <option value="verified" {{ request('status_sp2d') === 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                </select>
            </div>

            <div class="col-md-2">
                <div class="d-flex gap-2 flex-nowrap">
                    <button type="submit" class="btn btn-dark px-3 rounded-pill flex-grow-1 shadow-sm d-flex align-items-center justify-content-center">
                        <i class="fas fa-sliders-h me-2"></i>
                        <span>Filter</span>
                    </button>

                    <a href="{{ route('history.index') }}" class="btn btn-light px-3 rounded-pill border shadow-xs text-muted d-flex align-items-center justify-content-center reset-btn">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

    <div class="card glass-card-dark border-0 shadow-sm overflow-hidden" style="border-radius: 10px;" id="monitoring-container">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark text-dark small uppercase">
                    <tr class="text-light text-center text-nowrap">
                        <th class="ps-4 py-3">No</th>
                        <th class="py-3 text-center">SKPD</th>
                        <th class="py-3 text-center">Tgl. Pengajuan</th>
                        <th class="py-3 text-center">No. SPP</th>
                        <th class="py-3 text-center">No. SPM</th>
                        @if(auth()->user()->hasRole('super_admin'))
                            <th class="py-3 text-center">Instansi/Dinas</th>
                        @endif
                        <th class="py-3">Kegiatan</th>
                        <th class="py-3">Sub-Kegiatan</th>
                        <th class="py-3">Perihal</th>
                        <th class="py-3 text-center">Jumlah Permintaan</th>
                        <th class="py-3 text-center">Jenis Belanja</th>
                        <th class="py-3">Sumber Dana</th>
                        <th class="text-center py-3">Status</th>
                        <th class="text-center py-3">Status SP2D</th>
                        <th class="text-center pe-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($histories as $index => $s)
                    <tr class="align-middle text-nowrap">
                        <td class="ps-4 text-muted text-center">{{ $histories->firstItem() + $index }}</td>
                        <td>
                            <div class="fw-bold text-dark" data-bs-toggle="tooltip" title="{{ $s->department->nama_dinas ?? '-' }}">
                                {{ Str::limit($s->department->nama_dinas ?? '-', 20, '...') }}
                            </div>
                            <small class="text-muted">{{ $s->department->kode_dinas ?? '' }}</small>
                        </td>

                        <td class="text-center">{{ $s->tanggal ? \Carbon\Carbon::parse($s->tanggal)->format('d/m/Y') : '-' }}</td>

                        <td class="text-center">
                            <span class="badge bg-light text-dark border fw-bold">
                                {{ $s->nomor_spp }} - {{ $s->tanggal_spp ? \Carbon\Carbon::parse($s->tanggal_spp)->format('d/m/Y') : '-' }}
                            </span>
                        </td>

                        <td class="text-center">
                            <span class="badge bg-light text-dark border fw-bold">
                                {{ $s->nomor_spm }} - {{ $s->tanggal_spm ? \Carbon\Carbon::parse($s->tanggal_spm)->format('d/m/Y') : '-' }}
                            </span>
                        </td>

                        @if(auth()->user()->hasRole('super_admin'))
                            <td class="text-center">
                                <div class="text-dark" data-bs-toggle="tooltip" title="{{ $s->department->nama_dinas ?? '-' }}">
                                    {{ Str::limit($s->department->nama_dinas ?? '-', 20, '...') }}
                                </div>
                            </td>
                        @endif

                        <td data-bs-toggle="tooltip" title="{{ $s->judul_kegiatan }}">
                            <div class="text-truncate" style="max-width: 150px;">
                                {{ Str::limit($s->judul_kegiatan, 15) }}
                            </div>
                        </td>

                        <td data-bs-toggle="tooltip" title="{{ $s->judul_sub_kegiatan }}">
                            <div class="text-truncate" style="max-width: 150px;">
                                {{ Str::limit($s->judul_sub_kegiatan, 15) }}
                            </div>
                        </td>

                        <td data-bs-toggle="tooltip" title="{{ $s->perihal }}">
                            <div class="text-truncate" style="max-width: 150px;">
                                {{ Str::limit($s->perihal, 15) }}
                            </div>
                        </td>

                        <td class="fw-bold text-dark text-center">Rp {{ number_format($s->total_pencairan, 0, ',', '.') }}</td>

                        <td data-bs-toggle="tooltip" title="{{ $s->jenis_pencairan }}" class="fw-bold text-center">
                            <span class="text-dark fw-bold shadow-sm" title="{{ $s->jenis_pencairan }}">
                                {{ str_contains($s->jenis_pencairan, 'Nihil')
                                    ? 'N. ' . Str::before($s->jenis_pencairan, ' Nihil')
                                    : $s->jenis_pencairan }}
                            </span>
                        </td>

                        <td data-bs-toggle="tooltip" title="{{ $s->sumber_dana }}" class="fw-bold">
                            <span class="text-dark fw-bold shadow-sm" title="{{ $s->sumber_dana }}">
                                {{ $s->sumber_dana }}
                            </span>
                        </td>

                        <td class="text-center">
                            <span class="text-success fw-bold">
                                <i class="fas fa-check-circle me-1"></i> Disahkan
                            </span>
                        </td>

                       @php
                            $sp2d = $s->documents
                                ->first(function ($doc) {
                                    $jenis = strtoupper(
                                        trim($doc->jenis_dokumen ?? $doc->jenis_document ?? '')
                                    );

                                    return str_contains($jenis, 'SP2D');
                                });
                        @endphp

                        <td class="text-center">
                            @if(strtoupper(trim($s->jenis_pencairan ?? '')) === 'GU NIHIL')

                                <span class="badge bg-light text-secondary border fw-semibold px-3 py-2">
                                    <i class="fas fa-minus-circle me-1"></i>
                                    Tidak Diperlukan
                                </span>

                            @elseif(!$sp2d)

                                <span class="badge bg-danger-subtle text-danger-emphasis border border-danger-subtle fw-semibold px-3 py-2">
                                    <i class="fas fa-cloud-upload-alt me-1"></i>
                                    Belum Diupload
                                </span>

                            @elseif($sp2d->status_dokumen === 'verified')

                                <span class="badge bg-success-subtle text-success border border-success-subtle fw-semibold px-3 py-2">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Terverifikasi
                                </span>

                            @elseif($sp2d->status_dokumen === 'revisi')

                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle fw-semibold px-3 py-2">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    Revisi
                                </span>

                            @else

                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle fw-semibold px-3 py-2">
                                    <i class="fas fa-clock me-1"></i>
                                    Menunggu Verifikasi
                                </span>

                            @endif
                        </td>

                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center align-items-center gap-2">
                                <a href="{{ route('history.show', $s->id) }}"
                                   class="btn btn-sm btn-outline-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                   style="width: 32px; height: 32px;"
                                   title="Lihat Detail Arsip"
                                   data-bs-toggle="tooltip">
                                    <i class="fas fa-eye" style="font-size: 0.8rem;"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ auth()->user()->hasRole('super_admin') ? '15' : '14' }}" class="text-center py-5 text-muted">
                            <div class="d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-folder-open fa-3x mb-3 opacity-25"></i>
                                <span>Data riwayat validasi tidak ditemukan.</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($histories->hasPages())
        <div class="my-3 d-flex justify-content-center">
            <div class="glass-pagination bg-opacity-75 px-4 py-1 rounded-pill shadow-sm">
                {{ $histories->links('pagination::bootstrap-5') }}
            </div>
        </div>
    @endif
</div>

{{-- Modal: Ekspor riwayat pengajuan --}}
<div class="modal fade" id="modalExport" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark"><i class="fas fa-file-excel text-success me-2"></i>Parameter Export Excel</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('history.export') }}" method="GET" class="no_ajax">
                <div class="modal-body py-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">Dari Tanggal</label>
                            <input type="date" name="start_date" class="form-control bg-light border-0">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">Sampai Tanggal</label>
                            <input type="date" name="end_date" class="form-control bg-light border-0">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Jenis Pencairan</label>
                            <select name="jenis_pencairan" class="form-select bg-light border-0">
                                <option value="">Semua Jenis</option>
                                <option value="LS">LS</option>
                                <option value="GU">GU</option>
                                <option value="TU">TU</option>
                                <option value="UP">UP</option>
                                <option value="GU Nihil">GU Nihil</option>
                                <option value="TU Nihil">TU Nihil</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Nama Kegiatan / Sub Kegiatan</label>
                            <input type="text" name="kegiatan" class="form-control bg-light border-0" placeholder="Ketik kata kunci...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Download Excel</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Cetak riwayat pengajuan --}}
<div class="modal fade" id="modalPrint" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="fw-bold text-dark"><i class="fas fa-file-pdf text-danger me-2"></i>Parameter Cetak PDF</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('history.print') }}" method="GET" target="_blank" class="no-ajax">
                <div class="modal-body py-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold">Rentang Waktu</label>
                            <div class="input-group">
                                <input type="date" name="start_date" class="form-control bg-light border-0">
                                <span class="input-group-text bg-light border-0">s/d</span>
                                <input type="date" name="end_date" class="form-control bg-light border-0">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Kategori Pencairan</label>
                            <select name="jenis_pencairan" class="form-select bg-light border-0">
                                <option value="">Semua Kategori</option>
                                <option value="LS">LS</option>
                                <option value="GU">GU</option>
                                <option value="TU">TU</option>
                                <option value="UP">UP</option>
                                <option value="GU Nihil">GU Nihil</option>
                                <option value="TU Nihil">TU Nihil</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">Spesifik Kegiatan</label>
                            <input type="text" name="kegiatan" class="form-control bg-light border-0" placeholder="Nama kegiatan atau sub kegiatan...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Cetak Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
