<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\KelasController;

/*
|--------------------------------------------------------------------------
| Redirect ke login
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => redirect()->route('admin.login'));

/*
|--------------------------------------------------------------------------
| Auth (Login)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {
    // Halaman login
    Route::get('/login', fn() => view('auth.login'))->name('admin.login');

    // Proses login (JWT)
    Route::post('/api/login', function (Request $request) {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Email atau password salah'], 422);
        }

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Login berhasil',
            'token'   => $token,
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
        ]);
    })->name('admin.login.post');
});

/*
|--------------------------------------------------------------------------
| API (JWT protected)
|--------------------------------------------------------------------------
*/
Route::prefix('api')->middleware('jwt.auth')->group(function () {
    Route::get('/home', function () {
        return response()->json([
            'message' => 'Selamat datang di home!',
            'data'    => 'Ini data rahasia hanya untuk user login',
        ]);
    });
});

/*
|--------------------------------------------------------------------------
| Dashboard (session protected)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth.session'])->group(function () {
    // Halaman home (URL: /users/home)
    Route::get('/users/home', function () {
        return view('users.home');
    })->name('home');

    // Resource Kelas
    Route::prefix('users')->group(function () {
        Route::resource('kelas', KelasController::class)->parameters([
            'kelas' => 'kelas'
        ]);
    });
});
