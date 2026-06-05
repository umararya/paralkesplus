{{-- resources/views/admin/penyewaan/cetak/invoice.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Penyewaan - {{ $penyewaan->nama_penyewa }}</title>
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
            border-bottom: 3px solid #1D6FA4;
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
            background: #1D6FA4; color: #fff;
            text-align: center; padding: 7px 0;
            border-radius: 4px; margin-bottom: 14px;
        }
        .invoice-title-bar h2 {
            font-size: 15px; font-weight: 700;
            letter-spacing: 2px; text-transform: uppercase;
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
            letter-spacing: 0.8px; color: #1D6FA4;
            border-bottom: 1px solid #dde3ea; padding-bottom: 5px; margin-bottom: 8px;
        }
        .info-row { display: flex; gap: 6px; margin-bottom: 4px; line-height: 1.5; }
        .info-label { color: #666; min-width: 120px; font-size: 11px; }
        .info-value { font-weight: 600; color: #1a1a1a; font-size: 11px; }

        /* TABEL DETAIL */
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
            background: #1D6FA4; color: #fff; font-size: 10.5px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.8px;
            padding: 7px 12px; text-align: center; display: block;
        }
        .terbilang-table .tb-value {
            padding: 10px 12px; font-size: 11.5px; color: #1a1a1a;
            font-weight: 600; line-height: 1.6; font-style: italic; text-align: center;
            display: block;
        }
        .biaya-box {
            width: 300px; flex-shrink: 0;
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
        .biaya-row.total-row { background: #1D6FA4; color: #fff; }
        .biaya-row.total-row .label,
        .biaya-row.total-row .value { color: #fff; font-weight: 700; font-size: 12.5px; }

        /* CATATAN */
        .catatan-box {
            border: 1px solid #dde3ea; border-radius: 6px;
            padding: 10px 12px; margin-bottom: 20px; background: #f8fafc;
        }
        .catatan-box .catatan-title {
            font-size: 10px; font-weight: 700; color: #1D6FA4;
            text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 5px;
        }
        .catatan-box p { font-size: 11px; color: #555; line-height: 1.6; }

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

    $useDetail  = $penyewaan->has_detail;
    $details    = $useDetail ? $penyewaan->details : collect();
    $legacyList = (!$useDetail && $penyewaan->produk_alkes)
                    ? collect(explode(',', $penyewaan->produk_alkes))->map(fn($p) => trim($p))
                    : collect();

    if ($useDetail) {
        $subtotalSewa = $details->sum(function($d) {
            $sub = $d->qty * $d->harga_satuan;
            if (($d->diskon ?? 0) > 0) $sub = $sub * (1 - $d->diskon / 100);
            return round($sub);
        });
        $diskonGlobal = (int)($penyewaan->diskon_global  ?? 0);
        $biayaOngkir  = (int)($penyewaan->biaya_ongkir   ?? 0);
        $totalTagihan = max(0, $subtotalSewa - $diskonGlobal + $biayaOngkir);
    } else {
        $subtotalSewa = 0;
        $diskonGlobal = 0;
        $biayaOngkir  = (int)($penyewaan->biaya_ongkir ?? 0);
        $totalTagihan = $biayaOngkir;
    }

    function terbilangSewa(int $n): string {
        if ($n < 0) return 'minus ' . terbilangSewa(-$n);
        $satuan = ['','satu','dua','tiga','empat','lima','enam','tujuh','delapan','sembilan','sepuluh','sebelas'];
        if ($n < 12)   return $satuan[$n];
        if ($n < 20)   return terbilangSewa($n - 10) . ' belas';
        if ($n < 100)  return terbilangSewa((int)($n / 10)) . ' puluh' . ($n % 10 ? ' ' . terbilangSewa($n % 10) : '');
        if ($n < 200)  return 'seratus' . ($n - 100 ? ' ' . terbilangSewa($n - 100) : '');
        if ($n < 1000) return terbilangSewa((int)($n / 100)) . ' ratus' . ($n % 100 ? ' ' . terbilangSewa($n % 100) : '');
        if ($n < 2000) return 'seribu' . ($n - 1000 ? ' ' . terbilangSewa($n - 1000) : '');
        if ($n < 1_000_000)    return terbilangSewa((int)($n / 1000)) . ' ribu' . ($n % 1000 ? ' ' . terbilangSewa($n % 1000) : '');
        if ($n < 1_000_000_000) return terbilangSewa((int)($n / 1_000_000)) . ' juta' . ($n % 1_000_000 ? ' ' . terbilangSewa($n % 1_000_000) : '');
        return terbilangSewa((int)($n / 1_000_000_000)) . ' miliar' . ($n % 1_000_000_000 ? ' ' . terbilangSewa($n % 1_000_000_000) : '');
    }
    $terbilangStr = ucfirst(terbilangSewa($totalTagihan)) . ' Rupiah';
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
            <h2>Invoice Penyewaan Alat Kesehatan</h2>
        </div>

        {{-- INFO --}}
        <div class="info-section">
            <div class="info-box">
                <div class="info-title">📋 Informasi Invoice</div>
                <div class="info-row">
                    <span class="info-label">No. Invoice</span>
                    <span class="info-value">: INV-{{ str_pad($penyewaan->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tgl Mulai Sewa</span>
                    <span class="info-value">:
                        {{ $penyewaan->tgl_mulai
                            ? $penyewaan->tgl_mulai->locale('id')->translatedFormat('d F Y')
                            : '-' }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tgl Selesai Sewa</span>
                    <span class="info-value">:
                        {{ $penyewaan->tgl_selesai
                            ? $penyewaan->tgl_selesai->locale('id')->translatedFormat('d F Y')
                            : '-' }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Durasi Sewa</span>
                    <span class="info-value">: {{ $penyewaan->durasi_hari }} Hari</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Metode Bayar</span>
                    <span class="info-value">: {{ ucfirst($penyewaan->metode_pembayaran) }}</span>
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

        {{-- DETAIL --}}
        <div class="section-title">Detail Penyewaan</div>

        @if($useDetail)
        <table class="detail-table">
            <thead>
                <tr>
                    <th class="center" style="width:28px;">No</th>
                    <th>Nama Alat Kesehatan</th>
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
                @endphp
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $item->nama_alat }}</td>
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
                    <td colspan="7" class="center"
                        style="color:#aaa; font-style:italic; padding:14px 0;">
                        Tidak ada item detail tercatat.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @else
        <table class="detail-table">
            <thead>
                <tr>
                    <th class="center" style="width:30px;">No</th>
                    <th>Nama Alat Kesehatan</th>
                    <th class="center">Tgl Mulai</th>
                    <th class="center">Tgl Selesai</th>
                    <th class="center">Durasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($legacyList as $i => $produk)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $produk }}</td>
                    @if($i === 0)
                    <td class="center" rowspan="{{ count($legacyList) }}">
                        {{ $penyewaan->tgl_mulai ? $penyewaan->tgl_mulai->locale('id')->translatedFormat('d F Y') : '-' }}
                    </td>
                    <td class="center" rowspan="{{ count($legacyList) }}">
                        {{ $penyewaan->tgl_selesai ? $penyewaan->tgl_selesai->locale('id')->translatedFormat('d F Y') : '-' }}
                    </td>
                    <td class="center" rowspan="{{ count($legacyList) }}">{{ $penyewaan->durasi_hari }} Hari</td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        {{-- RINGKASAN + TERBILANG --}}
        <div class="biaya-section">

            <div class="terbilang-wrap">
                <table class="terbilang-table">
                    <tr><td class="tb-title">Terbilang</td></tr>
                    <tr><td class="tb-value">{{ $terbilangStr }}</td></tr>
                </table>
            </div>

            <div class="biaya-box">
                @if($useDetail)
                    <div class="biaya-row">
                        <span class="label">Subtotal Sewa</span>
                        <span class="value">Rp {{ number_format($subtotalSewa, 0, ',', '.') }}</span>
                    </div>
                    @if($diskonGlobal > 0)
                    <div class="biaya-row diskon-row">
                        <span class="label">Diskon</span>
                        <span class="value">– Rp {{ number_format($diskonGlobal, 0, ',', '.') }}</span>
                    </div>
                    @endif
                @endif
                <div class="biaya-row">
                    <span class="label">Ongkos Kirim</span>
                    <span class="value">
                        {{ $biayaOngkir > 0 ? 'Rp ' . number_format($biayaOngkir, 0, ',', '.') : 'Gratis' }}
                    </span>
                </div>
                <div class="biaya-row total-row">
                    <span class="label">Total Tagihan</span>
                    <span class="value">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- CATATAN --}}
        <div class="catatan-box">
            <div class="catatan-title">📝 Catatan</div>
            <p>{{ $penyewaan->keterangan ?: 'Tidak ada catatan tambahan.' }}</p>
            <p style="margin-top:6px; color:#888;">
                ※ Harap alat kesehatan dikembalikan dalam kondisi baik dan bersih sesuai tanggal selesai
                yang tertera. Keterlambatan pengembalian akan dikenakan biaya tambahan.
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