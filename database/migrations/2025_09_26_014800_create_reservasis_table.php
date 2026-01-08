<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservasi', function (Blueprint $table) {
            $table->id();

            /**
             * =========================
             * RELASI INTI
             * =========================
             */

            // Pelanggan yang booking
            $table->foreignId('pelanggan_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Slot jadwal yang dibooking
            $table->foreignId('schedule_id')
                ->constrained('schedules')
                ->cascadeOnDelete();

            /**
             * =========================
             * WAKTU EKSEKUSI
             * =========================
             */

            // Tanggal booking (jam diambil dari schedule)
            $table->date('tanggal');

            /**
             * =========================
             * STATUS
             * =========================
             */

            $table->enum('status', [
                'pending_payment',
                'paid',
                'canceled'
            ])->default('pending_payment');

            $table->enum('status_hadir', [
                'belum_hadir',
                'hadir'
            ])->default('belum_hadir');

            /**
             * =========================
             * OPSIONAL
             * =========================
             */

            $table->text('catatan')->nullable();

            $table->timestamps();

            /**
             * =========================
             * PROTEKSI DATA
             * =========================
             */

            // Cegah 1 user booking slot yang sama di tanggal yang sama
            $table->unique([
                'pelanggan_id',
                'schedule_id',
                'tanggal'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservasi');
    }
};
