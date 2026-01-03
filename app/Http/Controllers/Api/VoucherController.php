<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voucher;
use App\Models\UserVoucher;

class VoucherController extends Controller
{
    // ============================
    // LIST SEMUA VOUCHER (PUBLIC)
    // ============================
    public function index(Request $request)
    {
        $today = now()->toDateString();
        $userId = $request->user()?->id; // null kalau belum login

        $vouchers = Voucher::where('status', 'aktif')
            ->whereDate('tanggal_mulai', '<=', $today)
            ->whereDate('tanggal_akhir', '>=', $today)
            ->get()
            ->map(function ($voucher) use ($userId) {

                // default
                $voucher->is_claimed = false;

                if ($userId) {
                    $voucher->is_claimed = UserVoucher::where('user_id', $userId)
                        ->where('voucher_id', $voucher->id)
                        ->where('status', 'claimed')
                        ->exists();
                }

                return $voucher;
            });

        return response()->json($vouchers);
    }


    // ============================
    // LIST VOUCHER MILIK USER (🔥 FIX DI SINI)
    // ============================
    public function userVouchers(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $isMember = $user->member()->exists();

        $vouchers = $user->vouchers()
            ->wherePivot('status', 'claimed')
            ->whereDate('tanggal_akhir', '>=', now())
            ->where(function ($q) use ($isMember) {
                $q->where('role_target', 'semua');

                if ($isMember) {
                    $q->orWhere('role_target', 'member');
                } else {
                    $q->orWhere('role_target', 'pelanggan');
                }
            })
            ->get([
                'user_vouchers.id as id',      // 🔥 INI KUNCI NYA
                'vouchers.kode',
                'vouchers.diskon_persen',
                'vouchers.kelas_id',
                'vouchers.tanggal_akhir',
            ]);

        return response()->json($vouchers);
    }

    // ============================
    // CLAIM VOUCHER
    // ============================
    public function claim(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'voucher_id' => 'required|exists:vouchers,id'
        ]);

        $voucher = Voucher::findOrFail($request->voucher_id);

        if ($voucher->status !== 'aktif') {
            return response()->json(['message' => 'Voucher tidak aktif'], 400);
        }

        if (now()->lt($voucher->tanggal_mulai) || now()->gt($voucher->tanggal_akhir)) {
            return response()->json(['message' => 'Voucher sudah kadaluarsa'], 400);
        }

        if ($voucher->kuota <= 0) {
            return response()->json(['message' => 'Kuota voucher habis'], 400);
        }

        $alreadyClaimed = UserVoucher::where('user_id', $user->id)
            ->where('voucher_id', $voucher->id)
            ->exists();

        if ($alreadyClaimed) {
            return response()->json(['message' => 'Voucher sudah diklaim'], 400);
        }

        UserVoucher::create([
            'user_id'    => $user->id,
            'voucher_id' => $voucher->id,
            'status'     => 'claimed',
        ]);

        return response()->json([
            'message' => 'Voucher berhasil diklaim'
        ]);
    }
}
