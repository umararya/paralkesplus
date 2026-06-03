<?php
// app/exports/PenjualanDetailSheet.php

namespace App\Exports;

use App\Models\DetailPenjualan;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PenjualanDetailSheet implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    WithColumnFormatting,
    ShouldAutoSize
{
    protected ?string $search;
    protected ?string $dateFrom;
    protected ?string $dateTo;
    protected ?string $statusPembayaran;
    protected ?string $statusTransaksi;

    const LAST_COL = 'K';

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

    public function collection()
    {
        return DetailPenjualan::with(['penjualan.user'])
            ->whereHas('penjualan', function ($q) {
                $q->when($this->search, function ($q2) {
                    $s = $this->search;
                    $q2->where('nama_pelanggan',    'like', "%{$s}%")
                       ->orWhere('nomor_telepon',   'like', "%{$s}%")
                       ->orWhere('alamat_pelanggan','like', "%{$s}%");
                })
                ->when($this->dateFrom, fn($q2) => $q2->whereDate('tanggal_penjualan', '>=', $this->dateFrom))
                ->when($this->dateTo,   fn($q2) => $q2->whereDate('tanggal_penjualan', '<=', $this->dateTo))
                ->when($this->statusPembayaran && $this->statusPembayaran !== 'semua',
                    fn($q2) => $q2->where('status_pembayaran', $this->statusPembayaran))
                ->when($this->statusTransaksi && $this->statusTransaksi !== 'semua',
                    fn($q2) => $q2->where('status_transaksi', $this->statusTransaksi));
            })
            ->orderBy('penjualan_id', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            '#',
            'No. Transaksi',
            'Tanggal',
            'Nama Pelanggan',
            'Nama Barang',
            'Kondisi',
            'Qty',
            'Satuan',
            'Harga Satuan (Rp)',
            'Diskon Item (%)',
            'Subtotal (Rp)',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        $kondisi = match($row->kondisi) {
            'baru'  => 'Baru',
            'bekas' => 'Bekas',
            default => ucfirst($row->kondisi ?? '-'),
        };

        return [
            $no,
            $row->penjualan_id,
            $row->penjualan
                ? Carbon::parse($row->penjualan->tanggal_penjualan)->format('d/m/Y')
                : '-',
            $row->penjualan->nama_pelanggan ?? '-',
            $row->nama_barang               ?? '-',
            $kondisi,
            (int) ($row->qty          ?? 0),
            $row->satuan              ?? 'unit',
            (int) ($row->harga_satuan ?? 0),
            (int) ($row->diskon       ?? 0),
            (int) ($row->subtotal     ?? 0),
        ];
    }

    public function columnFormats(): array
    {
        $currency = '"Rp "#,##0';
        return [
            'I' => $currency,
            'K' => $currency,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = self::LAST_COL;

        // ── Header ──
        $headerStyle = [
            'font' => [
                'bold'  => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF2E9E6B'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
        ];

        // ── Zebra stripe ──
        for ($i = 2; $i <= $lastRow; $i++) {
            if ($i % 2 === 0) {
                $sheet->getStyle('A' . $i . ':' . $lastCol . $i)->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFE6FBF3'],
                    ],
                ]);
            }
        }

        // ── Warna kondisi (kolom F) ──
        for ($i = 2; $i <= $lastRow; $i++) {
            $val = $sheet->getCell('F' . $i)->getValue();
            if ($val === 'Baru') {
                $sheet->getStyle('F' . $i)->applyFromArray([
                    'font' => ['color' => ['argb' => 'FF16A34A'], 'bold' => true],
                ]);
            } elseif ($val === 'Bekas') {
                $sheet->getStyle('F' . $i)->applyFromArray([
                    'font' => ['color' => ['argb' => 'FFD97706'], 'bold' => true],
                ]);
            }
        }

        // ── Summary total subtotal di bawah ──
        if ($lastRow >= 2) {
            $summaryRow = $lastRow + 2;

            $sheet->setCellValue('J' . $summaryRow, 'TOTAL SUBTOTAL:');
            $sheet->setCellValue('K' . $summaryRow, '=SUM(K2:K' . $lastRow . ')');

            $sheet->getStyle('J' . $summaryRow . ':K' . $summaryRow)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['argb' => 'FF2E9E6B']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFD1FAE5'],
                ],
            ]);
        }

        // ── Freeze header ──
        $sheet->freezePane('A2');
        $sheet->getRowDimension(1)->setRowHeight(30);

        return [1 => $headerStyle];
    }

    public function title(): string
    {
        return 'Detail Barang';
    }
}