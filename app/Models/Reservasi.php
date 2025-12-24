<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\VisitLog; // ← penting! tambahkan ini biar dikenali

class Reservasi extends Model
{
    use HasFactory;

    protected $table = 'reservasi';

    protected $fillable = [
        'pelanggan_id',
        'trainer_id',
        'kelas_id',
        'jadwal',
        'status',
        'status_hadir',
        'catatan',
    ];

    protected $casts = [
        'jadwal' => 'datetime',
    ];

    public function pelanggan()
    {
        return $this->belongsTo(User::class, 'pelanggan_id');
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function qrCode()
    {
        return $this->hasOne(QrCode::class);
    }



    // ✅ hanya satu booted(), gabungkan logikanya di sini
    protected static function booted()
    {
        static::updated(function ($reservasi) {
            // cek kalau status berubah ke 'approved'
            if ($reservasi->isDirty('status') && $reservasi->status === 'approved') {

                // pastikan tidak duplikat
                $exists = VisitLog::where('reservasi_id', $reservasi->id)->exists();

                if (!$exists) {
                    VisitLog::create([
                        'reservasi_id' => $reservasi->id,
                        'user_id'      => $reservasi->pelanggan_id,
                        'status'       => 'approved',
                        'catatan'      => 'Reservasi disetujui dan dicatat ke Visit Log.',
                    ]);
                }
            }
        });
    }
}
