<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);
Route::post('/google-login', [AuthController::class, 'googleLogin']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('jwt.auth');

Route::middleware('jwt.refresh')->post('/refresh', [AuthController::class, 'refresh']);

/*
|--------------------------------------------------------------------------
| USER (JWT)
|--------------------------------------------------------------------------
*/
Route::middleware(['jwt.auth', 'role:pelanggan'])->get('/user', function () {
    return auth()->user();
});

/*
|--------------------------------------------------------------------------
| KELAS (PUBLIC - READ ONLY)
|--------------------------------------------------------------------------
*/
Route::get('/kelas', [KelasController::class, 'index']);
Route::get('/kelas/{id}', [KelasController::class, 'show']);

/*
|--------------------------------------------------------------------------
| RESERVASI (JWT - PELANGGAN)
|--------------------------------------------------------------------------
*/
Route::middleware(['jwt.auth', 'role:pelanggan'])
    ->apiResource('reservasi', ReservasiController::class);

/*
|--------------------------------------------------------------------------
| TRAINER (PUBLIC)
|--------------------------------------------------------------------------
*/
Route::get('/users/trainer', [UserController::class, 'getTrainers']);

/*
|--------------------------------------------------------------------------
| DISKON (TETAP ADA, TIDAK DIUBAH)
|--------------------------------------------------------------------------
*/
Route::apiResource('diskon', DiskonController::class);

/*
|--------------------------------------------------------------------------
| SCHEDULE
|--------------------------------------------------------------------------
*/
Route::get('/schedule', [ScheduleApiController::class, 'index']);
Route::get('/schedule/{id}', [ScheduleApiController::class, 'show']);
Route::get('/trainer/schedule', [ScheduleApiController::class, 'byTrainer']);

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
| CHECKOUT (JWT - PELANGGAN)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:api')->group(function () {
    Route::post('/checkout/price', [CheckoutController::class, 'price']);
    Route::post('/checkout/confirm', [CheckoutController::class, 'confirm']);
});
