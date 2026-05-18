<?php

namespace App\Observers;

use App\Models\DetailPenyewaan;
use App\Models\Inventory;
use App\Models\InventoryLog;
use App\Models\Penyewaan;

class PenyewaanObserver
{
    /**
     * Saat penyewaan dibuat: kurangi stok tersedia tiap item
     */
    public function created(Penyewaan $penyewaan): void
    {
        foreach ($penyewaan->details as $detail) {
            $inventory = Inventory::where('nama_produk', $detail->nama_produk)->first();
            if (!$inventory) continue;

            $qty = $detail->jumlah ?? 1;
            $inventory->keluarkanUntukSewa($qty);

            InventoryLog::create([
                'inventory_id'   => $inventory->id,
                'reference_type' => 'rental_start',
                'reference_id'   => $penyewaan->id,
                'qty_change'     => -$qty,
                'kondisi'        => 'baru',
                'keterangan'     => "Disewa oleh: " . ($penyewaan->nama_penyewa ?? '-'),
            ]);
        }
    }

    /**
     * Saat status penyewaan diupdate ke 'selesai': kembalikan stok
     */
    public function updated(Penyewaan $penyewaan): void
    {
        if ($penyewaan->isDirty('status') && $penyewaan->status === 'selesai') {
            foreach ($penyewaan->details as $detail) {
                $inventory = Inventory::where('nama_produk', $detail->nama_produk)->first();
                if (!$inventory) continue;

                $qty = $detail->jumlah ?? 1;
                $inventory->kembalikanDariSewa($qty);

                InventoryLog::create([
                    'inventory_id'   => $inventory->id,
                    'reference_type' => 'rental_return',
                    'reference_id'   => $penyewaan->id,
                    'qty_change'     => +$qty,
                    'kondisi'        => 'baru',
                    'keterangan'     => "Kembali dari sewa: " . ($penyewaan->nama_penyewa ?? '-'),
                ]);
            }
        }
    }
}