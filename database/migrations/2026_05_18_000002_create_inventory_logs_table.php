<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained('inventories')->onDelete('cascade');
            $table->string('reference_type'); // purchase | sale | rental_start | rental_return | buyback
            $table->unsignedBigInteger('reference_id');
            $table->integer('qty_change'); // positif = masuk, negatif = keluar
            $table->string('kondisi')->default('baru'); // baru | bekas
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
    }
};