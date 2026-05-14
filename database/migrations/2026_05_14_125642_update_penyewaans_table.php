<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penyewaans', function (Blueprint $table) {

            // Produk alkes → text (untuk menyimpan multiple item CSV)
            $table->text('produk_alkes')->change();

            // Kolom tanggal mulai & selesai
            if (!Schema::hasColumn('penyewaans', 'tgl_mulai')) {
                $table->date('tgl_mulai')->nullable()->after('produk_alkes');
            }
            if (!Schema::hasColumn('penyewaans', 'tgl_selesai')) {
                $table->date('tgl_selesai')->nullable()->after('tgl_mulai');
            }

            // Hapus kolom lama pengiriman boolean, ganti dengan string enum
            if (Schema::hasColumn('penyewaans', 'pengiriman_ditanggung_pelanggan')) {
                $table->dropColumn('pengiriman_ditanggung_pelanggan');
            }
            if (!Schema::hasColumn('penyewaans', 'pengiriman')) {
                $table->string('pengiriman', 50)->default('mandiri')->after('durasi_hari');
            }

            // Alamat penyewa
            if (!Schema::hasColumn('penyewaans', 'alamat_penyewa')) {
                $table->text('alamat_penyewa')->nullable()->after('biaya_ongkir');
            }

            // Bukti pembayaran → text untuk URL/teks
            if (Schema::hasColumn('penyewaans', 'bukti_pembayaran')) {
                $table->text('bukti_pembayaran')->nullable()->change();
            } else {
                $table->text('bukti_pembayaran')->nullable()->after('metode_pembayaran');
            }

            // Foto KTP/SIM → string path storage
            if (Schema::hasColumn('penyewaans', 'foto_ktp_sim')) {
                $table->string('foto_ktp_sim', 500)->nullable()->change();
            } else {
                $table->string('foto_ktp_sim', 500)->nullable()->after('bukti_pembayaran');
            }

        });
    }

    public function down(): void
    {
        Schema::table('penyewaans', function (Blueprint $table) {
            $table->dropColumn(['tgl_mulai', 'tgl_selesai', 'pengiriman', 'alamat_penyewa']);
            $table->boolean('pengiriman_ditanggung_pelanggan')->default(true)->after('durasi_hari');
        });
    }
};