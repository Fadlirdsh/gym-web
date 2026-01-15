<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MemberToken;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Kelas;

class MemberTokenController extends Controller
{
    /**
     * Ambil semua token milik user login
     */
    public function index()
    {
        $user = JWTAuth::parseToken()->authenticate();

        $member = $user->member;

        if (!$member) {
            return response()->json([]);
        }

        return MemberToken::where('member_id', $member->id)->get();
    }

    public function tokenSisa(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        $user = JWTAuth::parseToken()->authenticate();
        $member = $user->member;

        if (!$member || $member->status !== 'aktif') {
            return response()->json(['token_sisa' => 0]);
        }

        $kelas = Kelas::findOrFail($request->kelas_id);

        $token = MemberToken::where('member_id', $member->id)
            ->where('tipe_kelas', $kelas->tipe_kelas)
            ->first();

        return response()->json([
            'token_sisa' => $token?->token_sisa ?? 0
        ]);
    }
}
