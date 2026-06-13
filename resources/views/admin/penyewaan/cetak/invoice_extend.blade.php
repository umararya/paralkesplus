{{-- resources/views/admin/penyewaan/cetak/invoice_extend.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Perpanjangan - {{ $penyewaan->nama_penyewa }}</title>
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
        }
        .page-content { flex: 1; }

        /* KOP */
        .kop {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid #F59E0B;
            padding-bottom: 10px;
            margin-bottom: 14px;
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

        /* JUDUL */
        .invoice-title-bar {
            background: #F59E0B; color: #fff;
            text-align: center; padding: 7px 0;
            border-radius: 4px; margin-bottom: 6px;
        }
        .invoice-title-bar h2 {
            font-size: 15px; font-weight: 700;
            letter-spacing: 2px; text-transform: uppercase;
        }

        /* BADGE PERPANJANGAN */
        .extend-badge {
            text-align: center; margin-bottom: 14px;
        }
        .extend-badge span {
            display: inline-block;
            background: #FEF3C7; color: #92400E;
            border: 1.5px solid #F59E0B;
            border-radius: 6px; padding: 4px 18px;
            font-size: 11px; font-weight: 700;
            letter-spacing: 0.5px;
        }

        /* INFO */
        .info-section {
            display: flex; justify-content: space-between;
            gap: 20px; margin-bottom: 16px;
        }
        .info-box {
            flex: 1; border: 1px solid #dde3ea;
            border-radius: 6px; padding: 10px 12px;
        }
        .info-box .info-title {
            font-size: 10px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.8px; color: #F59E0B;
            border-bottom: 1px solid #dde3ea; padding-bottom: 5px; margin-bottom: 8px;
        }
        .info-row { display: flex; gap: 6px; margin-bottom: 4px; line-height: 1.5; }
        .info-label { color: #666; min-width: 130px; font-size: 11px; }
        .info-value { font-weight: 600; color: #1a1a1a; font-size: 11px; }
        .info-value.highlight { color: #D97706; }

        /* TABEL DETAIL EXTEND */
        .section-title {
            font-size: 11px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.8px; color: #F59E0B; margin-bottom: 7px;
        }
        .detail-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .detail-table thead tr { background: #F59E0B; color: #fff; }
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
        .detail-table tbody tr:nth-child(even) td { background: #fffbf0; }

        /* RINGKASAN + TERBILANG */
        .biaya-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 16px;
        }
        .terbilang-wrap { flex: 1; }
        .terbilang-table {
            width: 100%; border-collapse: collapse;
            border: 1px solid #dde3ea; border-radius: 6px; overflow: hidden;
        }
        .terbilang-table .tb-title {
            background: #F59E0B; color: #fff; font-size: 10.5px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.8px;
            padding: 7px 12px; text-align: center; display: block;
        }
        .terbilang-table .tb-value {
            padding: 10px 12px; font-size: 11.5px; color: #1a1a1a;
            font-weight: 600; line-height: 1.6; font-style: italic; text-align: center;
            display: block;
        }
        .biaya-box {
            width: 280px; flex-shrink: 0;
            border: 1px solid #dde3ea; border-radius: 6px; overflow: hidden;
        }
        .biaya-row {
            display: flex; justify-content: space-between;
            padding: 7px 12px; font-size: 11.5px; border-bottom: 1px solid #eef0f3;
        }
        .biaya-row:last-child { border-bottom: none; }
        .biaya-row .label { color: #555; }
        .biaya-row .value { font-weight: 600; }
        .biaya-row.total-row { background: #F59E0B; color: #fff; }
        .biaya-row.total-row .label,
        .biaya-row.total-row .value { color: #fff; font-weight: 700; font-size: 12.5px; }

        /* CATATAN */
        .catatan-box {
            border: 1px solid #dde3ea; border-radius: 6px;
            padding: 10px 12px; margin-bottom: 20px; background: #fffbf0;
        }
        .catatan-box .catatan-title {
            font-size: 10px; font-weight: 700; color: #D97706;
            text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 5px;
        }
        .catatan-box p { font-size: 11px; color: #555; line-height: 1.6; }

        /* BUKTI TRANSFER */
        .bukti-box {
            border: 1px solid #dde3ea; border-radius: 6px;
            padding: 10px 12px; margin-bottom: 16px; background: #f8fafc;
        }
        .bukti-box .bukti-title {
            font-size: 10px; font-weight: 700; color: #1D6FA4;
            text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 6px;
        }
        .bukti-box img {
            max-height: 100px; max-width: 200px;
            border-radius: 4px; border: 1px solid #dde3ea;
            object-fit: contain;
        }

        /* TTD */
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
        .ttd-box .ttd-space { height: 60px; }
        .ttd-box .ttd-name {
            border-top: 1.5px solid #1a1a1a; padding-top: 5px;
            font-size: 11.5px; font-weight: 700; color: #1a1a1a;
        }
        .ttd-box .ttd-jabatan { font-size: 10px; color: #777; margin-top: 2px; }
        .ttd-logo { text-align: center; }
        .ttd-logo img {
            height: 55px; object-fit: contain;
            filter: invert(1) sepia(1) saturate(2) hue-rotate(180deg); opacity: 0.15;
        }

        /* FOOTER */
        .page-footer {
            margin-top: auto; padding-top: 10px;
            border-top: 1.5px solid #F59E0B; text-align: center;
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
                   background:#F59E0B; color:#fff; border:none; border-radius:8px;
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

    // Terbilang helper
    function terbilangExtend(int $n): string {
        if ($n < 0) return 'minus ' . terbilangExtend(-$n);
        $satuan = ['','satu','dua','tiga','empat','lima','enam','tujuh','delapan','sembilan','sepuluh','sebelas'];
        if ($n < 12)    return $satuan[$n];
        if ($n < 20)    return terbilangExtend($n - 10) . ' belas';
        if ($n < 100)   return terbilangExtend((int)($n / 10)) . ' puluh' . ($n % 10 ? ' ' . terbilangExtend($n % 10) : '');
        if ($n < 200)   return 'seratus' . ($n - 100 ? ' ' . terbilangExtend($n - 100) : '');
        if ($n < 1000)  return terbilangExtend((int)($n / 100)) . ' ratus' . ($n % 100 ? ' ' . terbilangExtend($n % 100) : '');
        if ($n < 2000)  return 'seribu' . ($n - 1000 ? ' ' . terbilangExtend($n - 1000) : '');
        if ($n < 1_000_000)     return terbilangExtend((int)($n / 1000)) . ' ribu' . ($n % 1000 ? ' ' . terbilangExtend($n % 1000) : '');
        if ($n < 1_000_000_000) return terbilangExtend((int)($n / 1_000_000)) . ' juta' . ($n % 1_000_000 ? ' ' . terbilangExtend($n % 1_000_000) : '');
        return terbilangExtend((int)($n / 1_000_000_000)) . ' miliar' . ($n % 1_000_000_000 ? ' ' . terbilangExtend($n % 1_000_000_000) : '');
    }

    $harga        = (int) $extend->harga_extend;
    $terbilangStr = ucfirst(terbilangExtend($harga)) . ' Rupiah';

    $details    = $penyewaan->details;
    $useDetail  = $penyewaan->has_detail;
    $legacyList = (!$useDetail && $penyewaan->produk_alkes)
                    ? collect(explode(',', $penyewaan->produk_alkes))->map(fn($p) => trim($p))
                    : collect();
@endphp

<div class="page-wrapper">
    <div class="page-content">

        {{-- KOP --}}
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

        {{-- JUDUL --}}
        <div class="invoice-title-bar">
            <h2>Invoice Perpanjangan Sewa Alat Kesehatan</h2>
        </div>
        <div class="extend-badge">
            <span>📋 Dokumen Perpanjangan · Nomor: {{ $extend->nomor_extend }}</span>
        </div>

        {{-- INFO --}}
        <div class="info-section">
            <div class="info-box">
                <div class="info-title">📋 Informasi Perpanjangan</div>
                <div class="info-row">
                    <span class="info-label">No. Extend</span>
                    <span class="info-value">: {{ $extend->nomor_extend }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">No. Invoice Asal</span>
                    <span class="info-value">: INV-{{ str_pad($penyewaan->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Deadline Sebelumnya</span>
                    <span class="info-value">:
                        {{ $extend->tgl_selesai_lama->locale('id')->translatedFormat('d F Y') }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Deadline Baru</span>
                    <span class="info-value highlight">:
                        {{ $extend->tgl_selesai_baru->locale('id')->translatedFormat('d F Y') }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tambah Durasi</span>
                    <span class="info-value highlight">: {{ $extend->tambah_hari }} Hari</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Metode Bayar</span>
                    <span class="info-value">: {{ ucfirst($extend->metode_bayar) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal Extend</span>
                    <span class="info-value">:
                        {{ $extend->created_at->locale('id')->translatedFormat('d F Y') }}
                    </span>
                </div>
            </div>

            <div class="info-box">
                <div class="info-title">👤 Data Penyewa</div>
                <div class="info-row">
                    <span class="info-label">Nama Lengkap</span>
                    <span class="info-value">: {{ $penyewaan->nama_penyewa }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">No. Telepon</span>
                    <span class="info-value">: {{ $penyewaan->nomor_telepon }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Alamat</span>
                    <span class="info-value">: {{ $penyewaan->alamat_penyewa }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Metode Pengiriman</span>
                    <span class="info-value">: {{ $penyewaan->pengiriman_label ?? $penyewaan->pengiriman }}</span>
                </div>
            </div>
        </div>

        {{-- DETAIL ALAT --}}
        <div class="section-title">Alat Kesehatan yang Diperpanjang</div>

        @if($useDetail && $details->isNotEmpty())
        <table class="detail-table">
            <thead>
                <tr>
                    <th class="center" style="width:28px;">No</th>
                    <th>Nama Alat Kesehatan</th>
                    <th class="center" style="width:42px;">Qty</th>
                    <th class="center" style="width:48px;">Satuan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($details as $i => $item)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $item->nama_alat }}</td>
                    <td class="center">{{ $item->qty }}</td>
                    <td class="center">{{ $item->satuan }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <table class="detail-table">
            <thead>
                <tr>
                    <th class="center" style="width:30px;">No</th>
                    <th>Nama Alat Kesehatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($legacyList as $i => $produk)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $produk }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        {{-- RINGKASAN BIAYA --}}
        <div class="biaya-section">
            <div class="terbilang-wrap">
                <table class="terbilang-table">
                    <tr><td class="tb-title">Terbilang</td></tr>
                    <tr><td class="tb-value">{{ $terbilangStr }}</td></tr>
                </table>
            </div>
            <div class="biaya-box">
                <div class="biaya-row">
                    <span class="label">Tambah Durasi</span>
                    <span class="value">{{ $extend->tambah_hari }} hari</span>
                </div>
                <div class="biaya-row total-row">
                    <span class="label">Biaya Perpanjangan</span>
                    <span class="value">Rp {{ number_format($harga, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- BUKTI TRANSFER --}}
        @if($extend->bukti_transfer)
        <div class="bukti-box">
            <div class="bukti-title">📎 Bukti Transfer Perpanjangan</div>
            @php $ext = pathinfo($extend->bukti_transfer, PATHINFO_EXTENSION); @endphp
            @if(in_array(strtolower($ext), ['jpg','jpeg','png']))
                <img src="{{ asset('storage/' . $extend->bukti_transfer) }}"
                     alt="Bukti Transfer">
            @else
                <span style="font-size:11px; color:#1D6FA4;">
                    📄 File PDF terlampir (lihat di sistem)
                </span>
            @endif
        </div>
        @endif

        {{-- CATATAN --}}
        <div class="catatan-box">
            <div class="catatan-title">📝 Catatan Perpanjangan</div>
            <p>{{ $extend->catatan ?: 'Tidak ada catatan tambahan.' }}</p>
            <p style="margin-top:6px; color:#888;">
                ※ Perpanjangan ini berlaku sejak tanggal
                {{ $extend->tgl_selesai_lama->locale('id')->translatedFormat('d F Y') }}
                hingga
                {{ $extend->tgl_selesai_baru->locale('id')->translatedFormat('d F Y') }}.
                Alat kesehatan wajib dikembalikan tepat waktu sesuai deadline baru.
            </p>
        </div>

        {{-- TTD --}}
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
                <div class="ttd-label">Penyewa,</div>
                <div class="ttd-space"></div>
                <div class="ttd-name">{{ $penyewaan->nama_penyewa }}</div>
                <div class="ttd-jabatan">Penyewa</div>
            </div>
        </div>

    </div>{{-- end .page-content --}}

    <div class="page-footer">
        Dokumen perpanjangan ini digenerate otomatis oleh sistem Paralkes
        pada {{ $now->translatedFormat('d F Y, H:i') }} WIB<br>
        Invoice Asal: INV-{{ str_pad($penyewaan->id, 5, '0', STR_PAD_LEFT) }}
        &nbsp;·&nbsp; Terima kasih atas kepercayaan Anda.
    </div>
</div>

<script>
    // window.addEventListener('load', () => window.print());
</script>
</body>
</html>