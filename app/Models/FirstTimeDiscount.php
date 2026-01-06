<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FirstTimeDiscount extends Model
{
    protected $fillable = [
        'user_id',
        'expired_at',
        'used_at',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /* =====================
       Helper Logic (PENTING)
    ===================== */

    public function scopeValid($query)
    {
        return $query
            ->whereNull('used_at')
            ->where('expired_at', '>=', now());
    }

    public function markAsUsed()
    {
        $this->update([
            'used_at' => now()
        ]);
    }
}