<?php

namespace App\Enums;

enum VisitLogStatus: string
{
    // ===== EVENT SUKSES =====
    case HADIR = 'hadir';

    // ===== EVENT GAGAL / ANOMALI =====
    case SCAN_ULANG = 'scan_ulang';   // QR sudah dipakai tapi discan lagi
    case EXPIRED    = 'expired';      // QR valid tapi sudah kedaluwarsa
    case INVALID    = 'invalid';      // Token tidak ditemukan / palsu
    case NOT_PAID   = 'not_paid';     // Reservasi belum dibayar

    // ===== EVENT KHUSUS =====
    case MANUAL     = 'manual';       // Check-in via token manual
    case DIBATALKAN = 'dibatalkan';   // Reservasi dibatalkan
    case NO_SHOW    = 'no_show';      // Tidak hadir sampai batas waktu
}
