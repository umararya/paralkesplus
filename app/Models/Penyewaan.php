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
        'tgl_mulai',
        'tgl_selesai',
        'durasi_hari',
        'pengiriman',
        'biaya_ongkir',
        'alamat_penyewa',
        'metode_pembayaran',
        'bukti_pembayaran',
        'foto_ktp_sim',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'durasi_hari'  => 'integer',
        'biaya_ongkir' => 'integer',
        'tgl_mulai'    => 'date',
        'tgl_selesai'  => 'date',
    ];

    /**
     * Label status untuk tampil di UI
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'berjalan'          => 'Berjalan',
            'segera_konfirmasi' => 'Segera Konfirmasi',
            'selesai'           => 'Selesai',
            default             => ucfirst($this->status),
        };
    }

    /**
     * CSS class badge status
     */
    public function getStatusClassAttribute(): string
    {
        return match($this->status) {
            'berjalan'          => 'status-berjalan',
            'segera_konfirmasi' => 'status-konfirmasi',
            'selesai'           => 'status-selesai',
            default             => 'status-berjalan',
        };
    }

    /**
     * Label pengiriman singkat untuk tabel
     */
    public function getPengirimanLabelAttribute(): string
    {
        return match($this->pengiriman) {
            'mandiri'               => 'Mandiri',
            'Gosend / GrabExpress'  => 'Gosend / GrabExpress',
            'Rental Mobil Paralkes' => 'Rental Mobil Paralkes',
            default                 => $this->pengiriman,
        };
    }

    /**
     * Format biaya ongkir ke Rupiah
     */
    public function getBiayaOngkirFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->biaya_ongkir, 0, ',', '.');
    }
}