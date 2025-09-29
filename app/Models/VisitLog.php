<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservasi_id',
        'user_id',
        'status',
        'catatan'
    ];

    public function reservasi()
    {
        return $this->belongsTo(Reservasi::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
