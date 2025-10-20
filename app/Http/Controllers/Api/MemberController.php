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
    // 🔄 Auto Expire Member
    private function autoExpireMember()
    {
        Member::where('tanggal_berakhir', '<', now())
            ->where('status', 'aktif')
            ->update(['status' => 'nonaktif']);
    }

    // 1️⃣ Daftar member
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
            'tanggal_mulai' => now(),
            'tanggal_berakhir' => now()->addMonth(),
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Pendaftaran member berhasil, menunggu aktivasi',
            'member' => $member
        ]);
    }

    // 2️⃣ Aktivasi member
    public function aktivasi($member_id)
    {
        $this->autoExpireMember();

        $member = Member::find($member_id);
        if (!$member) return response()->json(['message' => 'Member tidak ditemukan'], 404);

        $member->update(['status' => 'aktif']);

        $kelasList = Kelas::where('tipe_kelas', $member->tipe_kelas)->get();
        foreach ($kelasList as $kelas) {
            $member->kelas()->syncWithoutDetaching([
                $kelas->id => [
                    'jumlah_token' => $member->maks_kelas,
                    'expired_at' => $member->tanggal_berakhir,
                ]
            ]);
        }

        return response()->json([
            'message' => 'Member diaktifkan dan token kelas diberikan',
            'member' => $member
        ]);
    }

    // 3️⃣ Lihat kelas member
    public function kelasMember()
    {
        $this->autoExpireMember();

        $user = JWTAuth::parseToken()->authenticate();

        $member = Member::with('kelas')->where('user_id', $user->id)->first();

        if (!$member) {
            // User belum member → kembalikan data default
            return response()->json([
                'id' => $user->id,
                'user_id' => $user->id,
                'status' => null,
                'kelas' => [],
            ]);
        }

        return response()->json([
            'id' => $member->id,
            'user_id' => $member->user_id,
            'status' => $member->status,
            'kelas' => $member->kelas
        ]);
    }

    // 4️⃣ Ikut kelas & kurangi token
    public function ikutKelas(Request $request)
    {
        $this->autoExpireMember();

        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'tipe_paket' => 'required|string',
        ]);

        $user = JWTAuth::parseToken()->authenticate();
        $kelasId = $request->kelas_id;
        $kelas = Kelas::find($kelasId);

        // User belum member atau nonaktif → hanya bisa ikut General
        if (!$user->member || $user->member->status !== 'aktif') {
            if ($request->tipe_paket !== 'General') {
                return response()->json([
                    'message' => 'Anda belum member aktif. Hanya bisa ikut paket General.'
                ], 403);
            }
        }

        // Member aktif → cek token & paket
        if ($user->member && $user->member->status === 'aktif' && $request->tipe_paket !== 'General') {
            $pivot = $user->member->kelas()->where('kelas_id', $kelasId)->first();
            if (!$pivot) {
                return response()->json([
                    'message' => 'Kelas ini tidak termasuk dalam paket membership Anda.'
                ], 400);
            }
            if ($pivot->pivot->jumlah_token <= 0) {
                return response()->json([
                    'message' => 'Kuota kelas Anda sudah habis. Silakan perpanjang membership.'
                ], 400);
            }

            // Kurangi token
            $user->member->kelas()->updateExistingPivot($kelasId, [
                'jumlah_token' => $pivot->pivot->jumlah_token - 1
            ]);
        }

        return response()->json([
            'message' => 'Berhasil ikut kelas',
        ]);
    }
}
