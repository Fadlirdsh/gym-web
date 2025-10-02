<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::with('diskons')->get();

        $data = $kelas->map(function ($item) {
            return [
                'id'           => $item->id,
                'nama_kelas'   => $item->nama_kelas,
                'tipe_kelas'   => $item->tipe_kelas,
                'harga'        => $item->harga,
                'deskripsi'    => $item->deskripsi,
                'tipe_paket'   => $item->tipe_paket,
                'jumlah_token' => $item->jumlah_token,
                'expired_at'   => $item->expired_at,
                'waktu_mulai'  => $item->waktu_mulai,
                'diskon_persen' => $item->diskon_persen,
                'harga_diskon' => $item->harga_diskon,
                'sisa_kursi'   => $item->kapasitas - $item->reservasi()->count(), // kapasitas tersisa
            ];
        });

        return response()->json($data);
    }

    public function show($id)
    {
        $kelas = Kelas::findOrFail($id);
        return response()->json($kelas);
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->update($request->all());

        return response()->json([
            'message' => 'Kelas berhasil diperbarui',
            'data'    => $kelas,
        ]);
    }

    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();

        return response()->json(['message' => 'Kelas berhasil dihapus']);
    }
}
