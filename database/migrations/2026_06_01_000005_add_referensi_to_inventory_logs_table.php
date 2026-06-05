<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_logs', function (Blueprint $table) {
            // Cek dan tambahkan kolom yang belum ada
            if (! Schema::hasColumn('inventory_logs', 'referensi_id')) {
                $table->unsignedBigInteger('referensi_id')->nullable()->after('inventory_id');
            }

            if (! Schema::hasColumn('inventory_logs', 'referensi_type')) {
                $table->string('referensi_type')->nullable()->after('referensi_id');
            }

            if (! Schema::hasColumn('inventory_logs', 'stok_sebelum')) {
                $table->unsignedInteger('stok_sebelum')->default(0)->after('qty');
            }

            if (! Schema::hasColumn('inventory_logs', 'stok_sesudah')) {
                $table->unsignedInteger('stok_sesudah')->default(0)->after('stok_sebelum');
            }

            if (! Schema::hasColumn('inventory_logs', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('keterangan');
                $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventory_logs', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['referensi_id', 'referensi_type', 'stok_sebelum', 'stok_sesudah', 'created_by']);
        });
    }
};