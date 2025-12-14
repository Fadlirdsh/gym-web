<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kupon_pengguna', function (Blueprint $table) {
            $table->id();

            // User pemilik kupon (1 user = 1 kupon first time)
            $table->unsignedBigInteger('user_id');

            // Status kupon
            $table->enum('status', ['pending', 'claimed', 'used', 'expired'])
                  ->default('pending');

            // Penanda kupon sudah dipakai
            $table->boolean('sudah_dipakai')->default(false);

            // Masa berlaku kupon (7 hari dari register)
            $table->timestamp('berlaku_hingga');

            // Diskon (diisi saat checkout)
            $table->decimal('persentase_diskon', 5, 2)->nullable();
            $table->decimal('harga_setelah_diskon', 10, 2)->nullable();

            $table->timestamps();

            // FK
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kupon_pengguna');
    }
};
