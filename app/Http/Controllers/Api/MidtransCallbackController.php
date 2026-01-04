<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Transaksi;
use App\Models\Reservasi;
use App\Models\Member;
use App\Models\UserVoucher;
use App\Models\Voucher;

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
     * MIDTRANS CALLBACK (FINAL SOURCE)
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
             * ⛔ IDEMPOTENT
             */
            if (in_array($transaksi->status, ['success', 'failed'])) {
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
                // Update transaksi
                $transaksi->update([
                    'status' => 'success',
                    'metode' => $paymentType,
                ]);

                /**
                 * ============================
                 * SWITCH BY JENIS
                 * ============================
                 */
                switch ($transaksi->jenis) {

                    /**
                     * 🔵 RESERVASI
                     */
                    case 'reservasi':
                        Reservasi::where('id', $transaksi->source_id)
                            ->update(['status' => 'paid']);
                        break;

                    /**
                     * 🟦 MEMBER (AKTIVASI)
                     */
                    case 'member':
                        $member = Member::lockForUpdate()->find($transaksi->source_id);
                        if ($member) {
                            $member->update([
                                'status' => 'aktif',
                                'tanggal_mulai' => now(),
                                'tanggal_berakhir' => now()->addMonth(),
                            ]);
                        }
                        break;

                    /**
                     * 🟩 TOKEN TOP-UP
                     */
                    case 'token':
                        $member = Member::lockForUpdate()->find($transaksi->source_id);

                        if (!$member) {
                            throw new \Exception('Member tidak ditemukan untuk token topup');
                        }

                        if ($member->status !== 'aktif') {
                            throw new \Exception('Member tidak aktif');
                        }

                        $meta = $transaksi->meta ?? [];

                        if (!isset($meta['token_qty']) || (int)$meta['token_qty'] <= 0) {
                            throw new \Exception('Token quantity tidak valid');
                        }

                        $tokenTambah = (int) $meta['token_qty'];

                        $member->update([
                            'token_total' => $member->token_total + $tokenTambah,
                            'token_sisa'  => $member->token_sisa + $tokenTambah,
                        ]);
                        break;
                }

                /**
                 * ============================
                 * VOUCHER (OPTIONAL)
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
