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
            'kelas_id'   => 'required|exists:kelas,id',
            'date'       => 'required|date',
            'time'       => 'required|string',
            'tipe_paket' => 'required|string',
        ]);

        $user = JWTAuth::parseToken()->authenticate();
        $kelas = Kelas::findOrFail($request->kelas_id);

        $member = Member::where('user_id', $user->id)
            ->where('status', 'aktif')
            ->first();

        $allPackages = ["General", "3 Classes", "5 Classes", "10 Classes", "20 Classes"];
        $generalOnly = ["General"];

        if ($member) {
            if (!in_array($request->tipe_paket, $allPackages)) {
                return response()->json(['message' => 'Tipe paket tidak valid.'], 400);
            }
        } else {
            if (!in_array($request->tipe_paket, $generalOnly)) {
                return response()->json(['message' => 'Hanya paket General untuk non-member.'], 403);
            }
        }

        // 🔹 Hitung harga dari tabel kelas
        $harga = $this->ambilHargaDariKelas($kelas, $request->tipe_paket);

        $jadwal = $request->date . ' ' . $request->time;

        $reservasi = Reservasi::create([
            'pelanggan_id' => $user->id,
            'trainer_id'   => $kelas->trainer_id ?? 1,
            'kelas_id'     => $kelas->id,
            'jadwal'       => Carbon::parse($jadwal),
            'status'       => 'pending',
            'tipe_paket'   => $request->tipe_paket,
            'harga'        => $harga,
        ]);

        return response()->json([
            'message' => 'Reservasi berhasil dibuat',
            'data'    => $reservasi,
        ], 201);
    }

    /**
     * 🔹 Endpoint untuk ambil harga dari kelas berdasarkan tipe paket
     */
    public function getHarga(Request $request)
    {
        $request->validate([
            'class_name' => 'required|string',
            'tipe_paket' => 'required|string',
        ]);

        $kelas = Kelas::where('nama_kelas', $request->class_name)->first();

        if (!$kelas) {
            return response()->json(['message' => 'Kelas tidak ditemukan.'], 404);
        }

        $harga = $this->ambilHargaDariKelas($kelas, $request->tipe_paket);

        return response()->json([
            'harga' => $harga,
            'nama_kelas' => $kelas->nama_kelas,
        ]);
    }

    /**
     * 🔹 Fungsi bantu ambil harga dari tabel kelas
     */
    private function ambilHargaDariKelas($kelas, $tipePaket)
    {
        $baseHarga = $kelas->harga;

        switch ($tipePaket) {
            case 'General':
                return $baseHarga;
            case '3 Classes':
                return $baseHarga * 3 * 0.95; // contoh diskon 5%
            case '5 Classes':
                return $baseHarga * 5 * 0.90; // diskon 10%
            case '10 Classes':
                return $baseHarga * 10 * 0.85; // diskon 15%
            case '20 Classes':
                return $baseHarga * 20 * 0.80; // diskon 20%
            default:
                return $baseHarga;
        }
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
