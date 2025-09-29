<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservasi_id',
        'user_id',
        'status',
        'catatan'
    ];

    public function reservasi()
    {
        return $this->belongsTo(Reservasi::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->where('role', 'pelanggan');
    }
    // VisitLog.php
    public function scopeApprovedOnDate($query, $date)
    {
        return $query->whereHas('reservasi', function ($q) {
            $q->where('status', 'approved');
        })->whereDate('created_at', $date);
    }

    public function scopeApprovedBetween($query, $startDate, $endDate)
    {
        return $query->whereHas('reservasi', function ($q) {
            $q->where('status', 'approved');
        })->whereBetween('created_at', [$startDate, $endDate]);
    }
}
