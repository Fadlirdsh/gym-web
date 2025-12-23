<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Kelas;
use App\Models\User;

class Voucher extends Model
{
    use HasFactory;

    protected $table = 'vouchers';

    protected $fillable = [
        'kode',
        'deskripsi',
        'diskon_persen',
        'kelas_id',
        'role_target',
        'tanggal_mulai',
        'tanggal_akhir',
        'kuota',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_akhir' => 'date',
    ];

    // =====================
    // RELASI
    // =====================
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'user_vouchers',
            'voucher_id',
            'user_id'
        )->withPivot('status')->withTimestamps();
    }

    // =====================
    // SCOPE
    // =====================
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    // =====================
    // HELPER
    // =====================
    public function isValid()
    {
        return $this->status === 'aktif'
            && now()->between(
                $this->tanggal_mulai->startOfDay(),
                $this->tanggal_akhir->endOfDay()
            )
            && $this->kuota > 0;
    }

    public function useVoucher()
    {
        if ($this->kuota > 0) {
            $this->decrement('kuota');

            if ($this->kuota <= 0) {
                $this->update(['status' => 'nonaktif']);
            }
        }
    }
}
