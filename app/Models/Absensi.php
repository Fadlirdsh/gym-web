<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;
    protected $table = 'tbl_absensi';
    protected $fillable = ['user_id', 'zona_id', 'waktu'];

    public function zona() {
        return $this->belongsTo(Zona::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
