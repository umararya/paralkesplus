<?php

namespace App\Observers;

use App\Models\Inventory;
use App\Models\InventoryLog;
use App\Models\Penjualan;

class PenjualanObserver
{
    public function created(Penjualan $penjualan): void
    {
        $inventory = Inventory::where('nama_produk', $penjualan->nama_produk)->first();

        if (!$inventory) return;

        $qty = $penjualan->jumlah ?? 1;

        $inventory->kurangiStokPenjualan($qty);

        InventoryLog::create([
            'inventory_id'   => $inventory->id,
            'reference_type' => 'sale',
            'reference_id'   => $penjualan->id,
            'qty_change'     => -$qty,
            'kondisi'        => 'baru',
            'keterangan'     => "Penjualan kepada: " . ($penjualan->nama_pembeli ?? '-'),
        ]);
    }
}