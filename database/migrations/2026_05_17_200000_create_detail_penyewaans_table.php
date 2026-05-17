<?php
// database/migrations/2026_05_17_200000_create_detail_penyewaans_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Tabel detail item per penyewaan ──────────────────────────
        Schema::create('detail_penyewaans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penyewaan_id')
                  ->constrained('penyewaans')
                  ->onDelete('cascade');
            $table->string('nama_alat');
            $table->unsignedInteger('qty')->default(1);
            $table->string('satuan', 50)->default('pcs');   // pcs, unit, set, dll
            $table->unsignedBigInteger('harga_satuan')->default(0);
            $table->unsignedTinyInteger('diskon')->default(0); // persen 0–100
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->timestamps();
        });

        // ── Tambah kolom total di tabel header penyewaans ────────────
        Schema::table('penyewaans', function (Blueprint $table) {
            if (!Schema::hasColumn('penyewaans', 'total_harga_sewa')) {
                $table->unsignedBigInteger('total_harga_sewa')
                      ->default(0)
                      ->after('biaya_ongkir');
            }
            if (!Schema::hasColumn('penyewaans', 'diskon_global')) {
                $table->unsignedBigInteger('diskon_global')
                      ->default(0)
                      ->after('total_harga_sewa');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_penyewaans');

        Schema::table('penyewaans', function (Blueprint $table) {
            $table->dropColumn(['total_harga_sewa', 'diskon_global']);
        });
    }
};