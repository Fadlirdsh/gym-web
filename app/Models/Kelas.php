<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'nama_kelas',
        'tipe_kelas',   // ENUM: Pilates Group, Pilates Private, Yoga Group, Yoga Private
        'harga',
        'deskripsi',
        'expired_at',
        'kapasitas',
        'gambar',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'expired_at' => 'datetime',
    ];

    protected $appends = ['harga_diskon', 'diskon_persen', 'sisa_kursi'];

    // --- Accessor: Sisa Kursi ---
    public function getSisaKursiAttribute()
    {
        return $this->kapasitas - ($this->reservasi_count ?? 0);
    }

    public function diskons()
    {
        return $this->hasMany(Diskon::class);
    }

    public function hargaSetelahDiskon()
    {
        $diskon = $this->diskons()
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_berakhir', '>=', now())
            ->orderByDesc('persentase')
            ->first();

        return $diskon
            ? $this->harga - ($this->harga * $diskon->persentase / 100)
            : $this->harga;
    }

    public function getHargaDiskonAttribute()
    {
        $diskon = $this->diskons()
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_berakhir', '>=', now())
            ->orderByDesc('persentase')
            ->first();

        return $diskon
            ? $this->harga - ($this->harga * $diskon->persentase / 100)
            : $this->harga;
    }

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

    public function scopeAktif($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expired_at')
                ->orWhere('expired_at', '>', now());
        });
    }

    public function qr()
    {
        return $this->hasOne(QrCode::class);
    }
}
