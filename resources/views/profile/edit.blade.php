@extends('layouts.app')

@section('content')
@php
    $roleName = $user->roles->first()?->name;
    $roleLabel = match ($roleName) {
        'super_admin' => 'MASTER',
        'operator' => 'Operator',
        'kabid_perbendaharaan' => 'Validator',
        'kasie_bud' => 'Verifikator',
        'bpk' => 'BPK',
        default => $roleName ? \Illuminate\Support\Str::headline($roleName) : 'Pengguna',
    };
    $productName = filled($systemSettings['branding.product_name'] ?? null) ? $systemSettings['branding.product_name'] : 'SPJOnline';
    $governmentName = filled($systemSettings['branding.government_name'] ?? null) ? $systemSettings['branding.government_name'] : 'Pemerintah Daerah';
    $agencyName = filled($systemSettings['branding.agency_name'] ?? null) ? $systemSettings['branding.agency_name'] : 'Pengelola Sistem';
    $officeAddress = $systemSettings['branding.office_address'] ?? null;
    $supportContact = $systemSettings['branding.contact'] ?? null;
    $policyTimezone = $systemSettings['system.timezone'] ?? 'Asia/Makassar';
@endphp
<style>
    .profile-page { color: #e2e8f0; }
    .profile-header, .profile-card {
        border: 1px solid rgba(148, 163, 184, .16);
        border-radius: 1rem;
        background: rgba(15, 23, 42, .88);
        box-shadow: 0 14px 36px rgba(2, 6, 23, .18);
        overflow: hidden;
    }
    .profile-header { padding: 1.15rem 1.35rem; margin-bottom: 1rem; }
    .profile-icon {
        display: grid; place-items: center; flex: 0 0 3rem; height: 3rem;
        border-radius: .8rem; color: #93c5fd; background: rgba(37, 99, 235, .18);
        font-size: 1.1rem;
    }
    .profile-info-card {
        height: 100%; padding: 1.1rem; border: 1px solid rgba(148, 163, 184, .16);
        border-radius: .85rem; background: rgba(30, 41, 59, .58);
    }
    .profile-info-icon {
        display: grid; place-items: center; flex: 0 0 2.4rem; height: 2.4rem;
        border-radius: .7rem; color: #7dd3fc; background: rgba(14, 165, 233, .12);
    }
    .profile-info-value { color: #f1f5f9; font-size: .9rem; font-weight: 600; overflow-wrap: anywhere; }
    .profile-info-copy { color: #aab7c9; font-size: .82rem; line-height: 1.55; overflow-wrap: anywhere; }
    .profile-info-link { color: #7dd3fc; text-decoration: none; }
    .profile-info-link:hover { color: #bae6fd; text-decoration: underline; }
    .profile-card-heading {
        padding: 1rem 1.2rem;
        background: rgba(30, 41, 59, .76);
        border-bottom: 1px solid rgba(148, 163, 184, .14);
    }
    .profile-card-body { padding: 1.2rem; }
    .profile-label { color: #cbd5e1; font-size: .82rem; font-weight: 600; margin-bottom: .4rem; }
    .profile-help { color: #94a3b8; font-size: .78rem; }
    .profile-readonly {
        min-height: 2.6rem; display: flex; align-items: center; padding: .6rem .8rem;
        border: 1px solid rgba(148, 163, 184, .16); border-radius: .65rem;
        color: #cbd5e1; background: rgba(15, 23, 42, .58);
    }
    @media (max-width: 575.98px) {
        .profile-header, .profile-card-body { padding: 1rem; }
    }
</style>

<div class="container-fluid px-4 profile-page py-2">
    <form action="{{ route('profile.update') }}" method="POST" autocomplete="off">
        @csrf
        @method('PUT')

        <section class="profile-header d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="profile-icon"><i class="fas fa-user-pen"></i></div>
                <div>
                    <h1 class="h5 text-white mb-1">Profil &amp; Keamanan Akun</h1>
                    <p class="profile-help mb-0">Perbarui identitas dan password akun Anda.</p>
                </div>
            </div>
            <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="fas fa-save me-2"></i>Simpan Perubahan</button>
        </section>

        <section class="profile-card mb-3">
            <header class="profile-card-heading">
                <h2 class="h6 text-white mb-1"><i class="fas fa-id-card text-primary me-2"></i>Informasi Akun</h2>
                <div class="profile-help">Identitas ini digunakan pada akun Anda. Hak akses dikelola oleh Super Admin.</div>
            </header>
            <div class="profile-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="profile-name" class="profile-label">Nama lengkap</label>
                        <input id="profile-name" type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control bg-dark text-light border-white border-opacity-10 @error('name') is-invalid @enderror" maxlength="255" required>
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="profile-nip" class="profile-label">NIP <span class="fw-normal text-white-50">(opsional)</span></label>
                        <input id="profile-nip" type="text" name="nip" value="{{ old('nip', $user->nip) }}" class="form-control bg-dark text-light border-white border-opacity-10 @error('nip') is-invalid @enderror" maxlength="30" autocomplete="off">
                        @error('nip') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="profile-email" class="profile-label">Email / Username</label>
                        <input id="profile-email" type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control bg-dark text-light border-white border-opacity-10 @error('email') is-invalid @enderror" maxlength="255" required autocomplete="username">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3">
                        <label class="profile-label">Role</label>
                        <div class="profile-readonly">{{ $roleLabel }}</div>
                    </div>
                    <div class="col-md-3">
                        <label class="profile-label">Instansi / Dinas</label>
                        <div class="profile-readonly">{{ $user->department?->nama_dinas ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </section>

        <section class="profile-card mb-3">
            <header class="profile-card-heading">
                <h2 class="h6 text-white mb-1"><i class="fas fa-lock text-warning me-2"></i>Ubah Password</h2>
                <div class="profile-help">Kosongkan jika tidak ingin mengganti password.</div>
            </header>
            <div class="profile-card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="current-password" class="profile-label">Password saat ini</label>
                        <input id="current-password" type="password" name="current_password" class="form-control bg-dark text-light border-white border-opacity-10 @error('current_password') is-invalid @enderror" autocomplete="current-password">
                        @error('current_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="new-password" class="profile-label">Password baru</label>
                        <input id="new-password" type="password" name="new_password" class="form-control bg-dark text-light border-white border-opacity-10 @error('new_password') is-invalid @enderror" minlength="8" autocomplete="new-password">
                        @error('new_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="new-password-confirmation" class="profile-label">Konfirmasi password baru</label>
                        <input id="new-password-confirmation" type="password" name="new_password_confirmation" class="form-control bg-dark text-light border-white border-opacity-10" minlength="8" autocomplete="new-password">
                    </div>
                </div>
            </div>
        </section>

    </form>

    <section class="profile-card mb-3" aria-labelledby="system-information-title">
        <header class="profile-card-heading">
            <h2 id="system-information-title" class="h6 text-white mb-1"><i class="fas fa-circle-info text-info me-2"></i>Informasi Sistem &amp; Dukungan</h2>
            <div class="profile-help">Informasi resmi aplikasi dan kontak yang dikelola melalui Master Settings.</div>
        </header>
        <div class="profile-card-body">
            <div class="row g-3">
                <div class="col-lg-4">
                    <article class="profile-info-card">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="profile-info-icon"><i class="fas fa-building"></i></div>
                            <div><div class="profile-help">Pengelola sistem</div><div class="profile-info-value">{{ $agencyName }}</div></div>
                        </div>
                        <div class="profile-info-copy">{{ $governmentName }}</div>
                        <div class="profile-info-copy mt-2">{{ $productName }} mendukung pengelolaan pengajuan dan dokumen.</div>
                    </article>
                </div>
                <div class="col-lg-4">
                    <article class="profile-info-card">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="profile-info-icon"><i class="fas fa-headset"></i></div>
                            <div><div class="profile-help">Kontak resmi</div><div class="profile-info-value">Dukungan dan informasi</div></div>
                        </div>
                        @if (filled($supportContact))
                            <div class="profile-info-copy">
                                @if (filter_var($supportContact, FILTER_VALIDATE_EMAIL))
                                    <a class="profile-info-link" href="mailto:{{ $supportContact }}">{{ $supportContact }}</a>
                                @else
                                    {{ $supportContact }}
                                @endif
                            </div>
                        @else
                            <div class="profile-info-copy">Kontak dukungan belum diatur. Silakan hubungi administrator sistem.</div>
                        @endif
                        @if (filled($officeAddress))
                            <div class="profile-info-copy mt-2"><i class="fas fa-location-dot me-1"></i>{{ $officeAddress }}</div>
                        @endif
                    </article>
                </div>
                <div class="col-lg-4">
                    <article class="profile-info-card">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="profile-info-icon"><i class="fas fa-shield-halved"></i></div>
                            <div><div class="profile-help">Informasi operasional</div><div class="profile-info-value">Akun dan keamanan</div></div>
                        </div>
                        <div class="profile-info-copy">Untuk kendala akun atau bantuan teknis, hubungi kontak resmi di atas atau administrator sistem.</div>
                        <div class="profile-info-copy mt-2"><i class="fas fa-clock me-1"></i>Zona waktu kebijakan: {{ $policyTimezone }}</div>
                    </article>
                </div>
            </div>
            <div class="text-end text-white-50 small mt-3">&copy; {{ now()->year }} {{ $productName }}. Hak cipta oleh Nizam Achda Damopolii.</div>
        </div>
    </section>
</div>
@endsection
