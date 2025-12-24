<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Google\Client as GoogleClient;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // 🔹 Ambil flag remember dari frontend
        $remember = $request->boolean('remember');

        // 🔹 Tentukan TTL (MENIT)
        $ttl = $remember
            ? 10080   // 7 hari
            : 120;    // 2 jam

        // 🔹 SET TTL SEBELUM TOKEN DIBUAT
        JWTAuth::factory()->setTTL($ttl);

        // 🔹 Attempt login (token dibuat DI SINI)
        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['error' => 'Email atau password salah'], 401);
        }

        $user = Auth::guard('api')->user();

        // 🔹 Cek role
        if (!in_array($user->role, ['pelanggan', 'trainer'])) {
            return response()->json(['error' => 'Anda tidak memiliki akses'], 403);
        }

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => $ttl * 60,
            'user'         => $user,
        ]);
    }

    public function googleLogin(Request $request)
    {
        $token = $request->input('token');

        $client = new GoogleClient(['client_id' => env('GOOGLE_CLIENT_ID')]);
        $payload = $client->verifyIdToken($token);

        if (!$payload) {
            return response()->json(['error' => 'Token Google tidak valid'], 401);
        }

        $googleId = $payload['sub'];
        $email = $payload['email'];
        $name = $payload['name'] ?? 'User Google';

        $user = User::where('google_id', $googleId)
            ->orWhere('email', $email)
            ->first();

        if (!$user) {
            $user = User::create([
                'google_id' => $googleId,
                'email' => $email,
                'name' => $name,
                'password' => bcrypt(Str::random(16)),
                'role' => 'pelanggan',
            ]);
        }

        $token = Auth::guard('api')->login($user);

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'user' => $user,
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed', // 🔹 'confirmed' otomatis cek password_confirmation
            'phone' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'pelanggan',
            'phone' => $request->phone,
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'Registrasi pelanggan berhasil.',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'user' => $user
        ], 201);
    }

    public function logout(Request $request)
    {
        try {
            // Blacklist token agar tidak bisa dipakai lagi
            JWTAuth::invalidate(JWTAuth::getToken());
            return response()->json(['message' => 'Logout berhasil, token telah diblacklist']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal logout atau token tidak valid'], 400);
        }
    }
}
