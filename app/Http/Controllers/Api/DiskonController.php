<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Diskon;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DiskonController extends Controller
{
    // List semua diskon
    public function index()
    {
        $diskons = Diskon::with('kelas')->get();

        return response()->json([
            'status' => 'success',
            'data' => $diskons
        ]);
    }

    // Detail diskon
    public function show(Diskon $diskon)
    {
        $diskon->load('kelas');

        return response()->json([
            'status' => 'success',
            'data' => $diskon
        ]);
    }

    // Tambah diskon baru
    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'nama_diskon' => 'required|string',
            'persentase' => 'required|integer|min:1|max:100',
            'tanggal_mulai' => 'required|date',
            'tanggal_berakhir' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $diskon = Diskon::create([
            'kelas_id' => $request->kelas_id,
            'nama_diskon' => $request->nama_diskon,
            'persentase' => $request->persentase,
            'tanggal_mulai' => Carbon::parse($request->tanggal_mulai)->startOfDay(),
            'tanggal_berakhir' => Carbon::parse($request->tanggal_berakhir)->endOfDay(),
        ]);

        $diskon->load('kelas');

        return response()->json([
            'status' => 'success',
            'message' => 'Diskon berhasil ditambahkan',
            'data' => $diskon
        ]);
    }

    // Update diskon
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

        $diskon->load('kelas');

        return response()->json([
            'status' => 'success',
            'message' => 'Diskon berhasil diperbarui',
            'data' => $diskon
        ]);
    }

    // Hapus diskon
    public function destroy(Diskon $diskon)
    {
        $diskon->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Diskon berhasil dihapus'
        ]);
    }
}
