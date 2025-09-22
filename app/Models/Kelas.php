<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas'; // nama tabel di database

    protected $fillable = [
        'nama_kelas',   // contoh: Pilates Group, Yoga Private
        'tipe_kelas',   // Group / Private
        'harga',        // decimal
        'deskripsi',    // text
        'diskon',       // decimal
        'tipe_paket',   // Package, ClassPass, Drop In
        // 'waktu_mulai',  // jam mulai kelas
        'jumlah_token',
        'expired_at',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'diskon' => 'decimal:2',
        // 'waktu_mulai' => 'datetime', // hanya jam:menit
    ];

    // // relasi ke customer (jika ada pivot table customer_kelas)
    // public function customers()
    // {
    //     return $this->belongsToMany(Customer::class, 'customer_kelas');
    // }
}
