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
use App\Http\Controllers\ManageDiskonController;
use App\Http\Controllers\Admin\HomeController;

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
    // 👥 Manage User & Member
    // ===============================
    Route::prefix('manage')->group(function () {
        Route::get('/', [UserController::class, 'manage'])->name('users.manage');
        Route::post('/', [UserController::class, 'storeWeb'])->name('users.store');
        Route::post('/member', [UserController::class, 'storeMember'])->name('members.store');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/{id}', [UserController::class, 'updateWeb'])->name('users.update');
        Route::delete('/{id}', [UserController::class, 'destroyWeb'])->name('users.destroy');
    });

    // ===============================
    // 📘 Resource Kelas
    // ===============================
    Route::prefix('users')->group(function () {
        Route::resource('kelas', KelasController::class)->parameters([
            'kelas' => 'kelas',
        ]);
    });

    // ===============================
    // 🕒 Jadwal / Schedule
    // ===============================
    // gunakan resource, exclude create/edit karena pakai modal
    Route::resource('schedules', ScheduleController::class)->except(['create', 'edit']);

    // route filter tetap dipertahankan
    Route::get('/schedules/filter', [ScheduleController::class, 'filter'])->name('schedules.filter');

    // ===============================
    // 💸 Diskon CRUD
    // ===============================
    Route::resource('diskon', DiskonController::class)->except(['create', 'edit']);

    // ===============================
    // 🎟️ Voucher CRUD
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
});
