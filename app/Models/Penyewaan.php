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
        'produk_alkes',        // tetap ada — backward compatibility data lama
        'tgl_mulai',
        'tgl_selesai',
        'durasi_hari',
        'pengiriman',
        'biaya_ongkir',
        'total_harga_sewa',    // NEW — sum subtotal semua item detail
        'diskon_global',       // NEW — potongan nominal di level header (Rp)
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

    /**
     * Total akhir = total_harga_sewa - diskon_global + biaya_ongkir
     */
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
    //  HELPER — deteksi mode render invoice
    // =========================================================

    /**
     * True  = sudah pakai sistem detail baru (tabel detail_penyewaans)
     * False = masih data lama (produk_alkes CSV)
     */
    public function getHasDetailAttribute(): bool
    {
        return $this->details()->exists();
    }
}