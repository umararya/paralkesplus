<?php
// database/migrations/2024_01_01_000005_create_pembelians_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembelians', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_pembelian');
            $table->string('nama_barang', 150);
            $table->integer('jumlah');
            $table->bigInteger('harga_satuan');
            $table->bigInteger('total')->virtualAs('jumlah * harga_satuan');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembelians');
    }
};