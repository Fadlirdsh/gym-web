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
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'maks_kelas' => 'required|in:3,5,10,20',
            'tipe_kelas' => 'required|in:Pilates Group,Pilates Private,Yoga Group,Yoga Private',
            'harga' => 'required|integer|min:0',
        ]);

        $user = User::find($request->user_id);

        if ($user->role !== 'pelanggan') {
            return response()->json(['message' => 'User bukan pelanggan'], 400);
        }

        if ($user->member) {
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

    // =========================
    // 2️⃣ Aktivasi member
    // =========================
    public function aktivasi($member_id)
    {
        $member = Member::find($member_id);
        if (!$member) return response()->json(['message' => 'Member tidak ditemukan'], 404);

        $member->update(['status' => 'aktif']);

        // Saat aktivasi, beri token sesuai maks_kelas
        $kelasList = Kelas::where('tipe_kelas', $member->tipe_kelas)->get();

        foreach ($kelasList as $kelas) {
            $member->kelas()->attach($kelas->id, [
                'jumlah_token' => $member->maks_kelas,
                'expired_at' => $member->tanggal_berakhir,
            ]);
        }

        return response()->json([
            'message' => 'Member diaktifkan dan token kelas diberikan',
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
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        $user = User::find($request->user_id);
        $kelasId = $request->kelas_id;

        // 🔸 Jika bukan member, hanya boleh booking 1x
        if (!$user->member) {
            return response()->json([
                'message' => 'Anda bukan member. Hanya bisa booking 1 kelas.',
                'boleh_booking' => true
            ]);
        }

        $member = $user->member;

        if ($member->status !== 'aktif') {
            return response()->json(['message' => 'Status member belum aktif'], 403);
        }

        $pivot = $member->kelas()->where('kelas_id', $kelasId)->first();

        if (!$pivot) return response()->json(['message' => 'Kelas tidak tersedia untuk member ini'], 400);
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
