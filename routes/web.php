<?php



use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

use App\Http\Controllers\DashboardController;

use App\Http\Controllers\SubmissionController;

use App\Http\Controllers\VerifikasiController;

use App\Http\Controllers\ValidasiController;

use App\Http\Controllers\AdminController;

use App\Http\Controllers\HistoryController;

use App\Http\Controllers\Admin\DepartmentController;

use App\Http\Controllers\Admin\UserController;

use App\Http\Controllers\BpkController;

use App\Http\Controllers\SuperAdminController;

/*

|--------------------------------------------------------------------------

| Web Routes

|--------------------------------------------------------------------------

*/



// --- PUBLIC ROUTES ---

Route::get('/', function () {

    return redirect()->route('login');

});



Route::controller(AuthController::class)->group(function () {

    Route::get('/login', 'showLogin')->name('login');

    Route::post('/login', 'login');

    Route::post('/logout', 'logout')->name('logout');

});



// --- PROTECTED ROUTES (Semua rute di bawah wajib Login) ---

Route::middleware(['auth'])->group(function () {



    // 1. Dashboard Utama

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');



    // 2. History (Akses untuk Role Tertentu)

    Route::get('/history', [HistoryController::class, 'index'])

        ->name('history.index')

        ->middleware('role:kabid_perbendaharaan|kasie_bud|super_admin');



    Route::get('/history/{id}', [HistoryController::class, 'show'])

        ->name('history.show')

        ->middleware('role:kabid_perbendaharaan|kasie_bud|bpk|super_admin');



    // 3. Fitur Submission (Akses Umum untuk List & Detail)

    Route::get('/submissions', [SubmissionController::class, 'index'])->name('submissions.index');

    Route::get('/submissions/{id}', [SubmissionController::class, 'show'])->name('submissions.show');



  Route::middleware(['auth', 'role:super_admin'])->prefix('admin')->group(function () {
    // Dashboard & Monitoring
   Route::get('/admin/dashboard', [App\Http\Controllers\SuperAdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/monitoring', [SuperAdminController::class, 'index'])->name('admin.monitoring');
    Route::post('/submissions/{id}/reset', [SuperAdminController::class, 'resetStatus'])->name('admin.submissions.reset');
Route::get('/monitoring/{id}', [SuperAdminController::class, 'show'])->name('monitoring.show');
    // Rute Reset Status (Aksi di dalam halaman Show)
    Route::post('/monitoring/{id}/reset', [SuperAdminController::class, 'resetStatus'])->name('monitoring.reset');
    // Data Master (User & Department)
    Route::resource('users', UserController::class);
Route::resource('departments', DepartmentController::class);
    Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset_password');
    // Manajemen Dinas (OPD)
    Route::get('/department', [DepartmentController::class, 'index'])->name('department.index');
    Route::post('/department', [DepartmentController::class, 'store'])->name('department.store');
    Route::put('/department/{id}', [DepartmentController::class, 'update'])->name('department.update');
    Route::delete('/department/{id}', [DepartmentController::class, 'destroy'])->name('department.destroy');
    });



    // 4. Role: OPERATOR (Input & Revisi)

    Route::middleware(['role:operator'])->group(function () {

        Route::get('/submission/create', [SubmissionController::class, 'create'])->name('submissions.create');

        Route::post('/submission/store', [SubmissionController::class, 'store'])->name('submissions.store');

        Route::get('/submission/{id}/edit-revisi', [SubmissionController::class, 'editRevisi'])->name('submissions.edit');

        Route::put('/submission/{id}/update-revisi', [SubmissionController::class, 'update'])->name('submissions.update');

    });



    // 5. Role: KASIE BUD (Verifikasi Tahap 1)

    Route::middleware(['role:kasie_bud'])->group(function () {

        Route::get('/verifikasi', [VerifikasiController::class, 'index'])->name('verifikasi.index');

        Route::get('/verifikasi/{id}', [VerifikasiController::class, 'show'])->name('verifikasi.show');

        Route::post('/verifikasi/document/{id}/action', [VerifikasiController::class, 'documentAction'])->name('verifikasi.document.action');

        Route::post('/verifikasi/{id}/setuju', [VerifikasiController::class, 'setuju'])->name('verifikasi.setuju');

        Route::post('/verifikasi/{id}/tolak', [VerifikasiController::class, 'tolak'])->name('verifikasi.tolak');

    });



    // 6. Role: KABID PERBENDAHARAAN (Validasi Akhir)

    Route::middleware(['role:kabid_perbendaharaan'])->group(function () {

        Route::get('/validasi', [ValidasiController::class, 'index'])->name('validasi.index');

        Route::get('/validasi/{id}', [ValidasiController::class, 'show'])->name('validasi.show');

        Route::post('/validasi/{id}/sahkan', [ValidasiController::class, 'sahkan'])->name('validasi.sahkan');

        Route::post('/validasi/{id}/tolak', [ValidasiController::class, 'tolak'])->name('validasi.tolak');

    });


// 7. Role: BPK (Audit Digital)
Route::middleware(['auth', 'role:bpk'])->prefix('audit')->name('bpk.')->group(function () {
    // Dashboard Khusus BPK
    Route::get('/dashboard', [BpkController::class, 'dashboard'])->name('dashboard');
    
    // Monitoring & Detail
    Route::get('/monitoring', [BpkController::class, 'index'])->name('monitoring');
    Route::get('/detail/{id}', [BpkController::class, 'show'])->name('show');
});





}); // End Middleware Auth Utama