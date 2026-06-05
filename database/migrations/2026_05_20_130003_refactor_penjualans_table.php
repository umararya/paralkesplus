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
            if (Schema::hasColumn('penjualans', 'total')) {
                $table->dropColumn('total');
            }
        });

        // ── Step 2: Baru drop kolom flat yang punya dependency ke 'total' ──
        Schema::table('penjualans', function (Blueprint $table) {
            $cols = ['nama_barang', 'qty', 'harga'];
            $existing = array_filter($cols, fn($c) => Schema::hasColumn('penjualans', $c));
            if (!empty($existing)) {
                $table->dropColumn(array_values($existing));
            }
        });

        // ── Step 3: Tambah kolom header transaksi yang proper ──
        Schema::table('penjualans', function (Blueprint $table) {
            if (!Schema::hasColumn('penjualans', 'nama_pelanggan')) {
                $table->string('nama_pelanggan')->after('tanggal_penjualan');
            }
            if (!Schema::hasColumn('penjualans', 'nomor_telepon')) {
                $table->string('nomor_telepon', 20)->nullable()->after('nama_pelanggan');
            }
            if (!Schema::hasColumn('penjualans', 'total_harga')) {
                $table->unsignedBigInteger('total_harga')->default(0)->after('alamat_pelanggan');
            }
            if (!Schema::hasColumn('penjualans', 'diskon_global')) {
                $table->unsignedBigInteger('diskon_global')->default(0)->after('total_harga');
            }
            if (!Schema::hasColumn('penjualans', 'total_bayar')) {
                $table->unsignedBigInteger('total_bayar')
                      ->storedAs('total_harga - diskon_global')
                      ->after('diskon_global');
            }
        });
    }

    public function down(): void
    {
        // ── Rollback Step 3: Drop generated column dulu ──
        Schema::table('penjualans', function (Blueprint $table) {
            if (Schema::hasColumn('penjualans', 'total_bayar')) {
                $table->dropColumn('total_bayar');
            }
        });

        // ── Rollback Step 3: Drop kolom lainnya ──
        Schema::table('penjualans', function (Blueprint $table) {
            $cols = ['nama_pelanggan', 'nomor_telepon', 'total_harga', 'diskon_global'];
            $existing = array_filter($cols, fn($c) => Schema::hasColumn('penjualans', $c));
            if (!empty($existing)) {
                $table->dropColumn(array_values($existing));
            }
        });

        // ── Rollback Step 1 & 2: Kembalikan kolom flat ──
        Schema::table('penjualans', function (Blueprint $table) {
            if (!Schema::hasColumn('penjualans', 'nama_barang')) {
                $table->string('nama_barang')->after('tanggal_penjualan');
            }
            if (!Schema::hasColumn('penjualans', 'qty')) {
                $table->unsignedInteger('qty')->after('nama_barang');
            }
            if (!Schema::hasColumn('penjualans', 'harga')) {
                $table->decimal('harga', 15, 2)->after('qty');
            }
        });

        Schema::table('penjualans', function (Blueprint $table) {
            if (!Schema::hasColumn('penjualans', 'total')) {
                $table->decimal('total', 15, 2)->storedAs('harga * qty')->after('harga');
            }
        });
    }
};