<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservasi extends Model
{
    use HasFactory;

    protected $table = 'reservasi';

    protected $fillable = [
        'pelanggan_id',
        'schedule_id',
        'tanggal',
        'status',        // pending_payment | paid | canceled
        'status_hadir',  // belum_hadir | hadir
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /*
    |------------------------------------------------------------------
    | RELATIONSHIPS
    |------------------------------------------------------------------
    */

    // User (pelanggan) yang melakukan booking
    public function pelanggan()
    {
        return $this->belongsTo(User::class, 'pelanggan_id');
    }

    public function kelas()
    {
        return $this->belongsTo(\App\Models\Kelas::class, 'kelas_id');
    }

    public function transaksi()
    {
        return $this->hasOne(\App\Models\Transaksi::class, 'source_id')
            ->where('jenis', 'reservasi');
    }


    // Slot jadwal yang dibooking
    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    // QR Code untuk absensi (jika ada)
    public function qrCode()
    {
        return $this->hasOne(QrCode::class);
    }

    /*
    |------------------------------------------------------------------
    | STATUS HELPERS
    |------------------------------------------------------------------
    */

    public function isPendingPayment(): bool
    {
        return $this->status === 'pending_payment';
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isCanceled(): bool
    {
        return $this->status === 'canceled';
    }
    /*
    |--------------------------------------------------------------------------
    | HISTORI HELPERS (SAFE)
    |--------------------------------------------------------------------------
    */

    public function getFinalStatusAttribute(): string
    {
        if ($this->status === 'canceled') {
            return 'canceled';
        }

        if ($this->status === 'paid' && $this->status_hadir === 'hadir') {
            return 'attended';
        }

        if ($this->status === 'paid' && $this->status_hadir === 'belum_hadir') {
            return 'no_show';
        }

        return $this->status;
    }

    public function scopeHistory($query, $userId)
    {
        return $query
            ->where('pelanggan_id', $userId)
            ->whereIn('status', ['paid', 'canceled'])
            ->orderBy('tanggal', 'desc');
    }
}
