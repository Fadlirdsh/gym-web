<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservasi;
use App\Models\Kelas;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;

class ReservasiController extends Controller
{
    // ===============================
    // List semua reservasi
    // ===============================
    public function index()
    {
        $reservasi = Reservasi::with(['pelanggan', 'trainer', 'kelas'])
            ->latest()
            ->get();

        return response()->json($reservasi);
    }

    // ===============================
    // Buat reservasi baru
    // ===============================
    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'date'     => 'required|date',
            'time'     => 'required|string',
            'catatan'  => 'nullable|string',
        ]);

        $user = JWTAuth::parseToken()->authenticate();
        $kelas = Kelas::findOrFail($request->kelas_id);

        // Hitung harga langsung dari kelas
        $harga = $kelas->harga;

        $jadwal = $request->date . ' ' . $request->time;

        // Simpan reservasi
        $reservasi = Reservasi::create([
            'pelanggan_id' => $user->id,
            'trainer_id'   => $kelas->trainer_id ?? 1,
            'kelas_id'     => $kelas->id,
            'jadwal'       => Carbon::parse($jadwal),
            'status'       => 'pending',
            'harga'        => $harga,
            'catatan'      => $request->catatan,
        ]);

        return response()->json([
            'message' => 'Reservasi berhasil dibuat',
            'data'    => $reservasi,
        ], 201);
    }

    // ===============================
    // Tampilkan reservasi tertentu
    // ===============================
    public function show($id)
    {
        $reservasi = Reservasi::with(['pelanggan', 'trainer', 'kelas'])->findOrFail($id);
        return response()->json($reservasi);
    }

    // ===============================
    // Update reservasi
    // ===============================
    public function update(Request $request, $id)
    {
        $reservasi = Reservasi::findOrFail($id);
        $reservasi->update($request->all());

        return response()->json([
            'message' => 'Reservasi berhasil diperbarui',
            'data'    => $reservasi,
        ]);
    }

    // ===============================
    // Hapus reservasi
    // ===============================
    public function destroy($id)
    {
        $reservasi = Reservasi::findOrFail($id);
        $reservasi->delete();

        return response()->json(['message' => 'Reservasi berhasil dihapus']);
    }

    // ===============================
    // Ambil user saat ini
    // ===============================
    public function currentUser()
    {
        $user = JWTAuth::parseToken()->authenticate();

        if ($user && $user->role === 'pelanggan') {
            return response()->json([
                'id'   => $user->id,
                'name' => $user->name
            ]);
        }

        return response()->json(['message' => 'User tidak ditemukan atau bukan pelanggan'], 404);
    }
}
