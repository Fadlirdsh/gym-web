<?php

namespace App\Http\Controllers;

use App\Models\Diskon;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DiskonController extends Controller
{
    // List semua diskon + data kelas untuk modal
    public function index()
    {
        $diskons = Diskon::with('kelas')->get();
        $kelas = Kelas::all();
        return view('admin.diskon', compact('diskons', 'kelas'));
    }

    // Simpan diskon baru
    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'nama_diskon' => 'required|string',
            'persentase' => 'required|integer|min:1|max:100',
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        Diskon::create([
            'kelas_id' => $request->kelas_id,
            'nama_diskon' => $request->nama_diskon,
            'persentase' => $request->persentase,
            'tanggal_mulai' => Carbon::parse($request->tanggal_mulai)->startOfDay(),
            'tanggal_berakhir' => Carbon::parse($request->tanggal_berakhir)->endOfDay(),
        ]);

        return redirect()->back()->with('success', 'Diskon berhasil ditambahkan!');
    }

    public function edit(Diskon $diskon)
    {
        return response()->json($diskon);
    }


    // Update diskon (pakai modal, jadi form action langsung ke route ini)
    public function update(Request $request, Diskon $diskon)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'nama_diskon' => 'required|string',
            'persentase' => 'required|integer|min:1|max:100',
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $diskon->update([
            'kelas_id' => $request->kelas_id,
            'nama_diskon' => $request->nama_diskon,
            'persentase' => $request->persentase,
            'tanggal_mulai' => Carbon::parse($request->tanggal_mulai)->startOfDay(),
            'tanggal_berakhir' => Carbon::parse($request->tanggal_berakhir)->endOfDay(),
        ]);

        return redirect()->back()->with('success', 'Diskon berhasil diperbarui!');
    }

    // Hapus diskon
    public function destroy(Diskon $diskon)
    {
        $diskon->delete();
        return redirect()->back()->with('success', 'Diskon berhasil dihapus!');
    }
}
