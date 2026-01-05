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
        Schema::create('member_tokens', function (Blueprint $table) {
            $table->id();

            $table->foreignId('member_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('tipe_kelas', [
                'Pilates Group',
                'Pilates Private',
                'Yoga Group',
                'Yoga Private'
            ]);

            $table->integer('token_total')->default(0);
            $table->integer('token_terpakai')->default(0);
            $table->integer('token_sisa')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_tokens');
    }
};
