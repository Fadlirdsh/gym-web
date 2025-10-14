<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tanggal_mulai',
        'tanggal_berakhir',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelas()
    {
        return $this->belongsToMany(Kelas::class, 'kelas_member')
            ->withPivot(['jumlah_token', 'expired_at'])
            ->withTimestamps();
    }

    public function members()
    {
        return $this->belongsToMany(Member::class, 'kelas_member')
            ->withPivot(['jumlah_token', 'expired_at'])
            ->withTimestamps();
    }
}
