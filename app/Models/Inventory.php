<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $fillable = [
        'nama_produk',
        'kategori',
        'satuan',
        'stok_tersedia',
        'stok_disewa',
        'stok_baru',
        'stok_bekas',
        'harga_beli_terakhir',
    ];

    public function logs()
    {
        return $this->hasMany(InventoryLog::class);
    }

    /**
     * Total stok keseluruhan (tersedia + sedang disewa)
     */
    public function getTotalStokAttribute(): int
    {
        return $this->stok_tersedia + $this->stok_disewa;
    }

    /**
     * Tambah stok (masuk: pembelian, return sewa, buyback)
     */
    public function tambahStok(int $qty, string $kondisi = 'baru'): void
    {
        $this->increment('stok_tersedia', $qty);

        if ($kondisi === 'bekas') {
            $this->increment('stok_bekas', $qty);
        } else {
            $this->increment('stok_baru', $qty);
        }
    }

    /**
     * Kurangi stok untuk penjualan
     */
    public function kurangiStokPenjualan(int $qty): void
    {
        $this->decrement('stok_tersedia', $qty);
        // Kurangi dari stok baru dulu, kalau habis ambil dari bekas
        $kurangiBaru = min($qty, $this->stok_baru);
        $kurangiBekas = $qty - $kurangiBaru;
        $this->decrement('stok_baru', $kurangiBaru);
        if ($kurangiBekas > 0) {
            $this->decrement('stok_bekas', $kurangiBekas);
        }
    }

    /**
     * Kurangi stok saat penyewaan dimulai
     */
    public function keluarkanUntukSewa(int $qty): void
    {
        $this->decrement('stok_tersedia', $qty);
        $this->increment('stok_disewa', $qty);
    }

    /**
     * Kembalikan stok saat sewa selesai
     */
    public function kembalikanDariSewa(int $qty): void
    {
        $this->increment('stok_tersedia', $qty);
        $this->decrement('stok_disewa', $qty);
    }
}