{{-- View: admin/settings/index --}}
@extends('layouts.app')

@section('content')
@php
    $applicationLogoPreviewUrl = filled($settings['branding.application_logo'])
        ? url('/storage/' . ltrim($settings['branding.application_logo'], '/'))
        : asset('logo_bolmong.png');
    $documentLogoPreviewUrl = filled($settings['branding.document_logo'])
        ? url('/storage/' . ltrim($settings['branding.document_logo'], '/'))
        : asset('logo_bolmong.png');
@endphp
<style>
    #master-settings-page {
        color: #f8fafc;
    }

    #master-settings-page .form-label,
    #master-settings-page .card-header h5,
    #master-settings-page .form-check-label,
    #master-settings-page .table,
    #master-settings-page .table th,
    #master-settings-page .table td {
        color: #f8fafc !important;
    }

    #master-settings-page .form-text,
    #master-settings-page .text-white-50,
    #master-settings-page .form-label .small {
        color: rgba(248, 250, 252, 0.76) !important;
    }

    #master-settings-page .settings-section {
        background: rgba(15, 23, 42, 0.72);
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }

    #master-settings-page .settings-section .card-header {
        background: rgba(15, 23, 42, 0.32) !important;
    }

    #master-settings-page .form-control,
    #master-settings-page .form-select {
        color: #f8fafc !important;
        border-color: rgba(203, 213, 225, 0.42) !important;
    }

    #master-settings-page .form-control:focus,
    #master-settings-page .form-select:focus {
        border-color: #60a5fa !important;
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.2) !important;
    }

    #master-settings-page input[type="file"]::file-selector-button {
        color: #f8fafc;
        background: #334155;
        border-color: #475569;
    }

    #master-settings-page .table code {
        color: #7dd3fc;
    }

    #master-settings-page .settings-section {
        overflow: hidden;
        transition: border-color .2s ease, background-color .2s ease;
        scroll-margin-top: 6rem;
    }

    #master-settings-page .settings-section .card-header {
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
    }

    #master-settings-page .form-label {
        margin-bottom: .45rem;
        font-size: .84rem;
        font-weight: 600;
        letter-spacing: .01em;
    }

    #master-settings-page .form-text {
        font-size: .78rem;
        line-height: 1.5;
    }

    #master-settings-page .form-control,
    #master-settings-page .form-select {
        min-height: 44px;
        border-radius: .7rem;
    }

    #master-settings-page textarea.form-control {
        min-height: auto;
        line-height: 1.5;
    }

    #master-settings-page .branding-upload-card {
        height: 100%;
        padding: 1rem;
        border: 1px solid rgba(255, 255, 255, .12);
        border-radius: .9rem;
        background: rgba(2, 6, 23, .22);
    }

    #master-settings-page .branding-logo-preview {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 112px;
        margin-top: .85rem;
        padding: .75rem;
        overflow: hidden;
        border: 1px dashed rgba(203, 213, 225, .35);
        border-radius: .75rem;
        background: rgba(255, 255, 255, .96);
    }

    #master-settings-page .branding-logo-preview img {
        display: block;
        max-width: 100%;
        max-height: 88px;
        object-fit: contain;
    }

    #settings-identity .branding-upload-card {
        padding: .75rem;
    }

    #settings-identity .branding-upload-card .form-label {
        margin-bottom: .25rem;
    }

    #settings-identity .branding-upload-card .form-text.mb-2 {
        margin-bottom: .45rem !important;
    }

    #settings-identity .logo-preview-input {
        min-height: 38px;
        padding: 0 .45rem;
        font-size: .75rem;
    }

    #settings-identity .logo-preview-input::file-selector-button {
        height: 36px;
        margin: 0 .65rem 0 -.45rem;
        padding: .4rem .7rem;
        border: 0;
        border-right: 1px solid rgba(255, 255, 255, .12);
        border-radius: .6rem 0 0 .6rem;
        color: #f8fafc;
        background: #334155;
        font: inherit;
        cursor: pointer;
    }

    #settings-identity .branding-logo-preview {
        min-height: 60px;
        margin-top: .4rem;
        padding: .35rem;
    }

    #settings-identity .branding-upload-card > .form-text.mt-2 {
        margin-top: .4rem !important;
        margin-bottom: 0;
    }

    #settings-identity .branding-logo-preview img {
        max-height: 46px;
    }

    #settings-identity .branding-upload-card .form-text {
        line-height: 1.3;
    }

    #master-settings-page .settings-savebar {
        position: sticky;
        top: 4.5rem;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
        padding: .75rem 1rem;
        border: 1px solid rgba(255, 255, 255, .14);
        border-radius: .9rem;
        background: rgba(15, 23, 42, .94);
        box-shadow: 0 8px 24px rgba(0, 0, 0, .2);
        backdrop-filter: blur(14px);
    }

    #master-settings-page .settings-savebar .btn {
        white-space: nowrap;
    }

    #master-settings-page .history-toggle {
        color: #e2e8f0;
        text-decoration: none;
    }

    #master-settings-page .history-toggle:hover {
        color: #fff;
    }

    #master-settings-page .settings-jumpnav {
        display: flex;
        flex-wrap: wrap;
        gap: .5rem;
        margin-bottom: 1rem;
        padding: .75rem;
        border: 1px solid rgba(255, 255, 255, .1);
        border-radius: .9rem;
        background: rgba(15, 23, 42, .58);
    }

    #master-settings-page .settings-jumpnav a {
        color: #e2e8f0;
        border: 1px solid rgba(203, 213, 225, .24);
        background: rgba(255, 255, 255, .04);
        text-decoration: none;
        transition: background-color .15s ease, border-color .15s ease;
    }

    #master-settings-page .settings-jumpnav a:hover,
    #master-settings-page .settings-jumpnav a:focus {
        color: #fff;
        border-color: rgba(96, 165, 250, .7);
        background: rgba(59, 130, 246, .18);
    }

    #master-settings-page .section-intro {
        max-width: 72rem;
        margin: 0 0 1rem;
        color: rgba(248, 250, 252, .76);
        font-size: .88rem;
        line-height: 1.6;
    }

    #settings-submission .schedule-policy-note {
        display: flex;
        align-items: flex-start;
        gap: .75rem;
        padding: .8rem 1rem;
        border: 1px solid rgba(96, 165, 250, .22);
        border-left: 3px solid #60a5fa;
        border-radius: .75rem;
        color: rgba(248, 250, 252, .82);
        background: rgba(15, 23, 42, .38);
        font-size: .8rem;
        line-height: 1.5;
    }

    #settings-submission .schedule-message-card {
        height: 100%;
        padding: 1rem;
        border: 1px solid rgba(255, 255, 255, .1);
        border-radius: .85rem;
        background: rgba(2, 6, 23, .2);
    }

    #settings-submission .schedule-message-card textarea {
        min-height: 5rem;
        resize: vertical;
    }

    #settings-ba-rekon .ba-rekon-toggle-card,
    #settings-ba-rekon .ba-rekon-message-card {
        height: 100%;
        padding: 1rem;
        border: 1px solid rgba(255, 255, 255, .1);
        border-radius: .85rem;
        background: rgba(2, 6, 23, .2);
    }

    #settings-ba-rekon .ba-rekon-message-card textarea {
        min-height: 5rem;
        resize: vertical;
    }

    #settings-ba-rekon .ba-rekon-day-input .form-control {
        min-width: 0;
        border-top-right-radius: 0 !important;
        border-bottom-right-radius: 0 !important;
    }

    #settings-ba-rekon .ba-rekon-day-input .input-group-text {
        flex: 0 0 5.5rem;
        justify-content: center;
        height: 44px;
        padding-right: .5rem;
        padding-left: .5rem;
        color: rgba(248, 250, 252, .78) !important;
        background: rgba(71, 85, 105, .72) !important;
        border-color: rgba(203, 213, 225, .35) !important;
        border-left: 0 !important;
        border-right: 1px solid rgba(203, 213, 225, .35) !important;
        border-radius: 0 .7rem .7rem 0 !important;
        white-space: nowrap;
    }

    #settings-ba-rekon .ba-rekon-policy-note {
        display: flex;
        align-items: flex-start;
        gap: .75rem;
        padding: .8rem 1rem;
        border: 1px solid rgba(96, 165, 250, .22);
        border-left: 3px solid #60a5fa;
        border-radius: .75rem;
        color: rgba(248, 250, 252, .84);
        background: rgba(15, 23, 42, .38);
        font-size: .8rem;
        line-height: 1.5;
    }

    #settings-security .security-setting-card {
        height: 100%;
        padding: 1rem;
        border: 1px solid rgba(255, 255, 255, .1);
        border-radius: .85rem;
        background: rgba(2, 6, 23, .22);
    }

    #settings-security .security-setting-card .form-label {
        margin-bottom: .65rem;
        color: #f8fafc !important;
    }

    #settings-security #maintenanceMode,
    #settings-ba-rekon #baRekonUploadEnabled {
        width: 2.75rem;
        height: 1.4rem;
        background-size: 1.1rem;
        margin-top: 0;
        cursor: pointer;
    }

    #settings-security .maintenance-toggle-row {
        display: flex;
        align-items: center;
        gap: .65rem;
        min-height: 1.75rem;
        padding-left: 0;
    }

    #settings-security .maintenance-toggle-row .form-check-input,
    #settings-ba-rekon .ba-rekon-toggle-row .form-check-input {
        position: static;
        flex: 0 0 auto;
        margin: 0 !important;
    }

    #settings-security .maintenance-toggle-row .form-check-label,
    #settings-ba-rekon .ba-rekon-toggle-row .form-check-label {
        margin: 0;
        line-height: 1.35;
    }

    #settings-ba-rekon .ba-rekon-toggle-row {
        display: flex;
        align-items: center;
        gap: .65rem;
        min-height: 1.75rem;
        padding-left: 0;
    }

    @media (max-width: 575.98px) {
        #master-settings-page {
            padding-left: .75rem !important;
            padding-right: .75rem !important;
        }

        #master-settings-page .settings-savebar {
            top: 4.25rem;
            padding: .65rem .75rem;
            justify-content: flex-end;
        }

        #master-settings-page .settings-savebar .savebar-hint {
            display: none;
        }

        #master-settings-page .settings-section .card-header {
            flex-wrap: wrap;
            gap: .75rem;
        }
    }
</style>

<div id="master-settings-page" class="container-fluid px-4 text-white">
    <div class="glass-header-dark rounded-4 p-4 mb-3 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center bg-primary bg-opacity-25 text-primary" style="width:48px;height:48px;">
                <i class="fas fa-sliders-h fs-4"></i>
            </div>
            <div>
                <h3 class="fw-bold mb-1">Master Settings</h3>
                <p class="text-white-50 mb-0">Pusat konfigurasi identitas, operasional, akses, dan audit SPJOnline.</p>
            </div>
        </div>
        <a href="{{ route('settings.logs') }}" class="btn btn-outline-light rounded-pill px-3">
            <i class="fas fa-clock-rotate-left me-2"></i>Riwayat Perubahan
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger rounded-3">
            <strong>Pengaturan belum tersimpan.</strong>
            <ul class="mb-0 mt-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <nav class="settings-jumpnav" aria-label="Navigasi bagian Master Settings">
        <a class="btn btn-sm rounded-pill px-3" href="#settings-identity">01 Identitas</a>
        <a class="btn btn-sm rounded-pill px-3" href="#settings-submission">02 Pengajuan</a>
        <a class="btn btn-sm rounded-pill px-3" href="#settings-ba-rekon">03 BA Rekon</a>
        <a class="btn btn-sm rounded-pill px-3" href="#settings-users-opd">04 User dan OPD</a>
        <a class="btn btn-sm rounded-pill px-3" href="#settings-security">05 Keamanan</a>
        <a class="btn btn-sm rounded-pill px-3" href="#settings-audit">06 Riwayat</a>
    </nav>

    <form id="master-settings-form" action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="no-ajax">
        @csrf
        @method('PUT')

        <div class="settings-savebar">
            <span class="savebar-hint small text-white-50"><i class="fas fa-circle-info me-1"></i>Perubahan berlaku setelah pengaturan disimpan.</span>
            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2"><i class="fas fa-save me-2"></i>Simpan Pengaturan</button>
        </div>

        <section id="settings-identity" class="card glass-card-dark settings-section border-0 rounded-4 mb-3">
            <div class="card-header bg-transparent border-bottom border-white border-opacity-10 py-3 px-4">
                <h5 class="fw-bold mb-0"><i class="fas fa-building me-2 text-info"></i>01. Identitas Instansi dan Branding</h5>
            </div>
            <div class="card-body p-4">
                <p class="section-intro">Identitas ini menjadi sumber nama dan logo untuk bagian aplikasi serta dokumen yang mendukung branding. Logo aplikasi dan logo dokumen diatur terpisah.</p>
                <div class="row g-4 align-items-stretch">
                    <div class="col-lg-6">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" for="productName">Nama Produk</label>
                                <input class="form-control bg-dark text-white border-secondary" id="productName" name="branding[product_name]" value="{{ old('branding.product_name', $settings['branding.product_name']) }}" required maxlength="80">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="governmentName">Nama Pemerintah Daerah</label>
                                <input class="form-control bg-dark text-white border-secondary" id="governmentName" name="branding[government_name]" value="{{ old('branding.government_name', $settings['branding.government_name']) }}" required maxlength="160">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="governmentShortName">Singkatan Nama Daerah</label>
                                <input class="form-control bg-dark text-white border-secondary" id="governmentShortName" name="branding[government_short_name]" value="{{ old('branding.government_short_name', $settings['branding.government_short_name']) }}" required maxlength="80" aria-describedby="governmentShortNameHint">
                                <div class="form-text" id="governmentShortNameHint">Ditampilkan sebagai subjudul di header aplikasi, misalnya BOLMONG.</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="agencyName">Nama Instansi</label>
                                <input class="form-control bg-dark text-white border-secondary" id="agencyName" name="branding[agency_name]" value="{{ old('branding.agency_name', $settings['branding.agency_name']) }}" required maxlength="160">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="officeAddress">Alamat</label>
                                <input class="form-control bg-dark text-white border-secondary" id="officeAddress" name="branding[office_address]" value="{{ old('branding.office_address', $settings['branding.office_address']) }}" maxlength="500">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="contactInfo">Kontak</label>
                                <input class="form-control bg-dark text-white border-secondary" id="contactInfo" name="branding[contact]" value="{{ old('branding.contact', $settings['branding.contact']) }}" maxlength="160">
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="branding-upload-card">
                                    <label class="form-label" for="applicationLogo">Logo Aplikasi</label>
                                    <div class="form-text mb-2">Untuk halaman login dan header aplikasi.</div>
                                    <input class="form-control bg-dark text-white border-secondary logo-preview-input" id="applicationLogo" type="file" name="branding[application_logo]" accept="image/png,image/jpeg,image/webp" data-preview-target="application-logo-preview" aria-describedby="applicationLogoHint">
                                    <div id="applicationLogoHint" class="form-text mt-2">Format: PNG, JPG, atau WebP. Maks. {{ $settings['security.logo_max_size_mb'] }} MB</div>
                                    <div class="branding-logo-preview"><img id="application-logo-preview" src="{{ $applicationLogoPreviewUrl }}" data-original-src="{{ $applicationLogoPreviewUrl }}" alt="Pratinjau logo aplikasi"></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="branding-upload-card">
                                    <label class="form-label" for="documentLogo">Logo Dokumen</label>
                                    <div class="form-text mb-2">Untuk PDF yang mendukung branding dokumen.</div>
                                    <input class="form-control bg-dark text-white border-secondary logo-preview-input" id="documentLogo" type="file" name="branding[document_logo]" accept="image/png,image/jpeg,image/webp" data-preview-target="document-logo-preview" aria-describedby="documentLogoHint">
                                    <div id="documentLogoHint" class="form-text mt-2">Format: PNG, JPG, atau WebP. Maks. {{ $settings['security.logo_max_size_mb'] }} MB</div>
                                    <div class="branding-logo-preview"><img id="document-logo-preview" src="{{ $documentLogoPreviewUrl }}" data-original-src="{{ $documentLogoPreviewUrl }}" alt="Pratinjau logo dokumen"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="settings-submission" class="card glass-card-dark settings-section border-0 rounded-4 mb-3">
            <div class="card-header bg-transparent border-bottom border-white border-opacity-10 py-3 px-4">
                <h5 class="fw-bold mb-0"><i class="fas fa-calendar-days me-2 text-warning"></i>02. Jadwal Pengajuan Transaksi</h5>
            </div>
            <div class="card-body p-4">
                <p class="section-intro">Aturan ini membatasi pembuatan pengajuan baru. Revisi, verifikasi, dan unggah dokumen lanjutan tetap mengikuti alur serta kewenangan masing-masing.</p>
                <div class="row g-3 align-items-start">
                    <div class="col-lg-4">
                        <label class="form-label" for="submissionScheduleMode">Mode Pengajuan Baru</label>
                        <select class="form-select bg-dark text-white border-secondary" id="submissionScheduleMode" name="submission[schedule_mode]" required>
                            <option value="scheduled" @selected(old('submission.schedule_mode', $settings['submission.schedule_mode']) === 'scheduled')>Otomatis sesuai jadwal</option>
                            <option value="open" @selected(old('submission.schedule_mode', $settings['submission.schedule_mode']) === 'open')>Buka terus (abaikan jadwal dan tenggat BA)</option>
                            <option value="closed" @selected(old('submission.schedule_mode', $settings['submission.schedule_mode']) === 'closed')>Tutup pengajuan baru</option>
                        </select>
                        <div class="form-text text-white-50 mt-2">Akses manual per SKPD tetap berlaku, termasuk saat mode Buka terus.</div>
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label" for="submissionLockStart">Mulai Penguncian Harian</label>
                        <input class="form-control bg-dark text-white border-secondary" id="submissionLockStart" type="time" name="submission[lock_start_time]" value="{{ old('submission.lock_start_time', $settings['submission.lock_start_time']) }}" required>
                        <div class="form-text text-white-50 mt-2">Pengajuan baru mulai ditutup pada waktu ini.</div>
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label" for="submissionLockEnd">Akhir Penguncian Harian</label>
                        <input class="form-control bg-dark text-white border-secondary" id="submissionLockEnd" type="time" name="submission[lock_end_time]" value="{{ old('submission.lock_end_time', $settings['submission.lock_end_time']) }}" required>
                        <div class="form-text text-white-50 mt-2">Penguncian berakhir; syarat akses lainnya tetap berlaku.</div>
                    </div>
                    <div class="col-lg-6">
                        <div class="schedule-message-card">
                            <label class="form-label" for="submissionScheduleMessage">Pesan Saat Jadwal Harian Terkunci</label>
                            <textarea class="form-control bg-dark text-white border-secondary" id="submissionScheduleMessage" name="submission[schedule_message]" rows="3" required maxlength="500">{{ old('submission.schedule_message', $settings['submission.schedule_message']) }}</textarea>
                            <div class="form-text text-white-50 mt-2">Ditampilkan saat mode otomatis sedang dalam jam penguncian. Placeholder: <code>{start}</code>, <code>{end}</code>, <code>{timezone}</code>.</div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="schedule-message-card">
                            <label class="form-label" for="submissionClosedMessage">Pesan Saat Pengajuan Ditutup oleh Admin</label>
                            <textarea class="form-control bg-dark text-white border-secondary" id="submissionClosedMessage" name="submission[closed_message]" rows="3" required maxlength="500">{{ old('submission.closed_message', $settings['submission.closed_message']) }}</textarea>
                            <div class="form-text text-white-50 mt-2">Ditampilkan saat mode pengajuan baru diatur ke Tutup.</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="schedule-policy-note">
                            <i class="fas fa-globe-asia text-info mt-1" aria-hidden="true"></i>
                            <div><strong>Zona waktu kebijakan:</strong> {{ $settings['system.timezone'] }} ({{ $timezoneOptions[$settings['system.timezone']] ?? 'WITA (UTC+8)' }}). Jadwal dan pemeriksaan BA Rekon berlaku untuk pengajuan baru; revisi serta tindak lanjut dokumen mengikuti alurnya masing-masing.</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="settings-ba-rekon" class="card glass-card-dark settings-section border-0 rounded-4 mb-3">
            <div class="card-header bg-transparent border-bottom border-white border-opacity-10 py-3 px-4">
                <h5 class="fw-bold mb-0"><i class="fas fa-file-contract me-2 text-success"></i>03. Jadwal dan Akses BA Rekon</h5>
            </div>
            <div class="card-body p-4">
                <p class="section-intro">Atur kapan BA Rekon dapat diunggah dan sampai tanggal berapa verifikasinya menjadi syarat pengajuan baru. Kedua batas ini memiliki fungsi yang berbeda.</p>
                <div class="row g-3 align-items-start">
                    <div class="col-lg-4">
                        <label class="form-label" for="baRekonDeadlineDay">Batas Verifikasi untuk Akses Pengajuan</label>
                        <div class="input-group ba-rekon-day-input">
                            <input class="form-control bg-dark text-white border-secondary" id="baRekonDeadlineDay" type="number" name="ba_rekon[deadline_day]" min="1" max="28" value="{{ old('ba_rekon.deadline_day', $settings['ba_rekon.deadline_day']) }}" required>
                            <span class="input-group-text bg-secondary text-white border-secondary">Tanggal</span>
                        </div>
                        <div class="form-text text-white-50 mt-2">Mulai hari berikutnya, BA Rekon bulan berjalan menjadi syarat pengajuan baru.</div>
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label" for="baRekonUploadStartDay">Mulai Periode Unggah</label>
                        <div class="input-group ba-rekon-day-input">
                            <input class="form-control bg-dark text-white border-secondary" id="baRekonUploadStartDay" type="number" name="ba_rekon[upload_start_day]" min="1" max="31" value="{{ old('ba_rekon.upload_start_day', $settings['ba_rekon.upload_start_day']) }}" required>
                            <span class="input-group-text bg-secondary text-white border-secondary">Tanggal</span>
                        </div>
                        <div class="form-text text-white-50 mt-2">Hari pertama operator dapat mengunggah BA.</div>
                    </div>
                    <div class="col-lg-4">
                        <label class="form-label" for="baRekonUploadEndDay">Akhir Periode Unggah</label>
                        <div class="input-group ba-rekon-day-input">
                            <input class="form-control bg-dark text-white border-secondary" id="baRekonUploadEndDay" type="number" name="ba_rekon[upload_end_day]" min="1" max="31" value="{{ old('ba_rekon.upload_end_day', $settings['ba_rekon.upload_end_day']) }}" required>
                            <span class="input-group-text bg-secondary text-white border-secondary">Tanggal</span>
                        </div>
                        <div class="form-text text-white-50 mt-2">Hari terakhir unggah, mengikuti zona waktu kebijakan.</div>
                    </div>
                    <div class="col-12">
                        <div class="row g-3 align-items-stretch">
                            <div class="col-lg-4">
                                <div class="ba-rekon-toggle-card">
                                    <div class="form-label d-block mb-0">Penerimaan Upload BA Rekon</div>
                                    <input type="hidden" name="ba_rekon[upload_enabled]" value="0">
                                    <div class="form-check form-switch ba-rekon-toggle-row mt-2">
                                        <input class="form-check-input" type="checkbox" role="switch" id="baRekonUploadEnabled" name="ba_rekon[upload_enabled]" value="1" @checked(old('ba_rekon.upload_enabled', $settings['ba_rekon.upload_enabled']) === '1')>
                                        <label class="form-check-label" for="baRekonUploadEnabled">Aktifkan unggah dan perbaikan</label>
                                    </div>
                                    <div class="form-text text-white-50 mt-2">Jika dinonaktifkan, seluruh unggahan dan perbaikan BA ditutup.</div>
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <div class="ba-rekon-policy-note h-100">
                                    <i class="fas fa-circle-info text-info mt-1" aria-hidden="true"></i>
                                    <div>BA berstatus menunggu atau ditolak tetap dapat diperbaiki di luar rentang tanggal selama penerimaan upload aktif. Pengaturan ini tidak menghapus dokumen maupun riwayat yang sudah ada.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="ba-rekon-message-card">
                            <label class="form-label" for="baRekonClosedMessage">Pesan Saat Upload Ditutup</label>
                            <input class="form-control bg-dark text-white border-secondary" id="baRekonClosedMessage" name="ba_rekon[closed_message]" value="{{ old('ba_rekon.closed_message', $settings['ba_rekon.closed_message']) }}" required maxlength="500">
                            <div class="form-text text-white-50 mt-2">Ditampilkan saat penerimaan upload dinonaktifkan.</div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="ba-rekon-message-card">
                            <label class="form-label" for="baRekonAccessMessage">Pesan Saat BA Belum Terverifikasi</label>
                            <textarea class="form-control bg-dark text-white border-secondary" id="baRekonAccessMessage" name="ba_rekon[access_message]" rows="2" required maxlength="500">{{ old('ba_rekon.access_message', $settings['ba_rekon.access_message']) }}</textarea>
                            <div class="form-text text-white-50 mt-2">Ditampilkan pada informasi akses pengajuan ketika BA belum terverifikasi.</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    <section id="settings-users-opd" class="card glass-card-dark settings-section border-0 rounded-4 mb-3">
        <div class="card-header bg-transparent border-bottom border-white border-opacity-10 py-3 px-4">
            <h5 class="fw-bold mb-0"><i class="fas fa-users me-2 text-primary"></i>04. Pengguna dan OPD</h5>
        </div>
        <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <p class="text-white-50 mb-0">Master Settings menghubungkan Anda ke modul yang mengelola akun, role, dan keterkaitan pengguna dengan OPD. Data dikelola di modul masing-masing.</p>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('users.index') }}" class="btn btn-outline-light rounded-pill px-3">
                    <i class="fas fa-user-gear me-2"></i>Manajemen User
                </a>
                <a href="{{ route('departments.index') }}" class="btn btn-outline-light rounded-pill px-3">
                    <i class="fas fa-building me-2"></i>Manajemen SKPD
                </a>
            </div>
        </div>
    </section>

    <section id="settings-security" class="card glass-card-dark settings-section border-0 rounded-4 mb-3">
        <div class="card-header bg-transparent border-bottom border-white border-opacity-10 py-3 px-4">
            <h5 class="fw-bold mb-0"><i class="fas fa-shield-halved me-2 text-warning"></i>05. Keamanan dan Informasi Sistem</h5>
        </div>
        <div class="card-body p-4">
            <p class="section-intro">Atur batas ukuran unggahan dan zona waktu kebijakan yang digunakan untuk jadwal pengajuan serta periode BA Rekon.</p>
            <div class="row g-3">
                <div class="col-lg-6">
                    <div class="security-setting-card">
                        <div class="form-label"><i class="fas fa-screwdriver-wrench text-danger me-2"></i>Mode maintenance</div>
                        <input type="hidden" name="system[maintenance_mode]" value="0">
                        <div class="form-check form-switch maintenance-toggle-row mt-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="maintenanceMode" name="system[maintenance_mode]" value="1" @checked(old('system.maintenance_mode', $settings['system.maintenance_mode']) === '1')>
                            <label class="form-check-label" for="maintenanceMode">Aktifkan maintenance</label>
                        </div>
                        <div class="form-text text-white-50 mt-2">Saat aktif, hanya akun Master yang dapat membuka halaman sistem. Pengguna lain melihat halaman pemeliharaan.</div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="security-setting-card">
                        <label class="form-label" for="systemTimezone"><i class="fas fa-globe-asia text-warning me-2"></i>Zona waktu kebijakan</label>
                        <select class="form-select bg-dark text-white border-secondary" id="systemTimezone" name="system[timezone]" required>
                            @foreach($timezoneOptions as $timezone => $label)
                                <option value="{{ $timezone }}" @selected(old('system.timezone', $settings['system.timezone']) === $timezone)>{{ $timezone }} — {{ $label }}</option>
                            @endforeach
                        </select>
                        <div class="form-text text-white-50 mt-2">Dipakai untuk jadwal pengajuan dan periode BA Rekon. Timestamp arsip lama tidak diubah.</div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-3">
                <div class="col-lg-4">
                    <div class="security-setting-card">
                        <label class="form-label" for="logoMaxSizeMb"><i class="fas fa-image text-success me-2"></i>Batas ukuran logo</label>
                        <select class="form-select bg-dark text-white border-secondary" id="logoMaxSizeMb" name="security[logo_max_size_mb]" required>
                            @foreach([1, 2, 3, 4, 5] as $sizeMb)
                                <option value="{{ $sizeMb }}" @selected((string) old('security.logo_max_size_mb', $settings['security.logo_max_size_mb']) === (string) $sizeMb)>{{ $sizeMb }} MB</option>
                            @endforeach
                        </select>
                        <div class="form-text text-white-50 mt-2">Berlaku untuk logo aplikasi dan logo dokumen. Format yang diterima tetap PNG, JPG, dan WebP.</div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="security-setting-card">
                        <label class="form-label" for="submissionDocumentMaxSizeMb">Batas per berkas pengajuan</label>
                        <select class="form-select bg-dark text-white border-secondary" id="submissionDocumentMaxSizeMb" name="submission[document_max_size_mb]" required>
                            @foreach(range(1, 20) as $sizeMb)
                                <option value="{{ $sizeMb }}" @selected((string) old('submission.document_max_size_mb', $settings['submission.document_max_size_mb']) === (string) $sizeMb)>{{ $sizeMb }} MB</option>
                            @endforeach
                        </select>
                        <div class="form-text text-white-50">Berlaku untuk PDF pengajuan awal, revisi, serta dokumen susulan SPJ dan STS.</div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="security-setting-card">
                        <label class="form-label" for="submissionSp2dMaxSizeMb">Batas per berkas SP2D</label>
                        <select class="form-select bg-dark text-white border-secondary" id="submissionSp2dMaxSizeMb" name="submission[sp2d_max_size_mb]" required>
                            @foreach(range(1, 20) as $sizeMb)
                                <option value="{{ $sizeMb }}" @selected((string) old('submission.sp2d_max_size_mb', $settings['submission.sp2d_max_size_mb']) === (string) $sizeMb)>{{ $sizeMb }} MB</option>
                            @endforeach
                        </select>
                        <div class="form-text text-white-50">Berlaku untuk unggah SP2D susulan setelah pengajuan disetujui. SP2D TU Nihil yang disertakan pada pengajuan awal mengikuti batas berkas pengajuan.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    </form>

    <section id="settings-audit" class="card glass-card-dark settings-section border-0 rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-transparent border-bottom border-white border-opacity-10 py-3 px-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="fas fa-clock-rotate-left me-2 text-info"></i>06. Riwayat Perubahan Pengaturan</h5>
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-outline-light rounded-pill px-3" type="button" data-bs-toggle="collapse" data-bs-target="#recentSettingsLogs" aria-expanded="false" aria-controls="recentSettingsLogs">
                    <i class="fas fa-chevron-down me-1"></i> Tampilkan ringkasan
                </button>
                <a href="{{ route('settings.logs') }}" class="link-info text-decoration-none">Lihat semua</a>
            </div>
        </div>
        <div class="collapse" id="recentSettingsLogs">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead><tr><th class="ps-4">Waktu</th><th>Pengguna</th><th>Pengaturan</th><th>Nilai Sebelumnya</th><th>Nilai Baru</th></tr></thead>
                <tbody>
                    @forelse($recentLogs as $log)
                        <tr>
                            <td class="ps-4 text-nowrap">{{ $log->changed_at->format('d M Y H:i') }}</td>
                            <td>{{ $log->user?->name ?? 'Pengguna dihapus' }}</td>
                            <td><code>{{ $log->setting_key }}</code></td>
                            <td class="text-break">{{ $log->old_value ?: '—' }}</td>
                            <td class="text-break">{{ $log->new_value ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-white-50 py-4">Belum ada perubahan pengaturan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </div>
    </section>
</div>

<script>
    document.querySelectorAll('#master-settings-page .logo-preview-input').forEach((input) => {
        input.addEventListener('change', () => {
            const image = document.getElementById(input.dataset.previewTarget);
            const file = input.files?.[0];
            if (!image) return;

            if (!file) {
                image.src = image.dataset.originalSrc;
                return;
            }

            if (!file.type.startsWith('image/')) return;

            if (image.dataset.objectUrl) URL.revokeObjectURL(image.dataset.objectUrl);
            image.dataset.objectUrl = URL.createObjectURL(file);
            image.src = image.dataset.objectUrl;
        });
    });

    const recentLogs = document.getElementById('recentSettingsLogs');
    recentLogs?.addEventListener('show.bs.collapse', () => {
        const toggle = document.querySelector('[data-bs-target="#recentSettingsLogs"]');
        if (toggle) toggle.innerHTML = '<i class="fas fa-chevron-up me-1"></i> Sembunyikan ringkasan';
    });
    recentLogs?.addEventListener('hide.bs.collapse', () => {
        const toggle = document.querySelector('[data-bs-target="#recentSettingsLogs"]');
        if (toggle) toggle.innerHTML = '<i class="fas fa-chevron-down me-1"></i> Tampilkan ringkasan';
    });

</script>
@endsection
