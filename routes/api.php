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
use App\Http\Controllers\Api\MidtransCallbackController;
use App\Http\Controllers\Api\QrCodeController;
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

/*
|--------------------------------------------------------------------------
| USER PROFILE
|--------------------------------------------------------------------------
*/
Route::middleware('jwt.auth')->get('/me', fn () => auth()->user());
Route::middleware('jwt.auth')->get('/user', fn () => auth()->user());

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
| CHECKOUT RESERVASI (FINAL – SATU PINTU)
|--------------------------------------------------------------------------
*/
Route::middleware(['jwt.auth', 'role:pelanggan'])->group(function () {
    Route::post('/checkout/price', [CheckoutController::class, 'price']);
    Route::post('/checkout/reservasi', [CheckoutController::class, 'checkoutReservasi']);
});

Route::middleware('jwt.auth')->get(
    '/transaksi/kode/{kode}',
    [TransaksiController::class, 'showByKode']
);


/*
|--------------------------------------------------------------------------
| VOUCHER
|--------------------------------------------------------------------------
*/
Route::get('/vouchers', [VoucherController::class, 'index']);

Route::middleware('jwt.auth')->group(function () {
    Route::get('/vouchers/my', [VoucherController::class, 'userVouchers']);
    Route::post('/vouchers/claim', [VoucherController::class, 'claim']);
    Route::middleware('jwt.auth')->get('/vouchers', [VoucherController::class, 'index']);

});



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
    Route::post('/transaksi/create', [TransaksiController::class, 'create']);
    Route::get('/transaksi', [TransaksiController::class, 'index']);
    Route::get('/transaksi/{id}', [TransaksiController::class, 'show']);

    Route::post('/midtrans/create', [MidtransController::class, 'createTransaction']);
    Route::post('/midtrans/token', [MidtransController::class, 'getSnapToken']);
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
