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

            // TIPE KELAS (Pilates/Yoga, Group/Private)
            $table->enum('tipe_kelas', [
                'Pilates Group',
                'Pilates Private',
                'Yoga Group',
                'Yoga Private'
            ])->default('Pilates Group');

            // HARGA PAKET
            $table->integer('harga')->default(0);

            // SISTEM TOKEN BARU 🚀
            $table->integer('token_total')->default(0);      // total token sesuai paket (3,5,10,20)
            $table->integer('token_terpakai')->default(0);   // yang sudah digunakan
            $table->integer('token_sisa')->default(0);       // token_total - token_terpakai

            // MASA BERLAKU
            $table->date('tanggal_mulai')->nullable();       // tanggal mulai paket
            $table->date('tanggal_berakhir')->nullable();    // masa berlaku paket

            // STATUS MEMBER
            $table->enum('status', ['aktif', 'nonaktif', 'pending'])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
