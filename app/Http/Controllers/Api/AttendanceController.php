<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\QrCode;
use App\Models\Reservasi;
use Tymon\JWTAuth\Facades\JWTAuth;

class AttendanceController extends Controller
{
    /**
     * ADMIN / TRAINER
     * Scan QR untuk absensi
     */
    public function scan(Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        // 1. Cari QR
        $qr = QrCode::where('token', $request->token)->first();

        if (!$qr) {
            return response()->json([
                'success' => false,
                'message' => 'QR tidak valid'
            ], 404);
        }

        // 2. Cek QR sudah dipakai
        if ($qr->used_at) {
            return response()->json([
                'success' => false,
                'message' => 'QR sudah digunakan'
            ], 400);
        }

        // 3. Cek expired
        if ($qr->expired_at->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'QR sudah kedaluwarsa'
            ], 400);
        }

        // 4. Ambil reservasi
        $reservasi = Reservasi::find($qr->reservasi_id);

        if (!$reservasi) {
            return response()->json([
                'success' => false,
                'message' => 'Reservasi tidak ditemukan'
            ], 404);
        }

        // 5. Tandai QR sudah dipakai
        $qr->update([
            'used_at' => Carbon::now()
        ]);

        // 6. Tandai reservasi HADIR
        $reservasi->update([
            'status_hadir' => 'hadir'
        ]);


        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil',
            'data' => [
                'reservasi_id' => $reservasi->id,
                'pelanggan_id' => $reservasi->pelanggan_id,
                'kelas_id'     => $reservasi->kelas_id,
                'jadwal'       => $reservasi->jadwal,
            ]
        ]);
    }

    public function today()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'can_checkin' => false,
                'reason' => 'Unauthenticated'
            ], 401);
        }

        $now = now();

        $reservasi = Reservasi::with('kelas')
            ->where('pelanggan_id', $user->id)
            ->where('status', 'approved')
            ->where('status_hadir', 'belum_hadir')
            ->whereBetween('jadwal', [
                $now->copy()->subHours(1),
                $now->copy()->addHours(2),
            ])
            ->orderBy('jadwal', 'asc')
            ->first();

        if (!$reservasi) {
            return response()->json([
                'can_checkin' => false
            ]);
        }

        return response()->json([
            'can_checkin'  => true,
            'reservasi_id' => $reservasi->id,
            'kelas'        => $reservasi->kelas->nama_kelas,
            'jam_mulai'    => $reservasi->jadwal->format('H:i'),
        ]);
    }
}
