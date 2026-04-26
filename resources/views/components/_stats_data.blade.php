        {{-- 1. TOTAL PENGAJUAN --}}
        <div class="col">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="flex-shrink-0 bg-accent-primary text-primary p-3 rounded-3">
                        <i class="fas fa-file-alt fa-2x"></i>
                    </div>
                    <div>
                        <span class="data-label text-muted small">Total Dokumen</span>
                        <h3 class="fw-bold mb-0">{{ $stats['total_berkas'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. PENDING (BARU) --}}
        <div class="col">
            <div class="card h-100 shadow-sm border-0 border-start border-primary border-4">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="flex-shrink-0 bg-light text-primary p-3 rounded-3">
                        <i class="fas fa-hourglass-start fa-2x"></i>
                    </div>
                    <div>
                        <span class="data-label text-muted small">Proses Verifikasi</span>
                        <h3 class="fw-bold mb-0">{{ $stats['pending'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. PERLU REVISI (KASIE BUD) --}}
        <div class="col">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="flex-shrink-0 bg-accent-warning text-warning p-3 rounded-3">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                    <div>
                        <span class="data-label text-muted small">Butuh Revisi</span>
                        <h3 class="fw-bold mb-0">{{ $stats['revisi'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. DITOLAK (KABID) --}}
        <div class="col">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="flex-shrink-0 bg-accent-danger text-danger p-3 rounded-3">
                        <i class="fas fa-times-circle fa-2x"></i>
                    </div>
                    <div>
                        <span class="data-label text-muted small">Ditolak</span>
                        <h3 class="fw-bold mb-0">{{ $stats['rejected'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. SELESAI --}}
        <div class="col">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="flex-shrink-0 bg-accent-success text-success p-3 rounded-3">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                    <div>
                        <span class="data-label text-muted small">Disahkan</span>
                        <h3 class="fw-bold mb-0">{{ $stats['disetujui'] ?? 0 }}</h3>
                    </div>
                </div>
            </div>
        </div>
    