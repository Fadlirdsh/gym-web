<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\FirstTimeDiscount;
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

        return response()->json(
            $this->issueToken($user, $remember)
        );
    }

    public function googleLogin(Request $request)
    {
        $request->validate([
            'token'    => 'required|string',
            'remember' => 'boolean'
        ]);

        $client = new GoogleClient([
            'client_id' => env('GOOGLE_CLIENT_ID')
        ]);

        $payload = $client->verifyIdToken($request->token);

        if (!$payload) {
            return response()->json(['error' => 'Token Google tidak valid'], 401);
        }

        $googleId = $payload['sub'];
        $email    = $payload['email'];
        $name     = $payload['name'] ?? 'User Google';

        $user = User::where('google_id', $googleId)
            ->orWhere('email', $email)
            ->first();

        if (!$user) {
            $user = User::create([
                'google_id' => $googleId,
                'email'     => $email,
                'name'      => $name,
                'password'  => bcrypt(Str::random(32)),
                'role'      => 'pelanggan',
            ]);
        } else {
            // sinkronisasi google_id kalau login via email sebelumnya
            if (!$user->google_id) {
                $user->google_id = $googleId;
                $user->save();
            }
        }

        return response()->json(
            $this->issueToken($user, $request->boolean('remember'))
        );
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
        // AUTO ASSIGN FIRST TIME DISCOUNT
        FirstTimeDiscount::create([
            'user_id'    => $user->id,
            'expired_at' => now()->addDays(7),
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

    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
            'role'  => $user->role,
        ]);
    }

    private function issueToken(User $user, bool $remember = false)
    {
        // TTL dalam menit
        $ttl = $remember
            ? 1440    // 24 jam (AMAN untuk mobile tahap awal)
            : 120;    // 2 jam

        JWTAuth::factory()->setTTL($ttl);

        $token = Auth::guard('api')->login($user);

        return [
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => $ttl * 60,
            'user'         => $user,
        ];
    }
}
