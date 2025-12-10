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
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MidtransController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\Api\TokenPackageController;

// =====================
// 🔹 ROUTE API KELAS (PUBLIC)
// =====================
Route::apiResource('kelas', KelasController::class);

// =====================
// 🔹 ROUTE API RESERVASI
// =====================
Route::middleware(['jwt.auth', 'role:pelanggan'])
    ->apiResource('reservasi', ReservasiController::class);

Route::get('/harga', [ReservasiController::class, 'getHarga']);

// =====================
// 🔹 AUTH (LOGIN / REGISTER / GOOGLE LOGIN)
// =====================
Route::post('/login', [AuthController::class, 'login']);
Route::post('/google-login', [AuthController::class, 'googleLogin']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('jwt.auth');

// 🔹 REFRESH TOKEN
Route::middleware('jwt.refresh')->post('/refresh', [AuthController::class, 'refresh']);

// =====================
// 🔹 USER LOGIN DATA (JWT PROTECTED)
// =====================
Route::middleware(['jwt.auth', 'role:pelanggan'])->get('/user', function () {
    return auth()->user();
});

// =====================
// 🔹 ROUTE TRAINER (PUBLIC)
// =====================
Route::get('/users/trainer', [UserController::class, 'getTrainers']);

// =====================
// 🔹 KUPON FREECLASS
// =====================
Route::middleware(['jwt.auth', 'role:pelanggan'])->group(function () {
    Route::get('/kupon', [KuponController::class, 'index']);
    Route::post('/kupon/claim', [KuponController::class, 'claim']);
    Route::post('/kupon/pakai', [KuponController::class, 'pakai']);
});

// =====================
// 🔹 DISKON
// =====================
Route::apiResource('diskon', DiskonController::class);

// =====================
// 🔹 SCHEDULE
// =====================
Route::get('/schedule', [ScheduleApiController::class, 'index']);
Route::get('/schedule/{id}', [ScheduleApiController::class, 'show']);
Route::get('/trainer/schedule', [ScheduleApiController::class, 'byTrainer']);

// =====================
// 🔹 MEMBER (FITUR MEMBERSHIP) — BUTUH JWT
// =====================
Route::prefix('member')->middleware('jwt.auth')->group(function () {

    Route::post('/store', [MemberController::class, 'store']);
    Route::get('/kelas', [MemberController::class, 'kelasMember']);
    Route::post('/bayar', [MemberController::class, 'bayarDummy']);
    Route::post('/ikut-kelas', [MemberController::class, 'ikutKelas']);
    Route::get('/transaksi/sync', [TransaksiController::class, 'sync']);

    // MIDTRANS TOKEN & CREATE ORDER
    Route::post('/midtrans/create', [MidtransController::class, 'createTransaction']);
    Route::post('/midtrans/token', [MidtransController::class, 'getSnapToken']);

    // TRANSAKSI (PROTECTED)
    Route::post('/transaksi/create', [TransaksiController::class, 'create']);
    Route::get('/transaksi', [TransaksiController::class, 'index']);
    Route::get('/transaksi/{id}', [TransaksiController::class, 'show']);
});

// =====================
// ❗ MIDTRANS CALLBACK (TANPA AUTH!)
// =====================
Route::post('/transaksi/store', [TransaksiController::class, 'store']);
Route::post('/transaksi/callback', [TransaksiController::class, 'callback']);


// =====================
// TOKEN PACKAGES
// =====================
Route::apiResource('token-packages', TokenPackageController::class);
