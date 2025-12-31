<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainerShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'trainer_id',
        'day',
        'shift_start',
        'shift_end',
        'is_active',
    ];

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'trainer_shift_id');
    }
}
