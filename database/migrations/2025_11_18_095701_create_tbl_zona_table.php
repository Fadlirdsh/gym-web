<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tbl_zona', function (Blueprint $table) {
            $table->id();
            $table->string('nama_zona');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->integer('radius_m'); // radius zona dalam meter
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('tbl_zona');
    }
};
