<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_penyewaans', function (Blueprint $table) {
            // Tambah setelah kolom penyewaan_id
            $table->foreignId('inventory_id')
                  ->nullable()
                  ->after('penyewaan_id')
                  ->constrained('inventories')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('detail_penyewaans', function (Blueprint $table) {
            $table->dropForeign(['inventory_id']);
            $table->dropColumn('inventory_id');
        });
    }
};