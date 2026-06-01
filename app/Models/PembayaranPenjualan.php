<?php
// app/Models/PembayaranPenjualan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembayaranPenjualan extends Model
{
    protected $table = 'pembayaran_penjualans';

    protected $fillable = [
        'penjualan_id',
        'created_by',
        'tipe',
        'metode',
        'jumlah_bayar',
        'foto_bukti',
        'keterangan',
        'tanggal_bayar',
    ];

    protected $casts = [
        'jumlah_bayar'  => 'integer',
        'tanggal_bayar' => 'date',
    ];

    // =========================================================
    //  RELASI
    // =========================================================

    public function penjualan(): BelongsTo
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // =========================================================
    //  ACCESSORS
    // =========================================================

    public function getJumlahBayarFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->jumlah_bayar, 0, ',', '.');
    }

    public function getTipeLabelAttribute(): string
    {
        return match($this->tipe) {
            'dp'        => 'Down Payment',
            'pelunasan' => 'Pelunasan',
            'cicilan'   => 'Cicilan',
            default     => ucfirst($this->tipe ?? '-'),
        };
    }
}