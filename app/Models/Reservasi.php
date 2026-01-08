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
}
