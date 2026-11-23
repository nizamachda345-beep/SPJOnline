<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#101827">
    @php
        $maintenanceSettings = $systemSettings ?? app(\App\Services\SystemSettingsService::class)->all();
        $maintenanceActive = ($maintenanceSettings['system.maintenance_mode'] ?? '0') === '1';
        $maintenanceProduct = filled($maintenanceSettings['branding.product_name'] ?? null)
            ? $maintenanceSettings['branding.product_name']
            : 'SPJOnline';
        $maintenanceGovernment = filled($maintenanceSettings['branding.government_short_name'] ?? null)
            ? $maintenanceSettings['branding.government_short_name']
            : 'BOLMONG';
        $maintenanceLogoPath = $maintenanceSettings['branding.application_logo'] ?? '';
        $maintenanceLogoDisk = \Illuminate\Support\Facades\Storage::disk('public');
        $maintenanceLogo = filled($maintenanceLogoPath) && $maintenanceLogoDisk->exists($maintenanceLogoPath)
            ? $maintenanceLogoDisk->url($maintenanceLogoPath)
            : (file_exists(public_path('logo_bolmong.png')) ? asset('logo_bolmong.png') : null);
        $maintenanceContact = $maintenanceSettings['branding.contact'] ?? '';
    @endphp
    <title>{{ $maintenanceActive ? 'Sedang Pemeliharaan' : 'Layanan Sementara Tidak Tersedia' }} — {{ $maintenanceProduct }}</title>
    <style>
        * { box-sizing: border-box; }
        :root { color-scheme: dark; font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        body {
            min-height: 100vh; margin: 0; display: grid; place-items: center; padding: 28px;
            color: #f8fafc; background:
                radial-gradient(ellipse at 18% 12%, rgba(37, 99, 235, .24), transparent 38%),
                radial-gradient(ellipse at 88% 85%, rgba(14, 165, 233, .14), transparent 34%),
                linear-gradient(135deg, #0b1220 0%, #172235 52%, #101827 100%);
            overflow: hidden;
        }
        body::before, body::after { content: ""; position: fixed; z-index: 0; border: 1px solid rgba(148, 163, 184, .10); border-radius: 50%; pointer-events: none; }
        body::before { width: 620px; height: 620px; top: -330px; right: -170px; }
        body::after { width: 480px; height: 480px; bottom: -300px; left: -160px; }
        .maintenance-shell { position: relative; z-index: 1; width: min(100%, 680px); }
        .brand { display: flex; align-items: center; gap: 12px; margin: 0 0 18px 8px; color: #cbd5e1; }
        .brand-logo { width: 42px; height: 42px; flex: 0 0 42px; display: grid; place-items: center; padding: 0; background: transparent; }
        .brand-logo img { max-width: 100%; max-height: 100%; object-fit: contain; }
        .brand-name { font-size: .95rem; font-weight: 750; letter-spacing: .02em; color: #f8fafc; }
        .brand-region { margin-top: 2px; font-size: .68rem; letter-spacing: .16em; color: #94a3b8; }
        .maintenance-card { overflow: hidden; border: 1px solid rgba(148,163,184,.22); border-radius: 24px; background: linear-gradient(145deg, rgba(30,41,59,.88), rgba(15,23,42,.84)); box-shadow: 0 28px 90px rgba(0,0,0,.42), inset 0 1px rgba(255,255,255,.06); backdrop-filter: blur(18px); }
        .card-top { display: flex; align-items: center; gap: 22px; padding: clamp(28px, 6vw, 52px); }
        .illustration { width: 112px; height: 112px; flex: 0 0 112px; display: grid; place-items: center; position: relative; border: 1px solid rgba(96,165,250,.24); border-radius: 28px; background: linear-gradient(145deg, rgba(37,99,235,.18), rgba(14,165,233,.08)); color: #60a5fa; }
        .illustration::before { content: ""; position: absolute; inset: 10px; border: 1px solid rgba(96,165,250,.18); border-radius: 21px; }
        .illustration svg { width: 52px; height: 52px; position: relative; }
        .copy { min-width: 0; }
        .status { display: inline-flex; align-items: center; gap: 8px; margin-bottom: 14px; padding: 7px 11px; border: 1px solid rgba(251,191,36,.25); border-radius: 999px; background: rgba(245,158,11,.10); color: #fcd34d; font-size: .7rem; font-weight: 750; letter-spacing: .1em; text-transform: uppercase; }
        .status-dot { width: 7px; height: 7px; border-radius: 50%; background: currentColor; box-shadow: 0 0 12px currentColor; }
        h1 { margin: 0; font-size: clamp(1.6rem, 4vw, 2.25rem); line-height: 1.15; letter-spacing: -.035em; }
        .lead { max-width: 440px; margin: 12px 0 0; color: #b8c4d5; font-size: .98rem; line-height: 1.65; }
        .card-bottom { display: flex; align-items: center; justify-content: space-between; gap: 20px; padding: 18px clamp(28px, 6vw, 52px); border-top: 1px solid rgba(148,163,184,.14); background: rgba(2,6,23,.22); }
        .service-note { display: flex; align-items: flex-start; gap: 10px; color: #94a3b8; font-size: .8rem; line-height: 1.5; }
        .service-note svg { width: 17px; height: 17px; flex: 0 0 17px; margin-top: 1px; color: #60a5fa; }
        .code { flex: 0 0 auto; color: #64748b; font: 600 .72rem/1.4 ui-monospace, SFMono-Regular, Menlo, monospace; }
        .progress { height: 3px; overflow: hidden; background: rgba(148,163,184,.09); }
        .progress span { display: block; width: 34%; height: 100%; border-radius: 4px; background: linear-gradient(90deg, #2563eb, #38bdf8); animation: progress 2.8s ease-in-out infinite alternate; }
        .contact { margin: 18px 8px 0; color: #94a3b8; font-size: .78rem; text-align: center; }
        .contact a { color: #bfdbfe; text-decoration: none; }
        @keyframes progress { from { transform: translateX(-8%); } to { transform: translateX(205%); } }
        @media (max-width: 560px) {
            body { padding: 18px; }
            .card-top { align-items: flex-start; flex-direction: column; gap: 20px; padding: 28px 24px; }
            .illustration { width: 82px; height: 82px; flex-basis: 82px; border-radius: 22px; }
            .illustration::before { inset: 7px; border-radius: 16px; }
            .illustration svg { width: 40px; height: 40px; }
            .card-bottom { align-items: flex-start; flex-direction: column; gap: 12px; padding: 18px 24px; }
        }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; scroll-behavior: auto !important; } }
    </style>
</head>
<body>
    <main class="maintenance-shell">
        <div class="brand">
            @if($maintenanceLogo)
                <div class="brand-logo"><img src="{{ $maintenanceLogo }}" alt="Logo {{ $maintenanceGovernment }}"></div>
            @endif
            <div>
                <div class="brand-name">{{ $maintenanceProduct }}</div>
                <div class="brand-region">{{ $maintenanceGovernment }}</div>
            </div>
        </div>

        <section class="maintenance-card" aria-labelledby="maintenanceTitle">
            <div class="card-top">
                <div class="illustration" aria-hidden="true">
                    <svg viewBox="0 0 64 64" fill="none">
                        <path d="M25 8h14l3 10-10 7-10-7 3-10Z" stroke="currentColor" stroke-width="3" stroke-linejoin="round"/>
                        <path d="m21 19-9 7 5 8 11-3m15-12 9 7-5 8-11-3M24 38l-4 12 12 7 12-7-4-12" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M32 25v12m0 6v.5" stroke="currentColor" stroke-width="4" stroke-linecap="round"/>
                    </svg>
                </div>
                <div class="copy">
                    <div class="status"><span class="status-dot"></span>{{ $maintenanceActive ? 'Pemeliharaan Sistem' : 'Layanan Sementara' }}</div>
                    <h1 id="maintenanceTitle">{{ $maintenanceActive ? 'Kami sedang melakukan pemeliharaan' : 'Layanan sementara tidak tersedia' }}</h1>
                    <p class="lead">{{ $maintenanceActive ? 'Akses aplikasi sedang dibatasi sementara agar pembaruan dan pemeriksaan sistem dapat dilakukan dengan aman.' : 'Permintaan Anda belum dapat diproses saat ini. Silakan coba kembali beberapa saat lagi.' }}</p>
                </div>
            </div>
            <div class="progress" aria-hidden="true"><span></span></div>
            <div class="card-bottom">
                <div class="service-note">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8"/><path d="M12 7v5l3 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                    <span>{{ $maintenanceActive ? 'Mohon tunggu sebentar, lalu coba akses kembali.' : 'Tim teknis sedang memulihkan layanan.' }}</span>
                </div>
                <div class="code">HTTP 503</div>
            </div>
        </section>

        @if(filled($maintenanceContact))
            <p class="contact">Perlu bantuan?
                @if(str_contains($maintenanceContact, '@'))
                    <a href="mailto:{{ $maintenanceContact }}">{{ $maintenanceContact }}</a>
                @else
                    <span>{{ $maintenanceContact }}</span>
                @endif
            </p>
        @endif
    </main>
</body>
</html>
