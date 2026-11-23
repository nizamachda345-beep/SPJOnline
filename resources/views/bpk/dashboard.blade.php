{{-- View: bpk/dashboard --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="row mb-3">
        <div class="col-12">
            <div class="card glass-card-dark border-0 shadow-sm">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8 d-flex align-items-center">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/d/d2/BPK_insignia.svg"
                                 alt="Logo BPK" class="me-3" style="width: 40px; height: 40px; object-fit: contain;">
                            <div>
                                <h4 class="fw-bold text-white mb-1">Dashboard Audit - BPK</h4>
                                <p class="text-white-50 small mb-0">Pemantauan real-time jejak digital berkas SPP/SPM Pemkab Bolmong.</p>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <a href="{{ route('bpk.monitoring') }}" class="btn btn-outline-light btn-pill shadow-sm px-4">
                                <i class="fas fa-search me-2"></i> Menu Audit
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
        @php
            $cards = [
                ['title' => 'Total Berkas', 'value' => $stats['total_berkas'], 'icon' => 'fa-layer-group', 'color' => 'text-info'],
                ['title' => 'Dalam Proses', 'value' => $stats['pending'] + $stats['proses_validasi'] + $stats['direvisi'] + $stats['verified'], 'icon' => 'fa-spinner', 'color' => 'text-warning'],
                ['title' => 'Disetujui', 'value' => $stats['disetujui'], 'icon' => 'fa-check-circle', 'color' => 'text-success'],
                ['title' => 'Pengajuan Ditolak', 'value' => $stats['revisi'] + $stats['reset'] + $stats['rejected'], 'icon' => 'fa-exclamation-triangle', 'color' => 'text-danger'],
                ['title' => 'Belum Upload SP2D', 'value' => $stats['sp2d_belum_upload'], 'icon' => 'fa-file-circle-exclamation', 'color' => 'text-warning', 'modal' => true],
            ];
        @endphp

        @foreach($cards as $card)
        <div class="col-6 col-md-4 col-xl">
            @if($card['modal'] ?? false)
            <button type="button" class="card glass-card-dark border-0 h-100 w-100 text-start" data-bs-toggle="modal" data-bs-target="#bpkSp2dBelumUploadModal" style="font: inherit; color: inherit; cursor: pointer;">
            @else
            <div class="card glass-card-dark border-0 h-100">
            @endif
                <div class="card-body py-4 px-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-white-50 small">{{ $card['title'] }}</span>
                            <h2 class="fw-bold mb-0 text-white">{{ $card['value'] }}</h2>
                        </div>
                        <span class="{{ $card['color'] }} fs-3"><i class="fas {{ $card['icon'] }}"></i></span>
                    </div>
                </div>
            @if($card['modal'] ?? false)
            </button>
            @else
            </div>
            @endif
        </div>
        @endforeach
    </div>

    <div class="modal fade" id="bpkSp2dBelumUploadModal" tabindex="-1" aria-labelledby="bpkSp2dBelumUploadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 rounded-4 shadow">
                <div class="modal-header border-0 pb-2">
                    <div>
                        <h5 class="modal-title fw-bold text-dark" id="bpkSp2dBelumUploadModalLabel">
                            <i class="fas fa-file-circle-exclamation text-warning me-2"></i>Pengajuan Belum Upload SP2D
                        </h5>
                        <p class="small text-muted mb-0">{{ number_format($sp2dBelumUpload->total()) }} pengajuan yang sudah disetujui dan belum memiliki dokumen SP2D.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body pt-2">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nomor SPP / SPM</th>
                                    <th>Kegiatan</th>
                                    <th>SKPD</th>
                                    <th>Tanggal Pembaruan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sp2dBelumUpload as $submission)
                                    <tr>
                                        <td class="text-nowrap">{{ $submission->nomor_spp ?? '-' }} / {{ $submission->nomor_spm ?? '-' }}</td>
                                        <td>{{ $submission->judul_kegiatan ?? '-' }}</td>
                                        <td>{{ $submission->department->nama_dinas ?? '-' }}</td>
                                        <td class="text-nowrap">{{ $submission->updated_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-5">Tidak ada pengajuan yang menunggu unggahan SP2D.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($sp2dBelumUpload->hasPages())
                        <div class="d-flex justify-content-center mt-3">
                            {{ $sp2dBelumUpload->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card glass-card-dark border-0 shadow-sm">
        <div class="card-header py-3 border-bottom border-secondary border-opacity-25">
            <h6 class="mb-0 fw-bold text-white">10 Berkas Terbaru Masuk</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle mb-4 text-white">
                    <thead class="glass-header-dark">
                        <tr>
                            <th class="ps-4">Dinas</th>
                            <th class="text-center">Tanggal</th>
                            <th>Nomor Berkas</th>
                            <th class="text-center">Kategori</th>
                            <th>Nominal</th>
                            <th >Status</th>

                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSubmissions as $s)
                        <tr>
                            <td class="ps-4 fw-bold">{{ $s->department->nama_dinas }}</td>
                            <td class="text-center">{{ $s->created_at->format('d M Y') }}</td>
                            <td><code class="text-info">{{ $s->nomor_spp }} / {{ $s->nomor_spm }}</code></td>
                            <td class="text-center">{{ $s->jenis_pencairan }}</td>
                            <td>Rp {{ number_format($s->total_pencairan, 0, ',', '.') }}</td>
                            <td class="text-white fw-bold">
                                @php
                                    $status = strtolower($s->status);
                                    $sp2dLengkap = $s->documents->contains(function ($document) {
                                        return str_contains(strtolower($document->jenis_dokumen ?? ''), 'sp2d')
                                            && in_array($document->status_dokumen, ['verified', 'approved'], true);
                                    });
                                    $statusStyle = $sp2dLengkap
                                        ? ['color' => 'text-success', 'icon' => 'fa-file-circle-check', 'label' => 'Lengkap SP2D']
                                        : ['color' => 'text-success', 'icon' => 'fa-check-circle', 'label' => 'Disetujui'];
                                @endphp

                                <i class="fas {{ $statusStyle['icon'] }} {{ $statusStyle['color'] }} me-2"></i>
                                {{ $statusStyle['label'] }}
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-white-50">
                                <i class="fas fa-box-open fa-2x mb-2"></i>
                                <p class="mb-0">Belum ada berkas audit yang tersedia.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalElement = document.getElementById('bpkSp2dBelumUploadModal');
    if (!modalElement) return;

    modalElement.querySelectorAll('.pagination a').forEach(function (link) {
        link.href += '#bpkSp2dBelumUploadModal';
    });

    if (new URLSearchParams(window.location.search).has('sp2d_page')) {
        bootstrap.Modal.getOrCreateInstance(modalElement).show();
    }
});
</script>
@endsection
