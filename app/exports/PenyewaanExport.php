<?php

namespace App\Exports;

use App\Models\Penyewaan;
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

class PenyewaanExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithColumnWidths,
    WithTitle
{
    protected string $search;

    public function __construct(string $search = '')
    {
        $this->search = $search;
    }

    public function collection()
    {
        return Penyewaan::with('details')
            ->when($this->search, function ($q) {
                $search = $this->search;
                $q->where('nama_penyewa',       'like', "%{$search}%")
                  ->orWhere('nomor_telepon',     'like', "%{$search}%")
                  ->orWhere('produk_alkes',      'like', "%{$search}%")
                  ->orWhere('status',            'like', "%{$search}%")
                  ->orWhere('pengiriman',        'like', "%{$search}%")
                  ->orWhere('alamat_penyewa',    'like', "%{$search}%")
                  ->orWhere('metode_pembayaran', 'like', "%{$search}%")
                  ->orWhere('keterangan',        'like', "%{$search}%")
                  ->orWhere('tgl_mulai',         'like', "%{$search}%")
                  ->orWhere('tgl_selesai',       'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Penyewa',
            'No. HP',
            'Nama Alat',
            'Tgl Mulai',
            'Tgl Selesai',
            'Status',
            'Metode Pembayaran',
            'Total (Rp)',
            'Keterangan',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        $namaAlat = $row->details->count()
            ? $row->details->pluck('nama_alat')->implode(', ')
            : ($row->produk_alkes ?? '-');

        $statusMap = [
            'berjalan'          => 'Berjalan',
            'segera_konfirmasi' => 'Segera Konfirmasi',
            'selesai'           => 'Selesai',
            'dibatalkan'        => 'Dibatalkan',
        ];
        $statusLabel = $statusMap[$row->status] ?? $row->status;

        return [
            $no,
            $row->nama_penyewa,
            $row->nomor_telepon,
            $namaAlat,
            $row->tgl_mulai ? \Carbon\Carbon::parse($row->tgl_mulai)->format('d M Y') : '-',
            $row->tgl_selesai ? \Carbon\Carbon::parse($row->tgl_selesai)->format('d M Y') : '-',
            $statusLabel,
            ucfirst($row->metode_pembayaran),
            $row->total_tagihan ?? $row->total_harga_sewa,
            $row->keterangan ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        $sheet->getStyle('A1:J1')->applyFromArray([
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

        $sheet->getStyle("A1:J{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['argb' => 'FFD1D5DB'],
                ],
            ],
        ]);

        $sheet->getStyle("A2:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("E2:F{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("G2:H{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("I2:I{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->getStyle("I2:I{$lastRow}")
              ->getNumberFormat()
              ->setFormatCode('#,##0');

        for ($i = 2; $i <= $lastRow; $i++) {
            if ($i % 2 === 0) {
                $sheet->getStyle("A{$i}:J{$i}")->getFill()
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
            'B' => 25,
            'C' => 15,
            'D' => 35,
            'E' => 15,
            'F' => 15,
            'G' => 20,
            'H' => 18,
            'I' => 18,
            'J' => 30,
        ];
    }

    public function title(): string
    {
        return 'Data Penyewaan';
    }
}