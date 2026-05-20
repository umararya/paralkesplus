<?php
// database/migrations/2026_05_20_000001_add_tempat_lahir_and_nomor_ktp_to_penyewaans_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penyewaans', function (Blueprint $table) {
            if (!Schema::hasColumn('penyewaans', 'tempat_tanggal_lahir')) {
                $table->string('tempat_tanggal_lahir', 255)
                      ->nullable()
                      ->after('nomor_telepon')
                      ->comment('Tempat dan tanggal lahir penyewa, contoh: Semarang, 01 Januari 1990');
            }
            if (!Schema::hasColumn('penyewaans', 'nomor_ktp')) {
                $table->string('nomor_ktp', 16)
                      ->nullable()
                      ->after('tempat_tanggal_lahir')
                      ->comment('Nomor Induk Kependudukan (NIK) 16 digit');
            }
        });
    }

    public function down(): void
    {
        Schema::table('penyewaans', function (Blueprint $table) {
            $table->dropColumn(['tempat_tanggal_lahir', 'nomor_ktp']);
        });
    }
};