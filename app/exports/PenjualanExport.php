<?php
// app/exports/PenjualanExport.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PenjualanExport implements WithMultipleSheets
{
    protected ?string $search;
    protected ?string $dateFrom;
    protected ?string $dateTo;
    protected ?string $statusPembayaran;
    protected ?string $statusTransaksi;

    public function __construct(
        ?string $search           = '',
        ?string $dateFrom         = null,
        ?string $dateTo           = null,
        ?string $statusPembayaran = null,
        ?string $statusTransaksi  = null
    ) {
        $this->search           = $search           ?? '';
        $this->dateFrom         = $dateFrom;
        $this->dateTo           = $dateTo;
        $this->statusPembayaran = $statusPembayaran;
        $this->statusTransaksi  = $statusTransaksi;
    }

    public function sheets(): array
    {
        return [
            new PenjualanRekapSheet(
                $this->search,
                $this->dateFrom,
                $this->dateTo,
                $this->statusPembayaran,
                $this->statusTransaksi
            ),
            new PenjualanDetailSheet(
                $this->search,
                $this->dateFrom,
                $this->dateTo,
                $this->statusPembayaran,
                $this->statusTransaksi
            ),
        ];
    }
}