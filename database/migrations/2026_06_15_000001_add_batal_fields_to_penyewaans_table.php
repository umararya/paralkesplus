<?php
// database/migrations/2026_06_15_000001_add_batal_fields_to_penyewaans_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penyewaans', function (Blueprint $table) {
            $table->text('alasan_batal')->nullable()->after('keterangan');
            $table->timestamp('dibatalkan_at')->nullable()->after('alasan_batal');
        });
    }

    public function down(): void
    {
        Schema::table('penyewaans', function (Blueprint $table) {
            $table->dropColumn(['alasan_batal', 'dibatalkan_at']);
        });
    }
};