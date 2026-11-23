<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Terjadi Kendala &mdash; {{ config('app.name', 'SPJOnline') }}</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 1.25rem; color: #e2e8f0; background: #111827; font: 15px/1.55 system-ui, sans-serif; }
        .error-card { width: min(100%, 560px); padding: 2rem; border: 1px solid #334155; border-radius: 1rem; background: #1e293b; box-shadow: 0 24px 70px #0005; }
        .error-icon { color: #f87171; font-size: 2rem; }
        h1 { margin: .75rem 0 .4rem; font-size: 1.5rem; }
        p, .muted { color: #aab7c9; }
        textarea { width: 100%; min-height: 110px; padding: .75rem; color: #f8fafc; background: #111827; border: 1px solid #475569; border-radius: .65rem; resize: vertical; }
        button { margin-top: .85rem; padding: .7rem 1rem; color: white; background: #dc2626; border: 0; border-radius: 999px; cursor: pointer; }
        button:disabled { opacity: .6; cursor: wait; }
        #feedback { min-height: 1.5rem; margin-top: .7rem; }
    </style>
</head>
<body>
    <main class="error-card">
        <div class="error-icon" aria-hidden="true">&#9888;</div>
        <h1>Terjadi kendala pada sistem</h1>
        <p>Permintaan belum dapat diproses. Silakan coba kembali. Sistem akan mencoba merekam log teknis untuk membantu penelusuran.</p>

        @if (auth()->check() && auth()->user()->hasRole('operator'))
            @php($automaticErrorReportId = request()->attributes->get('error_report_id'))
            @if ($automaticErrorReportId)
                <p class="muted">Log kendala sudah direkam otomatis dengan nomor laporan #{{ $automaticErrorReportId }}.</p>
            @else
                <form id="serverErrorReportForm">
                    <label for="serverErrorDescription" class="muted">Log otomatis belum tersedia. Jelaskan kendala untuk dikirim ke Super Admin.</label>
                    <textarea id="serverErrorDescription" maxlength="3000" placeholder="Jelaskan langkah yang dilakukan sebelum kendala muncul."></textarea>
                    <button type="submit" id="serverErrorReportButton">Kirim Log Error</button>
                    <div id="feedback" role="status" aria-live="polite"></div>
                </form>
                <script>
                    (() => {
                        const form = document.getElementById('serverErrorReportForm');
                        const button = document.getElementById('serverErrorReportButton');
                        const feedback = document.getElementById('feedback');
                        form.addEventListener('submit', async (event) => {
                            event.preventDefault();
                            button.disabled = true;
                            feedback.textContent = 'Mengirim laporan...';
                            try {
                                const response = await fetch(@json(route('error-reports.store')), {
                                    method: 'POST', credentials: 'same-origin',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                        'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json', 'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        error_type: @json(isset($exception) ? class_basename($exception) : 'ServerError'),
                                        error_message: 'Terjadi HTTP 500 saat memproses halaman ini. ' + document.getElementById('serverErrorDescription').value,
                                        page_path: window.location.pathname,
                                        http_method: 'GET', browser_info: navigator.userAgent
                                    })
                                });
                                const result = await response.json();
                                if (!response.ok) throw new Error(result.message || 'Laporan gagal dikirim.');
                                feedback.textContent = result.message;
                                feedback.style.color = '#86efac';
                            } catch (error) {
                                feedback.textContent = error.message || 'Laporan gagal dikirim.';
                                feedback.style.color = '#fca5a5';
                            } finally {
                                button.disabled = false;
                            }
                        });
                    })();
                </script>
            @endif
        @else
            <p class="muted">Kode kendala: HTTP 500</p>
        @endif
    </main>
</body>
</html>
