<?php
// app/Models/Penjualan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penjualan extends Model
{
    protected $table = 'penjualans';

    protected $fillable = [
        'user_id',
        'nama_pelanggan',
        'nomor_telepon',
        'alamat_pelanggan',
        'tanggal_penjualan',
        'jenis_pembayaran',
        'metode_pembayaran',
        'diskon_global',
        'total_harga',
        'total_terbayar',
        'foto_bukti',
        'keterangan',
        'status_pembayaran',
        'status_transaksi',
        'catatan_pembatalan',
    ];

    protected $casts = [
        'tanggal_penjualan' => 'date',
        'diskon_global'     => 'integer',
        'total_harga'       => 'integer',
        'total_terbayar'    => 'integer',
    ];

    // =========================================================
    //  RELASI
    // =========================================================

    public function details(): HasMany
    {
        return $this->hasMany(DetailPenjualan::class);
    }

    public function pembayarans(): HasMany
    {
        return $this->hasMany(PembayaranPenjualan::class)->orderBy('tanggal_bayar', 'asc');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // =========================================================
    //  ACCESSORS — Kalkulasi Keuangan
    // =========================================================

    /**
     * Total tagihan final setelah diskon global.
     */
    public function getTotalTagihanAttribute(): int
    {
        return max(0, ($this->total_harga ?? 0) - ($this->diskon_global ?? 0));
    }

    /**
     * Alias total_tagihan — dipakai di view show.
     */
    public function getTotalBayarAttribute(): int
    {
        return $this->total_tagihan;
    }

    /**
     * Sisa tagihan yang belum dibayar.
     */
    public function getSisaTagihanAttribute(): int
    {
        return max(0, $this->total_tagihan - ($this->total_terbayar ?? 0));
    }

    /**
     * Formatted accessors untuk tampilan view.
     */
    public function getTotalBayarFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->total_bayar, 0, ',', '.');
    }

    public function getTotalTerbayarFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->total_terbayar ?? 0, 0, ',', '.');
    }

    public function getSisaTagihanFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->sisa_tagihan, 0, ',', '.');
    }

    // =========================================================
    //  ACCESSORS — Label & Status
    // =========================================================

    public function getStatusPembayaranLabelAttribute(): string
    {
        return match($this->status_pembayaran) {
            'lunas'       => 'Lunas',
            'dp'          => 'DP / Sebagian',
            'belum_lunas' => 'Belum Lunas',
            default       => ucfirst($this->status_pembayaran ?? '-'),
        };
    }

    public function getStatusTransaksiLabelAttribute(): string
    {
        return match($this->status_transaksi) {
            'aktif'   => 'Aktif',
            'selesai' => 'Selesai',
            'batal'   => 'Dibatalkan',
            default   => ucfirst($this->status_transaksi ?? '-'),
        };
    }

    // =========================================================
    //  ACCESSORS — Backward Compatibility (dipakai index.blade)
    // =========================================================

    /**
     * Nama barang gabungan dari detail — dipakai di tabel index.
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
     */
    public function getQtyAttribute(): int
    {
        if ($this->relationLoaded('details') && $this->details->count() > 0) {
            return (int) $this->details->sum('qty');
        }
        return 0;
    }

    /**
     * Harga satuan pertama dari detail.
     */
    public function getHargaAttribute(): int
    {
        if ($this->relationLoaded('details') && $this->details->count() > 0) {
            return (int) ($this->details->first()->harga_satuan ?? 0);
        }
        return 0;
    }

    /**
     * Alias total_harga.
     */
    public function getTotalAttribute(): int
    {
        return $this->total_harga ?? 0;
    }

    // =========================================================
    //  HELPER METHODS — Dipakai Controller & View
    // =========================================================

    /**
     * Apakah transaksi sudah dibatalkan?
     */
    public function isBatal(): bool
    {
        return $this->status_transaksi === 'batal';
    }

    /**
     * Apakah pembayaran sudah lunas?
     */
    public function isLunas(): bool
    {
        return $this->status_pembayaran === 'lunas';
    }

    /**
     * Apakah masih bisa ditambah pembayaran?
     */
    public function bisaTambahPembayaran(): bool
    {
        return !$this->isBatal()
            && !$this->isLunas()
            && $this->sisa_tagihan > 0;
    }

    /**
     * Sync total_terbayar dan status_pembayaran dari relasi pembayarans.
     * Dipanggil oleh PembayaranPenjualanObserver.
     */
    public function syncStatusPembayaran(): void
    {
        // Reload dari DB agar sum akurat
        $totalTerbayar = $this->pembayarans()->sum('jumlah_bayar');

        $status = 'belum_lunas';
        if ($totalTerbayar >= $this->total_tagihan && $this->total_tagihan > 0) {
            $status = 'lunas';
        } elseif ($totalTerbayar > 0) {
            $status = 'dp';
        }

        $statusTransaksi = $this->status_transaksi;
        if ($status === 'lunas' && $statusTransaksi === 'aktif') {
            $statusTransaksi = 'selesai';
        }

        // Pakai query builder agar tidak trigger observer lagi (hindari infinite loop)
        static::withoutEvents(function () use ($totalTerbayar, $status, $statusTransaksi) {
            $this->update([
                'total_terbayar'    => $totalTerbayar,
                'status_pembayaran' => $status,
                'status_transaksi'  => $statusTransaksi,
            ]);
        });
    }
}