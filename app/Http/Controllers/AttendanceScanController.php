<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QrCode;
use App\Models\Reservasi;
use App\Models\VisitLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AttendanceScanController extends Controller
{
    public function scan(Request $request)
    {

        Log::info('SCAN', $request->all());


        $request->validate([
            'token' => 'required|string'
        ]);

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

        if ($qr->expired_at->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'QR sudah kedaluwarsa'
            ], 400);
        }

        $reservasi = Reservasi::find($qr->reservasi_id);
        if (!$reservasi) {
            return response()->json([
                'success' => false,
                'message' => 'Reservasi tidak ditemukan'
            ], 404);
        }

        $qr->update(['used_at' => now()]);
        $reservasi->update(['status_hadir' => 'hadir']);

        VisitLog::create([
            'reservasi_id' => $reservasi->id,
            'user_id'      => optional(auth()->user())->id,
            'checkin_at'   => now(),
            'catatan'      => 'Scan QR oleh admin',
        ]);


        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil',
        ], 200);
    }
}
