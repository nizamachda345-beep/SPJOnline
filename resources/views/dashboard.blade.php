{{-- View: dashboard --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">

<div class="row mb-4 align-items-stretch">

<div class="col-lg-12">
    <div class="glass-header-dark p-3 rounded-4 position-relative overflow-hidden">

        <div class="glass-reflection"></div>

        <div class="d-flex align-items-center justify-content-between position-relative" style="z-index: 2;">

            <div class="d-flex align-items-center">
                @auth
                    @php
                        $roleMap = [
                            'super_admin' => ['title' => 'SU', 'color' => '#4e73df'],
                            'operator'    => ['title' => 'OPERATOR', 'color' => '#f6ad55'],
                            'kasie_bud'   => ['title' => 'VERIFIKATOR', 'color' => '#36b9cc'],
                            'kabid_perbendaharaan' => ['title' => 'VALIDATOR', 'color' => '#1cc88a'],
                            'bpk'         => ['title' => 'AUDITOR', 'color' => '#e74a3b'],
                        ];

                        $meta = ['title' => 'USER', 'color' => '#ffffff'];
                        foreach($roleMap as $role => $data) {
                            if(auth()->user()->hasRole($role)) { $meta = $data; break; }
                        }
                    @endphp

                    <div class="role-line-indicator" style="background-color: {{ $meta['color'] }};"></div>

                    <div class="d-flex flex-column ms-3">

                        @if(auth()->user()->hasRole('operator'))
                            <span class="skpd-text-glow" style="--glow-color: {{ $meta['color'] }};">
                                {{ auth()->user()->department->nama_dinas ?? 'Semua SKPD' }}
                            </span>
                        @else
                            <span class="skpd-text-glow fw-bold" style="--glow-color:{{ $meta['color'] }};">
                                {{ auth()->user()->department->nama_dinas ?? 'Semua SKPD' }}
                            </span>
                        @endif
                        <span class="role-label-minimal">{{ $meta['title'] }}</span>
                    </div>
                @endauth
            </div>

            <div class="time-wrapper d-none d-md-block">
                <div class="d-flex flex-column align-items-end">

                    <div class="date-container mb-1">
                        <span id="liveTanggal" class="date-text-minimal">
                            Wednesday, 29 April 2026
                        </span>
                    </div>

                    <div class="d-flex align-items-center">
                        <span id="liveTime" class="clock-digits-minimal">00:00</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

</div>

    @php
    $role = auth()->user()->roles->first()->name ?? '';
    $hitungInProgress = ($stats['pending'] ?? 0) + ($stats['direvisi'] ?? 0) + ($stats['validasi'] ?? 0) + ($stats['revisi'] ?? 0) + ($stats['verifikasi'] ?? 0);

    $allCards = collect([
        ['id' => 'total', 'label' => 'Total Pengajuan', 'val' => $stats['total_berkas'], 'color' => 'primary', 'icon' => 'fa-file-medical', 'desc' => 'Akumulasi Berkas'],
        ['id' => 'verifikasi', 'label' => 'Verifikasi', 'val' => $stats['pending'], 'color' => 'royal', 'icon' => 'fa-clock-rotate-left', 'desc' => 'Proses Verifikasi'],
        ['id' => 'validasi', 'label' => 'Validasi', 'val' => $stats['validasi'], 'color' => 'royal', 'icon' => 'fa-hourglass-half', 'desc' => 'Proses Validasi'],
        ['id' => 'proses', 'label' => 'Sedang Diproses', 'val' => $hitungInProgress, 'color' => 'royal', 'icon' => 'fa-hourglass-half', 'desc' => 'Pengajuan dalam proses'],
        ['id' => 'revisi', 'label' => 'Revisi', 'val' => $stats['revisi'], 'color' => 'amber', 'icon' => 'fa-file-signature', 'desc' => 'Butuh Tindakan'],
        ['id' => 'expired', 'label' => 'Expired', 'val' => $stats['expired'] ?? 0, 'color' => 'crimson', 'icon' => 'fa-clock', 'desc' => 'Melewati batas waktu'],
        ['id' => 'ditolak', 'label' => 'Ditolak', 'val' => $stats['rejected'], 'color' => 'crimson', 'icon' => 'fa-ban', 'desc' => 'Berkas Bermasalah'],
        ['id' => 'disetujui', 'label' => 'Disahkan', 'val' => $stats['disetujui'], 'color' => 'emerald', 'icon' => 'fa-check-double', 'desc' => 'Selesai Validasi'],
        ['id' => 'anggaran_disetujui', 'label' => 'Total Anggaran', 'val' => $stats['total_anggaran_disetujui'] ?? 0, 'color' => 'emerald', 'icon' => 'fa-money-bill-wave', 'desc' => 'Total nilai pengajuan yang disetujui', 'currency' => true],
        ['id' => 'sp2d_belum_upload', 'label' => 'SP2D', 'val' => $stats['sp2d_belum_upload'], 'color' => 'danger', 'icon' => 'fa-file-circle-exclamation', 'desc' => 'Belum diunggah']
    ]);

    if (in_array($role, ['kasie_bud', 'kabid_perbendaharaan', 'bpk'])) {
        $displayIds = ['total', 'verifikasi', 'validasi', 'disetujui', 'sp2d_belum_upload'];
    } else {
        $displayIds = ['total', 'proses', 'revisi', 'expired', 'ditolak', 'disetujui', 'anggaran_disetujui', 'sp2d_belum_upload'];
    }

    $cards = $allCards->whereIn('id', $displayIds);
    $cardColumnClass = in_array($role, ['kasie_bud', 'kabid_perbendaharaan'])
        ? 'col-lg col-md-4 col-6'
        : 'col-xl-3 col-md-4 col-6';
@endphp

    <style>
        .dashboard-stat-card {
            min-height: 148px;
        }

        .dashboard-stat-card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 0.65rem;
        }

        .dashboard-stat-summary {
            min-height: 44px;
            gap: 0.75rem;
        }

        .dashboard-stat-summary .icon-box {
            width: 40px;
            height: 40px;
            flex: 0 0 40px;
        }

        .dashboard-stat-value {
            font-size: clamp(1.05rem, 1.5vw, 1.45rem);
            white-space: nowrap;
        }

        .dashboard-stat-summary > .text-end {
            flex: 1 1 auto;
            min-width: 0;
        }

        .dashboard-stat-value-currency {
            font-size: clamp(0.85rem, 1.1vw, 1rem);
            line-height: 1.2;
            white-space: normal;
            overflow-wrap: anywhere;
        }

        .dashboard-stat-label {
            min-height: 2.4em;
            line-height: 1.2;
            font-size: 0.72rem;
            font-weight: 700;
        }

        .dashboard-stat-description {
            line-height: 1.25;
            font-size: 0.68rem;
        }

        @media (max-width: 575.98px) {
            .dashboard-stat-card {
                min-height: 140px;
            }

            .dashboard-stat-card-body {
                padding: 1rem !important;
            }

            .dashboard-stat-label {
                font-size: 0.68rem;
            }
        }
    </style>

    <div class="row g-3 mb-4 dashboard-stat-grid">
        @foreach($cards as $card)
        <div class="{{ $cardColumnClass }}">
            @if($card['id'] === 'sp2d_belum_upload')
            <button type="button"
                    class="card idj-glass-card dashboard-stat-card border-0 shadow-sm rounded-4 h-100 card-hover overflow-hidden text-start w-100"
                    style="font: inherit; color: inherit; cursor: pointer;"
                    data-bs-toggle="modal"
                    data-bs-target="#belumUploadSp2dModal"
                    aria-label="Lihat daftar pengajuan yang belum mengunggah SP2D">
            @else
            <div class="card idj-glass-card dashboard-stat-card border-0 shadow-sm rounded-4 h-100 card-hover overflow-hidden">
            @endif
            <div class="glass-reflection"></div>
            <div class="card-body dashboard-stat-card-body p-3 p-lg-4">
                    <div class="dashboard-stat-summary d-flex align-items-center justify-content-between">
                        <div class="icon-box bg-{{ $card['color'] }}-soft text-{{ $card['color'] }} rounded-4 shadow-sm">
                            <i class="fas {{ $card['icon'] }}"></i>
                        </div>
                        <div class="text-end">
                            <h3 class="dashboard-stat-value {{ ($card['currency'] ?? false) ? 'dashboard-stat-value-currency' : '' }} fw-black mb-0 tabular-nums text-dark-blue">
                                @if($card['currency'] ?? false)
                                    Rp {{ number_format($card['val'], 0, ',', '.') }}
                                @else
                                    {{ number_format($card['val']) }}
                                @endif
                            </h3>
                        </div>
                    </div>
                    <div>
                        <span class="dashboard-stat-label card-label text-muted d-flex align-items-center text-uppercase">{{ $card['label'] }}</span>
                        <p class="dashboard-stat-description extra-small text-muted mb-0 opacity-75 fw-medium">{{ $card['desc'] }}</p>
                    </div>
                </div>
            @if($card['id'] === 'sp2d_belum_upload')
            </button>
            @else
            </div>
            @endif
        </div>
        @endforeach
    </div>

    <div class="modal fade" id="belumUploadSp2dModal" tabindex="-1" aria-labelledby="belumUploadSp2dModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-0 pb-2">
                    <div>
                        <h5 class="modal-title fw-bold text-dark" id="belumUploadSp2dModalLabel">
                            <i class="fas fa-file-circle-exclamation text-warning me-2"></i>Pengajuan Belum Upload SP2D
                        </h5>
                        <p class="small text-muted mb-0">{{ number_format($sp2dBelumUpload->total()) }} pengajuan yang sudah disahkan belum memiliki dokumen SP2D.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body pt-2">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nomor SPP / SPM</th>
                                    <th>Kegiatan</th>
                                    @if(auth()->user()->hasRole('kasie_bud') || auth()->user()->hasRole('kabid_perbendaharaan'))
                                        <th>SKPD</th>
                                    @endif
                                    <th>Tanggal Disahkan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sp2dBelumUpload as $submission)
                                    <tr>
                                        <td class="text-nowrap">
                                            <div class="fw-semibold">{{ $submission->nomor_spp ?? '-' }} / {{ $submission->nomor_spm ?? '-' }}</div>
                                        </td>
                                        <td>{{ $submission->judul_kegiatan ?? '-' }}</td>
                                        @if(auth()->user()->hasRole('kasie_bud') || auth()->user()->hasRole('kabid_perbendaharaan'))
                                            <td>{{ $submission->department->nama_dinas ?? '-' }}</td>
                                        @endif
                                        <td class="text-nowrap">{{ $submission->updated_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 3 + ((auth()->user()->hasRole('kasie_bud') || auth()->user()->hasRole('kabid_perbendaharaan')) ? 1 : 0) }}" class="text-center text-muted py-5">
                                            Tidak ada pengajuan yang menunggu unggahan SP2D.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($sp2dBelumUpload->hasPages())
                        <div class="d-flex justify-content-center mt-3 sp2d-pagination">
                            {{ $sp2dBelumUpload->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 align-items-stretch mb-3" id="mainDashboardGrid">

        <div class="col-xl-12 d-flex transition-layout" id="chartColumn">

            <div class="w-100">
                @include('components.monthly-chart')
            </div>
        </div>

        <div class="col-xl-12 d-flex transition-layout" id="sidebarColumn">

            <div class="card glass-card shadow-sm rounded-4 border-0 w-100 d-flex flex-column h-100 overflow-hidden">
                <div class="card-body p-4 d-flex flex-column h-100">

                    <div class="d-flex justify-content-between align-items-center mb-4 flex-shrink-0">
                        <h6 class="fw-bold text-dark mb-0 small text-uppercase tracking-widest" style="font-size: 0.7rem; opacity: 0.7;">Log Aktivitas</h6>
                        <div class="d-flex align-items-center gap-2">

                            <i class="fas fa-circle fa-fade text-primary"></i>
                            <span class="text-primary fw-bold" style="font-size: 0.75rem;">LIVE</span>
                        </div>
                    </div>

                    <div class="timeline-wrapper flex-grow-1 position-relative custom-scrollbar" style="overflow-y: auto; min-height: 0;">
                        <div class="position-absolute h-100 timeline-line" style="top: 0; left: 11px; width: 1px; background: rgba(0,0,0,0.05);"></div>

                        <div class="d-flex flex-column gap-4 pe-2">
                            @forelse($recentActivities as $item)
                                @php
                                    $statusConfig = match($item->status) {
                                        'proses validasi' => ['icon' => 'fa-sync-alt fa-spin', 'color' => '#3b82f6', 'label' => 'Validasi'],
                                        'revisi'          => ['icon' => 'fa-file-signature', 'color' => '#ff2121', 'label' => 'Revisi'],
                                        'disetujui'       => ['icon' => 'fa-check-circle fa-shake', 'color' => '#10b981', 'label' => 'Disetujui'],
                                        default           => ['icon' => 'fa-spinner fa-spin', 'color' => '#383415', 'label' => $item->status]
                                    };
                                @endphp

                                <div class="d-flex gap-3 position-relative">
                                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-white rounded-circle border"
                                         style="width: 24px; height: 24px; z-index: 2; border-color: {{ $statusConfig['color'] }}40 !important;">
                                        <i class="fas {{ $statusConfig['icon'] }}" style="font-size: 0.65rem; color: {{ $statusConfig['color'] }};"></i>
                                    </div>

                                    <div class="overflow-hidden w-100">
                                        <div class="mb-1">
                                            <span class="text-muted fw-bold text-uppercase" style="font-size: 0.55rem; letter-spacing: 0.5px;">
                                                {{ $item->department->nama_dinas ?? 'Instansi' }}
                                            </span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center gap-2">
                                            <div class="d-flex align-items-baseline gap-2 overflow-hidden">
                                                <span class="fw-medium text-dark-blue opacity-75" style="font-size: 0.65rem;">
                                                    {{ $item->nomor_spp }}/{{ $item->nomor_spm }}
                                                </span>
                                                <span class="text-muted fw-bold small text-truncate" style="font-size: 0.65rem;">
                                                    {{ $item->judul_kegiatan }}
                                                </span>
                                            </div>
                                            <div class="text-end flex-shrink-0">
                                                <p class="mb-0 fa-fade fw-bold" style="font-size: 0.6rem; color: {{ $statusConfig['color'] }};">
                                                    {{ $statusConfig['label'] }}
                                                </p>
                                                <small class="text-muted opacity-50" style="font-size: 0.6rem;">
                                                    {{ $item->updated_at->diffForHumans(null, true) }}
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <p class="text-muted small italic opacity-50">Kosong</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

<div class="col-12 col-md-6 col-xl-12 transition-layout">
    <div class="card glass-card-minimal border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body px-3 py-3 px-md-4">
            @php
                $dashboardProductName = $systemSettings['branding.product_name'] ?? 'SPJOnline';
                $dashboardAgencyName = $systemSettings['branding.agency_name'] ?? 'Badan Keuangan Daerah';
                $dashboardGovernmentName = preg_replace(
                    '/^Pemerintah Kabupaten\s+/i',
                    '',
                    trim($systemSettings['branding.government_name'] ?? 'Bolaang Mongondow')
                );
            @endphp
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                <div class="min-width-0">
                    <div class="fw-bold text-dark-blue">{{ $dashboardProductName }}</div>
                    <div class="text-muted">{{ $dashboardAgencyName }} <span class="mx-1">·</span> {{ $dashboardGovernmentName }}</div>
                </div>
                <div class="d-flex align-items-center gap-2 text-muted flex-shrink-0">
                    <span class="badge rounded-pill bg-white border text-muted fw-medium">v2.1.0</span>
                    <span aria-hidden="true">·</span>
                    <small>&copy; {{ date('Y') }} Nizam Achda</small>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        var sp2dModalElement = document.getElementById('belumUploadSp2dModal')
        if (sp2dModalElement) {
            sp2dModalElement.querySelectorAll('.pagination a').forEach(function (link) {
                link.href += '#belumUploadSp2dModal'
            })

            if (new URLSearchParams(window.location.search).has('sp2d_page')) {
                bootstrap.Modal.getOrCreateInstance(sp2dModalElement).show()
            }
        }

    });
</script>

<script>
    // Function: Memperbarui jam pada dashboard
    function updateClock() {
        const now = new Date();

        const timeZone = @json(app(\App\Services\SystemSettingsService::class)->timezone());

        const dateElement = document.getElementById('liveTanggal');
        const timeElement = document.getElementById('liveTime');

        if (dateElement) {
            const dateOptions = {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric',
                timeZone: timeZone
            };
            dateElement.textContent = now.toLocaleDateString('id-ID', dateOptions);
        }

        if (timeElement) {
            const timeOptions = {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false,
                timeZone: timeZone
            };

            const formatter = new Intl.DateTimeFormat('id-ID', timeOptions);
            const parts = formatter.formatToParts(now);

            const h = parts.find(p => p.type === 'hour').value;
            const m = parts.find(p => p.type === 'minute').value;
            const s = parts.find(p => p.type === 'second').value;

            timeElement.textContent = `${h}:${m}:${s}`;
        }
    }

    setInterval(updateClock, 1000);
    updateClock();
</script>
@endsection
