<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penyewaans', function (Blueprint $table) {
            if (!Schema::hasColumn('penyewaans', 'tgl_mulai')) {
                $table->datetime('tgl_mulai')->nullable()->after('produk_alkes');
            }
            if (!Schema::hasColumn('penyewaans', 'tgl_selesai')) {
                $table->datetime('tgl_selesai')->nullable()->after('tgl_mulai');
            }
            if (!Schema::hasColumn('penyewaans', 'total_harga_sewa')) {
                $table->bigInteger('total_harga_sewa')->default(0)->after('diskon_global');
            }
            if (!Schema::hasColumn('penyewaans', 'tempat_tanggal_lahir')) {
                $table->string('tempat_tanggal_lahir')->nullable()->after('nomor_telepon');
            }
            if (!Schema::hasColumn('penyewaans', 'nomor_ktp')) {
                $table->string('nomor_ktp')->nullable()->after('tempat_tanggal_lahir');
            }
        });
    }

    public function down(): void
    {
        Schema::table('penyewaans', function (Blueprint $table) {
            $table->dropColumn([
                'tgl_mulai',
                'tgl_selesai',
                'total_harga_sewa',
                'tempat_tanggal_lahir',
                'nomor_ktp',
            ]);
        });
    }
};