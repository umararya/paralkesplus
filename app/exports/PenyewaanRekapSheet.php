<?php
// app/exports/PenyewaanRekapSheet.php

namespace App\Exports;

use App\Models\Penyewaan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

class PenyewaanRekapSheet implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize,
    WithEvents
{
    protected ?string $search;
    protected ?string $dateFrom;
    protected ?string $dateTo;
    protected ?string $status;
    protected int $rowCount = 0;

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

    public function collection()
    {
        $query = Penyewaan::with('details')
            ->when($this->search, function ($q) {
                $s = $this->search;
                $q->where(function ($q2) use ($s) {
                    $q2->where('nama_penyewa',       'like', "%{$s}%")
                       ->orWhere('nomor_telepon',     'like', "%{$s}%")
                       ->orWhere('produk_alkes',      'like', "%{$s}%")
                       ->orWhere('status',            'like', "%{$s}%")
                       ->orWhere('alamat_penyewa',    'like', "%{$s}%")
                       ->orWhere('metode_pembayaran', 'like', "%{$s}%")
                       ->orWhere('keterangan',        'like', "%{$s}%");
                });
            })
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->when($this->status && $this->status !== 'semua', fn($q) => $q->where('status', $this->status))
            ->orderBy('created_at', 'desc')
            ->get();

        $this->rowCount = $query->count();

        return $query;
    }

    public function headings(): array
    {
        return [
            '#',
            'Tgl Input',
            'Nama Penyewa',
            'No. Telepon',
            'No. KTP',
            'Tempat, Tgl Lahir',
            'Produk (Gabungan)',
            'Total Qty',
            'Durasi (Hari)',
            'Tgl Mulai',
            'Tgl Selesai',
            'Pengiriman',
            'Biaya Ongkir (Rp)',
            'Diskon (Rp)',
            'Total Sewa (Rp)',
            'Total Tagihan (Rp)',
            'Metode Pembayaran',
            'Alamat Penyewa',
            'Status',
            'Keterangan',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        // Gabung nama alat + qty: "Tabung Oksigen Besar ×2, Tabung Oksigen Kecil ×3"
        if ($row->details->isNotEmpty()) {
            $produkGabung = $row->details
                ->map(fn($d) => $d->nama_alat . ' ×' . $d->qty)
                ->implode(', ');
            $totalQty = $row->details->sum('qty');
        } else {
            $produkGabung = $row->produk_alkes ?? '-';
            $totalQty     = '-';
        }

        return [
            $no,
            $row->created_at ? Carbon::parse($row->created_at)->format('d/m/Y') : '-',
            $row->nama_penyewa,
            $row->nomor_telepon,
            $row->nomor_ktp            ?? '-',
            $row->tempat_tanggal_lahir ?? '-',
            $produkGabung,
            $totalQty,
            $row->durasi_hari      ?? 0,
            $row->tgl_mulai   ? Carbon::parse($row->tgl_mulai)->format('d/m/Y')   : '-',
            $row->tgl_selesai ? Carbon::parse($row->tgl_selesai)->format('d/m/Y') : '-',
            $row->pengiriman_label ?? $row->pengiriman,
            $row->biaya_ongkir     ?? 0,
            $row->diskon_global    ?? 0,
            $row->total_harga_sewa ?? 0,
            $row->total_tagihan    ?? 0,
            ucfirst($row->metode_pembayaran ?? '-'),
            $row->alamat_penyewa,
            ucfirst(str_replace('_', ' ', $row->status ?? '')),
            $row->keterangan ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        // Zebra stripe baris data (mulai row 5 karena ada 3 baris info di atas + 1 header)
        for ($i = 5; $i <= $lastRow; $i++) {
            if ($i % 2 === 0) {
                $sheet->getStyle('A' . $i . ':T' . $i)->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFF0F7FF'],
                    ],
                ]);
            }
        }

        $sheet->getRowDimension(4)->setRowHeight(26);

        return [
            // Header kolom (row 4)
            4 => [
                'font' => [
                    'bold'  => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size'  => 10,
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
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['argb' => 'FF0E4F7A'],
                    ],
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet    = $event->sheet->getDelegate();
                $lastCol  = 'T';
                $lastRow  = $sheet->getHighestRow();
                $genTime  = Carbon::now()->format('d/m/Y H:i');

                // ── Sisipkan 3 baris info di atas (geser data ke bawah) ──
                $sheet->insertNewRowBefore(1, 3);

                // Row 1: Judul
                $sheet->mergeCells('A1:T1');
                $sheet->setCellValue('A1', 'LAPORAN PENYEWAAN — PARALKES+');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold'  => true,
                        'size'  => 14,
                        'color' => ['argb' => 'FF1D6FA4'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(32);

                // Row 2: Periode & info
                $periode = '';
                if ($this->dateFrom && $this->dateTo) {
                    $periode = Carbon::parse($this->dateFrom)->format('d M Y')
                             . ' — '
                             . Carbon::parse($this->dateTo)->format('d M Y');
                } elseif ($this->dateFrom) {
                    $periode = 'Mulai ' . Carbon::parse($this->dateFrom)->format('d M Y');
                } elseif ($this->dateTo) {
                    $periode = 'S/d ' . Carbon::parse($this->dateTo)->format('d M Y');
                } else {
                    $periode = 'Semua Periode';
                }

                $statusLabel = match($this->status) {
                    'berjalan'   => 'Berjalan',
                    'konfirmasi' => 'Perlu Konfirmasi',
                    'selesai'    => 'Selesai',
                    default      => 'Semua Status',
                };

                $sheet->mergeCells('A2:J2');
                $sheet->setCellValue('A2', 'Periode: ' . $periode . '   |   Status: ' . $statusLabel);
                $sheet->mergeCells('K2:T2');
                $sheet->setCellValue('K2', 'Digenerate: ' . $genTime . '   |   Total: ' . $this->rowCount . ' transaksi');
                $sheet->getStyle('A2:T2')->applyFromArray([
                    'font'      => ['size' => 10, 'color' => ['argb' => 'FF555555']],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    'fill'      => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFF0F7FF'],
                    ],
                ]);
                $sheet->getStyle('K2')->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(20);

                // Row 3: Spacer kosong tipis
                $sheet->getRowDimension(3)->setRowHeight(6);

                // ── Baris TOTAL di akhir ──
                $newLastRow = $sheet->getHighestRow() + 2;
                $sheet->mergeCells('A' . $newLastRow . ':L' . $newLastRow);
                $sheet->setCellValue('A' . $newLastRow, 'TOTAL: ' . $this->rowCount . ' transaksi');

                // Sum kolom Ongkir (M), Diskon (N), Total Sewa (O), Total Tagihan (P)
                $dataStart = 5; // row data mulai setelah 3 baris info + 1 header
                $dataEnd   = $newLastRow - 2;
                $sheet->setCellValue('M' . $newLastRow, '=SUM(M' . $dataStart . ':M' . $dataEnd . ')');
                $sheet->setCellValue('N' . $newLastRow, '=SUM(N' . $dataStart . ':N' . $dataEnd . ')');
                $sheet->setCellValue('O' . $newLastRow, '=SUM(O' . $dataStart . ':O' . $dataEnd . ')');
                $sheet->setCellValue('P' . $newLastRow, '=SUM(P' . $dataStart . ':P' . $dataEnd . ')');

                $sheet->getStyle('A' . $newLastRow . ':T' . $newLastRow)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF1D6FA4'],
                    ],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension($newLastRow)->setRowHeight(24);

                // Format angka Rupiah kolom M, N, O, P
                $rupiahFormat = '#,##0';
                foreach (['M', 'N', 'O', 'P'] as $col) {
                    $sheet->getStyle($col . $dataStart . ':' . $col . $newLastRow)
                          ->getNumberFormat()
                          ->setFormatCode($rupiahFormat);
                }

                // Freeze pane di bawah header
                $sheet->freezePane('A5');
            },
        ];
    }

    public function title(): string
    {
        return '📋 Rekap Penyewaan';
    }
}