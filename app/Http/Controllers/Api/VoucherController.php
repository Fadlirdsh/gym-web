<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voucher;
use App\Models\UserVoucher;

class VoucherController extends Controller
{
    // ============================
    // LIST SEMUA VOUCHER
    // ============================
    public function index()
    {
        $vouchers = Voucher::select(
            'id',
            'kode',
            'deskripsi',
            'diskon_persen',
            'kelas_id',
            'role_target',
            'tanggal_mulai',
            'tanggal_akhir',
            'kuota',
            'status',
            'created_at',
            'updated_at'
        )->get();

        return response()->json($vouchers);
    }

    // ============================
    // DETAIL VOUCHER
    // ============================
    public function show($id)
    {
        $voucher = Voucher::select(
            'id',
            'kode',
            'deskripsi',
            'diskon_persen',
            'kelas_id',
            'role_target',
            'tanggal_mulai',
            'tanggal_akhir',
            'kuota',
            'status',
            'created_at',
            'updated_at'
        )->findOrFail($id);

        return response()->json($voucher);
    }

    // ============================
    // CREATE VOUCHER
    // ============================
    public function store(Request $request)
    {
        $voucher = Voucher::create($request->all());
        return response()->json($voucher);
    }

    // ============================
    // UPDATE VOUCHER
    // ============================
    public function update(Request $request, $id)
    {
        $voucher = Voucher::findOrFail($id);
        $voucher->update($request->all());
        return response()->json($voucher);
    }

    // ============================
    // DELETE VOUCHER
    // ============================
    public function destroy($id)
    {
        Voucher::destroy($id);
        return response()->json(['message' => 'Voucher dihapus']);
    }

    // ============================
    // GET VOUCHER USER
    // ============================
    public function userVouchers(Request $request)
    {
        $user = $request->user();

        $vouchers = UserVoucher::with([
            'voucher:id,kode,deskripsi,diskon_persen,status'
        ])
        ->where('user_id', $user->id)
        ->where('status', 'aktif')
        ->get();

        return response()->json($vouchers);
    }

    // ============================
    // CLAIM VOUCHER
    // ============================
    public function claim(Request $request)
    {
        $request->validate([
            'voucher_id' => 'required|exists:vouchers,id',
        ]);

        $user = $request->user();

        // sudah klaim?
        $already = UserVoucher::where('user_id', $user->id)
            ->where('voucher_id', $request->voucher_id)
            ->exists();

        if ($already) {
            return response()->json(['message' => 'Voucher sudah diklaim'], 400);
        }

        UserVoucher::create([
            'user_id' => $user->id,
            'voucher_id' => $request->voucher_id,
            'status' => 'aktif',
        ]);

        return response()->json(['message' => 'Voucher berhasil diklaim']);
    }
}
