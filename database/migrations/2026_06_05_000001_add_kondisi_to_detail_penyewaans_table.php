<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detail_penyewaans', function (Blueprint $table) {
            $table->enum('kondisi', ['baru', 'bekas'])
                  ->default('baru')
                  ->after('inventory_id');
        });
    }

    public function down(): void
    {
        Schema::table('detail_penyewaans', function (Blueprint $table) {
            $table->dropColumn('kondisi');
        });
    }
};