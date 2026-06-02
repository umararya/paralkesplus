{{-- resources/views/admin/pembelian/cetak/invoice.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Buy Back - {{ $pembelian->nama_barang }}</title>
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
            border-bottom: 3px solid #D97706;
            padding-bottom: 10px; margin-bottom: 14px;
        }
        .kop-left img  { height: 60px; object-fit: contain; }
        .kop-center { text-align: center; flex: 1; padding: 0 12px; }
        .kop-center .nama-toko {
            font-size: 17px; font-weight: 800;
            color: #D97706; text-transform: uppercase; letter-spacing: 1px;
        }
        .kop-center .tagline { font-size: 10px; color: #555; margin-top: 2px; }
        .kop-center .alamat-toko { font-size: 9.5px; color: #666; margin-top: 3px; line-height: 1.5; }
        .kop-right img { height: 60px; object-fit: contain; }

        /* ── JUDUL + STATUS BADGE ── */
        .invoice-title-bar {
            background: #D97706; color: #fff;
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
            background: #fff; color: #D97706; border-color: #D97706;
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
            letter-spacing: 0.8px; color: #D97706;
            border-bottom: 1px solid #dde3ea; padding-bottom: 5px; margin-bottom: 8px;
        }
        .info-row { display: flex; gap: 6px; margin-bottom: 4px; line-height: 1.5; }
        .info-label { color: #666; min-width: 115px; font-size: 11px; }
        .info-value { font-weight: 600; color: #1a1a1a; font-size: 11px; }

        /* ── TABEL DETAIL ── */
        .section-title {
            font-size: 11px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.8px; color: #D97706; margin-bottom: 7px;
        }
        .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .detail-table thead tr { background: #D97706; color: #fff; }
        .detail-table th {
            padding: 8px 10px; font-size: 10.5px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.5px; text-align: left;
        }
        .detail-table th.center, .detail-table td.center { text-align: center; }
        .detail-table th.right,  .detail-table td.right  { text-align: right; }
        .detail-table td {
            padding: 10px 10px; font-size: 12px;
            border-bottom: 1px solid #eef0f3; vertical-align: middle;
        }
        .detail-table tbody tr:nth-child(even) td { background: #fffbeb; }

        .badge-kondisi {
            display: inline-block; border-radius: 4px;
            padding: 2px 8px; font-size: 10px; font-weight: 700;
        }
        .badge-bekas { background: #f5f3ff; color: #7c3aed; }
        .badge-baru  { background: #dcfce7; color: #15803d; }
        .badge-baik  { background: #d1fae5; color: #065f46; }
        .badge-rusak { background: #fee2e2; color: #b91c1c; }

        /* ── HARGA ASLI INFO ── */
        .harga-asli-info {
            font-size: 10px; color: #9ca3af; margin-top: 3px;
            font-style: italic;
        }
        .harga-asli-info span { color: #D97706; font-weight: 700; }

        /* ── BIAYA + TERBILANG ── */
        .biaya-section {
            display: flex; justify-content: space-between;
            align-items: flex-start; gap: 16px; margin-bottom: 14px;
        }
        .terbilang-wrap { flex: 1; }
        .terbilang-table {
            width: 100%; border-collapse: collapse;
            border: 1px solid #dde3ea; border-radius: 6px; overflow: hidden;
        }
        .terbilang-table .tb-title {
            background: #D97706; color: #fff; font-size: 10.5px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.8px;
            padding: 7px 12px; text-align: center; display: block;
        }
        .terbilang-table .tb-value {
            padding: 10px 12px; font-size: 11.5px; color: #1a1a1a;
            font-weight: 600; line-height: 1.6; font-style: italic; text-align: center;
            display: block;
        }
        .terbilang-table .tb-note {
            padding: 6px 12px 8px; font-size: 10.5px; color: #D97706;
            font-style: italic; text-align: center; display: block;
            border-top: 1px dashed #FDE68A; background: #fffbeb;
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
        .biaya-row.total-row { background: #D97706; color: #fff; }
        .biaya-row.total-row .label,
        .biaya-row.total-row .value { color: #fff; font-weight: 700; font-size: 12.5px; }
        .biaya-row.info-row-persen { background: #fffbeb; }
        .biaya-row.info-row-persen .label { color: #92400e; font-size: 10.5px; font-style: italic; }
        .biaya-row.info-row-persen .value { color: #D97706; font-size: 10.5px; font-weight: 700; }

        /* ── BUKTI FOTO ── */
        .bukti-section { margin-bottom: 14px; }
        .bukti-section .bukti-title {
            font-size: 11px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.8px; color: #D97706; margin-bottom: 7px;
        }
        .bukti-img {
            max-height: 120px; max-width: 200px;
            border-radius: 8px; border: 1px solid #dde3ea;
            object-fit: cover;
        }

        /* ── CATATAN ── */
        .catatan-box {
            border: 1px solid #dde3ea; border-radius: 6px;
            padding: 10px 12px; margin-bottom: 20px; background: #fffbeb;
            border-color: #FDE68A;
        }
        .catatan-box .catatan-title {
            font-size: 10px; font-weight: 700; color: #D97706;
            text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 5px;
        }
        .catatan-box p { font-size: 11px; color: #555; line-height: 1.6; }

        /* ── NOTICE BOX ── */
        .notice-box {
            border: 1px solid #FDE68A; border-radius: 6px;
            padding: 10px 14px; margin-bottom: 16px;
            background: #fffbeb; display: flex; align-items: flex-start; gap: 8px;
        }
        .notice-box .notice-icon { font-size: 16px; flex-shrink: 0; margin-top: 1px; }
        .notice-box p { font-size: 11px; color: #92400e; line-height: 1.7; }
        .notice-box strong { color: #D97706; }

        /* ── TTD ── */
        .ttd-section {
            display: flex; justify-content: space-between;
            align-items: flex-end; margin-top: 10px;
        }
        .ttd-box { text-align: center; width: 180px; }
        .ttd-box .ttd-label { font-size: 11px; color: #555; margin-bottom: 50px; }
        .ttd-box .ttd-name {
            border-top: 1.5px solid #1a1a1a; padding-top: 5px;
            font-size: 11.5px; font-weight: 700; color: #1a1a1a;
        }
        .ttd-box .ttd-jabatan { font-size: 10px; color: #777; margin-top: 2px; }
        .ttd-logo { text-align: center; }
        .ttd-logo img {
            height: 55px; object-fit: contain;
            opacity: 0.12;
        }

        /* ── FOOTER ── */
        .page-footer {
            margin-top: auto; padding-top: 10px;
            border-top: 1.5px solid #D97706; text-align: center;
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
                   background:#D97706; color:#fff; border:none; border-radius:8px;
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

    $jumlah       = (int)   ($pembelian->jumlah       ?? 0);
    $hargaBuyback = (float) ($pembelian->harga_satuan ?? 0);
    $total        = (float) ($pembelian->total        ?? ($jumlah * $hargaBuyback));
    $hargaAsli    = round($hargaBuyback / 0.5); // balik hitung: buyback = 50% harga asli
    $kondisi      = $pembelian->kondisi_barang ?? 'bekas';

    /* ── Terbilang ── */
    function terbilangBB(int $n): string {
        if ($n < 0) return 'minus ' . terbilangBB(-$n);
        $satuan = ['','satu','dua','tiga','empat','lima','enam','tujuh','delapan','sembilan','sepuluh','sebelas'];
        if ($n < 12)   return $satuan[$n];
        if ($n < 20)   return terbilangBB($n - 10) . ' belas';
        if ($n < 100)  return terbilangBB((int)($n / 10)) . ' puluh' . ($n % 10 ? ' ' . terbilangBB($n % 10) : '');
        if ($n < 200)  return 'seratus' . ($n - 100 ? ' ' . terbilangBB($n - 100) : '');
        if ($n < 1000) return terbilangBB((int)($n / 100)) . ' ratus' . ($n % 100 ? ' ' . terbilangBB($n % 100) : '');
        if ($n < 2000) return 'seribu' . ($n - 1000 ? ' ' . terbilangBB($n - 1000) : '');
        if ($n < 1_000_000)     return terbilangBB((int)($n / 1000)) . ' ribu' . ($n % 1000 ? ' ' . terbilangBB($n % 1000) : '');
        if ($n < 1_000_000_000) return terbilangBB((int)($n / 1_000_000)) . ' juta' . ($n % 1_000_000 ? ' ' . terbilangBB($n % 1_000_000) : '');
        return terbilangBB((int)($n / 1_000_000_000)) . ' miliar' . ($n % 1_000_000_000 ? ' ' . terbilangBB($n % 1_000_000_000) : '');
    }
    $terbilangStr = ucfirst(terbilangBB((int)$total)) . ' Rupiah';
@endphp

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

        {{-- ── JUDUL ── --}}
        <div class="invoice-title-bar">
            <h2>Bukti Pembelian Kembali (Buy Back)</h2>
            <span class="status-badge-print">🔄 BUY BACK</span>
        </div>

        {{-- ── INFO BOXES ── --}}
        <div class="info-section">

            {{-- Box 1: Info Dokumen --}}
            <div class="info-box">
                <div class="info-title">📋 Informasi Dokumen</div>
                <div class="info-row">
                    <span class="info-label">No. Buy Back</span>
                    <span class="info-value">: INV-BB-{{ str_pad($pembelian->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal</span>
                    <span class="info-value">:
                        {{ $pembelian->tanggal_pembelian
                            ? \Carbon\Carbon::parse($pembelian->tanggal_pembelian)->locale('id')->translatedFormat('d F Y')
                            : '-' }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Kondisi Barang</span>
                    <span class="info-value">: {{ ucfirst($kondisi) }}</span>
                </div>
                @if($pembelian->penjualan_id)
                <div class="info-row">
                    <span class="info-label">Ref. Penjualan</span>
                    <span class="info-value" style="color:#D97706;">
                        : INV-JL-{{ str_pad($pembelian->penjualan_id, 5, '0', STR_PAD_LEFT) }}
                    </span>
                </div>
                @endif
                <div class="info-row">
                    <span class="info-label">Dicetak</span>
                    <span class="info-value">: {{ $now->translatedFormat('d F Y') }}</span>
                </div>
            </div>

            {{-- Box 2: Data Penjual (Pelanggan) --}}
            <div class="info-box">
                <div class="info-title">👤 Data Penjual</div>
                <div class="info-row">
                    <span class="info-label">Nama</span>
                    <span class="info-value">: {{ $pembelian->nama_pelanggan ?: '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Peran</span>
                    <span class="info-value" style="color:#D97706; font-style:italic;">
                        : Penjual (menjual kembali ke Paralkes)
                    </span>
                </div>
                <div class="info-row" style="margin-top:8px;">
                    <span class="info-label">Paralkes sebagai</span>
                    <span class="info-value" style="color:#15803d;">: Pembeli</span>
                </div>
            </div>

            {{-- Box 3: Info Pembayaran --}}
            <div class="info-box" style="border-color:#FDE68A; background:#fffbeb;">
                <div class="info-title">💰 Info Pembayaran</div>
                <div class="info-row">
                    <span class="info-label">Harga Jual Asli</span>
                    <span class="info-value">
                        : Rp {{ number_format($hargaAsli, 0, ',', '.') }} / unit
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Harga Buy Back</span>
                    <span class="info-value" style="color:#D97706;">
                        : Rp {{ number_format($hargaBuyback, 0, ',', '.') }} / unit
                        <span style="font-size:10px; font-weight:400; color:#9ca3af;">(50%)</span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Qty</span>
                    <span class="info-value">: {{ number_format($jumlah) }} unit</span>
                </div>
                <div class="info-row" style="margin-top:4px; border-top:1px dashed #FDE68A; padding-top:6px;">
                    <span class="info-label" style="font-weight:700; color:#92400e;">Total Dibayarkan</span>
                    <span class="info-value" style="color:#D97706; font-size:12px;">
                        : Rp {{ number_format($total, 0, ',', '.') }}
                    </span>
                </div>
            </div>

        </div>

        {{-- ── DETAIL BARANG ── --}}
        <div class="section-title">Detail Barang Buy Back</div>
        <table class="detail-table">
            <thead>
                <tr>
                    <th class="center" style="width:32px;">No</th>
                    <th>Nama Barang</th>
                    <th class="center" style="width:70px;">Kondisi</th>
                    <th class="center" style="width:50px;">Qty</th>
                    <th class="right" style="width:130px;">Harga Jual Asli</th>
                    <th class="right" style="width:130px;">Harga Buy Back (50%)</th>
                    <th class="right" style="width:120px;">Total Dibayar</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="center">1</td>
                    <td>
                        <div style="font-weight:600; font-size:12.5px;">{{ $pembelian->nama_barang }}</div>
                        @if($pembelian->keterangan)
                        <div style="font-size:10.5px; color:#9ca3af; margin-top:2px; font-style:italic;">
                            {{ $pembelian->keterangan }}
                        </div>
                        @endif
                    </td>
                    <td class="center">
                        <span class="badge-kondisi badge-{{ $kondisi }}">
                            {{ ucfirst($kondisi) }}
                        </span>
                    </td>
                    <td class="center" style="font-weight:700;">{{ number_format($jumlah) }}</td>
                    <td class="right" style="color:#9ca3af; text-decoration:line-through; font-size:11px;">
                        Rp {{ number_format($hargaAsli, 0, ',', '.') }}
                    </td>
                    <td class="right" style="color:#D97706; font-weight:700;">
                        Rp {{ number_format($hargaBuyback, 0, ',', '.') }}
                    </td>
                    <td class="right" style="font-weight:800; font-size:13px;">
                        Rp {{ number_format($total, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- ── BIAYA + TERBILANG ── --}}
        <div class="biaya-section">

            {{-- Kiri: Terbilang --}}
            <div class="terbilang-wrap">
                <table class="terbilang-table">
                    <tr><td class="tb-title">Terbilang — Total Dibayarkan ke Penjual</td></tr>
                    <tr><td class="tb-value">{{ $terbilangStr }}</td></tr>
                    <tr>
                        <td class="tb-note">
                            Harga buy back = 50% dari harga jual asli
                            (Rp {{ number_format($hargaAsli, 0, ',', '.') }} → Rp {{ number_format($hargaBuyback, 0, ',', '.') }})
                        </td>
                    </tr>
                </table>
            </div>

            {{-- Kanan: Biaya Box --}}
            <div class="biaya-box">
                <div class="biaya-row">
                    <span class="label">Harga Jual Asli / unit</span>
                    <span class="value" style="color:#9ca3af; text-decoration:line-through;">
                        Rp {{ number_format($hargaAsli, 0, ',', '.') }}
                    </span>
                </div>
                <div class="biaya-row info-row-persen">
                    <span class="label">Persentase Buy Back</span>
                    <span class="value">50% dari harga asli</span>
                </div>
                <div class="biaya-row">
                    <span class="label">Harga Buy Back / unit</span>
                    <span class="value" style="color:#D97706;">
                        Rp {{ number_format($hargaBuyback, 0, ',', '.') }}
                    </span>
                </div>
                <div class="biaya-row">
                    <span class="label">Jumlah Barang</span>
                    <span class="value">{{ number_format($jumlah) }} unit</span>
                </div>
                <div class="biaya-row total-row">
                    <span class="label">Total Dibayarkan</span>
                    <span class="value">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- ── BUKTI FOTO (jika ada) ── --}}
        @if($pembelian->bukti_transaksi)
        <div class="bukti-section">
            <div class="bukti-title">📷 Bukti Transaksi</div>
            <img src="{{ asset('storage/' . $pembelian->bukti_transaksi) }}"
                 alt="Bukti Transaksi"
                 class="bukti-img">
        </div>
        @endif

        {{-- ── NOTICE PENTING ── --}}
        <div class="notice-box">
            <span class="notice-icon">⚠️</span>
            <p>
                <strong>Ketentuan Buy Back:</strong>
                Barang yang telah dibeli kembali oleh Paralkes menjadi <strong>milik Paralkes sepenuhnya</strong>.
                Harga buy back ditetapkan sebesar <strong>50% dari harga jual asli</strong>.
                Transaksi ini bersifat <strong>final</strong> setelah penandatanganan dokumen.
                Kondisi barang yang diterima adalah <strong>{{ ucfirst($kondisi) }}</strong>.
            </p>
        </div>

        {{-- ── CATATAN ── --}}
        @if($pembelian->keterangan)
        <div class="catatan-box">
            <div class="catatan-title">📝 Catatan</div>
            <p>{{ $pembelian->keterangan }}</p>
        </div>
        @endif

        {{-- ── TTD ── --}}
        <div class="ttd-section">
            <div class="ttd-box">
                <div class="ttd-label">Pembeli (Paralkes),</div>
                <div class="ttd-name">Paralkes</div>
                <div class="ttd-jabatan">Admin / Pengelola</div>
            </div>
            <div class="ttd-logo">
                <img src="{{ asset('images/logo-paralkes-white.png') }}" alt="Logo Paralkes">
            </div>
            <div class="ttd-box">
                <div class="ttd-label">Penjual,</div>
                <div class="ttd-name">{{ $pembelian->nama_pelanggan ?: '____________________' }}</div>
                <div class="ttd-jabatan">Penjual Barang</div>
            </div>
        </div>

    </div>{{-- end .page-content --}}

    <div class="page-footer">
        Dokumen ini digenerate secara otomatis oleh sistem Paralkes
        pada {{ $now->translatedFormat('d F Y, H:i') }} WIB<br>
        Terima kasih atas kepercayaan Anda kepada Paralkes.
    </div>
</div>

<script>
    // window.addEventListener('load', () => window.print());
</script>
</body>
</html>