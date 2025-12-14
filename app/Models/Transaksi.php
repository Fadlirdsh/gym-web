<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksis';

    protected $fillable = [
        'kode_transaksi',
        'user_id',
        'jenis',
        'source_id',
        'jumlah',
        'diskon_persen',           // 🔥 baru
        'total_setelah_diskon',    // 🔥 baru
        'metode',
        'status',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'diskon_persen' => 'integer',        // 🔥 baru
        'total_setelah_diskon' => 'integer', // 🔥 baru
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    // Relasi ke user (pembayar)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dinamis ke sumber transaksi (Member atau Reservasi)
    public function source()
    {
        if ($this->jenis === 'member') {
            return $this->belongsTo(Member::class, 'source_id');
        }

        if ($this->jenis === 'reservasi') {
            return $this->belongsTo(Reservasi::class, 'source_id');
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR
    |--------------------------------------------------------------------------
    */

    // Format jumlah
    public function getJumlahFormatAttribute()
    {
        return 'Rp ' . number_format($this->jumlah, 0, ',', '.');
    }

    // 🔥 Format total setelah diskon (kalau ada)
    public function getTotalSetelahDiskonFormatAttribute()
    {
        if (!$this->total_setelah_diskon) {
            return $this->jumlah_format; // fallback
        }

        return 'Rp ' . number_format($this->total_setelah_diskon, 0, ',', '.');
    }
}
