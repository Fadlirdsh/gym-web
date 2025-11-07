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
        'role',
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
    // RELASI
    // =====================

    /**
     * Setiap user bisa memiliki satu data member.
     * (user_id di tabel members)
     */
    public function member()
    {
        return $this->hasOne(Member::class, 'user_id');
    }

    /**
     * Jika di masa depan user dikaitkan ke kelas tertentu
     * misalnya pelanggan tetap punya kelas aktif.
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Relasi opsional untuk kupon pengguna
     */
    public function kupon()
    {
        return $this->hasOne(KuponPengguna::class, 'user_id');
    }

    // =====================
    // HELPER ATTRIBUTE
    // =====================

    /**
     * Cek apakah user ini adalah pelanggan.
     */
    public function isPelanggan()
    {
        return $this->role === 'pelanggan';
    }

    /**
     * Cek apakah user punya member aktif.
     */
    public function hasActiveMember()
    {
        return $this->member && $this->member->status === 'aktif';
    }
}
