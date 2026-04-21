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
    @vite('resources/css/idj.css')
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
                    confirmBtn.style.backgroundColor = '#ffb3ba';
                    confirmBtn.addEventListener('mouseenter', function() {
                        this.style.backgroundColor = '#ff9aa2';
                    });
                    confirmBtn.addEventListener('mouseleave', function() {
                        this.style.backgroundColor = '#ffb3ba';
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
                color: 0xb3d9ff, // Pastel blue untuk background
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