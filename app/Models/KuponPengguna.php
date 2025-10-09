<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class KuponPengguna extends Model
{
    use HasFactory;

    protected $table = 'kupon_pengguna';

    protected $fillable = [
        'user_id',
        'kode_kupon',
        'sudah_dipakai',
        'berlaku_hingga',
    ];

    protected $casts = [
        'sudah_dipakai' => 'boolean',
        'berlaku_hingga' => 'date',
    ];

    /**
     * Relasi ke tabel users
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Akses properti tambahan: apakah kupon masih berlaku?
     */
    public function getMasihBerlakuAttribute()
    {
        return !$this->sudah_dipakai && Carbon::now()->lte($this->berlaku_hingga);
    }
}
