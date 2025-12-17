<?php

namespace App\Services;

use App\Models\User;
use App\Models\Transaksi;
use App\Models\Kelas;
use Carbon\Carbon;

class PricingService
{
    public function getFinalPrice(User $user, int $kelasId): int
    {
        $kelas = Kelas::findOrFail($kelasId);

        $harga = (int) $kelas->harga;

        $belumPernahTransaksi =
            !Transaksi::where('user_id', $user->id)->exists();

        $masihFirstTime =
            Carbon::now()->lte(
                Carbon::parse($user->created_at)->addDays(7)
            );

        if (
            $belumPernahTransaksi &&
            $masihFirstTime &&
            $kelas->first_time_aktif &&
            $kelas->first_time_diskon > 0
        ) {
            $diskon = ($harga * $kelas->first_time_diskon) / 100;
            return max(0, (int) ($harga - $diskon));
        }

        return $harga;
    }
}
