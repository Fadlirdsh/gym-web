<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();

            // Relasi
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('trainer_id')->constrained('users')->cascadeOnDelete();

            // Bisa jadwal mingguan
            $table->enum('day', [
                'Monday', 'Tuesday', 'Wednesday', 
                'Thursday', 'Friday', 'Saturday', 'Sunday'
            ])->nullable(); // nullable supaya bisa pakai date

            // Bisa jadwal tanggal tertentu (optional)
            $table->date('date')->nullable();

            // Waktu mulai & selesai kelas
            $table->time('start_time');
            $table->time('end_time');

            // Optional info
            $table->string('class_focus')->nullable();

            // Status jadwal aktif/tidak
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
