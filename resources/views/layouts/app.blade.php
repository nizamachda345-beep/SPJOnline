{{-- View: layouts/app --}}
@php
    $policySettingsService = app(\App\Services\SystemSettingsService::class);
    $policyTimezone = $policySettingsService->timezone();
    $policyTimezoneLabel = $policySettingsService->timezoneLabel($policyTimezone);
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ filled($systemSettings['branding.product_name'] ?? null) ? $systemSettings['branding.product_name'] : config('app.name') }} - {{ filled($systemSettings['branding.government_name'] ?? null) ? $systemSettings['branding.government_name'] : 'Bolaang Mongondow' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="icon" href="/ico.ico" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r121/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanta@latest/dist/vanta.waves.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite('resources/css/idj.css')
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>

<style>

        .sidebar-transition {
            transition: width 0.3s ease-in-out, margin 0.3s ease-in-out;
        }
        .sidebar-text {
            white-space: nowrap;
            transition: opacity 0.2s ease-in-out;
        }
        .sidebar-section-label {
            color: #cbd5e1 !important;
            font-weight: 600;
            letter-spacing: .08em;
            opacity: .88;
        }
        .nav-link-locked {
            background: rgba(15, 23, 42, .46) !important;
            color: #94a3b8 !important;
        }

        .sidebar-hover-tooltip {
            position: fixed;
            z-index: 2000;
            max-width: min(240px, calc(100vw - 24px));
            padding: .55rem .8rem;
            border: 1px solid rgba(148, 163, 184, .28);
            border-left: 3px solid #3b82f6;
            border-radius: .7rem;
            color: #f8fafc;
            background: rgba(15, 23, 42, .96);
            box-shadow: 0 12px 30px rgba(2, 6, 23, .38);
            backdrop-filter: blur(12px);
            font-size: .8rem;
            font-weight: 600;
            line-height: 1.35;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transform: translateY(3px) scale(.98);
            transition: opacity .14s ease, transform .14s ease, visibility .14s ease;
        }

        .sidebar-hover-tooltip.is-visible {
            opacity: 1;
            visibility: visible;
            transform: translateY(0) scale(1);
        }

        @media (prefers-reduced-motion: reduce) {
            .sidebar-hover-tooltip {
                transition: none;
            }
        }
    </style>

</head>

<body x-data="{ sidebarOpen: true }">
<div id="vanta-bg" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;"></div>

<nav class="navbar navbar-expand-lg sticky-top py-2 navbar-glass">
    <div class="container-fluid px-4">

        <div class="d-flex align-items-center">
            @auth
            <button @click="sidebarOpen = !sidebarOpen" class="btn btn-link text-dark me-3 d-none d-md-block p-0 border-0" title="Toggle Sidebar">
                <i class="fas fa-bars fa-lg"></i>
            </button>
            @endauth
            <a class="navbar-brand fw-bold d-flex align-items-center me-0" href="#">
                @php
                    $applicationLogoSetting = $systemSettings['branding.application_logo'] ?? '';
                    $applicationLogoDisk = \Illuminate\Support\Facades\Storage::disk('public');
                    $applicationLogoUrl = filled($applicationLogoSetting) && $applicationLogoDisk->exists($applicationLogoSetting)
                        ? $applicationLogoDisk->url($applicationLogoSetting)
                        : (file_exists(public_path('logo_bolmong.png')) ? asset('logo_bolmong.png') : null);
                @endphp
                <div class="brand me-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                    @if($applicationLogoUrl)
                        <img src="{{ $applicationLogoUrl }}"
                            alt="{{ filled($systemSettings['branding.government_name'] ?? null) ? $systemSettings['branding.government_name'] : 'Lambang instansi' }}"
                            class="img-fluid object-fit-contain"
                            style="max-height: 100%; max-width: 100%; filter: drop-shadow(0px 2px 4px rgba(0,0,0,0.3));">
                    @endif
                    </div>
                    <div class="nav-divider me-2"></div>
                <div class="d-flex flex-column line-height-1 me-2">
                    <span class="brand-text mb-1">{{ filled($systemSettings['branding.product_name'] ?? null) ? $systemSettings['branding.product_name'] : 'SPJOnline' }}</span>
                    <small class="brand-subtext">{{ filled($systemSettings['branding.government_short_name'] ?? null) ? $systemSettings['branding.government_short_name'] : 'BOLMONG' }}</small>
                </div>
            </a>
        </div>

        <div class="d-flex align-items-center gap-3">
            @auth
@php
    $settingsService = app(\App\Services\SystemSettingsService::class);
    $user = auth()->user();
    $departmentId = $user->department_id;
    $waktuSekarang = \Carbon\CarbonImmutable::now($settingsService->timezone());
    $baRekon = $user->hasRole('operator') && $departmentId
        ? \App\Models\BaRekon::where('department_id', $departmentId)
            ->where('bulan', $waktuSekarang->month)
            ->where('tahun', $waktuSekarang->year)
            ->first()
        : null;
    $baRekonValid = $baRekon?->status === 'verified';
    $baRekonPending = $baRekon?->status === 'pending';
    $baRekonDitolak = $baRekon?->status === 'rejected';
    $isRevisableBaRekon = in_array($baRekon?->status, ['pending', 'rejected'], true);
    $baRekonUploadAvailability = $settingsService->baRekonUploadAvailability($waktuSekarang, $isRevisableBaRekon);
    if ($baRekon?->status === 'verified') {
        $baRekonUploadAvailability = [
            'allowed' => false,
            'message' => 'BA Rekon bulan ini sudah diverifikasi dan tidak dapat diunggah ulang.',
            'reason' => 'already_verified',
        ];
    }
    $submissionAvailability = $user->hasRole('operator')
        ? ($user->department
            ? $settingsService->submissionAvailability($user->department, $waktuSekarang, $baRekon?->status, true)
            : ['allowed' => false, 'reason' => 'department_missing', 'message' => 'Akun Anda belum terhubung ke SKPD manapun. Hubungi administrator.'])
        : ['allowed' => true, 'reason' => null, 'message' => null];
    $isTerkunci = !$submissionAvailability['allowed'];
@endphp

   @unless(auth()->user()->hasRole('bpk'))
   <div class="dropdown">
    @php
        $user = auth()->user();
        $unreadDbCount = $user->unreadNotifications->count();
        $tampilkanPeringatanAktif = $unreadDbCount > 0;
    @endphp

    <button id="notificationDropdownToggle"
            class="btn position-relative rounded-circle shadow-sm d-flex align-items-center justify-content-center border-0"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            style="width: 35px; height: 35px; background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);">

        <i id="bellIcon" class="fas fa-bell {{ $tampilkanPeringatanAktif ? 'text-primary bell-shake' : 'text-secondary' }}"
           style="font-size: {{ $tampilkanPeringatanAktif ? '1.1rem' : '0.95rem' }}; transition: all 0.2s ease;"></i>

        <span id="badgeWrapper">
            @if($tampilkanPeringatanAktif)
                <span id="notificationBadge" class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" style="width: 10px; height: 10px;">
                    <span class="visually-hidden">New alerts</span>
                </span>
            @endif
        </span>
    </button>

    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-3 mt-2 animate-dropdown"
        style="width: 380px; max-height: 480px; overflow-y: auto; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(16px);">

        <div class="d-flex justify-content-between align-items-center pb-3 mb-2 border-bottom border-light">
            <div class="d-flex align-items-center gap-2">
                <h6 class="fw-bold text-dark mb-0 text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">Pusat Notifikasi</h6>
                <span id="unreadCountBadge" class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2 py-1" style="font-size: 0.65rem; font-weight: 600;">
                    {{ $unreadDbCount }} Baru
                </span>
            </div>

            @if($unreadDbCount > 0)
                <form id="markAllReadForm" action="{{ route('notifications.readAll') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-link text-decoration-none text-primary fw-semibold p-0 border-0 bg-transparent" style="font-size: 0.7rem; transition: opacity 0.2s;">
                        <i class="fas fa-check-double me-1"></i>Tandai Semua Dibaca
                    </button>
                </form>
            @endif
        </div>

        <div id="notificationListContainer" class="d-flex flex-column gap-2 pt-1">
            @include('layouts.partials.notification-list')
        </div>
    </ul>
</div>
   @endunless

                <div class="dropdown">
                    <a class="nav-link d-flex align-items-center p-0 border-0" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar-wrapper position-relative">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=4e73df&color=fff"
                                class="rounded-circle border border-1 border-primary shadow-sm"
                                width="32" height="32">
                            <span class="status-indicator-online"></span>
                        </div>
                        <div class="ms-2 d-none d-lg-block">
                            <div class="fw-bold text-dark-blue leading-tight">{{ auth()->user()->name }}</div>
                            <div class="fw-sm text-dark-blue small opacity-75">Online</div>
                        </div>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end dropdown-glass border-0 shadow-lg mt-3 p-2">
                        <li class="px-3 py-3 mb-2 rounded-3 bg-white bg-opacity-25">
                            <a href="{{ route('profile.edit') }}" class="d-block text-decoration-none text-dark-blue" aria-label="Kelola profil dan keamanan akun">
                                <small class="d-block fw-bold tracking-wider mb-1" style="font-size: 0.55rem;">ACCOUNT</small>
                                <div class="fw-bold small text-truncate" style="max-width: 180px;">{{ auth()->user()->email }}</div>
                            </a>
                        </li>

                        @if(auth()->user()->isActualSuperAdmin())
                            <li class="px-3 pb-2 small text-muted">
                                Mode: {{ \App\Http\Controllers\AccessModeController::MODES[auth()->user()->accessMode()] ?? 'Belum dipilih' }}
                            </li>
                            <li>
                                <button type="button" class="dropdown-item rounded-3 d-flex align-items-center py-2" data-bs-toggle="modal" data-bs-target="#accessModeModal">
                                    <i class="fas fa-user-gear me-2 text-primary"></i>
                                    <span class="small">Ganti mode akses</span>
                                </button>
                            </li>
                        @endif

                        <li>
                            <form action="{{ route('logout') }}" method="POST" id="logout-form" class="no-ajax">
                                @csrf
                                <button type="submit" class="dropdown-item rounded-3 text-danger d-flex align-items-center py-2 transition-all no-ajax">
                                    <div class="icon-box-danger me-2">
                                        <i class="fas fa-power-off"></i>
                                    </div>
                                    <span class="fw-bold small">Keluar</span>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endauth
        </div>
    </div>
</nav>

@auth
    @if(auth()->user()->isActualSuperAdmin())
        <div class="modal fade" id="accessModeModal" tabindex="-1" aria-labelledby="accessModeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content bg-dark text-white border border-secondary rounded-4 shadow-lg">
                    <form method="POST" action="{{ route('access-mode.update') }}">
                        @csrf
                        <div class="modal-header border-secondary">
                            <h5 class="modal-title fs-6 fw-bold" id="accessModeModalLabel"><i class="fas fa-user-gear text-primary me-2"></i>Ganti Mode Akses</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body">
                            <p class="small text-white-50">Pilih menu dan kewenangan yang ingin dibuka. Role akun Master tetap tidak berubah.</p>
                            <label for="accessModeSelect" class="form-label small">Mode akses</label>
                            <select class="form-select bg-dark text-white border-secondary" id="accessModeSelect" name="access_mode" required>
                                @foreach(\App\Http\Controllers\AccessModeController::MODES as $mode => $label)
                                    <option value="{{ $mode }}" @selected(auth()->user()->accessMode() === $mode)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="modal-footer border-secondary">
                            <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary btn-sm">Terapkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endauth

@yield('breadcrumbs')

<div class="container-fluid overflow-hidden">
    <div class="row g-0">
        @auth

        <nav class="col-md-3 col-lg-2 d-none d-md-block sidebar sidebar-dark sidebar-transition @hasanyrole('super_admin|bpk') sidebar-dark @endhasanyrole @role('bpk') sidebar-bpk @endrole"
             id="sidebarDesktop"
             :style="sidebarOpen ? 'width: 16.666667%;' : 'width: 80px; overflow: hidden;'">
            <div class="px-3">
                <ul class="nav flex-column mb-4">

                    <li class="nav-item">
                        @role('super_admin')
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}" :data-sidebar-tooltip="!sidebarOpen ? 'Dashboard Admin' : null" :aria-label="!sidebarOpen ? 'Dashboard Admin' : null">
                                <i class="fas fa-tachometer-alt me-2"></i> <span class="sidebar-text" x-show="sidebarOpen">Dashboard Admin</span>
                            </a>
                        @elserole('bpk')
                            <a class="nav-link {{ request()->routeIs('bpk.dashboard') ? 'active' : '' }}" href="{{ route('bpk.dashboard') }}" :data-sidebar-tooltip="!sidebarOpen ? 'Dashboard Audit' : null" :aria-label="!sidebarOpen ? 'Dashboard Audit' : null">
                                <i class="fas fa-chart-line me-2"></i> <span class="sidebar-text" x-show="sidebarOpen">Dashboard Audit</span>
                            </a>
                        @else
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}" :data-sidebar-tooltip="!sidebarOpen ? 'Dashboard Utama' : null" :aria-label="!sidebarOpen ? 'Dashboard Utama' : null">
                                <i class="fas fa-home me-2"></i> <span class="sidebar-text" x-show="sidebarOpen">Dashboard Utama</span>
                            </a>
                        @endrole
                    </li>

@role('operator')

    @if(!$baRekonValid)
        <li class="nav-item">
            @if($baRekonPending)

                <a class="nav-link text-muted bg-light" href="#" style="cursor: not-allowed; opacity: 0.8;" :data-sidebar-tooltip="!sidebarOpen ? 'Menunggu Verifikasi BA Rekon' : null" :aria-label="!sidebarOpen ? 'Menunggu Verifikasi BA Rekon' : null">
                    <i class="fas fa-spinner fa-spin me-2 text-warning"></i>
                    <span class="sidebar-text" x-show="sidebarOpen">Menunggu Verifikasi</span>

                </a>
            @elseif($baRekonDitolak)

                <a class="nav-link border border-danger bg-danger bg-opacity-10 text-danger {{ request()->routeIs('ba-rekon.*') ? 'active' : '' }}" href="#" data-bs-toggle="modal" data-bs-target="#modalUploadBaRekon" :data-sidebar-tooltip="!sidebarOpen ? 'BA Rekon Ditolak' : null" :aria-label="!sidebarOpen ? 'BA Rekon Ditolak' : null">
                    <i class="fas fa-exclamation-circle me-2 text-danger"></i>
                   <span class="sidebar-text fw-bold text-white" x-show="sidebarOpen">BA Rekon Ditolak</span>

                </a>
            @else

                <a class="nav-link {{ request()->routeIs('ba-rekon.*') ? 'active' : '' }}" href="#" data-bs-toggle="modal" data-bs-target="#modalUploadBaRekon" :data-sidebar-tooltip="!sidebarOpen ? 'Upload BA Rekon' : null" :aria-label="!sidebarOpen ? 'Upload BA Rekon' : null">
                    <i class="fas fa-file-contract fs-6 me-2 text-warning"></i>
                    <span class="sidebar-text text-warning" x-show="sidebarOpen" style="opacity: 1; animation: placeholder-glow 2s ease-in-out infinite;">Upload BA Rekon</span>
                </a>
            @endif
        </li>
    @endif

    @if(!$isTerkunci)
        <li class="nav-item">
            <a class="nav-link {{ request()->is('submissions/create') ? 'active' : '' }}" href="{{ route('submissions.create') }}" :data-sidebar-tooltip="!sidebarOpen ? 'Upload SPP / SPM' : null" :aria-label="!sidebarOpen ? 'Upload SPP / SPM' : null">
                <i class="fas fa-cloud-upload-alt me-2"></i> <span class="sidebar-text" x-show="sidebarOpen">Pengajuan Transaksi</span>
            </a>
        </li>
    @else
        <li class="nav-item">
            <a class="nav-link nav-link-locked"
               href="#"
               data-denial-message="{{ $submissionAvailability['message'] }}"
               onclick="return showSubmissionAccessDenied(this.dataset.denialMessage)"
               style="cursor: not-allowed;"
               :data-sidebar-tooltip="!sidebarOpen ? 'Pengajuan Transaksi' : null" :aria-label="!sidebarOpen ? 'Pengajuan Transaksi' : null">

                <i class="fas fa-lock me-2 text-danger"></i>

                <span class="sidebar-text" x-show="sidebarOpen">
                    Pengajuan Transaksi
                </span>

            </a>
        </li>
    @endif

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('submissions.sp2d-upload') ? 'active' : '' }}" href="{{ route('submissions.sp2d-upload') }}" :data-sidebar-tooltip="!sidebarOpen ? 'SP2D Perlu Diunggah' : null" :aria-label="!sidebarOpen ? 'SP2D Perlu Diunggah' : null">
            <i class="fas fa-cloud-arrow-up me-2"></i> <span class="sidebar-text" x-show="sidebarOpen">Upload SP2D</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ in_array(request()->route()?->getName(), ['submissions.index', 'submissions.show'], true) && !request()->routeIs('submissions.sp2d-upload') ? 'active' : '' }}" href="{{ route('submissions.index') }}" :data-sidebar-tooltip="!sidebarOpen ? 'History Pengajuan' : null" :aria-label="!sidebarOpen ? 'History Pengajuan' : null">
            <i class="fas fa-history me-2"></i> <span class="sidebar-text" x-show="sidebarOpen">History Pengajuan</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('ba-rekon.archive*') ? 'active' : '' }}" href="{{ route('ba-rekon.archive') }}" :data-sidebar-tooltip="!sidebarOpen ? 'Arsip BA Rekon' : null" :aria-label="!sidebarOpen ? 'Arsip BA Rekon' : null">
            <i class="fas fa-calendar-check me-2"></i>
            <span class="sidebar-text" x-show="sidebarOpen">Arsip BA Rekon</span>
        </a>
    </li>
@endrole

                    @role('kasie_bud')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('verifikasi.index') || (request()->routeIs('verifikasi.show') && request('source') !== 'sp2d') ? 'active' : '' }}" href="{{ route('verifikasi.index') }}" :data-sidebar-tooltip="!sidebarOpen ? 'Verifikasi Permintaan' : null" :aria-label="!sidebarOpen ? 'Verifikasi Permintaan' : null">
                            <i class="fas fa-check-double me-2"></i> <span class="sidebar-text" x-show="sidebarOpen">Verifikasi Permintaan</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('verifikasi.sp2d') || (request()->routeIs('verifikasi.show') && request('source') === 'sp2d') ? 'active' : '' }}" href="{{ route('verifikasi.sp2d') }}" :data-sidebar-tooltip="!sidebarOpen ? 'Verifikasi SP2D' : null" :aria-label="!sidebarOpen ? 'Verifikasi SP2D' : null">
                            <i class="fas fa-file-invoice-dollar me-2"></i> <span class="sidebar-text" x-show="sidebarOpen">Verifikasi SP2D</span>
                        </a>
                    </li>
                    @endrole

                    @role('kabid_perbendaharaan')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('validasi*') ? 'active' : '' }}" href="{{ route('validasi.index') }}" :data-sidebar-tooltip="!sidebarOpen ? 'Validasi Permintaan' : null" :aria-label="!sidebarOpen ? 'Validasi Permintaan' : null">
                            <i class="fas fa-file-signature me-2"></i> <span class="sidebar-text" x-show="sidebarOpen">Validasi Permintaan</span>
                        </a>
                    </li>
                    @endrole

                    @role('super_admin')
                    <li class="nav-item border-top mt-3 pt-3" x-show="sidebarOpen">
                        <small class="sidebar-section-label ms-3 text-uppercase" style="font-size: 0.7rem;">Data Master</small>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.monitoring') ? 'active' : '' }}" href="{{ route('admin.monitoring') }}" :data-sidebar-tooltip="!sidebarOpen ? 'Monitoring Global' : null" :aria-label="!sidebarOpen ? 'Monitoring Global' : null">
                            <i class="fas fa-desktop me-2"></i> <span class="sidebar-text" x-show="sidebarOpen">Monitoring Global</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('users.*') ? 'active fw-bold' : '' }}" href="{{ route('users.index') }}" :data-sidebar-tooltip="!sidebarOpen ? 'Manajemen User' : null" :aria-label="!sidebarOpen ? 'Manajemen User' : null">
                            <i class="fas fa-users me-2"></i> <span class="sidebar-text" x-show="sidebarOpen">Manajemen User</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('departments.*') ? 'active fw-bold' : '' }}" href="{{ route('departments.index') }}" :data-sidebar-tooltip="!sidebarOpen ? 'Manajemen SKPD' : null" :aria-label="!sidebarOpen ? 'Manajemen SKPD' : null">
                            <i class="fas fa-building me-2"></i> <span class="sidebar-text" x-show="sidebarOpen">Manajemen SKPD</span>
                        </a>
                    </li>
                    @endrole

                    @hasanyrole('kabid_perbendaharaan|kasie_bud|super_admin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('history*') ? 'active' : '' }}" href="{{ route('history.index') }}" :data-sidebar-tooltip="!sidebarOpen ? 'Arsip Dokumen' : null" :aria-label="!sidebarOpen ? 'Arsip Dokumen' : null">
                            <i class="fas fa-align-right me-2"></i>
                            <span class="sidebar-text" x-show="sidebarOpen">Arsip Permintaan</span>
                        </a>
                    </li>
                    @endhasanyrole

                    @hasanyrole('kasie_bud|kabid_perbendaharaan')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('departments.*') ? 'active fw-bold' : '' }}" href="{{ route('departments.index') }}" :data-sidebar-tooltip="!sidebarOpen ? 'Monitoring BA Rekon' : null" :aria-label="!sidebarOpen ? 'Monitoring BA Rekon' : null">
                            <i class="fas fa-building me-2"></i>
                            <span class="sidebar-text" x-show="sidebarOpen">Monitoring BA Rekon</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('ba-rekon.archive*') ? 'active' : '' }}" href="{{ route('ba-rekon.archive') }}" :data-sidebar-tooltip="!sidebarOpen ? 'Arsip BA Rekon' : null" :aria-label="!sidebarOpen ? 'Arsip BA Rekon' : null">
                            <i class="fas fa-calendar-check me-2"></i>
                            <span class="sidebar-text" x-show="sidebarOpen">Arsip BA Rekon</span>
                        </a>
                    </li>
                    @endhasanyrole

                    @if(Auth::user()->hasRole('bpk'))
                        <li class="nav-item">
                            <a class="nav-link {{ Route::is('bpk.monitoring', 'bpk.show') ? 'active' : '' }}" href="{{ route('bpk.monitoring') }}" :data-sidebar-tooltip="!sidebarOpen ? 'Monitoring Berkas' : null" :aria-label="!sidebarOpen ? 'Monitoring Berkas' : null">
                                <i class="fas fa-file-invoice me-2"></i> <span class="sidebar-text" x-show="sidebarOpen">Monitoring Permintaan</span>
                            </a>
                        </li>
                    @endif
                    @role('super_admin')
                    <li class="nav-item border-top mt-3 pt-3">
                        <a class="nav-link {{ request()->routeIs('settings.*') ? 'active fw-bold' : '' }}" href="{{ route('settings.index') }}" :data-sidebar-tooltip="!sidebarOpen ? 'Master Settings' : null" :aria-label="!sidebarOpen ? 'Master Settings' : null">
                            <i class="fas fa-sliders-h me-2"></i> <span class="sidebar-text" x-show="sidebarOpen">Master Settings</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.error-reports.*') ? 'active fw-bold' : '' }}" href="{{ route('admin.error-reports.index') }}" :data-sidebar-tooltip="!sidebarOpen ? 'Laporan Error' : null" :aria-label="!sidebarOpen ? 'Laporan Error' : null">
                            <i class="fas fa-bug me-2"></i> <span class="sidebar-text" x-show="sidebarOpen">Laporan Error</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.activity.*') ? 'active fw-bold' : '' }}" href="{{ route('admin.activity.index') }}" :data-sidebar-tooltip="!sidebarOpen ? 'Aktivitas Pengguna' : null" :aria-label="!sidebarOpen ? 'Aktivitas Pengguna' : null">
                            <i class="fas fa-user-clock me-2"></i> <span class="sidebar-text" x-show="sidebarOpen">Aktivitas Pengguna</span>
                        </a>
                    </li>
                    @endrole
                </ul>
            </div>
        </nav>

        <div class="offcanvas offcanvas-start @hasanyrole('super_admin|bpk') sidebar-dark @endhasanyrole @role('bpk') sidebar-bpk @endrole" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title fw-semibold" id="sidebarOffcanvasLabel">
                    <i class="fas fa-file-invoice-dollar me-2"></i> iDJ Navigation
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body px-3">
                <ul class="nav flex-column mb-4">

                    <li class="nav-item">
                        @role('super_admin')
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                                <i class="fas fa-tachometer-alt me-2"></i> Dashboard Admin
                            </a>
                        @elserole('bpk')
                            <a class="nav-link {{ request()->routeIs('bpk.dashboard') ? 'active' : '' }}" href="{{ route('bpk.dashboard') }}">
                                <i class="fas fa-chart-line me-2"></i> Dashboard Audit
                            </a>
                        @else
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                <i class="fas fa-home me-2"></i> Dashboard Utama
                            </a>
                        @endrole
                    </li>

                    @role('operator')
                    <li class="nav-item border-top mt-2 pt-2">
                        <small class="text-muted ms-3 text-uppercase" style="font-size: 0.7rem;">Menu Operator</small>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('ba-rekon.*') ? 'active' : '' }}" href="#" data-bs-toggle="modal" data-bs-target="#modalUploadBaRekon">
                            <i class="fas fa-file-contract me-2 text-info"></i> Upload BA Rekon
                        </a>
                    </li>
                    @if(!$isTerkunci)
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('submissions/create') ? 'active' : '' }}" href="{{ route('submissions.create') }}">
                            <i class="fas fa-cloud-upload-alt me-2"></i> Pengajuan Transaksi
                        </a>
                    </li>
                    @else
                    <li class="nav-item">
                        <a class="nav-link nav-link-locked"
                           href="#"
                           data-denial-message="{{ $submissionAvailability['message'] }}"
                           onclick="return showSubmissionAccessDenied(this.dataset.denialMessage)"
                           style="cursor: not-allowed;">

                            <i class="fas fa-lock me-2 text-danger"></i>
                            Pengajuan Transaksi

                        </a>
                    </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('submissions.sp2d-upload') ? 'active' : '' }}" href="{{ route('submissions.sp2d-upload') }}">
                            <i class="fas fa-cloud-arrow-up me-2"></i> SP2D Perlu Diunggah
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ in_array(request()->route()?->getName(), ['submissions.index', 'submissions.show'], true) && !request()->routeIs('submissions.sp2d-upload') ? 'active' : '' }}" href="{{ route('submissions.index') }}">
                            <i class="fas fa-history me-2"></i> History Pengajuan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('ba-rekon.archive*') ? 'active' : '' }}" href="{{ route('ba-rekon.archive') }}">
                            <i class="fas fa-calendar-check me-2"></i> Arsip BA Rekon
                        </a>
                    </li>
                    @endrole

                    @role('kasie_bud')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('verifikasi.index') || (request()->routeIs('verifikasi.show') && request('source') !== 'sp2d') ? 'active' : '' }}" href="{{ route('verifikasi.index') }}">
                            <i class="fas fa-check-double me-2"></i> Verifikasi Permintaan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('verifikasi.sp2d') || (request()->routeIs('verifikasi.show') && request('source') === 'sp2d') ? 'active' : '' }}" href="{{ route('verifikasi.sp2d') }}">
                            <i class="fas fa-file-invoice-dollar me-2"></i> Verifikasi SP2D
                        </a>
                    </li>
                    @endrole

                    @role('kabid_perbendaharaan')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('validasi*') ? 'active' : '' }}" href="{{ route('validasi.index') }}">
                            <i class="fas fa-file-signature me-2"></i> Validasi Permintaan
                        </a>
                    </li>
                    @endrole

                    @role('super_admin')
                    <li class="nav-item border-top mt-3 pt-3">
                        <small class="sidebar-section-label ms-3 text-uppercase" style="font-size: 0.7rem;">Data Master</small>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.monitoring') ? 'active' : '' }}" href="{{ route('admin.monitoring') }}">
                            <i class="fas fa-desktop me-2"></i> Monitoring Global
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('users.*') ? 'active fw-bold' : '' }}" href="{{ route('users.index') }}">
                            <i class="fas fa-users me-2"></i> Manajemen User
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('departments.*') ? 'active fw-bold' : '' }}" href="{{ route('departments.index') }}">
                            <i class="fas fa-building me-2"></i> Manajemen SKPD
                        </a>
                    </li>
                    @endrole

                    @hasanyrole('kabid_perbendaharaan|kasie_bud|super_admin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('history*') ? 'active' : '' }}" href="{{ route('history.index') }}">
                            <i class="fas fa-align-right me-2"></i>
                            <span>Arsip Pengajuan</span>
                        </a>
                    </li>
                    @endhasanyrole

                    @hasanyrole('kasie_bud|kabid_perbendaharaan')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('departments.*') ? 'active fw-bold' : '' }}" href="{{ route('departments.index') }}">
                            <i class="fas fa-building me-2"></i> Monitoring BA Rekon
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('ba-rekon.archive*') ? 'active' : '' }}" href="{{ route('ba-rekon.archive') }}">
                            <i class="fas fa-calendar-check me-2"></i> Arsip BA Rekon
                        </a>
                    </li>
                    @endhasanyrole

                    @if(Auth::user()->hasRole('bpk'))
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('bpk.monitoring') || request()->routeIs('bpk.show') ? 'active' : '' }}" href="{{ route('bpk.monitoring') }}">
                                <i class="fas fa-file-invoice me-2"></i> Monitoring Berkas
                            </a>
                        </li>
                    @endif
                    @role('super_admin')
                    <li class="nav-item border-top mt-3 pt-3">
                        <a class="nav-link {{ request()->routeIs('settings.*') ? 'active fw-bold' : '' }}" href="{{ route('settings.index') }}">
                            <i class="fas fa-sliders-h me-2"></i> Master Settings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.error-reports.*') ? 'active fw-bold' : '' }}" href="{{ route('admin.error-reports.index') }}">
                            <i class="fas fa-bug me-2"></i> Laporan Error
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.activity.*') ? 'active fw-bold' : '' }}" href="{{ route('admin.activity.index') }}">
                            <i class="fas fa-user-clock me-2"></i> Aktivitas Pengguna
                        </a>
                    </li>
                    @endrole
                </ul>
            </div>
        </div>
        @endauth

        <main class="col-9 ps-3 pe-5 {{ Auth::check() ? 'col-md-9 col-lg-10' : '' }} min-vh-100"
            :style="sidebarOpen ? 'margin-left: {{ Auth::check() ? '16.666667%' : '0' }};' : 'margin-left: 80px; width: calc(100% - 80px);'"
            style="transition: all 0.3s;">
            <div class="container-fluid py-3">
                <div id="alert-container" class="mx-3">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                </div>

                <div id="main-content">
                    @yield('content')
                </div>
            </div>
        </main>
    </div>
</div>

{{-- Modal: Unggah Berita Acara Rekonsiliasi --}}
<div class="modal fade" id="modalUploadBaRekon" tabindex="-1" aria-labelledby="modalUploadBaRekonLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('ba-rekon.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold" id="modalUploadBaRekonLabel">
                        <i class="fas fa-file-contract text-info me-2"></i>
                        {{ isset($baRekon) && $baRekon->status === 'rejected' ? 'Upload Ulang BA Rekon' : 'Upload Berita Acara Rekonsiliasi (BA Rekon)' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    @unless($baRekonUploadAvailability['allowed'])
                        <div class="alert alert-warning py-2 small">{{ $baRekonUploadAvailability['message'] }}</div>
                    @endunless
                    @if(($baRekonUploadAvailability['reason'] ?? null) === 'correction_outside_upload_period')
                        <div class="alert alert-info py-2 small">{{ $baRekonUploadAvailability['message'] }}</div>
                    @endif
                    @if(isset($baRekon) && $baRekon->status === 'rejected')
                        <div class="alert alert-danger py-2 small">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <strong>Dokumen BA Rekon Anda sebelumnya ditolak!</strong> Silakan unggah ulang perbaikannya. Akses pengajuan terbuka setelah BA Rekon diverifikasi.

                            @if(!empty($baRekon->catatan))
                                <div class="mt-2 text-dark">
                                    <strong>Catatan Penolakan:</strong> {{ $baRekon->catatan }}
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="alert alert-info py-2 small">
                            <i class="fas fa-info-circle me-1"></i>
                            Upload dokumen BA Rekonsiliasi untuk bulan aktif guna mengakses fitur Pengajuan Transaksi SKPD Anda.
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="file_ba" class="form-label fw-semibold">Pilih File BA Rekon (PDF)</label>
                        <input type="file" class="form-control @error('file_ba') is-invalid @enderror" id="file_ba" name="file_ba" accept=".pdf" required @disabled(!$baRekonUploadAvailability['allowed'])>
                        <div class="form-text text-muted">Format file harus PDF dengan ukuran maksimal 2MB.</div>
                        @error('file_ba')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm" @disabled(!$baRekonUploadAvailability['allowed'])>
                        <i class="fas fa-upload me-1"></i>
                        {{ isset($baRekon) && $baRekon->status === 'rejected' ? 'Kirim Perbaikan' : 'Upload Dokumen' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.updateActiveLink === 'function') {
        window.updateActiveLink();
    }
    window.addEventListener('popstate', function() {
        if (typeof window.updateActiveLink === 'function') {
            window.updateActiveLink();
        }
    });

    document.addEventListener('submit', function(e) {
        const form = e.target.closest('form[data-confirm-delete]');
        if (!form) return;

        e.preventDefault();
        const message = form.getAttribute('data-confirm-delete') || 'Apakah Anda yakin ingin menghapus data ini?';

        Swal.fire({
            title: 'Konfirmasi Penghapusan',
            html: `<div class="text-muted">${message}</div>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#ff9aa2',
            allowOutsideClick: false
        }).then((result) => {
            if (result.isConfirmed) form.submit();
        });
    });

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('button[data-confirm]');
        if (!btn) return;

        e.preventDefault();
        const message = btn.getAttribute('data-confirm');
        const formId = btn.getAttribute('data-form-id');

        Swal.fire({
            title: 'Konfirmasi',
            text: message,
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Ya, Lanjutkan',
            confirmButtonColor: '#b3e5fc'
        }).then((result) => {
            if (result.isConfirmed && formId) {
                const targetForm = document.getElementById(formId);
                if (targetForm) targetForm.submit();
            }
        });
    });

    const successAlert = document.querySelector('.alert-success');
    if (successAlert) {
        setTimeout(function() {
            successAlert.style.transition = "opacity 0.8s ease";
            successAlert.style.opacity = "0";
            setTimeout(function() {
                successAlert.remove();
            }, 800);
        }, 5000);
    }
});

// Function: Menginisialisasi latar animasi Vanta
function initVanta() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    if (typeof VANTA !== 'undefined' && typeof THREE !== 'undefined') {
        VANTA.WAVES({
            el: "#vanta-bg",
            mouseControls: true,
            touchControls: true,
            gyroControls: true,
            minHeight: 200.00,
            minWidth: 200.00,
            scale: 1.00,
            scaleMobile: 1.00,
            color: 0x5c5c5c,
            shininess: 30.00,
            waveHeight: 15.00,
            waveSpeed: 0.80,
            zoom: 1.00
        });
    } else {
        setTimeout(initVanta, 100);
    }
}
window.addEventListener('load', initVanta);

document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
});

document.addEventListener('DOMContentLoaded', function() {
    const policyTimeZone = @json($policyTimezone);
    const policyTimeZoneLabel = @json($policyTimezoneLabel);

    function startLivePolicyTime() {
        const timeElement = document.getElementById('liveDateTimePolicy');
        if (!timeElement) return;

        function update() {
            const now = new Date();
            const options = {
                weekday: 'long', day: 'numeric', month: 'long', year: 'numeric',
                timeZone: policyTimeZone, hour12: false,
                hour: '2-digit', minute: '2-digit', second: '2-digit'
            };

            const formatter = new Intl.DateTimeFormat('id-ID', options);
            const parts = formatter.formatToParts(now);

            const dateStr = `${parts.find(p => p.type === 'weekday').value}, ${parts.find(p => p.type === 'day').value} ${parts.find(p => p.type === 'month').value} ${parts.find(p => p.type === 'year').value}`;
            const timeStr = `${parts.find(p => p.type === 'hour').value}:${parts.find(p => p.type === 'minute').value}:${parts.find(p => p.type === 'second').value}`;

            timeElement.innerText = `${dateStr} | ${timeStr} ${policyTimeZoneLabel}`;
        }
        setInterval(update, 1000);
        update();
    }
    startLivePolicyTime();
});

let isExpanded = true;
// Function: Mengubah tampilan grafik antara ringkas dan luas
function toggleExpand() {
    const chartCol = document.getElementById('chartColumn');
    const sidebarCol = document.getElementById('sidebarColumn');
    const chartWrapper = document.getElementById('chartWrapper');
    const btnIcon = document.querySelector('#expandBtn i');

    if (!chartCol || !sidebarCol) return;

    if (isExpanded) {
        chartCol.classList.replace('col-xl-12', 'col-xl-8');
        sidebarCol.classList.replace('col-xl-12', 'col-xl-4');
        if(chartWrapper) chartWrapper.style.height = '350px';
        if(btnIcon) btnIcon.classList.replace('fa-compress-alt', 'fa-expand-alt');
        isExpanded = false;
    } else {
        chartCol.classList.replace('col-xl-8', 'col-xl-12');
        sidebarCol.classList.replace('col-xl-4', 'col-xl-12');
        if(chartWrapper) chartWrapper.style.height = '400px';
        if(btnIcon) btnIcon.classList.replace('fa-expand-alt', 'fa-compress-alt');
        isExpanded = true;
    }

    setTimeout(() => {
        if (typeof monthlyChart !== 'undefined' && monthlyChart !== null) {
            monthlyChart.resize();
        }
    }, 400);
}

$(document).ready(function() {
    // Function: Memperbarui penanda navigasi aktif
    window.updateActiveLink = function() {
        const currentPath = window.location.pathname.toLowerCase().replace(/\/$/, "");

        $('#sidebarDesktop .nav-link, #sidebarOffcanvas .nav-link').each(function() {
            const href = $(this).attr('href');
            if (!href || href === '#' || href.startsWith('javascript')) return;

            let linkPath = '';
            try {
                linkPath = new URL(href, window.location.origin).pathname.toLowerCase().replace(/\/$/, "");
            } catch (e) { return; }

            $(this).removeClass('active fw-bold');

            let isMatch = false;
            if (linkPath === '' || linkPath === '/' || linkPath.includes('dashboard')) {
                isMatch = (currentPath === linkPath || currentPath === linkPath + '/dashboard');
            } else if (linkPath.includes('/audit') || currentPath.includes('/audit')) {
                if (linkPath.includes('monitoring') && currentPath.includes('monitoring')) {
                    isMatch = true;
                } else if (linkPath.includes('monitoring') && currentPath.includes('/audit/detail')) {
                    isMatch = true;
                } else {
                    isMatch = currentPath.startsWith(linkPath);
                }
            } else if (linkPath === '/verifikasi') {
                const source = new URLSearchParams(window.location.search).get('source');
                isMatch = currentPath === linkPath || (
                    currentPath.startsWith(linkPath + '/') &&
                    currentPath !== linkPath + '/sp2d' &&
                    source !== 'sp2d'
                );
            } else if (linkPath === '/verifikasi/sp2d') {
                const source = new URLSearchParams(window.location.search).get('source');
                isMatch = currentPath === linkPath || (
                    currentPath.startsWith('/verifikasi/') && source === 'sp2d'
                );
            } else if (linkPath === '/submissions') {
                isMatch = currentPath === linkPath || (
                    currentPath.startsWith(linkPath + '/') &&
                    currentPath !== linkPath + '/sp2d-upload'
                );
            } else {
                isMatch = currentPath.startsWith(linkPath);
            }

            if (isMatch) {
                $(this).addClass('active fw-bold');
                const parentCollapse = $(this).closest('.collapse');
                if (parentCollapse.length) {
                    parentCollapse.addClass('show');
                }
            }
        });
    };
    window.updateActiveLink();
});

document.addEventListener('click', function (e) {
    const link = e.target.closest('.pagination a, .glass-pagination a');
    if (!link) return;

    e.preventDefault();
    const url = link.getAttribute('href');
    if (!url || url === '#') return;

    const container = link.closest('#monitoring-container')
        || link.closest('#users-table-container')
        || link.closest('.card');
    if (!container) return;

    container.style.transition = 'opacity 0.2s ease-in-out, transform 0.2s ease-in-out';
    const currentHeight = container.offsetHeight;
    container.style.height = currentHeight + 'px';
    container.style.overflow = 'hidden';

    container.style.opacity = '0';
    container.style.transform = 'translateY(8px)';

    setTimeout(() => {
        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const selector = container.id ? `#${container.id}` : '.card';
            const newContent = doc.querySelector(selector);

            if (newContent) {
                container.innerHTML = newContent.innerHTML;
                window.history.pushState({}, '', url);

                window.scrollTo({ top: 0, behavior: 'smooth' });

                if (typeof window.updateActiveLink === 'function') {
                    window.updateActiveLink();
                }

                const newTooltips = container.querySelectorAll('[data-bs-toggle="tooltip"]');
                if (newTooltips.length && typeof bootstrap !== 'undefined') {
                    [...newTooltips].map(t => new bootstrap.Tooltip(t));
                }

                container.style.transition = 'none';
                container.style.opacity = '0';
                container.style.transform = 'translateY(-8px)';
                container.style.height = 'auto';
                container.style.overflow = 'visible';

                setTimeout(() => {
                    container.style.transition = 'opacity 0.25s ease-out, transform 0.25s ease-out';
                    container.style.opacity = '1';
                    container.style.transform = 'translateY(0)';
                }, 30);
            } else {
                window.location.href = url;
            }
        })
        .catch(() => {
            window.location.href = url;
        });
    }, 200);
});
</script>







@unless(auth()->user()->hasRole('bpk'))
<script>
document.addEventListener('DOMContentLoaded', function () {
    let lastUnreadCount = {{ $unreadDbCount ?? 0 }};
    const dropdownElement = document.getElementById('notificationDropdownToggle');
    const bellIcon = document.getElementById('bellIcon');
    const badgeWrapper = document.getElementById('badgeWrapper');
    const unreadCountBadge = document.getElementById('unreadCountBadge');
    const listContainer = document.getElementById('notificationListContainer');
    const markAllForm = document.getElementById('markAllReadForm');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    let markingRead = false;
    let pollVersion = 0;

    function clearUnreadIndicator() {
        lastUnreadCount = 0;

        if (bellIcon) {
            bellIcon.classList.remove('bell-shake', 'text-primary');
            bellIcon.classList.add('text-secondary');
            bellIcon.style.fontSize = '0.95rem';
        }

        document.getElementById('notificationBadge')?.remove();

        if (unreadCountBadge) {
            unreadCountBadge.textContent = '0 Baru';
        }

        document.querySelectorAll('.notification-item').forEach(item => {
            item.classList.remove('bg-white', 'border', 'border-primary-subtle', 'shadow-xs');
            item.classList.add('bg-light', 'opacity-75');
            item.querySelector('.badge.bg-primary')?.remove();
        });

        markAllForm?.remove();
    }

    function markNotificationsAsRead() {
        if (markingRead || lastUnreadCount === 0) return;

        markingRead = true;
        pollVersion++;
        clearUnreadIndicator();
        fetch("{{ route('notifications.readAll') }}", {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Gagal menandai notifikasi sebagai dibaca.');
        })
        .catch(error => console.error(error))
        .finally(() => { markingRead = false; });
    }

    if (dropdownElement) {
        dropdownElement.addEventListener('shown.bs.dropdown', markNotificationsAsRead);
    }

    if (markAllForm) {
        markAllForm.addEventListener('submit', function (e) {
            e.preventDefault();
            markNotificationsAsRead();
        });
    }

    // Function: Memeriksa notifikasi baru secara berkala
    function checkNewNotifications() {
        const requestVersion = pollVersion;

        fetch("{{ route('notifications.fetch') }}")
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (requestVersion !== pollVersion || markingRead) return;

                if (listContainer) {
                    listContainer.innerHTML = data.htmlList;
                }

                if (data.unreadCount === 0) {
                    clearUnreadIndicator();
                } else if (data.unreadCount > lastUnreadCount) {
                    if (unreadCountBadge) {
                        unreadCountBadge.textContent = data.unreadCount + ' Baru';
                    }

                    if (bellIcon) {
                        bellIcon.classList.remove('text-secondary');
                        bellIcon.classList.add('text-primary', 'bell-shake');
                        bellIcon.style.fontSize = '1.1rem';
                    }

                    if (badgeWrapper) {
                        let existingBadge = document.getElementById('notificationBadge');
                        if (!existingBadge) {
                            badgeWrapper.innerHTML = `
                                <span id="notificationBadge" class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" style="width: 10px; height: 10px;">
                                    <span class="visually-hidden">New alerts</span>
                                </span>
                            `;
                        } else {
                            existingBadge.style.display = 'block';
                        }
                    }
                    lastUnreadCount = data.unreadCount;
                } else if (unreadCountBadge) {
                    unreadCountBadge.textContent = data.unreadCount + ' Baru';
                }
            })
            .catch(error => console.error('Error fetching notifications:', error));
    }

    setInterval(checkNewNotifications, 10000);
});
</script>
@endunless







<style>
@keyframes smooth-shake {
    0% { transform: rotate(0deg); }
    15% { transform: rotate(14deg); }
    30% { transform: rotate(-12deg); }
    45% { transform: rotate(8deg); }
    60% { transform: rotate(-6deg); }
    75% { transform: rotate(3deg); }
    100% { transform: rotate(0deg); }
}

.bell-shake {
    animation: smooth-shake 1.8s ease-in-out infinite;
    transform-origin: top center;
}
</style>



<style>
    .notification-item:hover {
        transform: translateY(-1px);
        background-color: #f8f9fa !important;
    }
        background: rgba(255, 255, 255, 1) !important;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
    }
</style>



<style>
    .swal-dark-popup {
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 14px;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.45);
}

.swal-dark-title {
    color: #f8fafc !important;
    font-weight: 600 !important;
}

.swal-dark-text {
    color: #cbd5e1 !important;
}

.swal-dark-button {
    border-radius: 8px !important;
    padding: 9px 24px !important;
    font-weight: 600 !important;
}
</style>

<script>
function showSubmissionAccessDenied(message) {
    Swal.fire({
        icon: 'error',
        title: 'Akses Ditolak',
        text: message,
        confirmButtonText: 'OK',
        background: '#1e293b',
        color: '#f8fafc',
        iconColor: '#ef4444',
        confirmButtonColor: '#1d4ed8',
        customClass: {
            popup: 'swal-dark-popup',
            title: 'swal-dark-title',
            htmlContainer: 'swal-dark-text',
            confirmButton: 'swal-dark-button'
        }
    });
    return false;
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tooltip = document.createElement('div');
    tooltip.id = 'sidebar-hover-tooltip';
    tooltip.className = 'sidebar-hover-tooltip';
    tooltip.setAttribute('role', 'tooltip');
    document.body.appendChild(tooltip);

    let activeLink = null;

    function hideSidebarTooltip() {
        if (activeLink) {
            activeLink.removeAttribute('aria-describedby');
        }
        activeLink = null;
        tooltip.classList.remove('is-visible');
    }

    function showSidebarTooltip(link) {
        const label = link?.getAttribute('data-sidebar-tooltip');
        if (!label) {
            hideSidebarTooltip();
            return;
        }

        activeLink = link;
        activeLink.setAttribute('aria-describedby', tooltip.id);
        tooltip.textContent = label;
        tooltip.classList.add('is-visible');

        const linkRect = link.getBoundingClientRect();
        const tooltipRect = tooltip.getBoundingClientRect();
        const gap = 12;
        const left = linkRect.right + tooltipRect.width + gap <= window.innerWidth
            ? linkRect.right + gap
            : Math.max(8, linkRect.left - tooltipRect.width - gap);
        const top = Math.min(
            Math.max(8, linkRect.top + (linkRect.height - tooltipRect.height) / 2),
            window.innerHeight - tooltipRect.height - 8
        );

        tooltip.style.left = `${left}px`;
        tooltip.style.top = `${top}px`;
    }

    document.addEventListener('pointerover', function (event) {
        const link = event.target.closest('#sidebarDesktop [data-sidebar-tooltip]');
        if (link) showSidebarTooltip(link);
    });

    document.addEventListener('pointerout', function (event) {
        if (activeLink && activeLink.contains(event.target) && !activeLink.contains(event.relatedTarget)) {
            hideSidebarTooltip();
        }
    });

    document.addEventListener('focusin', function (event) {
        const link = event.target.closest('#sidebarDesktop [data-sidebar-tooltip]');
        if (link) showSidebarTooltip(link);
    });

    document.addEventListener('focusout', function (event) {
        if (activeLink && activeLink.contains(event.target) && !activeLink.contains(event.relatedTarget)) {
            hideSidebarTooltip();
        }
    });

    window.addEventListener('resize', hideSidebarTooltip);
    window.addEventListener('scroll', hideSidebarTooltip, true);
});
</script>

<style>
    #appScrollTop {
        position: fixed;
        right: 1.25rem;
        bottom: 1.25rem;
        z-index: 1030;
        display: inline-flex;
        width: 2.9rem;
        height: 2.9rem;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255, 255, 255, .28);
        border-radius: 50%;
        color: #fff;
        background: rgba(15, 23, 42, .92);
        box-shadow: 0 8px 24px rgba(0, 0, 0, .28);
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transform: translateY(.5rem);
        transition: opacity .2s ease, transform .2s ease, visibility .2s ease;
    }

    #appScrollTop.is-visible {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: translateY(0);
    }

    #appScrollTop:hover,
    #appScrollTop:focus-visible {
        color: #fff;
        background: #2563eb;
        border-color: #60a5fa;
    }

    @media (max-width: 575.98px) {
        #appScrollTop {
            right: 1rem;
            bottom: 1rem;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        #appScrollTop {
            transition: none;
        }
    }
</style>

<button type="button" id="appScrollTop" aria-label="Kembali ke atas" title="Kembali ke atas">
    <i class="fas fa-arrow-up" aria-hidden="true"></i>
</button>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const scrollTopButton = document.getElementById('appScrollTop');
    if (!scrollTopButton) return;

    const updateScrollTopButton = () => {
        scrollTopButton.classList.toggle('is-visible', window.scrollY > 350);
    };

    window.addEventListener('scroll', updateScrollTopButton, { passive: true });
    updateScrollTopButton();

    scrollTopButton.addEventListener('click', () => {
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
    });
});
</script>

@role('operator')
<style>
    .error-report-trigger {
        position: fixed; right: 1.25rem; bottom: 5.15rem; z-index: 1030;
        display: inline-flex; align-items: center; justify-content: center;
        width: 3rem; height: 2.9rem; padding: 0; overflow: hidden;
        border: 1px solid rgba(248, 113, 113, .4); border-radius: 999px;
        color: #fecaca; background: rgba(15, 23, 42, .96); white-space: nowrap;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .24);
        transition: width .22s ease, color .18s ease, background-color .18s ease, border-color .18s ease;
    }
    .error-report-trigger[hidden] { display: none !important; }
    .error-report-trigger:hover, .error-report-trigger:focus-visible {
        width: 11.5rem; padding: 0 .95rem; justify-content: flex-start;
        color: #fff; border-color: #f87171; background: #991b1b;
    }
    .error-report-trigger .error-report-label {
        max-width: 0; margin-left: 0 !important; overflow: hidden; opacity: 0;
        transition: max-width .22s ease, opacity .16s ease, margin-left .22s ease;
    }
    .error-report-trigger:hover .error-report-label,
    .error-report-trigger:focus-visible .error-report-label {
        max-width: 9rem; margin-left: .55rem !important; opacity: 1;
    }
    .error-report-modal .modal-content {
        color: #e2e8f0; background: #172235; border: 1px solid rgba(148, 163, 184, .25);
        border-radius: 1rem;
    }
    .error-report-modal .modal-header, .error-report-modal .modal-footer { border-color: rgba(148, 163, 184, .2); }
    .error-report-modal .form-control { color: #f8fafc; background: #202a3a; border-color: #475569; }
    .error-report-modal .form-control:focus { border-color: #60a5fa; box-shadow: 0 0 0 .2rem rgba(59, 130, 246, .18); }
    @media (max-width: 575.98px) {
        .error-report-trigger { right: 1rem; bottom: 4.75rem; width: auto; height: 2.8rem; padding: 0 .9rem; justify-content: flex-start; }
        .error-report-trigger .error-report-label { max-width: 9rem; margin-left: .55rem !important; opacity: 1; }
    }
    @media (prefers-reduced-motion: reduce) {
        .error-report-trigger, .error-report-trigger .error-report-label { transition: none; }
    }
</style>

<button type="button" class="error-report-trigger" data-bs-toggle="modal" data-bs-target="#errorReportModal" aria-label="Kirim laporan kendala" title="Kirim Log Error" hidden>
    <i class="fas fa-bug" aria-hidden="true"></i><span class="error-report-label ms-2">Kirim Log Error</span>
</button>

<div class="modal fade error-report-modal" id="errorReportModal" tabindex="-1" aria-labelledby="errorReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="errorReportForm">
                <div class="modal-header">
                    <div><h2 class="modal-title fs-5" id="errorReportModalLabel"><i class="fas fa-bug text-danger me-2"></i>Kirim Log Error</h2>
                        <p class="small text-white-50 mb-0 mt-1">Kirim kendala koneksi atau resource, atau tambahkan konteks yang belum tertangkap.</p></div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <label for="errorReportMessage" class="form-label small fw-semibold">Pesan kendala</label>
                    <textarea id="errorReportMessage" class="form-control" rows="5" maxlength="5000" required placeholder="Jelaskan kendala yang terjadi..."></textarea>
                    <div class="form-text text-white-50">Error aplikasi yang tertangkap otomatis tidak memerlukan pengiriman ulang. Periksa catatan manual agar tidak berisi informasi pribadi.</div>
                    <div id="errorReportFeedback" class="small mt-3" role="status" aria-live="polite"></div>
                    <input type="hidden" id="errorReportType">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-light rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4" id="errorReportSubmit"><i class="fas fa-paper-plane me-2"></i>Kirim Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(() => {
    const reportUrl = @json(route('error-reports.store'));
    const reportPath = new URL(reportUrl, window.location.origin).pathname;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const messageField = document.getElementById('errorReportMessage');
    const typeField = document.getElementById('errorReportType');
    const feedback = document.getElementById('errorReportFeedback');
    const form = document.getElementById('errorReportForm');
    const submitButton = document.getElementById('errorReportSubmit');
    let latestError = null;
    const sentAutomatically = new Map();
    const nativeFetch = window.fetch.bind(window);

    const showManualReport = (type, message, path = window.location.pathname, method = 'GET') => {
        const safeMessage = String(message || '').trim();
        if (!safeMessage) return;
        latestError = {
            type: String(type || 'OperatorReport').slice(0, 255),
            message: safeMessage.slice(0, 5000),
            path,
            method,
        };
        document.querySelector('.error-report-trigger')?.removeAttribute('hidden');
    };

    const sendAutomatically = async (type, message, path, method = 'GET') => {
        const key = [type, message, path].join('|');
        if (sentAutomatically.has(key)) return;

        const pending = nativeFetch(reportUrl, {
            method: 'POST', credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                error_type: type,
                error_message: message,
                page_path: path,
                http_method: method,
                browser_info: navigator.userAgent
            })
        }).then(async (response) => {
            if (!response.ok) throw new Error('Pengiriman otomatis gagal.');
            return true;
        }).catch(() => false);

        sentAutomatically.set(key, pending);
        const sent = await pending;
        if (!sent) {
            sentAutomatically.delete(key);
            showManualReport(type, message, path, method);
        } else {
            window.setTimeout(() => sentAutomatically.delete(key), 120000);
        }
    };

    const captureAutomatically = (type, message, path = window.location.pathname, method = 'GET') => {
        const safeMessage = String(message || '').trim().slice(0, 5000);
        if (!safeMessage || !csrf) {
            showManualReport(type, safeMessage || 'Terjadi kendala pada halaman.', path, method);
            return;
        }
        void sendAutomatically(type, safeMessage, path, method);
    };

    window.addEventListener('error', (event) => {
        if (event.target && event.target !== window) {
            showManualReport('ResourceError', 'Resource halaman gagal dimuat.');
            return;
        }

        const filename = event.filename || '';
        let sameOrigin = true;
        try {
            if (filename) sameOrigin = new URL(filename, window.location.href).origin === window.location.origin;
        } catch {
            sameOrigin = false;
        }

        const message = event.message || 'Terjadi error JavaScript pada halaman.';
        if (sameOrigin && message !== 'Script error.') {
            captureAutomatically('JavaScriptError', message);
        } else {
            showManualReport('ResourceError', 'Terjadi error dari resource eksternal atau sumber yang tidak dapat dipastikan.');
        }
    }, true);

    window.addEventListener('unhandledrejection', (event) => {
        const reason = event.reason;
        const message = reason?.message || String(reason || 'Proses halaman gagal diselesaikan.');
        captureAutomatically('UnhandledPromiseRejection', message);
    });

    window.fetch = (...args) => {
        const requestUrl = typeof args[0] === 'string' ? args[0] : args[0]?.url || '';
        const request = args[0] instanceof Request ? args[0] : null;
        const path = (() => { try { return new URL(requestUrl, window.location.origin).pathname; } catch { return window.location.pathname; } })();
        const method = String(args[1]?.method || request?.method || 'GET').toUpperCase();
        return nativeFetch(...args).then((response) => {
            if (response.status >= 500 && path !== reportPath && !response.headers.get('X-Error-Report-Id')) {
                captureAutomatically('HTTP ' + response.status, 'Server merespons dengan status HTTP ' + response.status + '.', path, method);
            }
            return response;
        }).catch((error) => {
            if (path !== reportPath && error?.name !== 'AbortError') {
                showManualReport('NetworkError', 'Permintaan ke server gagal. Periksa koneksi lalu coba kembali.', path, method);
            }
            throw error;
        });
    };

    document.getElementById('errorReportModal')?.addEventListener('show.bs.modal', () => {
        if (latestError) {
            typeField.value = latestError.type;
            messageField.value = latestError.message;
        }
        feedback.textContent = '';
        feedback.className = 'small mt-3';
    });

    form?.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!csrf) {
            feedback.textContent = 'Token keamanan tidak tersedia. Muat ulang halaman lalu coba lagi.';
            feedback.classList.add('text-warning');
            return;
        }

        submitButton.disabled = true;
        feedback.textContent = 'Mengirim laporan...';
        feedback.classList.add('text-white-50');
        try {
            const response = await nativeFetch(reportUrl, {
                method: 'POST', credentials: 'same-origin',
                headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    error_type: typeField.value || 'OperatorReport',
                    error_message: messageField.value,
                    page_path: latestError?.path || window.location.pathname,
                    http_method: latestError?.method || 'GET',
                    browser_info: navigator.userAgent
                })
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message || 'Laporan tidak dapat dikirim.');
            feedback.textContent = result.message || 'Log berhasil dikirim.';
            feedback.className = 'small mt-3 text-success';
            latestError = null;
            messageField.value = '';
            typeField.value = '';
        } catch (error) {
            feedback.textContent = error.message || 'Terjadi kendala saat mengirim laporan.';
            feedback.className = 'small mt-3 text-danger';
        } finally {
            submitButton.disabled = false;
        }
    });
})();
</script>
@endrole

@auth
<script>
(() => {
    const presenceUrl = @json(route('activity.presence'));
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    let lastSentAt = 0;
    let lastInteractionAt = Date.now();

    const noteInteraction = () => {
        lastInteractionAt = Date.now();
        sendPresence();
    };

    const sendPresence = () => {
        if (document.visibilityState !== 'visible' || !csrfToken
            || Date.now() - lastInteractionAt > 60000
            || Date.now() - lastSentAt < 25000) return;
        lastSentAt = Date.now();
        fetch(presenceUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        }).catch(() => {});
    };

    ['pointerdown', 'keydown', 'touchstart', 'scroll'].forEach((eventName) => {
        document.addEventListener(eventName, noteInteraction, { passive: true });
    });
    sendPresence();
    window.setInterval(sendPresence, 30000);
    document.addEventListener('visibilitychange', sendPresence);
})();
</script>
@endauth

</body>
</html>
