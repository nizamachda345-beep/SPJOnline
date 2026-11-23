<?php

namespace App\Http\Middleware;

use App\Models\UserActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class TrackUserActivity
{
    private const EXCLUDED_ROUTES = [
        'activity.presence',
        'admin.activity.feed',
        'dashboard.chart-data',
        'notifications.fetch',
    ];

    private const ACTIVITY_LABELS = [
        'dashboard' => 'Membuka dashboard',
        'login.submit' => 'Masuk ke sistem',
        'logout' => 'Keluar dari sistem',
        'notifications.readAll' => 'Menandai notifikasi sebagai dibaca',
        'profile.edit' => 'Membuka profil akun',
        'profile.update' => 'Memperbarui profil akun',
        'error-reports.store' => 'Mengirim laporan log error',
        'admin.error-reports.index' => 'Membuka daftar laporan error',
        'admin.error-reports.show' => 'Melihat detail laporan error',
        'admin.error-reports.resolve' => 'Menandai laporan error selesai',
        'admin.dashboard' => 'Membuka dashboard admin',
        'admin.monitoring' => 'Membuka monitoring global',
        'admin.activity.index' => 'Membuka log aktivitas pengguna',
        'monitoring.show' => 'Membuka detail monitoring pengajuan',
        'monitoring.reset' => 'Mereset status pengajuan',
        'users.index' => 'Membuka manajemen pengguna',
        'users.store' => 'Menambahkan pengguna',
        'users.update' => 'Memperbarui pengguna',
        'users.destroy' => 'Menghapus pengguna',
        'departments.index' => 'Membuka manajemen SKPD',
        'departments.store' => 'Menambahkan SKPD',
        'departments.update' => 'Memperbarui data SKPD',
        'departments.destroy' => 'Menghapus SKPD',
        'departments.submission-access' => 'Mengubah akses pengajuan SKPD',
        'settings.index' => 'Membuka Master Settings',
        'settings.update' => 'Menyimpan Master Settings',
        'settings.logs' => 'Membuka riwayat perubahan pengaturan',
        'submissions.index' => 'Membuka daftar pengajuan',
        'submissions.sp2d-upload' => 'Membuka daftar unggah SP2D',
        'submissions.backup' => 'Mengunduh cadangan pengajuan',
        'submissions.download-bundle' => 'Mengunduh berkas pengajuan',
        'submissions.show' => 'Membuka detail pengajuan',
        'submissions.create' => 'Membuka formulir pengajuan baru',
        'submissions.store' => 'Mengirim pengajuan baru',
        'submissions.update' => 'Mengirim revisi pengajuan',
        'submissions.uploadSpj' => 'Mengunggah dokumen SPJ',
        'submissions.uploadSts' => 'Mengunggah dokumen STS',
        'submissions.uploadSp2d' => 'Mengunggah dokumen SP2D',
        'verifikasi.index' => 'Membuka daftar verifikasi permintaan',
        'verifikasi.sp2d' => 'Membuka daftar verifikasi SP2D',
        'verifikasi.show' => 'Membuka detail verifikasi permintaan',
        'verifikasi.document.action' => 'Memproses verifikasi dokumen',
        'verifikasi.tolak' => 'Menolak permintaan',
        'verifikasi.sendToKabid' => 'Meneruskan permintaan ke Kabid',
        'validasi.index' => 'Membuka daftar validasi permintaan',
        'validasi.show' => 'Membuka detail validasi permintaan',
        'validasi.document.preview' => 'Melihat pratinjau dokumen validasi',
        'validasi.sahkan' => 'Mengesahkan permintaan',
        'validasi.tolak' => 'Menolak permintaan',
        'ba-rekon.archive' => 'Membuka arsip BA Rekon',
        'ba-rekon.archive.file' => 'Melihat berkas BA Rekon',
        'ba-rekon.store' => 'Mengunggah BA Rekon',
        'validasi.ba-rekon.show' => 'Membuka detail BA Rekon',
        'validasi.ba-rekon.verifikasi' => 'Memverifikasi BA Rekon',
        'validasi.ba-rekon.tolak' => 'Menolak BA Rekon',
        'history.index' => 'Membuka arsip permintaan',
        'history.export' => 'Mengekspor arsip permintaan',
        'history.print' => 'Mencetak arsip permintaan',
        'sp2d.verifikasi' => 'Memverifikasi SP2D',
        'bpk.dashboard' => 'Membuka dashboard pemeriksaan BPK',
        'bpk.monitoring' => 'Membuka monitoring pemeriksaan BPK',
        'bpk.show' => 'Membuka detail pemeriksaan BPK',
        'bpk.audit.submit' => 'Mengirim hasil pemeriksaan BPK',
    ];

    public function handle(Request $request, \Closure $next): Response
    {
        $authenticatedBeforeRequest = $request->user();
        $response = $next($request);
        $user = $authenticatedBeforeRequest ?? $request->user();

        if (!$user) {
            return $response;
        }

        $routeName = $request->route()?->getName();
        $now = now();
        $isPollingRequest = in_array($routeName, ['admin.activity.feed', 'dashboard.chart-data', 'notifications.fetch'], true);

        try {
            if (!$isPollingRequest) {
                // Bound presence writes while allowing the browser to send active-user heartbeats.
                DB::table('users')
                    ->where('id', $user->getKey())
                    ->where(function ($query) use ($now) {
                        $query->whereNull('last_seen_at')
                            ->orWhere('last_seen_at', '<=', $now->copy()->subSeconds(25));
                    })
                    ->update(['last_seen_at' => $now]);
            }

            if ($routeName && !in_array($routeName, self::EXCLUDED_ROUTES, true) && $response->getStatusCode() < 500) {
                UserActivityLog::create([
                    'user_id' => $user->getKey(),
                    'actor_name' => $user->name,
                    'role_name' => $user->isActualSuperAdmin() && $user->accessMode()
                        ? 'Master sebagai ' . (str_replace('_', ' ', $user->accessMode()))
                        : $user->roles->pluck('name')->map(fn ($role) => str_replace('_', ' ', $role))->join(', '),
                    'department_name' => $user->department?->nama_dinas,
                    'activity' => self::ACTIVITY_LABELS[$routeName]
                        ?? (($request->isMethod('GET') ? 'Membuka: ' : 'Menjalankan aksi: ') . Str::headline($routeName)),
                    'route_name' => $routeName,
                    'http_method' => $request->method(),
                    'occurred_at' => $now,
                ]);
            }
        } catch (Throwable $exception) {
            // Activity tracking must not break the user's primary request.
            report($exception);
        }

        return $response;
    }
}
