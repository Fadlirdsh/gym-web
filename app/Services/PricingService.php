<?php

namespace App\Services;

use App\Models\User;
use App\Models\Transaksi;
use App\Models\Kelas;
use App\Models\UserVoucher;
use Carbon\Carbon;

class PricingService
{

    /**
     * FIRST TIME DISCOUNT (FIXED BUSINESS RULE)
     * persen (%)
     */
    private const FIRST_TIME_DISCOUNT_MAP = [
        'Pilates Group'   => 40.00,
        'Pilates Private' => 13.64,
        'Yoga Group'      => 16.67,
        // 'Yoga Private' => 0 (tidak ada diskon)
    ];

    /**
     * Hitung harga final (first-time + voucher)
     * Voucher DIANGGAP SUDAH VALID (hasil validasi controller)
     */
    public function getFinalPrice(
        User $user,
        int $kelasId,
        ?int $voucherUserId = null,
        bool $useFirstTimeDiscount = false
    ): int {
        $kelas = Kelas::findOrFail($kelasId);
        $harga = (int) $kelas->harga;


        /**
         * ============================
         * FIRST-TIME DISCOUNT (AUTO)
         * ============================
         */
        if ($useFirstTimeDiscount) {
            $tipeKelas = $kelas->tipe_kelas;

            $diskonPersen = self::FIRST_TIME_DISCOUNT_MAP[$tipeKelas] ?? 0;

            if ($diskonPersen > 0) {
                $diskon = ($harga * $diskonPersen) / 100;
                $harga  = max(0, (int) round($harga - $diskon));
            }
        }

        /**
         * ============================
         * VOUCHER DISKON (OPSIONAL)
         * ============================
         */
        if ($voucherUserId) {
            $userVoucher = UserVoucher::with('voucher')
                ->where('id', $voucherUserId)
                ->where('user_id', $user->id)
                ->where('status', 'claimed')
                ->first();

            if ($userVoucher && $userVoucher->voucher) {
                $voucher = $userVoucher->voucher;

                // Validasi tambahan
                if (
                    $voucher->status === 'aktif' &&
                    now()->between($voucher->tanggal_mulai, $voucher->tanggal_akhir) &&
                    (
                        is_null($voucher->kelas_id) ||
                        $voucher->kelas_id === $kelas->id
                    )
                ) {
                    $diskonVoucher = ($harga * $voucher->diskon_persen) / 100;
                    $harga = max(0, (int) ($harga - $diskonVoucher));
                }
            }
        }

        return $harga;
    }
}
