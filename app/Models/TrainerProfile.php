<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainerProfile extends Model
{
    protected $table = 'trainer_profiles';

    protected $fillable = [
        'user_id',
        'photo',
        'headline',
        'bio',
        'skills',
        'experience_years',
        'certifications',
    ];

    protected $casts = [
        'skills' => 'array',
        'certifications' => 'array',
    ];

    /**
     * Append photo_url to JSON response
     */
    protected $appends = ['photo_url'];

    /**
     * Accessor for full photo URL
     */
    public function getPhotoUrlAttribute()
    {
        return $this->photo
            ? asset('storage/' . $this->photo)
            : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
