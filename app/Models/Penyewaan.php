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
        'produk_alkes',
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

    /* ── Accessors ── */

    /**
     * Durasi hari — selalu dihitung dari tgl_mulai → tgl_selesai (local-safe).
     * Memfix data lama yang salah karena timezone UTC offset.
     */
    public function getDurasiHariAttribute(): int
    {
        if ($this->tgl_mulai && $this->tgl_selesai) {
            $start = Carbon::parse($this->tgl_mulai->format('Y-m-d'))->startOfDay();
            $end   = Carbon::parse($this->tgl_selesai->format('Y-m-d'))->startOfDay();
            return (int) $start->diffInDays($end);
        }
        return (int) ($this->attributes['durasi_hari'] ?? 0);
    }

    /**
     * Sisa hari sampai tgl_selesai (negatif = sudah lewat).
     * FIX: parse dari string Y-m-d agar tidak kena offset UTC,
     * lalu bandingkan dengan today() tanpa komponen waktu.
     */
    public function getSisaHariAttribute(): int
    {
        if (! $this->tgl_selesai) return 0;

        // Ambil string tanggal murni dari DB, hindari timezone shift
        $selesai = Carbon::parse($this->tgl_selesai->format('Y-m-d'))->startOfDay();
        $today   = Carbon::today()->startOfDay();

        return (int) $today->diffInDays($selesai, false);
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

    /**
     * Nama alat: gabungkan dari detail (sistem baru)
     * atau fallback ke produk_alkes (legacy).
     */
    public function getNamaAlatAttribute(): string
    {
        if ($this->relationLoaded('details') && $this->details->count() > 0) {
            return $this->details->pluck('nama_alat')->filter()->implode(', ');
        }
        return $this->produk_alkes ?? '—';
    }

    /** Label pengiriman human-friendly */
    public function getPengirimanLabelAttribute(): string
    {
        return match ($this->pengiriman) {
            'mandiri'               => 'Mandiri',
            'Gosend / GrabExpress'  => 'Gosend / GrabExpress',
            'Rental Mobil Paralkes' => 'Rental Mobil',
            default                 => $this->pengiriman ?? '—',
        };
    }

    /** Biaya ongkir diformat Rupiah */
    public function getBiayaOngkirFormattedAttribute(): string
    {
        $nominal = $this->biaya_ongkir ?? 0;
        if ($nominal <= 0) return '—';
        return 'Rp ' . number_format($nominal, 0, ',', '.');
    }

    /** Label status human-friendly */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'berjalan'          => 'Berjalan',
            'segera_konfirmasi' => 'Segera Konfirmasi',
            'selesai'           => 'Selesai',
            'dibatalkan'        => 'Dibatalkan',
            default             => ucfirst($this->status ?? ''),
        };
    }

    /** CSS class untuk badge status */
    public function getStatusClassAttribute(): string
    {
        return match ($this->status) {
            'berjalan'          => 'status-berjalan',
            'segera_konfirmasi' => 'status-konfirmasi',
            'selesai'           => 'status-selesai',
            'dibatalkan'        => 'status-selesai',
            default             => 'status-berjalan',
        };
    }
}