<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voucher;
use App\Models\Kelas;
use Carbon\Carbon;

class VoucherController extends Controller
{
    /**
     * Menampilkan semua voucher dalam format JSON untuk API
     */
    public function index()
    {
        try {
            $vouchers = Voucher::with('kelas')->get();

            return response()->json($vouchers, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Gagal memuat voucher',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan voucher baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode' => 'required|string|unique:vouchers,kode',
            'deskripsi' => 'required|string',
            'diskon_persen' => 'required|numeric|min:0|max:100',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
            'kuota' => 'required|numeric|min:1',
            'role_target' => 'required|in:semua,pelanggan,member',
            'status' => 'required|in:aktif,nonaktif',
            'kelas_id' => 'nullable|exists:kelas,id'
        ]);

        $voucher = Voucher::create($validated);

        return response()->json([
            'success' => true,
            'voucher' => $voucher
        ], 201);
    }

    /**
     * Menampilkan satu voucher
     */
    public function show($id)
    {
        try {
            $voucher = Voucher::with('kelas')->findOrFail($id);
            return response()->json($voucher, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Voucher tidak ditemukan',
                'message' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update voucher
     */
    public function update(Request $request, $id)
    {
        $voucher = Voucher::findOrFail($id);

        $request->validate([
            'kode' => 'required|unique:vouchers,kode,' . $voucher->id,
            'deskripsi' => 'required',
            'diskon_persen' => 'required|integer|min:1|max:100',
            'kelas_id' => 'nullable|exists:kelas,id',
            'role_target' => 'required|in:semua,pelanggan,member',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
            'kuota' => 'required|integer|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $voucher->update($request->all());

        return response()->json([
            'success' => true,
            'voucher' => $voucher
        ]);
    }

    /**
     * Hapus voucher
     */
    public function destroy($id)
    {
        $voucher = Voucher::findOrFail($id);
        $voucher->delete();

        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil dihapus'
        ]);
    }
}
