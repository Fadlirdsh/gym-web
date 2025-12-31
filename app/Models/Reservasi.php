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
        'trainer_id',
        'kelas_id',
        'jadwal',
        'status',        // pending_payment | paid | canceled
        'status_hadir',  // belum_hadir | hadir
        'catatan',
    ];

    protected $casts = [
        'jadwal' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function pelanggan()
    {
        return $this->belongsTo(User::class, 'pelanggan_id');
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function qrCode()
    {
        return $this->hasOne(QrCode::class);
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS HELPERS (OPSIONAL TAPI DISARANKAN)
    |--------------------------------------------------------------------------
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
