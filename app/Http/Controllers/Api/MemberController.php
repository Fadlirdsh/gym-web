<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Member;
use App\Models\Kelas;

class MemberController extends Controller
{
    // =========================
    // 1️⃣ Daftar member
    // =========================
    public function store(Request $request)
    {
        $user = User::find($request->user_id);

        if (!$user || $user->role !== 'pelanggan') {
            return response()->json(['message' => 'User tidak valid atau bukan pelanggan'], 400);
        }

        if ($user->member) {
            return response()->json(['message' => 'User sudah terdaftar sebagai member'], 400);
        }

        $member = Member::create([
            'user_id' => $user->id,
            'tanggal_mulai' => now(),
            'tanggal_berakhir' => now()->addMonth(),
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Pendaftaran member berhasil, menunggu aktivasi',
            'member' => $member
        ]);
    }

    // =========================
    // 2️⃣ Aktivasi member
    // =========================
    public function aktivasi($member_id)
    {
        $member = Member::find($member_id);
        if (!$member) return response()->json(['message' => 'Member tidak ditemukan'], 404);

        $member->update(['status' => 'aktif']);

        $kelasSemua = Kelas::all();
        foreach ($kelasSemua as $kelas) {
            $member->kelas()->attach($kelas->id, [
                'jumlah_token' => $kelas->jumlah_token,
                'expired_at' => $kelas->expired_at,
            ]);
        }

        return response()->json([
            'message' => 'Member diaktifkan dan token kelas sudah diberikan',
            'member' => $member
        ]);
    }

    // =========================
    // 3️⃣ Lihat kelas member
    // =========================
    public function kelasMember($user_id)
    {
        $user = User::with('member.kelas')->find($user_id);

        if (!$user || !$user->member) {
            return response()->json(['message' => 'Member tidak ditemukan'], 404);
        }

        return response()->json([
            'member' => $user->member,
            'kelas' => $user->member->kelas
        ]);
    }

    // =========================
    // 4️⃣ Ikut kelas & kurangi token
    // =========================
    public function ikutKelas(Request $request)
    {
        $user = User::find($request->user_id);
        $kelasId = $request->kelas_id;

        if (!$user || !$user->member) {
            return response()->json(['message' => 'Member tidak ditemukan'], 404);
        }

        $member = $user->member;
        $pivot = $member->kelas()->where('kelas_id', $kelasId)->first();

        if (!$pivot) return response()->json(['message' => 'Member tidak memiliki akses ke kelas ini'], 400);
        if ($pivot->pivot->jumlah_token <= 0) return response()->json(['message' => 'Token habis'], 400);

        $member->kelas()->updateExistingPivot($kelasId, [
            'jumlah_token' => $pivot->pivot->jumlah_token - 1
        ]);

        return response()->json([
            'message' => 'Berhasil ikut kelas, token dikurangi 1',
            'sisa_token' => $pivot->pivot->jumlah_token - 1
        ]);
    }
}
