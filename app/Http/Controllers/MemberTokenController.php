<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\MemberToken;

class MemberTokenController extends Controller
{
    /**
     * ADMIN TOPUP TOKEN (MANUAL)
     * - TANPA MIDTRANS
     * - TANPA TRANSAKSI
     */
    public function topup(Request $request)
    {
        $request->validate([
            'member_id'  => 'required|exists:members,id',
            'tipe_kelas' => 'required|string',
            'jumlah'     => 'required|integer|min:1',
        ]);

        $member = Member::findOrFail($request->member_id);

        if ($member->status !== 'aktif') {
            return back()->with('error', 'Member tidak aktif');
        }

        $token = MemberToken::firstOrCreate(
            [
                'member_id'  => $member->id,
                'tipe_kelas' => $request->tipe_kelas,
            ],
            [
                'token_total'    => 0,
                'token_terpakai' => 0,
                'token_sisa'     => 0,
            ]
        );

        $token->increment('token_total', $request->jumlah);
        $token->increment('token_sisa', $request->jumlah);

        return back()->with('success', 'Token berhasil ditambahkan oleh admin');
    }
}
