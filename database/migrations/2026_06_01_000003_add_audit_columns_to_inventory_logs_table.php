<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_logs', function (Blueprint $table) {
            $table->unsignedInteger('stok_sebelum')
                  ->default(0)
                  ->after('qty_change');

            $table->unsignedInteger('stok_sesudah')
                  ->default(0)
                  ->after('stok_sebelum');

            $table->unsignedBigInteger('created_by')
                  ->nullable()
                  ->after('keterangan');

            $table->foreign('created_by')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inventory_logs', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['stok_sebelum', 'stok_sesudah', 'created_by']);
        });
    }
};