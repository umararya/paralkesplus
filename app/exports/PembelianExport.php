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
                $q->where('nama_barang',     'like', "%{$s}%")
                  ->orWhere('nama_pelanggan', 'like', "%{$s}%")
                  ->orWhere('keterangan',     'like', "%{$s}%");
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
            '#',
            'Tanggal',
            'Nama Barang',
            'Status',
            'Kondisi',
            'Qty',
            'Harga Satuan (Rp)',
            'Total (Rp)',
            'Nama Pelanggan (Buy Back)',
            'Keterangan',
            'Bukti Transaksi (URL)',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        // Harga satuan — coba field harga, harga_satuan, fallback 0
        $hargaSatuan = $row->harga ?? $row->harga_satuan ?? 0;

        $bukti = $row->bukti_transaksi
            ? asset('storage/' . $row->bukti_transaksi)
            : '-';

        return [
            $no,
            Carbon::parse($row->tanggal_pembelian)->format('d/m/Y'),
            $row->nama_barang,
            $row->status === 'buy_back' ? 'Buy Back' : 'Normal',
            ucfirst($row->kondisi_barang ?? '-'),
            $row->jumlah,
            $hargaSatuan,
            $row->total ?? 0,
            $row->nama_pelanggan ?? '-',
            $row->keterangan     ?? '-',
            $bukti,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        $headerStyle = [
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
                'wrapText'   => true,
            ],
        ];

        // Styling kolom URL bukti transaksi (kolom K = 11)
        $sheet->getStyle('K2:K' . $lastRow)->applyFromArray([
            'font' => ['color' => ['argb' => 'FF1D6FA4'], 'underline' => true],
        ]);

        // Warna zebra untuk baris data
        for ($i = 2; $i <= $lastRow; $i++) {
            if ($i % 2 === 0) {
                $sheet->getStyle('A' . $i . ':K' . $i)->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFF0F7FF'],
                    ],
                ]);
            }
        }

        // Set row height header
        $sheet->getRowDimension(1)->setRowHeight(28);

        return [1 => $headerStyle];
    }

    public function title(): string
    {
        return 'Data Pembelian';
    }
}