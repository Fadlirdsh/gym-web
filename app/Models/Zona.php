<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zona extends Model
{
    use HasFactory;
    protected $table = 'tbl_zona';
    protected $fillable = ['nama_zona', 'latitude', 'longitude', 'radius_m'];
}
