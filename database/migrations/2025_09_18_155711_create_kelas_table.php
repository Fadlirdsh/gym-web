<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas', 100);         // ex: "Pilates Group", "Yoga Private"
            $table->string('tipe_kelas', 50);          // Group / Private
            $table->decimal('harga', 10, 2);           // ex: 120000.00
            $table->text('deskripsi')->nullable();     // dari kolom "Description"
            // $table->decimal('diskon', 5, 2)->nullable(); // persentase diskon (%)
            $table->string('tipe_paket', 50)->nullable(); // Package, ClassPass, Drop In, dll
            $table->integer('jumlah_token')->nullable();     // berapa kali pertemuan
            $table->date('expired_at')->nullable();          // tanggal berakhir
            // $table->datetime('waktu_mulai')->nullable();   // dari "Time Class"
            $table->timestamps();                      // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
