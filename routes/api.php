<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReservasiController;
use App\Http\Controllers\Api\KelasController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\DiskonController;
use App\Http\Controllers\Api\ScheduleApiController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MidtransController;
use App\Http\Controllers\Api\TokenPackageController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\TransaksiController;

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);
Route::post('/google-login', [AuthController::class, 'googleLogin']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('jwt.auth');
Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('jwt.refresh');

/*
|--------------------------------------------------------------------------
| USER LOGIN DATA (JWT)
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
| TRAINER (PUBLIC)
|--------------------------------------------------------------------------
*/
Route::get('/users/trainer', [UserController::class, 'getTrainers']);

/*
|--------------------------------------------------------------------------
| SCHEDULE (PUBLIC)
|--------------------------------------------------------------------------
*/
Route::get('/schedule', [ScheduleApiController::class, 'index']);
Route::get('/schedule/{id}', [ScheduleApiController::class, 'show']);
Route::get('/trainer/schedule', [ScheduleApiController::class, 'byTrainer']);

/*
|--------------------------------------------------------------------------
| DISKON
|--------------------------------------------------------------------------
| - Pelanggan: READ ONLY
| - CRUD: BUKAN PUBLIC (admin saja, kalau ada)
*/
Route::get('/diskon', [DiskonController::class, 'index']);
Route::get('/diskon/{id}', [DiskonController::class, 'show']);

/*
|--------------------------------------------------------------------------
| RESERVASI (JWT - PELANGGAN)
|--------------------------------------------------------------------------
*/
Route::middleware(['jwt.auth', 'role:pelanggan'])->group(function () {
    Route::apiResource('reservasi', ReservasiController::class);
});

/*
|--------------------------------------------------------------------------
| MEMBER (JWT - PELANGGAN)
|--------------------------------------------------------------------------
*/
Route::prefix('member')->middleware(['jwt.auth', 'role:pelanggan'])->group(function () {

    // membership
    Route::post('/', [MemberController::class, 'store']);
    Route::get('/kelas', [MemberController::class, 'kelasMember']);
    Route::post('/bayar', [MemberController::class, 'bayarDummy']);
    Route::post('/ikut-kelas', [MemberController::class, 'ikutKelas']);
    Route::get('/status', [MemberController::class, 'checkStatus']);

    // transaksi
    Route::post('/transaksi', [TransaksiController::class, 'create']);
    Route::get('/transaksi', [TransaksiController::class, 'index']);
    Route::get('/transaksi/{id}', [TransaksiController::class, 'show']);
    Route::get('/transaksi/sync', [TransaksiController::class, 'sync']);

    // midtrans
    Route::post('/midtrans/create', [MidtransController::class, 'createTransaction']);
    Route::post('/midtrans/token', [MidtransController::class, 'getSnapToken']);
});

/*
|--------------------------------------------------------------------------
| CHECKOUT (JWT - PELANGGAN)
|--------------------------------------------------------------------------
*/
Route::middleware(['jwt.auth', 'role:pelanggan'])->group(function () {
    Route::post('/checkout/price', [CheckoutController::class, 'price']);
    Route::post('/checkout/confirm', [CheckoutController::class, 'confirm']);
});

/*
|--------------------------------------------------------------------------
| TOKEN PACKAGES (PUBLIC READ)
|--------------------------------------------------------------------------
*/
Route::get('/token-packages', [TokenPackageController::class, 'index']);
Route::get('/token-packages/{id}', [TokenPackageController::class, 'show']);

/*
|--------------------------------------------------------------------------
| MIDTRANS CALLBACK (NO AUTH)
|--------------------------------------------------------------------------
| ⚠️ HARUS SATU PINTU
*/
Route::post('/transaksi/store', [TransaksiController::class, 'store']);
Route::post('/transaksi/callback', [TransaksiController::class, 'callback']);
