<?php

namespace App\Exports;

use App\Models\Pembelian;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PembelianExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnWidths,
    WithTitle
{
    protected string $search;
    protected string $filter;

    public function __construct(string $search = '', string $filter = 'semua')
    {
        $this->search = $search;
        $this->filter = $filter;
    }

    public function collection()
    {
        return Pembelian::query()
            ->when($this->search, function ($q) {
                $search = $this->search;
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('keterangan', 'like', "%{$search}%")
                  ->orWhere('nama_pelanggan', 'like', "%{$search}%")
                  ->orWhere('tanggal_pembelian', 'like', "%{$search}%");
            })
            ->when($this->filter !== 'semua', function ($q) {
                $q->where('status', $this->filter);
            })
            ->orderBy('tanggal_pembelian', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Nama Barang',
            'Status',
            'Kondisi',
            'Qty',
            'Harga Satuan (Rp)',
            'Total (Rp)',
            'Keterangan',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        $statusLabel = $row->status === 'buy_back' ? 'Buy Back' : 'Normal';

        $kondisiMap = [
            'baru'  => 'Baru',
            'bekas' => 'Bekas',
            'baik'  => 'Baik',
            'rusak' => 'Rusak',
        ];
        $kondisiLabel = $kondisiMap[$row->kondisi_barang] ?? $row->kondisi_barang;

        return [
            $no,
            \Carbon\Carbon::parse($row->tanggal_pembelian)->format('d M Y'),
            $row->nama_barang,
            $statusLabel,
            $kondisiLabel,
            $row->jumlah,
            $row->harga_satuan,
            $row->attributes['total'] ?? ($row->jumlah * $row->harga_satuan),
            $row->keterangan ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['argb' => 'FFFFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FF1D6FA4'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle("A1:I{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FFD1D5DB'],
                ],
            ],
        ]);

        $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("D2:E{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("F2:F{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("G2:H{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->getStyle("G2:H{$lastRow}")
              ->getNumberFormat()
              ->setFormatCode('#,##0');

        for ($i = 2; $i <= $lastRow; $i++) {
            if ($i % 2 === 0) {
                $sheet->getStyle("A{$i}:I{$i}")->getFill()
                      ->setFillType(Fill::FILL_SOLID)
                      ->getStartColor()->setARGB('FFF8FAFC');
            }
        }

        $sheet->getRowDimension(1)->setRowHeight(22);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 15,
            'C' => 30,
            'D' => 12,
            'E' => 12,
            'F' => 8,
            'G' => 20,
            'H' => 20,
            'I' => 30,
        ];
    }

    public function title(): string
    {
        return 'Data Pembelian';
    }
}