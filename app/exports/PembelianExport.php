<?php
// app/exports/PembelianExport.php

namespace App\Exports;

use App\Models\Pembelian;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Carbon\Carbon;

class PembelianExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    private int $rowNumber = 0;

    // BUG 1 FIX: tambah $dateFrom dan $dateTo
    public function __construct(
        protected string $search   = '',
        protected string $filter   = '',
        protected string $dateFrom = '',
        protected string $dateTo   = '',
    ) {
        // BUG 3 FIX: reset di constructor
        $this->rowNumber = 0;
    }

    public function collection()
    {
        return Pembelian::query()
            // BUG 2 FIX: wrap orWhere dalam sub-closure agar tidak bocor
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('nama_barang',      'like', "%{$this->search}%")
                        ->orWhere('no_invoice',     'like', "%{$this->search}%")
                        ->orWhere('keterangan',     'like', "%{$this->search}%")
                        ->orWhere('nama_pelanggan', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filter && $this->filter !== 'semua', function ($q) {
                $q->where('status', $this->filter);
            })
            // BUG 2 FIX: tambah filter tanggal
            ->when($this->dateFrom, fn($q) =>
                $q->whereDate('tanggal_pembelian', '>=', $this->dateFrom)
            )
            ->when($this->dateTo, fn($q) =>
                $q->whereDate('tanggal_pembelian', '<=', $this->dateTo)
            )
            ->orderBy('tanggal_pembelian', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Pembelian',
            'No. Invoice / Faktur',
            'Nama Barang',
            'Jumlah',
            'Harga Satuan (Rp)',
            'Total (Rp)',
            'Kondisi',
            'Status',
            'Keterangan',
            'Bukti Pembayaran',
            'File Invoice / Faktur Supplier',
        ];
    }

    public function map($pembelian): array
    {
        $this->rowNumber++;

        $buktiUrl = $pembelian->bukti_transaksi
            ? url('storage/' . $pembelian->bukti_transaksi)
            : '';

        $invoiceUrl = $pembelian->file_invoice
            ? url('storage/' . $pembelian->file_invoice)
            : '';

        return [
            $this->rowNumber,
            Carbon::parse($pembelian->tanggal_pembelian)->format('d/m/Y'),
            $pembelian->no_invoice ?? '-',
            $pembelian->nama_barang,
            $pembelian->jumlah,
            $pembelian->harga_satuan,
            $pembelian->attributes['total'] ?? ($pembelian->jumlah * $pembelian->harga_satuan),
            ucfirst($pembelian->kondisi_barang ?? '-'),
            $pembelian->status === 'buy_back' ? 'Buy Back' : 'Normal',
            $pembelian->keterangan ?? '-',
            $buktiUrl   ?: 'Tidak ada',
            $invoiceUrl ?: 'Tidak ada',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $this->rowNumber + 1;

        // Header
        $sheet->getStyle('A1:L1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1E40AF'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
        ]);

        $sheet->freezePane('A2');
        $sheet->getRowDimension(1)->setRowHeight(28);

        // Zebra stripe baris data
        for ($row = 2; $row <= $lastRow; $row++) {
            $bgColor = ($row % 2 === 0) ? 'FFF1F5F9' : 'FFFFFFFF';
            $sheet->getStyle("A{$row}:L{$row}")->applyFromArray([
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => $bgColor],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
        }

        // Border seluruh tabel
        if ($lastRow >= 2) {
            $sheet->getStyle("A1:L{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['argb' => 'FFD1D5DB'],
                    ],
                ],
            ]);
        }

        // Alignment angka (kolom E-G: Jumlah, Harga, Total)
        $sheet->getStyle("E2:G{$lastRow}")
              ->getAlignment()
              ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Alignment No
        $sheet->getStyle("A2:A{$lastRow}")
              ->getAlignment()
              ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Format Rupiah Harga Satuan & Total
        $sheet->getStyle("F2:G{$lastRow}")
              ->getNumberFormat()
              ->setFormatCode('#,##0');

        // Lebar kolom URL
        $sheet->getColumnDimension('K')->setWidth(45);
        $sheet->getColumnDimension('L')->setWidth(45);

        // Hyperlink kolom K (Bukti) & L (Invoice)
        for ($row = 2; $row <= $lastRow; $row++) {
            $cellBukti = $sheet->getCell("K{$row}")->getValue();
            if ($cellBukti && $cellBukti !== 'Tidak ada') {
                $sheet->getCell("K{$row}")->getHyperlink()->setUrl($cellBukti);
                $sheet->getStyle("K{$row}")->applyFromArray([
                    'font' => [
                        'color'     => ['argb' => 'FF2563EB'],
                        'underline' => Font::UNDERLINE_SINGLE,
                    ],
                ]);
            }

            $cellInvoice = $sheet->getCell("L{$row}")->getValue();
            if ($cellInvoice && $cellInvoice !== 'Tidak ada') {
                $sheet->getCell("L{$row}")->getHyperlink()->setUrl($cellInvoice);
                $sheet->getStyle("L{$row}")->applyFromArray([
                    'font' => [
                        'color'     => ['argb' => 'FF2563EB'],
                        'underline' => Font::UNDERLINE_SINGLE,
                    ],
                ]);
            }
        }

        return [];
    }

    public function title(): string
    {
        return 'Data Pembelian';
    }
}