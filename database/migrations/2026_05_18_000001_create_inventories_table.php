<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('nama_produk');
            $table->string('kategori')->nullable();
            $table->string('satuan')->default('unit');
            $table->unsignedInteger('stok_tersedia')->default(0);
            $table->unsignedInteger('stok_disewa')->default(0);
            $table->unsignedInteger('stok_baru')->default(0);
            $table->unsignedInteger('stok_bekas')->default(0);
            $table->decimal('harga_beli_terakhir', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};