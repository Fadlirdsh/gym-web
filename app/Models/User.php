<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'kelas_id', // tambahkan ini supaya mass assignment kelas_id bisa
        'role',     // opsional, kalau kamu menyimpan role user
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Relasi ke model Kelas
     */
    public function kelas()
    {
        return $this->belongsTo(\App\Models\Kelas::class, 'kelas_id');
    }

    // =====================
    // Implementasi JWT
    // =====================
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
