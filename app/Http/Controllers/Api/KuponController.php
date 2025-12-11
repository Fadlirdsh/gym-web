<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\KuponPengguna;
use Carbon\Carbon;

class KuponController extends Controller
{
    /**
     * Lihat kupon user
     */
    public function index()
    {
        $user = Auth::guard('api')->user();

        $kupon = KuponPengguna::where('user_id', $user->id)->first();

        if (!$kupon) {
            return response()->json(['message' => 'Tidak ada kupon', 'kupon' => null], 200);
        }

        // cek expired
        if (Carbon::now()->gt($kupon->berlaku_hingga) && $kupon->status != 'used') {
            $kupon->status = 'expired';
            $kupon->save();
        }

        return response()->json(['kupon' => $kupon], 200);
    }

    /**
     * Klaim kupon user
     */
    public function claim()
    {
        $user = Auth::guard('api')->user();

        $kupon = KuponPengguna::where('user_id', $user->id)->first();

        if (!$kupon) {
            return response()->json(['message' => 'Kupon tidak tersedia'], 404);
        }

        // cek expired
        if (Carbon::now()->gt($kupon->berlaku_hingga)) {
            $kupon->status = 'expired';
            $kupon->save();
            return response()->json(['message' => 'Kupon sudah kadaluarsa'], 400);
        }

        if ($kupon->status != 'pending') {
            return response()->json(['message' => 'Kupon sudah diklaim atau tidak tersedia'], 400);
        }

        $kupon->status = 'claimed';
        $kupon->save();

        return response()->json(['message' => 'Kupon berhasil diklaim', 'kupon' => $kupon], 200);
    }

    /**
     * Pakai kupon
     */
    public function pakai()
    {
        $user = Auth::guard('api')->user();

        $kupon = KuponPengguna::where('user_id', $user->id)
            ->where('status', 'claimed')
            ->first();

        if (!$kupon) {
            return response()->json(['message' => 'Kupon tidak ditemukan atau belum diklaim'], 404);
        }

        if (Carbon::now()->gt($kupon->berlaku_hingga)) {
            $kupon->status = 'expired';
            $kupon->save();
            return response()->json(['message' => 'Kupon sudah kadaluarsa'], 400);
        }

        $kupon->status = 'used';
        $kupon->sudah_dipakai = true;
        $kupon->save();

        return response()->json(['message' => 'Kupon berhasil dipakai', 'kupon' => $kupon], 200);
    }
}
