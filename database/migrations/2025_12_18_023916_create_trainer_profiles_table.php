<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trainer_profiles', function (Blueprint $table) {
            $table->id();

            // relasi ke users
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->unique();

            // FOTO PROFIL TRAINER
            $table->string('photo')->nullable();
            // simpan path, contoh: trainer_profiles/andi.jpg

            // nilai jual trainer
            $table->string('headline')->nullable();
            $table->text('bio')->nullable();

            // keahlian & kredibilitas
            $table->json('skills')->nullable();
            $table->integer('experience_years')->nullable();
            $table->json('certifications')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trainer_profiles');
    }
};
