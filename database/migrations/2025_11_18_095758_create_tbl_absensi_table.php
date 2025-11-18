<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tbl_absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('zona_id')->constrained('tbl_zona')->onDelete('cascade');
            $table->timestamp('waktu');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('tbl_absensi');
    }
};
