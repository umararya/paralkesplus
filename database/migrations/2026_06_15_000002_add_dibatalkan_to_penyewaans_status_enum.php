<?php
// database/migrations/2026_06_15_000002_add_dibatalkan_to_penyewaans_status_enum.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah ENUM status agar include 'dibatalkan'
        DB::statement("
            ALTER TABLE `penyewaans`
            MODIFY COLUMN `status`
            ENUM('berjalan', 'segera_konfirmasi', 'selesai', 'dibatalkan')
            NOT NULL DEFAULT 'berjalan'
        ");
    }

    public function down(): void
    {
        // Kembalikan ke ENUM semula (pastikan tidak ada data 'dibatalkan' dulu)
        DB::statement("
            ALTER TABLE `penyewaans`
            MODIFY COLUMN `status`
            ENUM('berjalan', 'segera_konfirmasi', 'selesai')
            NOT NULL DEFAULT 'berjalan'
        ");
    }
};