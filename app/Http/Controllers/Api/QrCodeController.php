<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Helpers\QrHelper;
use App\Models\QrCode;
use App\Models\Reservasi;

class QrCodeController extends Controller
{
    public function show($reservasi_id)
    {
        $user = Auth::user();

        // ===============================
        // 1. VALIDASI RESERVASI
        // ===============================
        $reservasi = Reservasi::where('id', $reservasi_id)
            ->where('pelanggan_id', $user->id)
            ->where('status', 'paid')
            ->where('status_hadir', 'belum_hadir')
            ->first();

        if (!$reservasi) {
            return response()->json([
                'success' => false,
                'message' => 'Reservasi tidak valid'
            ], 403);
        }

        // ===============================
        // 2. CARI QR AKTIF (BELUM DIPAKAI & BELUM EXPIRED)
        // ===============================
        $qr = QrCode::where('reservasi_id', $reservasi->id)
            ->whereNull('used_at')
            ->where('expired_at', '>', now())
            ->first();

        // ===============================
        // 3. JIKA TIDAK ADA QR AKTIF → BUAT BARU
        // ===============================
        if (!$qr) {

            // hapus QR lama (expired / bekas)
            QrCode::where('reservasi_id', $reservasi->id)->delete();

            // buat QR baru
            $qr = QrCode::create([
                'reservasi_id' => $reservasi->id,
                'token'        => Str::uuid()->toString(),
                'expired_at'   => now()->addMinutes(30), // ⬅ diperpanjang biar stabil
            ]);
        }

        // ===============================
        // 4. RESPONSE (SELALU ADA QR)
        // ===============================
        return response()->json([
            'success'    => true,
            'qr_url'     => url('/attendance/scan?token=' . $qr->token),
            'token'      => $qr->token,
            'expired_at' => $qr->expired_at->toIso8601String(),
            'alias'   => QrHelper::alias($qr->token),
        ]);
    }
}
    