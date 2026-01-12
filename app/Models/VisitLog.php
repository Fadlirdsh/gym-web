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
        'status',
        'catatan',
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

    /* ==============================
     | SCOPES
     ============================== */

    /**
     * Hadir pada tanggal tertentu
     */
    public function scopeHadirOnDate($query, $date)
    {
        return $query
            ->where('status', \App\Enums\VisitLogStatus::HADIR->value)
            ->whereDate('created_at', $date);
    }

    public function scopeHadirBetween($query, $startDate, $endDate)
    {
        return $query
            ->where('status', \App\Enums\VisitLogStatus::HADIR->value)
            ->whereBetween('created_at', [$startDate, $endDate]);
    }
}
