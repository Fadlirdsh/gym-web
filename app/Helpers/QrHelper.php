<?php

namespace App\Helpers;

class QrHelper
{
    public static function alias(string $uuid): string
    {
        return 'ABSEN-' . strtoupper(substr(hash('crc32b', $uuid), 0, 4));
    }
}
