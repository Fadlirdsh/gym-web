<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tipe_kelas',
        'harga',
        'token_total',
        'token_terpakai',
        'token_sisa',
        'tanggal_mulai',
        'tanggal_berakhir',
        'status',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke kelas
    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_member')
            ->withPivot(['jumlah_token', 'expired_at'])
            ->withTimestamps();
    }

    // Scope member aktif (HANYA cek status & tanggal)
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif')
            ->where('tanggal_berakhir', '>=', now());
    }
}
