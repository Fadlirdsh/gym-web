<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'tipe_kelas',
        'token_total',
        'token_terpakai',
        'token_sisa',
        'source',          // admin / midtrans
        'transaction_id',  // nullable
    ];

    /**
     * ======================
     * RELATIONS
     * ======================
     */

    // Token ini milik member siapa
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    // Token ini berasal dari transaksi apa (jika ada)
    public function transaction()
    {
        return $this->belongsTo(Transaksi::class);
    }

    /**
     * ======================
     * HELPER
     * ======================
     */

    // Ambil token yang masih bisa dipakai
    public function scopeAvailable($query)
    {
        return $query->where('token_sisa', '>', 0);
    }
}
