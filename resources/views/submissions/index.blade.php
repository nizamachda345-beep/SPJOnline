@extends('layouts.app')

@section('content')
<div class="container-fluid">
    
    <div class="d-flex justify-content-between align-items-center bg-light shadow-sm bg-opacity-25 rounded p-4 mb-4 border border-warning-subtle">
        <div class="d-flex align-items-center">
            <div class="me-3 text-warning">
                <i class="fas fa-history fa-3x"></i>
            </div>
            <div>
                <h3 class="fw-bold text-dark mb-1">History Pengajuan iDJ</h3>
                <p class="text-muted mb-0">Kelola berkas SPP/SPM Anda dengan memantau status verifikasi secara real-time.</p>
            </div>
        </div>

        <div>
            <a href="{{ route('submissions.create') }}" class="btn btn-outline-warning shadow-lg px-4 text-dark">
                <i class="fas fa-plus me-2"></i>Buat Pengajuan Baru
            </a>
        </div>
    </div>

    {{-- Advanced Filter Bar --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-4">
            <form action="{{ route('submissions.index') }}" method="GET" class="row align-items-end g-3">
                <input type="hidden" name="sort" value="{{ $sortBy }}">
                <input type="hidden" name="direction" value="{{ $direction }}">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted uppercase">Nomor SPP</label>
                    <div class="input-group search-group">
                        <span class="input-group-text border-0 bg-light"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="nomor_spp_spm" class="form-control border-0 bg-light rounded-end" 
                               placeholder="Cari Nomor SPP..." value="{{ request('nomor_spp_spm') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted uppercase">Filter Tanggal</label>
                    <input type="date" name="tanggal" class="form-control border-0 bg-light" value="{{ request('tanggal') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted uppercase">Kategori Pencairan</label>
                    <select name="jenis_pencairan" class="form-select border-0 bg-light">
                        <option value="">Semua Kategori</option>
                        <option value="LS" {{ request('jenis_pencairan') == 'LS' ? 'selected' : '' }}>LS</option>
                        <option value="GU" {{ request('jenis_pencairan') == 'GU' ? 'selected' : '' }}>GU</option>
                        <option value="TU" {{ request('jenis_pencairan') == 'TU' ? 'selected' : '' }}>TU</option>
                        <option value="LS Nihil" {{ request('jenis_pencairan') == 'LS Nihil' ? 'selected' : '' }}>LS Nihil</option>
                        <option value="GU Nihil" {{ request('jenis_pencairan') == 'GU Nihil' ? 'selected' : '' }}>GU Nihil</option>
                        <option value="TU Nihil" {{ request('jenis_pencairan') == 'TU Nihil' ? 'selected' : '' }}>TU Nihil</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-dark px-4 rounded-pill flex-grow-1 shadow-sm d-flex align-items-center justify-content-center">
                        <i class="fas fa-sliders-h me-2"></i> 
                        <span>Filter</span>
                    </button>

                    <a href="{{ route('submissions.index') }}" class="btn btn-light px-4 rounded-pill border shadow-xs text-muted d-flex align-items-center justify-content-center">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 10px;">
        <div class="card-body p-0"> 
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark text-dark small uppercase">
                        <tr>
                            <th class="ps-4 py-3">No</th>
                            <th class="py-3">
                                <a href="{{ route('submissions.index', array_merge(request()->all(), ['sort' => 'tanggal', 'direction' => $sortBy == 'tanggal' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-light">
                                    Tanggal
                                    @if($sortBy == 'tanggal')
                                        <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="py-3">
                                <a href="{{ route('submissions.index', array_merge(request()->all(), ['sort' => 'nomor_spp_spm', 'direction' => $sortBy == 'nomor_spp_spm' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-light">
                                    No. SPP
                                    @if($sortBy == 'nomor_spp_spm')
                                        <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="py-3">
                                <a href="{{ route('submissions.index', array_merge(request()->all(), ['sort' => 'jenis_pencairan', 'direction' => $sortBy == 'jenis_pencairan' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-light">
                                    Ket
                                    @if($sortBy == 'jenis_pencairan')
                                        <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            @if(auth()->user()->hasRole('super_admin'))
                                <th class="py-3">
                                    <a href="{{ route('submissions.index', array_merge(request()->all(), ['sort' => 'departments.nama_dinas', 'direction' => $sortBy == 'departments.nama_dinas' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-light">
                                        Instansi/Dinas
                                        @if($sortBy == 'departments.nama_dinas')
                                            <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </a>
                                </th>
                            @endif
                            <th class="py-3">
                                <a href="{{ route('submissions.index', array_merge(request()->all(), ['sort' => 'judul_kegiatan', 'direction' => $sortBy == 'judul_kegiatan' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-light">
                                    Kegiatan
                                    @if($sortBy == 'judul_kegiatan')
                                        <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="py-3">
                                <a href="{{ route('submissions.index', array_merge(request()->all(), ['sort' => 'judul_sub_kegiatan', 'direction' => $sortBy == 'judul_sub_kegiatan' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-light">
                                    Sub-Kegiatan
                                    @if($sortBy == 'judul_sub_kegiatan')
                                        <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="py-3">
                                <a href="{{ route('submissions.index', array_merge(request()->all(), ['sort' => 'perihal', 'direction' => $sortBy == 'perihal' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-light">
                                    Perihal
                                    @if($sortBy == 'perihal')
                                        <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="py-3">
                                <a href="{{ route('submissions.index', array_merge(request()->all(), ['sort' => 'total_pencairan', 'direction' => $sortBy == 'total_pencairan' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-light">
                                    Total Pencairan
                                    @if($sortBy == 'total_pencairan')
                                        <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-center py-3">
                                <a href="{{ route('submissions.index', array_merge(request()->all(), ['sort' => 'status', 'direction' => $sortBy == 'status' && $direction == 'asc' ? 'desc' : 'asc'])) }}" class="text-decoration-none text-light">
                                    Status
                                    @if($sortBy == 'status')
                                        <i class="fas fa-sort-{{ $direction == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                            <th class="text-center pe-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($submissions as $index => $s)
                        <tr>
                            <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                            <td>{{ \Carbon\Carbon::parse($s->tanggal)->format('d/m/Y') }}</td>
                            <td><span class="badge bg-light text-dark border fw-bold">{{ $s->nomor_spp_spm }}</span></td>
                            <td class="fw-bold">{{ $s->jenis_pencairan }}</td>
                            @if(auth()->user()->hasRole('super_admin'))
                                <td class="fw-bold">{{ $s->department->nama_dinas }}</td>
                            @endif

                            <td data-bs-toggle="tooltip" title="{{ $s->judul_kegiatan }}">
                                <div class="text-truncate" style="max-width: 150px;">
                                    {{ Str::limit($s->judul_kegiatan, 15) }}
                                </div>
                            </td>

                            <td data-bs-toggle="tooltip" title="{{ $s->judul_sub_kegiatan }}">
                                <div class="text-truncate" style="max-width: 150px;">
                                    {{ Str::limit($s->judul_sub_kegiatan, 15) }}
                                </div>
                            </td>

                            <td data-bs-toggle="tooltip" title="{{ $s->perihal }}">
                                <div class="text-truncate" style="max-width: 150px;">
                                    {{ Str::limit($s->perihal, 15) }}
                                </div>
                            </td>
                            <td class="fw-bold text-dark">Rp {{ number_format($s->total_pencairan, 0, ',', '.') }}</td>
                            
                            <!-- <td class="text-center">
                                @if($s->status == 'pending')
                                    <span class="badge bg-warning"><i class="fas fa-spinner fa-spin me-1"></i>Menunggu Verifikasi</span>
                                @elseif($s->status == 'rejected')
                                    <span class="badge bg-danger"><i class="fas fa-times me-1"></i>Menunggu Revisi</span>
                                @elseif($s->status == 'revisi')
                                    <span class="badge bg-info text-dark"><i class="fas fa-edit me-1"></i>Berkas Direvisi</span>
                                @elseif($s->status == 'proses validasi')
                                    <span class="badge bg-primary"><i class="fas fa-sync-alt fa-spin me-1"></i>Proses Validasi</span>
                                @elseif($s->status == 'disetujui')
                                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Disahkan</span>
                                @endif
                            </td> -->

                            <td class="text-center">
    @if($s->status == 'pending')
        @php $approvedCount = $s->documents->where('status_dokumen', 'approved')->count(); @endphp
        
        @if($approvedCount == 6 && is_null($s->sent_to_kabid_at))
            <span class="text-info">
                <i class="fas fa-check-double me-1"></i> Validasi
            </span>
        @else
            <span class="text-warning"><i class="fas fa-spinner fa-spin me-1"></i>Verifikasi</span>
        @endif

    {{-- TAMBAHKAN BAGIAN INI --}}
    @elseif($s->status == 'rejected')
        <span class="text-danger"><i class="fas fa-times me-1"></i>Revisi</span>
    
    @elseif($s->status == 'direvisi')
        <span class="text-danger"><i class="fas fa-sync-alt fa-spin me-1"></i>Verifikasi</span>
        @elseif($s->status == 'revisi')
        <span class="text-danger"><i class="fas fa-sync-alt fa-spin me-1"></i>Revisi</span>
    
    
    @elseif($s->status == 'proses validasi')
        <span class="text-primary"><i class="fas fa-sync-alt fa-spin me-1"></i>Validasi</span>
    
    @elseif($s->status == 'disetujui')
        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Disahkan</span>
    @endif
</td>
                            
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center align-items-center">
                                    <a href="{{ route('submissions.show', $s->id) }}" 
                                       class="btn btn-sm btn-outline-primary me-2" 
                                       title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    @if($s->status == 'revisi')
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-warning" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalRevisi{{ $s->id }}"
                                                title="Revisi">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- MODAL REVISI - Harus di dalam loop forelse tapi di luar tag <tr> --}}
                        @if($s->status == 'revisi')
                        <div class="modal fade" id="modalRevisi{{ $s->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header bg-white py-3 border-bottom">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                                <i class="fas fa-tools"></i>
                                            </div>
                                            <div>
                                                <h5 class="modal-title fw-bold text-dark mb-0">Daftar Perbaikan Dokumen</h5>
                                                <p class="text-muted small mb-0">Nomor SPP: {{ $s->nomor_spp_spm }}</p>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <form action="{{ route('submissions.update', $s->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        
                                        <div class="modal-body p-4">
                                            <div class="mb-3">
                                                <h6 class="fw-bold text-secondary px-2"><i class="fas fa-list-ul me-2"></i>Berkas yang Harus Diupload Ulang:</h6>
                                            </div>

                                            <div class="list-group list-group-flush border rounded">
                                                @foreach($s->documents->where('status_dokumen', 'revisi') as $doc)
                                                    <div class="list-group-item p-3">
                                                        <div class="row align-items-start">
                                                            {{-- Kolom Info Dokumen & Catatan --}}
                                                            <div class="col-md-5 mb-3 mb-md-0">
                                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                                    <div class="d-flex align-items-center">
                                                                        <span class="badge bg-danger-soft text-danger me-2 p-2 rounded">
                                                                            <i class="fas fa-file-pdf"></i>
                                                                        </span>
                                                                        <h6 class="fw-bold text-dark mb-0 text-uppercase small">{{ $doc->jenis_dokumen }}</h6>
                                                                    </div>
                                                                    {{-- TOMBOL LIHAT BERKAS LAMA --}}
                                                                    {{-- Ganti bagian tombol Lihat Berkas Lama dengan ini --}}
                                                                    <button type="button" 
                                                                            class="btn btn-xs btn-outline-danger py-0 px-2 rounded-pill shadow-sm"
                                                                            onclick="previewPDF('{{ asset('storage/' . $doc->file_path) }}')"
                                                                            style="font-size: 0.65rem;">
                                                                        <i class="fas fa-eye me-1"></i> Lihat Dokumen
                                                                    </button>
                                                                </div>
                                                                
                                                                <div class="bg-light p-2 rounded border-start border-danger border-3">
                                                                    <small class="text-danger fw-bold d-block mb-1" style="font-size: 0.7rem;">Catatan Penolakan:</small>
                                                                    <span class="text-dark small italic">"{{ $doc->catatan }}"</span>
                                                                </div>
                                                            </div>

                                                            {{-- Kolom Input Upload --}}
                                                            <div class="col-md-7">
                                                                <label class="form-label small fw-bold text-muted">Pilih file pengganti:</label>
                                                                <input type="file" name="files[{{ $doc->id }}]" class="form-control form-control-sm" required accept=".pdf">
                                                                <div class="d-flex justify-content-between mt-1">
                                                                    <span class="text-muted extra-small">Max: 10MB (PDF)</span>
                                                                    <span class="text-danger fw-bold extra-small">* Wajib</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                            </div>
                                        </div>

                                        <div class="modal-footer bg-light border-0">
                                            <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary px-4 fw-bold shadow-sm">
                                                <i class="fas fa-cloud-upload-alt me-2"></i> Update Semua Berkas
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif

                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
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
<div class="modal fade" id="modalPreviewPDF" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title small fw-bold"><i class="fas fa-file-pdf me-2"></i>Preview Dokumen Lama</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <iframe id="pdfFrame" src="" width="100%" height="600px" frameborder="0"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
    function previewPDF(url) {
        // Set URL ke iframe
        document.getElementById('pdfFrame').src = url;
        
        // Tampilkan modal preview
        var myModal = new bootstrap.Modal(document.getElementById('modalPreviewPDF'));
        myModal.show();
    }

    // Opsional: Bersihkan src iframe saat modal ditutup agar tidak berat
    document.getElementById('modalPreviewPDF').addEventListener('hidden.bs.modal', function () {
        document.getElementById('pdfFrame').src = '';
    });
</script>
@endsection