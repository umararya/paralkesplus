<?php
// app/Observers/PembelianObserver.php

namespace App\Observers;

use App\Models\Pembelian;

class PembelianObserver
{
    /**
     * Semua logic inventory sudah ditangani di PembelianController@store
     * menggunakan DB::transaction. Observer ini sengaja dikosongkan
     * agar tidak terjadi double insert ke tabel inventories.
     */
    public function created(Pembelian $pembelian): void
    {
        // intentionally empty — handled in controller
    }

    public function updated(Pembelian $pembelian): void
    {
        //
    }

    public function deleted(Pembelian $pembelian): void
    {
        //
    }
}