{{-- resources/views/admin/penjualan/cetak/invoice.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Penjualan - {{ $penjualan->nama_pelanggan }}</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #1a1a1a;
            background: #fff;
        }
        .page-wrapper {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 12mm 14mm 14mm 14mm;
            background: #fff;
            display: flex;
            flex-direction: column;
            position: relative;
        }
        .page-content { flex: 1; }

        /* ── KOP ── */
        .kop {
            display: flex; align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid #1D6FA4;
            padding-bottom: 10px; margin-bottom: 14px;
        }
        .kop-left img  { height: 60px; object-fit: contain; }
        .kop-center { text-align: center; flex: 1; padding: 0 12px; }
        .kop-center .nama-toko {
            font-size: 17px; font-weight: 800;
            color: #1D6FA4; text-transform: uppercase; letter-spacing: 1px;
        }
        .kop-center .tagline { font-size: 10px; color: #555; margin-top: 2px; }
        .kop-center .alamat-toko { font-size: 9.5px; color: #666; margin-top: 3px; line-height: 1.5; }
        .kop-right img { height: 60px; object-fit: contain; }

        /* ── JUDUL + STATUS BADGE ── */
        .invoice-title-bar {
            background: #1D6FA4; color: #fff;
            padding: 7px 14px;
            border-radius: 4px; margin-bottom: 14px;
            display: flex; align-items: center; justify-content: center;
            position: relative;
        }
        .invoice-title-bar h2 {
            font-size: 15px; font-weight: 700;
            letter-spacing: 2px; text-transform: uppercase;
            text-align: center;
        }
        .status-badge-print {
            position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
            padding: 3px 10px; border-radius: 4px;
            font-size: 10px; font-weight: 800; letter-spacing: 0.8px;
            text-transform: uppercase; border: 2px solid;
        }
        .status-lunas   { background:#dcfce7; color:#15803d; border-color:#15803d; }
        .status-dp      { background:#fef3c7; color:#b45309; border-color:#b45309; }
        .status-belum   { background:#fee2e2; color:#b91c1c; border-color:#b91c1c; }

        /* ── WATERMARK BATAL ── */
        .watermark-batal {
            position: fixed; top: 50%; left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 88px; font-weight: 900; color: rgba(220,38,38,0.10);
            text-transform: uppercase; letter-spacing: 10px;
            pointer-events: none; z-index: 0; white-space: nowrap;
            user-select: none;
        }

        /* ── INFO BOXES ── */
        .info-section {
            display: flex; justify-content: space-between;
            gap: 10px; margin-bottom: 16px;
        }
        .info-box {
            flex: 1; border: 1px solid #dde3ea;
            border-radius: 6px; padding: 10px 12px;
        }
        .info-box .info-title {
            font-size: 10px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.8px; color: #1D6FA4;
            border-bottom: 1px solid #dde3ea; padding-bottom: 5px; margin-bottom: 8px;
        }
        .info-row { display: flex; gap: 6px; margin-bottom: 4px; line-height: 1.5; }
        .info-label { color: #666; min-width: 110px; font-size: 11px; }
        .info-value { font-weight: 600; color: #1a1a1a; font-size: 11px; }

        .info-box.kirim-box { border-color: #bfdbfe; background: #f0f7ff; }
        .info-box.kirim-box .info-title { color: #1D6FA4; border-bottom-color: #bfdbfe; }

        .chip-ambil {
            display: inline-block; background: #fef3c7; color: #92400e;
            border: 1px solid #fde68a; border-radius: 4px;
            padding: 2px 8px; font-size: 10px; font-weight: 700; margin-top: 2px;
        }

        /* ── TABEL DETAIL ── */
        .section-title {
            font-size: 11px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.8px; color: #1D6FA4; margin-bottom: 7px;
        }
        .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .detail-table thead tr { background: #1D6FA4; color: #fff; }
        .detail-table th {
            padding: 8px 10px; font-size: 10.5px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px; text-align: left;
        }
        .detail-table th.center, .detail-table td.center { text-align: center; }
        .detail-table th.right,  .detail-table td.right  { text-align: right; }
        .detail-table td {
            padding: 8px 10px; font-size: 11.5px;
            border-bottom: 1px solid #eef0f3; vertical-align: middle;
        }
        .detail-table tbody tr:nth-child(even) td { background: #f8fafc; }
        .badge-diskon {
            display: inline-block; background: #fef3c7; color: #b45309;
            border-radius: 4px; padding: 1px 6px; font-size: 10px; font-weight: 700;
        }
        .badge-kondisi-baru  {
            display: inline-block; background: #dcfce7; color: #15803d;
            border-radius: 4px; padding: 1px 6px; font-size: 10px; font-weight: 700;
        }
        .badge-kondisi-bekas {
            display: inline-block; background: #fef3c7; color: #b45309;
            border-radius: 4px; padding: 1px 6px; font-size: 10px; font-weight: 700;
        }

        /* ── BIAYA + TERBILANG ── */
        .biaya-section {
            display: flex; justify-content: space-between;
            align-items: flex-start; gap: 16px; margin-bottom: 14px;
        }
        .terbilang-wrap { flex: 1; }
        .terbilang-table {
            width: 100%; border-collapse: collapse;
            border: 1px solid #dde3ea; border-radius: 6px; overflow: hidden;
            margin-bottom: 8px;
        }
        .terbilang-table .tb-title {
            background: #1D6FA4; color: #fff; font-size: 10.5px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.8px;
            padding: 7px 12px; text-align: center; display: block;
        }
        .terbilang-table .tb-value {
            padding: 10px 12px; font-size: 11.5px; color: #1a1a1a;
            font-weight: 600; line-height: 1.6; font-style: italic; text-align: center;
            display: block;
        }
        .terbilang-table .tb-sub {
            padding: 6px 12px 8px; font-size: 10.5px; color: #b45309;
            font-style: italic; text-align: center; display: block;
            border-top: 1px dashed #fde68a; background: #fffbeb;
        }

        .biaya-box {
            width: 290px; flex-shrink: 0;
            border: 1px solid #dde3ea; border-radius: 6px; overflow: hidden;
        }
        .biaya-row {
            display: flex; justify-content: space-between;
            padding: 7px 12px; font-size: 11.5px; border-bottom: 1px solid #eef0f3;
        }
        .biaya-row:last-child { border-bottom: none; }
        .biaya-row .label { color: #555; }
        .biaya-row .value { font-weight: 600; }
        .biaya-row.diskon-row .label { color: #b45309; font-weight: 600; }
        .biaya-row.diskon-row .value { color: #b45309; font-weight: 700; }
        .biaya-row.ongkir-row .label { color: #1D6FA4; }
        .biaya-row.ongkir-row .value { color: #1D6FA4; font-weight: 700; }
        .biaya-row.instalasi-row .label { color: #7c3aed; }
        .biaya-row.instalasi-row .value { color: #7c3aed; font-weight: 700; }
        .biaya-row.total-row { background: #1D6FA4; color: #fff; }
        .biaya-row.total-row .label,
        .biaya-row.total-row .value { color: #fff; font-weight: 700; font-size: 12.5px; }
        .biaya-row.dibayar-row { background: #f0fdf4; }
        .biaya-row.dibayar-row .label { color: #15803d; font-weight: 600; }
        .biaya-row.dibayar-row .value { color: #15803d; font-weight: 700; }
        .biaya-row.sisa-row-lunas { background: #f0fdf4; }
        .biaya-row.sisa-row-lunas .label,
        .biaya-row.sisa-row-lunas .value { color: #15803d; font-weight: 700; }
        .biaya-row.sisa-row-belum { background: #fff7ed; }
        .biaya-row.sisa-row-belum .label { color: #b91c1c; font-weight: 600; }
        .biaya-row.sisa-row-belum .value { color: #b91c1c; font-weight: 800; font-size: 12px; }

        /* ── RIWAYAT PEMBAYARAN ── */
        .riwayat-section { margin-bottom: 14px; }
        .riwayat-title {
            font-size: 11px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.8px; color: #1D6FA4; margin-bottom: 7px;
        }
        .riwayat-table { width: 100%; border-collapse: collapse; }
        .riwayat-table thead tr { background: #1e40af; color: #fff; }
        .riwayat-table th {
            padding: 7px 10px; font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.4px; text-align: left;
        }
        .riwayat-table th.right, .riwayat-table td.right { text-align: right; }
        .riwayat-table th.center, .riwayat-table td.center { text-align: center; }
        .riwayat-table td {
            padding: 7px 10px; font-size: 11px;
            border-bottom: 1px solid #eef0f3; vertical-align: middle;
        }
        .riwayat-table tbody tr:nth-child(even) td { background: #f8fafc; }
        .badge-tipe {
            display: inline-block; border-radius: 4px;
            padding: 1px 7px; font-size: 10px; font-weight: 700;
        }
        .tipe-dp        { background: #fef3c7; color: #b45309; }
        .tipe-cicilan   { background: #dbeafe; color: #1e40af; }
        .tipe-pelunasan { background: #dcfce7; color: #15803d; }
        .riwayat-table tfoot td {
            padding: 7px 10px; font-size: 11.5px; font-weight: 700;
            border-top: 2px solid #1D6FA4; color: #1D6FA4;
        }

        /* ── CATATAN ── */
        .catatan-box {
            border: 1px solid #dde3ea; border-radius: 6px;
            padding: 10px 12px; margin-bottom: 20px; background: #f8fafc;
        }
        .catatan-box .catatan-title {
            font-size: 10px; font-weight: 700; color: #1D6FA4;
            text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 5px;
        }
        .catatan-box p { font-size: 11px; color: #555; line-height: 1.6; }

        /* ── TTD ── */
        .ttd-section {
            display: flex; justify-content: space-between;
            align-items: flex-end; margin-top: 10px;
        }
        .ttd-box { text-align: center; width: 180px; }
        .ttd-box .ttd-label { font-size: 11px; color: #555; margin-bottom: 6px; }
        .ttd-box .ttd-img {
            height: 60px; object-fit: contain;
            display: block; margin: 0 auto 4px auto;
        }
        .ttd-box .ttd-name {
            border-top: 1.5px solid #1a1a1a; padding-top: 5px;
            font-size: 11.5px; font-weight: 700; color: #1a1a1a;
        }
        .ttd-box .ttd-space {
            height: 60px;
        }
        .ttd-box .ttd-jabatan { font-size: 10px; color: #777; margin-top: 2px; }
        .ttd-logo { text-align: center; }
        .ttd-logo img {
            height: 55px; object-fit: contain;
            filter: invert(1) sepia(1) saturate(2) hue-rotate(180deg); opacity: 0.15;
        }

        /* ── FOOTER ── */
        .page-footer {
            margin-top: auto; padding-top: 10px;
            border-top: 1.5px solid #1D6FA4; text-align: center;
            font-size: 9.5px; color: #888; line-height: 1.7;
        }

        @media print {
            body { background: #fff; }
            .page-wrapper { padding: 10mm 12mm; }
            .no-print { display: none !important; }
            @page { size: A4; margin: 0; }
        }
    </style>
</head>
<body>

{{-- TOMBOL CETAK --}}
<div class="no-print" style="position:fixed; top:16px; right:16px; z-index:999; display:flex; gap:8px;">
    <button onclick="window.print()"
            style="display:inline-flex; align-items:center; gap:6px; padding:9px 18px;
                   background:#1D6FA4; color:#fff; border:none; border-radius:8px;
                   font-size:13px; font-weight:600; cursor:pointer;">
        🖨️ Cetak / Simpan PDF
    </button>
    <button onclick="window.close()"
            style="display:inline-flex; align-items:center; gap:6px; padding:9px 14px;
                   background:#f1f5f9; color:#475569; border:1px solid #cbd5e1;
                   border-radius:8px; font-size:13px; font-weight:600; cursor:pointer;">
        ✕ Tutup
    </button>
</div>

@php
    App::setLocale('id');
    \Carbon\Carbon::setLocale('id');
    $now = now('Asia/Jakarta');

    $details    = $penjualan->details ?? collect();
    $pembayarans = $penjualan->pembayarans ?? collect();

    /* ── Kalkulasi ── */
    $subtotalBarang  = $details->sum(function($d) {
        $sub = $d->qty * $d->harga_satuan;
        if (($d->diskon ?? 0) > 0) $sub = $sub * (1 - $d->diskon / 100);
        return round($sub);
    });
    $diskonGlobal    = max(0, (int)($penjualan->diskon_global    ?? 0));
    $hargaPengiriman = max(0, (int)($penjualan->harga_pengiriman ?? 0));
    $jasaInstalasi   = max(0, (int)($penjualan->jasa_instalasi   ?? 0));
    $jasaPengiriman  = $penjualan->jasa_pengiriman ?? 'ambil_sendiri';

    $totalTagihan    = max(0, $subtotalBarang - $diskonGlobal + $hargaPengiriman + $jasaInstalasi);
    $totalTerbayar   = max(0, (int)($penjualan->total_terbayar ?? $pembayarans->sum('jumlah_bayar')));
    $sisaTagihan     = max(0, $totalTagihan - $totalTerbayar);

    /* ── Label pengiriman ── */
    $kirimLabel = match($jasaPengiriman) {
        'gosend_grab'  => 'Via GoSend / GrabExpress',
        'rental_mobil' => 'Via Rental Mobil Paralkes',
        default        => 'Ambil dan antar kembali oleh penyewa',
    };
    $kirimIcon = match($jasaPengiriman) {
        'gosend_grab'  => '🛵',
        'rental_mobil' => '🚗',
        default        => '🚶',
    };

    /* ── Status badge ── */
    $statusPembayaran = $penjualan->status_pembayaran ?? 'belum_lunas';
    $statusTransaksi  = $penjualan->status_transaksi  ?? 'aktif';
    $isBatal          = $statusTransaksi === 'batal';

    $badgeClass = match($statusPembayaran) {
        'lunas' => 'status-lunas',
        'dp'    => 'status-dp',
        default => 'status-belum',
    };
    $badgeLabel = match($statusPembayaran) {
        'lunas' => '✓ LUNAS',
        'dp'    => '⚠ DOWN PAYMENT',
        default => '⚠ BELUM LUNAS',
    };
    if ($isBatal) {
        $badgeClass = 'status-belum';
        $badgeLabel = '✕ DIBATALKAN';
    }

    /* ── Tampilkan riwayat pembayaran? ── */
    $tampilRiwayat = $pembayarans->count() > 0
        && ($statusPembayaran !== 'lunas' || $pembayarans->count() > 1);

    /* ── Terbilang ── */
    function terbilangJual(int $n): string {
        if ($n < 0) return 'minus ' . terbilangJual(-$n);
        $satuan = ['','satu','dua','tiga','empat','lima','enam','tujuh','delapan','sembilan','sepuluh','sebelas'];
        if ($n < 12)   return $satuan[$n];
        if ($n < 20)   return terbilangJual($n - 10) . ' belas';
        if ($n < 100)  return terbilangJual((int)($n / 10)) . ' puluh' . ($n % 10 ? ' ' . terbilangJual($n % 10) : '');
        if ($n < 200)  return 'seratus' . ($n - 100 ? ' ' . terbilangJual($n - 100) : '');
        if ($n < 1000) return terbilangJual((int)($n / 100)) . ' ratus' . ($n % 100 ? ' ' . terbilangJual($n % 100) : '');
        if ($n < 2000) return 'seribu' . ($n - 1000 ? ' ' . terbilangJual($n - 1000) : '');
        if ($n < 1_000_000)     return terbilangJual((int)($n / 1000)) . ' ribu' . ($n % 1000 ? ' ' . terbilangJual($n % 1000) : '');
        if ($n < 1_000_000_000) return terbilangJual((int)($n / 1_000_000)) . ' juta' . ($n % 1_000_000 ? ' ' . terbilangJual($n % 1_000_000) : '');
        return terbilangJual((int)($n / 1_000_000_000)) . ' miliar' . ($n % 1_000_000_000 ? ' ' . terbilangJual($n % 1_000_000_000) : '');
    }

    $terbilangTotal  = ucfirst(terbilangJual($totalTagihan)) . ' Rupiah';
    $terbilangTerbayar = $totalTerbayar > 0
        ? ucfirst(terbilangJual($totalTerbayar)) . ' Rupiah'
        : null;
    $terbilangSisa = $sisaTagihan > 0
        ? ucfirst(terbilangJual($sisaTagihan)) . ' Rupiah'
        : null;
@endphp

{{-- Watermark BATAL --}}
@if($isBatal)
<div class="watermark-batal">DIBATALKAN</div>
@endif

<div class="page-wrapper">
    <div class="page-content">

        {{-- ── KOP ── --}}
        <div class="kop">
            <div class="kop-left">
                <img src="{{ asset('images/logo-cop-paralkesplus2.png') }}" alt="Logo Kiri">
            </div>
            <div class="kop-center">
                <div class="nama-toko">Paralkes</div>
                <div class="tagline">Penyewaan &amp; Penjualan Alat Kesehatan</div>
                <div class="alamat-toko">
                    Jl. Srikaton Selatan No.19, Purwoyoso, Kec. Ngaliyan, Kota Semarang, Jawa Tengah 50184<br>
                    Telp: 0877-7732-1557 &nbsp;·&nbsp; Instagram: @sewaalkes_paralkes
                </div>
            </div>
            <div class="kop-right">
                <img src="{{ asset('images/logo-cop-paralkesplus3.png') }}" alt="Logo Kanan">
            </div>
        </div>

        {{-- ── JUDUL + STATUS BADGE ── --}}
        <div class="invoice-title-bar">
            <h2>Invoice Penjualan Alat Kesehatan</h2>
            <span class="status-badge-print {{ $badgeClass }}">{{ $badgeLabel }}</span>
        </div>

        {{-- ── INFO BOXES ── --}}
        <div class="info-section">

            {{-- Box 1: Info Invoice --}}
            <div class="info-box">
                <div class="info-title">📋 Informasi Invoice</div>
                <div class="info-row">
                    <span class="info-label">No. Invoice</span>
                    <span class="info-value">: INV-JL-{{ str_pad($penjualan->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tgl Penjualan</span>
                    <span class="info-value">:
                        {{ $penjualan->tanggal_penjualan
                            ? \Carbon\Carbon::parse($penjualan->tanggal_penjualan)->locale('id')->translatedFormat('d F Y')
                            : '-' }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Metode Bayar</span>
                    <span class="info-value">: {{ ucfirst($penjualan->jenis_pembayaran ?? '-') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status Bayar</span>
                    <span class="info-value">:
                        @if($statusPembayaran === 'lunas')
                            <span style="color:#15803d;font-weight:700;">✓ Lunas</span>
                        @elseif($statusPembayaran === 'dp')
                            <span style="color:#b45309;font-weight:700;">Down Payment</span>
                        @else
                            <span style="color:#b91c1c;font-weight:700;">Belum Lunas</span>
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Dicetak</span>
                    <span class="info-value">: {{ $now->translatedFormat('d F Y') }}</span>
                </div>
            </div>

            {{-- Box 2: Data Pelanggan --}}
            <div class="info-box">
                <div class="info-title">👤 Data Pelanggan</div>
                <div class="info-row">
                    <span class="info-label">Nama Lengkap</span>
                    <span class="info-value">: {{ $penjualan->nama_pelanggan }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">No. Telepon</span>
                    <span class="info-value">: {{ $penjualan->nomor_telepon ?: '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Alamat</span>
                    <span class="info-value">: {{ $penjualan->alamat_pelanggan }}</span>
                </div>
            </div>

            {{-- Box 3: Pengiriman --}}
            @if($jasaPengiriman !== 'ambil_sendiri' || $hargaPengiriman > 0 || $jasaInstalasi > 0)
            <div class="info-box kirim-box">
                <div class="info-title">{{ $kirimIcon }} Informasi Pengiriman</div>
                <div class="info-row">
                    <span class="info-label">Jasa Kirim</span>
                    <span class="info-value">: {{ $kirimLabel }}</span>
                </div>
                @if($hargaPengiriman > 0)
                <div class="info-row">
                    <span class="info-label">Ongkos Kirim</span>
                    <span class="info-value" style="color:#1D6FA4;">
                        : Rp {{ number_format($hargaPengiriman, 0, ',', '.') }}
                    </span>
                </div>
                @else
                <div class="info-row">
                    <span class="info-label">Ongkos Kirim</span>
                    <span class="info-value" style="color:#15803d;">: Gratis (Rp 0)</span>
                </div>
                @endif
                @if($jasaInstalasi > 0)
                <div class="info-row">
                    <span class="info-label">Jasa Instalasi</span>
                    <span class="info-value" style="color:#7c3aed;">
                        : Rp {{ number_format($jasaInstalasi, 0, ',', '.') }}
                    </span>
                </div>
                @endif
            </div>
            @else
            <div class="info-box" style="display:flex;flex-direction:column;justify-content:flex-end;max-width:160px;flex:0 0 160px;">
                <div class="info-title">📦 Pengiriman</div>
                <span class="chip-ambil">🚶 {{ $kirimLabel }}</span>
            </div>
            @endif

        </div>

        {{-- ── DETAIL BARANG ── --}}
        <div class="section-title">Detail Penjualan</div>
        <table class="detail-table">
            <thead>
                <tr>
                    <th class="center" style="width:28px;">No</th>
                    <th>Nama Barang</th>
                    <th class="center" style="width:58px;">Kondisi</th>
                    <th class="center" style="width:42px;">Qty</th>
                    <th class="center" style="width:48px;">Satuan</th>
                    <th class="right"  style="width:105px;">Harga / Satuan</th>
                    <th class="center" style="width:52px;">Diskon</th>
                    <th class="right"  style="width:105px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($details as $i => $item)
                @php
                    $hargaSatuan = (int)($item->harga_satuan ?? 0);
                    $qty         = (int)($item->qty ?? 0);
                    $diskon      = (float)($item->diskon ?? 0);
                    $subtotalRaw = $qty * $hargaSatuan;
                    $subtotal    = $diskon > 0 ? round($subtotalRaw * (1 - $diskon / 100)) : $subtotalRaw;
                    $kondisi     = $item->kondisi ?? 'baru';
                @endphp
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td class="center">
                        <span class="badge-kondisi-{{ $kondisi }}">{{ ucfirst($kondisi) }}</span>
                    </td>
                    <td class="center">{{ $qty }}</td>
                    <td class="center">{{ $item->satuan }}</td>
                    <td class="right">Rp {{ number_format($hargaSatuan, 0, ',', '.') }}</td>
                    <td class="center">
                        @if($diskon > 0)
                            <span class="badge-diskon">{{ $diskon }}%</span>
                        @else
                            <span style="color:#bbb; font-size:11px;">–</span>
                        @endif
                    </td>
                    <td class="right">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="center"
                        style="color:#aaa; font-style:italic; padding:14px 0;">
                        Tidak ada item tercatat.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- ── BIAYA + TERBILANG ── --}}
        <div class="biaya-section">

            <div class="terbilang-wrap">
                <table class="terbilang-table">
                    <tr>
                        <td class="tb-title">Terbilang — Total Tagihan</td>
                    </tr>
                    <tr>
                        <td class="tb-value">{{ $terbilangTotal }}</td>
                    </tr>
                    @if($terbilangSisa && $sisaTagihan > 0)
                    <tr>
                        <td class="tb-sub">
                            Sisa: {{ $terbilangSisa }}
                        </td>
                    </tr>
                    @endif
                </table>
            </div>

            <div class="biaya-box">
                <div class="biaya-row">
                    <span class="label">Subtotal Barang</span>
                    <span class="value">Rp {{ number_format($subtotalBarang, 0, ',', '.') }}</span>
                </div>

                @if($diskonGlobal > 0)
                <div class="biaya-row diskon-row">
                    <span class="label">Diskon</span>
                    <span class="value">– Rp {{ number_format($diskonGlobal, 0, ',', '.') }}</span>
                </div>
                @endif

                @if($hargaPengiriman > 0)
                <div class="biaya-row ongkir-row">
                    <span class="label">🛵 Ongkos Kirim</span>
                    <span class="value">+ Rp {{ number_format($hargaPengiriman, 0, ',', '.') }}</span>
                </div>
                @endif

                @if($jasaInstalasi > 0)
                <div class="biaya-row instalasi-row">
                    <span class="label">🔧 Jasa Instalasi</span>
                    <span class="value">+ Rp {{ number_format($jasaInstalasi, 0, ',', '.') }}</span>
                </div>
                @endif

                <div class="biaya-row total-row">
                    <span class="label">Total Tagihan</span>
                    <span class="value">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</span>
                </div>

                @if($totalTerbayar > 0)
                <div class="biaya-row dibayar-row">
                    <span class="label">✓ Sudah Dibayar</span>
                    <span class="value">– Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</span>
                </div>

                @if($sisaTagihan > 0)
                <div class="biaya-row sisa-row-belum">
                    <span class="label">⚠ Sisa Tagihan</span>
                    <span class="value">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</span>
                </div>
                @else
                <div class="biaya-row sisa-row-lunas">
                    <span class="label">✓ Sisa Tagihan</span>
                    <span class="value">Rp 0 (LUNAS)</span>
                </div>
                @endif
                @endif

            </div>
        </div>

        {{-- ── RIWAYAT PEMBAYARAN (kondisional) ── --}}
        @if($tampilRiwayat)
        <div class="riwayat-section">
            <div class="riwayat-title">💳 Riwayat Pembayaran</div>
            <table class="riwayat-table">
                <thead>
                    <tr>
                        <th class="center" style="width:28px;">#</th>
                        <th style="width:100px;">Tanggal</th>
                        <th style="width:90px;">Tipe</th>
                        <th style="width:80px;">Via</th>
                        <th class="right">Jumlah</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pembayarans as $i => $bayar)
                    <tr>
                        <td class="center">{{ $i + 1 }}</td>
                        <td>
                            {{ $bayar->tanggal_bayar
                                ? \Carbon\Carbon::parse($bayar->tanggal_bayar)->translatedFormat('d M Y')
                                : '-' }}
                        </td>
                        <td>
                            <span class="badge-tipe tipe-{{ $bayar->tipe ?? 'dp' }}">
                                {{ ucfirst($bayar->tipe ?? '-') }}
                            </span>
                        </td>
                        <td style="text-transform:uppercase; font-size:10.5px; font-weight:700; color:#555;">
                            {{ $bayar->metode ?? '-' }}
                        </td>
                        <td class="right" style="font-weight:700;">
                            Rp {{ number_format($bayar->jumlah_bayar ?? 0, 0, ',', '.') }}
                        </td>
                        <td style="font-size:10.5px; color:#666;">
                            {{ $bayar->keterangan ?: '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align:right;">Total Terbayar</td>
                        <td class="right">Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif

        {{-- ── CATATAN ── --}}
        <div class="catatan-box">
            <div class="catatan-title">📝 Catatan</div>
            <p>{{ $penjualan->keterangan ?: 'Tidak ada catatan tambahan.' }}</p>
            @if($isBatal && $penjualan->catatan_pembatalan)
            <p style="margin-top:6px; color:#b91c1c; font-weight:600;">
                ⚠ Alasan Pembatalan: {{ $penjualan->catatan_pembatalan }}
            </p>
            @endif
            <p style="margin-top:6px; color:#888;">
                ※ Barang yang sudah dibeli tidak dapat dikembalikan kecuali terdapat kerusakan
                dari pabrik. Harap periksa kondisi barang saat penerimaan.
            </p>
        </div>

        {{-- ── TTD ── --}}
        <div class="ttd-section">
            <div class="ttd-box">
                <div class="ttd-label">Hormat Kami,</div>
                <img src="{{ asset('images/ttd.png') }}" alt="Tanda Tangan Admin" class="ttd-img">
                <div class="ttd-name">Paralkes</div>
                <div class="ttd-jabatan">Admin / Pengelola</div>
            </div>
            <div class="ttd-logo">
                <img src="{{ asset('images/logo-paralkes-white.png') }}" alt="Logo Paralkes">
            </div>
            <div class="ttd-box">
                <div class="ttd-label">Pelanggan,</div>
                <div class="ttd-space"></div>
                <div class="ttd-name">{{ $penjualan->nama_pelanggan }}</div>
                <div class="ttd-jabatan">Pembeli</div>
            </div>
        </div>

    </div>{{-- end .page-content --}}

    <div class="page-footer">
        Dokumen ini digenerate secara otomatis oleh sistem Paralkes
        pada {{ $now->translatedFormat('d F Y, H:i') }} WIB<br>
        Terima kasih telah mempercayakan kebutuhan alat kesehatan Anda kepada kami.
    </div>
</div>

<script>
    // window.addEventListener('load', () => window.print());
</script>
</body>
</html>