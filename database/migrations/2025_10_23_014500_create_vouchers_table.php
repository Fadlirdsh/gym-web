<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::create('vouchers', function (Blueprint $table) {
        $table->id();
        $table->string('kode')->unique();
        $table->text('deskripsi');
        $table->integer('diskon_persen');
        $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('set null');
        $table->enum('role_target', ['semua', 'pelanggan', 'member'])->default('semua');
        $table->date('tanggal_mulai');
        $table->date('tanggal_akhir');
        $table->integer('kuota')->default(0);
        $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
