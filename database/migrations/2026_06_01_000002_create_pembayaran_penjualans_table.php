<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran_penjualans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('penjualan_id')
                  ->constrained('penjualans')
                  ->cascadeOnDelete();

            $table->foreignId('created_by')
                  ->constrained('users')
                  ->restrictOnDelete();

            $table->enum('tipe', ['dp', 'pelunasan', 'cicilan'])
                  ->default('pelunasan');

            $table->enum('metode', ['cash', 'transfer', 'qris'])
                  ->default('cash');

            $table->unsignedBigInteger('jumlah_bayar');

            $table->string('foto_bukti')->nullable();

            $table->text('keterangan')->nullable();

            $table->timestamp('tanggal_bayar')->useCurrent();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_penjualans');
    }
};