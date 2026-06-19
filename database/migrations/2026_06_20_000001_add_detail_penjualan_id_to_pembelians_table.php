<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('pembelians', 'detail_penjualan_id')) {
            Schema::table('pembelians', function (Blueprint $table) {
                $table->unsignedBigInteger('detail_penjualan_id')
                      ->nullable()
                      ->after('penjualan_id');
                $table->foreign('detail_penjualan_id')
                      ->references('id')
                      ->on('detail_penjualans')
                      ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            $table->dropForeign(['detail_penjualan_id']);
            $table->dropColumn('detail_penjualan_id');
        });
    }
};