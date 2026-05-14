<?php
// app/Models/Penjualan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Penjualan extends Model
{
    use HasFactory;

    protected $table = 'penjualans';

    protected $fillable = [
        'tanggal_penjualan',
        'nama_barang',
        'qty',
        'alamat_pelanggan',
        'jenis_pembayaran',
        'harga',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_penjualan' => 'date',
        'harga'             => 'decimal:2',
        'total'             => 'decimal:2',
        'qty'               => 'integer',
    ];

    /**
     * Jika tidak pakai generated column, hitung total secara manual.
     * Aktifkan method ini dan hapus storedAs dari migration.
     */
    // public function getTotalAttribute(): float
    // {
    //     return (float) $this->harga * $this->qty;
    // }
}