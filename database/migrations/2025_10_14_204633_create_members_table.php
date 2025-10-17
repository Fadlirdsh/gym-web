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

            // Relasi ke tabel users
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Tambahan kolom baru untuk paket member
            $table->enum('maks_kelas', ['3', '5', '10', '20'])->default('3'); 
            $table->enum('tipe_kelas', [
                'Pilates Group',
                'Pilates Private',
                'Yoga Group',
                'Yoga Private'
            ])->default('Pilates Group');
            $table->integer('harga')->default(0);

            // Kolom lama tetap dipertahankan
            $table->date('tanggal_mulai')->nullable();   // tanggal mulai jadi member
            $table->date('tanggal_berakhir')->nullable(); // masa berlaku membership
            $table->enum('status', ['aktif', 'nonaktif', 'pending'])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
