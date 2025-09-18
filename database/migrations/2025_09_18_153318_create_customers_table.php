<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->timestamp('timestamp')->nullable();
            $table->string('full_name');
            $table->string('whatsapp_number', 30)->nullable();
            $table->string('email')->unique();
            $table->string('session_type')->nullable();
            $table->date('first_visit_date')->nullable();   // Tanggal
            $table->time('first_visit_time')->nullable();   // Jam kunjungan
            $table->unsignedInteger('number_of_pax')->nullable(); // Jumlah pax
            $table->string('special_condition')->nullable();
            $table->text('studio_terms')->nullable();
            $table->string('media_consent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
