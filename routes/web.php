<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

// Halaman login (Blade)
Route::get('/login', function () {
    return view('auth/login');
})->name('admin.login');

// Proses login → menghasilkan JWT
Route::post('/api/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'Email atau password salah'], 422);
    }

    // Generate JWT token
    $token = JWTAuth::fromUser($user);

    return response()->json([
        'message' => 'Login berhasil',
        'token' => $token,
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email
        ]
    ]);
})->name('admin.login.post');

// Middleware JWT untuk route protected
Route::middleware(['jwt.auth'])->group(function () {
    Route::get('/api/home', function () {
        return response()->json([
            'message' => 'Selamat datang di home!',
            'data' => 'Ini data rahasia hanya untuk user login'
        ]);
    });
});

Route::middleware(['auth.session'])->group(function () {
    Route::get('/users/home', function () {
        return view('users.home');
    })->name('home');
});


// Logout JWT di frontend → hapus token di localStorage
// Tidak perlu route logout di backend jika pakai JWT stateless
