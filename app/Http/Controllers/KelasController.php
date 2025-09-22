<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = Kelas::all();
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
            'diskon'      => 'nullable|numeric|min:0|max:100',
            'tipe_paket'  => 'nullable|string|max:50',
            // 'waktu_mulai' => 'required|date',
            'jumlah_token' => strtolower($request->tipe_paket) === 'classes'
                ? 'required|integer|min:1'
                : 'nullable',
            'expired_at'   => strtolower($request->tipe_paket) === 'classes'
                ? 'required|date'
                : 'nullable',
        ]);

        $data = $request->all();

        // Ubah format datetime-local ke MySQL datetime
        if (!empty($data['waktu_mulai'])) {
            $data['waktu_mulai'] = date('Y-m-d H:i:s', strtotime($data['waktu_mulai']));
        }

        Kelas::create($data);

        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil ditambahkan');
    }

    // public function edit(Kelas $kelas)
    // {
    //     return view('users.kelas-edit', compact('kelas'));
    // }

    public function update(Request $request, Kelas $kelas)
    {
        $request->validate([
            'nama_kelas'  => 'required|string|max:100',
            'tipe_kelas'  => 'required|string|max:50',
            'harga'       => 'required|numeric',
            'deskripsi'   => 'nullable|string',
            'diskon'      => 'nullable|numeric|min:0|max:100',
            'tipe_paket'  => 'nullable|string|max:50',
            // 'waktu_mulai' => 'required|date',
            'jumlah_token' => $request->tipe_paket === 'Classes'
                ? 'required|integer|min:1'
                : 'nullable',
            'expired_at'   => $request->tipe_paket === 'Classes'
                ? 'required|date'
                : 'nullable',
        ]);

        $data = $request->all();

        // Ubah format datetime-local ke MySQL datetime
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
