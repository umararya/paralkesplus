<?php
// app/Models/Pembelian.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    protected $fillable = [
        'tanggal_pembelian',
        'nama_barang',
        'jumlah',
        'harga_satuan',
        'keterangan',
        'bukti_transaksi',
    ];

    protected $appends = ['harga_formatted', 'total_formatted'];

    public function getHargaFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->harga_satuan, 0, ',', '.');
    }

    public function getTotalFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }
}