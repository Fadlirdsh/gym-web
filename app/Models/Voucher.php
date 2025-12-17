<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
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

    /**
     * Relasi ke tabel kelas
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * 🔥 Relasi ke user melalui pivot user_vouchers
     */
    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'user_vouchers',
            'voucher_id',
            'user_id'
        )
        ->withPivot('status')
        ->withTimestamps();
    }

    // =====================
    // HELPER
    // =====================

    /**
     * Cek apakah voucher masih berlaku
     */
    public function isValid()
    {
        return $this->status === 'aktif'
            && now()->between($this->tanggal_mulai, $this->tanggal_akhir)
            && $this->kuota > 0;
    }

    /**
     * Kurangi kuota saat voucher digunakan
     */
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
