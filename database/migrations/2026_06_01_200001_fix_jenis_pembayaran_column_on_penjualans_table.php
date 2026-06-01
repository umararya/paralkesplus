<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah jenis_pembayaran dari ENUM menjadi VARCHAR
        // agar bisa menampung nilai fleksibel: cash, transfer, qris, tunai, dll.
        DB::statement("ALTER TABLE penjualans MODIFY COLUMN jenis_pembayaran VARCHAR(30) NOT NULL DEFAULT 'cash'");

        // Normalise data lama: 'tunai' → 'cash'
        DB::statement("UPDATE penjualans SET jenis_pembayaran = 'cash' WHERE jenis_pembayaran = 'tunai'");

        // Tambah kolom metode_pembayaran jika belum ada (untuk simpan: cash/dp/transfer)
        if (!Schema::hasColumn('penjualans', 'metode_pembayaran')) {
            Schema::table('penjualans', function (Blueprint $table) {
                $table->string('metode_pembayaran', 20)->default('cash')->after('jenis_pembayaran');
            });
        }
    }

    public function down(): void
    {
        // Rollback: kembalikan ke ENUM semula
        DB::statement("UPDATE penjualans SET jenis_pembayaran = 'tunai' WHERE jenis_pembayaran = 'cash'");
        DB::statement("ALTER TABLE penjualans MODIFY COLUMN jenis_pembayaran ENUM('tunai','transfer','qris','kredit') NOT NULL DEFAULT 'tunai'");

        Schema::table('penjualans', function (Blueprint $table) {
            if (Schema::hasColumn('penjualans', 'metode_pembayaran')) {
                $table->dropColumn('metode_pembayaran');
            }
        });
    }
};