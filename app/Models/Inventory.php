<?php
// app/Models/Inventory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    protected $table = 'inventories';

    protected $fillable = [
        'nama_alat',
        'kategori',
        'satuan',
        'stok_baru',
        'stok_bekas',
        'stok_rusak',
        'harga_beli_baru',
        'harga_beli_bekas',
        'harga_sewa',
        'foto',
        'keterangan',
    ];

    protected $casts = [
        'stok_baru'        => 'integer',
        'stok_bekas'       => 'integer',
        'stok_rusak'       => 'integer',
        'harga_beli_baru'  => 'integer',
        'harga_beli_bekas' => 'integer',
        'harga_sewa'       => 'integer',
    ];

    /* ── Relasi ── */

    public function detailPenyewaans(): HasMany
    {
        return $this->hasMany(DetailPenyewaan::class);
    }

    public function detailPenjualans(): HasMany
    {
        return $this->hasMany(DetailPenjualan::class);
    }

    /* ── Accessor ── */

    /** Total stok tersedia (baru + bekas) */
    public function getStokTersediaAttribute(): int
    {
        return ($this->stok_baru ?? 0) + ($this->stok_bekas ?? 0);
    }

    /** Status stok: ok / low / zero */
    public function getStokStatusAttribute(): string
    {
        $total = $this->stok_tersedia;
        if ($total <= 0) return 'zero';
        if ($total <= 3) return 'low';
        return 'ok';
    }

    /** Label stok untuk badge */
    public function getStokLabelAttribute(): string
    {
        $total = $this->stok_tersedia;
        if ($total <= 0) return 'Stok Habis';
        if ($total <= 3) return "Stok: {$total} (menipis)";
        return "Stok: {$total}";
    }

    /** Harga beli terakhir yang relevan */
    public function getHargaBeliTerakhirAttribute(): int
    {
        return $this->harga_beli_baru ?? $this->harga_beli_bekas ?? 0;
    }
}