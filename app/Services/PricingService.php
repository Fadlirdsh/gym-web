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
     * Hitung harga final (first-time + voucher)
     * Voucher DIANGGAP SUDAH VALID (hasil validasi controller)
     */
    public function getFinalPrice(
        User $user,
        int $kelasId,
        ?int $voucherUserId = null
    ): int {
        $kelas = Kelas::findOrFail($kelasId);
        $harga = (int) $kelas->harga;

        /**
         * ============================
         * FIRST-TIME DISKON
         * ============================
         */
        $belumPernahTransaksi = !Transaksi::where('user_id', $user->id)->exists();
        $masihFirstTime = Carbon::now()->lte(
            Carbon::parse($user->created_at)->addDays(7)
        );

        if (
            $belumPernahTransaksi &&
            $masihFirstTime &&
            $kelas->first_time_aktif &&
            $kelas->first_time_diskon > 0
        ) {
            $diskon = ($harga * $kelas->first_time_diskon) / 100;
            $harga = max(0, (int) ($harga - $diskon));
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
