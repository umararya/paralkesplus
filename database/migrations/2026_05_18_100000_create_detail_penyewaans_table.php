<?php
// database/migrations/xxxx_xx_xx_create_detail_penyewaans_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_penyewaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penyewaan_id')
                  ->constrained('penyewaans')
                  ->cascadeOnDelete();
            $table->foreignId('inventory_id')
                  ->nullable()
                  ->constrained('inventories')
                  ->nullOnDelete();
            $table->string('nama_alat');
            $table->unsignedInteger('qty')->default(1);
            $table->string('satuan')->default('unit');
            $table->unsignedBigInteger('harga_satuan')->default(0);
            $table->unsignedTinyInteger('diskon')->default(0); // persen 0-100
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_penyewaans');
    }
};