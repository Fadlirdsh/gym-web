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
        'trainer_id',
        'day',
        'start_time',
        'end_time',
        'class_focus',
        'is_active',
    ];

    /**
     * =========================
     * RELATIONS
     * =========================
     */

    // Relasi ke shift kerja trainer
    public function trainerShift()
    {
        return $this->belongsTo(TrainerShift::class, 'trainer_shift_id');
    }

    // Relasi ke kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    // Relasi ke trainer (users)
    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    /**
     * =========================
     * ACCESSORS
     * =========================
     */

    // Hitung durasi kelas (menit)
    public function getDurationAttribute()
    {
        if (!$this->start_time || !$this->end_time) {
            return null;
        }

        $start = strtotime($this->start_time);
        $end   = strtotime($this->end_time);

        return ($end - $start) / 60;
    }

    /**
     * Identifier kelas (OPTIONAL)
     * ⚠️ Jangan dipakai untuk logic penting
     */
    public function getClassKeyAttribute()
    {
        if (!$this->day || !$this->start_time || !$this->kelas) {
            return null;
        }

        return sprintf(
            '%s-%s-%s-%s',
            $this->day,
            date('H:i', strtotime($this->start_time)),
            str_replace(' ', '', $this->kelas->nama_kelas),
            str_replace(' ', '', $this->kelas->tipe_kelas)
        );
    }
}
