<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Kondisi lama: enum('baik','bekas','rusak') — hanya untuk buy back
     * Kondisi baru: enum('baru','bekas','baik','rusak') — mencakup pembelian normal & buy back
     *
     * Mapping nilai lama → baru:
     *   'baik'  → tetap 'baik'   (buy back kondisi baik)
     *   'bekas' → tetap 'bekas'  (buy back / pembelian bekas)
     *   'rusak' → tetap 'rusak'  (buy back kondisi rusak)
     *   NULL    → tetap NULL     (data lama yang belum diisi)
     */
    public function up(): void
    {
        // MySQL tidak bisa langsung ALTER ENUM,
        // ubah dulu ke VARCHAR agar tidak error saat ganti nilai
        DB::statement("ALTER TABLE pembelians MODIFY COLUMN kondisi_barang VARCHAR(20) NULL");

        // Sekarang ubah ke ENUM baru yang lengkap
        DB::statement("ALTER TABLE pembelians MODIFY COLUMN kondisi_barang ENUM('baru','bekas','baik','rusak') NULL");
    }

    public function down(): void
    {
        // Kembalikan ke ENUM lama (buy back only)
        // Data dengan nilai 'baru' akan hilang — hati-hati rollback
        DB::statement("UPDATE pembelians SET kondisi_barang = NULL WHERE kondisi_barang = 'baru'");
        DB::statement("ALTER TABLE pembelians MODIFY COLUMN kondisi_barang ENUM('baik','bekas','rusak') NULL");
    }
};