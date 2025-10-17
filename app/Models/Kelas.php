<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas'; // nama tabel di database

    protected $fillable = [
        'nama_kelas',
        'tipe_kelas',
        'harga',
        'deskripsi',
        'tipe_paket',
        'jumlah_token',
        'expired_at',
        'kapasitas',
        'gambar',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
    ];

    // Tambahkan accessor ini supaya harga_diskon, diskon_persen, dan SISA_KURSI otomatis ada
    protected $appends = ['harga_diskon', 'diskon_persen', 'sisa_kursi'];

    // --- Accessor Baru untuk Sisa Kursi ---
    /**
     * Accessor untuk sisa_kursi (kapasitas dikurangi jumlah reservasi).
     * Properti ini bergantung pada pemanggilan withCount('reservasi') di Controller.
     */
    public function getSisaKursiAttribute()
    {
        // Pastikan reservasi_count ada (hasil dari withCount('reservasi'))
        return $this->kapasitas - ($this->reservasi_count ?? 0);
    }
    // ----------------------------------------

    public function diskons()
    {
        return $this->hasMany(Diskon::class);
    }

    // Metode ini tidak lagi diperlukan jika menggunakan getHargaDiskonAttribute
    public function hargaSetelahDiskon()
    {
        $diskon = $this->diskons()
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_berakhir', '>=', now())
            ->orderByDesc('persentase')
            ->first();

        if ($diskon) {
            return $this->harga - ($this->harga * $diskon->persentase / 100);
        }

        return $this->harga;
    }

    // Accessor untuk harga_diskon
    public function getHargaDiskonAttribute()
    {
        $diskon = $this->diskons()
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_berakhir', '>=', now())
            ->orderByDesc('persentase')
            ->first();

        return $diskon ? $this->harga - ($this->harga * $diskon->persentase / 100) : $this->harga;
    }

    // Accessor untuk persentase diskon
    public function getDiskonPersenAttribute()
    {
        $diskon = $this->diskons()
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_berakhir', '>=', now())
            ->orderByDesc('persentase')
            ->first();

        return $diskon ? $diskon->persentase : 0;
    }

    public function reservasi()
    {
        return $this->hasMany(\App\Models\Reservasi::class, 'kelas_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'kelas_id');
    }

    public function members()
    {
        return $this->belongsToMany(Member::class, 'kelas_member')
            ->withPivot(['jumlah_token', 'expired_at'])
            ->withTimestamps();
    }

    public function trainer()
    {
        return $this->belongsTo(Trainer::class, 'trainer_id');
    }
}
