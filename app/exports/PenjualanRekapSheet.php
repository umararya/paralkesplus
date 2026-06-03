<?php
// app/exports/PenjualanRekapSheet.php

namespace App\Exports;

use App\Models\Penjualan;
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
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PenjualanRekapSheet implements
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

    // Jumlah kolom (A..W = 23 kolom)
    const LAST_COL = 'W';
    const LAST_COL_IDX = 23;

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
        return Penjualan::with(['details', 'user', 'pembayarans'])
            ->when($this->search, function ($q) {
                $s = $this->search;
                $q->where('nama_pelanggan',     'like', "%{$s}%")
                  ->orWhere('nomor_telepon',     'like', "%{$s}%")
                  ->orWhere('alamat_pelanggan',  'like', "%{$s}%")
                  ->orWhere('jenis_pembayaran',  'like', "%{$s}%")
                  ->orWhere('keterangan',        'like', "%{$s}%")
                  ->orWhere('status_pembayaran', 'like', "%{$s}%");
            })
            ->when($this->dateFrom, fn($q) => $q->whereDate('tanggal_penjualan', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('tanggal_penjualan', '<=', $this->dateTo))
            ->when($this->statusPembayaran && $this->statusPembayaran !== 'semua',
                fn($q) => $q->where('status_pembayaran', $this->statusPembayaran))
            ->when($this->statusTransaksi && $this->statusTransaksi !== 'semua',
                fn($q) => $q->where('status_transaksi', $this->statusTransaksi))
            ->orderBy('tanggal_penjualan', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            '#',
            'No. Transaksi',
            'Tanggal',
            'Nama Pelanggan',
            'No. Telepon',
            'Alamat Pelanggan',
            'Barang Dijual',
            'Total Qty',
            'Subtotal Barang (Rp)',
            'Diskon Global (Rp)',
            'Ongkos Kirim (Rp)',
            'Jasa Instalasi (Rp)',
            'Total Tagihan (Rp)',
            'Total Terbayar (Rp)',
            'Sisa Tagihan (Rp)',
            'Metode Pembayaran',
            'Jenis Bayar Awal',
            'Jasa Pengiriman',
            'Status Pembayaran',
            'Status Transaksi',
            'Catatan Pembatalan',
            'Input Oleh',
            'Keterangan',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        $barangDijual = $row->details->pluck('nama_barang')->filter()->implode(', ');
        $totalQty     = (int) $row->details->sum('qty');

        $metodePembayaran = match($row->metode_pembayaran) {
            'cash'     => 'Cash',
            'dp'       => 'DP / Cicilan',
            'transfer' => 'Transfer',
            default    => ucfirst($row->metode_pembayaran ?? '-'),
        };

        $jenisBayarAwal = match($row->jenis_pembayaran) {
            'cash'     => 'Cash',
            'transfer' => 'Transfer',
            'qris'     => 'QRIS',
            default    => ucfirst($row->jenis_pembayaran ?? '-'),
        };

        $jasaPengiriman = match($row->jasa_pengiriman) {
            'gosend_grab'   => 'GoSend / GrabExpress',
            'rental_mobil'  => 'Rental Mobil Paralkes',
            'ambil_sendiri' => 'Ambil Sendiri',
            default         => ucfirst($row->jasa_pengiriman ?? 'Ambil Sendiri'),
        };

        $statusPembayaran = match($row->status_pembayaran) {
            'lunas'       => 'Lunas',
            'dp'          => 'DP / Sebagian',
            'belum_lunas' => 'Belum Lunas',
            default       => ucfirst($row->status_pembayaran ?? '-'),
        };

        $statusTransaksi = match($row->status_transaksi) {
            'aktif'   => 'Aktif',
            'selesai' => 'Selesai',
            'batal'   => 'Dibatalkan',
            default   => ucfirst($row->status_transaksi ?? '-'),
        };

        return [
            $no,
            $row->id,
            Carbon::parse($row->tanggal_penjualan)->format('d/m/Y'),
            $row->nama_pelanggan       ?? '-',
            $row->nomor_telepon        ?? '-',
            $row->alamat_pelanggan     ?? '-',
            $barangDijual ?: '-',
            $totalQty,
            (int) ($row->total_harga       ?? 0),
            (int) ($row->diskon_global     ?? 0),
            (int) ($row->harga_pengiriman  ?? 0),
            (int) ($row->jasa_instalasi    ?? 0),
            (int) ($row->total_tagihan     ?? 0),
            (int) ($row->total_terbayar    ?? 0),
            (int) ($row->sisa_tagihan      ?? 0),
            $metodePembayaran,
            $jenisBayarAwal,
            $jasaPengiriman,
            $statusPembayaran,
            $statusTransaksi,
            $row->catatan_pembatalan   ?? '-',
            $row->user->name           ?? '-',
            $row->keterangan           ?? '-',
        ];
    }

    public function columnFormats(): array
    {
        // Kolom I s/d O = subtotal, diskon, ongkir, instalasi, tagihan, terbayar, sisa
        $currency = '"Rp "#,##0';
        return [
            'I' => $currency,
            'J' => $currency,
            'K' => $currency,
            'L' => $currency,
            'M' => $currency,
            'N' => $currency,
            'O' => $currency,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = self::LAST_COL;

        // ── Header row ──
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

        // ── Zebra stripe ──
        for ($i = 2; $i <= $lastRow; $i++) {
            if ($i % 2 === 0) {
                $sheet->getStyle('A' . $i . ':' . $lastCol . $i)->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFF0F7FF'],
                    ],
                ]);
            }
        }

        // ── Warna status pembayaran (kolom S) ──
        for ($i = 2; $i <= $lastRow; $i++) {
            $val = $sheet->getCell('S' . $i)->getValue();
            if ($val === 'Lunas') {
                $sheet->getStyle('S' . $i)->applyFromArray([
                    'font' => ['color' => ['argb' => 'FF16A34A'], 'bold' => true],
                ]);
            } elseif ($val === 'DP / Sebagian') {
                $sheet->getStyle('S' . $i)->applyFromArray([
                    'font' => ['color' => ['argb' => 'FFD97706'], 'bold' => true],
                ]);
            } elseif ($val === 'Belum Lunas') {
                $sheet->getStyle('S' . $i)->applyFromArray([
                    'font' => ['color' => ['argb' => 'FFDC2626'], 'bold' => true],
                ]);
            }
        }

        // ── Warna status transaksi (kolom T) ──
        for ($i = 2; $i <= $lastRow; $i++) {
            $val = $sheet->getCell('T' . $i)->getValue();
            if ($val === 'Selesai') {
                $sheet->getStyle('T' . $i)->applyFromArray([
                    'font' => ['color' => ['argb' => 'FF16A34A'], 'bold' => true],
                ]);
            } elseif ($val === 'Aktif') {
                $sheet->getStyle('T' . $i)->applyFromArray([
                    'font' => ['color' => ['argb' => 'FF1D6FA4'], 'bold' => true],
                ]);
            } elseif ($val === 'Dibatalkan') {
                $sheet->getStyle('T' . $i)->applyFromArray([
                    'font' => ['color' => ['argb' => 'FFDC2626'], 'bold' => true],
                ]);
            }
        }

        // ── Summary row di bawah ──
        if ($lastRow >= 2) {
            $summaryRow = $lastRow + 2;

            $sheet->setCellValue('H' . $summaryRow, 'TOTAL:');
            $sheet->setCellValue('M' . $summaryRow, '=SUM(M2:M' . $lastRow . ')');
            $sheet->setCellValue('N' . $summaryRow, '=SUM(N2:N' . $lastRow . ')');
            $sheet->setCellValue('O' . $summaryRow, '=SUM(O2:O' . $lastRow . ')');

            $sheet->getStyle('H' . $summaryRow . ':O' . $summaryRow)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['argb' => 'FF1D6FA4']],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFDBEAFE'],
                ],
            ]);
        }

        // ── Freeze header row ──
        $sheet->freezePane('A2');

        // ── Row height header ──
        $sheet->getRowDimension(1)->setRowHeight(30);

        return [1 => $headerStyle];
    }

    public function title(): string
    {
        return 'Rekap Transaksi';
    }
}