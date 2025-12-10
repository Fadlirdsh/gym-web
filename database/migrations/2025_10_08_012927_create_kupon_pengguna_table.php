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
            $table->unsignedBigInteger('user_id');
            $table->enum('status', ['pending', 'claimed', 'used', 'expired'])->default('pending');
            $table->boolean('sudah_dipakai')->default(false); // opsional
            $table->timestamp('berlaku_hingga'); // 7 hari sejak register
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kupon_pengguna');
    }
};
