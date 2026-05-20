<?php
// app/Models/DetailPenjualan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailPenjualan extends Model
{
    protected $table = 'detail_penjualans';

    protected $fillable = [
        'penjualan_id',
        'inventory_id',
        'nama_barang',
        'kondisi',
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

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }
}