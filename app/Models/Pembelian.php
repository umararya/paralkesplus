<?php
// app/Models/Pembelian.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    protected $fillable = [
        'tanggal_pembelian',
        'no_invoice',         // ← TAMBAHAN
        'nama_barang',
        'jumlah',
        'harga_satuan',
        'kondisi_barang',
        'keterangan',
        'bukti_transaksi',
        'file_invoice',
        'status',
        'penjualan_id',
        'nama_pelanggan',
    ];

    // total adalah generated column di MySQL (jumlah * harga_satuan)
    protected $appends = ['harga_formatted', 'total_formatted'];

    public function getHargaFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->harga_satuan ?? 0, 0, ',', '.');
    }

    public function getTotalFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->attributes['total'] ?? 0, 0, ',', '.');
    }

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'penjualan_id');
    }

    public function isBuyBack(): bool
    {
        return $this->status === 'buy_back';
    }
}