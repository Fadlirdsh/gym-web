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

            // Relasi ke shift kerja trainer (SUMBER KEBENARAN TRAINER + HARI)
            $table->foreignId('trainer_shift_id')
                ->constrained('trainer_shifts')
                ->cascadeOnDelete();

            // Relasi ke kelas
            $table->foreignId('kelas_id')
                ->constrained('kelas')
                ->cascadeOnDelete();

            /**
             * =========================
             * SLOT JADWAL KELAS
             * =========================
             */

            // Slot waktu (HARUS di dalam jam shift)
            $table->time('start_time');
            $table->time('end_time');

            // Kapasitas per slot
            $table->unsignedInteger('capacity');

            /**
             * =========================
             * META
             * =========================
             */

            $table->string('class_focus')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            /**
             * =========================
             * PROTEKSI DATA
             * =========================
             */

            // Cegah schedule bentrok di shift yang sama
            $table->unique([
                'trainer_shift_id',
                'kelas_id',
                'start_time',
                'end_time'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
