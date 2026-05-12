<?php
// app/Models/Penyewaan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penyewaan extends Model
{
    use HasFactory;

    protected $table = 'penyewaans';

    protected $fillable = [
        'nama_penyewa',
        'nomor_telepon',
        'produk_alkes',
        'durasi_hari',
        'pengiriman_ditanggung_pelanggan',
        'biaya_ongkir',
        'alamat_penyewa',
        'metode_pembayaran',
        'bukti_pembayaran',
        'foto_ktp_sim',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'durasi_hari'                     => 'integer',
        'pengiriman_ditanggung_pelanggan' => 'boolean',
        'biaya_ongkir'                    => 'integer',
    ];

    // Accessor: label status yang lebih ramah
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'berjalan'          => 'Berjalan',
            'segera_konfirmasi' => 'Segera Konfirmasi',
            'selesai'           => 'Selesai',
            default             => ucfirst($this->status),
        };
    }

    // Accessor: class badge status
    public function getStatusClassAttribute(): string
    {
        return match($this->status) {
            'berjalan'          => 'status-berjalan',
            'segera_konfirmasi' => 'status-konfirmasi',
            'selesai'           => 'status-selesai',
            default             => 'status-berjalan',
        };
    }

    // Accessor: format biaya ongkir ke Rupiah
    public function getBiayaOngkirFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->biaya_ongkir, 0, ',', '.');
    }
}