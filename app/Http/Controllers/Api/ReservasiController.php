<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservasi;
use App\Models\Kelas;
use App\Models\Member;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;

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
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'date'     => 'required|date',
            'time'     => 'required|string',
            'tipe_paket' => 'required|string', // pastikan tipe paket dikirim
        ]);

        // Ambil user dari token JWT
        $user = JWTAuth::parseToken()->authenticate();
        $kelas = Kelas::findOrFail($request->kelas_id);

        // Ambil member jika ada
        $member = Member::where('user_id', $user->id)
            ->where('status', 'aktif')
            ->first();

        // Daftar tipe paket yang diperbolehkan
        $allPackages = ["General", "3 Classes", "5 Classes", "6 Classes", "10 Classes", "20 Classes"];
        $generalOnly = ["General"];

        if ($member) {
            // Member aktif → boleh semua paket
            if (!in_array($request->tipe_paket, $allPackages)) {
                return response()->json([
                    'message' => 'Tipe paket tidak valid.'
                ], 400);
            }

            // Jika pilih selain General → cek kelas & token
            if ($request->tipe_paket !== 'General') {
                $pivot = $member->kelas()->where('kelas_id', $kelas->id)->first();

                if (!$pivot) {
                    return response()->json([
                        'message' => 'Kelas ini tidak termasuk dalam paket membership Anda.'
                    ], 400);
                }

                if ($pivot->pivot->jumlah_token <= 0) {
                    return response()->json([
                        'message' => 'Kuota kelas Anda sudah habis. Silakan perpanjang membership.'
                    ], 400);
                }

                // Kurangi token
                $member->kelas()->updateExistingPivot($kelas->id, [
                    'jumlah_token' => $pivot->pivot->jumlah_token - 1
                ]);
            }
        } else {
            // Bukan member / pending / nonaktif → hanya boleh General
            if (!in_array($request->tipe_paket, $generalOnly)) {
                return response()->json([
                    'message' => 'Hanya paket General yang bisa dipilih untuk non-member atau member nonaktif/pending.'
                ], 403);
            }
        }

        // Gabungkan tanggal & waktu
        $jadwal = $request->date . ' ' . $request->time;

        // Simpan data reservasi
        $reservasi = Reservasi::create([
            'pelanggan_id' => $user->id,
            'trainer_id'   => $kelas->trainer_id ?? 1,
            'kelas_id'     => $kelas->id,
            'jadwal'       => Carbon::parse($jadwal),
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
