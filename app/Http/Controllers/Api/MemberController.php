<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\Kelas;
use App\Models\Reservasi;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;

class MemberController extends Controller
{
    /**
     * Auto expire member (BERDASARKAN TANGGAL SAJA)
     */
    private function autoExpireMember()
    {
        Member::where('tanggal_berakhir', '<', now())
            ->where('status', 'aktif')
            ->update(['status' => 'nonaktif']);
    }

    /**
     * Daftar member (pending)
     */
    public function store(Request $request)
    {
        $this->autoExpireMember();
        $user = JWTAuth::parseToken()->authenticate();

        $request->validate([
            'tipe_kelas' => 'required|in:Pilates Group,Pilates Private,Yoga Group,Yoga Private',
            'harga' => 'required|integer|min:0',
        ]);

        if ($user->role !== 'pelanggan') {
            return response()->json(['message' => 'User bukan pelanggan'], 400);
        }

        if (Member::where('user_id', $user->id)->exists()) {
            return response()->json(['message' => 'User sudah terdaftar sebagai member'], 400);
        }

        $member = Member::create([
            'user_id' => $user->id,
            'tipe_kelas' => $request->tipe_kelas,
            'harga' => $request->harga,
            'token_total' => 0,
            'token_terpakai' => 0,
            'token_sisa' => 0,
            'tanggal_mulai' => null,
            'tanggal_berakhir' => null,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Pendaftaran member berhasil, menunggu pembayaran',
            'member' => $member
        ]);
    }

    /**
     * Ikut kelas
     * - Token ada → langsung pakai token
     * - Token habis → PAY PER CLASS (MIDTRANS)
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

        $member = Member::where('user_id', $user->id)
            ->where('status', 'aktif')
            ->first();

        /**
         * ===============================
         * CASE 1: MEMBER + TOKEN ADA
         * ===============================
         */
        if ($member && $member->token_sisa > 0) {
            $member->update([
                'token_terpakai' => $member->token_terpakai + 1,
                'token_sisa'     => $member->token_sisa - 1,
            ]);

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
                'token_sisa' => $member->token_sisa,
                'paid' => true
            ]);
        }

        /**
         * ===============================
         * CASE 2: TOKEN HABIS / NON MEMBER
         * → PAY PER CLASS (MIDTRANS)
         * ===============================
         */
        return response()->json([
            'message' => 'Token habis atau bukan member. Lanjutkan pembayaran.',
            'use_midtrans' => true,
            'kelas_id' => $kelas->id
        ], 200);
    }

    /**
     * Simulasi pembayaran (dummy)
     */
    public function bayarDummy()
    {
        $user = JWTAuth::parseToken()->authenticate();
        $member = Member::where('user_id', $user->id)->first();

        if (!$member) {
            return response()->json(['message' => 'Member tidak ditemukan'], 404);
        }

        if ($member->status === 'aktif') {
            return response()->json(['message' => 'Member sudah aktif'], 400);
        }

        $member->update([
            'status' => 'aktif',
            'tanggal_mulai' => now(),
            'tanggal_berakhir' => now()->addMonth(),
        ]);

        return response()->json([
            'message' => 'Simulasi pembayaran berhasil, member aktif',
            'member' => $member
        ]);
    }
}
