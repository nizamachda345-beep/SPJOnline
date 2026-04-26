@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- 1. HEADER DASHBOARD --}}
    <div class="d-flex w-100 mb-1">
        <div class="d-flex flex-fill align-items-center justify-content-between bg-light shadow-sm bg-opacity-25 rounded p-4 border border-primary-subtle">
            <div class="d-flex align-items-center gap-3">
                <h2 class="fw-bold mb-0 text-primary">Dashboard iDJ</h2>
                <div class="vr opacity-25 d-none d-md-block" style="height: 1.2rem;"></div>
                <p class="text-muted mb-0 d-none d-md-block">Ringkasan aktivitas pengajuan dan status saat ini.</p>
            </div>

            <div class="d-flex align-items-center gap-2" style="color: #475569;">
                <i class="far fa-clock" style="font-size: 0.85rem;"></i>
                <span id="liveDateTime" 
                    style="font-family: 'Inter', system-ui, sans-serif; 
                            font-weight: 500; 
                            font-size: 0.8rem; 
                            letter-spacing: 0.02em;
                            color: #202c3c;">
                    Memuat Waktu...
                </span>
            </div>
        </div>
    </div>
    {{-- 2. STATS CARDS SECTION --}}
    {{-- Kita hapus row-cols-xl-5 dan gunakan justify-content-center --}}
    <div class="row g-3 mb-4 justify-content-center">
        @php
    $role = auth()->user()->roles->first()->name ?? '';
    $cards = [];

    if($role == 'operator' || $role == 'kasie_bud') {
        $cards = [
            [
                'label' => 'Total Dokumen', 
                'value' => $stats['total_berkas'], 
                'icon' => 'fa-file-invoice', 
                'color' => 'indigo', 
                'bg' => 'bg-indigo-soft', 
                'border' => 'border-start border-indigo border-4'
            ],
            [
                'label' => 'Proses Verifikasi', 
                'value' => $stats['pending'], 
                'icon' => 'fa-clock-rotate-left', 
                'color' => 'royal', 
                'bg' => 'bg-royal-soft', 
                'border' => 'border-start border-royal border-4'
            ],
            [
                'label' => $role == 'operator' ? 'Butuh Revisi' : 'Menunggu Revisi', 
                'value' => $stats['revisi'], 
                'icon' => 'fa-pen-ruler', 
                'color' => 'amber', 
                'bg' => 'bg-amber-soft', 
                'border' => 'border-start border-amber border-4'
            ],
            
        ];
        
        // TAMBAHAN: Card Proses Validasi khusus untuk Operator
        if($role == 'operator') {
            $cards[] = [
                'label' => 'Proses Validasi',
                'value' => $stats['validasi'] ?? 0, 
                'icon' => 'fa-hourglass-half', 
                'color' => 'royal', 
                'bg' => 'bg-royal-soft', 
                'border' => 'border-start border-royal border-4'
            ];
        }
        
        // Card Disahkan (Muncul di Operator & Kasie BUD)
        $cards[] = [
                'label' => 'Ditolak', 
                'value' => $stats['rejected'], 
                'icon' => 'fa-ban', 
                'color' => 'crimson', 
                'bg' => 'bg-crimson-soft', 
                'border' => 'border-start border-crimson border-4'
            ];
        $cards[] = [
            'label' => 'Disahkan', 
            'value' => $stats['disetujui'], 
            'icon' => 'fa-circle-check', 
            'color' => 'emerald', 
            'bg' => 'bg-emerald-soft', 
            'border' => 'border-start border-emerald border-4'
        ];
    
    } elseif($role == 'kabid_perbendaharaan') {
        // ... (Kode Kabid tetap sama)
        $cards = [
            ['label' => 'Total Berkas', 'value' => $stats['total_berkas'], 'icon' => 'fa-receipt', 'color' => 'indigo', 'bg' => 'bg-indigo-soft', 'border' => 'border-start border-indigo border-4'],
            ['label' => 'Dalam Proses', 'value' => $stats['pending'], 'icon' => 'fa-spinner', 'color' => 'royal', 'bg' => 'bg-royal-soft', 'border' => 'border-start border-royal border-4'],
            ['label' => 'Menunggu Revisi', 'value' => $stats['revisi'] ?? 0, 'icon' => 'fa-user-clock', 'color' => 'emerald', 'bg' => 'bg-emerald-soft', 'border' => 'border-start border-emerald border-4'],
            ['label' => 'Disahkan', 'value' => $stats['selesai'] ?? 0, 'icon' => 'fa-check', 'color' => 'amber', 'bg' => 'bg-amber-soft', 'border' => 'border-start border-amber border-4'],
        ];
    }

    $count = count($cards);
    // Jika jumlah card banyak (misal 5 atau 6), gunakan col-xl agar muat dalam satu baris
    $colClass = ($count >= 5) ? 'col-xl' : 'col-xl-3';
@endphp

    <div class="row g-3 mb-2 justify-content-center">
    @foreach($cards as $card)
        <div class="col-12 col-md-6 col-lg-4 {{ $colClass }}">
            <div class="card h-100 shadow-sm border-0 {{ $card['border'] ?? '' }} card-hover">
                <div class="card-body p-4 text-center d-flex flex-column align-items-center justify-content-center">
                    {{-- Squircle Icon Box --}}
                    <div class="icon-box {{ $card['bg'] }} text-{{ $card['color'] }} mb-1 shadow-sm">
                        <i class="fas {{ $card['icon'] }} fa-lg"></i>
                    </div>
                    
                    {{-- Typography Refinement --}}
                    <h2 class="fw-black mb-1 tabular-nums text-dark-blue">{{ $card['value'] ?? 0 }}</h2>
                    <span class="card-label text-uppercase text-muted">
                        {{ $card['label'] }}
                    </span>
                </div>
            </div>
        </div>
    @endforeach

</div>

    {{-- 3. CHART SECTION --}}
    <div class="mb-1">
        @include('components.monthly-chart')
    </div>

   {{-- 4. FOOTER INFO SECTION --}}
    {{-- 1. WELCOME CARD (Lighter & Cleaner) --}}
    <div class="col-lg-9">
        <div class="card shadow-sm border-0 h-100 rounded-4 overflow-hidden">
            {{-- Aksen Dekorasi Halus --}}
            <!-- <div class="position-absolute top-0 end-0 m-3 opacity-5 d-none d-md-block">
                <i class="fas fa-fingerprint fa-6x text-dark"></i>
            </div>
             -->
            <div class="card-body p-4">
                <div class="d-flex flex-column h-100 justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                                <i class="fas fa-user-circle fa-lg text-primary"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0 text-dark text-nowrap">Selamat Datang, {{ auth()->user()->name }}</h5>
                                <p class="text-muted small mb-0 fw-medium">iDJ v1.0</p>
                            </div>
                        </div>

                        <div class="mb-4">
                            <span class="badge bg-light text-primary border border-primary-subtle rounded-pill px-3 py-2 fw-bold" style="font-size: 0.75rem;">
                                <i class="fas fa-shield-alt me-1 text-primary"></i> {{ strtoupper(auth()->user()->role_label) }}
                            </span>
                        </div>
                    </div>

                    {{-- Info Alert yang Lebih Terintegrasi --}}
                    <div class="bg-warning bg-opacity-10 border-start border-warning border-4 p-3 rounded-end-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-sync-alt fa-spin-hover text-warning me-3"></i>
                            <div class="small">
                                <span class="text-dark fw-bold">Update Status:</span> 
                                <span class="text-secondary">Terdapat <strong>{{ $stats['reset'] ?? 0 }}</strong> berkas yang dikembalikan oleh Admin ke tahap awal.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- 2. MINI STATS CARD (Compact & Dark Mode Style) --}}
    <div class="col-lg-3">
    <div class="card shadow-sm border-0 bg-dark text-white h-100 rounded-4 position-relative overflow-hidden" style="min-height: 180px;">
        <div class="card-body d-flex flex-column justify-content-center align-items-center text-center p-4">
            
            {{-- Icon Container dengan Glassmorphism Soft --}}
            <div class="bg-white bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mb-3" 
                 style="width: 60px; height: 60px; border: 1px solid rgba(255,255,255,0.1);">
                <i class="fas fa-history fa-lg text-warning"></i>
            </div>

            {{-- Statistik Utama --}}
            <h2 class="fw-bold mb-1 display-5 tabular-nums" style="letter-spacing: -1px;">
                {{ $stats['reset'] ?? 0 }}
            </h2>
            
            {{-- Label --}}
            <p class="text-uppercase fw-semibold text-white-50 mb-0" 
               style="font-size: 0.7rem; letter-spacing: 1.5px;">
                Total Berkas Direset
            </p>

        </div>

        {{-- Subtle Decorative Glow (Optional - untuk kesan Mewah) --}}
        <div class="position-absolute top-0 end-0 p-4 opacity-10" style="filter: blur(40px); pointer-events: none;">
            <div class="bg-warning rounded-circle" style="width: 80px; height: 80px;"></div>
        </div>
    </div>
</div>
</div>

<style>
    /* 1. PALET WARNA PROFESIONAL (iDJ Premium Palette) */
    .text-indigo { color: #4e73df !important; }
    .bg-indigo-soft { background-color: #f8f9fc !important; }
    .border-indigo { border-color: #4e73df !important; }

    .text-royal { color: #2e59d9 !important; }
    .bg-royal-soft { background-color: #f0f3ff !important; }
    .border-royal { border-color: #2e59d9 !important; }

    .text-amber { color: #f6c23e !important; }
    .bg-amber-soft { background-color: #fffdf5 !important; }
    .border-amber { border-color: #f6c23e !important; }

    .text-emerald { color: #1cc88a !important; }
    .bg-emerald-soft { background-color: #f6fffb !important; }
    .border-emerald { border-color: #1cc88a !important; }

    .text-crimson { color: #e74a3b !important; }
    .bg-crimson-soft { background-color: #fff5f4 !important; }
    .border-crimson { border-color: #e74a3b !important; }

    .text-dark-blue { color: #2c3e50 !important; }

    /* 2. REFINEMENT KARTU (Card & Interaction) */
    .card-hover {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 16px !important;
        cursor: pointer;
        border: 1px solid rgba(0,0,0,0.03) !important;
    }

    .card-hover:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px -5px rgba(0,0,0,0.1) !important;
    }

    /* 3. ICON SQUIRCLE BOX */
    .icon-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    /* 4. MIKRO-ANIMASI SAAT HOVER */
    /* Animasi icon umum (bounce) */
    .card-hover:hover .fas:not(.fa-hourglass-start):not(.fa-hourglass-half):not(.fa-sync-alt) {
        animation: bounceIcon 0.6s infinite alternate;
    }

    /* Animasi putar khusus icon proses/waktu */
    .card-hover:hover .fa-hourglass-start, 
    .card-hover:hover .fa-hourglass-half,
    .card-hover:hover .fa-sync-alt,
    .card-hover:hover .fa-spin-hover {
        animation: rotateIcon 1.5s infinite linear;
    }

    /* 5. TIPOGRAFI & UTILITAS */
    .fw-black { font-weight: 800; letter-spacing: -0.5px; }
    
    .card-label { 
        font-size: 0.65rem !important; 
        font-weight: 700; 
        letter-spacing: 0.08rem;
        line-height: 1.2;
    }

    .tabular-nums { font-variant-numeric: tabular-nums; }
    .text-nowrap { white-space: nowrap; }

    /* Batasi teks label maksimal 2 baris agar rapi */
    .card-body span {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* 6. KEYFRAMES */
    @keyframes bounceIcon {
        from { transform: scale(1); }
        to { transform: scale(1.15) translateY(-2px); }
    }

    @keyframes rotateIcon {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* 7. RESPONSIVE ADJUSTMENT */
    @media (max-width: 991.98px) {
        .card-body { padding: 1.25rem !important; }
    }
</style>


@endsection