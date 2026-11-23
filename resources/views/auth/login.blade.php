{{-- View: auth/login --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login {{ filled($systemSettings['branding.product_name'] ?? null) ? $systemSettings['branding.product_name'] : 'SPJOnline' }} - {{ filled($systemSettings['branding.government_name'] ?? null) ? $systemSettings['branding.government_name'] : 'Bolaang Mongondow' }}</title>

    @php
        $productName = filled($systemSettings['branding.product_name'] ?? null)
            ? $systemSettings['branding.product_name']
            : 'SPJOnline';
        $governmentName = filled($systemSettings['branding.government_name'] ?? null)
            ? $systemSettings['branding.government_name']
            : 'Bolaang Mongondow';
        $governmentDisplayName = preg_replace('/^Pemerintah Kabupaten\s+/i', '', trim($governmentName));
        $agencyName = filled($systemSettings['branding.agency_name'] ?? null)
            ? $systemSettings['branding.agency_name']
            : 'Badan Keuangan Daerah';
        $applicationLogoSetting = $systemSettings['branding.application_logo'] ?? '';
        $applicationLogoDisk = \Illuminate\Support\Facades\Storage::disk('public');
        $applicationLogoUrl = filled($applicationLogoSetting) && $applicationLogoDisk->exists($applicationLogoSetting)
            ? $applicationLogoDisk->url($applicationLogoSetting)
            : (file_exists(public_path('logo_bolmong.png')) ? asset('logo_bolmong.png') : null);
    @endphp

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


<style>
        :root {
            --glass-bg: rgba(255, 255, 255, 0.15);
            --glass-border: rgba(255, 255, 255, 0.2);
            --primary-accent: #3b82f6;
        }

        body, html {
            height: 100%;
            margin: 0;
            overflow-x: hidden;
            overflow-y: auto;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        #vanta-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        .login-wrapper {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }

        /* Glassmorphism Card Core */
        .login-card {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            color: white;
        }

        .login-card .card-body {
            padding: 1.5rem !important;
        }

        .login-card .brand-block {
            margin-bottom: 1.25rem !important;
        }

        .login-card .logo-img {
            width: 64px;
            height: 64px;
            object-fit: contain;
        }

        .login-card .form-field {
            margin-bottom: 0.875rem !important;
        }

        .login-card .form-field:last-of-type {
            margin-bottom: 1.25rem !important;
        }

        .login-card .form-label {
            margin-bottom: 0.25rem !important;
        }

        .extra-small {
            font-size: 0.65rem;
            letter-spacing: 0.1rem;
            opacity: 0.8;
        }

        /* Minimalist Inputs */
        .input-group {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: 12px !important;
            transition: all 0.3s ease;
        }

        .input-group:focus-within {
            border-color: var(--primary-accent);
            background: rgba(255, 255, 255, 0.1);
        }

        .input-group-text {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.6);
            padding-left: 1.2rem;
        }

        .form-control {
            background: transparent;
            border: none;
            color: white;
            font-size: 0.85rem;
            padding: 0.8rem 1rem;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        .form-control:focus {
            background: transparent;
            color: white;
            box-shadow: none;
        }

        /* Glass Button Minimalist */
        .btn-glass {
            background: var(--primary-accent);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 0.6rem 1.5rem;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }

        .btn-glass:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
            color: white;
        }

        .logo-img {
            filter: drop-shadow(0 10px 15px rgba(0,0,0,0.2));
            opacity: 0.9;
        }

        .text-soft {
            color: rgba(255, 255, 255, 0.7);
        }

        .login-footer {
            font-size: 0.58rem;
            letter-spacing: 0.04rem;
        }

        .login-footer .government-name {
            white-space: nowrap;
        }
    </style>

</head>
<body>

    <div id="vanta-canvas"></div>

    <div class="login-wrapper container">
        <div class="row justify-content-center w-100">
            <div class="col-md-5 col-lg-4">

                <div class="card login-card">
                    <div class="card-body text-center">

                        <div class="brand-block">
                            @if($applicationLogoUrl)
                                <img src="{{ $applicationLogoUrl }}"
                                     alt="{{ $governmentName }}" class="logo-img mb-2" width="64" height="64">
                            @endif
                            <h4 class="fw-bold mb-1">{{ $productName }}</h4>
                            <p class="extra-small text-uppercase mb-0 text-soft">
                                {{ $agencyName }} <br>
                                <span style="color: var(--primary-accent)">{{ $governmentDisplayName }}</span>
                            </p>
                        </div>

                        <form action="{{ route('login') }}" method="POST">
                            @csrf

@if ($errors->any())
    <div class="alert alert-danger border-0 bg-danger bg-opacity-25 text-white extra-small mb-3 py-2" style="border-radius: 10px;">
        <i class="fas fa-exclamation-circle me-2"></i>
        @foreach ($errors->all() as $error)
            {{ $error }}
        @endforeach
    </div>
@endif
                            <div class="form-field">
                                <label for="loginEmail" class="form-label text-white text-start d-block mb-1">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope-open"></i></span>
                                    <input id="loginEmail" type="email" name="email" class="form-control" placeholder="email@bolmong.go.id" value="{{ old('email') }}" autocomplete="username" required autofocus>
                                </div>
                            </div>

                            <div class="form-field">
                                <label for="loginPassword" class="form-label text-white text-start d-block mb-1">Kata sandi</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    <input id="loginPassword" type="password" name="password" class="form-control" placeholder="Masukkan kata sandi" autocomplete="current-password" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-glass w-100 mb-2">
                                MASUK <i class="fas fa-chevron-right ms-2 small"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <p class="text-white extra-small opacity-50 login-footer">
                        <span class="government-name">{{ $governmentName }}</span><br>
                        <span class="fw-light">&copy; 2026 Nizam Achda Damopolii</span>
                    </p>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r121/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/vanta@latest/dist/vanta.waves.min.js"></script>

    <script>
        if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) VANTA.WAVES({
            el: "#vanta-canvas",
            mouseControls: true,
            touchControls: true,
            gyroControls: false,
            minHeight: 200.00,
            minWidth: 200.00,
            scale: 1.00,
            scaleMobile: 1.00,
            color: 0x070b14,
            shininess: 20.00,
            waveHeight: 15.00,
            waveSpeed: 0.40,
            zoom: 0.8
        })
    </script>




</body>
</html>
