<?php
// Routes: web

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\{
    AuthController, DashboardController, SubmissionController,
    VerifikasiController, ValidasiController, HistoryController,
    BpkController, SuperAdminController
};
use App\Http\Controllers\Admin\{DepartmentController, SettingsController, UserController};
use App\Http\Controllers\BaRekonController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AccessModeController;

// Function: Mengarahkan halaman utama ke login
Route::get('/', fn() => redirect()->route('login'));

Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login')->middleware(\App\Http\Middleware\TrackUserActivity::class)->name('login.submit');
    Route::post('/logout', 'logout')->middleware(['auth', \App\Http\Middleware\TrackUserActivity::class])->name('logout');
});

Route::middleware([
    'auth',
    \App\Http\Middleware\ApplyMaintenanceMode::class,
    \App\Http\Middleware\EnsureMasterAccessMode::class,
    \App\Http\Middleware\TrackUserActivity::class,
])->group(function () {

    // Route: Menggantikan symlink public/storage di Wasmer Edge
    Route::get('/storage/{path}', function (string $path) {
        abort_if(str_contains($path, '..'), 403);
        abort_unless(Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path);
    })->where('path', '.*');

    Route::post('/access-mode', [AccessModeController::class, 'update'])->name('access-mode.update');

    Route::post('/activity/presence', fn () => response()->json(['ok' => true]))
        ->name('activity.presence');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::post('/error-reports', [\App\Http\Controllers\ErrorReportController::class, 'store'])
        ->middleware('role:operator')
        ->name('error-reports.store');

    Route::middleware(['role:operator|kasie_bud|kabid_perbendaharaan|super_admin'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/chart-data', [DashboardController::class, 'chartData'])->name('dashboard.chart-data');
    });

    Route::middleware('role:kabid_perbendaharaan|kasie_bud|super_admin')->group(function () {
        Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
        Route::get('/history/export', [HistoryController::class, 'exportExcel'])->name('history.export');
        Route::get('/history/print', [HistoryController::class, 'printPDF'])->name('history.print');
        Route::get('/history/{id}', [HistoryController::class, 'show'])->name('history.show');
    });

    Route::get('/submissions', [SubmissionController::class, 'index'])
        ->middleware('role:operator|super_admin')
        ->name('submissions.index');

    Route::get('/submissions/sp2d-upload', [SubmissionController::class, 'sp2dUploadIndex'])
        ->middleware('role:operator')
        ->name('submissions.sp2d-upload');

    Route::get('/submissions/backup', [SubmissionController::class, 'backup'])
        ->middleware('role:operator|kabid_perbendaharaan|kasie_bud|super_admin')
        ->name('submissions.backup');

    Route::get('/submissions/{id}/download-bundle', [SubmissionController::class, 'downloadPdfBundle'])
        ->middleware('role:operator|kabid_perbendaharaan|kasie_bud|super_admin|bpk')
        ->name('submissions.download-bundle');

    Route::get('/submissions/{id}', [SubmissionController::class, 'show'])
        ->middleware('role:operator|super_admin')
        ->name('submissions.show');

    Route::delete('/submissions/{id}', [SubmissionController::class, 'destroy'])
        ->middleware('role:operator|super_admin')
        ->name('submissions.destroy');

    Route::middleware('role:super_admin')->prefix('admin')->group(function () {
        Route::get('/error-reports', [\App\Http\Controllers\Admin\ErrorReportController::class, 'index'])->name('admin.error-reports.index');
        Route::get('/error-reports/{errorReport}', [\App\Http\Controllers\Admin\ErrorReportController::class, 'show'])->name('admin.error-reports.show');
        Route::patch('/error-reports/{errorReport}/resolve', [\App\Http\Controllers\Admin\ErrorReportController::class, 'resolve'])->name('admin.error-reports.resolve');
        Route::get('/activity', [\App\Http\Controllers\Admin\UserActivityController::class, 'index'])->name('admin.activity.index');
        Route::get('/activity/feed', [\App\Http\Controllers\Admin\UserActivityController::class, 'feed'])->name('admin.activity.feed');
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
        Route::get('/settings/logs', [SettingsController::class, 'logs'])->name('settings.logs');
        Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/monitoring', [SuperAdminController::class, 'index'])->name('admin.monitoring');
        Route::get('/monitoring/{id}', [SuperAdminController::class, 'show'])->name('monitoring.show');
        Route::post('/monitoring/{id}/reset', [SuperAdminController::class, 'resetStatus'])->name('monitoring.reset');

        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::match(['put', 'patch'], '/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::middleware(['role:operator'])->group(function () {
        Route::post('/ba-rekon/store', [BaRekonController::class, 'store'])->name('ba-rekon.store');
    });

    Route::middleware('role:operator|kasie_bud|kabid_perbendaharaan|super_admin')->prefix('ba-rekon')->name('ba-rekon.')->group(function () {
        Route::get('/arsip', [BaRekonController::class, 'archive'])->name('archive');
        Route::get('/{id}/file', [BaRekonController::class, 'archiveFile'])->name('archive.file');
    });

    Route::middleware(['role:operator', 'check.dept.upload'])->group(function () {
        Route::get('/submission/create', [SubmissionController::class, 'create'])->name('submissions.create');
        Route::post('/submission/store', [SubmissionController::class, 'store'])->name('submissions.store');
    });

    Route::middleware('role:operator')->group(function () {
        Route::put('/submission/{id}/update-revisi', [SubmissionController::class, 'update'])->name('submissions.update');
        Route::post('/submissions/{id}/upload-spj', [SubmissionController::class, 'uploadSpj'])->name('submissions.uploadSpj');
        Route::post('/submissions/{id}/upload-sts', [SubmissionController::class, 'uploadSts'])->name('submissions.uploadSts');
        Route::post('/submissions/{id}/upload-sp2d', [SubmissionController::class, 'uploadSp2d'])->name('submissions.uploadSp2d');
    });

    Route::middleware('role:kasie_bud')->prefix('verifikasi')->name('verifikasi.')->group(function () {
        Route::get('/sp2d', [VerifikasiController::class, 'sp2dIndex'])->name('sp2d');
        Route::get('/', [VerifikasiController::class, 'index'])->name('index');
        Route::get('/{id}', [VerifikasiController::class, 'show'])->name('show');
        Route::post('/document/{id}/action', [VerifikasiController::class, 'documentAction'])->name('document.action');
        Route::put('/{id}/tolak', [VerifikasiController::class, 'tolak'])->name('tolak');
        Route::post('/{id}/send-to-kabid', [VerifikasiController::class, 'sendToKabid'])->name('sendToKabid');
    });

    Route::middleware(['role:kabid_perbendaharaan|kasie_bud|super_admin'])->group(function () {
        Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
        Route::post('/departments/{department}/submission-access', [DepartmentController::class, 'updateSubmissionAccess'])->name('departments.submission-access');
        Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
        Route::match(['put', 'patch'], '/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
        Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');
        Route::post('/sp2d/verifikasi/{id}', [SubmissionController::class, 'verifikasiSp2d'])->name('sp2d.verifikasi');
    });

    Route::prefix('validasi')->name('validasi.')->group(function () {
        Route::middleware('role:kabid_perbendaharaan|kasie_bud|super_admin')->group(function () {
            Route::get('/ba-rekon/{id}', [BaRekonController::class, 'showKabid'])->name('ba-rekon.show');
            Route::post('/ba-rekon/{id}/verifikasi', [BaRekonController::class, 'verifikasi'])->name('ba-rekon.verifikasi');
            Route::post('/ba-rekon/{id}/tolak', [BaRekonController::class, 'tolakBaRekon'])->name('ba-rekon.tolak');
        });

        Route::middleware('role:kabid_perbendaharaan|super_admin')->group(function () {
            Route::get('/', [ValidasiController::class, 'index'])->name('index');
            Route::get('/{id}', [ValidasiController::class, 'show'])->name('show');
            Route::get('/document/{id}/preview', [ValidasiController::class, 'previewPdf'])->name('document.preview');
            Route::post('/{id}/sahkan', [ValidasiController::class, 'sahkan'])->name('sahkan');
            Route::post('/{id}/tolak', [ValidasiController::class, 'tolak'])->name('tolak');
        });
    });

    Route::middleware('role:bpk')->prefix('audit')->name('bpk.')->group(function () {
        Route::get('/dashboard', [BpkController::class, 'dashboard'])->name('dashboard');
        Route::get('/monitoring', [BpkController::class, 'index'])->name('monitoring');
        Route::get('/detail/{id}', [BpkController::class, 'show'])->name('show');
        Route::post('/detail/{id}/submit', [BpkController::class, 'submitAudit'])->name('audit.submit');
        Route::get('/history/{id}', [HistoryController::class, 'show'])->name('history.show');
    });

    // Function: Menandai semua notifikasi sebagai dibaca
    Route::post('/notifications/read-all', function () {
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    })->name('notifications.readAll');

    Route::get('/notifications/fetch-latest', [DashboardController::class, 'fetchLatest'])->name('notifications.fetch');
});