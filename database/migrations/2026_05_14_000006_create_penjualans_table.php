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
            $table->string('nama_barang');          // nama barang / produk
            $table->unsignedInteger('qty');          // jumlah (qty)
            $table->string('alamat_pelanggan');      // alamat pelanggan
            $table->enum('jenis_pembayaran', [       // jenis pembayaran
                'tunai', 'transfer', 'qris', 'kredit'
            ])->default('tunai');
            $table->decimal('harga', 15, 2);         // harga satuan
            $table->decimal('total', 15, 2)          // total = harga * qty (auto-fill)
                  ->storedAs('harga * qty');
            $table->text('keterangan')->nullable();  // keterangan / catatan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penjualans');
    }
};