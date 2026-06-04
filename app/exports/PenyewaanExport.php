<?php
// app/exports/PenyewaanExport.php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PenyewaanExport implements WithMultipleSheets
{
    protected ?string $search;
    protected ?string $dateFrom;
    protected ?string $dateTo;
    protected ?string $status;

    public function __construct(
        ?string $search   = '',
        ?string $dateFrom = null,
        ?string $dateTo   = null,
        ?string $status   = 'semua'
    ) {
        $this->search   = $search   ?? '';
        $this->dateFrom = $dateFrom;
        $this->dateTo   = $dateTo;
        $this->status   = $status   ?? 'semua';
    }

    public function sheets(): array
    {
        return [
            new PenyewaanRekapSheet(
                $this->search,
                $this->dateFrom,
                $this->dateTo,
                $this->status
            ),
            new PenyewaanDetailSheet(
                $this->search,
                $this->dateFrom,
                $this->dateTo,
                $this->status
            ),
        ];
    }
}