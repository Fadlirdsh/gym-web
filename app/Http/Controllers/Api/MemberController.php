<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\MemberToken;
use App\Models\Kelas;
use App\Models\Reservasi;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;

class MemberController extends Controller
{
    /**
     * =========================================
     * AUTO EXPIRE MEMBER (FIX DATETIME BUG)
     * =========================================
     */
    private function autoExpireMember()
    {
        Member::where('status', 'aktif')
            ->whereNotNull('tanggal_berakhir')
            ->whereDate('tanggal_berakhir', '<', now()->toDateString())
            ->update(['status' => 'nonaktif']);
    }

    /**
     * ==========================
     * CEK STATUS MEMBER (API)
     * ==========================
     */
    public function status()
{
    $this->autoExpireMember();

    $user = JWTAuth::parseToken()->authenticate();

    $member = Member::where('user_id', $user->id)->first();

    return response()->json([
        'is_member' => (bool) $member,
        'status' => $member?->status, // aktif | pending | nonaktif | null
        'expired_at' => $member?->tanggal_berakhir,
    ]);
}


    /**
     * ==========================
     * IKUT KELAS
     * ==========================
     */
    public function ikutKelas(Request $request)
    {
        $this->autoExpireMember();

        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'date'     => 'required|date',
            'time'     => 'required|string',
        ]);

        $user  = JWTAuth::parseToken()->authenticate();
        $kelas = Kelas::findOrFail($request->kelas_id);

        // ✅ PAKAI SCOPE aktif()
        $member = Member::where('user_id', $user->id)
            ->aktif()
            ->first();

        /**
         * ===============================
         * CASE 1: MEMBER AKTIF + TOKEN ADA
         * ===============================
         */
        if ($member) {
            $token = MemberToken::where('member_id', $member->id)
                ->where('tipe_kelas', $kelas->tipe_kelas)
                ->where('token_sisa', '>', 0)
                ->first();

            if ($token) {
                $token->increment('token_terpakai');
                $token->decrement('token_sisa');

                $reservasi = Reservasi::create([
                    'pelanggan_id' => $user->id,
                    'trainer_id'   => $kelas->trainer_id ?? 1,
                    'kelas_id'     => $kelas->id,
                    'jadwal'       => Carbon::parse($request->date . ' ' . $request->time),
                    'status'       => 'paid',
                    'status_hadir' => 'belum_hadir',
                ]);

                return response()->json([
                    'message' => 'Berhasil booking menggunakan token',
                    'reservasi_id' => $reservasi->id,
                    'paid' => true,
                ]);
            }
        }

        /**
         * ===============================
         * CASE 2: NON MEMBER / TOKEN HABIS
         * ===============================
         */
        return response()->json([
            'message' => 'Token tidak tersedia. Lanjutkan pembayaran.',
            'use_midtrans' => true,
            'kelas_id' => $kelas->id,
        ]);
    }
}
