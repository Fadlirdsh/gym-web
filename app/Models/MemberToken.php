<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberToken extends Model
{
    protected $fillable = [
        'member_id',
        'tipe_kelas',
        'token_total',
        'token_terpakai',
        'token_sisa',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
