<?php
// database/migrations/2026_05_14_000006_create_penjualans_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_penjualan');
            $table->string('nama_barang');
            $table->unsignedInteger('qty');
            $table->string('alamat_pelanggan');
            $table->enum('jenis_pembayaran', [
                'tunai', 'transfer', 'qris', 'kredit'
            ])->default('tunai');
            $table->decimal('harga', 15, 2);
            $table->decimal('total', 15, 2)->storedAs('harga * qty');
            $table->text('keterangan')->nullable();
            $table->string('foto_bukti')->nullable(); // ← BARU: path file bukti transfer
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualans');
    }
};