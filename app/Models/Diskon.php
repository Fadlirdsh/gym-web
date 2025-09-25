<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diskon extends Model
{
    use HasFactory;
    
    protected $table = 'diskons';

    protected $fillable = [
        'kelas_id',
        'nama_diskon',
        'persentase',
        'tanggal_mulai',
        'tanggal_berakhir',
    ];

    // Relasi ke Kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}
