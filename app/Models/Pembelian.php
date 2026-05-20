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
        'kondisi_barang',
        'keterangan',
        'bukti_transaksi',
        'status',
        'penjualan_id',
        'nama_pelanggan',
    ];

    protected $appends = ['total', 'harga_formatted', 'total_formatted'];

    // ── Auto-hitung total sebelum simpan ──
    protected static function booted(): void
    {
        static::saving(function (Pembelian $model) {
            $model->total = $model->jumlah * $model->harga_satuan;
        });
    }

    public function getTotalAttribute(): float
    {
        // Fallback jika kolom total ada di DB
        return ($this->jumlah ?? 0) * ($this->harga_satuan ?? 0);
    }

    public function getHargaFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->harga_satuan ?? 0, 0, ',', '.');
    }

    public function getTotalFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->getTotalAttribute(), 0, ',', '.');
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