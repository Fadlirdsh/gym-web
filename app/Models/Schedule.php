<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'kelas_id',
        'trainer_id',
        'day',
        'date',
        'start_time',
        'end_time',
        'class_focus',
        'is_active',
    ];

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

    // OPTIONAL: Hitung durasi
    public function getDurationAttribute()
    {
        $start = strtotime($this->start_time);
        $end   = strtotime($this->end_time);

        return ($end - $start) / 60; // durasi dalam menit
    }

    public function getClassKeyAttribute()
    {
        if (!$this->day || !$this->start_time || !$this->kelas) {
            return null;
        }

        return $this->day . '-' .
            date('h:iA', strtotime($this->start_time)) . '-' .
            str_replace(' ', '', $this->kelas->nama_kelas) . '-' .
            str_replace(' ', '', $this->kelas->tipe_kelas);
    }
}
