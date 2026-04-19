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

    <style>
        :root {
            color-scheme: light;
            --body-bg: #f4f7f8;
            --surface: #ffffff;
            --surface-soft: #f8f9fa;
            --border-muted: rgba(33,37,41,.08);
            --text-muted: #6c757d;
            --text-dark: #1f2937;
            --accent-primary: #e6edff;
            --accent-secondary: #eef2f7;
            --accent-success: #e7f5ea;
            --accent-warning: #fff7e6;
        }

        body {
            min-height: 100vh;
            background: var(--body-bg);
            font-family: 'Inter', sans-serif;
            color: var(--text-dark);
            font-size: 0.92rem;
            line-height: 1.55;
        }

        h1, h2, h3, h4, h5, h6 {
            margin-bottom: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.01em;
        }

        h1 { font-size: 1.75rem; }
        h2 { font-size: 1.45rem; }
        h3 { font-size: 1.25rem; }
        h4 { font-size: 1.1rem; }
        h5 { font-size: 1rem; }
        h6 { font-size: 0.9rem; }

        .navbar {
            background: var(--surface);
            border-bottom: 1px solid var(--border-muted);
        }

        .navbar-brand,
        .nav-link,
        .dropdown-item {
            color: #1f2937;
        }

        .navbar-brand {
            font-size: 1rem;
            letter-spacing: .025em;
        }

        .navbar-brand:hover,
        .nav-link:hover,
        .dropdown-item:hover {
            color: #0f172a;
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
        }

        .sidebar.sidebar-dark {
            background: #0f172a;
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
            background: #1d4ed8;
            color: #fff !important;
            border-radius: 0 1.5rem 1.5rem 0;
            box-shadow: 0 8px 24px rgba(15,23,42,.12);
        }

        .sidebar .nav-link {
            color: #495057;
            font-weight: 500;
            padding: .85rem 1.15rem;
            border-radius: .85rem;
            margin: .2rem 0;
            transition: all .15s ease-in-out;
            font-size: 0.95rem;
        }

        .sidebar .nav-link:hover {
            background: var(--surface-soft);
            color: #0f172a;
        }

        .sidebar .nav-link.active {
            color: #0f172a !important;
            background: rgba(13,110,253,.08);
            font-weight: 600;
        }

        .main-content {
            margin-left: 16.5rem;
            padding: 1.5rem;
        }

        @media (max-width: 991.98px) {
            .sidebar {
                position: relative;
                top: 0;
                width: 100%;
                height: auto;
            }
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
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
            padding: 0.95rem 1rem;
        }

        .card-body {
            padding: 1.1rem;
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
            color: #0f172a;
        }

        .btn-soft.btn-primary {
            background: var(--accent-primary);
            color: #0f172a;
        }

        .btn-soft:hover {
            opacity: .95;
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

        .table-hover tbody tr:hover {
            background: rgba(15,23,42,.04);
        }

        .table th,
        .table td {
            padding: 1rem 0.85rem;
            vertical-align: middle;
            font-size: 0.95rem;
        }

        .card.border-start-gold {
            border-left: 4px solid #d4af37 !important;
        }

        .card.border-start-navy {
            border-left: 4px solid #1d4ed8 !important;
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

        /* Sidebar Responsive Styles */
        .sidebar {
            transition: all 0.3s ease-in-out;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 16.5rem;
            z-index: 1000;
            overflow-y: auto;
            padding-top: 4.5rem;
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

        /* Offcanvas for mobile */
        .offcanvas {
            width: 16.5rem !important;
        }

        /* Main content adjustment */
        .main-content {
            margin-left: 16.5rem;
            transition: margin-left 0.3s ease-in-out;
        }

        .sidebar.collapsed + .main-content {
            margin-left: 70px;
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
                            <i class="fas fa-history me-2"></i> Riwayat Pengajuan
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
                            <i class="fas fa-history me-2 text-success"></i>
                            <span>History Berkas</span>
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
                            <i class="fas fa-history me-2"></i> Riwayat Pengajuan
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
                            <i class="fas fa-history me-2 text-success"></i>
                            <span>History Berkas</span>
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
            <div id="alert-container" class="mb-4">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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
});
</script>
</body>
</html>