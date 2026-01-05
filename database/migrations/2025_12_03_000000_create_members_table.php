<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('status', ['aktif', 'nonaktif', 'pending'])
                ->default('pending');

            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_berakhir')->nullable();

            // ⬇️ INI SATU-SATUNYA TAMBAHAN PENTING
            $table->foreignId('activated_by_transaction_id')
                ->nullable()
                ->constrained('transaksis')
                ->nullOnDelete();


            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
