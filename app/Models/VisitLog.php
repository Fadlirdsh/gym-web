<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitLog extends Model
{
    use HasFactory;

    /**
     * VisitLog = catatan kejadian hadir
     * Status & kelas diambil dari reservasi
     */
    protected $fillable = [
        'reservasi_id',
        'user_id',
        'catatan',
        'checkin_at',
    ];

    protected $casts = [
        'checkin_at' => 'datetime',
    ];

    /* ==============================
     | RELATIONS
     ============================== */

    public function reservasi()
    {
        return $this->belongsTo(Reservasi::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Kelas DIAMBIL dari reservasi
     */
    public function kelas()
    {
        return $this->hasOneThrough(
            Kelas::class,
            Reservasi::class,
            'id',        // FK di reservasi
            'id',        // PK di kelas
            'reservasi_id',
            'kelas_id'
        );
    }

    /* ==============================
     | SCOPES
     ============================== */

    /**
     * Hadir pada tanggal tertentu
     */
    public function scopeHadirOnDate($query, $date)
    {
        return $query->whereHas('reservasi', function ($q) {
            $q->where('status', 'paid')
                ->where('status_hadir', 'hadir');
        })->whereDate('checkin_at', $date);
    }

    /**
     * Hadir dalam rentang tanggal
     */
    public function scopeHadirBetween($query, $startDate, $endDate)
    {
        return $query->whereHas('reservasi', function ($q) {
            $q->where('status', 'paid')
                ->where('status_hadir', 'hadir');
        })->whereBetween('checkin_at', [$startDate, $endDate]);
    }
}
