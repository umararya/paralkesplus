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
        'status_extend',           // ← baru
        'alasan_batal_extend',     // ← baru
        'dibatalkan_extend_at',    // ← baru
    ];

    protected $casts = [
        'tgl_selesai_lama'      => 'date',
        'tgl_selesai_baru'      => 'date',
        'harga_extend'          => 'integer',
        'dibatalkan_extend_at'  => 'datetime',  // ← baru
    ];

    /* ─────────────────────────────────────────────
     |  RELASI
     ───────────────────────────────────────────── */

    public function penyewaan(): BelongsTo
    {
        return $this->belongsTo(Penyewaan::class, 'penyewaan_id');
    }

    /* ─────────────────────────────────────────────
     |  ACCESSORS
     ───────────────────────────────────────────── */

    public function getHargaExtendFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->harga_extend, 0, ',', '.');
    }

    public function getNomorExtendAttribute(): string
    {
        return 'EXT-' . str_pad($this->penyewaan_id, 5, '0', STR_PAD_LEFT)
             . '-' . str_pad($this->id, 3, '0', STR_PAD_LEFT);
    }

    /** Apakah extend ini masih aktif */
    public function getIsAktifAttribute(): bool
    {
        return $this->status_extend === 'aktif';
    }

    /** Apakah extend ini sudah dibatalkan */
    public function getIsBatalAttribute(): bool
    {
        return $this->status_extend === 'dibatalkan';
    }
}