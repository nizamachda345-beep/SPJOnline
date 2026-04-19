@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-filter me-2 text-primary"></i>Filter Pencarian Berkas</h6>
        </div>
        <div class="card-body">
            <form action="{{ url()->current() }}" method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="small fw-bold text-uppercase text-muted">Nomor SPP/SPM</label>
                    <input type="text" name="nomor" class="form-control shadow-none" placeholder="Cari nomor..." value="{{ request('nomor') }}">
                </div>

                <div class="col-md-2">
                    <label class="small fw-bold text-uppercase text-muted">Asal Instansi</label>
                    <select name="dinas" class="form-select shadow-none">
                        <option value="">-- Semua Dinas --</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}" {{ request('dinas') == $d->id ? 'selected' : '' }}>
                                {{ $d->nama_dinas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="small fw-bold text-uppercase text-muted">Tanggal Input</label>
                    <input type="date" name="tanggal" class="form-control shadow-none" value="{{ request('tanggal') }}">
                </div>

                <div class="col-md-2">
                    <label class="small fw-bold text-uppercase text-muted">Judul Kegiatan</label>
                    <input type="text" name="kegiatan" class="form-control shadow-none" placeholder="Cari judul kegiatan..." value="{{ request('kegiatan') }}">
                </div>

                <div class="col-md-2">
                    <label class="small fw-bold text-uppercase text-muted">Min. Nominal (Rp)</label>
                    <input type="number" name="min_nominal" class="form-control shadow-none" placeholder="Contoh: 10000000" value="{{ request('min_nominal') }}">
                </div>

                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary btn-pill w-100" title="Terapkan Filter">
                        <i class="fas fa-search me-2"></i>
                    </button>
                    <a href="{{ url()->current() }}" class="btn btn-light btn-pill border w-100" title="Bersihkan Filter">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0 border-start border-start-gold">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold text-primary"><i class="fas fa-search me-2"></i>Monitoring Berkas Global</h5>
                <small class="text-muted">Lihat status terkini untuk keseluruhan pengajuan.</small>
            </div>
            <span class="badge bg-light text-dark border">Mode: {{ auth()->user()->role_label }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-3">Asal Dinas</th>
                            <th>Nomor SPP/SPM</th>
                            <th>Judul Kegiatan</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $s)
                        <tr>
                            <td class="ps-3 fw-bold text-dark">{{ $s->department->nama_dinas }}</td>
                            <td><code class="text-dark fw-bold">{{ $s->nomor_spp_spm }}</code></td>
                            <td class="small text-muted">{{ Str::limit($s->judul_kegiatan, 40) }}</td>
                            <td>
                                @if($s->status == 'disetujui')
                                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Disahkan</span>
                                @elseif($s->status == 'proses verifikasi')
                                    <span class="badge bg-info"><i class="fas fa-user-check me-1"></i>Proses Verifikasi</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                            <td class="text-center pe-3">
                                <div class="btn-group">
                                    <a href="{{ route('monitoring.show', $s->id) }}" class="btn btn-sm btn-light border btn-pill" title="Lihat Detail">
                                        <i class="fas fa-eye text-primary"></i>
                                    </a>
                                    @role('super_admin')
                                        @if($s->status != 'pending')
                                        <form action="{{ route('admin.submissions.reset', $s->id) }}" method="POST" onsubmit="return confirm('Peringatan: Verifikasi/Validasi akan dihapus dan status kembali ke awal. Lanjutkan?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-light border btn-pill" title="Batalkan Verifikasi/Validasi">
                                                <i class="fas fa-undo-alt text-danger"></i>
                                            </button>
                                        </form>
                                        @endif
                                    @endrole
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5">
                                <div class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x mb-3 text-muted"></i>
                                    <p class="mb-0 text-muted">Data tidak tersedia sesuai kriteria saat ini. Silakan sesuaikan filter atau coba lagi nanti.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection