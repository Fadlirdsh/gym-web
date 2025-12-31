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
        Schema::create('trainer_shifts', function (Blueprint $table) {
            $table->id();

            // Relasi ke trainer
            $table->foreignId('trainer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Hari kerja (weekly shift)
            $table->enum('day', [
                'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday',
                'Saturday',
                'Sunday'
            ]);

            // Jam kerja
            $table->time('shift_start');
            $table->time('shift_end');

            // Status aktif / libur
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainer_shifts');
    }
};
