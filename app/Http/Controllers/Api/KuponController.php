<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\KuponPengguna;
use Carbon\Carbon;
use App\Helpers\KuponHelper;

class KuponController extends Controller
{
    /**
     * Lihat kupon user
     */
    public function index()
    {
        $user = Auth::guard('api')->user();

        $kupons = KuponPengguna::where('user_id', $user->id)->get();

        if ($kupons->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada kupon',
                'kupon' => [],
                'diskon_per_tipe' => KuponHelper::diskonPerTipe(),
            ], 200);
        }

        // Periksa expired dan buat field "valid" sementara untuk frontend
        $kupons->each(function ($k) {
            // tandai expired di memory
            if (Carbon::now()->gt($k->berlaku_hingga) && $k->status !== 'used') {
                $k->status = 'expired';
            }
            // properti tambahan untuk frontend
            $k->valid = ($k->status === 'pending' || $k->status === 'claimed') 
                        && Carbon::now()->lte($k->berlaku_hingga);
        });

        return response()->json([
            'kupon' => $kupons,
            'diskon_per_tipe' => KuponHelper::diskonPerTipe(),
        ], 200);
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

        if ($kupon->status !== 'pending') {
            return response()->json(['message' => 'Kupon sudah diklaim atau tidak tersedia'], 400);
        }

        $kupon->status = 'claimed';
        $kupon->save();

        return response()->json([
            'message' => 'Kupon berhasil diklaim',
            'kupon' => $kupon,
        ], 200);
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
            return response()->json([
                'message' => 'Kupon tidak ditemukan atau belum diklaim'
            ], 404);
        }

        // jika expired
        if (Carbon::now()->gt($kupon->berlaku_hingga)) {
            $kupon->status = 'expired';
            $kupon->save();

            return response()->json([
                'message' => 'Kupon sudah kadaluarsa'
            ], 400);
        }

        // tandai sebagai digunakan
        $kupon->status = 'used';
        $kupon->sudah_dipakai = true;
        $kupon->save();

        return response()->json([
            'message' => 'Kupon berhasil dipakai',
            'kupon' => $kupon
        ], 200);
    }
}
