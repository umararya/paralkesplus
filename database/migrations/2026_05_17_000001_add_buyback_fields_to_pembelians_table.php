<?php
// database/migrations/2026_05_17_000001_add_buyback_fields_to_pembelians_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            $table->enum('status', ['normal', 'buy_back'])->default('normal')->after('keterangan');
            $table->unsignedBigInteger('penjualan_id')->nullable()->after('status');
            $table->string('nama_pelanggan', 255)->nullable()->after('penjualan_id');
            $table->enum('kondisi_barang', ['baik', 'bekas', 'rusak'])->nullable()->after('nama_pelanggan');

            $table->foreign('penjualan_id')
                  ->references('id')
                  ->on('penjualans')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            $table->dropForeign(['penjualan_id']);
            $table->dropColumn(['status', 'penjualan_id', 'nama_pelanggan', 'kondisi_barang']);
        });
    }
};