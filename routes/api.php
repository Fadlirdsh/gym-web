<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReservasiController;
use App\Http\Controllers\Api\KelasController;
use App\Http\Controllers\Api\KuponController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\DiskonController;
use App\Http\Controllers\Api\ScheduleApiController;
use App\Http\Controllers\Api\AbsensiController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MidtransController;
use App\Http\Controllers\TransaksiController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/

// Kelas
Route::apiResource('kelas', KelasController::class);

// Harga reservasi
Route::get('/harga', [ReservasiController::class, 'getHarga']);

// Trainer public
Route::get('/users/trainer', [UserController::class, 'getTrainers']);

// Schedule public
Route::get('/schedules', [ScheduleApiController::class, 'index']);
Route::get('/schedules/{id}', [ScheduleApiController::class, 'show']);

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/google-login', [AuthController::class, 'googleLogin']);
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->middleware('jwt.auth');
Route::middleware('jwt.refresh')->post('/refresh', [AuthController::class, 'refresh']);

// Get user login data (customer only)
Route::middleware(['jwt.auth', 'role:pelanggan'])->get('/user', function () {
    return auth()->user();
});

/*
|--------------------------------------------------------------------------
| ABSENSI (SANCTUM)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/absensi', [AbsensiController::class, 'absen']);
});

/*
|--------------------------------------------------------------------------
| PELANGGAN PROTECTED ROUTES
|--------------------------------------------------------------------------
*/

// Reservasi
Route::middleware(['jwt.auth', 'role:pelanggan'])
    ->apiResource('reservasi', ReservasiController::class);

// Kupon
Route::middleware(['jwt.auth', 'role:pelanggan'])->group(function () {
    Route::get('/kupon', [KuponController::class, 'aktif']);
    Route::post('/kupon/claim', [KuponController::class, 'claim']);
    Route::post('/kupon/pakai', [KuponController::class, 'pakai']);
});

// Diskon
Route::apiResource('diskon', DiskonController::class);

/*
|--------------------------------------------------------------------------
| MEMBER ROUTES (JWT REQUIRED)
|--------------------------------------------------------------------------
*/
Route::prefix('member')->middleware('jwt.auth')->group(function () {

    // Membership
    Route::post('/store', [MemberController::class, 'store']);
    Route::get('/kelas', [MemberController::class, 'kelasMember']);
    Route::post('/bayar', [MemberController::class, 'bayarDummy']);
    Route::post('/ikut-kelas', [MemberController::class, 'ikutKelas']);

    // Transaksi
    Route::post('/transaksi/create', [TransaksiController::class, 'create']);
    Route::get('/transaksi', [TransaksiController::class, 'index']);
    Route::get('/transaksi/sync', [TransaksiController::class, 'sync']);
    Route::get('/transaksi/{id}', [TransaksiController::class, 'show']);

    // Midtrans
    Route::post('/midtrans/create', [MidtransController::class, 'createTransaction']);
    Route::post('/midtrans/token', [MidtransController::class, 'getSnapToken']);
});

/*
|--------------------------------------------------------------------------
| MIDTRANS CALLBACK (NO AUTH)
|--------------------------------------------------------------------------
*/
Route::post('/transaksi/store', [TransaksiController::class, 'store']);
Route::post('/transaksi/callback', [TransaksiController::class, 'callback']);
