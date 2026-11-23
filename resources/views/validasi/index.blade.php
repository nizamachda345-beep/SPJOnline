{{-- View: validasi/index --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3 glass-card-dark position-relative">
        <div class="glass-reflection"></div>
        <div class="card-body p-3">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <div class="d-flex align-items-center">

                        <div class="icon-box bg-primary bg-opacity-10 p-3 rounded-3 me-3">
                            <i class="fa-solid fa-file-circle-check fa-lg text-primary"></i>
                        </div>
                        <div>

                            <h4 class="fw-black text-white mb-1 tracking-tight" style="letter-spacing: -0.5px;">Validasi Permintaan</h4>

                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0 x-small">
                                    <li class="breadcrumb-item text-light opacity-50">
                                        <i class="fas fa-layer-group me-1 text-primary opacity-75"></i> Panel Kabid Perbendaharaan
                                    </li>
                                    <li class="breadcrumb-item active fw-semibold text-primary" aria-current="page">
                                        Daftar Pengajuan
                                    </li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 text-md-end mt-3 mt-md-0">
                    <div class="d-inline-flex align-items-center bg-white-50 px-3 py-2 rounded-pill shadow-sm" style="backdrop-filter: blur(10px);">
                        <div class="me-3 text-center">
                            <p class="text-muted mb-0 extra-small text-uppercase fw-bold">Total Dokumen</p>
                            <p class="fw-black text-dark h6 mb-0">{{ $submissions->total() }} <span class="small fw-sm text-muted">Berkas</span></p>
                        </div>
                        <div class="bg-primary-soft text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px; background-color: rgba(13, 110, 253, 0.12);">
                            <i class="fas fa-check-double" style="font-size: 0.85rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card glass-card-dark border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body p-3">
            <form action="{{ route('validasi.index') }}" method="GET" class="row g-2 align-items-center no-ajax">

                <div class="col-lg-4">
                    <div class="input-group bg-light rounded-pill px-3 py-1 border">
                        <span class="input-group-text bg-transparent border-0 text-muted">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control bg-transparent border-0 shadow-none extra-small"
                               placeholder="Cari Nomor SPP/SPM atau SKPD..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-lg-3">
                    <select name="department_id" class="form-select bg-light rounded-pill border shadow-none extra-small">
                        <option value="">Semua SKPD</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->nama_dinas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-lg-3">
                    <select name="jenis" class="form-select bg-light rounded-pill border shadow-none extra-small">
                        <option value="">Semua Jenis</option>
                        <option value="LS" {{ request('jenis') == 'LS' ? 'selected' : '' }}>LS</option>
                        <option value="GU" {{ request('jenis') == 'GU' ? 'selected' : '' }}>GU</option>
                        <option value="TU" {{ request('jenis') == 'TU' ? 'selected' : '' }}>TU</option>
                        <option value="UP" {{ request('jenis') == 'UP' ? 'selected' : '' }}>UP</option>
                        <option value="LS Nihil" {{ request('jenis') == 'LS Nihil' ? 'selected' : '' }}>LS Nihil</option>
                        <option value="GU Nihil" {{ request('jenis') == 'GU Nihil' ? 'selected' : '' }}>GU Nihil</option>
                        <option value="TU Nihil" {{ request('jenis') == 'TU Nihil' ? 'selected' : '' }}>TU Nihil</option>
                    </select>
                </div>

                <div class="col-lg-2 d-flex gap-2">
                    <button type="submit" class="btn btn-dark rounded-pill w-100 fw-bold extra-small">
                        Filter
                    </button>
                    <a href="{{ route('validasi.index') }}" class="btn btn-outline-secondary rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;" title="Reset Filter">
                        <i class="fas fa-sync-alt"></i>
                    </a>
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

                            <th class="py-3 text-center">
                                <a href="{{ route('validasi.index', array_merge(request()->all(), ['sort' => 'tanggal', 'direction' => $sortBy == 'tanggal' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-light">
                                    Tgl. Pengajuan
                                    @if($sortBy == 'tanggal')
                                        <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>

                            <th class="py-3 text-center">
                                <a href="{{ route('validasi.index', array_merge(request()->all(), ['sort' => 'nomor_spp', 'direction' => $sortBy == 'nomor_spp' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-center text-light text-decoration-none">
                                    No. SPP
                                    @if($sortBy == 'nomor_spp')
                                        <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-light opacity-50" style="font-size: 0.8rem;"></i>
                                    @endif
                                </a>
                            </th>

                            <th class="py-3 text-center">
                                <a href="{{ route('validasi.index', array_merge(request()->all(), ['sort' => 'nomor_spm', 'direction' => $sortBy == 'nomor_spm' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-center text-light text-decoration-none">
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
                                    <a href="{{ route('validasi.index', array_merge(request()->all(), ['sort' => 'departments.nama_dinas', 'direction' => $sortBy == 'departments.nama_dinas' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-light">
                                        Instansi/Dinas
                                        @if($sortBy == 'departments.nama_dinas')
                                            <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                            @endif

                            <th class="py-3">
                                <a href="{{ route('validasi.index', array_merge(request()->all(), ['sort' => 'judul_kegiatan', 'direction' => $sortBy == 'judul_kegiatan' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-light">
                                    Kegiatan
                                    @if($sortBy == 'judul_kegiatan')
                                        <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>

                            <th class="py-3">
                                <a href="{{ route('validasi.index', array_merge(request()->all(), ['sort' => 'judul_sub_kegiatan', 'direction' => $sortBy == 'judul_sub_kegiatan' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-light">
                                    Sub-Kegiatan
                                    @if($sortBy == 'judul_sub_kegiatan')
                                        <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>

                            <th class="py-3">
                                <a href="{{ route('validasi.index', array_merge(request()->all(), ['sort' => 'perihal', 'direction' => $sortBy == 'perihal' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-light">
                                    Perihal
                                    @if($sortBy == 'perihal')
                                        <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>

                            <th class="py-3 text-center">
                                <a href="{{ route('validasi.index', array_merge(request()->all(), ['sort' => 'total_pencairan', 'direction' => $sortBy == 'total_pencairan' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-light text-decoration-none">
                                    Jumlah Permintaan
                                    @if($sortBy == 'total_pencairan')
                                        <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>

                            <th class="py-3 text-center">
                                <a href="{{ route('validasi.index', array_merge(request()->all(), ['sort' => 'jenis_pencairan', 'direction' => $sortBy == 'jenis_pencairan' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-light">
                                    Jenis Belanja
                                    @if($sortBy == 'jenis_pencairan')
                                        <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>

                            <th class="py-3">
                                <a href="{{ route('validasi.index', array_merge(request()->all(), ['sort' => 'sumber_dana', 'direction' => $sortBy == 'sumber_dana' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-light">
                                    Sumber Dana
                                    @if($sortBy == 'sumber_dana')
                                        <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>

                            <th class="text-center py-3">
                                <a href="{{ route('validasi.index', array_merge(request()->all(), ['sort' => 'status', 'direction' => $sortBy == 'status' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-light text-decoration-none">
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
                        <tr class="align-middle text-nowrap">
                            <td class="ps-4 text-muted text-center">{{ $submissions->firstItem() + $index }}</td>
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

                            <td data-bs-toggle="tooltip" title="{{ $s->sumber_dana }}" class="fw-bold text-center">
                                <span class="text-dark fw-bold shadow-sm" title="{{ $s->sumber_dana }}">
                                    {{ $s->sumber_dana }}
                                </span>
                            </td>

                            <td class="text-center">
                                @if($s->status == 'pending')
                                    @php $approvedCount = $s->documents->where('status_dokumen', 'approved')->count(); @endphp
                                    @if($approvedCount == 6 && is_null($s->sent_to_kabid_at))
                                        <span class="text-info"><i class="fas fa-check-double me-1"></i> Validasi</span>
                                    @else
                                        <span class="text-warning"><i class="fas fa-spinner fa-spin me-1"></i> Verifikasi</span>
                                    @endif
                                @elseif($s->status == 'rejected' || $s->status == 'revisi' || $s->status == 'direvisi')
                                    <span class="text-danger"><i class="fas fa-sync-alt fa-spin me-1"></i> Revisi</span>
                                @elseif($s->status == 'proses validasi')
                                    <span class="text-primary"><i class="fas fa-sync-alt fa-spin me-1"></i> Validasi</span>
                                @elseif($s->status == 'disetujui')
                                    <span class="text-success"><i class="fas fa-check-circle me-1"></i> Disahkan</span>
                                @elseif($s->status == 'reset')
                                    <span class="text-danger"><i class="fas fa-clock-rotate-left me-1"></i> Reset</span>
                                @endif
                            </td>

                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center align-items-center gap-2">

                                    <a href="{{ route('validasi.show', $s->id) }}" class="btn btn-sm btn-outline-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;" title="Lihat Detail Validasi" data-bs-toggle="tooltip">
                                        <i class="fas fa-eye" style="font-size: 0.8rem;"></i>
                                    </a>

                                    @if($s->status != 'disetujui')
                                        <form action="{{ route('validasi.sahkan', $s->id) }}" method="POST" id="form-sahkan-{{ $s->id }}" class="no-ajax m-0">
                                            @csrf
                                            <button type="button"
                                                    onclick="konfirmasiPengesahan('{{ $s->id }}')"
                                                    class="btn btn-sm btn-success rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                                    style="width: 32px; height: 32px;"
                                                    title="Sahkan Berkas"
                                                    data-bs-toggle="tooltip">
                                                <i class="fas fa-check-double" style="font-size: 0.8rem;"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ auth()->user()->hasRole('super_admin') ? '14' : '13' }}" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 text-muted"></i>
                                <h6 class="fw-light">Tidak ada pengajuan yang perlu divalidasi</h6>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($submissions->hasPages())
            <div class="my-3 d-flex justify-content-center">
                <div class="glass-pagination bg-opacity-75 px-4 py-1 rounded-pill shadow-sm">
                    {{ $submissions->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>

<script>
// Function: Meminta konfirmasi pengesahan pengajuan
function konfirmasiPengesahan(id) {
    Swal.fire({
        title: 'Sahkan Berkas Final?',
        text: "Tindakan ini akan mengunci dokumen secara permanen dalam sistem.",
        icon: 'warning',
        iconColor: '#198754',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-check-double me-2"></i> Ya, Sahkan',
        cancelButtonText: 'Batal',
        reverseButtons: true,

        background: 'rgba(15, 17, 26, 0.95)',
        backdrop: `rgba(15, 17, 26, 0.6) blur(4px)`,
        color: '#f8f9fa',

        customClass: {
            popup: 'border border-white border-opacity-10 rounded-4 shadow-lg',
            title: 'fw-bold text-white tracking-tight pt-3',
            htmlContainer: 'text-white-50 small leading-sm px-3',
            confirmButton: 'btn btn-success rounded-pill px-4 py-2 mx-2 text-sm',
            cancelButton: 'btn btn-glass-action rounded-pill px-4 py-2 mx-2 text-sm text-light'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-sahkan-' + id).submit();
        }
    });
}
</script>
@endsection
