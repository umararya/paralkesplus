{{-- resources/views/admin/penyewaan/cetak/perjanjian_extend.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perjanjian Perpanjangan - {{ $penyewaan->nama_penyewa }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10.5pt;
            color: #000;
            background: #f0f0f0;
            line-height: 1.45;
        }
        .page-wrapper {
            width: 210mm;
            min-height: 297mm;
            margin: 10mm auto;
            padding: 15mm 19mm 13mm 19mm;
            background: #fff;
            box-shadow: 0 2px 12px rgba(0,0,0,.15);
            display: flex;
            flex-direction: column;
        }
        .page-content { flex: 1; }

        /* KOP */
        .kop {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px double #F59E0B;
            padding-bottom: 8px;
            margin-bottom: 5px;
        }
        .kop-left img  { height: 50px; object-fit: contain; }
        .kop-center    { text-align: center; flex: 1; padding: 0 8px; }
        .kop-center .nama-toko {
            font-size: 14pt; font-weight: 800;
            color: #1D6FA4; text-transform: uppercase;
            letter-spacing: 2px; font-family: Arial, sans-serif;
        }
        .kop-center .tagline {
            font-size: 8pt; color: #555; margin-top: 1px;
            font-family: Arial, sans-serif;
        }
        .kop-center .alamat-toko {
            font-size: 7.5pt; color: #555; margin-top: 2px;
            font-family: Arial, sans-serif; line-height: 1.4;
        }
        .kop-right img { height: 50px; object-fit: contain; }

        /* JUDUL */
        .doc-title { text-align: center; margin: 8px 0 3px; }
        .doc-title h1 {
            font-size: 11.5pt; font-weight: 700;
            text-transform: uppercase; letter-spacing: 1px;
        }
        .doc-nomor {
            font-size: 10.5pt; text-align: center;
            margin-bottom: 2px; font-weight: 700;
        }
        .doc-subtitle {
            font-size: 9.5pt; text-align: center;
            margin-bottom: 6px; color: #D97706;
            font-family: Arial, sans-serif;
        }

        /* PARAGRAF */
        .pembuka {
            font-size: 10pt; text-align: justify;
            margin: 5px 0; line-height: 1.45;
        }

        /* PARA PIHAK */
        .pihak-table-wrap { margin: 4px 0 3px; }
        .pihak-outer { width: 100%; border-collapse: collapse; font-size: 10pt; }
        .pihak-outer td { padding: 1px 3px; vertical-align: top; }
        .pihak-nomor { width: 18px; font-weight: 700; vertical-align: top; }
        .pihak-data-table { width: 100%; border-collapse: collapse; font-size: 10pt; }
        .pihak-data-table td { padding: 1px 3px; vertical-align: top; }
        .pihak-data-table td:first-child  { width: 150px; }
        .pihak-data-table td:nth-child(2) { width: 10px; text-align: center; }
        .pihak-data-table td:last-child   { font-weight: 600; }
        .pihak-selanjutnya { font-size: 10pt; margin: 2px 0 5px 18px; }

        /* PASAL */
        .pasal { margin: 6px 0 5px; }
        .pasal-heading    { font-size: 10.5pt; font-weight: 700; text-align: center; margin-bottom: 0; }
        .pasal-subheading { font-size: 10.5pt; font-weight: 700; text-align: center; margin-bottom: 3px; }
        .pasal-body       { font-size: 10pt; text-align: justify; margin-bottom: 3px; line-height: 1.45; }

        /* AYAT */
        .ayat-item  { display: flex; margin-bottom: 2px; font-size: 10pt; line-height: 1.45; }
        .ayat-nomor { min-width: 24px; flex-shrink: 0; }
        .ayat-isi   { flex: 1; text-align: justify; }

        /* SUB AYAT */
        .sub-ayat-list { margin: 1px 0 0; padding: 0; list-style: none; }
        .sub-ayat-item { display: flex; margin-bottom: 1px; line-height: 1.45; }
        .sub-huruf { min-width: 24px; flex-shrink: 0; }
        .sub-isi   { flex: 1; text-align: justify; }

        /* TABEL DATA PASAL */
        .data-pasal-table { width: 100%; border-collapse: collapse; font-size: 10pt; margin: 3px 0 4px; }
        .data-pasal-table td { padding: 2px 4px; vertical-align: top; line-height: 1.4; }
        .data-pasal-table td:first-child  { width: 200px; }
        .data-pasal-table td:nth-child(2) { width: 10px; text-align: center; }
        .data-pasal-table td:last-child   { font-weight: 600; }

        /* HIGHLIGHT BOX */
        .highlight-box {
            border: 1.5px solid #F59E0B; border-radius: 4px;
            padding: 7px 12px; margin: 5px 0;
            background: #FFFBEB; font-size: 10pt;
        }

        /* TTD */
        .ttd-section    { margin-top: 14px; }
        .ttd-para-pihak { font-size: 10pt; font-weight: 700; margin-bottom: 4px; }
        .ttd-row        { display: flex; justify-content: space-between; margin-top: 4px; }
        .ttd-col        { width: 44%; text-align: center; }
        .ttd-col .ttd-label   { font-size: 10pt; font-weight: 700; margin-bottom: 6px; }
        .ttd-col .ttd-img     {
            height: 68px; object-fit: contain;
            display: block; margin: 0 auto 4px auto;
        }
        .ttd-col .ttd-space   { height: 68px; }
        .ttd-col .ttd-garis   { border-top: 1.5px solid #000; padding-top: 4px; font-size: 10pt; font-weight: 700; }
        .ttd-col .ttd-jabatan { font-size: 9pt; margin-top: 1px; }
        .ttd-logo-center {
            display: flex; align-items: flex-end; justify-content: center;
            padding-bottom: 8px;
        }
        .ttd-logo-center img {
            height: 52px; object-fit: contain;
            opacity: 0.10; filter: grayscale(1);
        }

        /* NO BREAK */
        .no-break { page-break-inside: avoid; break-inside: avoid; }

        /* FOOTER */
        .page-footer {
            border-top: 1px solid #ccc;
            padding-top: 3px;
            font-size: 7pt;
            color: #999;
            text-align: center;
            font-family: Arial, sans-serif;
            margin-top: auto;
        }

        @media print {
            * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            html, body { background: #fff !important; width: 210mm; }
            .no-print { display: none !important; }
            @page { size: 210mm 297mm portrait; margin: 15mm 19mm 13mm 19mm; }
            .page-wrapper {
                width: 100% !important; min-height: auto !important;
                padding: 0 !important; margin: 0 !important;
                box-shadow: none !important;
            }
        }
    </style>
</head>
<body>

{{-- TOMBOL CETAK --}}
<div class="no-print"
     style="position:fixed;top:16px;right:16px;z-index:999;display:flex;gap:8px;">
    <button onclick="window.print()"
            style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;
                   background:#F59E0B;color:#fff;border:none;border-radius:8px;
                   font-size:13px;font-weight:600;cursor:pointer;font-family:Arial,sans-serif;">
        🖨️ Cetak / Simpan PDF
    </button>
    <button onclick="window.close()"
            style="display:inline-flex;align-items:center;gap:6px;padding:9px 14px;
                   background:#f1f5f9;color:#475569;border:1px solid #cbd5e1;
                   border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;
                   font-family:Arial,sans-serif;">
        ✕ Tutup
    </button>
</div>

@php
    App::setLocale('id');
    \Carbon\Carbon::setLocale('id');

    $now               = now('Asia/Jakarta');
    $nomorPerjanjian   = str_pad($penyewaan->id, 3, '0', STR_PAD_LEFT)
                         . '/PAR/EXT/' . $now->format('Y')
                         . '/' . str_pad($extend->id, 3, '0', STR_PAD_LEFT);
    $tglBuatHari       = $now->translatedFormat('l');
    $tglBuatLabel      = $now->translatedFormat('d F Y');
    $tglLamaLabel      = strtoupper($extend->tgl_selesai_lama->locale('id')->translatedFormat('d F Y'));
    $tglBaruLabel      = strtoupper($extend->tgl_selesai_baru->locale('id')->translatedFormat('d F Y'));
    $produkList        = [];

    if ($penyewaan->has_detail && $penyewaan->details->isNotEmpty()) {
        $produkList = $penyewaan->details->map(fn($d) => $d->nama_alat . ' (' . $d->qty . ' ' . $d->satuan . ')')->toArray();
    } elseif ($penyewaan->produk_alkes) {
        $produkList = array_map('trim', explode(',', $penyewaan->produk_alkes));
    }
@endphp

<div class="page-wrapper">
    <div class="page-content">

        {{-- KOP --}}
        <div class="kop">
            <div class="kop-left">
                <img src="{{ asset('images/logo-cop-paralkesplus2.png') }}" alt="Logo">
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
                <img src="{{ asset('images/logo-cop-paralkesplus3.png') }}" alt="Logo">
            </div>
        </div>

        {{-- JUDUL --}}
        <div class="doc-title"><h1>Addendum Perjanjian Sewa Alat Kesehatan</h1></div>
        <div class="doc-nomor">Nomor: {{ $nomorPerjanjian }}</div>
        <div class="doc-subtitle">(Perpanjangan Masa Sewa)</div>

        <div class="pembuka">
            <strong>ADDENDUM PERJANJIAN SEWA ALAT KESEHATAN Nomor: {{ $nomorPerjanjian }}</strong>
            ini merupakan bagian tidak terpisahkan dari Perjanjian Sewa Alat Kesehatan asal
            Nomor: <strong>{{ str_pad($penyewaan->id, 3, '0', STR_PAD_LEFT) }}/PAR/SR/{{ \Carbon\Carbon::parse($penyewaan->tgl_mulai ?? now())->format('Y') }}</strong>.
            Dibuat dan ditandatangani di Semarang pada hari
            (<u><strong>{{ $tglBuatHari }}</strong></u>)
            tanggal <u><strong>{{ $tglBuatLabel }}</strong></u>, oleh dan di antara:
        </div>

        {{-- PIHAK PERTAMA --}}
        <div class="pihak-table-wrap no-break">
            <table class="pihak-outer"><tr>
                <td class="pihak-nomor">1</td>
                <td><table class="pihak-data-table">
                    <tr><td>Nama</td><td>:</td><td><strong>ADAM PARAKITRI (SELAKU PEMILIK USAHA)</strong></td></tr>
                    <tr><td>Alamat</td><td>:</td><td>JL. SEKAYU BARU 3/393 SEMARANG JAWA TENGAH</td></tr>
                </table></td>
            </tr></table>
            <div class="pihak-selanjutnya">Selanjutnya disebut sebagai "<strong>Pihak Pertama</strong>".</div>
        </div>

        {{-- PIHAK KEDUA --}}
        <div class="pihak-table-wrap no-break">
            <table class="pihak-outer"><tr>
                <td class="pihak-nomor">2</td>
                <td><table class="pihak-data-table">
                    <tr><td>Nama</td><td>:</td><td><strong>{{ strtoupper($penyewaan->nama_penyewa) }}</strong></td></tr>
                    <tr><td>Alamat</td><td>:</td><td>{{ strtoupper($penyewaan->alamat_penyewa) }}</td></tr>
                    <tr>
                        <td>Nomor KTP &amp; HP</td><td>:</td>
                        <td>
                            {{ $penyewaan->nomor_ktp ?? '—' }}
                            &nbsp;/&nbsp; {{ $penyewaan->nomor_telepon }}
                        </td>
                    </tr>
                </table></td>
            </tr></table>
            <div class="pihak-selanjutnya">Selanjutnya disebut sebagai "<strong>Pihak Kedua</strong>".</div>
        </div>

        <div class="pembuka">
            Para Pihak dengan ini sepakat untuk mengubah ketentuan Perjanjian Awal
            khususnya mengenai Masa Sewa dengan syarat-syarat sebagai berikut:
        </div>

        {{-- PASAL 1 addendum --}}
        <div class="pasal no-break">
            <div class="pasal-heading">Pasal 1</div>
            <div class="pasal-subheading">Objek Perpanjangan</div>
            <p class="pasal-body">Alat Kesehatan yang diperpanjang masa sewanya sebagaimana dimaksud dalam addendum ini adalah:</p>
            <table class="data-pasal-table">
                <tr>
                    <td>(1). Jenis Alat Kesehatan</td><td>:</td>
                    <td>
                        <strong>
                        @foreach($produkList as $i => $p)
                            {{ strtoupper(trim($p)) }}@if($i < count($produkList)-1) / @endif
                        @endforeach
                        </strong>
                    </td>
                </tr>
                <tr>
                    <td>(2). No. Invoice Asal</td><td>:</td>
                    <td><strong>INV-{{ str_pad($penyewaan->id, 5, '0', STR_PAD_LEFT) }}</strong></td>
                </tr>
                <tr>
                    <td>(3). No. Dokumen Extend</td><td>:</td>
                    <td><strong>{{ $extend->nomor_extend }}</strong></td>
                </tr>
            </table>
        </div>

        {{-- PASAL 2 addendum --}}
        <div class="pasal no-break">
            <div class="pasal-heading">Pasal 2</div>
            <div class="pasal-subheading">Perubahan / Perpanjangan Masa Sewa</div>

            <div class="ayat-item">
                <span class="ayat-nomor">(1)</span>
                <span class="ayat-isi">
                    Para Pihak dengan ini sepakat bahwa Masa Sewa yang semula berakhir pada tanggal
                    <u><strong>{{ $tglLamaLabel }}</strong></u>
                    diperpanjang selama
                    <strong>{{ $extend->tambah_hari }} ({{ \App\Helpers\TerbilangHelper::convert($extend->tambah_hari) ?? $extend->tambah_hari }}) hari</strong>
                    sehingga berakhir pada tanggal
                    <u><strong>{{ $tglBaruLabel }}</strong></u>.
                </span>
            </div>

            <div class="highlight-box">
                <table class="data-pasal-table" style="margin:0;">
                    <tr>
                        <td>Deadline Sebelumnya</td><td>:</td>
                        <td><strong>{{ $tglLamaLabel }}</strong></td>
                    </tr>
                    <tr>
                        <td>Deadline Baru</td><td>:</td>
                        <td><strong>{{ $tglBaruLabel }}</strong></td>
                    </tr>
                    <tr>
                        <td>Tambah Durasi</td><td>:</td>
                        <td><strong>{{ $extend->tambah_hari }} Hari</strong></td>
                    </tr>
                    <tr>
                        <td>Biaya Perpanjangan</td><td>:</td>
                        <td><strong>Rp {{ number_format($extend->harga_extend, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <td>Metode Pembayaran</td><td>:</td>
                        <td><strong>{{ strtoupper($extend->metode_bayar) }}</strong></td>
                    </tr>
                </table>
            </div>

            <div class="ayat-item" style="margin-top:5px;">
                <span class="ayat-nomor">(2)</span>
                <span class="ayat-isi">
                    Pihak Kedua berkewajiban untuk mengembalikan Alat Kesehatan kepada Pihak Pertama
                    paling lambat pada tanggal <u><strong>{{ $tglBaruLabel }}</strong></u>.
                    Keterlambatan pengembalian melewati batas waktu tersebut akan dikenakan biaya
                    tambahan sesuai kebijakan Pihak Pertama.
                </span>
            </div>

            <div class="ayat-item">
                <span class="ayat-nomor">(3)</span>
                <span class="ayat-isi">
                    Pihak Kedua wajib melunasi biaya perpanjangan sebesar
                    <strong>Rp {{ number_format($extend->harga_extend, 0, ',', '.') }}</strong>
                    kepada Pihak Pertama selambat-lambatnya pada saat addendum ini ditandatangani.
                    Bukti pembayaran wajib diserahkan kepada Pihak Pertama.
                </span>
            </div>
        </div>

        {{-- PASAL 3 addendum --}}
        <div class="pasal no-break">
            <div class="pasal-heading">Pasal 3</div>
            <div class="pasal-subheading">Ketentuan Lain</div>
            <p class="pasal-body">
                Selain perubahan sebagaimana diatur dalam addendum ini, seluruh ketentuan dan syarat
                yang tertuang dalam Perjanjian Awal tetap berlaku dan mengikat Para Pihak.
                addendum ini merupakan satu kesatuan dan bagian yang tidak terpisahkan dari
                Perjanjian Awal.
            </p>
        </div>

        {{-- PENUTUP --}}
        <div class="pembuka" style="margin-top:10px;">
            Demikian addendum ini dibuat dan ditandatangani di tempat dan waktu sebagaimana
            disebutkan di bagian awal, dalam rangkap 2 (dua) dan bermeterai cukup, masing-masing
            Pihak memperoleh 1 (satu) rangkap asli yang kesemuanya memiliki kekuatan hukum yang sama.
        </div>

        {{-- TTD --}}
        <div class="ttd-section">
            <div class="ttd-para-pihak">Para Pihak,</div>
            <div class="ttd-row">
                <div class="ttd-col">
                    <div class="ttd-label">Pihak Pertama,</div>
                    <img src="{{ asset('images/ttd.png') }}" alt="Tanda Tangan" class="ttd-img">
                    <div class="ttd-garis">ADAM PARAKITRI</div>
                    <div class="ttd-jabatan">Pemilik Usaha</div>
                </div>
                <div class="ttd-logo-center">
                    <img src="{{ asset('images/logo-paralkes-white.png') }}" alt="Logo">
                </div>
                <div class="ttd-col">
                    <div class="ttd-label">Pihak Kedua,</div>
                    <div class="ttd-space"></div>
                    <div class="ttd-garis">{{ strtoupper($penyewaan->nama_penyewa) }}</div>
                    <div class="ttd-jabatan">Penyewa</div>
                </div>
            </div>
        </div>

    </div>{{-- end .page-content --}}

    <div class="page-footer">
        Addendum Perjanjian No. {{ $nomorPerjanjian }} &nbsp;·&nbsp; Paralkes &nbsp;·&nbsp;
        Digenerate otomatis pada {{ $now->translatedFormat('d F Y, H:i') }} WIB
    </div>
</div>

<script>
    // window.addEventListener('load', () => window.print());
</script>
</body>
</html>