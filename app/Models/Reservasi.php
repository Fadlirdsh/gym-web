<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservasi extends Model
{
    use HasFactory;

    // nama tabel (kalau tidak pakai plural Laravel default)
    protected $table = 'reservasi';

    // field yang bisa diisi (mass assignment)
    protected $fillable = [
        'pelanggan_id',
        'trainer_id',
        'kelas_id',
        'jadwal',
        'status',
        'catatan',
        // kalau nanti aktifkan pembayaran, tambahin juga:
        // 'metode_pembayaran',
        // 'status_pembayaran',
    ];

    //  relasi ke user sebagai pelanggan
    public function pelanggan()
    {
        return $this->belongsTo(User::class, 'pelanggan_id');
    }

    //  relasi ke user sebagai trainer
    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    //  relasi ke kelas
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}
