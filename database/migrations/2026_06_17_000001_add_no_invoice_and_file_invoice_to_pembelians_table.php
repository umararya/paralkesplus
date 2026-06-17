<?php
// database/migrations/2026_06_17_000001_add_no_invoice_and_file_invoice_to_pembelians_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            // no_invoice: setelah tanggal_pembelian
            if (!Schema::hasColumn('pembelians', 'no_invoice')) {
                $table->string('no_invoice', 100)->nullable()->after('tanggal_pembelian');
            }
            // file_invoice: setelah bukti_transaksi
            if (!Schema::hasColumn('pembelians', 'file_invoice')) {
                $table->string('file_invoice')->nullable()->after('bukti_transaksi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pembelians', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('pembelians', 'no_invoice'))   $columns[] = 'no_invoice';
            if (Schema::hasColumn('pembelians', 'file_invoice')) $columns[] = 'file_invoice';
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};