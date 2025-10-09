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
            $table->unsignedBigInteger('user_id'); // pelanggan
            $table->string('kode_kupon')->default('FREECLASS');
            $table->boolean('sudah_dipakai')->default(false);
            $table->date('berlaku_hingga')->nullable(); // berlaku 1 bulan
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kupon_pengguna');
    }
};
