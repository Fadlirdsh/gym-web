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

            // Kode unik untuk transaksi (TRX-xxxxx)
            $table->string('kode_transaksi')->unique();

            // User yang melakukan pembayaran
            $table->unsignedBigInteger('user_id');

            // Jenis pembayaran
            $table->enum('jenis', ['member', 'reservasi', 'token']);

            // ID sumber transaksi (member / reservasi)
            $table->unsignedBigInteger('source_id');

            // Harga sebelum diskon
            $table->integer('harga_asli');

            // Diskon rupiah
            $table->integer('diskon')->default(0);

            // Total bayar
            $table->integer('total_bayar');

            // Metode pembayaran
            $table->string('metode')->nullable();

            // Status pembayaran
            $table->string('status')->default('pending');

            // 🔥 META TRANSAKSI (KONTEKS)
            $table->json('meta')->nullable();

            $table->timestamps();

            // Relasi
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
