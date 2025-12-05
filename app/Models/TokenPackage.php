<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TokenPackage extends Model
{
    protected $fillable = [
        'jumlah_token',
        'tipe_kelas',
        'harga'
    ];
}
