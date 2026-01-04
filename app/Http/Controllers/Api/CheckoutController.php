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
use App\Models\Member;
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

    public function checkoutMember(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'tipe_kelas' => 'required|in:Pilates Group,Pilates Private,Yoga Group,Yoga Private',
            'harga'      => 'required|integer|min:0',
        ]);

        DB::beginTransaction();

        try {
            /**
             * 1️⃣ BUAT MEMBER (PENDING)
             */
            $member = Member::create([
                'user_id' => $user->id,
                'tipe_kelas' => $request->tipe_kelas,
                'harga' => $request->harga,
                'token_total' => 0,
                'token_terpakai' => 0,
                'token_sisa' => 0,
                'status' => 'pending',
            ]);

            /**
             * 2️⃣ BUAT TRANSAKSI MEMBER
             */
            $kodeTrx = 'TRX-' . strtoupper(Str::random(10));

            Transaksi::create([
                'kode_transaksi' => $kodeTrx,
                'user_id' => $user->id,
                'jenis' => 'member',
                'source_id' => $member->id, // ⬅️ INI KUNCI
                'harga_asli' => $request->harga,
                'diskon' => 0,
                'total_bayar' => $request->harga,
                'metode' => 'midtrans',
                'status' => 'pending',
            ]);

            /**
             * 3️⃣ SNAP MIDTRANS
             */
            $snapToken = Snap::getSnapToken([
                'transaction_details' => [
                    'order_id' => $kodeTrx,
                    'gross_amount' => $request->harga,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                ],
                'item_details' => [
                    [
                        'id' => 'MEMBER',
                        'price' => $request->harga,
                        'quantity' => 1,
                        'name' => 'Membership Gym',
                    ]
                ],
            ]);

            DB::commit();

            return response()->json([
                'snap_token' => $snapToken,
                'member_id' => $member->id,
                'kode_trx' => $kodeTrx,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Checkout member gagal',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function checkoutToken(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'token_qty' => 'required|integer|in:3,5,10',
        ]);

        // Ambil member aktif
        $member = Member::where('user_id', $user->id)
            ->where('status', 'aktif')
            ->first();

        if (!$member) {
            return response()->json([
                'message' => 'Membership tidak aktif'
            ], 400);
        }

        /**
         * 🔐 1️⃣ Ambil harga kelas sebagai BASE PRICE
         * (contoh: Pilates Group = 250.000)
         */
        $hargaKelas = Kelas::where('tipe_kelas', $member->tipe_kelas)
            ->value('harga');

        if (!$hargaKelas) {
            return response()->json([
                'message' => 'Harga kelas tidak ditemukan'
            ], 400);
        }

        /**
         * 🔐 2️⃣ Tentukan diskon berdasarkan tipe_kelas
         */
        $diskonPersen = match ($member->tipe_kelas) {
            'Pilates Group'   => 0.27,
            'Pilates Private' => 0.15,
            'Yoga Group'      => 0.25,
            'Yoga Private'    => 0.10,
            default           => 0,
        };

        /**
         * 🔐 3️⃣ Hitung harga token
         */
        $tokenQty     = $request->token_qty;
        $hargaNormal  = $hargaKelas * $tokenQty;
        $hargaToken   = (int) ceil($hargaNormal * (1 - $diskonPersen));

        DB::beginTransaction();

        try {
            $kodeTrx = 'TRX-' . strtoupper(Str::random(10));

            Transaksi::create([
                'kode_transaksi' => $kodeTrx,
                'user_id'        => $user->id,
                'jenis'          => 'token',
                'source_id'      => $member->id,
                'harga_asli'     => $hargaNormal,
                'diskon'         => $hargaNormal - $hargaToken,
                'total_bayar'    => $hargaToken,
                'metode'         => 'midtrans',
                'status'         => 'pending',
                'meta' => [
                    'token_qty'     => $tokenQty,
                    'harga_kelas'   => $hargaKelas,
                    'harga_normal'  => $hargaNormal,
                    'diskon_persen' => $diskonPersen,
                    'tipe_kelas'    => $member->tipe_kelas,
                ],
            ]);

            $snapToken = Snap::getSnapToken([
                'transaction_details' => [
                    'order_id'     => $kodeTrx,
                    'gross_amount' => $hargaToken,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email'      => $user->email,
                ],
                'item_details' => [
                    [
                        'id'       => 'TOKEN',
                        'price'    => $hargaToken,
                        'quantity' => 1,
                        'name'     => "Token {$tokenQty}x ({$member->tipe_kelas})",
                    ]
                ],
            ]);

            DB::commit();

            return response()->json([
                'snap_token' => $snapToken,
                'kode_trx'   => $kodeTrx,
                'total'      => $hargaToken,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Checkout token gagal',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
