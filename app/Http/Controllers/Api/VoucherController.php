<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voucher;
use App\Models\UserVoucher;

class VoucherController extends Controller
{
    // ============================
    // LIST SEMUA VOUCHER (PUBLIC)
    // ============================
    public function index()
    {
        $vouchers = Voucher::where('status', 'aktif')
            ->whereDate('tanggal_mulai', '<=', now())
            ->whereDate('tanggal_akhir', '>=', now())
            ->get([
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
            ]);

        return response()->json($vouchers);
    }

    // ============================
    // DETAIL VOUCHER
    // ============================
    public function show($id)
    {
        $voucher = Voucher::findOrFail($id);

        return response()->json($voucher);
    }

    // ============================
    // CREATE VOUCHER (ADMIN)
    // ============================
    public function store(Request $request)
    {
        $request->validate([
            'kode'           => 'required|string|unique:vouchers,kode',
            'deskripsi'      => 'nullable|string',
            'diskon_persen'  => 'required|integer|min:1|max:100',
            'kelas_id'       => 'nullable|integer',
            'role_target'    => 'nullable|string',
            'tanggal_mulai'  => 'required|date',
            'tanggal_akhir'  => 'required|date|after_or_equal:tanggal_mulai',
            'kuota'          => 'required|integer|min:1',
            'status'         => 'required|in:aktif,nonaktif',
        ]);

        $voucher = Voucher::create($request->all());

        return response()->json($voucher, 201);
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

        return response()->json([
            'message' => 'Voucher berhasil dihapus'
        ]);
    }

    // ============================
    // LIST VOUCHER MILIK USER
    // ============================
    public function userVouchers(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $vouchers = $user->vouchers()
            ->whereDate('tanggal_akhir', '>=', now())
            ->get([
                'vouchers.id',
                'vouchers.kode',
                'vouchers.deskripsi',
                'vouchers.diskon_persen',
                'vouchers.kelas_id',
                'vouchers.tanggal_akhir',
            ]);

        return response()->json($vouchers);
    }

    // ============================
    // CLAIM VOUCHER (JWT REQUIRED)
    // ============================
    public function claim(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'voucher_id' => 'required|exists:vouchers,id'
        ]);

        $voucher = Voucher::find($request->voucher_id);

        // cek status
        if ($voucher->status !== 'aktif') {
            return response()->json(['message' => 'Voucher tidak aktif'], 400);
        }

        // cek tanggal
        if (now()->lt($voucher->tanggal_mulai) || now()->gt($voucher->tanggal_akhir)) {
            return response()->json(['message' => 'Voucher sudah kadaluarsa'], 400);
        }

        // cek kuota
        if ($voucher->kuota <= 0) {
            return response()->json(['message' => 'Kuota voucher habis'], 400);
        }

        // cek pernah klaim
        $alreadyClaimed = UserVoucher::where('user_id', $user->id)
            ->where('voucher_id', $voucher->id)
            ->exists();

        if ($alreadyClaimed) {
            return response()->json(['message' => 'Voucher sudah diklaim'], 400);
        }

        // simpan user_voucher
        UserVoucher::create([
            'user_id'    => $user->id,
            'voucher_id' => $voucher->id,
            'status'     => 'aktif',
        ]);

        // kurangi kuota
        $voucher->decrement('kuota');

        return response()->json([
            'message' => 'Voucher berhasil diklaim'
        ]);
    }
}
