<?php

namespace App\Exports;

use App\Models\Penjualan;
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

class PenjualanExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnWidths,
    WithTitle
{
    protected string $search;
    protected array $filters;

    public function __construct(string $search = '', array $filters = [])
    {
        $this->search  = $search;
        $this->filters = $filters;
    }

    public function collection()
    {
        return Penjualan::with('details')
            ->when($this->search, function ($q) {
                $search = $this->search;
                $q->where('nama_pelanggan',     'like', "%{$search}%")
                  ->orWhere('nomor_telepon',    'like', "%{$search}%")
                  ->orWhere('alamat_pelanggan', 'like', "%{$search}%")
                  ->orWhere('jenis_pembayaran', 'like', "%{$search}%")
                  ->orWhere('keterangan',       'like', "%{$search}%")
                  ->orWhere('tanggal_penjualan','like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Nama Barang',
            'Qty',
            'Alamat Pelanggan',
            'Jenis Pembayaran',
            'Harga (Rp)',
            'Total (Rp)',
            'Keterangan',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            \Carbon\Carbon::parse($row->tanggal_penjualan)->format('d M Y'),
            $row->nama_barang,
            $row->qty,
            $row->alamat_pelanggan,
            ucfirst($row->jenis_pembayaran),
            $row->harga,
            $row->total,
            $row->keterangan ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        // Style header row
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

        // Border seluruh data
        $sheet->getStyle("A1:I{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FFD1D5DB'],
                ],
            ],
        ]);

        // Alignment kolom angka (No, Qty, Harga, Total)
        $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("D2:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("G2:H{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        // Format currency kolom G dan H
        $sheet->getStyle("G2:H{$lastRow}")
              ->getNumberFormat()
              ->setFormatCode('#,##0');

        // Row zebra striping
        for ($i = 2; $i <= $lastRow; $i++) {
            if ($i % 2 === 0) {
                $sheet->getStyle("A{$i}:I{$i}")->getFill()
                      ->setFillType(Fill::FILL_SOLID)
                      ->getStartColor()->setARGB('FFF8FAFC');
            }
        }

        // Row height header
        $sheet->getRowDimension(1)->setRowHeight(22);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 15,
            'C' => 30,
            'D' => 8,
            'E' => 30,
            'F' => 18,
            'G' => 18,
            'H' => 18,
            'I' => 30,
        ];
    }

    public function title(): string
    {
        return 'Data Penjualan';
    }
}