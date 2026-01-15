<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Transaksi;
use App\Models\Reservasi;
use App\Models\UserVoucher;
use App\Models\Voucher;
use App\Services\TransactionProcessor;
use Midtrans\Config;
use Midtrans\Notification;

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
     * MIDTRANS CALLBACK (FINAL & SAFE)
     * =====================================
     */
    public function handle(Request $request)
    {
        Log::info('🔥 MIDTRANS CALLBACK MASUK', $request->all());

        $notif = new Notification();

        $orderId       = $notif->order_id;
        $paymentStatus = $notif->transaction_status;
        $fraudStatus   = $notif->fraud_status ?? null;
        $paymentType   = $notif->payment_type ?? 'midtrans';

        DB::beginTransaction();

        try {
            /**
             * 1️⃣ Ambil transaksi (LOCK)
             */
            $transaksi = Transaksi::where('kode_transaksi', $orderId)
                ->lockForUpdate()
                ->first();

            if (!$transaksi) {
                DB::rollBack();
                return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
            }

            /**
             * 🔒 IDEMPOTENT BERDASARKAN EKSEKUSI HAK
             * (BUKAN status pembayaran)
             */
            if ($transaksi->is_processed) {
                DB::commit();
                return response()->json(['message' => 'Already processed']);
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
                // Update status pembayaran
                $transaksi->update([
                    'status' => 'success',
                    'metode' => $paymentType,
                ]);

                // 🔥 EKSEKUSI HAK (TOKEN / MEMBER / RESERVASI / QR)
                TransactionProcessor::process($transaksi);

                /**
                 * ============================
                 * VOUCHER (EFEK TAMBAHAN)
                 * ============================
                 */
                $userVoucher = UserVoucher::where('user_id', $transaksi->user_id)
                    ->where('status', 'claimed')
                    ->lockForUpdate()
                    ->first();

                if ($userVoucher) {
                    $userVoucher->update([
                        'status'  => 'used',
                        'used_at' => now(),
                    ]);

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
             * STATUS FAILED
             * ============================
             */
            if (in_array($paymentStatus, ['cancel', 'expire', 'deny'])) {
                $transaksi->update(['status' => 'failed']);

                // Batalkan reservasi kalau ada
                if ($transaksi->jenis === 'reservasi') {
                    Reservasi::where('id', $transaksi->source_id)
                        ->update(['status' => 'canceled']);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Callback processed']);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('❌ MIDTRANS CALLBACK ERROR', [
                'order_id' => $orderId,
                'error'    => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Callback error',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
