<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * Kolom yang bisa diisi mass assignment
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'profile_photo',
    ];

    /**
     * Kolom yang disembunyikan
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
        'membership_status',
        'profile_photo_url',
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
     * Setiap user memiliki satu data member
     */
    public function member()
    {
        return $this->hasOne(Member::class);
    }

    /**
     * ======================
     * HELPER
     * ======================
     */

    public function getMembershipStatusAttribute()
    {
        if (!$this->member) {
            return 'none';
        }

        return $this->member->status;
    }


    /**
     * Relasi kelas (opsional)
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * 🔥 Relasi voucher milik user (pivot: user_vouchers)
     */
    public function vouchers()
    {
        return $this->belongsToMany(
            Voucher::class,
            'user_vouchers',
            'user_id',
            'voucher_id'
        )->withPivot('status');
    }

    // =====================
    // HELPER
    // =====================

    /**
     * Cek apakah user adalah pelanggan
     */
    public function isPelanggan()
    {
        return $this->role === 'pelanggan';
    }

    /**
     * Cek apakah user punya member aktif
     */
    public function hasActiveMember()
    {
        return $this->member && $this->member->status === 'aktif';
    }

    public function trainerProfile()
    {
        return $this->hasOne(TrainerProfile::class, 'user_id');
    }



    public function getProfilePhotoUrlAttribute()
    {
        if (!$this->profile_photo) {
            return null;
        }

        return rtrim(config('app.url'), '/') . '/storage/' . $this->profile_photo;
    }
}
