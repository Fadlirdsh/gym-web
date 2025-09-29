<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        // Ambil semua kelas sekaligus relasi diskon (untuk admin web)
        $kelas = Kelas::with('diskons')->get();
        return view('admin.Kelas', compact('kelas'));
    }

    public function create()
    {
        return view('users.kelas-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas'   => 'required|string|max:100',
            'tipe_kelas'   => 'required|string|max:50',
            'harga'        => 'required|numeric',
            'deskripsi'    => 'nullable|string',
            'tipe_paket'   => 'nullable|string|max:50',
            'jumlah_token' => strtolower($request->tipe_paket) === 'classes'
                ? 'required|integer|min:1'
                : 'nullable',
            'expired_at'   => strtolower($request->tipe_paket) === 'classes'
                ? 'required|date'
                : 'nullable',
        ]);

        $data = $request->all();

        if (!empty($data['waktu_mulai'])) {
            $data['waktu_mulai'] = date('Y-m-d H:i:s', strtotime($data['waktu_mulai']));
        }

        Kelas::create($data);

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil ditambahkan');
    }

    public function update(Request $request, Kelas $kelas)
    {
        $request->validate([
            'nama_kelas'   => 'required|string|max:100',
            'tipe_kelas'   => 'required|string|max:50',
            'harga'        => 'required|numeric',
            'deskripsi'    => 'nullable|string',
            'tipe_paket'   => 'nullable|string|max:50',
            'jumlah_token' => $request->tipe_paket === 'Classes'
                ? 'required|integer|min:1'
                : 'nullable',
            'expired_at'   => $request->tipe_paket === 'Classes'
                ? 'required|date'
                : 'nullable',
        ]);

        $data = $request->all();

        if (!empty($data['waktu_mulai'])) {
            $data['waktu_mulai'] = date('Y-m-d H:i:s', strtotime($data['waktu_mulai']));
        }

        $kelas->update($data);

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil diperbarui');
    }

    public function destroy(Kelas $kelas)
    {
        $kelas->delete();
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dihapus');
    }

    /**
     * API untuk mobile app (Ionic)
     */
    public function apiIndex()
    {
        $kelas = Kelas::with('diskons')->get();

        // transform data biar rapi sesuai kebutuhan frontend
        $data = $kelas->map(function ($item) {
            return [
                'id'          => $item->id,
                'nama_kelas'  => $item->nama_kelas,
                'tipe_kelas'  => $item->tipe_kelas,
                'harga'       => $item->harga,
                'deskripsi'   => $item->deskripsi,
                'tipe_paket'  => $item->tipe_paket,
                'jumlah_token'=> $item->jumlah_token,
                'expired_at'  => $item->expired_at,
                'waktu_mulai' => $item->waktu_mulai,
                'diskon_persen' => $item->diskons->first()->persen ?? 0,
                'harga_diskon'  => $item->harga_diskon ?? $item->harga,
                'sisa_kursi'    => $item->sisa_kursi ?? null, // kalau ada field kursi
            ];
        });

        return response()->json($data);
    }
}
