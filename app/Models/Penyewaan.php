<?php
// app/Models/Penyewaan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Penyewaan extends Model
{
    use HasFactory;

    protected $table = 'penyewaans';

    protected $fillable = [
        'nama_penyewa',
        'nomor_telepon',
        'tempat_tanggal_lahir',  // ← BARU
        'nomor_ktp',             // ← BARU
        'produk_alkes',
        'tgl_mulai',
        'tgl_selesai',
        'durasi_hari',
        'pengiriman',
        'biaya_ongkir',
        'total_harga_sewa',
        'diskon_global',
        'alamat_penyewa',
        'metode_pembayaran',
        'bukti_pembayaran',
        'foto_ktp_sim',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'durasi_hari'      => 'integer',
        'biaya_ongkir'     => 'integer',
        'total_harga_sewa' => 'integer',
        'diskon_global'    => 'integer',
        'tgl_mulai'        => 'date',
        'tgl_selesai'      => 'date',
    ];

    // =========================================================
    //  RELASI
    // =========================================================

    public function details()
    {
        return $this->hasMany(DetailPenyewaan::class, 'penyewaan_id');
    }

    // =========================================================
    //  ACCESSOR — kalkulasi tagihan
    // =========================================================

    public function getTotalTagihanAttribute(): int
    {
        return max(0,
            ($this->total_harga_sewa ?? 0)
            - ($this->diskon_global ?? 0)
            + ($this->biaya_ongkir ?? 0)
        );
    }

    public function getTotalTagihanFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->total_tagihan, 0, ',', '.');
    }

    public function getTotalHargaSewaFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->total_harga_sewa ?? 0, 0, ',', '.');
    }

    public function getDiskonGlobalFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->diskon_global ?? 0, 0, ',', '.');
    }

    public function getBiayaOngkirFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->biaya_ongkir ?? 0, 0, ',', '.');
    }

    // =========================================================
    //  ACCESSOR — nama alat (support data lama & baru)
    // =========================================================

    /**
     * Otomatis ambil dari detail_penyewaans jika ada,
     * fallback ke produk_alkes untuk data lama.
     */
    public function getNamaAlatAttribute(): string
    {
        if ($this->relationLoaded('details') && $this->details->isNotEmpty()) {
            return $this->details->pluck('nama_alat')->implode(', ');
        }

        $detail = $this->details()->pluck('nama_alat');
        if ($detail->isNotEmpty()) {
            return $detail->implode(', ');
        }

        return $this->produk_alkes ?? '—';
    }

    // =========================================================
    //  ACCESSOR — status & label
    // =========================================================

    public function getSisaHariAttribute(): int
    {
        if (!$this->tgl_selesai) return 0;
        $today   = Carbon::today();
        $selesai = Carbon::parse($this->tgl_selesai)->startOfDay();
        return (int) $today->diffInDays($selesai, false);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'berjalan'          => 'Berjalan',
            'segera_konfirmasi' => 'Segera Konfirmasi!',
            'selesai'           => 'Selesai',
            default             => ucfirst($this->status),
        };
    }

    public function getStatusClassAttribute(): string
    {
        return match($this->status) {
            'berjalan'          => 'status-berjalan',
            'segera_konfirmasi' => 'status-konfirmasi',
            'selesai'           => 'status-selesai',
            default             => 'status-berjalan',
        };
    }

    public function getPengirimanLabelAttribute(): string
    {
        return match($this->pengiriman) {
            'mandiri'               => 'Mandiri',
            'Gosend / GrabExpress'  => 'Gosend / GrabExpress',
            'Rental Mobil Paralkes' => 'Rental Mobil Paralkes',
            default                 => $this->pengiriman ?? '-',
        };
    }

    // =========================================================
    //  HELPER
    // =========================================================

    public function getHasDetailAttribute(): bool
    {
        return $this->details()->exists();
    }
}