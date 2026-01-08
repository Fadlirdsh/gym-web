<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Reservasi;
use App\Models\Transaksi;
use App\Models\UserVoucher;
use App\Models\Member;
use App\Models\TokenPackage;
use App\Services\PricingService;
use Carbon\Carbon;
use App\Models\FirstTimeDiscount;
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
            'schedule_id'     => 'required|exists:schedules,id',
            'tanggal'         => 'required|date',
            'catatan'         => 'nullable|string',
            'voucher_user_id' => 'nullable|exists:user_vouchers,id',
        ]);

        $user = auth()->user();

        // Ambil schedule + relasi penting
        $schedule = \App\Models\Schedule::with(['kelas', 'trainerShift'])
            ->where('id', $request->schedule_id)
            ->where('is_active', true)
            ->firstOrFail();

        // =========================
        // 🔒 VALIDASI: EXPIRED KELAS
        // =========================
        $kelas = $schedule->kelas;

        if ($kelas->expired_at) {
            $bookingDate = Carbon::parse($request->tanggal)->startOfDay();
            $expiredAt   = Carbon::parse($kelas->expired_at)->startOfDay();

            if ($bookingDate->gt($expiredAt)) {
                return response()->json([
                    'message' => 'Tanggal booking melebihi masa aktif kelas'
                ], 422);
            }
        }

        //tolak kelas yang selesai setelah jam tutup
        if ($schedule->end_time > '22:00') {
            return response()->json([
                'message' => 'Jadwal kelas melewati jam operasional'
            ], 422);
        }


        // 🔒 VALIDASI: tanggal cocok dengan hari shift
        $tanggal = Carbon::parse($request->tanggal);
        if ($tanggal->dayOfWeekIso !== $schedule->trainerShift->day) {
            return response()->json([
                'message' => 'Tanggal tidak sesuai dengan jadwal kelas'
            ], 400);
        }

        // 🔒 VALIDASI: slot masih tersedia
        if (! $schedule->isAvailable($request->tanggal)) {
            return response()->json([
                'message' => 'Slot kelas sudah penuh'
            ], 400);
        }

        DB::beginTransaction();

        try {
            // =========================
            // VOUCHER
            // =========================
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

            // =========================
            // FIRST TIME DISCOUNT
            // =========================
            $hasSuccessReservasi = Transaksi::where('user_id', $user->id)
                ->where('jenis', 'reservasi')
                ->where('status', 'success')
                ->exists();

            $firstTimeDiscount = FirstTimeDiscount::where('user_id', $user->id)
                ->valid()
                ->first();

            $canUseFirstTimeDiscount = !$hasSuccessReservasi && $firstTimeDiscount !== null;

            // =========================
            // HITUNG HARGA
            // =========================
            $kelas = $schedule->kelas;

            $hargaFinal = $pricing->getFinalPrice(
                $user,
                $kelas->id,
                $voucherUserId,
                $canUseFirstTimeDiscount
            );

            $diskon = max(0, $kelas->harga - $hargaFinal);

            // =========================
            // BUAT RESERVASI (PENTING: SEBELUM PAYMENT)
            // =========================
            $reservasi = Reservasi::create([
                'pelanggan_id' => $user->id,
                'schedule_id'  => $schedule->id,
                'tanggal'      => $request->tanggal,
                'status'       => 'pending_payment',
                'status_hadir' => 'belum_hadir',
                'catatan'      => $request->catatan,
            ]);

            // =========================
            // TRANSAKSI
            // =========================
            $kodeTrx = 'TRX-' . strtoupper(Str::random(10));

            Transaksi::create([
                'kode_transaksi' => $kodeTrx,
                'user_id'        => $user->id,
                'jenis'          => 'reservasi',
                'source_id'      => $reservasi->id,
                'harga_asli'     => $kelas->harga,
                'diskon'         => $diskon,
                'total_bayar'    => $hargaFinal,
                'metode'         => 'midtrans',
                'status'         => 'pending',
            ]);

            // =========================
            // MIDTRANS
            // =========================
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
                        'id'       => 'SCHEDULE-' . $schedule->id,
                        'price'    => $hargaFinal,
                        'quantity' => 1,
                        'name'     => $kelas->nama_kelas . ' (' .
                            $schedule->start_time . '-' .
                            $schedule->end_time . ')',
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
