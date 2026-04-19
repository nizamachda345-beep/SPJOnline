@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h3 class="fw-bold text-primary">Antrean Verifikasi Berkas (Kasie BUD)</h3>
            <p class="text-muted">Daftar pengajuan dari seluruh dinas yang perlu diperiksa.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Dinas/Instansi</th>
                            <th>No. SPP/SPM</th>
                            <th>Total Pencairan</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $index => $s)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="fw-bold">{{ $s->department->nama_dinas }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $s->nomor_spp_spm }}</span></td>
                            <td class="fw-bold text-primary">Rp {{ number_format($s->total_pencairan, 0, ',', '.') }}</td>
                            <td>{{ $s->created_at->format('d M Y H:i') }}</td>
                            <td class="text-center">
                                @if($s->status == 'pending')
                                    <span class="badge bg-warning text-dark">Menunggu Verifikasi</span>
                                @elseif($s->status == 'revisi')
                                    <span class="badge bg-info text-dark">Menunggu Verifikasi (Revisi)</span>
                                @elseif($s->status == 'rejected')
                                    <span class="badge bg-danger">Menunggu Revisi</span>
                                @elseif($s->status == 'proses validasi')
                                    <span class="badge bg-primary">Menunggu Validasi</span>
                                @elseif($s->status == 'disetujui')
                                    <span class="badge bg-success">Disetujui</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('verifikasi.show', $s->id) }}" class="btn btn-sm btn-info text-white">
                                    <i class="fas fa-search me-1"></i> Periksa Berkas
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-check-circle fa-3x mb-3 text-light"></i><br>
                                Tidak ada antrean verifikasi saat ini.
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