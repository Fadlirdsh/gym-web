<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();

            // Kode unik untuk transaksi
            $table->string('kode_transaksi')->unique();

            // User yang melakukan pembayaran
            $table->unsignedBigInteger('user_id');

            // Jenis pembayaran (member atau reservasi)
            $table->enum('jenis', ['member', 'reservasi']);

            // ID sumber transaksi (id member atau id reservasi)
            $table->unsignedBigInteger('source_id');

            // Jumlah nominal pembayaran
            $table->integer('jumlah');

            // Metode pembayaran (midtrans, manual, transfer, dsb)
            $table->string('metode')->nullable();

            // Status pembayaran (pending, success, failed)
            $table->string('status')->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
