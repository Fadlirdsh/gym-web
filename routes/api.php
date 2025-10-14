<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReservasiController;
use App\Http\Controllers\Api\KelasController;
use App\Http\Controllers\Api\KuponController;
use App\Http\Controllers\Api\MemberController;


// =====================
// 🔹 ROUTE API KELAS
// =====================
Route::apiResource('kelas', KelasController::class);

// =====================
// 🔹 ROUTE API RESERVASI
// =====================
Route::middleware(['jwt.auth', 'role:pelanggan'])->apiResource('reservasi', ReservasiController::class);

// =====================
// 🔹 ROUTE API USER
// =====================
Route::apiResource('users', UserController::class);
Route::get('/pelanggan', [UserController::class, 'pelanggan']);

// =====================
// 🔹 AUTH (LOGIN / REGISTER / GOOGLE LOGIN)
// =====================
Route::post('/login', [AuthController::class, 'login']);
Route::post('/google-login', [AuthController::class, 'googleLogin']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('jwt.auth');

// =====================
// 🔹 AMBIL DATA USER LOGIN (JWT)
// =====================
Route::middleware(['jwt.auth', 'role:pelanggan'])->get('/user', function (Request $request) {
    return $request->user();
});

// =====================
// 🔹 KUPON FREECLASS
// =====================
Route::middleware(['jwt.auth', 'role:pelanggan'])->group(function () {
    Route::get('/kupon', [KuponController::class, 'aktif']);   // ambil kupon aktif
    Route::post('/kupon/claim', [KuponController::class, 'claim']); // klaim kupon baru
    Route::post('/kupon/pakai', [KuponController::class, 'pakai']); // pakai kupon
});

// =====================
// 🔹 MEMBER 
// =====================
Route::apiResource('member', MemberController::class)->only(['store']);

// Route custom
Route::post('/member/ikut-kelas', [MemberController::class, 'ikutKelas']);
Route::post('/member/aktivasi/{member_id}', [MemberController::class, 'aktivasi']);
Route::get('/member/kelas/{user_id}', [MemberController::class, 'kelasMember']);
