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
        'foto_bukti',   // ← BARU
    ];

    protected $casts = [
        'tanggal_penjualan' => 'date',
        'harga'             => 'decimal:2',
        'total'             => 'decimal:2',
        'qty'               => 'integer',
    ];
}