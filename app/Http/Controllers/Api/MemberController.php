<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Member;
use App\Models\Kelas;
use Tymon\JWTAuth\Facades\JWTAuth;

class MemberController extends Controller
{
    /**
     * 🔄 Auto expire member:
     * - Jika tanggal berakhir lewat → nonaktif
     * - Jika token habis → nonaktif
     */
    private function autoExpireMember()
    {
        // 1. Tanggal berakhir lewat
        Member::where('tanggal_berakhir', '<', now())
            ->where('status', 'aktif')
            ->update(['status' => 'nonaktif']);

        // 2. Token habis
        Member::where('token_sisa', '<=', 0)
            ->where('status', 'aktif')
            ->update(['status' => 'nonaktif']);
    }

    // 1️⃣ Daftar member (pending dulu)
    public function store(Request $request)
    {
        $this->autoExpireMember();

        $user = JWTAuth::parseToken()->authenticate();

        $request->validate([
            'maks_kelas' => 'required|in:3,5,10,20',
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
            'maks_kelas' => $request->maks_kelas,
            'tipe_kelas' => $request->tipe_kelas,
            'harga' => $request->harga,
            'token_total' => $request->maks_kelas,
            'token_terpakai' => 0,
            'token_sisa' => $request->maks_kelas,
            'tanggal_mulai' => now(),
            'tanggal_berakhir' => now()->addMonth(),
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Pendaftaran member berhasil, menunggu aktivasi',
            'member' => $member
        ]);
    }

    // 2️⃣ Aktivasi member (manual)
    public function aktivasi($member_id)
    {
        $this->autoExpireMember();

        $member = Member::find($member_id);
        if (!$member) return response()->json(['message' => 'Member tidak ditemukan'], 404);

        $member->update(['status' => 'aktif']);

        return response()->json([
            'message' => 'Member diaktifkan',
            'member' => $member
        ]);
    }

    // 3️⃣ Lihat kelas & token member
    public function kelasMember()
    {
        $this->autoExpireMember();

        $user = JWTAuth::parseToken()->authenticate();

        $member = Member::where('user_id', $user->id)->first();

        if (!$member) {
            return response()->json([
                'id' => $user->id,
                'user_id' => $user->id,
                'status' => null,
                'token_total' => 0,
                'token_terpakai' => 0,
                'token_sisa' => 0,
                'tipe_kelas' => null,
            ]);
        }

        // Ambil kelas sesuai tipe
        $kelasList = Kelas::where('tipe_kelas', $member->tipe_kelas)->get();

        return response()->json([
            'id' => $member->id,
            'user_id' => $member->user_id,
            'status' => $member->status,
            'token_total' => $member->token_total,
            'token_terpakai' => $member->token_terpakai,
            'token_sisa' => $member->token_sisa,
            'tipe_kelas' => $member->tipe_kelas,
            'kelas' => $kelasList
        ]);
    }

    // 4️⃣ Ikut kelas & kurangi token → auto nonaktif jika habis
    public function ikutKelas(Request $request)
    {
        $this->autoExpireMember();

        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        $user = JWTAuth::parseToken()->authenticate();
        $kelas = Kelas::find($request->kelas_id);

        $member = Member::where('user_id', $user->id)
            ->where('status', 'aktif')
            ->first();

        // Belum member → hanya General
        if (!$member) {
            return response()->json([
                'message' => 'Anda belum memiliki membership aktif. Hanya bisa ikut paket General.'
            ], 403);
        }

        // Cocokkan tipe kelas
        if ($kelas->tipe_kelas !== $member->tipe_kelas) {
            return response()->json([
                'message' => "Kelas ini tidak termasuk paket membership Anda ({$member->tipe_kelas})."
            ], 403);
        }

        // Cek token
        if ($member->token_sisa <= 0) {
            $member->update(['status' => 'nonaktif']);
            return response()->json([
                'message' => 'Token habis. Membership otomatis nonaktif.'
            ], 403);
        }

        // Kurangi token
        $member->token_terpakai += 1;
        $member->token_sisa -= 1;

        // Kalau token habis → langsung nonaktif
        if ($member->token_sisa <= 0) {
            $member->status = 'nonaktif';
        }

        $member->save();

        return response()->json([
            'message' => 'Berhasil ikut kelas',
            'token_sisa' => $member->token_sisa,
            'status_member' => $member->status
        ]);
    }

    // 5️⃣ Bayar dummy (untuk testing)
    public function bayarDummy(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $member = Member::where('user_id', $user->id)->first();

        if (!$member) {
            return response()->json(['message' => 'User belum punya member'], 404);
        }

        if ($member->status === 'aktif') {
            return response()->json(['message' => 'Member sudah aktif'], 400);
        }

        $member->update([
            'status' => 'aktif',
            'tanggal_mulai' => now(),
            'tanggal_berakhir' => now()->addMonth(),
            'token_total' => $member->maks_kelas,
            'token_terpakai' => 0,
            'token_sisa' => $member->maks_kelas,
        ]);

        return response()->json([
            'message' => 'Pembayaran dummy sukses, member aktif!',
            'member' => $member
        ]);
    }
}
