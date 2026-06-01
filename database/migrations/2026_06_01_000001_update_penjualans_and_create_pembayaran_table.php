<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Tambah kolom baru di tabel penjualans ──────────────────
        Schema::table('penjualans', function (Blueprint $table) {
            // Hanya tambah kolom yang belum ada
            if (!Schema::hasColumn('penjualans', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->after('id');
            }
            if (!Schema::hasColumn('penjualans', 'metode_pembayaran')) {
                $table->string('metode_pembayaran')->default('cash')->after('jenis_pembayaran');
            }
            if (!Schema::hasColumn('penjualans', 'total_terbayar')) {
                $table->unsignedBigInteger('total_terbayar')->default(0)->after('total_harga');
            }
            if (!Schema::hasColumn('penjualans', 'status_pembayaran')) {
                $table->string('status_pembayaran')->default('belum_lunas')->after('total_terbayar');
            }
            if (!Schema::hasColumn('penjualans', 'status_transaksi')) {
                $table->string('status_transaksi')->default('aktif')->after('status_pembayaran');
            }
            if (!Schema::hasColumn('penjualans', 'catatan_pembatalan')) {
                $table->text('catatan_pembatalan')->nullable()->after('status_transaksi');
            }
        });

        // ── 2. Buat tabel pembayaran_penjualans ────────────────────────
        if (!Schema::hasTable('pembayaran_penjualans')) {
            Schema::create('pembayaran_penjualans', function (Blueprint $table) {
                $table->id();
                $table->foreignId('penjualan_id')->constrained('penjualans')->cascadeOnDelete();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->enum('tipe', ['dp', 'pelunasan', 'cicilan'])->default('pelunasan');
                $table->enum('metode', ['cash', 'transfer', 'qris'])->default('cash');
                $table->unsignedBigInteger('jumlah_bayar');
                $table->string('foto_bukti')->nullable();
                $table->text('keterangan')->nullable();
                $table->date('tanggal_bayar');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_penjualans');

        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropColumn([
                'user_id',
                'metode_pembayaran',
                'total_terbayar',
                'status_pembayaran',
                'status_transaksi',
                'catatan_pembatalan',
            ]);
        });
    }
};