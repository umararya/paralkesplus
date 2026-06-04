<?php
// app/exports/PenyewaanDetailSheet.php

namespace App\Exports;

use App\Models\Penyewaan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
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

class PenyewaanDetailSheet implements
    FromCollection,
    WithHeadings,
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
        $penyewaans = Penyewaan::with('details')
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

        // Flatten: 1 baris per detail produk
        $rows = collect();
        $no   = 0;

        foreach ($penyewaans as $sewa) {
            $no++;
            $tglInput   = $sewa->created_at ? Carbon::parse($sewa->created_at)->format('d/m/Y') : '-';
            $tglMulai   = $sewa->tgl_mulai   ? Carbon::parse($sewa->tgl_mulai)->format('d/m/Y')   : '-';
            $tglSelesai = $sewa->tgl_selesai ? Carbon::parse($sewa->tgl_selesai)->format('d/m/Y') : '-';
            $status     = ucfirst(str_replace('_', ' ', $sewa->status ?? ''));

            if ($sewa->details->isNotEmpty()) {
                $itemNo = 0;
                foreach ($sewa->details as $detail) {
                    $itemNo++;
                    $rows->push([
                        $no,                                   // #
                        $tglInput,                             // Tgl Input
                        $sewa->nama_penyewa,                   // Nama Penyewa
                        $sewa->nomor_telepon,                  // No. Telepon
                        $sewa->nomor_ktp ?? '-',               // No. KTP
                        $detail->nama_alat,                    // Nama Alat
                        $detail->qty,                          // Qty
                        $detail->harga_sewa ?? 0,              // Harga Sewa/hari (Rp)
                        ($detail->qty * ($detail->harga_sewa ?? 0) * ($sewa->durasi_hari ?? 1)), // Subtotal
                        $sewa->durasi_hari ?? 0,               // Durasi (Hari)
                        $tglMulai,                             // Tgl Mulai
                        $tglSelesai,                           // Tgl Selesai
                        $sewa->pengiriman_label ?? $sewa->pengiriman, // Pengiriman
                        $sewa->biaya_ongkir  ?? 0,             // Ongkir (Rp)
                        $sewa->alamat_penyewa,                 // Alamat
                        ucfirst($sewa->metode_pembayaran ?? '-'), // Metode Bayar
                        $status,                               // Status
                        $sewa->keterangan ?? '-',              // Keterangan
                    ]);
                }
            } else {
                // Fallback kalau tidak ada details
                $rows->push([
                    $no,
                    $tglInput,
                    $sewa->nama_penyewa,
                    $sewa->nomor_telepon,
                    $sewa->nomor_ktp ?? '-',
                    $sewa->produk_alkes ?? '-',
                    '-',
                    0,
                    0,
                    $sewa->durasi_hari ?? 0,
                    $tglMulai,
                    $tglSelesai,
                    $sewa->pengiriman_label ?? $sewa->pengiriman,
                    $sewa->biaya_ongkir ?? 0,
                    $sewa->alamat_penyewa,
                    ucfirst($sewa->metode_pembayaran ?? '-'),
                    $status,
                    $sewa->keterangan ?? '-',
                ]);
            }
        }

        $this->rowCount = $no;
        return $rows;
    }

    public function headings(): array
    {
        return [
            '#',
            'Tgl Input',
            'Nama Penyewa',
            'No. Telepon',
            'No. KTP',
            'Nama Alat',
            'Qty',
            'Harga Sewa/hari (Rp)',
            'Subtotal (Rp)',
            'Durasi (Hari)',
            'Tgl Mulai',
            'Tgl Selesai',
            'Pengiriman',
            'Ongkir (Rp)',
            'Alamat Penyewa',
            'Metode Pembayaran',
            'Status',
            'Keterangan',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        // Zebra stripe (mulai row 5)
        for ($i = 5; $i <= $lastRow; $i++) {
            if ($i % 2 === 0) {
                $sheet->getStyle('A' . $i . ':R' . $i)->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFFFF8E1'],
                    ],
                ]);
            }
        }

        $sheet->getRowDimension(4)->setRowHeight(26);

        return [
            4 => [
                'font' => [
                    'bold'  => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size'  => 10,
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF7C3AED'],  // ungu, beda dari sheet 1
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                    'wrapText'   => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['argb' => 'FF5B21B6'],
                    ],
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet   = $event->sheet->getDelegate();
                $genTime = Carbon::now()->format('d/m/Y H:i');

                // ── Sisipkan 3 baris info di atas ──
                $sheet->insertNewRowBefore(1, 3);

                // Row 1: Judul
                $sheet->mergeCells('A1:R1');
                $sheet->setCellValue('A1', 'DETAIL PRODUK PENYEWAAN — PARALKES+');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => [
                        'bold'  => true,
                        'size'  => 14,
                        'color' => ['argb' => 'FF7C3AED'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(32);

                // Row 2: Info periode
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

                $sheet->mergeCells('A2:I2');
                $sheet->setCellValue('A2', 'Periode: ' . $periode . '   |   Status: ' . $statusLabel);
                $sheet->mergeCells('J2:R2');
                $sheet->setCellValue('J2', 'Digenerate: ' . $genTime . '   |   Total: ' . $this->rowCount . ' transaksi');
                $sheet->getStyle('A2:R2')->applyFromArray([
                    'font'      => ['size' => 10, 'color' => ['argb' => 'FF555555']],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    'fill'      => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFF5F3FF'],
                    ],
                ]);
                $sheet->getStyle('J2')->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(20);

                // Row 3: Spacer
                $sheet->getRowDimension(3)->setRowHeight(6);

                // ── Baris TOTAL di akhir ──
                $newLastRow = $sheet->getHighestRow() + 2;
                $dataStart  = 5;
                $dataEnd    = $newLastRow - 2;

                $sheet->mergeCells('A' . $newLastRow . ':F' . $newLastRow);
                $sheet->setCellValue('A' . $newLastRow, 'TOTAL: ' . $this->rowCount . ' transaksi');
                $sheet->setCellValue('G' . $newLastRow, '=SUM(G' . $dataStart . ':G' . $dataEnd . ')'); // total qty
                $sheet->setCellValue('I' . $newLastRow, '=SUM(I' . $dataStart . ':I' . $dataEnd . ')'); // total subtotal
                $sheet->setCellValue('N' . $newLastRow, '=SUM(N' . $dataStart . ':N' . $dataEnd . ')'); // total ongkir

                $sheet->getStyle('A' . $newLastRow . ':R' . $newLastRow)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FF7C3AED'],
                    ],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension($newLastRow)->setRowHeight(24);

                // Format angka
                $rupiahFormat = '#,##0';
                foreach (['H', 'I', 'N'] as $col) {
                    $sheet->getStyle($col . $dataStart . ':' . $col . $newLastRow)
                          ->getNumberFormat()
                          ->setFormatCode($rupiahFormat);
                }

                // Freeze pane
                $sheet->freezePane('A5');
            },
        ];
    }

    public function title(): string
    {
        return '📦 Detail per Produk';
    }
}