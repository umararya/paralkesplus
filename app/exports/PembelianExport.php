<?php
// app/exports/PembelianExport.php

namespace App\Exports;

use App\Models\Pembelian;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PembelianExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    WithColumnFormatting,
    ShouldAutoSize
{
    protected int $no = 0; // FIX: instance variable, bukan static

    public function __construct(
        protected string $search        = '',
        protected string $filter        = '',
        protected string $dateFrom      = '',
        protected string $dateTo        = '',
        protected string $kondisiBarang = '',
    ) {}

    public function collection()
    {
        return Pembelian::query()
            ->when($this->search, function ($q) {
                // FIX: orWhere digroup agar tidak bypass filter lain
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
            ->when($this->dateFrom, fn($q) =>
                $q->whereDate('tanggal_pembelian', '>=', $this->dateFrom)
            )
            ->when($this->dateTo, fn($q) =>
                $q->whereDate('tanggal_pembelian', '<=', $this->dateTo)
            )
            ->when($this->kondisiBarang && $this->kondisiBarang !== 'semua', function ($q) {
                $q->where('kondisi_barang', $this->kondisiBarang);
            })
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
            'Status',
            'Kondisi Barang',
            'Qty',
            'Harga Satuan (Rp)',
            'Total (Rp)',
            'Keterangan',
            'Bukti Pembayaran',
            'File Invoice / Faktur Supplier',
        ];
    }

    public function map($row): array
    {
        $this->no++;

        $statusLabel = match($row->status) {
            'buy_back' => 'Buy Back',
            'normal'   => 'Normal',
            default    => ucfirst($row->status ?? '-'),
        };

        $kondisiLabel = match($row->kondisi_barang) {
            'baru'  => 'Baru',
            'bekas' => 'Bekas',
            'baik'  => 'Baik',
            'rusak' => 'Rusak',
            default => ucfirst($row->kondisi_barang ?? '-'),
        };

        $buktiUrl   = $row->bukti_transaksi ? url('storage/' . $row->bukti_transaksi) : '';
        $invoiceUrl = $row->file_invoice    ? url('storage/' . $row->file_invoice)    : '';

        return [
            $this->no,
            Carbon::parse($row->tanggal_pembelian)->format('d/m/Y'),
            $row->no_invoice ?? '-',
            $row->nama_barang,
            $statusLabel,
            $kondisiLabel,
            (int) ($row->jumlah      ?? 0),
            (int) ($row->harga_satuan ?? 0),
            (int) ($row->attributes['total'] ?? ($row->jumlah * $row->harga_satuan)),
            $row->keterangan ?? '-',
            $buktiUrl   ?: 'Tidak ada',
            $invoiceUrl ?: 'Tidak ada',
        ];
    }

    public function columnFormats(): array
    {
        $currency = '"Rp "#,##0';
        return [
            'H' => $currency,
            'I' => $currency,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastDataRow = $this->no + 1; // baris terakhir data (header = row 1)

        // ── Header row ──
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
        $sheet->getRowDimension(1)->setRowHeight(30);

        // ── Zebra stripe baris data ──
        for ($i = 2; $i <= $lastDataRow; $i++) {
            if ($i % 2 === 0) {
                $sheet->getStyle("A{$i}:L{$i}")->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFF1F5F9'],
                    ],
                ]);
            }
            $sheet->getStyle("A{$i}:L{$i}")->getAlignment()
                  ->setVertical(Alignment::VERTICAL_CENTER);
        }

        // ── Border seluruh tabel ──
        if ($lastDataRow >= 2) {
            $sheet->getStyle("A1:L{$lastDataRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['argb' => 'FFD1D5DB'],
                    ],
                ],
            ]);
        }

        // ── Alignment No (kolom A) ──
        $sheet->getStyle("A2:A{$lastDataRow}")
              ->getAlignment()
              ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ── Alignment angka (kolom G-I: Qty, Harga, Total) ──
        $sheet->getStyle("G2:I{$lastDataRow}")
              ->getAlignment()
              ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // ── Warna status (kolom E) ──
        for ($i = 2; $i <= $lastDataRow; $i++) {
            $val = $sheet->getCell("E{$i}")->getValue();
            if ($val === 'Buy Back') {
                $sheet->getStyle("E{$i}")->applyFromArray([
                    'font' => ['color' => ['argb' => 'FFD97706'], 'bold' => true],
                ]);
            } elseif ($val === 'Normal') {
                $sheet->getStyle("E{$i}")->applyFromArray([
                    'font' => ['color' => ['argb' => 'FF1D4ED8'], 'bold' => true],
                ]);
            }
        }

        // ── Warna kondisi (kolom F) ──
        for ($i = 2; $i <= $lastDataRow; $i++) {
            $val = $sheet->getCell("F{$i}")->getValue();
            if ($val === 'Baru') {
                $sheet->getStyle("F{$i}")->applyFromArray([
                    'font' => ['color' => ['argb' => 'FF1D6FA4'], 'bold' => true],
                ]);
            } elseif ($val === 'Bekas') {
                $sheet->getStyle("F{$i}")->applyFromArray([
                    'font' => ['color' => ['argb' => 'FF7C3AED'], 'bold' => true],
                ]);
            } elseif ($val === 'Baik') {
                $sheet->getStyle("F{$i}")->applyFromArray([
                    'font' => ['color' => ['argb' => 'FF16A34A'], 'bold' => true],
                ]);
            } elseif ($val === 'Rusak') {
                $sheet->getStyle("F{$i}")->applyFromArray([
                    'font' => ['color' => ['argb' => 'FFDC2626'], 'bold' => true],
                ]);
            }
        }

        // ── Summary row (Total Qty & Total Nilai) ──
        if ($lastDataRow >= 2) {
            $summaryRow = $lastDataRow + 2;

            $sheet->setCellValue("F{$summaryRow}", 'TOTAL:');
            $sheet->setCellValue("G{$summaryRow}", "=SUM(G2:G{$lastDataRow})");
            $sheet->setCellValue("I{$summaryRow}", "=SUM(I2:I{$lastDataRow})");

            $sheet->getStyle("F{$summaryRow}:I{$summaryRow}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['argb' => 'FF1E40AF']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFDBEAFE'],
                ],
            ]);

            // Format currency pada summary
            $sheet->getStyle("I{$summaryRow}")
                  ->getNumberFormat()
                  ->setFormatCode('"Rp "#,##0');
        }

        // ── Lebar kolom URL ──
        $sheet->getColumnDimension('K')->setWidth(45);
        $sheet->getColumnDimension('L')->setWidth(45);

        // ── Hyperlink kolom K (Bukti) & L (Invoice) ──
        for ($i = 2; $i <= $lastDataRow; $i++) {
            $cellBukti = $sheet->getCell("K{$i}")->getValue();
            if ($cellBukti && $cellBukti !== 'Tidak ada') {
                $sheet->getCell("K{$i}")->getHyperlink()->setUrl($cellBukti);
                $sheet->getStyle("K{$i}")->applyFromArray([
                    'font' => [
                        'color'     => ['argb' => 'FF2563EB'],
                        'underline' => Font::UNDERLINE_SINGLE,
                    ],
                ]);
            }

            $cellInvoice = $sheet->getCell("L{$i}")->getValue();
            if ($cellInvoice && $cellInvoice !== 'Tidak ada') {
                $sheet->getCell("L{$i}")->getHyperlink()->setUrl($cellInvoice);
                $sheet->getStyle("L{$i}")->applyFromArray([
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