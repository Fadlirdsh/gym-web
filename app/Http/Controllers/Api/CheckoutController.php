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
use App\Models\MemberToken;
use App\Models\TokenPackage;
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
     * =====================================================
     * CHECKOUT RESERVASI (❌ TIDAK DIUBAH)
     * =====================================================
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

            $hargaFinal = $pricing->getFinalPrice(
                $user,
                $kelas->id,
                $voucherUserId
            );

            $diskon = $kelas->harga - $hargaFinal;

            $reservasi = Reservasi::create([
                'pelanggan_id' => $user->id,
                'trainer_id'   => $kelas->trainer_id ?? 1,
                'kelas_id'     => $kelas->id,
                'jadwal'       => Carbon::parse($request->date . ' ' . $request->time),
                'status'       => 'pending_payment',
                'status_hadir' => 'belum_hadir',
                'catatan'      => $request->catatan,
            ]);

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
            ]);

            return response()->json([
                'message' => 'Checkout gagal',
            ], 500);
        }
    }

    /**
     * =====================================================
     * CHECKOUT MEMBER (MEMBER = STATUS SAJA)
     * =====================================================
     */
    public function checkoutMember(Request $request)
    {
        $user = auth()->user();

        DB::beginTransaction();

        try {
            // 1. Pastikan cuma satu member
            $member = Member::firstOrCreate(
                ['user_id' => $user->id],
                ['status' => 'pending']
            );

            if ($member->status === 'aktif') {
                return response()->json([
                    'message' => 'Membership sudah aktif'
                ], 400);
            }

            // 🔴 HARGA MEMBER (FIX)
            $hargaMember = 250000;

            // 2. Buat transaksi
            $kodeTrx = 'TRX-' . strtoupper(Str::random(10));

            Transaksi::create([
                'kode_transaksi' => $kodeTrx,
                'user_id'        => $user->id,
                'jenis'          => 'member',
                'source_id'      => $member->id,
                'harga_asli'     => $hargaMember,
                'diskon'         => 0,
                'total_bayar'    => $hargaMember,
                'metode'         => 'midtrans',
                'status'         => 'pending',
            ]);

            // 3. Midtrans Snap
            $snapToken = Snap::getSnapToken([
                'transaction_details' => [
                    'order_id'     => $kodeTrx,
                    'gross_amount' => $hargaMember,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email'      => $user->email,
                ],
                'item_details' => [
                    [
                        'id'       => 'MEMBER',
                        'price'    => $hargaMember,
                        'quantity' => 1,
                        'name'     => 'Aktivasi Membership',
                    ]
                ],
            ]);

            DB::commit();

            return response()->json([
                'snap_token' => $snapToken,
                'kode_trx'   => $kodeTrx,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Checkout member gagal'
            ], 500);
        }
    }

    /**
     * =====================================================
     * CHECKOUT TOKEN (PAKAI token_packages)
     * =====================================================
     */
    public function checkoutToken(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'token_package_id' => 'required|exists:token_packages,id',
        ]);

        $member = Member::firstOrCreate(
            ['user_id' => $user->id],
            [
                'status' => 'aktif',
                'tanggal_mulai' => now(),
                'tanggal_berakhir' => now()->addMonth(),
            ]
        );

        // kalau member ada tapi nonaktif → aktifkan
        if ($member->status !== 'aktif') {
            $member->update([
                'status' => 'aktif',
                'tanggal_mulai' => now(),
                'tanggal_berakhir' => now()->addMonth(),
            ]);
        }

        $package = TokenPackage::findOrFail($request->token_package_id);

        DB::beginTransaction();

        try {
            $kodeTrx = 'TRX-' . strtoupper(Str::random(10));

            Transaksi::create([
                'kode_transaksi' => $kodeTrx,
                'user_id'        => $user->id,
                'jenis'          => 'token',
                'source_id'      => $package->id,
                'harga_asli'     => $package->harga,
                'diskon'         => 0,
                'total_bayar'    => $package->harga,
                'metode'         => 'midtrans',
                'status'         => 'pending',
            ]);

            $snapToken = Snap::getSnapToken([
                'transaction_details' => [
                    'order_id'     => $kodeTrx,
                    'gross_amount' => $package->harga,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email'      => $user->email,
                ],
                'item_details' => [
                    [
                        'id'       => 'TOKEN-' . $package->id,
                        'price'    => $package->harga,
                        'quantity' => 1,
                        'name'     => "{$package->jumlah_token} Token ({$package->tipe_kelas})",
                    ]
                ],
            ]);

            DB::commit();

            return response()->json([
                'snap_token' => $snapToken,
                'kode_trx'   => $kodeTrx,
                'total'      => $package->harga,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Checkout token gagal',
            ], 500);
        }
    }
}
