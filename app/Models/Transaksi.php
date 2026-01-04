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
        'jenis',       // member | token | reservasi
        'source_id',   // id member / reservasi
        'harga_asli',
        'diskon',
        'total_bayar',
        'metode',
        'status',
    ];

    protected $casts = [
        'harga_asli'  => 'integer',
        'diskon'      => 'integer',
        'total_bayar' => 'integer',
        'meta'        => 'array',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Untuk transaksi reservasi
     */
    public function reservasi()
    {
        return $this->belongsTo(Reservasi::class, 'source_id');
    }

    /**
     * Untuk transaksi member & token
     */
    public function member()
    {
        return $this->belongsTo(Member::class, 'source_id');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS (FORMAT RUPIAH)
    |--------------------------------------------------------------------------
    */

    public function getHargaAsliFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->harga_asli, 0, ',', '.');
    }

    public function getDiskonFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->diskon, 0, ',', '.');
    }

    public function getTotalBayarFormatAttribute(): string
    {
        return 'Rp ' . number_format($this->total_bayar, 0, ',', '.');
    }
}
