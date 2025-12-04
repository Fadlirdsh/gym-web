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

            // Relasi ke kelas (Pilates/Yoga Group)
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();

            // Relasi ke trainer
            $table->foreignId('trainer_id')->constrained('users')->cascadeOnDelete();

            // Jadwal mingguan (Monday–Sunday)
            $table->enum('day', [
                'Monday', 'Tuesday', 'Wednesday',
                'Thursday', 'Friday', 'Saturday', 'Sunday'
            ])->nullable(); // nullable agar bisa pakai date untuk jadwal khusus

            // Jadwal tanggal spesifik (optional)
            $table->date('date')->nullable();

            // Waktu mulai & selesai kelas
            $table->time('start_time');
            $table->time('end_time')->nullable(); // Boleh null jika durasi belum pasti

            // Fokus kelas (catatan tambahan)
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
