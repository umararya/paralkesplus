<?php

namespace App\Observers;

use App\Models\Inventory;
use App\Models\InventoryLog;
use App\Models\Pembelian;

class PembelianObserver
{
    public function created(Pembelian $pembelian): void
    {
        $isBuyback = $pembelian->is_buyback ?? false;
        $kondisi   = $isBuyback ? 'bekas' : 'baru';
        $refType   = $isBuyback ? 'buyback' : 'purchase';

        // Cari atau buat inventory berdasarkan nama produk
        $inventory = Inventory::firstOrCreate(
            ['nama_produk' => $pembelian->nama_produk],
            [
                'kategori'             => $pembelian->kategori ?? null,
                'satuan'               => 'unit',
                'stok_tersedia'        => 0,
                'stok_disewa'          => 0,
                'stok_baru'            => 0,
                'stok_bekas'           => 0,
                'harga_beli_terakhir'  => $pembelian->harga_beli ?? null,
            ]
        );

        $qty = $pembelian->jumlah ?? 1;

        $inventory->tambahStok($qty, $kondisi);

        if (!$isBuyback) {
            $inventory->update(['harga_beli_terakhir' => $pembelian->harga_beli ?? $inventory->harga_beli_terakhir]);
        }

        InventoryLog::create([
            'inventory_id'   => $inventory->id,
            'reference_type' => $refType,
            'reference_id'   => $pembelian->id,
            'qty_change'     => +$qty,
            'kondisi'        => $kondisi,
            'keterangan'     => $isBuyback
                ? "Buy back dari: " . ($pembelian->nama_penjual ?? '-')
                : "Pembelian dari supplier: " . ($pembelian->supplier ?? '-'),
        ]);
    }
}