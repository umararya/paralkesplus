<?php
// app/Exports/PembelianExport.php

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
use Carbon\Carbon;

class PembelianExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    protected string $search;
    protected string $filter;

    public function __construct(?string $search = '', ?string $filter = '')
    {
        $this->search = $search ?? '';
        $this->filter = $filter ?? '';
    }

    public function collection()
    {
        return Pembelian::when($this->search, function ($q) {
                $s = $this->search;
                $q->where('nama_barang',  'like', "%{$s}%")
                  ->orWhere('no_invoice', 'like', "%{$s}%")
                  ->orWhere('keterangan', 'like', "%{$s}%");
            })
            ->when($this->filter && $this->filter !== 'semua', function ($q) {
                $q->where('status', $this->filter);
            })
            ->orderBy('tanggal_pembelian', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Pembelian',
            'No. Invoice / Faktur',      // kolom 3
            'Nama Barang',
            'Jumlah',
            'Harga Satuan (Rp)',
            'Total (Rp)',
            'Kondisi',
            'Status',
            'Keterangan',
            'Bukti Pembayaran',          // kolom 11
            'File Invoice / Faktur Supplier', // ← REVISI: kolom baru (12)
        ];
    }

    /**
     * Nomor urut baris
     */
    private int $rowNumber = 0;

    public function map($pembelian): array
    {
        $this->rowNumber++;

        // ── Bukti Pembayaran ──
        $buktiUrl = '';
        if ($pembelian->bukti_transaksi) {
            $buktiUrl = url('storage/' . $pembelian->bukti_transaksi);
        }

        // ── File Invoice / Faktur Supplier ── (REVISI: tambah)
        $invoiceUrl = '';
        if ($pembelian->file_invoice) {
            $invoiceUrl = url('storage/' . $pembelian->file_invoice);
        }

        return [
            $this->rowNumber,
            Carbon::parse($pembelian->tanggal_pembelian)->format('d/m/Y'),
            $pembelian->no_invoice ?? '-',
            $pembelian->nama_barang,
            $pembelian->jumlah,
            $pembelian->harga_satuan,
            $pembelian->total ?? ($pembelian->jumlah * $pembelian->harga_satuan),
            ucfirst($pembelian->kondisi_barang ?? '-'),
            $pembelian->status === 'buy_back' ? 'Buy Back' : 'Normal',
            $pembelian->keterangan ?? '-',
            $buktiUrl ?: 'Tidak ada',           // kolom 11
            $invoiceUrl ?: 'Tidak ada',          // kolom 12 ← REVISI
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Hitung baris terakhir data
        $lastRow = $this->rowNumber + 1; // +1 karena heading di baris 1

        // ── Header row styling ──
        $sheet->getStyle('A1:L1')->applyFromArray([  // ← REVISI: A1:L1 (was K1)
            'font' => [
                'bold'  => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1E40AF'],  // biru gelap
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
        ]);

        // ── Freeze pane header ──
        $sheet->freezePane('A2');

        // ── Tinggi header ──
        $sheet->getRowDimension(1)->setRowHeight(28);

        // ── Styling baris data (zebra stripe) ──
        for ($row = 2; $row <= $lastRow; $row++) {
            $bgColor = ($row % 2 === 0) ? 'FFF1F5F9' : 'FFFFFFFF';
            $sheet->getStyle("A{$row}:L{$row}")->applyFromArray([  // ← REVISI: L
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => $bgColor],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);
        }

        // ── Border seluruh tabel ──
        if ($lastRow >= 2) {
            $sheet->getStyle("A1:L{$lastRow}")->applyFromArray([  // ← REVISI: L
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color'       => ['argb' => 'FFD1D5DB'],
                    ],
                ],
            ]);
        }

        // ── Alignment kolom angka ──
        $sheet->getStyle("E2:G{$lastRow}")->getAlignment()
              ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // ── Alignment kolom No ──
        $sheet->getStyle("A2:A{$lastRow}")->getAlignment()
              ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ── Format Rupiah untuk kolom Harga Satuan & Total ──
        $sheet->getStyle("F2:G{$lastRow}")
              ->getNumberFormat()
              ->setFormatCode('#,##0');

        // ── Lebar kolom URL agar tidak terlalu lebar ──
        $sheet->getColumnDimension('K')->setWidth(45); // Bukti Pembayaran
        $sheet->getColumnDimension('L')->setWidth(45); // File Invoice ← REVISI

        // ── Baris data: hyperlink untuk kolom Bukti & Invoice ──
        for ($row = 2; $row <= $lastRow; $row++) {
            // Kolom K: Bukti Pembayaran
            $cellBukti = $sheet->getCell("K{$row}")->getValue();
            if ($cellBukti && $cellBukti !== 'Tidak ada') {
                $sheet->getCell("K{$row}")->getHyperlink()->setUrl($cellBukti);
                $sheet->getStyle("K{$row}")->applyFromArray([
                    'font' => [
                        'color'     => ['argb' => 'FF2563EB'],
                        'underline' => \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE,
                    ],
                ]);
            }

            // Kolom L: File Invoice ← REVISI
            $cellInvoice = $sheet->getCell("L{$row}")->getValue();
            if ($cellInvoice && $cellInvoice !== 'Tidak ada') {
                $sheet->getCell("L{$row}")->getHyperlink()->setUrl($cellInvoice);
                $sheet->getStyle("L{$row}")->applyFromArray([
                    'font' => [
                        'color'     => ['argb' => 'FF2563EB'],
                        'underline' => \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE,
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