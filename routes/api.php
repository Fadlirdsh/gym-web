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
use App\Http\Controllers\Api\TrainerShiftController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\Api\TokenPackageController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\TrainerProfileController;
use App\Http\Controllers\Api\VoucherController;
use App\Http\Controllers\Api\MidtransCallbackController;
use App\Http\Controllers\Api\QrCodeController;
use App\Http\Controllers\Api\MemberTokenController;
use App\Http\Controllers\Api\AttendanceController;

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
Route::middleware('auth:api')->get('/me', [AuthController::class, 'me']);

/*
|--------------------------------------------------------------------------
| USER PROFILE
|--------------------------------------------------------------------------
*/
Route::middleware('jwt.auth')->get('/me', fn() => auth()->user());
Route::middleware('jwt.auth')->get('/user', fn() => auth()->user());

/*
|--------------------------------------------------------------------------

| MEMBER (JWT – PELANGGAN) ✅ TAMBAHAN FIX
|--------------------------------------------------------------------------
*/
Route::middleware(['jwt.auth', 'role:pelanggan'])->group(function () {
    Route::get('/member/status', [MemberController::class, 'status']);
});

/*
|--------------------------------------------------------------------------

| KELAS (PUBLIC)
|--------------------------------------------------------------------------
*/
Route::get('/kelas', [KelasController::class, 'index']);
Route::get('/kelas/{id}', [KelasController::class, 'show']);
Route::get('/kelas/{id}/available-days', [KelasController::class, 'availableDays']);

/*
|--------------------------------------------------------------------------

| TRAINER (PUBLIC)
|--------------------------------------------------------------------------
*/
Route::get('/users/trainer', [UserController::class, 'getTrainers']);

/*
|--------------------------------------------------------------------------

| SCHEDULE
|--------------------------------------------------------------------------
*/
Route::get('/schedule', [ScheduleApiController::class, 'index']);
Route::get('/schedule/{id}', [ScheduleApiController::class, 'show']);
Route::get('/schedules/by-trainer', [ScheduleApiController::class, 'byTrainer']);
Route::get('/schedules/available', [ScheduleApiController::class, 'available']);

Route::middleware(['jwt.auth', 'role:trainer'])
    ->get('/trainer/schedule', [ScheduleApiController::class, 'byTrainer']);

/*
|--------------------------------------------------------------------------

| RESERVASI (JWT – PELANGGAN)
|--------------------------------------------------------------------------
*/
Route::middleware(['jwt.auth', 'role:pelanggan'])
    ->apiResource('reservasi', ReservasiController::class);

/*
|--------------------------------------------------------------------------

| CHECKOUT (FINAL – SATU PINTU)
|--------------------------------------------------------------------------
*/
Route::middleware(['jwt.auth', 'role:pelanggan'])->group(function () {

    Route::post('/checkout/member', [CheckoutController::class, 'checkoutMember']);
    Route::post('/checkout/token', [CheckoutController::class, 'checkoutToken']);
    Route::post('/checkout/reservasi', [CheckoutController::class, 'checkoutReservasi']);
});

/*
|--------------------------------------------------------------------------

| TOKEN PACKAGES (PUBLIC)
|--------------------------------------------------------------------------
*/
Route::get('/token-packages', [TokenPackageController::class, 'index']);

/*
|--------------------------------------------------------------------------

| TRANSAKSI (JWT)
|--------------------------------------------------------------------------
*/
Route::middleware('jwt.auth')->group(function () {
    Route::get('/transaksi', [TransaksiController::class, 'index']);
    Route::get('/transaksi/{id}', [TransaksiController::class, 'show']);
    Route::get('/transaksi/kode/{kode}', [TransaksiController::class, 'showByKode']);
});

/*
|--------------------------------------------------------------------------

| VOUCHER
|--------------------------------------------------------------------------
*/
Route::get('/vouchers', [VoucherController::class, 'index']);

Route::middleware('jwt.auth')->group(function () {
    Route::get('/vouchers/my', [VoucherController::class, 'userVouchers']);
    Route::post('/vouchers/claim', [VoucherController::class, 'claim']);
});

/*
|--------------------------------------------------------------------------

| MIDTRANS CALLBACK (NO AUTH – FINAL)
|--------------------------------------------------------------------------
*/
Route::post('/midtrans/callback', [MidtransCallbackController::class, 'handle']);

/*
|--------------------------------------------------------------------------

| ABSENSI – PELANGGAN
|--------------------------------------------------------------------------
*/
Route::middleware(['jwt.auth', 'role:pelanggan'])->group(function () {
    Route::get('/attendance/today', [AttendanceController::class, 'today']);
    Route::get('/attendance/qr/{reservasi_id}', [QrCodeController::class, 'show']);
});

/*
|--------------------------------------------------------------------------

| ABSENSI – ADMIN / TRAINER
|--------------------------------------------------------------------------
*/
Route::middleware(['jwt.auth', 'role:admin,trainer'])->group(function () {
    Route::post('/attendance/scan', [AttendanceController::class, 'scan']);
});

/*
|--------------------------------------------------------------------------
| SHIFT
|--------------------------------------------------------------------------
*/

Route::middleware('auth:api')->group(function () {
    Route::get('/trainer/shifts', [TrainerShiftController::class, 'index']);
});

/*
|--------------------------------------------------------------------------
| Member Token (PUBLIC)
|--------------------------------------------------------------------------
*/
Route::middleware(['jwt.auth', 'role:pelanggan'])->group(function () {
    Route::get('/member/token', [MemberTokenController::class, 'tokenSisa']);
});

/*
|--------------------------------------------------------------------------
| Profile Trainer
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:api'])->group(function () {
    Route::get('/trainer/profile', [TrainerProfileController::class, 'show']);
    Route::post('/trainer/profile', [TrainerProfileController::class, 'store']);
});
