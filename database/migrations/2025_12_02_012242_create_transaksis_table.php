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

            // Jenis pembayaran (member atau reservasi)
            $table->enum('jenis', ['member', 'reservasi']);

            // ID sumber transaksi (id member atau id reservasi)
            $table->unsignedBigInteger('source_id');

            // === Field baru untuk checkout yang bener ===

            // Harga asli sebelum diskon
            $table->integer('harga_asli');

            // Besar diskon (dalam rupiah)
            $table->integer('diskon')->default(0);

            // Total akhir yang harus dibayar
            $table->integer('total_bayar');

            // Kupon yang dipakai (optional)
            // $table->unsignedBigInteger('kupon_pengguna_id')->nullable();

            // Metode pembayaran (midtrans, manual, transfer, dsb)
            $table->string('metode')->nullable();

            // Status pembayaran
            $table->string('status')->default('pending');

            $table->timestamps();

            // Relasi
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('kupon_pengguna_id')->references('id')->on('kupon_pengguna')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
