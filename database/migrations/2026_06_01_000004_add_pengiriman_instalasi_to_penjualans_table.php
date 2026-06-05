<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->string('jasa_pengiriman')->default('diambil_sendiri')->after('keterangan');
            $table->decimal('harga_pengiriman', 15, 2)->default(0)->after('jasa_pengiriman');
            $table->decimal('jasa_instalasi', 15, 2)->default(0)->after('harga_pengiriman');
        });
    }

    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn(['jasa_pengiriman', 'harga_pengiriman', 'jasa_instalasi']);
        });
    }
};