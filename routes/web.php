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
use App\Http\Controllers\TrainerShiftController;
use App\Http\Controllers\AttendanceScanController;

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
    // 🏠 Home
    // ===============================
    Route::get('/home', [HomeController::class, 'index'])->name('admin.home');

    // ===============================
    // 👥 Manage User
    // ===============================
    Route::prefix('manage')->group(function () {
        Route::get('/', [UserController::class, 'manage'])->name('users.manage');
        Route::post('/', [UserController::class, 'storeWeb'])->name('users.store');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/{id}', [UserController::class, 'updateWeb'])->name('users.update');
        Route::delete('/{id}', [UserController::class, 'destroyWeb'])->name('users.destroy');
    });

    // ===============================
    // 📚 Kelas
    // ===============================
    Route::resource('kelas', KelasController::class)->parameters([
        'kelas' => 'kelas',
    ]);

    Route::get('/kelas/{id}/qr', [KelasController::class, 'getQr'])
        ->whereNumber('id')
        ->name('kelas.qr');

    // ===============================
    // 🕒 Schedule
    // ===============================
    Route::get('/schedules/export-pdf', [ScheduleController::class, 'exportPDF'])->name('schedules.exportPDF');
    Route::resource('schedules', ScheduleController::class)->except(['create', 'show']);
    Route::get('/schedules/{id}/edit', [ScheduleController::class, 'edit'])->name('schedules.edit');

    // ===============================
    // 💸 Diskon
    // ===============================
    Route::resource('diskon', DiskonController::class)->except(['create', 'edit']);

    // ===============================
    // 🎟 Voucher
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

    // ===============================
    // 👥 Member
    // ===============================
    Route::prefix('member')->group(function () {
        Route::get('/', [MemberController::class, 'index'])->name('member.index');
        Route::post('/create', [MemberController::class, 'store'])->name('member.store');
        Route::post('/assign-user', [MemberController::class, 'assignUser'])
            ->name('member.assignUser');
    });

    // ===============================
    // 🎟 Token Package
    // ===============================
    Route::resource('token-package', TokenPackageController::class);

    // ===============================
    // 🔴 ABSENSI SCAN (FINAL & SATU-SATUNYA)
    // ===============================
    Route::post('/attendance/scan', [AttendanceScanController::class, 'scan'])
        ->name('attendance.scan');


    // ===============================
    // 📷 HALAMAN SCAN
    // ===============================
    Route::get('/scan', function () {
        return view('admin.scan');
    })->name('admin.scan');


    // ===============================
    // 🧑‍🏫 Trainer Shift
    // ===============================
    Route::resource('trainer-shifts', TrainerShiftController::class);
});
