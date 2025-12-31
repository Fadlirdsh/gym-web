<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\QrCode;
use App\Models\Reservasi;

class QrCodeController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | PELANGGAN - GENERATE QR ABSENSI
    |--------------------------------------------------------------------------
    | SYARAT:
    | - reservasi milik user
    | - status = paid
    | - status_hadir = belum_hadir
    */
    public function show($reservasi_id)
    {
        $user = Auth::user();

        /*
        |----------------------------------------------------------------------
        | 1. VALIDASI RESERVASI
        |----------------------------------------------------------------------
        */
        $reservasi = Reservasi::where('id', $reservasi_id)
            ->where('pelanggan_id', $user->id)
            ->where('status', 'paid')              // 🔥 KUNCI UTAMA
            ->where('status_hadir', 'belum_hadir')
            ->first();

        if (!$reservasi) {
            return response()->json([
                'success' => false,
                'message' => 'Reservasi belum dibayar, sudah hadir, atau tidak valid'
            ], 403);
        }

        /*
        |----------------------------------------------------------------------
        | 2. JIKA QR MASIH AKTIF → PAKAI ULANG
        |----------------------------------------------------------------------
        */
        if (
            $reservasi->qrCode &&
            !$reservasi->qrCode->used_at &&
            $reservasi->qrCode->expired_at->isFuture()
        ) {
            return response()->json([
                'success'    => true,
                'qr_token'   => $reservasi->qrCode->token,
                'expired_at' => $reservasi->qrCode->expired_at->toIso8601String(),
            ]);
        }

        /*
        |----------------------------------------------------------------------
        | 3. HAPUS QR LAMA (JIKA ADA)
        |----------------------------------------------------------------------
        */
        QrCode::where('reservasi_id', $reservasi->id)->delete();

        /*
        |----------------------------------------------------------------------
        | 4. BUAT QR BARU
        |----------------------------------------------------------------------
        */
        $qr = QrCode::create([
            'reservasi_id' => $reservasi->id,
            'token'        => Str::uuid()->toString(),
            'expired_at'   => Carbon::now()->addMinutes(10),
        ]);

        return response()->json([
            'success'    => true,
            'qr_token'   => $qr->token,
            'expired_at' => $qr->expired_at->toIso8601String(),
        ]);
    }
}
