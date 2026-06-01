<?php

namespace App\Observers;

use App\Models\Inventory;
use App\Models\InventoryLog;
use App\Models\Penjualan;

class PenjualanObserver
{
    /**
     * Dipanggil saat transaksi BARU dibuat.
     * Barang langsung diserahkan → stok langsung berkurang.
     */
    public function created(Penjualan $penjualan): void
    {
        if ($penjualan->status_transaksi === 'batal') {
            return;
        }

        $this->kurangiStok($penjualan);
    }

    /**
     * Dipanggil saat transaksi diupdate.
     * Hanya proses jika status_transaksi berubah menjadi 'batal'.
     */
    public function updated(Penjualan $penjualan): void
    {
        $statusBerubah   = $penjualan->wasChanged('status_transaksi');
        $jadiBatal       = $penjualan->status_transaksi === 'batal';
        $sebelumnyaBatal = $penjualan->getOriginal('status_transaksi') === 'batal';

        if ($statusBerubah && $jadiBatal && ! $sebelumnyaBatal) {
            $this->rollbackStok($penjualan);
        }
    }

    // ── Private Helpers ──────────────────────────────────────

    private function kurangiStok(Penjualan $penjualan): void
    {
        $penjualan->loadMissing('detailPenjualans');

        foreach ($penjualan->detailPenjualans as $detail) {
            $inventory = Inventory::find($detail->inventory_id);

            if (! $inventory) {
                continue;
            }

            $stokSebelum = $inventory->stok_tersedia;

            // Pastikan stok tidak minus
            $qtyKurang = min($detail->qty, $stokSebelum);
            $inventory->decrement('stok_tersedia', $qtyKurang);
            $inventory->refresh();

            InventoryLog::create([
                'inventory_id'   => $inventory->id,
                'reference_type' => 'sale',                         // sesuai konvensi yang sudah ada
                'reference_id'   => $penjualan->id,
                'qty_change'     => -$qtyKurang,                    // negatif = keluar
                'kondisi'        => $detail->kondisi ?? 'baru',     // ambil dari detail jika ada
                'stok_sebelum'   => $stokSebelum,
                'stok_sesudah'   => $inventory->stok_tersedia,
                'keterangan'     => "Penjualan #{$penjualan->id} kepada {$penjualan->nama_pelanggan}",
                'created_by'     => auth()->id() ?? $penjualan->user_id,
            ]);
        }
    }

    private function rollbackStok(Penjualan $penjualan): void
    {
        $penjualan->loadMissing('detailPenjualans');

        foreach ($penjualan->detailPenjualans as $detail) {
            $inventory = Inventory::find($detail->inventory_id);

            if (! $inventory) {
                continue;
            }

            $stokSebelum = $inventory->stok_tersedia;
            $inventory->increment('stok_tersedia', $detail->qty);
            $inventory->refresh();

            InventoryLog::create([
                'inventory_id'   => $inventory->id,
                'reference_type' => 'sale_cancelled',               // konvensi baru untuk pembatalan
                'reference_id'   => $penjualan->id,
                'qty_change'     => $detail->qty,                   // positif = masuk kembali
                'kondisi'        => $detail->kondisi ?? 'baru',
                'stok_sebelum'   => $stokSebelum,
                'stok_sesudah'   => $inventory->stok_tersedia,
                'keterangan'     => "Pembatalan Penjualan #{$penjualan->id} - {$penjualan->nama_pelanggan}",
                'created_by'     => auth()->id() ?? $penjualan->user_id,
            ]);
        }
    }
}