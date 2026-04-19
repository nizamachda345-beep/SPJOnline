@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow-sm border-0" style="border-radius: 1rem;">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="mb-3">
                            <i class="fas fa-file-invoice-dollar fa-3x text-primary"></i>
                        </div>
                        <h4 class="fw-bold text-dark">Login iDJ</h4>
                        <p class="text-muted small">Silakan masuk untuk mengelola arsip digital</p>
                    </div>

                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-secondary">Alamat Email</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope text-muted"></i>
                                </span>
                                <input type="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="nama@idj.test"
                                    value="{{ old('email') }}" required autofocus>
                            </div>
                            @error('email')
                                <div class="text-danger" style="font-size: 0.75rem; margin-top: 4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label small fw-bold text-secondary">Password</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock text-muted"></i>
                                </span>
                                <input type="password" name="password"
                                    class="form-control"
                                    placeholder="Masukkan password" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-pill shadow-sm w-100">
                            MASUK SEKARANG <i class="fas fa-sign-in-alt ms-2"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4">
                <p class="text-muted" style="font-size: 0.75rem;">
                    &copy; 2026 iDJ System <br>
                    <span class="fw-bold">Kabupaten Bolaang Mongondow</span>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection