<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'timestamp',
        'full_name',
        'whatsapp_number',
        'email',
        'session_type', // jenis kelas apa : pilates,yoga dll
        'first_visit_date',
        'first_visit_time',   
        'number_of_pax',      
        'special_condition', // seperti misalnya ada keluhan diiisi disini, contoh : pelanggan punya keluhan di leher
        'studio_terms', // persetujuan pelanggan terhadap peraturan studio, nilai nya selalu yes
        'media_consent', // apakah pelanggan boleh di jadikan konten? nilai nya bisa yes bisa no 
    ];
}
