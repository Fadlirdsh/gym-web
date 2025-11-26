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
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas', 100);        // ex: "Pilates Group", "Yoga Private"

            // === Ubah menjadi ENUM ===
            $table->enum('tipe_kelas', [
                'Pilates Group',
                'Pilates Private',
                'Yoga Group',
                'Yoga Private'
            ]);

            $table->decimal('harga', 10, 2);          // ex: 120000.00
            $table->text('deskripsi')->nullable();    // dari kolom "Description"
            $table->date('expired_at')->nullable();        // tanggal berakhir
            $table->integer('kapasitas')->default(20);     // kapasitas peserta
            $table->string('gambar')->nullable();          // gambar kelas
            $table->timestamps();                          // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
    }
};
