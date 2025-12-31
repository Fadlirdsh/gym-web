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

            /**
             * =========================
             * RELASI INTI
             * =========================
             */

            // Relasi ke shift kerja trainer (WAJIB)
            $table->foreignId('trainer_shift_id')
                  ->constrained('trainer_shifts')
                  ->cascadeOnDelete();

            // Relasi ke kelas
            $table->foreignId('kelas_id')
                  ->constrained('kelas')
                  ->cascadeOnDelete();

            // Relasi ke trainer (redundan tapi sengaja, untuk query cepat)
            $table->foreignId('trainer_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            /**
             * =========================
             * JADWAL KELAS
             * =========================
             */

            $table->enum('day', [
                'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday',
                'Saturday',
                'Sunday'
            ]);

            $table->time('start_time');
            $table->time('end_time');

            /**
             * =========================
             * META
             * =========================
             */

            $table->string('class_focus')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
