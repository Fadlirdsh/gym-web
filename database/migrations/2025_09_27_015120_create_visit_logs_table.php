<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('visit_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('reservasi_id')
                ->constrained('reservasi')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // JENIS KEJADIAN (EVENT)
            // contoh: hadir, invalid, expired
            $table->string('status');

            $table->text('catatan')->nullable();

            // created_at = waktu check-in
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_logs');
    }
};
