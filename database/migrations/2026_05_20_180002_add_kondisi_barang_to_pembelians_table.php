<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom kondisi_barang ke tabel pembelians.
     * Dibutuhkan untuk membedakan stok baru/bekas saat pembelian
     * dan meng-update inventory secara tepat.
     */
    public function up(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            if (!Schema::hasColumn('pembelians', 'kondisi_barang')) {
                // Ditempatkan setelah kolom harga_satuan
                $table->string('kondisi_barang', 20)
                      ->default('baru')
                      ->after('harga_satuan')
                      ->comment('baru | bekas (untuk pembelian normal); baik | bekas | rusak (untuk buy back)');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            if (Schema::hasColumn('pembelians', 'kondisi_barang')) {
                $table->dropColumn('kondisi_barang');
            }
        });
    }
};