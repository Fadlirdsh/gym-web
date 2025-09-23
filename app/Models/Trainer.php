<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Trainer extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'specialization',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relasi ke Schedule (1 trainer bisa punya banyak jadwal)
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'trainer_id');
    }
}
