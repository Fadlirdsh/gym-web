<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainer_shift_id',
        'kelas_id',
        'start_time',
        'end_time',
        'capacity',
        'class_focus',
        'is_active',
    ];

    /*
    |------------------------------------------------------------------
    | RELATIONSHIPS
    |------------------------------------------------------------------
    */

    // Shift kerja trainer (SUMBER hari & trainer)
    public function trainerShift()
    {
        return $this->belongsTo(TrainerShift::class, 'trainer_shift_id');
    }
    
    // Kelas yang dijadwalkan
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    // Semua reservasi yang menggunakan slot ini
    public function reservasi()
    {
        return $this->hasMany(Reservasi::class, 'schedule_id');
    }

    /*
    |------------------------------------------------------------------
    | ACCESSORS & HELPERS
    |------------------------------------------------------------------
    */

    // Durasi kelas (menit)
    public function getDurationAttribute(): ?int
    {
        if (!$this->start_time || !$this->end_time) {
            return null;
        }

        $start = strtotime($this->start_time);
        $end   = strtotime($this->end_time);

        return ($end - $start) / 60;
    }

    // Hitung sisa slot pada tanggal tertentu
    public function sisaSlot(string $tanggal): int
    {
        $terpakai = $this->reservasi()
            ->where('tanggal', $tanggal)
            ->where('status', '!=', 'canceled')
            ->count();

        return max(0, $this->capacity - $terpakai);
    }

    // Apakah slot masih tersedia pada tanggal tertentu
    public function isAvailable(string $tanggal): bool
    {
        return $this->sisaSlot($tanggal) > 0;
    }
}
