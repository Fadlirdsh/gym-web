<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('trainer_id')->constrained('users')->onDelete('cascade');
            $table->string('day');                  // contoh: Monday
            $table->time('time');                   // jam kelas
            $table->string('class_focus')->nullable(); // optional
            $table->boolean('is_active')->default(true); // on/off jadwal
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
