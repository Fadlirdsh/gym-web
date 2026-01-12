<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QrCode;
use App\Models\Reservasi;
use App\Models\VisitLog;
use App\Helpers\QrHelper;

use App\Enums\VisitLogStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceScanController extends Controller
{
    public function scan(Request $request)
    {

        Log::info('SCAN', $request->all());


        $request->validate([
            'token' => 'required|string'
        ]);

        $input = trim($request->token);

        // ===============================
        // 1️⃣ COBA SEBAGAI TOKEN (UUID)
        // ===============================
        $qr = QrCode::where('token', $input)
            ->whereNull('used_at')
            ->where('expired_at', '>', now())
            ->first();

        // ===============================
        // 2️⃣ JIKA TIDAK KETEMU → COBA SEBAGAI ALIAS
        // ===============================
        if (!$qr) {

            $activeQrs = QrCode::whereNull('used_at')
                ->where('expired_at', '>', now())
                ->get();

            foreach ($activeQrs as $candidate) {
                if (QrHelper::alias($candidate->token) === strtoupper($input)) {
                    $qr = $candidate;
                    break;
                }
            }
        }

        // ===============================
        // 3️⃣ MASIH NULL → INVALID
        // ===============================
        if (!$qr) {
            return response()->json([
                'success' => false,
                'message' => 'QR / Kode manual tidak valid'
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

        if ($reservasi->status !== 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Reservasi belum dibayar'
            ], 403);
        }

        DB::transaction(function () use ($qr, $reservasi) {

            $qr->lockForUpdate();

            if ($qr->used_at) {
                throw new \Exception('QR sudah digunakan');
            }

            $qr->update(['used_at' => now()]);
            $reservasi->update(['status_hadir' => 'hadir']);
        });

        VisitLog::create([
            'reservasi_id' => $reservasi->id,
            'user_id'      => optional(auth()->user())->id,
            'status'       => VisitLogStatus::HADIR->value,
            'catatan'      => 'Scan QR oleh admin',
        ]);


        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil',
        ], 200);
    }
}
