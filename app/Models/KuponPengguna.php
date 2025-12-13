<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class KuponPengguna extends Model
{
    use HasFactory;

    protected $table = 'kupon_pengguna';

    protected $fillable = [
        'user_id',
        'status',
        'berlaku_hingga',
        'persentase_diskon',
        'harga_setelah_diskon',
    ];

    protected $casts = [
        'berlaku_hingga' => 'datetime',
        'persentase_diskon' => 'decimal:2',
        'harga_setelah_diskon' => 'decimal:2',
    ];

    /**
     * Relasi ke user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Properti apakah kupon masih valid
     */
    public function getMasihBerlakuAttribute()
    {
        return in_array($this->status, ['pending', 'claimed'])
            && Carbon::now()->lte($this->berlaku_hingga);
    }
}
