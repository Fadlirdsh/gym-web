<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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

    /**
     * Relasi ke tabel kelas (opsional, jika ada tabel kelas)
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    /**
     * Cek apakah voucher masih berlaku (aktif dan belum kedaluwarsa)
     */
    public function isValid()
    {
        $today = Carbon::today();
        return $this->status === 'aktif' &&
               $today->between($this->tanggal_mulai, $this->tanggal_akhir) &&
               $this->kuota > 0;
    }

    /**
     * Kurangi kuota setiap kali voucher digunakan
     */
    public function useVoucher()
    {
        if ($this->kuota > 0) {
            $this->kuota -= 1;
            if ($this->kuota == 0) {
                $this->status = 'nonaktif';
            }
            $this->save();
        }
    }
}
