<?php
// app/Exports/PenjualanExport.php

namespace App\Exports;

use App\Models\Penjualan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Carbon\Carbon;

class PenjualanExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    protected string $search;

    // ← FIX: nullable string, default ''
    public function __construct(?string $search = '')
    {
        $this->search = $search ?? '';
    }

    public function collection()
    {
        return Penjualan::when($this->search, function ($q) {
                $s = $this->search;
                $q->where('nama_barang',      'like', "%{$s}%")
                  ->orWhere('alamat_pelanggan','like', "%{$s}%")
                  ->orWhere('jenis_pembayaran','like', "%{$s}%")
                  ->orWhere('keterangan',      'like', "%{$s}%");
            })
            ->orderBy('tanggal_penjualan', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            '#',
            'Tanggal',
            'Nama Barang',
            'Qty',
            'Alamat Pelanggan',
            'Jenis Pembayaran',
            'Harga Satuan',
            'Total',
            'Keterangan',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            Carbon::parse($row->tanggal_penjualan)->format('d/m/Y'),
            $row->nama_barang,
            $row->qty,
            $row->alamat_pelanggan,
            ucfirst($row->jenis_pembayaran),
            $row->harga  ?? 0,
            $row->total  ?? 0,
            $row->keterangan ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1D6FA4'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Data Penjualan';
    }
}