<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login iDJ - Bolaang Mongondow</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
    :root {
        --primary-navy: #0d2137;
        --accent-blue: #0d6efd;
    }

    body {
        min-height: 100vh;
        display: flex;
        align-items: center;
        font-family: 'Inter', sans-serif;
        margin: 0;
        overflow: hidden; /* Mencegah scrollbar saat loading Vanta */
    }

    /* ADVANCED GLASSMORPHISM CARD */
    .login-card {
        border: 1px solid rgba(255, 255, 255, 0.18); /* Border tipis untuk efek kaca */
        border-radius: 1.5rem;
        
        /* Transparansi dan Blur */
        background: rgba(255, 255, 255, 0.65); /* Lebih transparan */
        backdrop-filter: blur(25px); /* Efek blur kaca yang kuat */
        -webkit-backdrop-filter: blur(25px); /* Dukungan Safari */
        
        box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3); /* Shadow dalam namun halus */
        
        /* Transisi agar terasa 'hidup' */
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        z-index: 2;
    }

    /* Hover Interaction: Terangkat & Shadow Membesar */
    .login-card:hover {
        transform: translateY(-8px);
        background: rgba(255, 255, 255, 0.8); /* Sedikit lebih terang saat hover */
        box-shadow: 0 20px 50px 0 rgba(0, 0, 0, 0.4);
    }

    /* Optimalisasi Input Field */
    .input-group-text {
        background: rgba(248, 249, 25a, 0.5); /* Semi transparan */
        border-right: none;
        color: #888;
    }

    .form-control {
        border-left: none;
        padding: 0.75rem 1rem;
        background: rgba(255, 255, 255, 0.5); /* Semi transparan */
    }
    
    .form-control:focus {
        background: rgba(255, 255, 255, 0.8); /* Terang saat fokus */
        border-color: #dee2e6;
        box-shadow: none;
    }

    /* Tombol Login Berdenyut (Pulse) */
    .btn-login {
        background: var(--accent-blue);
        border: none;
        border-radius: 50px;
        padding: 0.8rem;
        font-weight: 700;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .btn-login:hover {
        background: #0b5ed7;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(13, 110, 253, 0.5);
    }
    
    /* Animasi Denyut pada tombol saat halaman dimuat */
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(13, 110, 253, 0); }
        100% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0); }
    }
    .btn-pulse {
        animation: pulse 2s infinite;
    }

    .brand-icon {
        background: linear-gradient(135deg, #0d6efd, #0dcaf0);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .extra-small {
        font-size: 0.65rem;
    }
    
    /* Vanta Background Fix */
    #vanta-canvas {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        z-index: -1;
    }
</style>
</head>
<body id="vanta-bg">

<div id="vanta-canvas"></div>

<div class="container" style="z-index: 1;">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            
            <div class="card login-card">
                <div class="card-body p-4 p-md-5">
                    
                    {{-- Logo & Brand: Lebih Rapi --}}
                    <div class="text-center mb-5">
                        <div class="mb-3">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/d/d0/Lambang_Kabupaten_Bolaang_Mongondow.png?_=20190106080338" alt="Logo iDJ" class="" width="70">
                            <!-- <i class="fas fa-file-invoice-dollar fa-4x brand-icon"></i> -->
                        </div>
                        <h3 class="fw-bold text-dark">iDJ <span class="text-primary">System</span></h3>
                        <p class="text-muted small uppercase fw-semibold" style="letter-spacing: 1px;">Sistem Dokumen Juara</p>
                    </div>

                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        
                        {{-- Input Email --}}
                        <div class="mb-3">
                            <label class="form-label extra-small fw-bold text-secondary text-uppercase mb-1">Email</label>
                            <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope text-muted"></i>
                                </span>
                                <input type="email" name="email" class="form-control" placeholder="email@bolmong.go.id" required autofocus>
                            </div>
                        </div>

                        {{-- Input Password --}}
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <label class="form-label extra-small fw-bold text-secondary text-uppercase">Password</label>
                            </div>
                            <div class="input-group shadow-sm rounded-3 overflow-hidden">
                                <span class="input-group-text">
                                    <i class="fas fa-lock text-muted"></i>
                                </span>
                                <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                            </div>
                        </div>

                        {{-- Submit Button: Berdenyut --}}
                        <button type="submit" class="btn btn-primary btn-login w-100 shadow-sm text-white btn-pulse">
                            LOGIN <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4">
                <p class="text-white extra-small opacity-75">
                    &copy; 2026 iDJ System <br>
                    <span class="fw-bold">Nizam Achda</span>
                </p>
            </div>

        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r121/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vanta@latest/dist/vanta.waves.min.js"></script>

<script>
    // Inisialisasi Vanta Waves pada elemen #vanta-canvas
    VANTA.WAVES({
      el: "#vanta-canvas",
      mouseControls: true,
      touchControls: true,
      gyroControls: false,
      minHeight: 200.00,
      minWidth: 200.00,
      scale: 1.00,
      scaleMobile: 1.00,
      color: 0x0d2137, // Warna Navy disesuaikan dengan tema iDJ
      shininess: 30.00,
      waveHeight: 15.00,
      waveSpeed: 0.8,
      zoom: 0.75
    })
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>