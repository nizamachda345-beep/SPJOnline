@extends('layouts.app')

@section('content')
<style>
    .error-detail-page { color: #e2e8f0; }
    .error-detail-header,
    .error-detail-card {
        overflow: hidden;
        border: 1px solid rgba(148, 163, 184, .16);
        border-radius: 1rem;
        background: rgba(15, 23, 42, .9);
        box-shadow: 0 12px 32px rgba(2, 6, 23, .18);
    }
    .error-detail-header { margin-bottom: 1rem; }
    .error-detail-header-body { padding: 1.1rem 1.35rem; }
    .error-detail-icon {
        display: grid; place-items: center; flex: 0 0 3rem; height: 3rem;
        border: 1px solid rgba(248, 113, 113, .22); border-radius: .85rem;
        color: #fda4af; background: rgba(190, 24, 93, .16); font-size: 1.1rem;
    }
    .error-detail-subtitle { color: #aab7c9; font-size: .82rem; }
    .error-detail-status {
        display: inline-flex; align-items: center; gap: .45rem; padding: .48rem .75rem;
        border: 1px solid; border-radius: 999px; font-size: .76rem; font-weight: 600;
    }
    .error-detail-status.is-open { color: #fcd34d; border-color: rgba(245, 158, 11, .28); background: rgba(245, 158, 11, .08); }
    .error-detail-status.is-resolved { color: #86efac; border-color: rgba(34, 197, 94, .25); background: rgba(34, 197, 94, .08); }
    .error-detail-card { height: 100%; }
    .error-detail-card-heading {
        display: flex; align-items: center; justify-content: space-between; gap: .75rem;
        min-height: 3.4rem; padding: .85rem 1.1rem;
        border-bottom: 1px solid rgba(148, 163, 184, .16);
        color: #f1f5f9; font-size: .9rem; font-weight: 650;
    }
    .error-detail-card-body { padding: 1.1rem; }
    .error-type-label { color: #9fb2cc; font-size: .78rem; }
    .error-type-chip {
        display: inline-flex; align-items: center; padding: .3rem .6rem;
        border: 1px solid rgba(248, 113, 113, .2); border-radius: .55rem;
        color: #fda4af; background: rgba(190, 24, 93, .1); font-size: .74rem; font-weight: 600;
    }
    .error-message-box {
        min-height: 9rem; margin: .65rem 0 0; padding: 1rem;
        border: 1px solid rgba(148, 163, 184, .14); border-radius: .75rem;
        color: #f1f5f9; background: rgba(2, 6, 23, .42);
        font: .9rem/1.65 ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
        white-space: pre-wrap; overflow-wrap: anywhere;
    }
    .error-detail-hint { color: #94a3b8; font-size: .78rem; line-height: 1.5; }
    .error-context-list { display: grid; grid-template-columns: minmax(5.5rem, .72fr) minmax(0, 1.5fr); margin: 0; }
    .error-context-list dt, .error-context-list dd {
        margin: 0; padding: .62rem 0; border-bottom: 1px solid rgba(148, 163, 184, .1);
        font-size: .8rem; line-height: 1.45;
    }
    .error-context-list dt { color: #94a3b8; font-weight: 500; }
    .error-context-list dd { color: #e2e8f0; overflow-wrap: anywhere; }
    .error-context-list dt:last-of-type, .error-context-list dd:last-of-type { border-bottom: 0; }
    .error-browser-info { color: #b9c6d8 !important; font-size: .74rem !important; }
    .error-resolved-note { padding-top: .85rem; border-top: 1px solid rgba(148, 163, 184, .12); color: #aab7c9; font-size: .78rem; }
    @media (max-width: 575.98px) {
        .error-detail-header-body { padding: 1rem; }
        .error-detail-card-body { padding: .9rem; }
        .error-context-list { grid-template-columns: minmax(4.5rem, .65fr) minmax(0, 1.35fr); }
    }
</style>

<div class="container-fluid px-4 py-2 error-detail-page">
    <section class="error-detail-header">
        <div class="error-detail-header-body d-flex align-items-center justify-content-between gap-3 flex-wrap">
            <div class="d-flex align-items-center gap-3">
                <span class="error-detail-icon" aria-hidden="true"><i class="fas fa-bug"></i></span>
                <div>
                    <h1 class="h5 text-white mb-1">Detail Laporan Error #{{ $errorReport->id }}</h1>
                    <p class="error-detail-subtitle mb-0">Tercatat {{ $errorReport->occurred_at?->timezone($displayTimezone)->format('d M Y, H:i') ?? 'Waktu tidak tersedia' }}</p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="error-detail-status {{ $errorReport->status === 'resolved' ? 'is-resolved' : 'is-open' }}">
                    <i class="fas {{ $errorReport->status === 'resolved' ? 'fa-check-circle' : 'fa-clock' }}" aria-hidden="true"></i>
                    {{ $errorReport->status === 'resolved' ? 'Selesai' : 'Perlu ditinjau' }}
                </span>
                <a href="{{ route('admin.error-reports.index') }}" class="btn btn-outline-light btn-sm rounded-pill px-3"><i class="fas fa-arrow-left me-2"></i>Kembali</a>
            </div>
        </div>
    </section>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    <div class="row g-3 align-items-stretch">
        <div class="col-lg-7">
            <section class="error-detail-card">
                <header class="error-detail-card-heading">
                    <span><i class="fas fa-terminal text-info me-2" aria-hidden="true"></i>Pesan Error</span>
                    @if ($errorReport->error_type)<span class="error-type-chip">{{ $errorReport->error_type }}</span>@endif
                </header>
                <div class="error-detail-card-body">
                    <div class="error-type-label">Ringkasan pesan error</div>
                    <pre class="error-message-box">{{ $errorReport->error_message }}</pre>
                    <p class="error-detail-hint mt-3 mb-0"><i class="fas fa-circle-info me-1"></i>Gunakan pesan dan konteks di samping untuk menelusuri kendala. Laporan ini tidak menyertakan stack trace.</p>
                </div>
            </section>
        </div>

        <div class="col-lg-5">
            <section class="error-detail-card">
                <header class="error-detail-card-heading"><span><i class="fas fa-list-check text-info me-2" aria-hidden="true"></i>Konteks Laporan</span></header>
                <div class="error-detail-card-body">
                    <dl class="error-context-list">
                        <dt>Pengirim</dt><dd>{{ $errorReport->actor_name }}</dd>
                        <dt>Role</dt><dd>{{ $errorReport->role_name ?: '—' }}</dd>
                        <dt>Instansi</dt><dd>{{ $errorReport->department_name ?: '—' }}</dd>
                        <dt>Sumber</dt>
                        <dd>
                            @if ($errorReport->error_type === 'OperatorReport')
                                Laporan operator
                            @elseif (in_array($errorReport->error_type, ['JavaScriptError', 'UnhandledPromiseRejection', 'NetworkError'], true))
                                Browser
                            @else
                                Error server
                            @endif
                        </dd>
                        <dt>Halaman</dt><dd>{{ $errorReport->page_path ?: '—' }}</dd>
                        <dt>Metode</dt><dd>{{ $errorReport->http_method ?: '—' }}</dd>
                        <dt>Browser</dt><dd class="error-browser-info">{{ $errorReport->browser_info ?: '—' }}</dd>
                    </dl>

                    @if ($errorReport->status !== 'resolved')
                        <form method="POST" action="{{ route('admin.error-reports.resolve', $errorReport) }}" class="mt-3">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-success w-100 rounded-pill" type="submit"><i class="fas fa-check me-2"></i>Tandai Selesai</button>
                        </form>
                    @else
                        <div class="error-resolved-note">
                            <i class="fas fa-check-circle text-success me-1" aria-hidden="true"></i>
                            Selesai{{ $errorReport->resolver ? ' oleh ' . $errorReport->resolver->name : '' }}
                            @if ($errorReport->resolved_at)
                                · {{ $errorReport->resolved_at->timezone($displayTimezone)->format('d M Y, H:i') }}
                            @endif
                        </div>
                    @endif
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
