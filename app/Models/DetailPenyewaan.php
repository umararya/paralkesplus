<?php
// app/Models/DetailPenyewaan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPenyewaan extends Model
{
    protected $table = 'detail_penyewaans';

    protected $fillable = [
        'penyewaan_id',
        'inventory_id',
        'kondisi',         // <-- tambahan baru
        'nama_alat',
        'qty',
        'satuan',
        'harga_satuan',
        'diskon',
        'subtotal',
    ];

    protected $casts = [
        'qty'          => 'integer',
        'harga_satuan' => 'integer',
        'diskon'       => 'integer',
        'subtotal'     => 'integer',
    ];

    /* ── Relasi ── */

    public function penyewaan(): BelongsTo
    {
        return $this->belongsTo(Penyewaan::class);
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    /* ── Accessor ── */

    /** Label kondisi human-friendly */
    public function getKondisiLabelAttribute(): string
    {
        return match ($this->kondisi) {
            'baru'  => 'Baru',
            'bekas' => 'Bekas',
            default => ucfirst($this->kondisi ?? ''),
        };
    }
}