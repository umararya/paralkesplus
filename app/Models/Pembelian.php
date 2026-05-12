<?php
// app/Models/Pembelian.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pembelian extends Model
{
    use HasFactory;

    protected $table = 'pembelians';

    protected $fillable = [
        'tanggal_pembelian',
        'nama_barang',
        'jumlah',
        'harga_satuan',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_pembelian' => 'date',
        'jumlah'            => 'integer',
        'harga_satuan'      => 'integer',
    ];

    // Accessor: total harga (fallback jika tidak pakai virtualAs)
    public function getTotalAttribute(): int
    {
        return $this->jumlah * $this->harga_satuan;
    }

    // Format harga ke Rupiah
    public function getHargaFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->harga_satuan, 0, ',', '.');
    }

    public function getTotalFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }
}