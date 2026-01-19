<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiItem extends Model
{
    protected $fillable = [
        'transaksi_id',
        'item_name',
        'price',
        'qty',
        'item_type',
        'item_id',
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }
}
