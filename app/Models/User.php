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
        'role', // opsional, kalau kamu menyimpan role user
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // =====================
    // JWT
    // =====================
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    // =====================
    // Relasi
    // =====================

    // 1️⃣ Relasi ke Member
    public function member()
    {
        return $this->hasOne(Member::class);
    }

    // 2️⃣ Relasi ke kelas via pivot (kelas_member)
    public function kelas()
    {
        return $this->belongsToMany(
            \App\Models\Kelas::class,
            'kelas_member',   // nama tabel pivot
            'member_id',      // FK di pivot untuk member
            'kelas_id'        // FK di pivot untuk kelas
        )
        ->withPivot(['jumlah_token', 'expired_at'])
        ->withTimestamps();
    }

    // 3️⃣ Relasi ke kupon
    public function kupon()
    {
        return $this->hasOne(KuponPengguna::class, 'user_id');
    }
}
