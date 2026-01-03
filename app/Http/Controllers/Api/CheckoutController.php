<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Reservasi;
use App\Models\Transaksi;
use App\Models\Kelas;
use App\Models\UserVoucher;
use App\Services\PricingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;


use Midtrans\Snap;
use Midtrans\Config;

class CheckoutController extends Controller
{
    public function __construct()
    {
        Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized  = true;
        Config::$is3ds        = true;
    }

    /**
     * ============================
     * CHECKOUT RESERVASI (FINAL)
     * ============================
     * Voucher: OPTIONAL
     */
    public function checkoutReservasi(Request $request, PricingService $pricing)
    {
        $request->validate([
            'kelas_id'         => 'required|exists:kelas,id',
            'date'             => 'required|date',
            'time'             => 'required|string',
            'catatan'          => 'nullable|string',
            'voucher_user_id'  => 'nullable|exists:user_vouchers,id',
        ]);

        $user  = auth()->user();
        $kelas = Kelas::findOrFail($request->kelas_id);

        DB::beginTransaction();

        try {
            /**
             * =====================================
             * 1️⃣ VALIDASI VOUCHER (JIKA ADA)
             * =====================================
             */
            $voucherUser = null;


            $voucherUserId = null;

            if ($request->voucher_user_id) {
                $voucherUser = UserVoucher::where('id', $request->voucher_user_id)
                    ->where('user_id', $user->id)
                    ->where('status', 'claimed')
                    ->lockForUpdate()
                    ->first();

                if (!$voucherUser) {
                    return response()->json([
                        'message' => 'Voucher tidak valid atau sudah digunakan'
                    ], 400);
                }

                $voucherUserId = $voucherUser->id;
            }

            /**
             * =====================================
             * 2️⃣ HITUNG HARGA FINAL (ANTI MANIPULASI)
             * =====================================
             */
            $hargaFinal = $pricing->getFinalPrice(
                $user,
                $kelas->id,
                $voucherUserId
            );

            $diskon = $kelas->harga - $hargaFinal;

            /**
             * =====================================
             * 3️⃣ BUAT RESERVASI
             * =====================================
             */
            $reservasi = Reservasi::create([
                'pelanggan_id' => $user->id,
                'trainer_id'   => $kelas->trainer_id ?? 1,
                'kelas_id'     => $kelas->id,
                'jadwal'       => Carbon::parse($request->date . ' ' . $request->time),
                'status'       => 'pending_payment',
                'status_hadir' => 'belum_hadir',
                'catatan'      => $request->catatan,
            ]);

            /**
             * =====================================
             * 4️⃣ BUAT TRANSAKSI
             * =====================================
             */
            $kodeTrx = 'TRX-' . strtoupper(Str::random(10));

            Transaksi::create([
                'kode_transaksi' => $kodeTrx,
                'user_id'        => $user->id,
                'jenis'          => 'reservasi',
                'source_id'      => $reservasi->id,
                'harga_asli'     => $kelas->harga,
                'diskon'         => max(0, $diskon),
                'total_bayar'    => $hargaFinal,
                'metode'         => 'midtrans',
                'status'         => 'pending',
            ]);

            /**
             * =====================================
             * 5️⃣ MIDTRANS SNAP TOKEN
             * =====================================
             */
            $snapToken = Snap::getSnapToken([
                'transaction_details' => [
                    'order_id'     => $kodeTrx,
                    'gross_amount' => $hargaFinal,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email'      => $user->email,
                ],
                'item_details' => [
                    [
                        'id'       => $kelas->id,
                        'price'    => $hargaFinal,
                        'quantity' => 1,
                        'name'     => $kelas->nama_kelas,
                    ]
                ],
            ]);

            DB::commit();

            return response()->json([
                'snap_token'   => $snapToken,
                'reservasi_id' => $reservasi->id,
                'kode_trx'     => $kodeTrx,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Checkout reservasi error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Checkout gagal',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
