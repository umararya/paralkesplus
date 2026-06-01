<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->enum('metode_pembayaran', ['cash', 'dp', 'transfer'])
                  ->default('cash')
                  ->after('total_bayar');

            $table->enum('status_pembayaran', ['lunas', 'dp', 'belum_lunas'])
                  ->default('belum_lunas')
                  ->after('metode_pembayaran');

            $table->enum('status_transaksi', ['aktif', 'selesai', 'batal'])
                  ->default('aktif')
                  ->after('status_pembayaran');

            $table->unsignedBigInteger('total_terbayar')
                  ->default(0)
                  ->after('status_transaksi');

            $table->text('catatan_pembatalan')
                  ->nullable()
                  ->after('total_terbayar');
        });
    }

    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn([
                'metode_pembayaran',
                'status_pembayaran',
                'status_transaksi',
                'total_terbayar',
                'catatan_pembatalan',
            ]);
        });
    }
};