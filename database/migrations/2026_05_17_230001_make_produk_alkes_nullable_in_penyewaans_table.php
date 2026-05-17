<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penyewaans', function (Blueprint $table) {
            $table->string('produk_alkes')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('penyewaans', function (Blueprint $table) {
            $table->string('produk_alkes')->nullable(false)->change();
        });
    }
};