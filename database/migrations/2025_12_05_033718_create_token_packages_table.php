<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('token_packages', function (Blueprint $table) {
            $table->id();

            // Jumlah token (3,5,10,20)
            $table->integer('jumlah_token');

            // Tipe kelas
            $table->enum('tipe_kelas', [
                'Pilates Group',
                'Pilates Private',
                'Yoga Group',
                'Yoga Private'
            ]);

            // Harga paket
            $table->integer('harga');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('token_packages');
    }
};
