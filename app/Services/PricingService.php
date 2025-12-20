<?php

namespace App\Services;

use App\Models\User;
use App\Models\Transaksi;
use App\Models\Kelas;
use App\Models\Voucher;
use Carbon\Carbon;

class PricingService
{
    /**
     * Hitung harga final, termasuk first-time diskon dan voucher
     */
    public function getFinalPrice(User $user, int $kelasId, ?int $voucherId = null): int
    {
        $kelas = Kelas::findOrFail($kelasId);
        $harga = (int) $kelas->harga;

        // Diskon first-time
        $belumPernahTransaksi = !Transaksi::where('user_id', $user->id)->exists();
        $masihFirstTime = Carbon::now()->lte(Carbon::parse($user->created_at)->addDays(7));

        if ($belumPernahTransaksi && $masihFirstTime && $kelas->first_time_aktif && $kelas->first_time_diskon > 0) {
            $diskon = ($harga * $kelas->first_time_diskon) / 100;
            $harga = max(0, (int) ($harga - $diskon));
        }

        // Diskon voucher
        if ($voucherId) {
            $voucher = Voucher::find($voucherId);
            if ($voucher) {
                $diskonVoucher = ($harga * $voucher->diskon_persen) / 100;
                $harga = max(0, (int) ($harga - $diskonVoucher));
            }
        }

        return $harga;
    }
}
