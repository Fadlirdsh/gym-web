<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{

    const STATUS_PENDING  = 'pending';
    const STATUS_PAID     = 'paid';
    const STATUS_FAILED   = 'failed';
    const STATUS_EXPIRED  = 'expired';
    const STATUS_CANCELED = 'canceled';
    const STATUS_REFUNDED = 'refunded';
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

    public function items()
    {
        return $this->hasMany(TransaksiItem::class, 'transaksi_id', 'id');
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

    /*
    |--------------------------------------------------------------------------
    | HISTORI SCOPE
    |--------------------------------------------------------------------------
    */

    public function scopeHistory($query, $userId)
    {
        return $query
            ->where('user_id', $userId)
            ->where('status', self::STATUS_PAID)
            ->orderByDesc('paid_at');
    }
}
