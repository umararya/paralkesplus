<?php
// database/migrations/2026_06_15_000003_add_batal_fields_to_penyewaan_extends_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penyewaan_extends', function (Blueprint $table) {
            $table->enum('status_extend', ['aktif', 'dibatalkan'])
                  ->default('aktif')
                  ->after('catatan');
            $table->text('alasan_batal_extend')->nullable()->after('status_extend');
            $table->timestamp('dibatalkan_extend_at')->nullable()->after('alasan_batal_extend');
        });
    }

    public function down(): void
    {
        Schema::table('penyewaan_extends', function (Blueprint $table) {
            $table->dropColumn(['status_extend', 'alasan_batal_extend', 'dibatalkan_extend_at']);
        });
    }
};