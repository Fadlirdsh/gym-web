<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\FirstTimeDiscount;
use App\Models\Transaksi;
use App\Models\Reservasi;
use App\Models\Member;
use App\Models\MemberToken;
use App\Models\TokenPackage;
use App\Models\UserVoucher;
use App\Models\Voucher;
use App\Models\QrCode;
use Illuminate\Support\Str;
use Carbon\Carbon;
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

                        $reservasi = Reservasi::lockForUpdate()
                            ->find($transaksi->source_id);

                        if (!$reservasi) {
                            break;
                        }

                        // 1️⃣ Update status reservasi
                        $reservasi->update([
                            'status' => 'paid'
                        ]);

                        // 2️⃣ BUAT QR SETELAH STATUS PAID
                        \App\Models\QrCode::firstOrCreate(
                            ['reservasi_id' => $reservasi->id],
                            [
                                'token'      => \Illuminate\Support\Str::uuid()->toString(),
                                'expired_at' => \Carbon\Carbon::parse($reservasi->tanggal)->endOfDay(),
                            ]
                        );

                        // 🔒 KUNCI FIRST-TIME DISCOUNT (JANGAN DIUBAH)
                        $firstTimeDiscount = FirstTimeDiscount::where('user_id', $transaksi->user_id)
                            ->whereNull('used_at')
                            ->where('expired_at', '>=', now())
                            ->lockForUpdate()
                            ->first();

                        if ($firstTimeDiscount) {
                            $firstTimeDiscount->markAsUsed();
                        }

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
                                'activated_by_transaction_id' => $transaksi->id,
                            ]);
                        }
                        break;
                    /**
                     * 🟩 TOKEN TOP-UP (PAKAI member_tokens)
                     */
                    case 'token':
                        $package = TokenPackage::lockForUpdate()
                            ->findOrFail($transaksi->source_id);

                        $member = Member::lockForUpdate()
                            ->where('user_id', $transaksi->user_id)
                            ->where('status', 'aktif')
                            ->firstOrFail();

                        // 🔐 CEGAH CALLBACK DOBEL
                        $alreadyProcessed = MemberToken::where('transaction_id', $transaksi->id)->exists();
                        if ($alreadyProcessed) {
                            break; // callback ulang → STOP
                        }

                        $memberToken = MemberToken::firstOrCreate(
                            [
                                'member_id'  => $member->id,
                                'tipe_kelas' => $package->tipe_kelas,
                            ],
                            [
                                'token_total'    => 0,
                                'token_terpakai' => 0,
                                'token_sisa'     => 0,
                            ]
                        );

                        $memberToken->increment('token_total', $package->jumlah_token);
                        $memberToken->increment('token_sisa', $package->jumlah_token);

                        // 🔴 CATAT SUMBER TOKEN
                        $memberToken->update([
                            'source'         => 'midtrans',
                            'transaction_id' => $transaksi->id,
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
