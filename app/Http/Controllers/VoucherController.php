<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use App\Models\Kelas;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /**
     * =========================
     * ADMIN VIEW
     * =========================
     */
    public function index()
    {
        try {
            return view('admin.voucher', [
                'vouchers' => Voucher::with('kelas')->latest()->get(),
                'kelas'    => Kelas::all(),
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memuat voucher');
        }
    }

    /**
     * =========================
     * STORE
     * =========================
     */
    public function store(Request $request)
    {
        $data = $this->validateVoucher($request);

        Voucher::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil ditambahkan',
        ]);
    }

    /**
     * =========================
     * UPDATE
     * =========================
     */
    public function update(Request $request, Voucher $voucher)
    {
        $data = $this->validateVoucher($request, $voucher->id);

        $voucher->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil diperbarui',
        ]);
    }

    /**
     * =========================
     * DELETE
     * =========================
     */
    public function destroy(Voucher $voucher)
    {
        $voucher->delete();

        return response()->json([
            'success' => true,
            'message' => 'Voucher berhasil dihapus',
        ]);
    }

    /**
     * =========================
     * VALIDATION
     * =========================
     */
    private function validateVoucher(Request $request, $id = null)
    {
        return $request->validate([
            'kode'           => 'required|string|max:50|unique:vouchers,kode,' . $id,
            'deskripsi'      => 'nullable|string',
            'diskon_persen'  => 'required|integer|min:1|max:100',
            'kelas_id'       => 'nullable|exists:kelas,id',
            'role_target'    => 'required|in:semua,pelanggan,member',
            'tanggal_mulai'  => 'required|date',
            'tanggal_akhir'  => 'required|date|after_or_equal:tanggal_mulai',
            'kuota'          => 'required|integer|min:1',
            'status'         => 'required|in:aktif,nonaktif',
        ]);
    }
}
