<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainers', function (Blueprint $table) {
            $table->id();
            $table->string('name');                          // nama trainer
            $table->string('email')->unique();               // email untuk login
            $table->string('password');                      // password (hash)
            $table->string('phone', 20)->nullable();         // nomor telepon (opsional)
            $table->string('specialization')->nullable();    // fokus latihan (opsional)
            $table->boolean('is_active')->default(true);     // status aktif/nonaktif
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainers');
    }
};
