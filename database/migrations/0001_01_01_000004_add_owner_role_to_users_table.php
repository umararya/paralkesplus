<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL tidak support ALTER COLUMN untuk ENUM secara langsung,
        // kita modifikasi column definition via raw SQL
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','staff','manager','owner') NOT NULL DEFAULT 'staff'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','staff','manager') NOT NULL DEFAULT 'staff'");
    }
};