<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TokenPackage;
use Illuminate\Http\Request;

class TokenPackageController extends Controller
{
    /**
     * Tampilkan semua paket token
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => TokenPackage::orderBy('jumlah_token', 'asc')->get()
        ]);
    }

    /**
     * Simpan paket token baru
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'jumlah_token' => 'required|integer|min:1',
            'tipe_kelas' => 'required|string',
            'harga' => 'required|integer|min:0'
        ]);

        $tokenPackage = TokenPackage::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Paket token berhasil dibuat',
            'data' => $tokenPackage
        ]);
    }

    /**
     * Update paket token
     */
    public function update(Request $request, $id)
    {
        $package = TokenPackage::findOrFail($id);

        $data = $request->validate([
            'jumlah_token' => 'required|integer|min:1',
            'tipe_kelas' => 'required|string',
            'harga' => 'required|integer|min:0'
        ]);

        $package->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Paket token berhasil diupdate',
            'data' => $package
        ]);
    }

    /**
     * Hapus paket token
     */
    public function destroy($id)
    {
        $package = TokenPackage::findOrFail($id);
        $package->delete();

        return response()->json([
            'success' => true,
            'message' => 'Paket token berhasil dihapus'
        ]);
    }
}
