<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CONTROLLERS
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReservasiController;
use App\Http\Controllers\Api\KelasController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\DiskonController;
use App\Http\Controllers\Api\ScheduleApiController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MidtransController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\Api\TokenPackageController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\TrainerProfileController;
use App\Http\Controllers\Api\VoucherController;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);
Route::post('/google-login', [AuthController::class, 'googleLogin']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('jwt.auth')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('jwt.refresh')->post('/refresh', [AuthController::class, 'refresh']);

/*
|--------------------------------------------------------------------------
| ME / USER PROFILE (JWT – ALL ROLES)
|--------------------------------------------------------------------------
*/
Route::middleware('jwt.auth')->get('/me', function () {
    return auth()->user();
});

// Alias untuk frontend
Route::middleware('jwt.auth')->get('/user', function () {
    return auth()->user();
});

/*
|--------------------------------------------------------------------------
| KELAS (PUBLIC)
|--------------------------------------------------------------------------
*/
Route::get('/kelas', [KelasController::class, 'index']);
Route::get('/kelas/{id}', [KelasController::class, 'show']);

/*
|--------------------------------------------------------------------------
| RESERVASI (JWT – PELANGGAN)
|--------------------------------------------------------------------------
*/
Route::middleware(['jwt.auth', 'role:pelanggan'])
    ->apiResource('reservasi', ReservasiController::class);

/*
|--------------------------------------------------------------------------
| TRAINER (PUBLIC LIST)
|--------------------------------------------------------------------------
*/
Route::get('/users/trainer', [UserController::class, 'getTrainers']);

/*
|--------------------------------------------------------------------------
| DISKON
|--------------------------------------------------------------------------
*/
Route::apiResource('diskon', DiskonController::class);

/*
|--------------------------------------------------------------------------
| VOUCHERS
|--------------------------------------------------------------------------
*/
// PUBLIC: bisa dilihat siapa saja
Route::get('/vouchers', [VoucherController::class, 'index']);

// JWT: khusus user
Route::middleware('jwt.auth')->group(function () {
    Route::get('/vouchers/my', [VoucherController::class, 'userVouchers']);
    Route::post('/voucher/claim', [VoucherController::class, 'claim']);
});

/*
|--------------------------------------------------------------------------
| SCHEDULE
|--------------------------------------------------------------------------
*/
Route::get('/schedule', [ScheduleApiController::class, 'index']);
Route::get('/schedule/{id}', [ScheduleApiController::class, 'show']);

Route::middleware(['jwt.auth', 'role:trainer'])
    ->get('/trainer/schedule', [ScheduleApiController::class, 'byTrainer']);

/*
|--------------------------------------------------------------------------
| MEMBER (JWT)
|--------------------------------------------------------------------------
*/
Route::prefix('member')->middleware('jwt.auth')->group(function () {
    Route::post('/store', [MemberController::class, 'store']);
    Route::get('/kelas', [MemberController::class, 'kelasMember']);
    Route::post('/bayar', [MemberController::class, 'bayarDummy']);
    Route::post('/ikut-kelas', [MemberController::class, 'ikutKelas']);
    Route::get('/transaksi/sync', [TransaksiController::class, 'sync']);

    // MIDTRANS
    Route::post('/midtrans/create', [MidtransController::class, 'createTransaction']);
    Route::post('/midtrans/token', [MidtransController::class, 'getSnapToken']);

    // TRANSAKSI
    Route::post('/transaksi/create', [TransaksiController::class, 'create']);
    Route::get('/transaksi', [TransaksiController::class, 'index']);
    Route::get('/transaksi/{id}', [TransaksiController::class, 'show']);
});

/*
|--------------------------------------------------------------------------
| MIDTRANS CALLBACK (NO AUTH)
|--------------------------------------------------------------------------
*/
Route::post('/transaksi/store', [TransaksiController::class, 'store']);
Route::post('/transaksi/callback', [TransaksiController::class, 'callback']);

/*
|--------------------------------------------------------------------------
| TOKEN PACKAGES
|--------------------------------------------------------------------------
*/
Route::apiResource('token-packages', TokenPackageController::class);

/*
|--------------------------------------------------------------------------
| CHECKOUT (JWT – PELANGGAN)
|--------------------------------------------------------------------------
*/
Route::middleware(['jwt.auth', 'role:pelanggan'])->group(function () {
    Route::post('/checkout/price', [CheckoutController::class, 'price']);
    Route::post('/checkout/confirm', [CheckoutController::class, 'confirm']);

    // Tambahan: route Midtrans token agar FormBooking.tsx bisa pakai
    Route::post('/checkout/midtrans/token', [CheckoutController::class, 'midtransToken']);
});

/*
|--------------------------------------------------------------------------
| TRAINER PROFILE (JWT – TRAINER)
|--------------------------------------------------------------------------
*/
Route::middleware(['jwt.auth', 'role:trainer'])->group(function () {
    Route::get('/trainer/profile', [TrainerProfileController::class, 'show']);
    Route::post('/trainer/profile', [TrainerProfileController::class, 'store']);
});
