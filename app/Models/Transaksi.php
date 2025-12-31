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
        'jenis',         // member | reservasi
        'source_id',     // id member / reservasi
        'harga_asli',    // harga sebelum diskon
        'diskon',        // nominal diskon (rupiah)
        'total_bayar',   // total akhir
        'metode',        // midtrans / manual / transfer
        'status',        // pending | success | failed | refund | dll
    ];

    protected $casts = [
        'harga_asli'  => 'integer',
        'diskon'      => 'integer',
        'total_bayar' => 'integer',
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

    public function reservasi()
    {
        return $this->belongsTo(Reservasi::class, 'source_id')
            ->where('jenis', 'reservasi');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'source_id')
            ->where('jenis', 'member');
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
