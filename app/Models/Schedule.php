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
        'time',
        'class_focus',
        'is_active',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }
    public function reservasi()
    {
        return $this->hasMany(\App\Models\Reservasi::class, 'kelas_id', 'kelas_id');
    }
}
