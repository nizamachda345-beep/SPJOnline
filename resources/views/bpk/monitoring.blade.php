@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card mb-4 border-0 shadow-sm border-start border-start-gold">
        <div class="card-header bg-dark text-white py-3">
            <h6 class="mb-0 fw-bold"><i class="fas fa-filter me-2"></i>Filter Audit Berkas</h6>
        </div>
        <div class="card-body">
            <form action="{{ url()->current() }}" method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="small fw-bold text-uppercase text-muted">Thn Anggaran</label>
                    <select name="tahun" class="form-select shadow-none form-select-sm">
                        @php $currentYear = date('Y'); @endphp
                        @for($i = $currentYear; $i >= $currentYear - 5; $i--)
                            <option value="{{ $i }}" {{ request('tahun', $currentYear) == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="small fw-bold text-uppercase text-muted">Tanggal Input</label>
                    <input type="date" name="tanggal" class="form-control shadow-none form-select-sm" value="{{ request('tanggal') }}">
                </div>

                <div class="col-md-2">
                    <label class="small fw-bold text-uppercase text-muted">Nomor Berkas</label>
                    <input type="text" name="nomor" class="form-control shadow-none form-select-sm" placeholder="Cari nomor..." value="{{ request('nomor') }}">
                </div>

                <div class="col-md-2">
                    <label class="small fw-bold text-uppercase text-muted">Asal Instansi</label>
                    <select name="dinas" class="form-select shadow-none form-select-sm">
                        <option value="">-- Semua Dinas --</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}" {{ request('dinas') == $d->id ? 'selected' : '' }}>
                                {{ $d->nama_dinas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="small fw-bold text-uppercase text-muted">Min. Nominal</label>
                    <input type="number" name="min_nominal" class="form-control shadow-none form-select-sm" placeholder="Contoh: 1000000" value="{{ request('min_nominal') }}">
                </div>

                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-dark btn-sm btn-pill w-100" title="Cari">
                        <i class="fas fa-search me-1"></i> Cari
                    </button>
                    <a href="{{ url()->current() }}" class="btn btn-light btn-sm btn-pill border w-100" title="Reset">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0 border-start border-start-gold">
        <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold"><i class="fas fa-file-invoice me-2"></i>Audit Berkas Digital - BPK</h5>
                <small class="text-muted">Laporan ringkas untuk pemeriksaan audit digital.</small>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-3">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'dinas', 'order' => request('order') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none text-dark">
                                    SKPD <i class="fas fa-sort{{ request('sort') == 'dinas' ? (request('order') == 'asc' ? '-alpha-down' : '-alpha-up') : '' }} ms-1"></i>
                                </a>
                            </th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'order' => request('order') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none text-dark">
                                    Tanggal Input <i class="fas fa-sort{{ request('sort') == 'created_at' ? (request('order') == 'asc' ? '-numeric-down' : '-numeric-up') : '' }} ms-1"></i>
                                </a>
                            </th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'nomor', 'order' => request('order') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none text-dark">
                                    Nomor SPP/SPM <i class="fas fa-sort{{ request('sort') == 'nomor' ? (request('order') == 'asc' ? '-numeric-down' : '-numeric-up') : '' }} ms-1"></i>
                                </a>
                            </th>
                            <th>
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'nominal', 'order' => request('order') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none text-dark">
                                    Total Pencairan <i class="fas fa-sort{{ request('sort') == 'nominal' ? (request('order') == 'asc' ? '-amount-down' : '-amount-up') : '' }} ms-1"></i>
                                </a>
                            </th>
                            <th class="text-center">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'status', 'order' => request('order') == 'asc' ? 'desc' : 'asc']) }}" class="text-decoration-none text-dark">
                                    Status <i class="fas fa-sort{{ request('sort') == 'status' ? (request('order') == 'asc' ? '-alpha-down' : '-alpha-up') : '' }} ms-1"></i>
                                </a>
                            </th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $s)
                        <tr>
                            <td class="ps-3 fw-bold text-dark">{{ $s->department->nama_dinas }}</td>
                            <td><span>{{ $s->created_at->format('d/m/y') }}</span></td>
                            <td><span class="badge bg-light text-dark border">{{ $s->nomor_spp_spm }}</span></td>
                            <td class="text-primary fw-bold">Rp {{ number_format($s->total_pencairan, 0, ',', '.') }}</td>
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
                                <a href="{{ route('bpk.show', $s->id) }}" class="btn btn-sm btn-outline-dark btn-pill shadow-sm">
                                    <i class="fas fa-search me-1"></i> Audit
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="text-center py-5">
                                    <i class="fas fa-box-open fa-3x mb-3 text-muted"></i>
                                    <p class="mb-0 text-muted">Tidak ada data berkas yang sesuai dengan kriteria audit Anda. Silakan sesuaikan filter atau coba kembali nanti.</p>
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