<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Kelas;
use App\Models\VisitLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class QrCodeController extends Controller
{
    public function show($kelas_id)
    {
        try {
            $kelas = Kelas::findOrFail($kelas_id);

            $qrPayload = json_encode([
                'kelas_id' => $kelas->id,
                'timestamp' => now()->timestamp
            ]);

            // Pastikan dikembalikan sebagai string SVG
            $qrSvg = (string) QrCode::format('svg')->size(200)->generate($qrPayload);

            return response()->json([
                'success' => true,
                'kelas' => $kelas->nama_kelas,
                'qr_svg' => $qrSvg
            ]);

        } catch (\Exception $e) {
            Log::error('QR Controller Error: '.$e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan server'
            ], 500);
        }
    }

    public function scan(Request $request)
    {
        $request->validate([
            'qr_data' => 'required'
        ]);

        $data = json_decode($request->qr_data);

        if (!$data || !isset($data->kelas_id)) {
            return response()->json([
                'status' => false,
                'message' => 'QR Code tidak valid'
            ], 400);
        }

        $kelas = Kelas::find($data->kelas_id);

        if (!$kelas) {
            return response()->json([
                'status' => false,
                'message' => 'Kelas tidak ditemukan'
            ], 404);
        }

        VisitLog::create([
            'user_id' => Auth::id(),
            'kelas_id' => $kelas->id,
            'status' => 'hadir',
            'catatan' => 'Scan QR berhasil'
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Kehadiran berhasil dicatat'
        ]);
    }
}
