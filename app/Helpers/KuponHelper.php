<?php

namespace App\Helpers;

class KuponHelper
{
    public static function diskonPerTipe()
    {
        return [
            'Pilates Group'   => 40,
            'Pilates Private' => 15,
            'Yoga Group'      => 15,
            'Yoga Private'    => 0,
        ];
    }

    public static function hitungDiskon($tipeKelas, $harga)
    {
        $diskon = self::diskonPerTipe()[$tipeKelas] ?? 0;

        if ($diskon === 0) {
            return $harga; // tidak bisa pakai kupon
        }

        return $harga - ($harga * ($diskon / 100));
    }
}
