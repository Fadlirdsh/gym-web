<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    /**
     * Kolom yang boleh diisi
     * (SESUAI tabel members yang sudah dipreteli)
     */
    protected $fillable = [
        'user_id',
        'tanggal_mulai',
        'tanggal_berakhir',
        'status',
        'activated_by_transaction_id',
    ];

    /**
     * ======================
     * RELATIONS
     * ======================
     */

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke token per tipe kelas
    public function tokens()
    {
        return $this->hasMany(MemberToken::class);
    }

    /**
     * ======================
     * SCOPES
     * ======================
     */

    // Scope member aktif (status + tanggal)
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif')
            ->where(function ($q) {
                $q->whereNull('tanggal_berakhir')
                    ->orWhere('tanggal_berakhir', '>=', now());
            });
    }
}
