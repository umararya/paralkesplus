<?php
// app/Exports/PenyewaanExport.php

namespace App\Exports;

use App\Models\Penyewaan;
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

class PenyewaanExport implements
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
        return Penyewaan::with('details')
            ->when($this->search, function ($q) {
                $s = $this->search;
                $q->where('nama_penyewa',       'like', "%{$s}%")
                  ->orWhere('nomor_telepon',     'like', "%{$s}%")
                  ->orWhere('produk_alkes',      'like', "%{$s}%")
                  ->orWhere('status',            'like', "%{$s}%")
                  ->orWhere('pengiriman',        'like', "%{$s}%")
                  ->orWhere('alamat_penyewa',    'like', "%{$s}%")
                  ->orWhere('metode_pembayaran', 'like', "%{$s}%")
                  ->orWhere('keterangan',        'like', "%{$s}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            '#',
            'Nama Penyewa',
            'No. Telepon',
            'Tempat, Tgl Lahir',
            'No. KTP',
            'Alamat',
            'Alat / Produk',
            'Tgl Mulai',
            'Tgl Selesai',
            'Durasi (Hari)',
            'Pengiriman',
            'Metode Pembayaran',
            'Biaya Ongkir (Rp)',
            'Diskon Global (Rp)',
            'Total Sewa (Rp)',
            'Total Tagihan (Rp)',
            'Status',
            'Keterangan',
            'Bukti Pembayaran (URL)',
            'Foto KTP/SIM (URL)',
        ];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;

        $alat = $row->details->count()
            ? $row->details->pluck('nama_alat')->implode(', ')
            : ($row->produk_alkes ?? '-');

        $buktiBayar = $row->bukti_pembayaran
            ? asset('storage/' . $row->bukti_pembayaran)
            : '-';

        $fotoKtp = $row->foto_ktp_sim
            ? asset('storage/' . $row->foto_ktp_sim)
            : '-';

        return [
            $no,
            $row->nama_penyewa,
            $row->nomor_telepon,
            $row->tempat_tanggal_lahir ?? '-',
            $row->nomor_ktp            ?? '-',
            $row->alamat_penyewa,
            $alat,
            $row->tgl_mulai   ? Carbon::parse($row->tgl_mulai)->format('d/m/Y')   : '-',
            $row->tgl_selesai ? Carbon::parse($row->tgl_selesai)->format('d/m/Y') : '-',
            $row->durasi_hari      ?? 0,
            $row->pengiriman,
            ucfirst($row->metode_pembayaran),
            $row->biaya_ongkir     ?? 0,
            $row->diskon_global    ?? 0,
            $row->total_harga_sewa ?? 0,
            $row->total_tagihan    ?? 0,
            ucfirst(str_replace('_', ' ', $row->status ?? '')),
            $row->keterangan ?? '-',
            $buktiBayar,
            $fotoKtp,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        // Styling header row
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

        // Styling kolom URL (S & T = kolom 19 & 20)
        $sheet->getStyle('S2:T' . $lastRow)->applyFromArray([
            'font' => ['color' => ['argb' => 'FF1D6FA4'], 'underline' => true],
        ]);

        // Warna zebra untuk baris data
        for ($i = 2; $i <= $lastRow; $i++) {
            if ($i % 2 === 0) {
                $sheet->getStyle('A' . $i . ':T' . $i)->applyFromArray([
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
        return 'Data Penyewaan';
    }
}