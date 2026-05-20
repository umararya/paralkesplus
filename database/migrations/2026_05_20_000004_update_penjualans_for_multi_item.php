<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            // Kolom baru header penjualan (multi-item)
            if (!Schema::hasColumn('penjualans', 'nama_pelanggan')) {
                $table->string('nama_pelanggan', 255)->nullable()->after('id');
            }
            if (!Schema::hasColumn('penjualans', 'nomor_telepon')) {
                $table->string('nomor_telepon', 20)->nullable()->after('nama_pelanggan');
            }
            if (!Schema::hasColumn('penjualans', 'alamat_pelanggan')) {
                $table->text('alamat_pelanggan')->nullable()->after('nomor_telepon');
            }
            if (!Schema::hasColumn('penjualans', 'diskon_global')) {
                $table->unsignedBigInteger('diskon_global')->default(0)->after('alamat_pelanggan');
            }
            if (!Schema::hasColumn('penjualans', 'total_harga')) {
                $table->unsignedBigInteger('total_harga')->default(0)->after('diskon_global');
            }
        });
    }

    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn([
                'nama_pelanggan',
                'nomor_telepon',
                'diskon_global',
                'total_harga',
            ]);
        });
    }
};