@extends('layouts.app')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold text-primary">
            <i class="fas fa-file-signature me-2"></i> Validasi Akhir Kabid Perbendaharaan
        </h5>
        <span class="badge bg-primary">{{ $submissions->count() }} Pengajuan Menunggu</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Dinas / SKPD</th>
                        <th>Nomor SPP/SPM</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Jumlah Berkas</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($submissions as $s)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $s->department->nama_dinas ?? 'Dinas Tidak Diketahui' }}</div>
                            <small class="text-muted">Oleh: {{ $s->user->name }}</small>
                        </td>
                        <td class="fw-bold text-primary">{{ $s->nomor_spp_spm }}</td>
                        <td>{{ $s->created_at->format('d M Y') }}<br><small class="text-muted">{{ $s->created_at->format('H:i') }} WITA</small></td>
                        <td>
                            <span class="badge rounded-pill bg-light text-dark border">
                                {{ $s->documents->count() }} Dokumen
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center">
                                <a href="{{ route('validasi.show', $s->id) }}" class="btn btn-outline-info btn-sm me-2">
                                    <i class="fas fa-eye me-1"></i> Detail
                                </a>

                                <form action="{{ route('validasi.sahkan', $s->id) }}" method="POST" data-confirm-delete="Sahkan seluruh berkas dari <strong>{{ $s->department->nama_dinas }}</strong> secara final?">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm fw-bold">
                                        <i class="fas fa-check-double me-1"></i> Sahkan
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="fas fa-folder-open fa-3x mb-3 d-block opacity-25"></i>
                            Belum ada berkas yang menunggu validasi akhir.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection