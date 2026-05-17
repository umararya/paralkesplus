<?php
// app/Models/DetailPenyewaan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailPenyewaan extends Model
{
    use HasFactory;

    protected $table = 'detail_penyewaans';

    protected $fillable = [
        'penyewaan_id',
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

    // ── Relasi ke header penyewaan
    public function penyewaan()
    {
        return $this->belongsTo(Penyewaan::class, 'penyewaan_id');
    }

    // ── Accessor format Rupiah
    public function getHargaSatuanFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->harga_satuan, 0, ',', '.');
    }

    public function getSubtotalFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    // ── Hitung otomatis subtotal sebelum disimpan
    protected static function booted(): void
    {
        static::saving(function (DetailPenyewaan $item) {
            $item->subtotal = (int) round(
                $item->qty * $item->harga_satuan * (1 - $item->diskon / 100)
            );
        });
    }
}