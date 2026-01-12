<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\QrCode;
use App\Models\Reservasi;
use App\Models\VisitLog;

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

        // 1. VALIDASI QR
        $qr = QrCode::where('token', $request->token)->first();

        if (!$qr) {
            return response()->json([
                'success' => false,
                'message' => 'QR tidak valid'
            ], 404);
        }

        if ($qr->used_at) {
            return response()->json([
                'success' => false,
                'message' => 'QR sudah digunakan'
            ], 400);
        }

        if ($qr->expired_at && $qr->expired_at->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'QR sudah kedaluwarsa'
            ], 400);
        }

        // 2. AMBIL RESERVASI
        $reservasi = Reservasi::with('schedule.kelas')->find($qr->reservasi_id);

        if (!$reservasi) {
            return response()->json([
                'success' => false,
                'message' => 'Reservasi tidak ditemukan'
            ], 404);
        }

        // 3. CEGAH DOBEL CHECK-IN
        if (
            VisitLog::where('reservasi_id', $reservasi->id)
            ->where('status', 'hadir')
            ->exists()
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Reservasi sudah check-in'
            ], 409);
        }

        // 4. TRANSACTION
        DB::transaction(function () use ($qr, $reservasi) {

            $qr->update([
                'used_at' => Carbon::now()
            ]);

            VisitLog::create([
                'reservasi_id' => $reservasi->id,
                'user_id'      => auth()->check() ? auth()->id() : null,
                'status'       => 'hadir',
                'catatan'      => 'Scan QR oleh admin',
            ]);

            $reservasi->update([
                'status_hadir' => 'hadir'
            ]);
        });

        // 5. RESPONSE (TANPA PROPERTI FIKTIF)
        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil',
            'data' => [
                'reservasi_id' => $reservasi->id,
                'pelanggan_id' => $reservasi->pelanggan_id,
                'kelas'        => $reservasi->schedule->kelas->nama_kelas,
                'tanggal'      => $reservasi->tanggal,
                'jam_mulai'    => $reservasi->schedule->start_time,
                'jam_selesai'  => $reservasi->schedule->end_time,
            ]
        ]);
    }

    /**
     * USER
     * Cek apakah bisa check-in hari ini
     */
    public function today()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'can_checkin' => false,
                'reason' => 'unauthenticated'
            ], 401);
        }

        $reservasi = Reservasi::with('schedule.kelas')
            ->where('pelanggan_id', $user->id)
            ->whereDate('tanggal', today())
            ->orderBy('tanggal', 'asc')
            ->first();

        if (!$reservasi) {
            return response()->json([
                'can_checkin' => false,
                'reason' => 'no_reservation_today'
            ]);
        }

        if ($reservasi->status !== 'paid') {
            return response()->json([
                'can_checkin' => false,
                'reason' => 'not_paid'
            ]);
        }

        if (!$reservasi->schedule || !$reservasi->schedule->kelas) {
            return response()->json([
                'can_checkin' => false,
                'reason' => 'invalid_schedule'
            ], 500);
        }

        if (
            VisitLog::where('reservasi_id', $reservasi->id)
            ->where('status', 'hadir')
            ->exists()
        ) {
            return response()->json([
                'can_checkin' => false,
                'reason' => 'already_checked_in'
            ]);
        }

        $start = Carbon::parse($reservasi->tanggal)
            ->setTimeFromTimeString($reservasi->schedule->start_time);


        $now = now();

        if ($now->lt($start->copy()->subHours(2))) {
            return response()->json([
                'can_checkin' => false,
                'reason' => 'too_early'
            ]);
        }

        if ($now->gt($start->copy()->addHour())) {
            return response()->json([
                'can_checkin' => false,
                'reason' => 'session_expired'
            ]);
        }

        return response()->json([
            'can_checkin'  => true,
            'reservasi_id' => $reservasi->id,
            'kelas'        => $reservasi->schedule->kelas->nama_kelas,
            'jam_mulai'    => substr($reservasi->schedule->start_time, 0, 5),
            'jam_selesai'  => substr($reservasi->schedule->end_time, 0, 5),
        ]);
    }
}
