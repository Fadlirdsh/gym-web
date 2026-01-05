<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_tokens', function (Blueprint $table) {
            $table->id();

            // Relasi ke member
            $table->foreignId('member_id')
                ->constrained('members')
                ->cascadeOnDelete();

            // Tipe kelas token
            $table->enum('tipe_kelas', [
                'Pilates Group',
                'Pilates Private',
                'Yoga Group',
                'Yoga Private'
            ]);

            // Token
            $table->integer('token_total')->default(0);
            $table->integer('token_terpakai')->default(0);
            $table->integer('token_sisa')->default(0);

            /**
             * ASAL TOKEN
             * - midtrans : beli via payment gateway
             * - admin    : diinput manual admin (cash / hardware)
             */
            $table->enum('source', ['midtrans', 'admin'])
                ->default('midtrans');

            /**
             * Relasi ke transaksi (NULLABLE)
             * - NULL = token dari admin / cash
             * - ADA  = token dari pembayaran online
             */
            $table->foreignId('transaction_id')
                ->nullable()
                ->constrained('transaksis')
                ->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_tokens');
    }
};
