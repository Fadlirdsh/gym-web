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

// 🔹 REFRESH TOKEN (AGAR USER TETAP LOGIN)
// menggunakan middleware bawaan JWT
Route::middleware('jwt.refresh')->post('/refresh', [AuthController::class, 'refresh']);

// =====================
// 🔹 AMBIL DATA USER LOGIN (JWT PROTECTED)
// =====================
Route::middleware(['jwt.auth', 'role:pelanggan'])->get('/user', function () {
    return auth()->user();   // JWT harus pakai auth()
});

// =====================
// 🔹 KUPON FREECLASS
// =====================
Route::middleware(['jwt.auth', 'role:pelanggan'])->group(function () {
    Route::get('/kupon', [KuponController::class, 'aktif']);
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
Route::get('/schedules', [ScheduleApiController::class, 'index']);
Route::get('/schedules/{id}', [ScheduleApiController::class, 'show']);

// =====================
// 🔹 MEMBER (FITUR MEMBERSHIP)
// =====================
Route::prefix('member')->middleware('jwt.auth')->group(function () {
    Route::post('/', [MemberController::class, 'store']);
    Route::put('/aktivasi/{member_id}', [MemberController::class, 'aktivasi']);
    Route::get('/kelas/{user_id}', [MemberController::class, 'kelasMember']);
    Route::post('/ikut-kelas', [MemberController::class, 'ikutKelas']);
});
