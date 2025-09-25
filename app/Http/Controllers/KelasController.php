<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        // Ambil semua kelas sekaligus relasi diskon
        $kelas = Kelas::with('diskons')->get();

// dd($kelas->first()->diskons, $kelas->first()->harga_diskon, $kelas->first()->diskon_persen);

        return view('users.Kelas', compact('kelas'));
    }

    public function create()
    {
        return view('users.kelas-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas'  => 'required|string|max:100',
            'tipe_kelas'  => 'required|string|max:50',
            'harga'       => 'required|numeric',
            'deskripsi'   => 'nullable|string',
            'tipe_paket'  => 'nullable|string|max:50',
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
            'nama_kelas'  => 'required|string|max:100',
            'tipe_kelas'  => 'required|string|max:50',
            'harga'       => 'required|numeric',
            'deskripsi'   => 'nullable|string',
            'tipe_paket'  => 'nullable|string|max:50',
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
}
