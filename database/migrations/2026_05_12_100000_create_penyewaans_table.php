<?php
// database/migrations/2026_05_12_100000_create_penyewaans_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penyewaans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_penyewa');
            $table->string('nomor_telepon');
            $table->string('produk_alkes');
            $table->unsignedInteger('durasi_hari');
            $table->boolean('pengiriman_ditanggung_pelanggan')->default(true);
            $table->unsignedBigInteger('biaya_ongkir')->default(0);
            $table->text('alamat_penyewa');
            $table->string('metode_pembayaran'); // tunai, transfer, dll
            $table->string('bukti_pembayaran')->nullable(); // path file
            $table->string('foto_ktp_sim')->nullable();    // path file
            $table->enum('status', ['berjalan', 'segera_konfirmasi', 'selesai'])->default('berjalan');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penyewaans');
    }
};