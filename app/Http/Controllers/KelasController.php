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
            'nama_kelas' => 'required|string|max:100',
            'tipe_kelas' => 'required|string|max:50',
            'harga'      => 'required|numeric',
            'deskripsi'  => 'nullable|string',
            'diskon'     => 'nullable|numeric',
            'tipe_paket' => 'nullable|string|max:50',
            'waktu_mulai' => 'required|date_format:H:i',
        ]);

        Kelas::create($request->all());
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil ditambahkan');
    }

    // public function edit(Kelas $kelas)
    // {
    //     return view('users.kelas-edit', compact('kelas'));
    // }

    public function update(Request $request, Kelas $kelas)
    {
        //   dd($request->all(), $kelas->id);
        $request->validate([
            'nama_kelas' => 'required|string|max:100',
            'tipe_kelas' => 'required|string|max:50',
            'harga'      => 'required|numeric',
            'deskripsi'  => 'nullable|string',
            'diskon'     => 'nullable|numeric',
            'tipe_paket' => 'nullable|string|max:50',
            'waktu_mulai' => 'required|date_format:H:i',
        ]);

        $kelas->update($request->all());
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil diperbarui');
    }

    public function destroy(Kelas $kelas)
    {
        $kelas->delete();
        return redirect()->route('kelas.index')->with('success', 'Kelas berhasil dihapus');
    }
}
