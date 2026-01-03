<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaksi;
use App\Models\Reservasi;
use App\Models\UserVoucher;
use App\Models\Voucher;
use Midtrans\Config;
use Midtrans\Notification;
use Illuminate\Support\Facades\Log;


class MidtransCallbackController extends Controller
{
    public function __construct()
    {
        Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized  = true;
        Config::$is3ds        = true;
    }

    /**
     * =====================================
     * MIDTRANS CALLBACK (PAYMENT STATUS)
     * =====================================
     */
    public function handle(Request $request)
    {
        $notif = new Notification();

        $orderId       = $notif->order_id;
        $paymentStatus = $notif->transaction_status;
        $fraudStatus   = $notif->fraud_status ?? null;

        DB::beginTransaction();

        try {
            // 1️⃣ Ambil transaksi (LOCK)
            $transaksi = Transaksi::where('kode_transaksi', $orderId)
                ->lockForUpdate()
                ->first();

            if (!$transaksi) {
                DB::rollBack();
                return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
            }

            // ⛔ IDPOTENT: kalau sudah paid / failed, STOP
            if (in_array($transaksi->status, ['paid', 'failed'])) {
                DB::commit();
                return response()->json(['message' => 'Callback already processed']);
            }

            /**
             * ============================
             * STATUS SUCCESS
             * ============================
             */
            if (
                in_array($paymentStatus, ['capture', 'settlement']) &&
                ($fraudStatus === 'accept' || $fraudStatus === null)
            ) {
                // 2️⃣ Update transaksi
                $transaksi->update(['status' => 'paid']);

                // 3️⃣ Update reservasi
                if ($transaksi->jenis === 'reservasi') {
                    Reservasi::where('id', $transaksi->source_id)
                        ->update(['status' => 'paid']);
                }

                // 4️⃣ Tandai voucher USED (jika ada)
                $userVoucher = UserVoucher::where('user_id', $transaksi->user_id)
                    ->where('status', 'claimed')
                    ->first();

                if ($userVoucher) {
                    $userVoucher->update([
                        'status'  => 'used',
                        'used_at' => now(),
                    ]);

                    // 5️⃣ Kurangi kuota voucher
                    $voucher = Voucher::find($userVoucher->voucher_id);
                    if ($voucher && $voucher->kuota > 0) {
                        $voucher->decrement('kuota');

                        if ($voucher->kuota <= 0) {
                            $voucher->update(['status' => 'nonaktif']);
                        }
                    }
                }
            }

            /**
             * ============================
             * STATUS GAGAL / EXPIRED
             * ============================
             */
            if (in_array($paymentStatus, ['cancel', 'expire', 'deny'])) {
                $transaksi->update(['status' => 'failed']);

                if ($transaksi->jenis === 'reservasi') {
                    Reservasi::where('id', $transaksi->source_id)
                        ->update(['status' => 'cancelled']);
                }
            }

            DB::commit();

            return response()->json(['message' => 'Callback processed']);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Midtrans Callback Error', [
                'order_id' => $orderId ?? null,
                'error'    => $e->getMessage(),
                'trace'    => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Callback error',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
