<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Step 1: Drop generated column 'total' DULU sebelum drop dependency-nya ──
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn('total');
        });

        // ── Step 2: Baru drop kolom flat yang punya dependency ke 'total' ──
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn(['nama_barang', 'qty', 'harga']);
        });

        // ── Step 3: Tambah kolom header transaksi yang proper ──
        Schema::table('penjualans', function (Blueprint $table) {
            $table->string('nama_pelanggan')->after('tanggal_penjualan');
            $table->string('nomor_telepon', 20)->nullable()->after('nama_pelanggan');
            $table->unsignedBigInteger('total_harga')->default(0)->after('alamat_pelanggan');
            $table->unsignedBigInteger('diskon_global')->default(0)->after('total_harga');
            $table->unsignedBigInteger('total_bayar')
                  ->storedAs('total_harga - diskon_global')
                  ->after('diskon_global');
        });
    }

    public function down(): void
    {
        // ── Rollback Step 3: Drop kolom baru ──
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn('total_bayar'); // generated column dulu
        });

        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn(['nama_pelanggan', 'nomor_telepon', 'total_harga', 'diskon_global']);
        });

        // ── Rollback Step 1 & 2: Kembalikan kolom flat ──
        Schema::table('penjualans', function (Blueprint $table) {
            $table->string('nama_barang')->after('tanggal_penjualan');
            $table->unsignedInteger('qty')->after('nama_barang');
            $table->decimal('harga', 15, 2)->after('qty');
        });

        Schema::table('penjualans', function (Blueprint $table) {
            $table->decimal('total', 15, 2)->storedAs('harga * qty')->after('harga');
        });
    }
};