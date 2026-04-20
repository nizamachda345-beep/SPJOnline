<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iDJ - Sistem Arsip Digital</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r121/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanta@latest/dist/vanta.waves.min.js"></script>

    <style>
        :root {
            color-scheme: light;
            --body-bg: transparent;
            --surface: rgba(255, 255, 255, 0.97);
            --surface-soft: rgba(248, 249, 250, 0.95);
            --border-muted: rgba(33, 37, 41, 0.08);
            --text-muted: #6c757d;
            --text-dark: #1f2937;
            --accent-primary: #e6edff;
            --accent-secondary: #eef2f7;
            --accent-success: #e7f5ea;
            --accent-warning: #fff7e6;
            --primary: #4e73df; /* Lebih soft dari navy murni */
            --primary-soft: #eef2ff;
            --success: #51c944;
            --success-soft: #e8fadf;
            --warning: #ffb800;
            --warning-soft: #fff2d6;
            --danger: #ff3e1d;
            --danger-soft: #ffe5e5;
            --info: #03c3ec;
            --info-soft: #e1faff;
            --dark: #233446;
            --text-primary: #444a50;
            --text-secondary: #8592a3;
            --border-color: #d9dee3;
            --surface: #ffffff;
        }

        body {
            min-height: 100vh;
            background: transparent;
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
            font-size: 0.7rem;
            line-height: 1.6;
        }

        h1, h2, h3, h4, h5, h6 {
            margin-bottom: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.005em;
        }

        h1 { font-size: 1.15rem; }
        h2 { font-size: 1.1rem; }
        h3 { font-size: 0.95rem; }
        h4 { font-size: 0.875rem; }
        h5 { font-size: 0.8rem; }
        h6 { font-size: 0.7rem; }

        .navbar {
            background: var(--surface);
            border-bottom: 1px solid var(--border-muted);
        }

        .navbar-brand,
        .nav-link,
        .dropdown-item {
            color: var(--text-dark);
        }

        .navbar-brand {
            font-size: 1rem;
            letter-spacing: .025em;
        }

        .navbar-brand:hover,
        .nav-link:hover,
        .dropdown-item:hover {
            color: var(--dark);
        }

        .sidebar {
            position: fixed;
            top: 72px;
            bottom: 0;
            left: 0;
            width: 16.5rem;
            padding-top: 0.95rem;
            background: var(--surface);
            border-right: 1px solid var(--border-muted);
            overflow-y: auto;
            transition: all 0.3s ease-in-out;
        }

        .sidebar.sidebar-dark {
            background: var(--dark);
            border-right-color: rgba(255,255,255,.08);
        }

        .sidebar.sidebar-dark .nav-link {
            color: #cbd5e1;
        }

        .sidebar.sidebar-dark .nav-link:hover {
            background: rgba(255,255,255,.08);
            color: #fff;
        }

        .sidebar.sidebar-dark .nav-link.active {
            background: var(--primary);
            color: #fff !important;
            border-radius: 0 1.5rem 1.5rem 0;
            box-shadow: 0 8px 24px rgba(15,23,42,.12);
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar.collapsed .nav-link span {
            display: none;
        }

        .sidebar.collapsed .nav-link i {
            margin-right: 0;
        }

        .sidebar.collapsed .border-top small {
            display: none;
        }

        .card {
            border: 0;
            border-radius: 0.85rem;
            background: var(--surface);
            box-shadow: 0 10px 24px rgba(15,23,42,.05);
        }

        .card-header {
            background: var(--surface);
            border-bottom: 1px solid var(--border-muted);
            padding: 1rem 1.125rem;
        }

        .card-body {
            padding: 1.25rem;
        }

        .data-label {
            display: block;
            margin-bottom: .65rem;
            font-size: .7rem;
            letter-spacing: .18em;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--text-muted);
        }

        .btn-pill {
            border-radius: 50rem !important;
        }

        .btn-soft {
            background: var(--accent-secondary);
            border: 1px solid transparent;
            color: var(--text-dark);
        }

        .btn-soft.btn-primary {
            background: var(--accent-primary);
            color: var(--text-dark);
        }

        .btn-soft:hover {
            opacity: .95;
        }

        .btn {
            font-size: 0.75rem;
            padding: 0.5rem 0.75rem;
            border-radius: 0.5rem;
            font-weight: 500;
        }

        .bg-accent-primary {
            background: var(--accent-primary) !important;
        }

        .bg-accent-secondary {
            background: var(--accent-secondary) !important;
        }

        .bg-accent-success {
            background: var(--accent-success) !important;
        }

        .bg-accent-warning {
            background: var(--accent-warning) !important;
        }

        .bg-soft-primary {
            background: var(--accent-primary) !important;
            color: #0f172a !important;
        }

        /* Text Color Utilities for Background Contrast */
        .bg-dark, .bg-primary, .bg-secondary, .bg-success, .bg-danger, .bg-warning, .bg-info {
            color: #ffffff !important;
        }

        .bg-dark h1, .bg-dark h2, .bg-dark h3, .bg-dark h4, .bg-dark h5, .bg-dark h6,
        .bg-primary h1, .bg-primary h2, .bg-primary h3, .bg-primary h4, .bg-primary h5, .bg-primary h6,
        .bg-secondary h1, .bg-secondary h2, .bg-secondary h3, .bg-secondary h4, .bg-secondary h5, .bg-secondary h6,
        .bg-success h1, .bg-success h2, .bg-success h3, .bg-success h4, .bg-success h5, .bg-success h6,
        .bg-danger h1, .bg-danger h2, .bg-danger h3, .bg-danger h4, .bg-danger h5, .bg-danger h6,
        .bg-warning h1, .bg-warning h2, .bg-warning h3, .bg-warning h4, .bg-warning h5, .bg-warning h6,
        .bg-info h1, .bg-info h2, .bg-info h3, .bg-info h4, .bg-info h5, .bg-info h6 {
            color: #ffffff !important;
        }

        .bg-light, .bg-white,
        .bg-accent-primary, .bg-accent-secondary, .bg-accent-success, .bg-accent-warning {
            color: #1f2937 !important;
        }

        .bg-light h1, .bg-light h2, .bg-light h3, .bg-light h4, .bg-light h5, .bg-light h6,
        .bg-white h1, .bg-white h2, .bg-white h3, .bg-white h4, .bg-white h5, .bg-white h6,
        .bg-accent-primary h1, .bg-accent-primary h2, .bg-accent-primary h3, .bg-accent-primary h4, .bg-accent-primary h5, .bg-accent-primary h6,
        .bg-accent-secondary h1, .bg-accent-secondary h2, .bg-accent-secondary h3, .bg-accent-secondary h4, .bg-accent-secondary h5, .bg-accent-secondary h6,
        .bg-accent-success h1, .bg-accent-success h2, .bg-accent-success h3, .bg-accent-success h4, .bg-accent-success h5, .bg-accent-success h6,
        .bg-accent-warning h1, .bg-accent-warning h2, .bg-accent-warning h3, .bg-accent-warning h4, .bg-accent-warning h5, .bg-accent-warning h6 {
            color: #1f2937 !important;
        }

        .table-hover tbody tr:hover {
            background: rgba(15,23,42,.04);
        }

        .table th,
        .table td {
            padding: 0.875rem 0.75rem;
            vertical-align: middle;
            font-size: 0.75rem;
        }



        .card.border-start-gold {
            border-left: 4px solid #d4af37 !important;
        }

        .card.border-start-navy {
            border-left: 4px solid var(--primary) !important;
        }

        /* Alert Colors - Ensuring Text is Readable */
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border-color: #ffeaa7;
        }

        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border-color: #bee5eb;
        }

        /* Badge Colors - Soft and Professional */
        .badge {
            text-transform: uppercase;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.5rem 0.8rem;
            border-radius: 5px;
            border: 1px solid transparent;
        }

        .badge.bg-primary {
            background-color: var(--primary);
            color: #ffffff !important;
            border-color: rgba(78, 115, 223, 0.2);
        }

        .badge.bg-success {
            background-color: var(--success);
            color: #ffffff !important;
            border-color: rgba(81, 201, 68, 0.2);
        }

        .badge.bg-danger {
            background-color: var(--danger);
            color: #ffffff !important;
            border-color: rgba(255, 62, 29, 0.2);
        }

        .badge.bg-warning {
            background-color: var(--warning);
            color: #ffffff !important;
            border-color: rgba(255, 184, 0, 0.2);
        }

        .badge.bg-info {
            background-color: var(--info);
            color: #ffffff !important;
            border-color: rgba(3, 195, 236, 0.2);
        }

        .badge.bg-secondary {
            background-color: var(--text-secondary);
            color: #ffffff !important;
            border-color: rgba(108, 117, 125, 0.2);
        }

        .badge.bg-light {
            background-color: rgba(248, 249, 250, 0.8);
            color: var(--text-dark) !important;
            border-color: rgba(33, 37, 41, 0.1);
        }

        .alert,
        .modal-content {
            border-radius: 1rem;
        }

        .form-control,
        .form-select,
        .input-group-text {
            border-radius: .75rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
        }

        .input-group-text {
            background: var(--surface-soft);
            border-right: 0;
        }

        .table thead th {
            border-bottom-color: var(--border-muted);
        }

        .table tbody tr:hover {
            background: var(--surface-soft);
        }

        /* Skeleton Loading Styles */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 0%, #f9f9f9 50%, #f0f0f0 100%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s ease-in-out infinite;
            border-radius: 0.5rem;
        }

        @keyframes skeleton-loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        .skeleton-table thead th {
            height: 2.5rem;
        }

        .skeleton-table tbody tr {
            height: 2.8rem;
        }

        .skeleton-text {
            height: 0.9rem;
            margin-bottom: 0.5rem;
            display: inline-block;
            width: 100%;
        }

        .skeleton-avatar {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
        }

        /* SweetAlert2 Dark Theme */
        .swal2-popup {
            background: var(--surface);
            color: var(--text-dark);
            box-shadow: 0 10px 40px rgba(15,23,42,.15);
            border-radius: 1rem;
        }

        .swal2-title {
            color: var(--text-dark);
            font-weight: 700;
        }

        .swal2-html-container {
            color: var(--text-muted);
        }

        .swal2-confirm,
        .swal2-cancel {
            padding: 0.6rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 600;
            border: none;
        }

        .swal2-confirm {
            background: var(--primary);
        }

        .swal2-confirm:hover {
            background: var(--primary);
            opacity: 0.9;
        }

        .swal2-cancel {
            background: #e5e7eb;
            color: var(--text-dark);
        }

        .swal2-cancel:hover {
            background: #d1d5db;
        }

        .main-content {
            margin-left: 16.5rem;
            padding: 1.5rem;
            transition: margin-left 0.3s ease-in-out;
            background: transparent;
        }

        .sidebar.collapsed + .main-content {
            margin-left: 70px;
        }

        /* Offcanvas for mobile */
        .offcanvas {
            width: 16.5rem !important;
        }

        /* Hide sidebar toggle on mobile */
        @media (max-width: 767.98px) {
            .sidebar {
                display: none !important;
            }
            .main-content {
                margin-left: 0 !important;
            }
        }
    </style>
</head>
<body>
<div id="vanta-bg" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;"></div>
            
<nav class="navbar navbar-expand-lg navbar-light sticky-top shadow-sm py-2">
    <div class="container-fluid px-4">
        <a class="navbar-brand fw-semibold" href="#">
            <i class="fas fa-file-invoice-dollar me-2"></i> iDJ <small class="fw-light" style="font-size: 0.75rem;">v1.0</small>
        </a>
        
        @auth
        <!-- Sidebar Toggle Button for Mobile -->
        <button class="btn btn-outline-secondary d-md-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
            <i class="fas fa-bars"></i>
        </button>
        @endauth
        
        @auth
        <div class="ms-auto d-flex align-items-center">
            <div class="dropdown">
                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle fa-lg"></i> {{ auth()->user()->name }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-sign-out-alt me-2"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
        @endauth
    </div>
</nav>

{{-- Breadcrumbs Navigation --}}
@yield('breadcrumbs')

<div class="container-fluid px-4">
    <div class="row">
        @auth
        {{-- Sidebar for Desktop --}}
        <nav class="col-md-3 d-none d-md-block sidebar @hasanyrole('super_admin|bpk') sidebar-dark @endhasanyrole" id="sidebarDesktop">
            <div class="px-3">
                <ul class="nav flex-column mb-4">
                    {{-- 1. DASHBOARD DINAMIS (Satu Menu, Beda Rute) --}}
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

                    {{-- 2. MENU KHUSUS OPERATOR --}}
                    @role('operator')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('submissions/create') ? 'active' : '' }}" href="{{ route('submissions.create') }}">
                            <i class="fas fa-cloud-upload-alt me-2"></i> Upload Berkas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('submissions*') ? 'active' : '' }}" href="{{ route('submissions.index') }}">
                            <i class="fas fa-history me-2"></i> History Pengajuan
                        </a>
                    </li>
                    @endrole

                    {{-- 3. MENU KHUSUS VERIFIKATOR/VALIDATOR --}}
                    @role('kasie_bud')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('verifikasi*') ? 'active' : '' }}" href="{{ route('verifikasi.index') }}">
                            <i class="fas fa-check-double me-2"></i> Verifikasi Berkas
                        </a>
                    </li>
                    @endrole

                    @role('kabid_perbendaharaan')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('validasi*') ? 'active' : '' }}" href="{{ route('validasi.index') }}">
                            <i class="fas fa-file-signature me-2"></i> Validasi Berkas
                        </a>
                    </li>
                    @endrole     

                    {{-- 4. MENU MASTER DATA (HANYA SUPER ADMIN) --}}
                    @role('super_admin')
                    <li class="nav-item border-top mt-3 pt-3">
                        <small class="text-muted ms-3 text-uppercase" style="font-size: 0.7rem;">Data Master</small>
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

                    {{-- 5. MENU HISTORY (MULTI ROLE) --}}
                    @hasanyrole('kabid_perbendaharaan|kasie_bud|super_admin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('history*') ? 'active' : '' }}" href="{{ route('history.index') }}">
                            <i class="fas fa-history me-2"></i>
                            <span>History</span>
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
                </ul>
            </div>
        </nav>

        {{-- Offcanvas Sidebar for Mobile --}}
        <div class="offcanvas offcanvas-start @hasanyrole('super_admin|bpk') sidebar-dark @endhasanyrole" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title fw-semibold" id="sidebarOffcanvasLabel">
                    <i class="fas fa-file-invoice-dollar me-2"></i> iDJ Navigation
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body px-3">
                <ul class="nav flex-column mb-4">
                    {{-- 1. DASHBOARD DINAMIS (Satu Menu, Beda Rute) --}}
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

                    {{-- 2. MENU KHUSUS OPERATOR --}}
                    @role('operator')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('submissions/create') ? 'active' : '' }}" href="{{ route('submissions.create') }}">
                            <i class="fas fa-cloud-upload-alt me-2"></i> Upload Berkas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('submissions*') ? 'active' : '' }}" href="{{ route('submissions.index') }}">
                            <i class="fas fa-history me-2"></i> History Pengajuan
                        </a>
                    </li>
                    @endrole

                    {{-- 3. MENU KHUSUS VERIFIKATOR/VALIDATOR --}}
                    @role('kasie_bud')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('verifikasi*') ? 'active' : '' }}" href="{{ route('verifikasi.index') }}">
                            <i class="fas fa-check-double me-2"></i> Verifikasi Berkas
                        </a>
                    </li>
                    @endrole

                    @role('kabid_perbendaharaan')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('validasi*') ? 'active' : '' }}" href="{{ route('validasi.index') }}">
                            <i class="fas fa-file-signature me-2"></i> Validasi Berkas
                        </a>
                    </li>
                    @endrole     

                    {{-- 4. MENU MASTER DATA (HANYA SUPER ADMIN) --}}
                    @role('super_admin')
                    <li class="nav-item border-top mt-3 pt-3">
                        <small class="text-muted ms-3 text-uppercase" style="font-size: 0.7rem;">Data Master</small>
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
                            <i class="fas fa-building me-2"></i> Manajemen Dinas
                        </a>
                    </li>
                    @endrole

                    {{-- 5. MENU HISTORY (MULTI ROLE) --}}
                    @hasanyrole('kabid_perbendaharaan|kasie_bud|super_admin')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('history*') ? 'active' : '' }}" href="{{ route('history.index') }}">
                            <i class="fas fa-history me-2"></i>
                            <span>History</span>
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
                </ul>
            </div>
        </div>
        @endauth

        <main class="{{ Auth::check() ? 'col-md-9 main-content' : 'col-md-12' }}">
            <div id="alert-container" class="mb-2">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            </div>
            <div id="content-wrapper">
                @yield('content')
            </div>
        </main>
    </div>
</div>


{{-- Sidebar Toggle Script --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sidebar collapse toggle for desktop
    const sidebar = document.getElementById('sidebarDesktop');
    const mainContent = document.querySelector('.main-content');
    
    // Add toggle button to navbar for desktop collapse
    const navbar = document.querySelector('.navbar .container-fluid');
    const toggleBtn = document.createElement('button');
    toggleBtn.className = 'btn btn-outline-secondary d-none d-md-inline-block me-2';
    toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
    toggleBtn.onclick = function() {
        sidebar.classList.toggle('collapsed');
    };
    
    // Insert toggle button before user dropdown
    const userDropdown = navbar.querySelector('.dropdown');
    if (userDropdown) {
        navbar.insertBefore(toggleBtn, userDropdown);
    }

    // SweetAlert2 Delete Confirmation
    document.querySelectorAll('form[data-confirm-delete]').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const message = this.getAttribute('data-confirm-delete') || 'Apakah Anda yakin ingin menghapus data ini?';
            
            Swal.fire({
                title: 'Konfirmasi Penghapusan',
                html: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                allowOutsideClick: false,
                didOpen: (modal) => {
                    const confirmBtn = modal.querySelector('.swal2-confirm');
                    confirmBtn.style.backgroundColor = '#dc2626';
                    confirmBtn.addEventListener('mouseenter', function() {
                        this.style.backgroundColor = '#b91c1c';
                    });
                    confirmBtn.addEventListener('mouseleave', function() {
                        this.style.backgroundColor = '#dc2626';
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // SweetAlert2 for other confirm buttons
    document.querySelectorAll('button[data-confirm]').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm') || 'Apakah Anda yakin?';
            
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi',
                html: message,
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal',
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    const formId = this.getAttribute('data-form-id');
                    if (formId) {
                        document.getElementById(formId).submit();
                    }
                }
            });
        });
    });
});
</script>
<script>
    function initVanta() {
        if (typeof VANTA !== 'undefined' && typeof THREE !== 'undefined') {
            VANTA.WAVES({
                el: "#vanta-bg",
                mouseControls: true,
                touchControls: true,
                gyroControls: false,
                minHeight: 200.00,
                minWidth: 200.00,
                scale: 1.00,
                scaleMobile: 1.00,
                color: 0x4e73df, // Ubah warna ke biru iDJ agar terlihat (jangan putih)
                shininess: 30.00,
                waveHeight: 15.00,
                waveSpeed: 0.80,
                zoom: 1.00
            });
        } else {
            // Jika gagal, tunggu 100ms lalu coba lagi
            setTimeout(initVanta, 100);
        }
    }

    // Jalankan setelah seluruh window (termasuk script luar) selesai dimuat
    window.addEventListener('load', initVanta);
</script>

</body>
</html>