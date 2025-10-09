<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservasi;
use Illuminate\Http\Request;
use Carbon\Carbon;
// use Illuminate\Support\Facades\Log;


class ReservasiController extends Controller
{
    public function index()
    {
        $reservasi = Reservasi::with(['pelanggan', 'trainer', 'kelas'])
            ->latest()
            ->get();

        return response()->json($reservasi);
    }

    public function store(Request $request)
    {
        // Log::info('Data request:', $request->all());
        // Log::info('User:', auth()->user());
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'date'     => 'required|date',
            'time'     => 'required|string',
        ]);

        $user = auth()->user();

        $kelas = \App\Models\Kelas::findOrFail($request->kelas_id);

        // Buat jadwal berdasarkan input date dan time
        $jadwal = $request->date . ' ' . $request->time;

        $reservasi = \App\Models\Reservasi::create([
            'pelanggan_id' => $user->id,
            'trainer_id'   => $kelas->trainer_id ?? 1,
            'kelas_id'     => $kelas->id,
            'jadwal' => Carbon::parse($request->jadwal),
            'status'       => 'pending',
        ]);

        return response()->json([
            'message' => 'Reservasi berhasil dibuat',
            'data'    => $reservasi,
        ], 201);
    }

    public function show($id)
    {
        $reservasi = Reservasi::with(['pelanggan', 'trainer', 'kelas'])->findOrFail($id);
        return response()->json($reservasi);
    }

    public function update(Request $request, $id)
    {
        $reservasi = Reservasi::findOrFail($id);
        $reservasi->update($request->all());

        return response()->json([
            'message' => 'Reservasi berhasil diperbarui',
            'data'    => $reservasi,
        ]);
    }

    public function destroy($id)
    {
        $reservasi = Reservasi::findOrFail($id);
        $reservasi->delete();

        return response()->json(['message' => 'Reservasi berhasil dihapus']);
    }

    public function currentUser()
    {
        $user = auth()->user();

        if ($user && $user->role === 'pelanggan') {
            return response()->json([
                'id'   => $user->id,
                'name' => $user->name
            ]);
        }

        return response()->json(['message' => 'User tidak ditemukan atau bukan pelanggan'], 404);
    }
}
