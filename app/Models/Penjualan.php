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

    /* ── Accessors ── */

    /** Total tagihan final setelah diskon global */
    public function getTotalTagihanAttribute(): int
    {
        return max(0, ($this->total_harga ?? 0) - ($this->diskon_global ?? 0));
    }

    /**
     * Nama barang: gabungkan dari detail (tampil di tabel index).
     * Accessor ini dipakai oleh view index: $item->nama_barang
     */
    public function getNamaBarangAttribute(): string
    {
        if ($this->relationLoaded('details') && $this->details->count() > 0) {
            return $this->details->pluck('nama_barang')->filter()->implode(', ');
        }
        return '—';
    }

    /**
     * Total qty dari semua detail.
     * Accessor ini dipakai oleh view index: $item->qty
     */
    public function getQtyAttribute(): int
    {
        if ($this->relationLoaded('details') && $this->details->count() > 0) {
            return (int) $this->details->sum('qty');
        }
        return 0;
    }

    /**
     * Harga satuan pertama dari detail (untuk tampilan ringkas di tabel).
     * Accessor ini dipakai oleh view index: $item->harga
     */
    public function getHargaAttribute(): int
    {
        if ($this->relationLoaded('details') && $this->details->count() > 0) {
            return (int) ($this->details->first()->harga_satuan ?? 0);
        }
        return 0;
    }

    /**
     * Total harga semua detail (alias total_harga).
     * Accessor ini dipakai oleh view index: $item->total
     */
    public function getTotalAttribute(): int
    {
        return $this->total_harga ?? 0;
    }
}