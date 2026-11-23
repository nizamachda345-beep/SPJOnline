{{-- View: submissions/index --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center glass-header-dark shadow-sm opacity-75 rounded-4 p-4 mb-3">
        <div class="d-flex align-items-center">
            <div class="me-3 text-light">
                <i class="fas fa-align-right fa-3x"></i>
            </div>
            <div>
                <h3 class="fw-bold text-light mb-1">History Pengajuan iDJ</h3>
                <p class="text-light mb-0">Kelola berkas SPP/SPM Anda dengan memantau status verifikasi secara real-time.</p>
            </div>
        </div>

        <div>
            @if(!$isTerkunci)
                <a href="{{ route('submissions.create') }}" class="btn shadow-sm btn-outline-light shadow-lg px-4">
                    <i class="fas fa-plus me-2"></i>Buat Pengajuan Baru
                </a>
            @else
                <a href="#" data-denial-message="{{ $submissionBlockMessage }}" onclick="return showSubmissionAccessDenied(this.dataset.denialMessage)" class="btn shadow-sm btn-outline-light shadow-lg px-4" style="cursor: not-allowed; opacity: 0.6;">
                    <i class="fas fa-lock me-2 text-danger"></i>Buat Pengajuan Baru
                </a>
            @endif
        </div>
    </div>

    <div class="glass-card-dark border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body p-4">
            <form action="{{ route('submissions.index') }}" method="GET" class="row align-items-end g-3 no-ajax">
                <input type="hidden" name="sort" value="{{ $sortBy }}">
                <input type="hidden" name="direction" value="{{ $direction }}">

                <div class="col-xl-3 col-md-6">
                    <label class="form-label small fw-bold text-light uppercase">Nomor SPP/SPM</label>
                    <div class="input-group search-group">
                        <span class="input-group-text border-0 bg-light"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="nomor" class="form-control border-0 bg-light rounded-end"
                            placeholder="Cari No. SPP atau SPM..." value="{{ request('nomor') }}">
                    </div>
                </div>

                <div class="col-xl-2 col-md-6">
                    <label class="form-label small fw-bold text-light uppercase">Filter Tanggal</label>
                    <input type="date" name="tanggal" class="form-control border-0 bg-light" value="{{ request('tanggal') }}">
                </div>

                <div class="col-xl-2 col-md-4">
                    <label class="form-label small fw-bold text-light uppercase">Jenis Permintaan</label>
                    <select name="jenis_pencairan" class="form-select border-0 bg-light">
                        <option value="">Semua Permintaan</option>
                        <option value="LS" {{ request('jenis_pencairan') == 'LS' ? 'selected' : '' }}>LS</option>
                        <option value="GU" {{ request('jenis_pencairan') == 'GU' ? 'selected' : '' }}>GU</option>
                        <option value="TU" {{ request('jenis_pencairan') == 'TU' ? 'selected' : '' }}>TU</option>
                        <option value="UP" {{ request('jenis_pencairan') == 'UP' ? 'selected' : '' }}>UP</option>
                        <option value="LS Nihil" {{ request('jenis_pencairan') == 'LS Nihil' ? 'selected' : '' }}>LS Nihil</option>
                        <option value="GU Nihil" {{ request('jenis_pencairan') == 'GU Nihil' ? 'selected' : '' }}>GU Nihil</option>
                        <option value="TU Nihil" {{ request('jenis_pencairan') == 'TU Nihil' ? 'selected' : '' }}>TU Nihil</option>
                    </select>
                </div>

                <div class="col-xl-3 col-md-6">
                    <label class="form-label small fw-bold text-light uppercase">Status Pengajuan</label>
                    <select name="status" class="form-select border-0 bg-light">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                        <option value="proses_verifikasi" {{ request('status') === 'proses_verifikasi' ? 'selected' : '' }}>Proses Verifikasi</option>
                        <option value="direvisi" {{ request('status') === 'direvisi' ? 'selected' : '' }}>Menunggu Verifikasi Ulang</option>
                        <option value="revisi" {{ request('status') === 'revisi' ? 'selected' : '' }}>Perlu Revisi</option>
                        <option value="validasi" {{ request('status') === 'validasi' ? 'selected' : '' }}>Proses Validasi</option>
                        <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                        <option value="reset" {{ request('status') === 'reset' ? 'selected' : '' }}>Reset</option>
                        <optgroup label="Status SP2D">
                            <option value="sp2d_needs_upload" {{ request('status') === 'sp2d_needs_upload' ? 'selected' : '' }}>Perlu Upload SP2D</option>
                            <option value="sp2d_revision" {{ request('status') === 'sp2d_revision' ? 'selected' : '' }}>Revisi SP2D</option>
                            <option value="sp2d_verification" {{ request('status') === 'sp2d_verification' ? 'selected' : '' }}>Menunggu Verifikasi SP2D</option>
                            <option value="sp2d_complete" {{ request('status') === 'sp2d_complete' ? 'selected' : '' }}>Lengkap SP2D</option>
                        </optgroup>
                    </select>
                </div>

                <div class="col-xl-2 col-md-6">
                    <div class="d-flex gap-2 flex-nowrap">
                        <button type="submit" class="btn btn-dark px-3 rounded-pill flex-grow-1 shadow-sm d-flex align-items-center justify-content-center">
                            <i class="fas fa-sliders-h me-2"></i>
                            <span>Filter</span>
                        </button>

                        <a href="{{ route('submissions.index') }}" class="btn btn-light px-3 rounded-pill border shadow-xs text-muted d-flex align-items-center justify-content-center reset-btn">
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
                        <tr class="text-nowrap">
                            <th class="ps-4 py-3">No</th>
                            <th class="py-3 text-center">
                                <a href="{{ route('submissions.index', array_merge(request()->all(), ['sort' => 'tanggal', 'direction' => $sortBy == 'tanggal' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-light">
                                    Tgl. Pengajuan
                                    @if($sortBy == 'tanggal')
                                        <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="py-3 text-center">
                                <a href="{{ route('submissions.index', array_merge(request()->all(), ['sort' => 'nomor_spp', 'direction' => $sortBy == 'nomor_spp' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-center text-light">
                                    No. SPP
                                    @if($sortBy == 'nomor_spp')
                                        <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-light opacity-50" style="font-size: 0.8rem;"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="py-3 text-center">
                                <a href="{{ route('submissions.index', array_merge(request()->all(), ['sort' => 'nomor_spm', 'direction' => $sortBy == 'nomor_spm' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-center text-light">
                                    No. SPM
                                    @if($sortBy == 'nomor_spm')
                                        <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-light opacity-50" style="font-size: 0.8rem;"></i>
                                    @endif
                                </a>
                            </th>

                            @if(auth()->user()->hasRole('super_admin'))
                                <th class="py-3 text-center">
                                    <a href="{{ route('submissions.index', array_merge(request()->all(), ['sort' => 'departments.nama_dinas', 'direction' => $sortBy == 'departments.nama_dinas' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-light">
                                        Instansi/Dinas
                                        @if($sortBy == 'departments.nama_dinas')
                                            <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                            @endif
                            <th class="py-3">
                                <a href="{{ route('submissions.index', array_merge(request()->all(), ['sort' => 'judul_kegiatan', 'direction' => $sortBy == 'judul_kegiatan' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-light">
                                    Kegiatan
                                    @if($sortBy == 'judul_kegiatan')
                                        <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="py-3">
                                <a href="{{ route('submissions.index', array_merge(request()->all(), ['sort' => 'judul_sub_kegiatan', 'direction' => $sortBy == 'judul_sub_kegiatan' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-light">
                                    Sub-Kegiatan
                                    @if($sortBy == 'judul_sub_kegiatan')
                                        <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="py-3">
                                <a href="{{ route('submissions.index', array_merge(request()->all(), ['sort' => 'perihal', 'direction' => $sortBy == 'perihal' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-light">
                                    Perihal
                                    @if($sortBy == 'perihal')
                                        <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="py-3 text-center">
                                <a href="{{ route('submissions.index', array_merge(request()->all(), ['sort' => 'total_pencairan', 'direction' => $sortBy == 'total_pencairan' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-light">
                                    Jumlah Permintaan
                                    @if($sortBy == 'total_pencairan')
                                        <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="py-3 text-center">
                                <a href="{{ route('submissions.index', array_merge(request()->all(), ['sort' => 'jenis_pencairan', 'direction' => $sortBy == 'jenis_pencairan' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-light">
                                    Jenis Belanja
                                    @if($sortBy == 'jenis_pencairan')
                                        <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th>
                                <a href="{{ route('submissions.index', array_merge(request()->all(), ['sort' => 'sumber_dana', 'direction' => $sortBy == 'sumber_dana' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-light">
                                    Sumber Dana
                                    @if($sortBy == 'sumber_dana')
                                        <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-center py-3">
                                <a href="{{ route('submissions.index', array_merge(request()->all(), ['sort' => 'status', 'direction' => $sortBy == 'status' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-light">
                                    Status
                                    @if($sortBy == 'status')
                                        <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-center pe-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $index => $s)
                        <tr>
                            <td class="ps-4 text-muted text-center">{{ $index + 1 }}</td>
                            <td class="text-center">{{ \Carbon\Carbon::parse($s->tanggal)->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <span class="badge text-dark fw-bold">
                                    {{ $s->nomor_spp }} - {{ $s->tanggal_spp ? \Carbon\Carbon::parse($s->tanggal_spp)->format('d/m/Y') : '-' }}
                                </span>

                            </td>
                                                        <td class="text-center">
                                <span class="badge text-dark fw-bold">
                                    {{ $s->nomor_spm }} - {{ $s->tanggal_spm ? \Carbon\Carbon::parse($s->tanggal_spp)->format('d/m/Y') : '-' }}
                                </span>

                            </td>

                            @if(auth()->user()->hasRole('super_admin'))
                                <td class="fw-bold text-center">{{ $s->department->nama_dinas ?? '-' }}</td>
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
                            <td class="fw-bold text-center">
                                <span class="text-dark fw-bold shadow-sm" title="{{ $s->jenis_pencairan }}">
                                    {{ str_contains($s->jenis_pencairan, 'Nihil')
                                        ? 'N. ' . Str::before($s->jenis_pencairan, ' Nihil')
                                        : $s->jenis_pencairan }}
                                </span>
                            </td>
                            <td class="fw-bold text-center">
                                <span class="text-dark fw-bold shadow-sm" title="{{ $s->sumber_dana }}">
                                    {{ $s->sumber_dana }}
                                </span>
                            </td>
                            <td class="text-center align-middle status-column-fixed">
                                @php
                                    $sp2dDoc = $s->documents->first(fn($d) => stripos($d->jenis_dokumen, 'sp2d') !== false);
                                    $sp2dStatus = $sp2dDoc ? $sp2dDoc->status_dokumen : null;
                                    $isGuNihil = strtoupper(trim($s->jenis_pencairan ?? '')) === 'GU NIHIL';
                                @endphp

                                @if($isGuNihil)
                                    <span class="text-success fw-bold text-nowrap">
                                        <i class="fas fa-check-circle me-1"></i> Lengkap
                                    </span>

                                @elseif($sp2dStatus == 'revisi')
                                    <span class="text-danger text-nowrap" data-bs-toggle="tooltip" title="Catatan: {{ $sp2dDoc->catatan ?? '-' }}">
                                        <i class="fas fa-exclamation-triangle me-1"></i> Revisi SP2D
                                    </span>

                                @elseif($sp2dStatus == 'uploaded' || $sp2dStatus == 'direvisi')
                                    <span class="text-warning text-nowrap">
                                        <i class="fas fa-spinner fa-spin me-1"></i> Verifikasi SP2D
                                    </span>

                                @elseif($sp2dStatus == 'verified' || $sp2dStatus == 'approved')
                                    <span class="text-success fw-bold text-nowrap">
                                        <i class="fas fa-check-circle me-1"></i> Lengkap
                                    </span>

                                @elseif($s->status == 'pending')
                                    @php $approvedCount = $s->documents->where('status_dokumen', 'approved')->count(); @endphp
                                    @if($approvedCount == 6 && is_null($s->sent_to_kabid_at))
                                        <span class="text-info text-nowrap"><i class="fas fa-check-double me-1"></i> Validasi</span>
                                    @else
                                        <span class="text-warning text-nowrap"><i class="fas fa-spinner fa-spin me-1"></i> Verifikasi</span>
                                    @endif
                                @elseif($s->status == 'rejected' || $s->status == 'revisi' || $s->status == 'direvisi')
                                    <span class="text-danger text-nowrap"><i class="fas fa-sync-alt fa-spin me-1"></i> Revisi</span>
                                @elseif($s->status == 'proses validasi')
                                    <span class="text-primary text-nowrap"><i class="fas fa-sync-alt fa-spin me-1"></i> Validasi</span>
                                @elseif($s->status == 'disetujui')
                                    <div class="animate-blink-wrapper">
                                        <span class="status-toggle text-success fw-bold text-nowrap status-text-transition" data-state="disetujui">
                                            <i class="fas fa-check-circle me-1"></i> Disetujui
                                        </span>
                                    </div>
                                @elseif($s->status == 'expired')
                                    <span class="text-danger text-nowrap"><i class="fa-solid fa-repeat"></i></i> Expired</span>
                                @elseif($s->status == 'reset')
                                    <span class="text-danger text-nowrap"><i class="fas fa-clock-rotate-left me-1"></i> Reset</span>
                                @endif
                            </td>
                           <td class="text-center align-middle pe-4">
                                <div class="d-flex justify-content-center align-items-center gap-2">

                                    <a href="{{ route('submissions.show', $s->id) }}"
                                    class="btn btn-sm btn-outline-primary rounded-circle p-0 d-flex align-items-center justify-content-center shadow-sm"
                                    style="width: 32px; height: 32px;"
                                    title="Detail">
                                        <i class="fas fa-eye" style="font-size: 0.8rem;"></i>
                                    </a>

                                    @if($s->status == 'revisi' || $s->status == 'reset' || $s->status == 'rejected')
                                        <button type="button"
                                                class="btn btn-sm btn-outline-warning rounded-circle p-0 d-flex align-items-center justify-content-center shadow-sm"
                                                style="width: 32px; height: 32px;"
                                                data-bs-toggle="modal" data-bs-target="#modalRevisi{{ $s->id }}"
                                                title="Perbaiki Berkas">
                                            <i class="fas fa-edit" style="font-size: 0.8rem;"></i>
                                        </button>
                                    @endif

                                    @php
                                        $sp2dDoc = $s->documents->first(fn($d) => stripos($d->jenis_dokumen, 'sp2d') !== false);
                                        $sp2dStatus = $sp2dDoc ? $sp2dDoc->status_dokumen : null;
                                        $statusLower = strtolower($s->status);
                                    @endphp

                                    @if(!auth()->user()->hasRole('operator') && $s->status == 'disetujui' && strtoupper(trim($s->jenis_pencairan ?? '')) !== 'GU NIHIL' && (is_null($sp2dDoc) || in_array($sp2dStatus, ['revisi'])))
                                        <button type="button"
                                                class="btn btn-sm btn-outline-success rounded-circle p-0 d-flex align-items-center justify-content-center shadow-sm"
                                                style="width: 32px; height: 32px;"
                                                data-bs-toggle="modal" data-bs-target="#modalUploadSp2d{{ $s->id }}"
                                                title="{{ $sp2dDoc ? 'Perbaiki Dokumen SP2D' : 'Upload SP2D' }}" data-bs-toggle="tooltip">
                                            <i class="fa-solid fa-cloud-arrow-up" style="font-size: 0.8rem;"></i>
                                        </button>
                                    @endif

                                    @php
                                        $statusLower = strtolower($s->status);
                                        $isOperator = auth()->user()->hasRole('operator');
                                        $isSuperAdmin = auth()->user()->hasRole('super_admin');

                                        $canDelete = $isSuperAdmin || ($isOperator && in_array($statusLower, ['pending', 'expired']));
                                    @endphp

                                    @if($canDelete)
                                        <form id="delete-form-{{ $s->id }}" action="{{ route('submissions.destroy', $s->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger rounded-circle p-0 d-flex align-items-center justify-content-center shadow-sm"
                                                    style="width: 32px; height: 32px;"
                                                    title="Hapus Pengajuan"
                                                    onclick="confirmDelete({{ $s->id }})">
                                                <i class="fas fa-trash-alt" style="font-size: 0.8rem;"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ auth()->user()->hasRole('super_admin') ? '13' : '12' }}" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 text-muted"></i>
                                <h6 class="fw-light">Belum ada pengajuan yang diajukan</h6>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($submissions->hasPages())
            <div class="my-3 d-flex justify-content-center">
                <div class="glass-pagination bg-opacity-75 px-4 py-1 rounded-pill shadow-sm ">
                    {{ $submissions->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>

@foreach($submissions as $s)

    @if($s->status == 'revisi' || $s->status == 'reset' || $s->status == 'rejected')
        {{-- Modal: Perbaikan data dan dokumen pengajuan --}}
        <div class="modal fade modal-glass-dark" id="modalRevisi{{ $s->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content glass-card border-0">
                    <div class="modal-header border-bottom border-secondary-subtle py-3 px-4">
                        <div class="d-flex align-items-center">
                            <div class="glass-icon-red me-3">
                                <i class="fas fa-tools shadow-sm"></i>
                            </div>
                            <div class="text-start">
                                <h5 class="modal-title fw-bold text-white mb-0">Revisi Data & Dokumen</h5>
                                <p class="text-light opacity-50 small mb-0">Lengkapi data dan unggah berkas baru.</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white opacity-50" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                  <form action="{{ route('submissions.update', $s->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
                        <div class="modal-body p-4 text-start">

                            <div class="col-md-12 mb-4">
                                <div class="alert alert-danger border border-danger-subtle text-light rounded-4 p-3 d-flex align-items-center h-100 mb-0 bg-danger bg-opacity-10">
                                    <i class="fas fa-exclamation-triangle text-danger fa-2x me-3"></i>
                                    <div class="w-100">
                                        <small class="text-danger fw-bold opacity-75 d-block text-uppercase">Catatan Verifikator / Penolakan:</small>
                                        <div class="glass-alert p-2 rounded-3 mt-2">
                                            <p class="text-light opacity-75 small mb-0" style="font-size: 0.75rem;">
                                                {{ $s->catatan_kabid ?? 'Silakan periksa kembali dokumen atau data utama yang perlu diperbaiki.' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="glass-inner-card p-3 mb-4 rounded-4 border border-secondary-subtle">
                                <h6 class="fw-bold text-info mb-3 small text-uppercase tracking-wider">
                                    <i class="fas fa-fingerprint me-2"></i>Verifikasi Data Utama
                                </h6>
                                <div class="row g-3">

                                    <div class="col-md-6">
                                        <label class="form-label mini-label text-light opacity-75">NOMOR SPP</label>
                                        <input type="text" name="nomor_spp" class="form-control glass-input text-white"
                                               value="{{ $s->nomor_spp }}" required maxlength="6">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label mini-label text-light opacity-75">NOMOR SPM</label>
                                        <input type="text" name="nomor_spm" class="form-control glass-input text-white"
                                               value="{{ $s->nomor_spm }}" required maxlength="6">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label mini-label text-light opacity-75">TANGGAL SPP</label>
                                        <input type="date" name="tanggal_spp" class="form-control glass-input text-white"
                                               value="{{ $s->tanggal_spp }}" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label mini-label text-light opacity-75">TANGGAL SPM</label>
                                        <input type="date" name="tanggal_spm" class="form-control glass-input text-white"
                                               value="{{ $s->tanggal_spm }}" required>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label mini-label text-light opacity-75">TOTAL PERMINTAAN</label>
                                        <div class="input-group glass-input-group">
                                            <span class="input-group-text bg-transparent border-0 text-info fw-bold">Rp</span>
                                            <input type="text" id="total_pencairan_input" class="form-control bg-transparent border-0 text-white fw-bold input-rupiah"
                                                   value="{{ number_format($s->total_pencairan, 0, ',', '.') }}" required>
                                            <input type="hidden" name="total_pencairan" id="total_pencairan_raw" value="{{ $s->total_pencairan }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h6 class="fw-bold text-light opacity-75 mb-3 small text-uppercase tracking-wider">
                                <i class="fas fa-file-upload me-2"></i>Berkas Perbaikan
                            </h6>

                            <div class="list-container">
                                <div class="row g-3">
                                    @foreach($s->documents->filter(function($doc) use ($s) {
                                        return in_array($doc->status_dokumen, ['revisi', 'rejected']) ||
                                            in_array($s->status, ['reset', 'rejected']);
                                    }) as $doc)
                                        <div class="col-md-4">
                                            <div class="glass-item p-3 rounded-4 border border-secondary-subtle h-100 d-flex flex-column justify-content-between bg-dark text-light" x-data="{ fileName: '' }">
                                                <div>

                                                    <div class="d-flex align-items-center mb-2">
                                                        <span class="text-info me-2 fs-5"><i class="fas fa-file-pdf"></i></span>
                                                        <h6 class="text-white small mb-0 fw-bold">
                                                            {{ strtoupper($doc->jenis_dokumen) }} <span class="text-danger">*</span>
                                                        </h6>
                                                    </div>

                                                    <div class="glass-alert p-2 rounded-3 mb-3 bg-warning bg-opacity-10 border border-warning border-opacity-25">
                                                        <small class="text-warning d-block fw-bold" style="font-size: 0.65rem;">Keterangan :</small>
                                                        <p class="text-light opacity-75 small mb-0" style="font-size: 0.75rem;">{{ $doc->catatan }}</p>
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="btn btn-outline-light btn-sm w-100 mb-0 text-truncate rounded-pill py-2" style="cursor: pointer; font-size: 0.75rem;">
                                                        <i class="fas fa-upload me-1"></i>
                                                        <span x-text="fileName ? fileName : 'Pilih File PDF'">Pilih File PDF</span>
                                                        <input type="file" name="files[{{ $doc->id }}]"
                                                            class="d-none"
                                                            accept=".pdf"
                                                            @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''">
                                                    </label>

                                                    <div class="d-flex justify-content-between mt-2 px-1">
                                                        <span class="text-light opacity-25" style="font-size: 0.55rem;">Maks {{ $systemSettings['submission.document_max_size_mb'] ?? 10 }} MB</span>
                                                        <span class="text-info fw-bold" style="font-size: 0.55rem;">* Opsional / Revisi</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pb-4 px-4">
                            <button type="button" class="btn btn-glass-dark text-light rounded-pill text-decoration-none" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-glass-primary px-4 py-2 rounded-pill fw-bold">
                                <i class="fas fa-paper-plane me-2"></i>Kirim Perbaikan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if(!auth()->user()->hasRole('operator') && $s->status == 'disetujui' && strtoupper(trim($s->jenis_pencairan ?? '')) !== 'GU NIHIL')
        {{-- Modal: Unggah dokumen SP2D --}}
        <div class="modal fade" id="modalUploadSp2d{{ $s->id }}" tabindex="-1" aria-labelledby="modalUploadSp2dLabel{{ $s->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="{{ route('submissions.uploadSp2d', $s->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalUploadSp2dLabel{{ $s->id }}">
                                <i class="fas fa-file-pdf text-success me-2"></i> Upload Dokumen SP2D
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-start">
                            <p class="text-muted small mb-3">
                                Silakan unggah file PDF dokumen SP2D dengan No.SPM: <strong>{{ $s->nomor_spm }}</strong>
                            </p>

                            <div class="mb-3">
                                <label for="sp2d_file_{{ $s->id }}" class="form-label fw-bold">File SP2D (PDF, maks. {{ $systemSettings['submission.sp2d_max_size_mb'] ?? 2 }} MB)</label>
                                <input type="file" class="form-control" id="sp2d_file_{{ $s->id }}" name="sp2d_file" accept=".pdf" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fas fa-upload me-1"></i> Simpan SP2D
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach

{{-- Modal: Pratinjau PDF dokumen --}}
<div class="modal fade" id="modalPreviewPDF" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title small fw-bold"><i class="fas fa-file-pdf me-2"></i>Preview Dokumen Lama</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="pdfFrame" src="" width="100%" height="600px" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.input-rupiah').forEach(function(input) {
    input.addEventListener('keyup', function(e) {
        let val = this.value.replace(/\D/g, '');

        const hiddenInput = this.parentElement.querySelector('input[type="hidden"]');
        if(hiddenInput) {
            hiddenInput.value = val;
        }

        this.value = formatRupiah(val);
    });
});

// Function: Memformat nominal ke rupiah
function formatRupiah(angka) {
    let number_string = angka.toString(),
        sisa = number_string.length % 3,
        rupiah = number_string.substr(0, sisa),
        ribuan = number_string.substr(sisa).match(/\d{3}/g);

    if (ribuan) {
        let separator = sisa ? '.' : '';
        rupiah += separator + ribuan.join('.');
    }
    return rupiah;
}
</script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        const modalPreview = document.getElementById('modalPreviewPDF');
        modalPreview.addEventListener('show.bs.modal', function () {
            setTimeout(() => {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                if (backdrops.length > 1) {
                    backdrops[backdrops.length - 1].style.zIndex = '1065';
                    modalPreview.style.zIndex = '1070';
                }
            }, 10);
        });

        modalPreview.addEventListener('hidden.bs.modal', function () {
            document.getElementById('pdfFrame').src = '';

            const openModals = document.querySelectorAll('.modal.show');
            if (openModals.length > 0) {
                document.body.classList.add('modal-open');
                document.body.style.overflow = 'hidden';
                document.body.style.paddingRight = '0px';

                openModals[0].focus();
            }
        });
    });

    // Function: Membuka pratinjau PDF pengajuan
    function previewPDF(url) {
        document.getElementById('pdfFrame').src = url + '#toolbar=0';
        var myModal = new bootstrap.Modal(document.getElementById('modalPreviewPDF'));
        myModal.show();
    }
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        setInterval(function () {
            const toggleElements = document.querySelectorAll('.status-toggle');

            toggleElements.forEach(function (el) {
                el.classList.add('fade-out');

                setTimeout(function () {
                    let currentState = el.getAttribute('data-state');

                    if (currentState === 'disetujui') {
                        el.setAttribute('data-state', 'upload');
                        el.innerHTML = '<i class="fas fa-file-upload me-1"></i> Upload SP2D';
                    } else {
                        el.setAttribute('data-state', 'disetujui');
                        el.innerHTML = '<i class="fas fa-check-circle me-1"></i> Disetujui';
                    }

                    el.classList.remove('fade-out');
                }, 300);
            });
        }, 7000);
    });
</script>
<script>
    // Function: Meminta konfirmasi penghapusan pengajuan
    function confirmDelete(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Data pengajuan beserta berkas terkait akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>



<style>
    /* Mengunci lebar kolom status agar tabel stabil */
    .status-column-fixed {
        width: 160px;
        min-width: 160px;
        max-width: 160px;
        white-space: nowrap;
    }

    /* Animasi kedip hanya berlaku pada pembungkus luar (stabil) */
    @keyframes blinkEffect {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.3; }
    }

    .animate-blink-wrapper {
        animation: blinkEffect 2s infinite ease-in-out;
        display: inline-block;
    }

    /* Efek transisi halus khusus untuk teks di dalamnya */
    .status-text-transition {
        transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
        opacity: 1;
        display: inline-block;
    }

    .status-text-transition.fade-out {
        opacity: 0;
        transform: translateY(3px); /* Sedikit geser ke bawah saat memudar */
    }
</style>

@endsection
