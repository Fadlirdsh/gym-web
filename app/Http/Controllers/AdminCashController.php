<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Member;
use App\Models\MemberToken;
use App\Models\TokenPackage;
use App\Models\Transaksi;

class AdminCashController extends Controller
{
    /**
     * =====================================================
     * ADMIN CASH — AKTIVASI / PERPANJANG MEMBER
     * =====================================================
     */
    public function activateMember(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'bulan'   => 'nullable|integer|min:1', // default 1 bulan
        ]);

        $admin = auth()->user();
        $user  = User::findOrFail($request->user_id);
        $bulan = $request->bulan ?? 1;

        DB::transaction(function () use ($user, $bulan, $admin) {

            $member = Member::firstOrCreate(
                ['user_id' => $user->id],
                ['status' => 'nonaktif']
            );

            // ⏱️ Hitung masa aktif
            $mulai = $member->status === 'aktif'
                ? Carbon::parse($member->tanggal_berakhir)
                : now();

            $berakhir = $mulai->copy()->addMonths($bulan);

            // ✅ Aktifkan / perpanjang
            $member->update([
                'status'           => 'aktif',
                'tanggal_mulai'    => $mulai,
                'tanggal_berakhir' => $berakhir,
            ]);

            // 💰 Harga contoh (bebas lo ganti)
            $hargaMember = 250000 * $bulan;

            // 🧾 Catat transaksi cash
            Transaksi::create([
                'kode_transaksi' => 'TRX-' . strtoupper(Str::random(10)),
                'user_id'        => $user->id,
                'jenis'          => 'member',
                'source_id'      => $member->id,
                'harga_asli'     => $hargaMember,
                'diskon'         => 0,
                'total_bayar'    => $hargaMember,
                'metode'         => 'cash',
                'status'         => 'success',
                'is_processed'   => true,
                'created_by'     => $admin->id,
            ]);
        });

        return redirect()
            ->back()
            ->with('success', 'Member berhasil diaktifkan / diperpanjang (cash)');
    }

    /**
     * =====================================================
     * ADMIN CASH — TOPUP TOKEN
     * =====================================================
     */
    public function topupToken(Request $request)
    {
        $request->validate([
            'member_id'        => 'required|exists:members,id',
            'token_package_id' => 'required|exists:token_packages,id',
        ]);

        $admin   = auth()->user();
        $member  = Member::findOrFail($request->member_id);
        $package = TokenPackage::findOrFail($request->token_package_id);

        if ($member->status !== 'aktif') {
            return response()->json([
                'message' => 'Member tidak aktif',
            ], 422);
        }

        DB::transaction(function () use ($member, $package, $admin) {

            // 🧾 Catat transaksi cash
            Transaksi::create([
                'kode_transaksi' => 'TRX-' . strtoupper(Str::random(10)),
                'user_id'        => $member->user_id,
                'jenis'          => 'token',
                'source_id'      => $package->id,
                'harga_asli'     => $package->harga,
                'diskon'         => 0,
                'total_bayar'    => $package->harga,
                'metode'         => 'cash',
                'status'         => 'success',
                'is_processed'   => true,
                'created_by'     => $admin->id,
            ]);

            // ➕ Tambah token
            $memberToken = MemberToken::firstOrCreate(
                [
                    'member_id'  => $member->id,
                    'tipe_kelas' => $package->tipe_kelas,
                ],
                [
                    'token_total'    => 0,
                    'token_terpakai' => 0,
                    'token_sisa'     => 0,
                ]
            );

            $memberToken->increment('token_total', $package->jumlah_token);
            $memberToken->increment('token_sisa', $package->jumlah_token);
        });

        return redirect()
            ->back()
            ->with('success', 'Topup token berhasil (cash)');
    }
}
