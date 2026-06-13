<?php
// app/Models/PenyewaanExtend.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenyewaanExtend extends Model
{
    protected $table = 'penyewaan_extends';

    protected $fillable = [
        'penyewaan_id',
        'tgl_selesai_lama',
        'tgl_selesai_baru',
        'tambah_hari',
        'harga_extend',
        'metode_bayar',
        'bukti_transfer',
        'catatan',
    ];

    protected $casts = [
        'tgl_selesai_lama' => 'date',
        'tgl_selesai_baru' => 'date',
        'harga_extend'     => 'integer',
    ];

    public function penyewaan(): BelongsTo
    {
        return $this->belongsTo(Penyewaan::class, 'penyewaan_id');
    }

    public function getHargaExtendFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->harga_extend, 0, ',', '.');
    }

    public function getNomorExtendAttribute(): string
    {
        return 'EXT-' . str_pad($this->penyewaan_id, 5, '0', STR_PAD_LEFT)
             . '-' . str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }
}