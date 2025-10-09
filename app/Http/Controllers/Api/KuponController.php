<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\KuponPengguna;
use Carbon\Carbon;

class KuponController extends Controller
{
    // Klaim kupon FREECLASS untuk akun pelanggan baru
    public function claim(Request $request) // sebelumnya claimKupon
    {
        $user = Auth::guard('api')->user();

        if ($user->created_at->lt(Carbon::now()->subDays(7))) {
            return response()->json([
                'message' => 'Kupon FREECLASS hanya untuk akun baru.'
            ], 403);
        }

        $sudahPunyaKupon = KuponPengguna::where('user_id', $user->id)
            ->where('kode_kupon', 'FREECLASS')
            ->exists();

        if ($sudahPunyaKupon) {
            return response()->json([
                'message' => 'Kamu sudah pernah klaim kupon FREECLASS.'
            ], 400);
        }

        $kupon = KuponPengguna::create([
            'user_id' => $user->id,
            'kode_kupon' => 'FREECLASS',
            'sudah_dipakai' => false,
            'berlaku_hingga' => now()->addDays(7),
        ]);

        return response()->json([
            'message' => 'Selamat! Kamu berhasil klaim kupon FREECLASS.',
            'kupon' => $kupon
        ], 201);
    }

    public function aktif(Request $request) // sebelumnya kupon
    {
        $user = Auth::guard('api')->user();

        $kupon = KuponPengguna::where('user_id', $user->id)
            ->orderBy('berlaku_hingga', 'desc')
            ->first();

        if (!$kupon) {
            return response()->json(['message' => 'Tidak ada kupon aktif', 'kupon' => null], 200);
        }

        if ($kupon->sudah_dipakai) {
            return response()->json([
                'message' => 'Kupon sudah dipakai',
                'kupon' => $kupon
            ], 200);
        }

        if ($kupon->berlaku_hingga < now()) {
            return response()->json([
                'message' => 'Kupon sudah kadaluarsa',
                'kupon' => null
            ], 200);
        }

        return response()->json([
            'message' => 'Kupon aktif ditemukan',
            'kupon' => $kupon
        ], 200);
    }

    public function pakai(Request $request)
    {
        $user = Auth::guard('api')->user();

        $kupon = KuponPengguna::where('user_id', $user->id)
            ->where('kode_kupon', 'FREECLASS')
            ->first();

        if (!$kupon) {
            return response()->json([
                'message' => 'Kupon tidak ditemukan.'
            ], 404);
        }

        if ($kupon->sudah_dipakai) {
            return response()->json([
                'message' => 'Kupon sudah dipakai.',
                'kupon' => $kupon
            ], 200);
        }

        $kupon->sudah_dipakai = true;
        $kupon->save();

        return response()->json([
            'message' => 'Kupon berhasil diklaim.',
            'kupon' => $kupon
        ], 200);
    }
}
