<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReservasiController;
use App\Http\Controllers\Api\KelasController;

// Route API untuk Kelas
Route::apiResource('kelas', KelasController::class);

// Route API untuk Reservasi
Route::apiResource('reservasi', ReservasiController::class);

// Route API untuk User
Route::apiResource('users', UserController::class);
Route::get('/pelanggan', [UserController::class, 'pelanggan']);

// Login API (tanpa prefix admin)
Route::post('/login', [AuthController::class, 'login']);

// Jika butuh auth user
// Route::middleware('auth:sanctum')->get('/user', fn (Request $request) => $request->user());

Route::middleware(['jwt.auth', 'role:pelanggan'])->get('/user', function (Request $request) {
    return $request->user();
});
