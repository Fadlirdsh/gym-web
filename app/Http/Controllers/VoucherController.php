<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voucher;
use Carbon\Carbon;

class VoucherController extends Controller
{
    /**
     * Menampilkan semua voucher
     */
    public function index()
    {
        $vouchers = Voucher::with('kelas')->get(); // ambil semua voucher beserta relasi kelas
        return view('admin.voucher', compact('vouchers'));
    }


    /**
     * Menyimpan voucher baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode' => 'required|unique:vouchers,kode',
            'deskripsi' => 'required',
            'diskon_persen' => 'required|integer|min:1|max:100',
            'kelas_id' => 'nullable|exists:kelas,id',
            'role_target' => 'required|in:semua,pelanggan,member',
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
            'kuota' => 'required|integer|min:0',
            'status' => 'required|in:aktif,nonaktif',
        ]);

        $voucher = Voucher::create($request->all());
        return response()->json([
            'success' => true,
            'voucher' => $voucher
        ]);
    }

    /**
     * Menampilkan satu voucher
     */
    public function show($id)
    {
        $voucher = Voucher::findOrFail($id);
        return response()->json($voucher);
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

    /**
     * Validasi voucher saat digunakan
     * Memeriksa role, kelas, tanggal, kuota
     */
    public function validateVoucher(Request $request)
    {
        $user = $request->user(); // ambil user login
        $kode = $request->kode;
        $kelasId = $request->kelas_id;

        $voucher = Voucher::where('kode', $kode)->first();

        if (!$voucher) {
            return response()->json(['error' => 'Voucher tidak ditemukan'], 404);
        }

        // cek status aktif
        if ($voucher->status !== 'aktif') {
            return response()->json(['error' => 'Voucher tidak aktif'], 400);
        }

        // cek role
        if ($voucher->role_target !== 'semua' && $voucher->role_target !== $user->role) {
            return response()->json(['error' => 'Voucher tidak berlaku untuk akun Anda'], 403);
        }

        // cek kelas
        if ($voucher->kelas_id && $voucher->kelas_id != $kelasId) {
            return response()->json(['error' => 'Voucher tidak berlaku untuk kelas ini'], 403);
        }

        // cek tanggal
        $today = Carbon::today();
        if ($today->lt($voucher->tanggal_mulai) || $today->gt($voucher->tanggal_akhir)) {
            return response()->json(['error' => 'Voucher sudah kadaluarsa'], 400);
        }

        // cek kuota
        if ($voucher->kuota <= 0) {
            $voucher->status = 'nonaktif';
            $voucher->save();
            return response()->json(['error' => 'Voucher sudah habis'], 400);
        }

        // kurangi kuota & update status jika habis
        $voucher->useVoucher();

        return response()->json([
            'success' => true,
            'diskon' => $voucher->diskon_persen,
            'message' => 'Voucher berhasil digunakan'
        ]);
    }
}
