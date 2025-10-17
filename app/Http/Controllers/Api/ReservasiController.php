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
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'date'     => 'required|date',
            'time'     => 'required|string',
            'jumlah_kelas' => 'nullable|integer|min:1', // tambahkan untuk member
        ]);

        $user = auth()->user();
        $kelas = \App\Models\Kelas::findOrFail($request->kelas_id);

        // Cek membership user
        $member = \App\Models\Member::where('user_id', $user->id)
            ->where('status', 'aktif')
            ->first();

        $jumlahBooking = $request->jumlah_kelas ?? 1; // default 1

        if ($member) {
            // Jika member, cek apakah masih punya sisa kelas
            if ($member->sisa_kelas < $jumlahBooking) {
                return response()->json([
                    'message' => 'Sisa kelas kamu tidak mencukupi. Silakan perpanjang membership.'
                ], 400);
            }

            // Kurangi sisa kelas sesuai jumlah booking
            $member->decrement('sisa_kelas', $jumlahBooking);
        } else {
            // Jika bukan member, batasi hanya 1 kelas
            if ($jumlahBooking > 1) {
                return response()->json([
                    'message' => 'Non-member hanya dapat memesan 1 kelas per booking.'
                ], 403);
            }
        }

        // Gabungkan tanggal dan waktu jadi jadwal
        $jadwal = $request->date . ' ' . $request->time;

        $reservasi = \App\Models\Reservasi::create([
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
