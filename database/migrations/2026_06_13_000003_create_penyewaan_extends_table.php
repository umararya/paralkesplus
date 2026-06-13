<?php
// database/migrations/2026_06_13_000003_create_penyewaan_extends_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penyewaan_extends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penyewaan_id')
                  ->constrained('penyewaans')
                  ->onDelete('cascade');
            $table->date('tgl_selesai_lama');
            $table->date('tgl_selesai_baru');
            $table->integer('tambah_hari')->default(0);
            $table->decimal('harga_extend', 15, 2)->default(0);
            $table->string('metode_bayar', 100)->nullable();
            $table->string('bukti_transfer', 255)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penyewaan_extends');
    }
};