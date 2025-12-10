<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\DiskonController;
use App\Http\Controllers\ReservasiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VisitLogController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\TokenPackageController;
use App\Http\Controllers\QrCodeController;

/*
|--------------------------------------------------------------------------
| Redirect default
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => redirect()->route('admin.login'));

/*
|--------------------------------------------------------------------------
| Auth (Login & Logout)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware('web')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [LoginController::class, 'login'])->name('admin.login.submit');
    Route::post('/logout', [LoginController::class, 'logout'])->name('admin.logout');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (hanya untuk role=admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['web', 'auth:web', 'role.admin'])->group(function () {

    // ===============================
    // 🏠 Halaman Home (Dashboard Ringkas)
    // ===============================
    Route::get('/home', [HomeController::class, 'index'])->name('admin.home');

    // ===============================
    // 👥 Manage User (TANPA Member, karena dipindah)
    // ===============================
    Route::prefix('manage')->group(function () {
        Route::get('/', [UserController::class, 'manage'])->name('users.manage');
        Route::post('/', [UserController::class, 'storeWeb'])->name('users.store');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/{id}', [UserController::class, 'updateWeb'])->name('users.update');
        Route::delete('/{id}', [UserController::class, 'destroyWeb'])->name('users.destroy');
    });

    // Resource Kelas
    Route::resource('kelas', KelasController::class)->parameters([
        'kelas' => 'kelas',
    ]);

    // Route tambahan untuk QR
    Route::get('/admin/kelas/{id}/qr', [KelasController::class, 'getQr'])
        ->whereNumber('id') // <- ini penting, hanya menerima numeric
        ->name('kelas.qr');


    // ===============================
    // 🕒 Jadwal / Schedule
    // ===============================
    Route::get('/schedules/export-pdf', [ScheduleController::class, 'exportPDF'])->name('schedules.exportPDF');
    Route::resource('schedules', ScheduleController::class)->except(['create', 'show']);
    Route::get('/schedules/{id}/edit', [ScheduleController::class, 'edit'])->name('schedules.edit');

    // ===============================
    // 💸 Diskon CRUD
    // ===============================
    Route::resource('diskon', DiskonController::class)->except(['create', 'edit']);

    // ===============================
    // 🎟 Voucher CRUD
    // ===============================
    Route::resource('voucher', VoucherController::class)->except(['create', 'edit']);

    // ===============================
    // 📊 Dashboard
    // ===============================
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    // ===============================
    // 📅 Reservasi
    // ===============================
    Route::resource('reservasi', ReservasiController::class);
    Route::patch('/reservasi/{id}/status', [ReservasiController::class, 'updateStatus'])
        ->name('reservasi.updateStatus');

    // ===============================
    // 👀 VisitLog
    // ===============================
    Route::get('/visitlog', [VisitLogController::class, 'index'])->name('visitlog.index');
    Route::get('/checkin', [VisitLogController::class, 'store']);


    // ===============================
    // 👥 Manage Member (BENAR)
    // ===============================
    Route::prefix('member')->group(function () {

        Route::get('/', [MemberController::class, 'index'])->name('member.index');

        Route::post('/create', [MemberController::class, 'store'])->name('member.store');

        // Route assign user yang BENAR
        Route::post('/assign-user', [MemberController::class, 'assignUser'])
            ->name('member.assignUser');
    });

    // ===============================
    // Token
    // ===============================
    Route::prefix('admin')->group(function () {
        Route::resource('token-package', TokenPackageController::class);
    });

    // ===============================
    // Qr Code
    // ===============================
    Route::get('/kelas/{kelas_id}/qr', [QrCodeController::class, 'show'])
        ->name('kelas.qr.modal')
        ->middleware('auth');
    Route::get('/kelas/{kelas_id}/qr', [QrCodeController::class, 'show']);
});