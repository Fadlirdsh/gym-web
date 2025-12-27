<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\QrCode;
use App\Models\Reservasi;
use Carbon\Carbon;

class AttendanceScanController extends Controller
{
    public function scan(Request $request)
    {
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

        $qr->update(['used_at' => Carbon::now()]);

        Reservasi::where('id', $qr->reservasi_id)
            ->update(['status_hadir' => 'hadir']);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil'
        ]);
    }
}

