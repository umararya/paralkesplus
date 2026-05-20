<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom keterangan ke tabel inventories.
     * Kolom lain (nama_produk, kategori, satuan, stok_tersedia, stok_disewa,
     * stok_baru, stok_bekas, harga_beli_terakhir) sudah ada sejak
     * 2026_05_18_000001_create_inventories_table.php
     */
    public function up(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            if (!Schema::hasColumn('inventories', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('harga_beli_terakhir');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventories', function (Blueprint $table) {
            if (Schema::hasColumn('inventories', 'keterangan')) {
                $table->dropColumn('keterangan');
            }
        });
    }
};