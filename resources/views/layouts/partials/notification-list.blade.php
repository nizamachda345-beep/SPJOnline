{{-- View: layouts/partials/notification-list --}}
@php

    $user = auth()->user();
    $userDepartment = $user->department ?? null;
    $showPin = false;
    $pinTitle = '';
    $pinMessage = '';
    $pinBadge = '';
    $pinBadgeClass = '';

    if ($userDepartment && $user->hasRole('operator')) {
        $tanggalHariIni = $waktuSekarang->day;
        $batasHariBaRekon = (int) ($systemSettings['ba_rekon.deadline_day'] ?? 10);

        $settingsService = app(\App\Services\SystemSettingsService::class);
        $uploadAvailability = $settingsService->baRekonUploadAvailability(
            $waktuSekarang,
            $baRekonPending || $baRekonDitolak,
        );
        $aksesPengajuan = $submissionAvailability;

        if (!$baRekonValid && !$uploadAvailability['allowed']) {
            $showPin = true;
            $pinTitle = 'Upload BA Rekonsiliasi Ditutup';
            $pinMessage = $uploadAvailability['message'];
            $pinBadge = 'DITUTUP';
            $pinBadgeClass = 'bg-secondary text-white';
        } elseif (!$baRekonValid && $aksesPengajuan['reason'] !== 'settings_unavailable') {
            if ($aksesPengajuan['reason'] === 'ba_rekon') {
                $showPin = true;
                $pinTitle = 'Perhatian: BA Rekonsiliasi';
                $pinMessage = 'Pengajuan baru terkunci. Lengkapi BA Rekonsiliasi bulan ini dan tunggu verifikasi untuk membuka akses.';
                $pinBadge = 'WAJIB';
                $pinBadgeClass = 'bg-danger text-white';
            } elseif ($tanggalHariIni <= $batasHariBaRekon) {
                $showPin = true;
                $pinTitle = 'Pengingat: BA Rekonsiliasi';
                $pinMessage = 'Memasuki awal bulan, mohon segera mengunggah dokumen Berita Acara Rekonsiliasi bulan ini untuk proses verifikasi.';
                $pinBadge = 'INFO';
                $pinBadgeClass = 'bg-warning text-dark';
            }
        }
    }
@endphp

@if($showPin)
    <div class="card border-warning mb-3 shadow-sm">
        <div class="card-body p-3">
            <div class="d-flex align-items-start gap-3">
                <div class="text-warning fs-5 flex-shrink-0">
                    <i class="fas fa-thumbtack"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fw-bold text-dark" style="font-size: 0.8rem;">
                            {{ $pinTitle }}
                        </span>
                        <span class="badge {{ $pinBadgeClass }}" style="font-size: 0.6rem;">{{ $pinBadge }}</span>
                    </div>
                    <p class="mb-2 text-muted" style="font-size: 0.75rem; line-height: 1.4;">
                        {{ $pinMessage }}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endif

@forelse($user->notifications()->latest()->take(8)->get() as $notification)
    <div class="p-3 mb-2 rounded-3 {{ $notification->unread() ? 'bg-white border border-primary border-opacity-10 shadow-sm' : 'bg-light bg-opacity-50 opacity-75' }} notification-item"
         style="transition: all 0.2s ease;">
        <div class="d-flex align-items-start gap-3">

            <div class="bg-{{ $notification->data['type'] ?? 'primary' }}-subtle text-{{ $notification->data['type'] ?? 'primary' }} rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width: 32px; height: 32px; font-size: 0.85rem;">
                <i class="fas {{ $notification->data['icon'] ?? 'fa-info-circle' }}"></i>
            </div>

            <div class="flex-grow-1 overflow-hidden">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="fw-semibold text-dark text-truncate pe-1" style="font-size: 0.75rem;">
                        {{ $notification->data['title'] ?? 'Informasi' }}
                    </span>
                    @if($notification->unread())
                        <span class="badge bg-primary rounded-circle p-1 flex-shrink-0" style="width: 6px; height: 6px;"></span>
                    @endif
                </div>
                <p class="text-secondary mb-1 text-wrap" style="font-size: 0.7rem; line-height: 1.4;">
                    {{ $notification->data['message'] ?? '-' }}
                </p>
                <span class="text-muted d-flex align-items-center" style="font-size: 0.6rem;">
                    <i class="far fa-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                </span>
            </div>
        </div>
    </div>
@empty
    @if(!$showPin)
        <div class="py-5 text-center">
            <div class="text-muted mb-2 opacity-40" style="font-size: 2rem;">
                <i class="far fa-bell-slash"></i>
            </div>
            <p class="text-muted mb-0 fw-medium" style="font-size: 0.75rem;">Tidak ada notifikasi atau aktivitas baru.</p>
        </div>
    @endif
@endforelse
