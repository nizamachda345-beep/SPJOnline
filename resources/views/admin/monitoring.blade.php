{{-- View: admin/monitoring --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card glass-card-dark border-0 shadow-sm rounded-4 mb-3">
        <div class="card-body p-3">
            <form action="{{ url()->current() }}" method="GET" class="row g-3 align-items-end no-ajax">

                <input type="hidden" name="sort" value="{{ $sortBy ?? 'created_at' }}">
                <input type="hidden" name="direction" value="{{ $direction ?? 'desc' }}">

                <div class="col-xl-3 col-md-6 col-12">
                    <label class="form-label small fw-bold text-light text-uppercase tracking-wider mb-2">Nomor SPP/SPM</label>
                    <div class="input-group search-group">
                        <span class="input-group-text border-0 bg-light"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="nomor" class="form-control border-0 bg-light rounded-end shadow-none"
                            placeholder="Cari No. SPP atau SPM..."
                            value="{{ request('nomor') ?? request('nomor_spp') ?? request('nomor_spm') }}">
                    </div>
                </div>

                <div class="col-xl-3 col-12">
                    <label class="form-label small fw-semibold text-uppercase tracking-wider text-white-50 mb-2">SKPD</label>
                    <select name="dinas" class="form-select glass-input-dark shadow-none">
                        <option value="">Pilih SKPD</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}" @selected(request('dinas') == $d->id)>
                                {{ $d->nama_dinas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xl-3 col-md-6 col-12">
                    <label class="form-label small fw-bold text-light text-uppercase tracking-wider mb-2">Filter Tanggal</label>
                    <input type="date" name="tanggal" class="form-control border-0 bg-light shadow-none" value="{{ request('tanggal') }}">
                </div>

                <div class="col-xl-3 col-md-6 col-12">
                    <div class="d-flex gap-2 flex-nowrap" style="height: 38px;">
                        <button type="submit" class="btn btn-dark px-3 rounded-3 flex-grow-1 shadow-sm d-flex align-items-center justify-content-center fw-semibold transition-all">
                            <i class="fas fa-sliders-h me-2"></i>
                            <span>Filter</span>
                        </button>

                        <button type="button" id="btnResetFilter" class="btn btn-light px-3 rounded-3 border shadow-xs text-muted d-flex align-items-center justify-content-center reset-btn transition-all" title="Reset Filter">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <div class="card glass-header-dark border-0 shadow-sm rounded-4 mb-3 overflow-hidden" id="monitoring-container">
        <div class="p-4 d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-3 border-bottom border-white-10" style="background: linear-gradient(to bottom, rgba(255,255,255,0.02), transparent);">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center shadow-sm">
                    <i class="fas fa-globe text-primary fs-1"></i>
                </div>
                <div>
                    <h5 class="mb-1 fw-bold text-white d-flex align-items-center tracking-wide" style="background: linear-gradient(45deg, #ffffff, #a3b8cc); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                        Monitoring Global
                    </h5>
                    <p class="text-white-50 small mb-0 opacity-75">Pantau real-time status keseluruhan pengajuan berkas dalam sistem.</p>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 bg-white bg-opacity-5 border border-white border-opacity-10 px-3 py-2 rounded-pill shadow-xs">
                <span class="text-primary fw-semibold tracking-wider text-uppercase" style="font-size: 0.75rem;">
                    <i class="fas fa-user-shield text-primary"></i>
                    {{ auth()->user()->role_label }}
                </span>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-0">
                    <thead style="background: rgba(0, 0, 0, 0.25);">
                        <tr class="small text-uppercase tracking-wider">
                            <th class="ps-4 py-3">SKPD / DINAS</th>
                            <th class="py-3 text-center">Tanggal</th>
                            <th class="py-3 text-center">No. SPP / SPM</th>
                            <th class="py-3 text-center">Jenis</th>
                            <th class="py-3 text-center">Total Pencairan</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="py-3 text-center">Status SP2D</th>
                            <th class="py-3 text-center">Audit</th>
                            <th class="text-center pe-4 py-3" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $s)
                        <tr class="border-bottom border-white-5 transition-all" style="background: transparent;">
                            <td class="ps-4 text-white-80">
                                <div class="fw-semibold">
                                    {{ $s->department->nama_dinas }}
                                </div>
                                <small class="text-light">{{ $s->department->kode_dinas }}</small>
                            </td>
                           <td class="text-center">
                                <div>{{ $s->created_at ? \Carbon\Carbon::parse($s->created_at)->format('d-m-Y') : '-' }}</div>
                                <small class="text-white-50">

                                    {{ $s->created_at ? \Carbon\Carbon::parse($s->created_at)->format('H:i') . ' ' . (\Carbon\Carbon::parse($s->created_at)->offsetHours == 8 ? 'WITA' : (\Carbon\Carbon::parse($s->created_at)->offsetHours == 9 ? 'WIT' : 'WIB')) : '-' }}
                                </small>
                            </td>
                            <td class="text-center">
                                <span class="text-info font-monospace">{{ $s->nomor_spp ?? '-' }}</span>
                                <span class="text-light font-monospace">/</span>
                                <span class="text-warning font-monospace">{{ $s->nomor_spm ?? '-' }}</span>
                            </td>

                            <td class="small fw-semibold text-light text-uppercase text-center">
                                {{ $s->jenis_pencairan }}
                            </td>

                            <td class="text-center">
                                @if($s->total_pencairan)
                                    <span class="text-success text-center fw-semibold">
                                        Rp {{ number_format($s->total_pencairan, 0, ',', '.') }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td class="text-center fw-semibold status-cell">
                                @php
                                    $statusPengajuan = strtolower(trim((string) $s->status));
                                @endphp
                                @switch($statusPengajuan)
                                    @case('disetujui')
                                    @case('approved')
                                    @case('verified')
                                        <span class="text-success"><i class="fas fa-check-circle me-1"></i>DISAHKAN</span>
                                        @break
                                    @case('proses verifikasi')
                                        <span class="text-info"><i class="fas fa-spinner fa-spin me-1"></i>VERIFIKASI</span>
                                        @break
                                    @case('proses validasi')
                                        <span class="text-info"><i class="fas fa-spinner fa-spin me-1"></i>VALIDASI</span>
                                        @break
                                    @case('revisi')
                                        <span class="text-warning"><i class="fas fa-pen-to-square me-1"></i>PERLU REVISI</span>
                                        @break
                                    @case('direvisi')
                                        <span class="text-info"><i class="fas fa-rotate me-1"></i>REVISI DIKIRIM ULANG</span>
                                        @break
                                    @case('ditolak')
                                    @case('rejected')
                                        <span class="text-danger"><i class="fas fa-ban me-1"></i>DITOLAK</span>
                                        @break
                                    @case('expired')
                                        <span class="text-danger"><i class="fas fa-clock me-1"></i>EXPIRED</span>
                                        @break
                                    @case('reset')
                                        <span class="text-danger"><i class="fas fa-undo-alt me-1"></i>RESET</span>
                                        @break
                                    @case('pending')
                                        <span class="text-warning"><i class="fas fa-clock me-1"></i>PENDING</span>
                                        @break
                                    @default
                                        <span class="text-white-50">{{ $statusPengajuan !== '' ? strtoupper(str_replace('_', ' ', $statusPengajuan)) : '-' }}</span>
                                @endswitch
                            </td>

                            <td class="text-center">
                                @php
                                    $jenisPencairan = strtoupper(trim((string) $s->jenis_pencairan));
                                    $menggunakanSp2d = in_array($jenisPencairan, ['LS', 'GU', 'TU', 'UP'], true);
                                    $dokumenSp2d = $s->documents->first(
                                        fn ($document) => stripos($document->jenis_dokumen, 'SP2D') !== false
                                    );
                                    $statusSp2d = strtolower((string) $dokumenSp2d?->status_dokumen);
                                @endphp
                                @if($statusPengajuan === 'expired')
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 rounded-pill">Expired</span>
                                @elseif($jenisPencairan === 'GU NIHIL')
                                    <span class="badge bg-secondary bg-opacity-10 text-white-50 border border-secondary border-opacity-25 px-2 py-1 rounded-pill">Tidak Berlaku</span>
                                @elseif(in_array($statusSp2d, ['lengkap', 'verified', 'approved'], true))
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">Terverifikasi</span>
                                @elseif(in_array($statusSp2d, ['uploaded', 'direvisi', 'pending'], true))
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1 rounded-pill">Menunggu Verifikasi</span>
                                @elseif(in_array($statusSp2d, ['revisi', 'rejected'], true))
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 rounded-pill">Perlu Revisi</span>
                                @elseif($menggunakanSp2d || $jenisPencairan === 'TU NIHIL')
                                    <span class="badge bg-secondary bg-opacity-10 text-white-50 border border-secondary border-opacity-25 px-2 py-1 rounded-pill">Belum Diunggah</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-white-50 border border-secondary border-opacity-25 px-2 py-1 rounded-pill">Tidak Berlaku</span>
                                @endif
                            </td>

                            <td class="text-center align-middle">

                                @if($s->status_audit === 'sesuai')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill small fw-bold">
                                        <i class="fas fa-check-double me-1"></i>AUDITED
                                    </span>
                                @elseif($s->status_audit === 'dengan_catatan')

                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-2 py-1 rounded-pill small fw-bold"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="{{ $s->catatan_audit }}">
                                        <i class="fas fa-exclamation-circle me-1"></i>AUDITED (CATATAN)
                                    </span>
                                @else
                                    <span class="text-white-50 opacity-50 small font-monospace">-</span>
                                @endif
                            </td>

                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-2 action-container">

                                    <a href="{{ route('monitoring.show', $s->id) }}"
                                    class="btn btn-sm btn-dark rounded-circle d-flex align-items-center justify-content-center shadow-sm shadow-light btn-hover"
                                    style="width: 34px; height: 34px; min-width: 34px; background: rgba(255,255,255,0.05); transition: all 0.25s;"
                                    title="Lihat Detail">
                                        <i class="fas fa-eye text-white-80"></i>
                                    </a>

                                    @role('super_admin')

                                        @if($s->status != 'pending' && $s->status != 'reset')
                                        <form action="{{ route('monitoring.reset', $s->id) }}" method="POST" class="d-inline m-0 form-reset-status">
                                            @csrf
                                            <button type="button"
                                                    class="btn btn-sm btn-dark rounded-circle d-flex align-items-center justify-content-center shadow-sm shadow-light btn-action-reset"
                                                    style="width: 34px; height: 34px; min-width: 34px; background: rgba(255,255,255,0.05); transition: all 0.25s;"
                                                    title="Reset Status Berkas">
                                                <i class="fas fa-undo-alt text-danger"></i>
                                            </button>
                                        </form>
                                        @endif
                                    @endrole

                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="border-0">
                                <div class="text-center py-5 text-white-50">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-0 small">Data tidak tersedia sesuai kriteria saat ini.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($submissions->hasPages())
            <div class="my-3 d-flex justify-content-center">
                <div class="glass-pagination  px-4 py-1 rounded-pill shadow-sm">
                    {{ $submissions->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const resetButtons = document.querySelectorAll('.btn-action-reset');

    resetButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            const form = this.closest('.form-reset-status');
            if (!form) return;

            const urlAction = form.getAttribute('action');
            const tokenElement = form.querySelector('input[name="_token"]');
            const csrfToken = tokenElement ? tokenElement.value : '';

            const tableRow = this.closest('tr');

            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Verifikasi/Validasi berkas akan dihapus dan status kembali ke awal (Reset)!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-undo-alt me-1"></i> Ya, Reset Status!',
                cancelButtonText: 'Batal',
                background: '#1e1e2e',
                color: '#ffffff',
                customClass: {
                    popup: 'border border-secondary-subtle rounded-4'
                }
            }).then((result) => {
                if (result.isConfirmed) {

                    Swal.showLoading();

                    fetch(urlAction, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            _token: csrfToken
                        })
                    })
                    .then(async response => {
                        const isJson = response.headers.get('content-type')?.includes('application/json');
                        const data = isJson ? await response.json() : null;

                        if (!response.ok) {
                            throw new Error(data?.message || 'Gagal memproses request ke server.');
                        }
                        return data;
                    })
                    .then(data => {
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Status berkas telah diubah menjadi RESET.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false,
                            background: '#1e1e2e',
                            color: '#ffffff'
                        });

                        if (tableRow) {
                            const statusCell = tableRow.querySelector('.status-cell');
                            if (statusCell) {
                                statusCell.innerHTML = `
                                    <span class="text-danger text-center fw-bold animate__animated animate__fadeIn">
                                        <i class="fas fa-undo-alt me-1"></i>RESET
                                    </span>
                                `;
                            }

                            if (form) {
                                form.parentNode.removeChild(form);
                            }
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            title: 'Sistem Menolak Request',
                            text: 'Terjadi kegagalan rute: ' + error.message,
                            icon: 'error',
                            background: '#1e1e2e',
                            color: '#ffffff'
                        });
                    });
                }
            });
        });
    });

    const btnResetFilter = document.getElementById('btnResetFilter');
    if (btnResetFilter) {
        btnResetFilter.addEventListener('click', function() {
            Swal.fire({
                title: 'Bersihkan Filter?',
                text: "Semua kata kunci pencarian akan dikosongkan kembali.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Bersihkan',
                cancelButtonText: 'Kembali',
                background: '#1e1e2e',
                color: '#ffffff'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = window.location.pathname;
                }
            });
        });
    }
});
</script>
@endsection
