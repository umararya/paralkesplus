<?php
// app/Models/Inventory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inventory extends Model
{
    protected $table = 'inventories';

    protected $fillable = [
        'nama_produk',
        'kategori',
        'satuan',
        'stok_tersedia',
        'stok_disewa',
        'stok_baru',
        'stok_bekas',
        'harga_beli_terakhir',
        'keterangan',
    ];

    protected $casts = [
        'stok_tersedia'       => 'integer',
        'stok_disewa'         => 'integer',
        'stok_baru'           => 'integer',
        'stok_bekas'          => 'integer',
        'harga_beli_terakhir' => 'integer',
    ];

    /* ── Relasi ── */

    public function logs(): HasMany
    {
        return $this->hasMany(InventoryLog::class);
    }

    public function detailPenyewaans(): HasMany
    {
        return $this->hasMany(DetailPenyewaan::class);
    }

    public function detailPenjualans(): HasMany
    {
        return $this->hasMany(DetailPenjualan::class);
    }

    /* ── Helper: Tambah Stok ── */

    /**
     * Tambah stok sesuai kondisi (baru/bekas).
     *
     * @param int    $jumlah  Jumlah unit yang dikembalikan/ditambah
     * @param string $kondisi 'baru' atau 'bekas'
     * @param bool   $dariSewa true jika pengembalian dari proses sewa
     *                         (akan mengurangi stok_disewa)
     */
    public function tambahStok(int $jumlah, string $kondisi, bool $dariSewa = false): void
    {
        $this->stok_tersedia += $jumlah;

        if ($kondisi === 'baru') {
            $this->stok_baru += $jumlah;
        } else {
            $this->stok_bekas += $jumlah;
        }

        // Jika pengembalian dari sewa, kurangi stok_disewa
        if ($dariSewa) {
            $this->stok_disewa = max(0, ($this->stok_disewa ?? 0) - $jumlah);
        }

        $this->save();
    }

    /**
     * Kurangi stok (untuk penjualan / sewa).
     *
     * @param int    $jumlah  Jumlah unit yang diambil
     * @param string $kondisi 'baru' atau 'bekas'
     * @param bool   $untukSewa true jika pengurangan untuk proses sewa
     *                          (akan menambah stok_disewa)
     */
    public function kurangiStok(int $jumlah, string $kondisi = 'baru', bool $untukSewa = false): void
    {
        $this->stok_tersedia = max(0, $this->stok_tersedia - $jumlah);

        if ($kondisi === 'baru') {
            $this->stok_baru = max(0, $this->stok_baru - $jumlah);
        } else {
            $this->stok_bekas = max(0, $this->stok_bekas - $jumlah);
        }

        // Jika untuk sewa, tambahkan ke stok_disewa
        if ($untukSewa) {
            $this->stok_disewa = ($this->stok_disewa ?? 0) + $jumlah;
        }

        $this->save();
    }

    /* ── Accessor ── */

    /** Status stok: ok / low / zero */
    public function getStokStatusAttribute(): string
    {
        $total = $this->stok_tersedia ?? 0;
        if ($total <= 0) return 'zero';
        if ($total <= 3) return 'low';
        return 'ok';
    }

    /** Label stok untuk badge */
    public function getStokLabelAttribute(): string
    {
        $total = $this->stok_tersedia ?? 0;
        if ($total <= 0) return 'Stok Habis';
        if ($total <= 3) return "Stok: {$total} (menipis)";
        return "Stok: {$total}";
    }
}