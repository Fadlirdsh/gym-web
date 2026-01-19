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
        Schema::create('transaksi_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaksi_id');

            $table->string('item_name');
            $table->integer('price');
            $table->integer('qty')->default(1);

            $table->string('item_type')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();

            $table->timestamps();

            $table->foreign('transaksi_id')
                ->references('id')
                ->on('transaksis')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_items');
    }
};
