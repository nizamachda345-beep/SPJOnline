@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark">Riwayat Pengajuan iDJ</h3>
            <p class="text-muted">Kelola berkas SPP/SPM Anda dengan memantau status verifikasi secara real-time.</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('submissions.create') }}" class="btn btn-primary shadow-sm px-4">
                <i class="fas fa-plus me-2"></i>Buat Pengajuan Baru
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 12px;">
        <div class="card-body p-0"> {{-- p-0 agar table-responsive pas ke pinggir card --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small uppercase">
                        <tr>
                            <th class="ps-4">No</th>
                            <th>Tanggal</th>
                            <th>No. SPP/SPM</th>
                            @if(auth()->user()->hasRole('super_admin'))
                                <th>Instansi/Dinas</th>
                            @endif
                            <th>Kegiatan</th>
                            <th>Total Pencairan</th>
                            <th class="text-center">Status</th>
                            <th class="text-center pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $index => $s)
                        <tr>
                            <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($s->tanggal)->format('d/m/Y') }}</td>
                            <td><span class="badge bg-light text-dark border fw-normal">{{ $s->nomor_spp_spm }}</span></td>
                            
                            @if(auth()->user()->hasRole('super_admin'))
                                <td class="fw-bold">{{ $s->department->nama_dinas }}</td>
                            @endif

                            <td>{{ Str::limit($s->judul_kegiatan, 35) }}</td>
                            <td class="fw-bold text-dark">Rp {{ number_format($s->total_pencairan, 0, ',', '.') }}</td>
                            
                            <td class="text-center">
                                @if($s->status == 'pending')
                                    <span class="badge bg-secondary">Menunggu Verifikasi</span>
                                @elseif($s->status == 'rejected')
                                    <span class="badge bg-danger">Menunggu Revisi</span>
                                @elseif($s->status == 'revisi')
                                    <span class="badge bg-info text-dark">Berkas Direvisi</span>
                                @elseif($s->status == 'proses validasi')
                                    <span class="badge bg-primary">Proses Validasi</span>
                                @elseif($s->status == 'disetujui')
                                    <span class="badge bg-success">Disahkan</span>
                                @endif
                            </td>
                            
                           <td class="text-center pe-4">
                                <div class="d-flex justify-content-center align-items-center">
                                    {{-- Tombol Detail --}}
                                    <a href="{{ route('submissions.show', $s->id) }}" 
                                    class="btn btn-sm btn-outline-secondary me-2" {{-- TAMBAHKAN me-2 DI SINI --}}
                                    title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    {{-- TOMBOL REVISI: Muncul hanya jika statusnya 'rejected' --}}
                                    @if($s->status == 'rejected')
                                        <a href="{{ route('submissions.edit', $s->id) }}" 
                                        class="btn btn-sm btn-warning shadow-sm" 
                                        title="Perbaiki Berkas">
                                            <i class="fas fa-edit me-1"></i> Revisi
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 d-block opacity-25"></i>
                                Belum ada data pengajuan yang ditemukan.
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