<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\Api\AuthController;

Route::get('/kelas', [KelasController::class, 'apiIndex']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('users', UserController::class);

// Login API tanpa prefix admin
Route::post('/login', [AuthController::class, 'login']);
