<?php
// app/Observers/PembayaranPenjualanObserver.php

namespace App\Observers;

use App\Models\PembayaranPenjualan;

class PembayaranPenjualanObserver
{
    /**
     * Setelah pembayaran baru disimpan → sync status penjualan.
     */
    public function created(PembayaranPenjualan $pembayaran): void
    {
        $pembayaran->penjualan?->syncStatusPembayaran();
    }

    /**
     * Setelah pembayaran diupdate → sync ulang status penjualan.
     */
    public function updated(PembayaranPenjualan $pembayaran): void
    {
        $pembayaran->penjualan?->syncStatusPembayaran();
    }

    /**
     * Setelah pembayaran dihapus → sync ulang status penjualan.
     */
    public function deleted(PembayaranPenjualan $pembayaran): void
    {
        $pembayaran->penjualan?->syncStatusPembayaran();
    }
}