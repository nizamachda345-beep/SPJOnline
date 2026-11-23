{{-- View: verifikasi/index --}}
@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-3 glass-header-dark position-relative">

        <div class="glass-reflection"></div>

        <div class="card-body p-3">

            <div class="row align-items-center">

                <div class="col-md-7">

                    <div class="d-flex align-items-center">

                        <div class="bg-primary-soft text-primary rounded-3 d-flex align-items-center justify-content-center me-3"
                             style="width: 40px; height: 45px;">

                            <i class="fas {{ $isSp2dQueue ? 'fa-file-invoice-dollar' : 'fa-check-double' }} fs-4"></i>

                        </div>

                        <div>

                            <h4 class="fw-black text-light mb-2 tracking-tight">
                                {{ $isSp2dQueue ? 'Verifikasi SP2D' : 'Verifikasi Permintaan' }}
                            </h4>

                            <nav aria-label="breadcrumb">

                                <ol class="breadcrumb mb-0 extra-small">

                                    <li class="breadcrumb-item text-light">

                                        <i class="fas fa-layer-group me-1"></i>
                                        Panel Kasie BUD

                                    </li>

                                    <li class="breadcrumb-item active fw-medium text-primary">

                                        {{ $isSp2dQueue ? 'Verifikasi SP2D' : 'Verifikasi Permintaan' }}

                                    </li>

                                </ol>

                            </nav>

                        </div>

                    </div>

                </div>

                <div class="col-md-5 text-md-end mt-3 mt-md-0">

                    <div class="d-inline-flex align-items-center bg-light-50 px-3 py-2 rounded-pill shadow-sm"
                         style="backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);">

                        <div class="me-3 text-center">

                            <p class="text-muted-show mb-0 extra-small text-uppercase fw-bold tracking-wider">
                                Total Pengajuan
                            </p>

                            <p class="fw-black text-muted-show h6 mb-0">

                                {{ $submissions->total() }}

                                <span class="small fw-sm text-muted-show">
                                    Pengajuan
                                </span>

                            </p>

                        </div>

                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center"
                             style="width: 30px; height: 30px;">

                            <i class="fas fa-inbox text-white fs-6"></i>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="glass-card-dark border-0 shadow-sm rounded-4 mb-4">

        <div class="card-body p-4">

            <form action="{{ route($listRouteName) }}"
                  method="GET"
                  class="row align-items-end g-3 no-ajax">

                <input type="hidden"
                       name="sort"
                       value="{{ $sortBy ?? 'created_at' }}">

                <input type="hidden"
                       name="direction"
                       value="{{ $direction ?? 'desc' }}">

                <div class="col-md-3">

                    <label class="form-label small fw-bold text-light uppercase">
                        Nomor SPP/SPM
                    </label>

                    <div class="input-group search-group">

                        <span class="input-group-text border-0 bg-light">

                            <i class="fas fa-search text-muted"></i>

                        </span>

                        <input type="text"
                               name="nomor"
                               class="form-control border-0 bg-light rounded-end"
                               placeholder="Cari No. SPP atau SPM..."
                               value="{{ request('nomor') }}">

                    </div>

                </div>

                <div class="col-md-3">

                    <label class="form-label small fw-bold text-light uppercase">
                        Filter Tanggal
                    </label>

                    <input type="date"
                           name="tanggal"
                           class="form-control border-0 bg-light"
                           value="{{ request('tanggal') }}">

                </div>

                <div class="col-md-3">

                    <label class="form-label small fw-bold text-light uppercase">
                        Jenis Permintaan
                    </label>

                    <select name="jenis_pencairan"
                            class="form-select border-0 bg-light">

                        <option value="">
                            Semua Permintaan
                        </option>

                        <option value="LS"
                            {{ request('jenis_pencairan') == 'LS' ? 'selected' : '' }}>
                            LS
                        </option>

                        <option value="GU"
                            {{ request('jenis_pencairan') == 'GU' ? 'selected' : '' }}>
                            GU
                        </option>

                        <option value="TU"
                            {{ request('jenis_pencairan') == 'TU' ? 'selected' : '' }}>
                            TU
                        </option>

                        <option value="UP"
                            {{ request('jenis_pencairan') == 'UP' ? 'selected' : '' }}>
                            UP
                        </option>

                        <option value="GU Nihil"
                            {{ request('jenis_pencairan') == 'GU Nihil' ? 'selected' : '' }}>
                            GU Nihil
                        </option>

                        <option value="TU Nihil"
                            {{ request('jenis_pencairan') == 'TU Nihil' ? 'selected' : '' }}>
                            TU Nihil
                        </option>

                    </select>

                </div>

                <div class="col-md-3">

                    <div class="d-flex gap-2 flex-nowrap">

                        <button type="submit"
                                class="btn btn-dark px-3 rounded-pill flex-grow-1 shadow-sm d-flex align-items-center justify-content-center">

                            <i class="fas fa-sliders-h me-2"></i>

                            <span>
                                Filter
                            </span>

                        </button>

                        <a href="{{ route($listRouteName) }}"
                           class="btn btn-light px-3 rounded-pill border shadow-xs text-muted d-flex align-items-center justify-content-center reset-btn">

                            <i class="fas fa-sync-alt"></i>

                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>

    <div class="card border-0 shadow-sm overflow-hidden"
         style="border-radius: 10px;"
         id="monitoring-container">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table align-middle mb-0">

                    <thead class="table-dark text-dark small uppercase">

                        <tr class="align-middle text-nowrap">

                            <th class="ps-4 py-3 text-uppercase small fw-bold text-center"
                                style="width: 50px;">
                                No
                            </th>

                            <th class="py-3">

                                <a href="{{ route(
                                    $listRouteName,
                                    array_merge(
                                        request()->all(),
                                        [
                                            'sort' => 'departments.nama_dinas',
                                            'direction' =>
                                                $sortBy == 'departments.nama_dinas'
                                                && $direction == 'asc'
                                                    ? 'desc'
                                                    : 'asc'
                                        ]
                                    )
                                ) }}"
                                class="text-decoration-none text-light d-flex align-items-center gap-2">

                                    <span class="text-uppercase small fw-bold">
                                        SKPD
                                    </span>

                                </a>

                            </th>

                            <th class="py-3 text-center">

                                <a href="{{ route(
                                    $listRouteName,
                                    array_merge(
                                        request()->all(),
                                        [
                                            'sort' => 'nomor_spp',
                                            'direction' =>
                                                $sortBy == 'nomor_spp'
                                                && $direction == 'asc'
                                                    ? 'desc'
                                                    : 'asc'
                                        ]
                                    )
                                ) }}"
                                class="text-decoration-none text-light">

                                    <span class="text-uppercase small fw-bold">
                                        No. SPP - Tgl SPP
                                    </span>

                                </a>

                            </th>

                            <th class="py-3 text-center">

                                <a href="{{ route(
                                    $listRouteName,
                                    array_merge(
                                        request()->all(),
                                        [
                                            'sort' => 'nomor_spm',
                                            'direction' =>
                                                $sortBy == 'nomor_spm'
                                                && $direction == 'asc'
                                                    ? 'desc'
                                                    : 'asc'
                                        ]
                                    )
                                ) }}"
                                class="text-decoration-none text-light">

                                    <span class="text-uppercase small fw-bold">
                                        No. SPM - Tgl SPM
                                    </span>

                                </a>

                            </th>

                            <th class="py-3">

                                <a href="{{ route(
                                    $listRouteName,
                                    array_merge(
                                        request()->all(),
                                        [
                                            'sort' => 'judul_kegiatan',
                                            'direction' =>
                                                $sortBy == 'judul_kegiatan'
                                                && $direction == 'asc'
                                                    ? 'desc'
                                                    : 'asc'
                                        ]
                                    )
                                ) }}"
                                class="text-decoration-none text-light">

                                    <span class="text-uppercase small fw-bold">
                                        Kegiatan
                                    </span>

                                </a>

                            </th>

                            <th class="py-3">

                                <a href="{{ route(
                                    $listRouteName,
                                    array_merge(
                                        request()->all(),
                                        [
                                            'sort' => 'judul_sub_kegiatan',
                                            'direction' =>
                                                $sortBy == 'judul_sub_kegiatan'
                                                && $direction == 'asc'
                                                    ? 'desc'
                                                    : 'asc'
                                        ]
                                    )
                                ) }}"
                                class="text-decoration-none text-light">

                                    <span class="text-uppercase small fw-bold">
                                        Sub-Kegiatan
                                    </span>

                                </a>

                            </th>

                            <th class="py-3">

                                <a href="{{ route(
                                    $listRouteName,
                                    array_merge(
                                        request()->all(),
                                        [
                                            'sort' => 'perihal',
                                            'direction' =>
                                                $sortBy == 'perihal'
                                                && $direction == 'asc'
                                                    ? 'desc'
                                                    : 'asc'
                                        ]
                                    )
                                ) }}"
                                class="text-decoration-none text-light">

                                    <span class="text-uppercase small fw-bold">
                                        Perihal
                                    </span>

                                </a>

                            </th>

                            <th class="py-3 text-end">

                                <a href="{{ route(
                                    $listRouteName,
                                    array_merge(
                                        request()->all(),
                                        [
                                            'sort' => 'total_pencairan',
                                            'direction' =>
                                                $sortBy == 'total_pencairan'
                                                && $direction == 'asc'
                                                    ? 'desc'
                                                    : 'asc'
                                        ]
                                    )
                                ) }}"
                                class="text-decoration-none text-light d-flex align-items-center justify-content-end gap-2">

                                    <span class="text-uppercase small fw-bold">
                                        Jumlah Permintaan
                                    </span>

                                </a>

                            </th>

                            <th class="py-3 text-center">

                                <a href="{{ route(
                                    $listRouteName,
                                    array_merge(
                                        request()->all(),
                                        [
                                            'sort' => 'jenis_pencairan',
                                            'direction' =>
                                                $sortBy == 'jenis_pencairan'
                                                && $direction == 'asc'
                                                    ? 'desc'
                                                    : 'asc'
                                        ]
                                    )
                                ) }}"
                                class="text-decoration-none text-light">

                                    <span class="text-uppercase small fw-bold">
                                        Jenis Belanja
                                    </span>

                                </a>

                            </th>

                            <th class="py-3 text-center">

                                <a href="{{ route(
                                    $listRouteName,
                                    array_merge(
                                        request()->all(),
                                        [
                                            'sort' => 'sumber_dana',
                                            'direction' =>
                                                $sortBy == 'sumber_dana'
                                                && $direction == 'asc'
                                                    ? 'desc'
                                                    : 'asc'
                                        ]
                                    )
                                ) }}"
                                class="text-decoration-none text-light">

                                    <span class="text-uppercase small fw-bold">
                                        Sumber Dana
                                    </span>

                                </a>

                            </th>

                            <th class="py-3 text-center">

                                <a href="{{ route(
                                    $listRouteName,
                                    array_merge(
                                        request()->all(),
                                        [
                                            'sort' => 'status',
                                            'direction' =>
                                                $sortBy == 'status'
                                                && $direction == 'asc'
                                                    ? 'desc'
                                                    : 'asc'
                                        ]
                                    )
                                ) }}"
                                class="text-decoration-none text-light">

                                    <span class="text-uppercase small fw-bold">
                                        Status
                                    </span>

                                </a>

                            </th>

                            <th class="text-center pe-4 py-3"
                                style="width: 100px;">

                                <span class="text-uppercase small fw-bold">
                                    Aksi
                                </span>

                            </th>

                        </tr>

                    </thead>

                    <tbody style="background: transparent;">

                        @forelse($submissions as $index => $s)

                            @php

                                $isSp2dVerification =
                                    strtolower(
                                        trim(
                                            $s->status ?? ''
                                        )
                                    ) !== ''
                                    && in_array(
                                        strtolower(trim($s->status ?? '')),
                                        ['disetujui', 'approved'],
                                        true
                                    )
                                    &&
                                    in_array(
                                        strtolower(
                                            trim(
                                                $s->status_sp2d ?? ''
                                            )
                                        ),
                                        [
                                            'uploaded',
                                            'direvisi'
                                        ],
                                        true
                                    );

                                $statusSp2d =
                                    strtolower(
                                        trim(
                                            $s->status_sp2d ?? ''
                                        )
                                    );

                                if (
                                    $isSp2dVerification
                                    &&
                                    $statusSp2d === 'uploaded'
                                ) {

                                    $queueLabel =
                                        'Menunggu Verifikasi SP2D';

                                    $queueBadge =
                                        'bg-warning text-dark';

                                    $queueIcon =
                                        'fa-file-invoice-dollar';

                                } elseif (
                                    $isSp2dVerification
                                    &&
                                    $statusSp2d === 'direvisi'
                                ) {

                                    $queueLabel =
                                        'Verifikasi Ulang SP2D';

                                    $queueBadge =
                                        'bg-info text-dark';

                                    $queueIcon =
                                        'fa-redo';

                                } else {

                                    $queueLabel =
                                        'Verifikasi Permintaan';

                                    $queueBadge =
                                        'bg-primary';

                                    $queueIcon =
                                        'fa-check-double';
                                }

                                $jenis =
                                    strtoupper(
                                        $s->jenis_pencairan ?? '-'
                                    );

                                $badgeClass =
                                    match ($jenis) {

                                        'LS' =>
                                            'bg-primary',

                                        'GU' =>
                                            'bg-success',

                                        'TU' =>
                                            'bg-warning text-dark',

                                        'UP' =>
                                            'bg-info text-dark',

                                        'GU NIHIL' =>
                                            'bg-secondary',

                                        'TU NIHIL' =>
                                            'bg-dark',

                                        default =>
                                            'bg-light text-dark'
                                    };

                            @endphp

                            <tr class="align-middle text-nowrap">

                                <td class="ps-4 text-center text-muted small">

                                    {{ $submissions->firstItem() + $index }}

                                </td>

                                <td>

                                    <div class="fw-bold text-dark"
                                         data-bs-toggle="tooltip"
                                         title="{{ $s->department->nama_dinas ?? '-' }}">

                                        {{ Str::limit(
                                            $s->department->nama_dinas ?? '-',
                                            20,
                                            '...'
                                        ) }}

                                    </div>

                                    <small class="text-muted">

                                        {{ $s->department->kode_dinas ?? '-' }}

                                    </small>

                                </td>

                                <td class="text-center">

                                    <div class="fw-semibold text-dark small">

                                        {{ $s->nomor_spp ?? '-' }}

                                    </div>

                                    <div class="text-muted"
                                         style="font-size: 0.7rem;">

                                        {{ $s->tanggal_spp
                                            ? \Carbon\Carbon::parse(
                                                $s->tanggal_spp
                                            )->format('d/m/Y')
                                            : '-' }}

                                    </div>

                                </td>

                                <td class="text-center">

                                    <div class="fw-semibold text-dark small">

                                        {{ $s->nomor_spm ?? '-' }}

                                    </div>

                                    <div class="text-muted"
                                         style="font-size: 0.7rem;">

                                        {{ $s->tanggal_spm
                                            ? \Carbon\Carbon::parse(
                                                $s->tanggal_spm
                                            )->format('d/m/Y')
                                            : '-' }}

                                    </div>

                                </td>

                                <td>

                                    <span class="small text-dark"
                                          title="{{ $s->judul_kegiatan }}">

                                        {{ Str::limit(
                                            $s->judul_kegiatan ?? '-',
                                            20,
                                            '...'
                                        ) }}

                                    </span>

                                </td>

                                <td>

                                    <span class="small text-muted"
                                          title="{{ $s->judul_sub_kegiatan }}">

                                        {{ Str::limit(
                                            $s->judul_sub_kegiatan ?? '-',
                                            20,
                                            '...'
                                        ) }}

                                    </span>

                                </td>

                                <td>

                                    <span class="small text-dark"
                                          title="{{ $s->perihal }}">

                                        {{ Str::limit(
                                            $s->perihal ?? '-',
                                            20,
                                            '...'
                                        ) }}

                                    </span>

                                </td>

                                <td class="text-end fw-bold text-dark">

                                    <span class="text-muted small fw-normal">
                                        Rp
                                    </span>

                                    {{ number_format(
                                        $s->total_pencairan,
                                        0,
                                        ',',
                                        '.'
                                    ) }}

                                </td>

                                <td class="text-center">

                                    <div class="d-flex flex-column align-items-center gap-1">

                                        <span class="badge {{ $badgeClass }} px-2 py-1"
                                              style="font-size: 0.68rem;">

                                            {{ $jenis }}

                                        </span>

                                    </div>

                                </td>

                                <td class="text-center">

                                    <span class="small fw-semibold text-secondary">

                                        {{ $s->sumber_dana ?? '-' }}

                                    </span>

                                </td>

                                <td class="text-center">

                                    <div class="d-flex flex-column align-items-center gap-1">

                                        <span class="badge {{ $queueBadge }} px-2 py-1"
                                              style="font-size: 0.60rem;">

                                            <i class="fas {{ $queueIcon }} me-1"></i>

                                            {{ $queueLabel }}

                                        </span>

                                    </div>

                                </td>

                                <td class="pe-4 text-center">

                                    <a href="{{ route('verifikasi.show', $s->id) }}{{ $isSp2dQueue ? '?source=sp2d' : '' }}"
                                    class="btn btn-white btn-sm rounded-circle shadow-sm hover-scale d-inline-flex align-items-center justify-content-center"
                                    style="width: 32px;
                                        height: 32px;
                                        background: rgba(255, 255, 255, 0.6);
                                        transition: all 0.2s ease;"
                                    title="{{ $isSp2dVerification
                                        ? 'Verifikasi SP2D'
                                        : 'Lihat Detail & Verifikasi'
                                    }}">

                                        <i class="fas
                                            {{ $isSp2dVerification
                                                ? 'fa-file-signature'
                                                : 'fa-search'
                                            }}
                                            text-primary"
                                           style="font-size: 0.8rem;">
                                        </i>

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="12"
                                    class="text-center py-5 border-0">

                                    <div class="opacity-50">

                                        <i class="fas fa-folder-open fa-3x mb-3 text-muted"></i>

                                        <h6 class="fw-light">
                                            Tidak ada pengajuan yang perlu diverifikasi
                                        </h6>

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    @if($submissions->hasPages())

        <div class="my-3 d-flex justify-content-center">

            <div class="glass-pagination px-4 py-1 rounded-pill shadow-sm">

                {{ $submissions->links('pagination::bootstrap-5') }}

            </div>

        </div>

    @endif

</div>
@endsection
