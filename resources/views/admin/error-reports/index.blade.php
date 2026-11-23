@extends('layouts.app')

@section('content')
<style>
    .error-reports-page { color: #e2e8f0; }
    .error-reports-header,
    .error-reports-table {
        overflow: hidden;
        border: 1px solid rgba(148, 163, 184, .16);
        border-radius: 1rem;
        background: rgba(15, 23, 42, .9);
        box-shadow: 0 12px 32px rgba(2, 6, 23, .18);
    }
    .error-reports-header { margin-bottom: 1rem; }
    .error-reports-header-body { padding: 1.15rem 1.35rem; }
    .error-reports-icon {
        display: grid;
        width: 3rem;
        height: 3rem;
        flex: 0 0 3rem;
        place-items: center;
        border: 1px solid rgba(248, 113, 113, .22);
        border-radius: .85rem;
        color: #fda4af;
        background: rgba(190, 24, 93, .16);
        font-size: 1.1rem;
    }
    .error-reports-subtitle { color: #aab7c9; font-size: .84rem; }
    .error-reports-count {
        padding: .55rem .9rem;
        border: 1px solid rgba(96, 165, 250, .25);
        border-radius: 999px;
        color: #bfdbfe;
        background: rgba(37, 99, 235, .12);
        font-size: .78rem;
        font-weight: 600;
        white-space: nowrap;
    }
    .error-reports-table .table { min-width: 820px; --bs-table-bg: transparent; }
    .error-reports-table thead th {
        padding: .9rem 1rem;
        border-bottom: 1px solid rgba(148, 163, 184, .16);
        color: #b8c4d5;
        background: rgba(15, 23, 42, .7);
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: .045em;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .error-reports-table tbody td {
        padding: .9rem 1rem;
        border-color: rgba(148, 163, 184, .1);
        color: #e2e8f0;
        font-size: .84rem;
        vertical-align: middle;
    }
    .error-reports-table tbody tr:last-child td { border-bottom: 0; }
    .error-reports-table .table tbody tr:hover,
    .error-reports-table .table tbody tr:hover > * {
        color: #f8fafc !important;
        background-color: rgba(51, 65, 85, .72) !important;
    }
    .error-reports-table .table tbody tr:hover .text-white-50 { color: #cbd5e1 !important; }
    .error-report-message { max-width: 400px; color: #cbd5e1; }
    .error-report-empty { padding: 3.25rem 1rem !important; text-align: center; }
    .error-report-empty-icon {
        display: grid;
        width: 3.4rem;
        height: 3.4rem;
        place-items: center;
        margin: 0 auto .9rem;
        border: 1px solid rgba(148, 163, 184, .16);
        border-radius: 1rem;
        color: #94a3b8;
        background: rgba(148, 163, 184, .08);
        font-size: 1.15rem;
    }
    .error-report-empty-title { color: #e2e8f0; font-weight: 600; }
    .error-report-empty-copy { max-width: 390px; margin: .35rem auto 0; color: #94a3b8; font-size: .82rem; }
    .error-reports-table .table tbody tr:hover .error-report-empty-title { color: #f8fafc !important; }
    .error-reports-table .table tbody tr:hover .error-report-empty-copy { color: #cbd5e1 !important; }
    .error-report-status { display: inline-flex; align-items: center; padding: .35rem .65rem; border: 1px solid; border-radius: 999px; font-size: .75rem; font-weight: 600; white-space: nowrap; }
    .error-report-status.is-open { color: #fcd34d; border-color: rgba(245, 158, 11, .28); background: rgba(245, 158, 11, .08); }
    .error-report-status.is-resolved { color: #86efac; border-color: rgba(34, 197, 94, .25); background: rgba(34, 197, 94, .08); }
    @media (max-width: 575.98px) {
        .error-reports-header-body { padding: 1rem; }
        .error-reports-count { margin-left: 3.75rem; }
    }
</style>

<div class="container-fluid px-4 py-2 error-reports-page">
    <section class="error-reports-header">
        <div class="error-reports-header-body d-flex align-items-center justify-content-between gap-3 flex-wrap">
            <div class="d-flex align-items-center gap-3">
                <span class="error-reports-icon" aria-hidden="true"><i class="fas fa-bug"></i></span>
                <div>
                    <h1 class="h5 text-white mb-1">Laporan Log Error</h1>
                    <p class="error-reports-subtitle mb-0">Tinjau kendala yang direkam otomatis atau dilaporkan operator.</p>
                </div>
            </div>
            <span class="error-reports-count">{{ $reports->total() }} laporan</span>
        </div>
    </section>

    <section class="error-reports-table">
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>Instansi</th>
                        <th>Pesan Error</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reports as $report)
                        @php
                            $reportSource = match ($report->error_type) {
                                'OperatorReport' => 'Laporan operator',
                                'JavaScriptError', 'UnhandledPromiseRejection', 'NetworkError' => 'Browser',
                                default => 'Server',
                            };
                        @endphp
                        <tr>
                            <td class="text-nowrap">{{ $report->occurred_at?->timezone($displayTimezone)->format('d M Y, H:i') ?? '—' }}</td>
                            <td>
                                <div class="fw-semibold">{{ $report->role_name ?: '—' }}</div>
                            </td>
                            <td>{{ $report->department_name ?: '—' }}</td>
                            <td>
                                <div class="error-report-message text-truncate" title="{{ $report->error_message }}">{{ $report->error_message }}</div>
                                <div class="small text-white-50 mt-1">{{ $reportSource }}{{ $report->error_type ? ' · ' . $report->error_type : '' }}</div>
                            </td>
                            <td><span class="error-report-status {{ $report->status === 'resolved' ? 'is-resolved' : 'is-open' }}">{{ $report->status === 'resolved' ? 'Selesai' : 'Perlu ditinjau' }}</span></td>
                            <td class="text-center"><a href="{{ route('admin.error-reports.show', $report) }}" class="btn btn-sm btn-outline-info rounded-pill px-3"><i class="fas fa-eye me-1"></i>Detail</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="error-report-empty">
                                <div class="error-report-empty-icon"><i class="fas fa-inbox" aria-hidden="true"></i></div>
                                <div class="error-report-empty-title">Belum ada laporan error</div>
                                <p class="error-report-empty-copy">Laporan kendala dari operator akan muncul di sini setelah dikirim.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($reports->hasPages())
            <div class="p-3 border-top border-secondary border-opacity-25">{{ $reports->links() }}</div>
        @endif
    </section>
</div>
@endsection
