<?php
// app/Models/Penyewaan.php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Penyewaan extends Model
{
    protected $table = 'penyewaans';

    protected $fillable = [
        'nama_penyewa',
        'nomor_telepon',
        'tempat_tanggal_lahir',
        'nomor_ktp',
        'alamat_penyewa',
        'produk_alkes',          // legacy — tetap ada agar data lama tidak rusak
        'tgl_mulai',
        'tgl_selesai',
        'durasi_hari',
        'pengiriman',
        'biaya_ongkir',
        'diskon_global',
        'total_harga_sewa',
        'metode_pembayaran',
        'bukti_pembayaran',
        'foto_ktp_sim',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tgl_mulai'        => 'date',
        'tgl_selesai'      => 'date',
        'durasi_hari'      => 'integer',
        'biaya_ongkir'     => 'integer',
        'diskon_global'    => 'integer',
        'total_harga_sewa' => 'integer',
    ];

    /* ── Relasi ── */

    public function details(): HasMany
    {
        return $this->hasMany(DetailPenyewaan::class);
    }

    /* ── Accessor ── */

    /** Sisa hari sampai tgl_selesai (negatif = sudah lewat) */
    public function getSisaHariAttribute(): int
    {
        if (! $this->tgl_selesai) return 0;
        return (int) Carbon::today()->diffInDays($this->tgl_selesai, false);
    }

    /** Apakah sudah pakai sistem detail baru */
    public function getHasDetailAttribute(): bool
    {
        return $this->details()->exists();
    }

    /** Total tagihan final (subtotal items - diskon global + ongkir) */
    public function getTotalTagihanAttribute(): int
    {
        return max(0, ($this->total_harga_sewa ?? 0)
            - ($this->diskon_global ?? 0)
            + ($this->biaya_ongkir ?? 0));
    }

    /** Label status human-friendly */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'berjalan'          => 'Berjalan',
            'segera_konfirmasi' => 'Segera Konfirmasi',
            'selesai'           => 'Selesai',
            'dibatalkan'        => 'Dibatalkan',
            default             => ucfirst($this->status),
        };
    }
}