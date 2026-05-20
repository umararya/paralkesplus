<?php
// app/Models/Penjualan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penjualan extends Model
{
    protected $table = 'penjualans';

    protected $fillable = [
        'nama_pelanggan',
        'nomor_telepon',
        'alamat_pelanggan',
        'tanggal_penjualan',
        'jenis_pembayaran',
        'diskon_global',
        'total_harga',
        'foto_bukti',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_penjualan' => 'date',
        'diskon_global'     => 'integer',
        'total_harga'       => 'integer',
    ];

    /* ── Relasi ── */

    public function details(): HasMany
    {
        return $this->hasMany(DetailPenjualan::class);
    }

    /* ── Accessor ── */

    /** Total tagihan final setelah diskon global */
    public function getTotalTagihanAttribute(): int
    {
        return max(0, ($this->total_harga ?? 0) - ($this->diskon_global ?? 0));
    }
}