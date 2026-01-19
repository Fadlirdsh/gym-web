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
     * CHECKOUT RESERVASI
     * =====================================================
     */
    public function checkoutReservasi(Request $request, PricingService $pricing)
    {
        Log::info('CHECKOUT PAYLOAD', $request->all());
        // =========================
        // VALIDASI REQUEST DASAR
        // =========================
        $request->validate([
            'schedule_id'     => 'required|exists:schedules,id',
            'tanggal'         => 'required|date',
            'catatan'         => 'nullable|string',
            'voucher_user_id' => 'nullable|exists:user_vouchers,id',
            'metode'          => 'required|in:midtrans,token',
        ]);

        // 🔒 Token tidak boleh pakai voucher
        if ($request->metode === 'token' && $request->voucher_user_id) {
            return response()->json([
                'message' => 'Voucher tidak dapat digunakan saat pembayaran token'
            ], 422);
        }


        $user = auth('api')->user();

        // =========================
        // AMBIL SCHEDULE
        // =========================
        $schedule = \App\Models\Schedule::with(['kelas', 'trainerShift'])
            ->where('id', $request->schedule_id)
            ->where('is_active', true)
            ->firstOrFail();

        $kelas = $schedule->kelas;

        // =========================
        // 🔴 VALIDASI DOUBLE BOOKING (DI SINI TEMPATNYA)
        // =========================
        $statusesToBlock = $request->metode === 'token'
            ? ['paid']                       // ⬅️ TOKEN
            : ['paid', 'pending_payment'];   // ⬅️ MIDTRANS

        $alreadyBooked = Reservasi::where('pelanggan_id', $user->id)
            ->where('schedule_id', $schedule->id)
            ->whereDate('tanggal', $request->tanggal)
            ->whereIn('status', $statusesToBlock)
            ->exists();

        if ($alreadyBooked) {
            return response()->json([
                'message' => 'Kamu sudah booking kelas ini di tanggal tersebut'
            ], 422);
        }


        // =========================
        // VALIDASI KELAS EXPIRED
        // =========================
        if ($kelas->expired_at) {
            $bookingDate = Carbon::parse($request->tanggal)->startOfDay();
            $expiredAt   = Carbon::parse($kelas->expired_at)->startOfDay();

            if ($bookingDate->gt($expiredAt)) {
                return response()->json([
                    'message' => 'Tanggal booking melebihi masa aktif kelas'
                ], 422);
            }
        }

        // =========================
        // VALIDASI JAM OPERASIONAL
        // =========================
        if ($schedule->end_time > '22:00') {
            return response()->json([
                'message' => 'Jadwal kelas melewati jam operasional'
            ], 422);
        }

        // =========================
        // VALIDASI HARI SHIFT
        // =========================
        $tanggal = Carbon::parse($request->tanggal);
        $map = [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
        ];

        if ($map[$tanggal->dayOfWeekIso] !== $schedule->trainerShift->day) {
            return response()->json([
                'message' => 'Tanggal tidak sesuai dengan jadwal kelas'
            ], 400);
        }

        // =========================
        // VALIDASI SLOT
        // =========================
        if (! $schedule->isAvailable($request->tanggal)) {
            return response()->json([
                'message' => 'Slot kelas sudah penuh'
            ], 400);
        }

        // =========================
        // JALUR TOKEN
        // =========================
        if ($request->metode === 'token') {
            return $this->checkoutReservasiToken($request, $schedule);
        }

        // =========================
        // JALUR MIDTRANS
        // =========================
        DB::beginTransaction();

        try {
            // Voucher
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

            // First time discount
            $hasSuccessReservasi = Transaksi::where('user_id', $user->id)
                ->where('jenis', 'reservasi')
                ->where('status', Transaksi::STATUS_PAID)
                ->exists();

            $firstTimeDiscount = FirstTimeDiscount::where('user_id', $user->id)
                ->valid()
                ->first();

            $canUseFirstTimeDiscount = !$hasSuccessReservasi && $firstTimeDiscount !== null;

            // Hitung harga
            $hargaFinal = $pricing->getFinalPrice(
                $user,
                $kelas->id,
                $voucherUserId,
                $canUseFirstTimeDiscount
            );

            if ($hargaFinal <= 0) {
                return response()->json([
                    'message' => 'Total bayar tidak valid'
                ], 422);
            }

            $diskon = max(0, $kelas->harga - $hargaFinal);

            // Buat reservasi
            $reservasi = Reservasi::create([
                'pelanggan_id' => $user->id,
                'schedule_id'  => $schedule->id,
                'tanggal'      => $request->tanggal,
                'status'       => 'pending_payment',
                'status_hadir' => 'belum_hadir',
                'catatan'      => $request->catatan,
            ]);

            // Transaksi
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

            // Midtrans
            $snapToken = Snap::getSnapToken([
                'transaction_details' => [
                    'order_id'     => $kodeTrx,
                    'gross_amount' => $hargaFinal,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email'      => $user->email,
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
            Log::error('Checkout reservasi error', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Checkout gagal'], 500);
        }


    }

    /**
     * =====================================================
     * CHECKOUT RESERVASI TOKEN
     * =====================================================
     */
    protected function checkoutReservasiToken(Request $request, $schedule)
    {
        $user = auth('api')->user();
        $kelas = $schedule->kelas;

        DB::beginTransaction();

        try {
            $member = $user->member;
            if (!$member || $member->status !== 'aktif') {
                return response()->json([
                    'message' => 'Member tidak aktif'
                ], 403);
            }

            Log::info('TOKEN DEBUG', [
                'member_id'   => $member->id,
                'kelas_nama'  => $kelas->nama_kelas,
            ]);

            $token = \App\Models\MemberToken::where('member_id', $member->id)
                ->where('tipe_kelas', $kelas->tipe_kelas)
                ->lockForUpdate()
                ->first();

            Log::info('TOKEN RESULT', [
                'token' => $token,
            ]);

            if (!$token || $token->token_sisa < 1) {
                return response()->json([
                    'message' => 'Token tidak cukup'
                ], 422);
            }

            $reservasi = Reservasi::create([
                'pelanggan_id' => $user->id,
                'schedule_id'  => $schedule->id,
                'tanggal'      => $request->tanggal,
                'status' => Transaksi::STATUS_PAID,
                'status_hadir' => 'belum_hadir',
                'catatan'      => $request->catatan,
            ]);

            $token->increment('token_terpakai');
            $token->decrement('token_sisa');

            Transaksi::create([
                'kode_transaksi' => 'TRX-' . strtoupper(Str::random(10)),
                'user_id'        => $user->id,
                'jenis'          => 'reservasi',
                'source_id'      => $reservasi->id,
                'harga_asli'     => $kelas->harga,
                'diskon'         => 0,
                'total_bayar'    => 0,
                'metode'         => 'token',
                'status'         => 'paid',
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Reservasi berhasil menggunakan token',
                'reservasi_id' => $reservasi->id,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * =====================================================
     * CHECKOUT MEMBER
     * =====================================================
     */
    public function checkoutMember(Request $request)
    {
        $user = auth('api')->user();

        DB::beginTransaction();

        try {
            $member = Member::firstOrCreate(
                ['user_id' => $user->id],
                ['status' => 'pending']
            );

            if ($member->status === 'aktif') {
                return response()->json([
                    'message' => 'Membership sudah aktif'
                ], 400);
            }

            $hargaMember = 250000;
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

            $snapToken = Snap::getSnapToken([
                'transaction_details' => [
                    'order_id'     => $kodeTrx,
                    'gross_amount' => $hargaMember,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email'      => $user->email,
                ],
            ]);

            DB::commit();

            return response()->json([
                'snap_token' => $snapToken,
                'kode_trx'   => $kodeTrx,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Checkout member gagal'], 500);
        }
    }

    /**
     * =====================================================
     * CHECKOUT TOKEN (BELI TOKEN)
     * =====================================================
     */
    public function checkoutToken(Request $request)
    {
        $user = auth('api')->user();

        $request->validate([
            'token_package_id' => 'required|exists:token_packages,id',
        ]);

        $member = $user->member;

        if (!$member || $member->status !== 'aktif') {
            return response()->json([
                'message' => 'Hanya member aktif yang dapat membeli token'
            ], 403);
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
            ]);

            DB::commit();

            return response()->json([
                'snap_token' => $snapToken,
                'kode_trx'   => $kodeTrx,
                'total'      => $package->harga,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('CHECKOUT TOKEN ERROR', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return response()->json([
                'message' => 'Checkout token gagal',
                'error'   => $e->getMessage(), // ⬅️ DEV MODE
            ], 500);
        }
    }
}
