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

            // relasi pelanggan (user dengan role = pelanggan)
            $table->foreignId('pelanggan_id')
                ->constrained('users')
                ->onDelete('cascade');

            // relasi trainer (user dengan role = trainer)
            $table->foreignId('trainer_id')
                ->constrained('users')
                ->onDelete('cascade');

            // relasi kelas
            $table->foreignId('kelas_id')
                ->constrained('kelas')
                ->onDelete('cascade');

            // jadwal reservasi
            $table->dateTime('jadwal');

            // status reservasi
            $table->enum('status', ['pending', 'approved', 'canceled'])
                ->default('pending');

            $table->enum('status_hadir', ['belum_hadir', 'hadir'])->default('belum_hadir');


            // // metode pembayaran (opsional)
            // $table->enum('metode_pembayaran', ['cash', 'transfer', 'ewallet'])
            //       ->nullable();

            // // status pembayaran (opsional)
            // $table->enum('status_pembayaran', ['belum_bayar', 'lunas'])
            //       ->default('belum_bayar');

            // catatan tambahan opsional
            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservasi');
    }
};
