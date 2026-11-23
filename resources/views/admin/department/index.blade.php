{{-- View: admin/department/index --}}
@extends('layouts.app')

@section('content')

<style>
    .ba-month {
        width: 27px;
        height: 27px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        border: 1px solid rgba(255,255,255,.22);
        color: #fff;
        font-size: .7rem;
        font-weight: 700;
        text-decoration: none;
        transition: transform .15s ease, box-shadow .15s ease;
    }
    .ba-month:hover { color: #fff; transform: translateY(-1px); box-shadow: 0 0 0 2px rgba(255,255,255,.12); }
    .ba-month.month-verified { background: rgba(25,135,84,.8); border-color: #198754; }
    .ba-month.month-pending { background: rgba(255,193,7,.82); border-color: #ffc107; color: #212529; }
    .ba-month.month-rejected { background: rgba(220,53,69,.78); border-color: #dc3545; }
    .ba-month.month-empty { background: rgba(148,163,184,.12); border-color: rgba(148,163,184,.24); color: #94a3b8; }
    .ba-month.is-selected { outline: 2px solid #60a5fa; outline-offset: 2px; }
    .ba-month-grid {
        display: grid;
        grid-template-columns: repeat(6, 27px);
        justify-content: center;
        gap: 5px 4px;
    }
    .ba-month-legend {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: .45rem 1rem;
        padding: .7rem 1rem;
        border-bottom: 1px solid rgba(255,255,255,.08);
        color: rgba(226,232,240,.78);
        font-size: .72rem;
    }
    .ba-month-legend-item { display: inline-flex; align-items: center; gap: .4rem; }
    .ba-month-legend-dot { width: 10px; height: 10px; border-radius: 50%; border: 1px solid transparent; }
    .ba-month-legend-dot.month-verified { background: #198754; border-color: #198754; }
    .ba-month-legend-dot.month-pending { background: #ffc107; border-color: #ffc107; }
    .ba-month-legend-dot.month-rejected { background: #dc3545; border-color: #dc3545; }
    .ba-month-legend-dot.month-empty { background: rgba(148,163,184,.2); border-color: rgba(148,163,184,.45); }
    .ba-month-legend-dot.month-selected { background: transparent; border: 2px solid #60a5fa; }
    .badge.ba-status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .3rem;
        width: fit-content;
        max-width: 100%;
        min-width: 0;
        min-height: 28px;
        margin: 0;
        padding: .32rem .55rem;
        border-radius: 999px;
        background-color: rgba(15,23,42,.88) !important;
        font-size: .64rem;
        font-weight: 700;
        line-height: 1.2;
        text-align: center;
        box-sizing: border-box;
        white-space: nowrap;
    }
    .badge.ba-status-badge.bg-success { color: #4ade80 !important; border-color: rgba(74,222,128,.4) !important; }
    .badge.ba-status-badge.bg-warning { color: #fbbf24 !important; border-color: rgba(251,191,36,.4) !important; }
    .badge.ba-status-badge.bg-danger { color: #fb7185 !important; border-color: rgba(251,113,133,.4) !important; }
    .badge.ba-status-badge.bg-info { color: #67e8f9 !important; border-color: rgba(103,232,249,.4) !important; }
    .badge.ba-status-badge.bg-secondary { color: #cbd5e1 !important; border-color: rgba(203,213,225,.3) !important; }
    .ba-access-status { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: .12rem; max-width: 180px; margin: 0 auto; line-height: 1.2; }
    .ba-access-status-label { font-size: .67rem; font-weight: 700; text-transform: uppercase; }
    .ba-status-byline { display: block; font-size: .62rem; font-weight: 500; line-height: 1.1; text-transform: none; }
    .ba-status-detail { display: block; font-size: .58rem; font-weight: 700; line-height: 1.1; letter-spacing: .03em; text-transform: uppercase; }
</style>

@php
    $namaBulanBa = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
@endphp

<div class="container-fluid px-4">

    <div class="d-flex justify-content-between align-items-center glass-header-dark shadow-sm rounded-4 p-4 mb-3">
        <div class="d-flex align-items-center">
            <div class="me-3 text-light">
                <i class="fas fa-building-user fa-3x text-primary"></i>
            </div>
            <div>
                @role('super_admin')
                <h3 class="fw-bold text-light mb-1">Manajemen SKPD</h3>
                <p class="text-light mb-0">Kelola SKPD / Dinas dan Operator.</p>
                @endrole
                @role('kabid_perbendaharaan|kasie_bud')
                <h3 class="fw-bold text-light mb-1">Monitoring Validasi BA Rekon</h3>
                <p class="text-light mb-0">Pantau status validasi Berita Acara Rekonsiliasi dari masing-masing SKPD / Dinas.</p>
                @endrole
            </div>
        </div>
        @role('super_admin')
        <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center">
            <button class="btn btn-glass-primary rounded-pill px-4 py-2 text-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahDinas">
                <i class="fas fa-plus-circle me-2"></i> Tambah Dinas
            </button>
        </div>
        @endrole
    </div>

<div class="glass-card-dark border-0 shadow-sm rounded-4 mb-3">
    <div class="card-body p-4">
        <form action="{{ route('departments.index') }}" method="GET" class="row align-items-end g-3 no-ajax">

            @if(isset($sortBy))
                <input type="hidden" name="sort" value="{{ $sortBy }}">
                <input type="hidden" name="direction" value="{{ $direction }}">
            @endif

            @hasanyrole('kabid_perbendaharaan|kasie_bud')
            <div class="col-md-3">
                <label for="filter-tahun-ba-rekon" class="form-label small fw-bold text-light uppercase">Tahun</label>
                <select id="filter-tahun-ba-rekon" name="tahun" class="form-select bg-light border-0">
                    @foreach($tahunOptions as $year)
                        <option value="{{ $year }}" @selected($tahunDipilih === $year)>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
            @else
            <div class="col-md-9">
            @endhasanyrole
                <label class="form-label small fw-bold text-light uppercase">Pencarian SKPD / Dinas</label>
                <div class="input-group search-group">
                    <span class="input-group-text border-0 bg-light"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-0 bg-light rounded-end"
                        placeholder="Cari berdasarkan Nama Dinas, SKPD, atau Kode Dinas..." value="{{ request('search') }}">
                </div>
            </div>

            <div class="col-md-3">
                <div class="d-flex gap-2 flex-nowrap">
                    <button type="submit" class="btn btn-dark px-3 rounded-pill flex-grow-1 shadow-sm d-flex align-items-center justify-content-center">
                        <i class="fas fa-search me-2"></i>
                        <span>Cari</span>
                    </button>

                    <a href="{{ route('departments.index') }}" class="btn btn-light px-3 rounded-pill border shadow-xs text-muted d-flex align-items-center justify-content-center reset-btn" title="Reset Pencarian">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

    <div class="row g-4">

        @role('super_admin')
        <div class="col-lg-12">
        @endrole
        @hasanyrole('kabid_perbendaharaan|kasie_bud')
        <div class="col-lg-12">
        @endhasanyrole
            <div class="card glass-card-dark overflow-hidden shadow-lg" id="monitoring-container">
                <div class="card-header bg-transparent border-bottom border-white border-opacity-10 py-3 d-flex align-items-center justify-content-between gap-3">
                    <h6 class="m-0 fw-bold text-white">
                        <i class="fas fa-table me-2 text-gradient-indigo"></i>Daftar Instansi / OPD
                    </h6>
                    <span class="badge rounded-pill bg-white bg-opacity-10 border border-white border-opacity-10 text-light px-3 py-2">
                        {{ $departments->total() }} SKPD
                    </span>
                </div>
                <div class="card-body p-0">
                    @hasanyrole('kabid_perbendaharaan|kasie_bud')
                    <div class="ba-month-legend" aria-label="Keterangan warna status BA Rekon">
                        <span class="ba-month-legend-item"><span class="ba-month-legend-dot month-verified"></span>Terverifikasi</span>
                        <span class="ba-month-legend-item"><span class="ba-month-legend-dot month-pending"></span>Menunggu</span>
                        <span class="ba-month-legend-item"><span class="ba-month-legend-dot month-rejected"></span>Ditolak</span>
                        <span class="ba-month-legend-item"><span class="ba-month-legend-dot month-empty"></span>Belum upload</span>
                        <span class="ba-month-legend-item"><span class="ba-month-legend-dot month-selected"></span>Bulan dipilih</span>
                    </div>
                    @endhasanyrole
                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle mb-0">
<thead>
    <tr>
        <th class="ps-4 py-3">Nama Dinas</th>

        @role('super_admin')
        <th class="py-3 text-center">Kode</th>
        <th class="py-3 text-center pe-4" style="width: 130px;">Aksi</th>
        @endrole

        @hasanyrole('kabid_perbendaharaan|kasie_bud')
        <th class="py-3 text-center">Kode</th>
        <th class="py-3 text-center">Bulan</th>
        <th class="py-3 text-center">Status BA Rekon</th>
        <th class="py-3 text-center">Status Akses</th>
        <th class="py-3 text-center pe-4" style="width: 150px;">Aksi</th>
        @endhasanyrole
    </tr>
</thead>
                            <tbody>
@forelse($departments as $dept)
    @php
        $baRekon = $dept->baRekons
            ->where('tahun', $tahunDipilih)
            ->firstWhere('bulan', $bulanDipilih);
        $aksesOnly = !$baRekon || $baRekon->status === 'verified';
        $baRekonBulanIni = $dept->baRekons->first(
            fn ($item) => (int) $item->tahun === $waktuSekarang->year
                && (int) $item->bulan === $waktuSekarang->month
        );
        $aksesManual = $dept->akses_pengajuan_manual;
        $submissionAvailability = app(\App\Services\SystemSettingsService::class)
            ->submissionAvailability($dept, $waktuSekarang, $baRekonBulanIni?->status, true);
        [$aksesLabel, $aksesClass] = match ($submissionAvailability['reason']) {
            'settings_unavailable' => ['Tidak Dapat Memeriksa', 'secondary'],
            'department_closed' => ['Dikunci Manual', 'danger'],
            'schedule_closed' => ['Ditutup Global', 'danger'],
            'schedule_time' => ['Terkunci - Jadwal', 'warning'],
            'ba_rekon' => ['Terkunci', 'warning'],
            default => [$aksesManual === true ? 'Dibuka Manual' : 'Terbuka', 'success'],
        };
    @endphp
    <tr>
        <td class="ps-4">
            <div class="d-flex align-items-center">
                <div class="rounded-3 text-center d-flex align-items-center justify-content-center" style="background: rgba(99, 102, 241, 0.12); color: #818cf8; width: 38px; height: 38px;">
                    <i class="fas fa-building-user fs-6"></i>
                </div>
                <div class="ms-3">
                    <div class="fw-bold text-light">{{ $dept->nama_dinas }}</div>
                    <div class="text-white-50 small" style="font-size: 0.72rem; opacity: 0.75;">
                        <i class="fas fa-users me-1"></i> {{ $dept->users_count ?? 0 }} Operator Terdaftar
                    </div>
                </div>
            </div>
        </td>

        <td class="text-center">
            <span class="badge bg-white bg-opacity-5 border border-white border-opacity-10 px-2 py-1 fw-semibold" style="color: #a5b4fc; font-family: 'Courier New', Courier, monospace;">
                {{ $dept->kode_dinas ?? '-' }}
            </span>
        </td>

        @hasanyrole('kabid_perbendaharaan|kasie_bud')
        <td class="text-center">
            <div class="ba-month-grid" aria-label="Pilih bulan BA Rekon">
                @for($month = 1; $month <= 12; $month++)
                    @php
                        $monthBa = $dept->baRekons
                            ->where('tahun', $tahunDipilih)
                            ->firstWhere('bulan', $month);
                        $monthClass = match($monthBa?->status) {
                            'verified' => 'month-verified',
                            'pending' => 'month-pending',
                            'rejected' => 'month-rejected',
                            default => 'month-empty',
                        };
                        $monthStatusLabel = match($monthBa?->status) {
                            'verified' => 'terverifikasi',
                            'pending' => 'menunggu verifikasi',
                            'rejected' => 'ditolak',
                            default => 'belum upload',
                        };
                    @endphp
                    <a href="{{ request()->fullUrlWithQuery(['bulan' => $month]) }}"
                       class="ba-month {{ $monthClass }} {{ $bulanDipilih === $month ? 'is-selected' : '' }}"
                       title="{{ $namaBulanBa[$month] }} {{ $tahunDipilih }}: {{ $monthStatusLabel }}"
                       aria-label="Pilih {{ $namaBulanBa[$month] }} {{ $tahunDipilih }}; status {{ $monthStatusLabel }}"
                       aria-current="{{ $bulanDipilih === $month ? 'page' : 'false' }}">
                        {{ $month }}
                    </a>
                @endfor
            </div>
        </td>
        <td class="text-center">
            @if($baRekon)
                @if($baRekon->status == 'verified')
                    <span class="badge ba-status-badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1">
                        Terverifikasi
                    </span>
                @elseif($baRekon->status == 'rejected')
                    <span class="badge ba-status-badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1"
                          title="Catatan: {{ $baRekon->catatan ?? 'Tidak ada catatan' }}" data-bs-toggle="tooltip">
                        Ditolak
                    </span>
                @else
                    <span class="badge ba-status-badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1">
                        Menunggu Verifikasi
                    </span>
                @endif
            @else
                <span class="badge ba-status-badge bg-secondary bg-opacity-10 text-light border border-secondary border-opacity-25 px-2 py-1">
                    Belum Upload
                </span>
            @endif
        </td>
        <td class="text-center">
            <div class="ba-access-status text-{{ $aksesClass === 'secondary' ? 'light' : $aksesClass }}">
                <span class="ba-access-status-label">{{ $aksesLabel }}</span>
                @if($submissionAvailability['reason'] === 'ba_rekon')
                    <span class="ba-status-detail">BA Rekon</span>
                @elseif($aksesManual === true && $dept->akses_pengajuan_dibuka_oleh)
                    <span class="ba-status-byline">{{ $dept->akses_pengajuan_dibuka_oleh }}</span>
                @endif
            </div>
        </td>
        <td class="text-center pe-4">
            <div class="d-flex justify-content-center align-items-center flex-wrap gap-2">
@if($baRekon)
<button type="button"
        class="btn btn-outline-info rounded-circle d-flex align-items-center justify-content-center text-decoration-none"
        style="width: 32px; height: 32px; padding: 0;"
        title="Preview BA Rekon"
        aria-label="Pratinjau BA Rekon {{ $dept->nama_dinas }} bulan {{ $bulanDipilih }} tahun {{ $tahunDipilih }}"
        data-bs-toggle="modal"
        data-bs-target="#modalBaRekon"
        data-url="{{ route('validasi.ba-rekon.show', ['id' => $dept->id, 'bulan' => $bulanDipilih, 'tahun' => $tahunDipilih]) }}">
    <i class="fas fa-eye fs-7"></i>
</button>
@endif

<form action="{{ $aksesOnly ? route('departments.submission-access', $dept->id) : route('validasi.ba-rekon.verifikasi', ['id' => $dept->id, 'bulan' => $bulanDipilih, 'tahun' => $tahunDipilih]) }}"
      method="POST" class="d-inline" id="form-verifikasi-{{ $dept->id }}">
    @csrf
    @if($aksesOnly)
        <input type="hidden" name="akses" value="open">
    @endif
    <button type="button" class="btn btn-outline-success rounded-circle d-flex align-items-center justify-content-center"
            style="width: 32px; height: 32px; padding: 0;" title="{{ $aksesOnly ? 'Buka akses manual pengajuan' : 'Setujui BA Rekon' }}" data-bs-toggle="tooltip"
            aria-label="{{ $aksesOnly ? 'Buka akses manual pengajuan untuk ' . $dept->nama_dinas : 'Setujui BA Rekon ' . $dept->nama_dinas }}"
            onclick="{{ $aksesOnly ? 'konfirmasiAksesPengajuan(' . $dept->id . ', true)' : 'konfirmasiVerifikasi(' . $dept->id . ')' }}">
        <i class="fas {{ $aksesOnly ? 'fa-unlock-alt' : 'fa-check' }} fs-7"></i>
    </button>
</form>

<form action="{{ $aksesOnly ? route('departments.submission-access', $dept->id) : route('validasi.ba-rekon.tolak', ['id' => $dept->id, 'bulan' => $bulanDipilih, 'tahun' => $tahunDipilih]) }}"
      method="POST" class="d-inline" id="form-tolak-{{ $dept->id }}">
    @csrf
    @unless($aksesOnly)
        <input type="hidden" name="catatan" id="input-catatan-{{ $dept->id }}">
    @else
        <input type="hidden" name="akses" value="closed">
    @endunless
    <button type="button" class="btn btn-outline-danger rounded-circle d-flex align-items-center justify-content-center"
            style="width: 32px; height: 32px; padding: 0;" title="{{ $aksesOnly ? 'Tutup akses manual pengajuan' : 'Tolak BA Rekon' }}" data-bs-toggle="tooltip"
            aria-label="{{ $aksesOnly ? 'Tutup akses manual pengajuan untuk ' . $dept->nama_dinas : 'Tolak BA Rekon ' . $dept->nama_dinas }}"
            onclick="{{ $aksesOnly ? 'konfirmasiAksesPengajuan(' . $dept->id . ', false)' : 'konfirmasiTolak(' . $dept->id . ')' }}">
        <i class="fas {{ $aksesOnly ? 'fa-lock' : 'fa-times' }} fs-7"></i>
    </button>
</form>
            </div>
        </td>
        @endhasanyrole

@role('super_admin')
<td class="text-center pe-4">
    <div class="d-flex justify-content-center align-items-center gap-2">

        <button type="button" class="btn btn-outline-info rounded-circle d-flex align-items-center justify-content-center btn-edit-dept text-decoration-none"
                style="width: 32px; height: 32px; padding: 0;"
                data-id="{{ $dept->id }}"
                data-nama="{{ $dept->nama_dinas }}"
                data-kode="{{ $dept->kode_dinas }}"
                title="Edit Data">
            <i class="fas fa-pencil-alt fs-7"></i>
        </button>

        <button type="button" class="btn btn-outline-danger rounded-circle d-flex align-items-center justify-content-center btn-delete-dept"
                style="width: 32px; height: 32px; padding: 0;"
                data-id="{{ $dept->id }}"
                title="Hapus Data">
            <i class="fas fa-trash-alt fs-7"></i>
        </button>

        <form action="{{ route('departments.destroy', $dept->id) }}" method="POST" id="form-delete-{{ $dept->id }}" class="d-none no-ajax">
            @csrf
            @method('DELETE')
        </form>
    </div>
</td>
@endrole
    </tr>
@empty
    <tr>
        @role('super_admin')
        <td colspan="3" class="text-center py-5 text-white-50" style="opacity: 0.5;">
        @endrole
        @hasanyrole('kabid_perbendaharaan|kasie_bud')
        <td colspan="6" class="text-center py-5 text-white-50" style="opacity: 0.5;">
        @endhasanyrole
            <i class="fas fa-folder-open d-block mb-2 fa-2x text-gradient-indigo"></i> Belum ada data dinas yang tersimpan.
        </td>
    </tr>
@endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($departments->hasPages())
                    <div class="my-3 d-flex justify-content-center">
                        <div class="glass-pagination text-light px-4 py-1 rounded-pill shadow-sm">
                            {{ $departments->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @endif
            </div>

        </div>

    </div>
</div>

{{-- Modal: Tambah dinas --}}
<div class="modal fade" id="modalTambahDinas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card-dark rounded-4 shadow-xl border border-white border-opacity-10">
            <div class="modal-header border-bottom border-white border-opacity-10 py-3 px-4">
                <h6 class="modal-title fw-bold m-0 text-white">
                    <i class="fas fa-plus-circle me-2 text-gradient-indigo"></i>Tambah Data Dinas Baru
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('departments.store') }}" method="POST" class="no-ajax">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="small text-white-50 fw-semibold mb-2 text-uppercase d-block" style="font-size: 0.65rem; letter-spacing: 0.05em;">Nama Dinas Lengkap</label>

                        <input type="text" name="nama_dinas" class="form-control rounded-3 py-2 glass-input text-white" placeholder="Contoh: Badan Keuangan Daerah" required autocomplete="off">
                    </div>
                    <div class="mb-0">
                        <label class="small text-white-50 fw-semibold mb-2 text-uppercase d-block" style="font-size: 0.65rem; letter-spacing: 0.05em;">Kode Singkatan</label>
                        <input type="text" name="kode_dinas" class="form-control rounded-3 py-2 glass-input text-white" placeholder="Contoh: BKD" autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer border-top border-white border-opacity-10 py-3 px-4">
                    <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3 border-opacity-25" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-glass-primary btn-sm rounded-pill px-4">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Ubah data dinas --}}
<div class="modal fade" id="modalEditDinas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content glass-card-dark rounded-4 shadow-xl border border-white border-opacity-10">
            <div class="modal-header border-bottom border-white border-opacity-10 py-3 px-4">
                <h6 class="modal-title fw-bold m-0 text-white">
                    <i class="fas fa-edit me-2 text-info"></i>Edit Data Dinas
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="formEditDinas" method="POST" class="no-ajax">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="small text-white-50 fw-semibold mb-2 text-uppercase d-block" style="font-size: 0.65rem; letter-spacing: 0.05em;">Nama Dinas Lengkap</label>
                        <input type="text" name="nama_dinas" id="edit_nama_dinas" class="form-control rounded-3 py-2 glass-input text-white" required autocomplete="off">
                    </div>
                    <div class="mb-0">
                        <label class="small text-white-50 fw-semibold mb-2 text-uppercase d-block" style="font-size: 0.65rem; letter-spacing: 0.05em;">Kode Singkatan</label>
                        <input type="text" name="kode_dinas" id="edit_kode_dinas" class="form-control rounded-3 py-2 glass-input text-white" autocomplete="off">
                    </div>
                </div>
                <div class="modal-footer border-top border-white border-opacity-10 py-3 px-4">
                    <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3 border-opacity-25 text-white-50" data-bs-dismiss="modal">Batal</button>

                    <button type="submit" class="btn btn-info btn-sm rounded-pill px-4 text-white shadow-sm fw-semibold">Update Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal: Pratinjau Berita Acara Rekonsiliasi --}}
<div class="modal fade" id="modalBaRekon" tabindex="-1" aria-labelledby="modalBaRekonLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="width: min(95vw, 1400px); max-width: none;">
        <div class="modal-content glass-card-dark rounded-4 overflow-hidden border border-white border-opacity-10">
            <div class="modal-header border-bottom border-white border-opacity-10">
                <h5 class="modal-title text-white fw-bold" id="modalBaRekonLabel">Preview Berita Acara Rekonsiliasi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="baRekonContent">
                    <p class="text-white-50 text-center py-4 mb-0">Memuat data...</p>
                </div>
            </div>
            <div class="modal-footer border-top border-white border-opacity-10">
                <button type="button" class="btn btn-outline-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('click', function(e) {
    const editBtn = e.target.closest('.btn-edit-dept');
    if (editBtn) {
        e.preventDefault();

        const id = editBtn.getAttribute('data-id');
        const nama = editBtn.getAttribute('data-nama');
        const kode = editBtn.getAttribute('data-kode');

        document.getElementById('formEditDinas').setAttribute('action', `/departments/${id}`);
        document.getElementById('edit_nama_dinas').value = nama;
        document.getElementById('edit_kode_dinas').value = (kode === '-' || !kode) ? '' : kode;

        const modalElement = document.getElementById('modalEditDinas');
        const editModal = new bootstrap.Modal(modalElement);
        editModal.show();
        return;
    }

    const deleteBtn = e.target.closest('.btn-delete-dept');
    if (deleteBtn) {
        e.preventDefault();
        const id = deleteBtn.getAttribute('data-id');

        Swal.fire({
            title: 'Hapus Data Dinas?',
            text: "Data instansi yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#495057',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            background: 'rgba(30, 30, 46, 0.95)',
            backdrop: 'rgba(0, 0, 0, 0.4)',
            customClass: {
                popup: 'glass-swal-popup'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-delete-' + id).submit();
            }
        });
        return;
    }
});
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalBaRekon = document.getElementById('modalBaRekon');

    if (modalBaRekon) {
        modalBaRekon.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const url = button.getAttribute('data-url');
            const contentArea = document.getElementById('baRekonContent');

            contentArea.innerHTML = `<iframe src="${url}" title="Preview BA Rekon" style="display:block;width:100%;height:min(78vh,850px);border:0;background:#fff;"></iframe>`;
        });
    }
});
</script>

<script>
// Function: Meminta konfirmasi verifikasi BA Rekon
function konfirmasiAksesPengajuan(id, buka) {
    Swal.fire({
        title: buka ? 'Buka akses pengajuan?' : 'Tutup akses pengajuan?',
        text: buka
            ? 'SKPD ini dapat mengakses Pengajuan Transaksi tanpa BA Rekon terverifikasi.'
            : 'Akses Pengajuan Transaksi SKPD ini akan ditutup.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: buka ? '#198754' : '#dc3545',
        cancelButtonColor: '#495057',
        confirmButtonText: buka ? 'Ya, Buka Akses' : 'Ya, Tutup Akses',
        cancelButtonText: 'Batal',
        background: 'rgba(30, 30, 46, 0.95)',
        backdrop: 'rgba(0, 0, 0, 0.4)',
        customClass: { popup: 'glass-swal-popup' }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-' + (buka ? 'verifikasi' : 'tolak') + '-' + id).submit();
        }
    });
}

function konfirmasiVerifikasi(id) {
    Swal.fire({
        title: 'Setujui BA Rekon?',
        text: "Apakah Anda yakin ingin menyetujui BA Rekon untuk dinas ini?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#495057',
        confirmButtonText: 'Ya, Setujui!',
        cancelButtonText: 'Batal',
        background: 'rgba(30, 30, 46, 0.95)',
        backdrop: 'rgba(0, 0, 0, 0.4)',
        customClass: {
            popup: 'glass-swal-popup'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-verifikasi-' + id).submit();
        }
    });
}

// Function: Meminta konfirmasi penolakan BA Rekon
function konfirmasiTolak(id) {
    Swal.fire({
        title: 'Tolak BA Rekon',
        text: "Silakan masukkan alasan penolakan:",
        icon: 'warning',
        input: 'textarea',
        inputPlaceholder: 'Tuliskan catatan penolakan di sini...',
        inputAttributes: {
            'aria-label': 'Tuliskan catatan penolakan di sini'
        },
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#495057',
        confirmButtonText: 'Ya, Tolak!',
        cancelButtonText: 'Batal',
        background: 'rgba(30, 30, 46, 0.95)',
        backdrop: 'rgba(0, 0, 0, 0.4)',
        customClass: {
            popup: 'glass-swal-popup'
        },
        inputValidator: (value) => {
            if (!value) {
                return 'Catatan penolakan wajib diisi!';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('input-catatan-' + id).value = result.value;
            document.getElementById('form-tolak-' + id).submit();
        }
    });
}
</script>



<style>
    /* Styling Dark Glassmorphic untuk SweetAlert2 */
    .glass-swal-popup {
        background: rgba(30, 30, 46, 0.75) !important;
        backdrop-filter: blur(16px) !important;
        -webkit-backdrop-filter: blur(16px) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        border-radius: 16px !important;
        color: #f8f9fa !important;
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37) !important;
    }

    .glass-swal-popup .swal2-title,
    .glass-swal-popup .swal2-html-container {
        color: #f8f9fa !important;
    }

    .glass-swal-popup .swal2-textarea {
        background: rgba(20, 20, 30, 0.6) !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        color: #ffffff !important;
        border-radius: 8px !important;
    }

    .glass-swal-popup .swal2-textarea:focus {
        border-color: #0d6efd !important;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25) !important;
    }
</style>

@endsection
