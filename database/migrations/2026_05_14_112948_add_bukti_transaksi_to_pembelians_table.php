<?php
// database/migrations/2026_05_14_112948_add_bukti_transaksi_to_pembelians_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            if (!Schema::hasColumn('pembelians', 'bukti_transaksi')) {
                $table->string('bukti_transaksi')->nullable()->after('keterangan');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            // Guard: hanya drop jika kolom ada,
            // dan hanya jika BUKAN sudah di-define di create_pembelians_table
            // (cek via hasColumn agar rollback tidak error)
            if (Schema::hasColumn('pembelians', 'bukti_transaksi')) {
                $table->dropColumn('bukti_transaksi');
            }
        });
    }
};