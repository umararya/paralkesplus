{{-- resources/views/admin/penyewaan/cetak/perjanjian.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perjanjian Sewa - {{ $penyewaan->nama_penyewa }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10.5pt;
            color: #000;
            background: #f0f0f0;
            line-height: 1.45;
        }

        /* ── SCREEN ── */
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

        /* ── KOP SURAT ── */
        .kop {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px double #1D6FA4;
            padding-bottom: 8px;
            margin-bottom: 5px;
        }
        .kop-left img  { height: 50px; object-fit: contain; }
        .kop-center    { text-align: center; flex: 1; padding: 0 8px; }
        .kop-center .nama-toko {
            font-size: 14pt;
            font-weight: 800;
            color: #1D6FA4;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-family: Arial, sans-serif;
        }
        .kop-center .tagline {
            font-size: 8pt;
            color: #555;
            margin-top: 1px;
            font-family: Arial, sans-serif;
        }
        .kop-center .alamat-toko {
            font-size: 7.5pt;
            color: #555;
            margin-top: 2px;
            font-family: Arial, sans-serif;
            line-height: 1.4;
        }
        .kop-right img { height: 50px; object-fit: contain; }

        /* ── JUDUL ── */
        .doc-title { text-align: center; margin: 8px 0 3px; }
        .doc-title h1 {
            font-size: 11.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .doc-nomor {
            font-size: 10.5pt;
            text-align: center;
            margin-bottom: 6px;
            font-weight: 700;
        }

        /* ── PARAGRAF ── */
        .pembuka {
            font-size: 10pt;
            text-align: justify;
            margin: 5px 0;
            line-height: 1.45;
        }

        /* ── PARA PIHAK ── */
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

        /* ── MENERANGKAN ── */
        .menerangkan-block { margin: 4px 0; font-size: 10pt; }
        .menerangkan-item  { display: flex; margin-bottom: 3px; line-height: 1.45; }
        .mn-nomor { min-width: 26px; }
        .mn-isi   { flex: 1; text-align: justify; }

        /* ── PASAL ── */
        .pasal { margin: 6px 0 5px; }
        .pasal-heading    { font-size: 10.5pt; font-weight: 700; text-align: center; margin-bottom: 0; }
        .pasal-subheading { font-size: 10.5pt; font-weight: 700; text-align: center; margin-bottom: 3px; }
        .pasal-body       { font-size: 10pt; text-align: justify; margin-bottom: 3px; line-height: 1.45; }

        /* ── AYAT ── */
        .ayat-item  { display: flex; margin-bottom: 2px; font-size: 10pt; line-height: 1.45; }
        .ayat-nomor { min-width: 24px; flex-shrink: 0; }
        .ayat-isi   { flex: 1; text-align: justify; }

        /* ── SUB AYAT ── */
        .sub-ayat-list { margin: 1px 0 0; padding: 0; list-style: none; }
        .sub-ayat-item { display: flex; margin-bottom: 1px; line-height: 1.45; }
        .sub-huruf { min-width: 24px; flex-shrink: 0; }
        .sub-isi   { flex: 1; text-align: justify; }

        /* ── SUB-SUB ── */
        .subsub-list  { margin: 1px 0 0; padding: 0; list-style: none; }
        .subsub-item  { display: flex; margin-bottom: 1px; line-height: 1.45; }
        .subsub-nomor { min-width: 26px; flex-shrink: 0; }
        .subsub-isi   { flex: 1; text-align: justify; }

        /* ── TABEL DATA PASAL ── */
        .data-pasal-table { width: 100%; border-collapse: collapse; font-size: 10pt; margin: 3px 0 4px; }
        .data-pasal-table td { padding: 2px 4px; vertical-align: top; line-height: 1.4; }
        .data-pasal-table td:first-child  { width: 190px; }
        .data-pasal-table td:nth-child(2) { width: 10px; text-align: center; }
        .data-pasal-table td:last-child   { font-weight: 600; }

        /* ── TANDA TANGAN ── */
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

        /* ── NO BREAK ── */
        .no-break { page-break-inside: avoid; break-inside: avoid; }

        /* ── FOOTER ── */
        .page-footer {
            border-top: 1px solid #ccc;
            padding-top: 3px;
            font-size: 7pt;
            color: #999;
            text-align: center;
            font-family: Arial, sans-serif;
            margin-top: auto;
        }

        /* ══════════════════════════════════════
           PRINT — A4 (210mm × 297mm)
        ══════════════════════════════════════ */
        @media print {
            * { -webkit-print-color-adjust: exact; print-color-adjust: exact; }

            html, body {
                background: #fff !important;
                width: 210mm;
            }

            .no-print { display: none !important; }

            @page {
                size: 210mm 297mm portrait;
                margin-top:    15mm;
                margin-right:  19mm;
                margin-bottom: 13mm;
                margin-left:   19mm;
            }

            .page-wrapper {
                width: 100% !important;
                min-height: auto !important;
                padding: 0 !important;
                margin: 0 !important;
                box-shadow: none !important;
                display: flex !important;
                flex-direction: column !important;
                height: calc(297mm - 15mm - 13mm);
                page-break-after: always;
                break-after: page;
            }

            .page-wrapper:last-child {
                page-break-after: avoid;
                break-after: avoid;
            }

            .page-content { flex: 1; overflow: hidden; }

            .page-footer { margin-top: auto !important; }
        }
    </style>
</head>
<body>

{{-- ── TOMBOL CETAK ── --}}
<div class="no-print"
     style="position:fixed;top:16px;right:16px;z-index:999;display:flex;gap:8px;">
    <button onclick="window.print()"
            style="display:inline-flex;align-items:center;gap:6px;padding:9px 18px;
                   background:#1D6FA4;color:#fff;border:none;border-radius:8px;
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

    $now             = now('Asia/Jakarta');
    $nomorPerjanjian = str_pad($penyewaan->id, 3, '0', STR_PAD_LEFT) . '/PAR/SR/' . $now->format('Y');
    $tglBuatHari     = $now->translatedFormat('l');
    $tglBuatLabel    = $now->translatedFormat('d F Y');
    $tglMulaiLabel   = $penyewaan->tgl_mulai
                        ? strtoupper($penyewaan->tgl_mulai->locale('id')->translatedFormat('d F Y'))
                        : '-';
    $tglSelesaiLabel = $penyewaan->tgl_selesai
                        ? strtoupper($penyewaan->tgl_selesai->locale('id')->translatedFormat('d F Y'))
                        : '-';
    $produkList = array_map('trim', explode(', ', $penyewaan->produk_alkes));
@endphp


{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- HALAMAN 1 — Para Pihak + Pasal 1 + Pasal 2 + Pasal 3         --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div class="page-wrapper">
    <div class="page-content">

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

        <div class="doc-title"><h1>Perjanjian Sewa Alat Kesehatan</h1></div>
        <div class="doc-nomor">Nomor: {{ $nomorPerjanjian }}</div>

        <div class="pembuka">
            <strong>PERJANJIAN SEWA ALAT KESEHATAN Nomor: {{ $nomorPerjanjian }}</strong>
            ini dibuat dan ditandatangani di Semarang pada hari ini,
            (<u><strong>{{ $tglBuatHari }}</strong></u>)
            tanggal <u><strong>{{ $tglBuatLabel }}</strong></u>
            ("<strong>Perjanjian</strong>"), oleh dan di antara:
        </div>

        {{-- PIHAK PERTAMA --}}
        <div class="pihak-table-wrap no-break">
            <table class="pihak-outer"><tr>
                <td class="pihak-nomor">1</td>
                <td><table class="pihak-data-table">
                    <tr><td>Nama</td><td>:</td><td><strong>ADAM PARAKITRI (SELAKU PEMILIK USAHA)</strong></td></tr>
                    <tr><td>Tempat/Tanggal Lahir</td><td>:</td><td>SEMARANG, 15 JULI 1991</td></tr>
                    <tr><td>Alamat</td><td>:</td><td>JL. SEKAYU BARU 3/393 SEMARANG JAWA TENGAH</td></tr>
                    <tr><td>Nomor KTP &amp; HP</td><td>:</td><td>3374151507910002</td></tr>
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
                    <tr>
                        <td>Tempat/Tanggal Lahir</td><td>:</td>
                        <td>
                            @if($penyewaan->tempat_tanggal_lahir)
                                {{ strtoupper($penyewaan->tempat_tanggal_lahir) }}
                            @else
                                <span style="color:#aaa; font-style:italic;">— (belum diisi)</span>
                            @endif
                        </td>
                    </tr>
                    <tr><td>Alamat</td><td>:</td><td>{{ strtoupper($penyewaan->alamat_penyewa) }}</td></tr>
                    <tr>
                        <td>Nomor KTP &amp; HP</td><td>:</td>
                        <td>
                            @if($penyewaan->nomor_ktp)
                                {{ $penyewaan->nomor_ktp }}
                            @else
                                <span style="color:#aaa; font-style:italic;">— (belum diisi)</span>
                            @endif
                            &nbsp;/&nbsp; {{ $penyewaan->nomor_telepon }}
                        </td>
                    </tr>
                </table></td>
            </tr></table>
            <div class="pihak-selanjutnya">Selanjutnya disebut sebagai "<strong>Pihak Kedua</strong>".</div>
        </div>

        <div class="pembuka">
            Pihak Pertama dan Pihak Kedua secara bersama-sama selanjutnya disebut sebagai
            "<strong>Para Pihak</strong>". Para Pihak dengan ini terlebih dahulu menerangkan
            hal-hal sebagai berikut.
        </div>

        <div class="menerangkan-block">
            <div class="menerangkan-item">
                <span class="mn-nomor">(1)</span>
                <span class="mn-isi">Bahwa Pihak Pertama adalah perorangan yang memiliki dan menguasai sebuah usaha Sewa Alat Kesehatan yang peruntukannya digunakan sebagai Fasilitas penyedia persewaan berbagai Alat Kesehatan kepada Pelanggan/Penyewa.</span>
            </div>
            <div class="menerangkan-item">
                <span class="mn-nomor">(2)</span>
                <span class="mn-isi">Bahwa Pihak Kedua adalah perorangan yang bermaksud untuk menyewa alat kesehatan milik Pihak Pertama dan Pihak Pertama telah bersedia untuk menyewakan Alat Kesehatan tersebut kepada Pihak Kedua.</span>
            </div>
        </div>

        <div class="pembuka">
            Berdasarkan hal-hal tersebut di atas dan dengan iktikad baik, Para Pihak dengan ini
            sepakat untuk saling mengikatkan diri dalam Perjanjian ini dengan ketentuan-ketentuan
            dan syarat-syarat sebagaimana diatur dalam pasal-pasal di bawah ini.
        </div>

        {{-- PASAL 1 --}}
        <div class="pasal no-break">
            <div class="pasal-heading">Pasal 1</div>
            <div class="pasal-subheading">Kesepakatan Sewa-Menyewa</div>
            <p class="pasal-body">Pihak Pertama dengan ini sepakat untuk menyewakan Alat Kesehatan kepada Pihak Kedua sebagaimana Pihak Kedua dengan ini sepakat untuk membayar harga sewa Alat Kesehatan tersebut kepada Pihak Pertama.</p>
        </div>

        {{-- PASAL 2 --}}
        <div class="pasal no-break">
            <div class="pasal-heading">Pasal 2</div>
            <div class="pasal-subheading">Hak dan Kewajiban Para Pihak</div>
            <div class="ayat-item">
                <span class="ayat-nomor">(1)</span>
                <span class="ayat-isi">Hak dan Kewajiban Pihak Pertama
                    <div class="sub-ayat-list">
                        <div class="sub-ayat-item"><span class="sub-huruf">a.</span><span class="sub-isi">Pihak Pertama berhak untuk menerima pembayaran harga sewa dari Pihak Kedua.</span></div>
                        <div class="sub-ayat-item"><span class="sub-huruf">b.</span><span class="sub-isi">Pihak Pertama berkewajiban untuk menyerahkan hak penggunaan Alat Kesehatan kepada Pihak Kedua.</span></div>
                    </div>
                </span>
            </div>
            <div class="ayat-item">
                <span class="ayat-nomor">(2)</span>
                <span class="ayat-isi">Hak dan Kewajiban Pihak Kedua
                    <div class="sub-ayat-list">
                        <div class="sub-ayat-item"><span class="sub-huruf">a.</span><span class="sub-isi">Pihak Kedua berhak untuk menggunakan dan memanfaatkan fasilitas Alat Kesehatan.</span></div>
                        <div class="sub-ayat-item"><span class="sub-huruf">b.</span><span class="sub-isi">Pihak Kedua berkewajiban untuk melakukan pembayaran harga sewa kepada Pihak Pertama.</span></div>
                    </div>
                </span>
            </div>
        </div>

        {{-- PASAL 3 --}}
        <div class="pasal no-break">
            <div class="pasal-heading">Pasal 3</div>
            <div class="pasal-subheading">Alat Kesehatan</div>
            <p class="pasal-body">Alat Kesehatan yang disewa sebagaimana dimaksud dalam Perjanjian ini adalah sebuah Alat Kesehatan dalam kondisi baik dan layak untuk disewakan dengan ketentuan sebagai berikut :</p>
            <table class="data-pasal-table">
                <tr>
                    <td>(1). Jenis Alat Kesehatan</td><td>:</td>
                    <td>
                        @if(count($produkList) === 1)
                            <strong>{{ strtoupper($produkList[0]) }}</strong>
                        @else
                            @foreach($produkList as $i => $p)
                                <strong>{{ strtoupper(trim($p)) }}</strong>@if($i < count($produkList)-1) / @endif
                            @endforeach
                        @endif
                    </td>
                </tr>
                <tr><td>(2). Peruntukan Alat Kesehatan</td><td>:</td><td><strong>PEMAKAIAN PRIBADI</strong></td></tr>
                <tr><td>(3). Fasilitas Alat Kesehatan</td><td>:</td><td>Barang yang disewa dalam keadaan lengkap, layak dan tidak ada kendala.</td></tr>
            </table>
        </div>

    </div>{{-- end .page-content --}}

    <div class="page-footer">
        Halaman 1 dari 4 &nbsp;·&nbsp; Perjanjian Sewa No. {{ $nomorPerjanjian }} &nbsp;·&nbsp; Paralkes
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- HALAMAN 2 — Pasal 4 Ayat 1–3                                  --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div class="page-wrapper">
    <div class="page-content">

        <div class="kop">
            <div class="kop-left"><img src="{{ asset('images/logo-cop-paralkesplus2.png') }}" alt="Logo"></div>
            <div class="kop-center">
                <div class="nama-toko">Paralkes</div>
                <div class="tagline">Penyewaan &amp; Penjualan Alat Kesehatan</div>
                <div class="alamat-toko">Jl. Srikaton Selatan No.19, Purwoyoso, Kec. Ngaliyan, Kota Semarang, Jawa Tengah 50184</div>
            </div>
            <div class="kop-right"><img src="{{ asset('images/logo-cop-paralkesplus3.png') }}" alt="Logo"></div>
        </div>

        {{-- PASAL 4 --}}
        <div class="pasal" style="margin-top:8px;">
            <div class="pasal-heading">Pasal 4</div>
            <div class="pasal-subheading">Pelaksanaan Hak Sewa</div>

            <div class="ayat-item">
                <span class="ayat-nomor">1.</span>
                <span class="ayat-isi">Pihak Kedua wajib untuk menggunakan Alat Kesehatan sesuai dengan peruntukan Alat Kesehatan dan karenanya Pihak Kedua <strong>dilarang</strong> untuk:
                    <div class="sub-ayat-list">
                        <div class="sub-ayat-item"><span class="sub-huruf">a.</span><span class="sub-isi">Menggunakan Barang untuk kegiatan yang bertentangan dengan peraturan perundang-undangan, ketertiban umum, dan kesusilaan.</span></div>
                        <div class="sub-ayat-item"><span class="sub-huruf">b.</span>
                            <span class="sub-isi">Menggunakan Barang untuk kegiatan lain di luar peruntukan Alat Kesehatan tanpa izin tertulis dari Pihak Pertama dengan ketentuan:
                                <div class="subsub-list">
                                    <div class="subsub-item"><span class="subsub-nomor">i.</span><span class="subsub-isi">Dalam hal Pihak Kedua melaksanakan hak menggunakan Alat Kesehatan untuk kegiatan lain di luar peruntukan, Pihak Pertama berhak untuk memerintahkan Pihak Kedua untuk mengembalikan pelaksanaan hak sewa Alat Kesehatan tersebut sesuai dengan peruntukannya.</span></div>
                                    <div class="subsub-item"><span class="subsub-nomor">ii.</span><span class="subsub-isi">Dalam hal Pihak Kedua tidak melaksanakan perintah Pihak Pertama sebagaimana dimaksud dalam angka i huruf b ayat (1), Pihak Pertama berhak untuk mengakhiri masa sewa secara sepihak.</span></div>
                                    <div class="subsub-item"><span class="subsub-nomor">iii.</span><span class="subsub-isi">Dalam hal Pihak Pertama mengakhiri masa sewa secara sepihak sebagaimana dimaksud dalam angka ii huruf b ayat (1) pasal ini, Pihak Pertama tidak berkewajiban untuk mengembalikan sebagian dari harga sewa untuk masa sewa yang belum digunakan oleh Pihak Kedua.</span></div>
                                </div>
                            </span>
                        </div>
                    </div>
                </span>
            </div>

            <div class="ayat-item">
                <span class="ayat-nomor">2.</span>
                <span class="ayat-isi">Pihak Kedua berkewajiban untuk menanggung atas biaya sendiri penggunaan fasilitas Barang Sewa Alat Kesehatan seperti Pengiriman Barang melalui Jasa Ekspedisi.</span>
            </div>

            <div class="ayat-item">
                <span class="ayat-nomor">3.</span>
                <span class="ayat-isi">Dalam melaksanakan hak menggunakan Alat Kesehatan, Pihak Kedua wajib untuk melaksanakan hak tersebut dengan sebaik-baiknya, seperti layaknya seorang yang memiliki barang mewah yang baik dan karenanya Pihak Kedua berkewajiban untuk:
                    <div class="sub-ayat-list">
                        <div class="sub-ayat-item"><span class="sub-huruf">a.</span><span class="sub-isi">Menjamin kebersihan Alat Kesehatan dan menjamin melakukan perawatan Alat Kesehatan dengan baik.</span></div>
                        <div class="sub-ayat-item"><span class="sub-huruf">b.</span><span class="sub-isi">Melakukan perbaikan-perbaikan atas segala kerusakan kecil bagian-bagian dari Barang Alat Kesehatan yang meliputi tetapi tidak terbatas pada bagian-bagian perangkat Alat Kesehatan.</span></div>
                        <div class="sub-ayat-item"><span class="sub-huruf">c.</span><span class="sub-isi">Menjamin apabila dikemudian hari terjadi <em>Force Majeure</em> (keadaan Kahar seperti Bencana alam seperti gempa bumi, gunung meletus, badai, angin topan, tsunami, banjir besar, tanah longsor, dan kebakaran), maka Pihak Kedua wajib bertanggungjawab dan mengganti atas kerusakan atau kehilangan barang Alat Kesehatan yang terjadi.</span></div>
                    </div>
                </span>
            </div>

            <div class="ayat-item">
                <span class="ayat-nomor">4.</span>
                <span class="ayat-isi">Pihak Pertama berkewajiban untuk melakukan perbaikan atas kerusakan bagian-bagian Alat Kesehatan yang bukan disebabkan karena kesalahan dan/atau penggunaan Barang oleh Pihak Kedua.</span>
            </div>

            <div class="ayat-item">
                <span class="ayat-nomor">5.</span>
                <span class="ayat-isi">Pihak Kedua dilarang untuk melakukan perubahan, membuat baru, atau mengurangi bagian-bagian dari Alat Kesehatan tanpa izin tertulis dari Pihak Pertama.</span>
            </div>

            <div class="ayat-item">
                <span class="ayat-nomor">6.</span>
                <span class="ayat-isi">Pihak Kedua dilarang untuk mengulangsewakan Alat Kesehatan kepada pihak ketiga atau melepaskan hak sewanya berdasarkan Perjanjian ini dan menyerahkannya kepada pihak ketiga tanpa kesepakatan tertulis dari Pihak Pertama dengan ketentuan:
                    <div class="sub-ayat-list">
                        <div class="sub-ayat-item"><span class="sub-huruf">a.</span><span class="sub-isi">Dalam hal Pihak Kedua mengulangsewakan Alat Kesehatan kepada pihak ketiga atau melepaskan hak sewanya dan menyerahkannya kepada pihak ketiga tanpa kesepakatan tertulis dari Pihak Pertama, Pihak Pertama berhak untuk mengakhiri masa sewa secara sepihak.</span></div>
                        <div class="sub-ayat-item"><span class="sub-huruf">b.</span><span class="sub-isi">Atas pengakhiran Masa Sewa secara sepihak sebagaimana dimaksud dalam huruf a ayat (6) pasal ini, Pihak Pertama tidak berkewajiban untuk mengembalikan sebagian Harga Sewa atas Masa Sewa yang belum digunakan oleh Pihak Kedua.</span></div>
                    </div>
                </span>
            </div>

            <div class="ayat-item">
                <span class="ayat-nomor">7.</span>
                <span class="ayat-isi">Para Pihak dengan ini sepakat bahwa Perjanjian ini dan segala akibatnya tidak akan berakhir dengan meninggalnya salah satu atau kedua belah Pihak yang segala hak dan kewajibannya akan dilanjutkan kepada para ahli waris dari Para Pihak.</span>
            </div>
        </div>

        {{-- PASAL 5 --}}
        <div class="pasal no-break">
            <div class="pasal-heading">Pasal 5</div>
            <div class="pasal-subheading">Serah Terima Hak Sewa</div>
            <div class="ayat-item">
                <span class="ayat-nomor">1.</span>
                <span class="ayat-isi">Pihak Kedua berhak untuk menggunakan Alat Kesehatan sejak diserahterimakannya hak menggunakan Alat Kesehatan oleh Pihak Pertama kepada Pihak Kedua dengan ketentuan:
                    <div class="sub-ayat-list">
                        <div class="sub-ayat-item"><span class="sub-huruf">a.</span><span class="sub-isi">Serah terima hak menggunakan Alat Kesehatan tersebut dilakukan dengan cara penyerahan Barang Alat Kesehatan kepada Pihak Kedua.</span></div>
                        <div class="sub-ayat-item"><span class="sub-huruf">b.</span><span class="sub-isi">Penyerahan Alat Kesehatan sebagaimana dimaksud dalam huruf a ayat (1) Pasal ini dilakukan selambat-lambatnya pada saat Pihak Kedua melakukan pembayaran Harga Sewa kepada Pihak Pertama.</span></div>
                        <div class="sub-ayat-item"><span class="sub-huruf">c.</span><span class="sub-isi">Para Pihak dengan ini sepakat bahwa pada saat serah terima hak menggunakan Alat Kesehatan, Alat Kesehatan tersebut dalam keadaan kosong dan terawat baik.</span></div>
                    </div>
                </span>
            </div>
            <div class="ayat-item">
                <span class="ayat-nomor">2.</span>
                <span class="ayat-isi">Pihak Kedua berkewajiban untuk mengembalikan hak menggunakan Alat Kesehatan kepada Pihak Pertama dengan ketentuan:
                    <div class="sub-ayat-list">
                        <div class="sub-ayat-item"><span class="sub-huruf">a.</span><span class="sub-isi">Pengembalian hak menggunakan Alat Kesehatan tersebut dilakukan dengan cara pengembalian Alat Kesehatan oleh Pihak Kedua kepada Pihak Pertama.</span></div>
                        <div class="sub-ayat-item"><span class="sub-huruf">b.</span><span class="sub-isi">Pada saat pengembalian Alat Kesehatan dilakukan, Alat Kesehatan harus seperti dalam keadaan ketika dilakukannya penyerahan Alat Kesehatan oleh Pihak Pertama kepada Pihak Kedua.</span></div>
                    </div>
                </span>
            </div>
        </div>

    </div>{{-- end .page-content --}}

    <div class="page-footer">
        Halaman 2 dari 4 &nbsp;·&nbsp; Perjanjian Sewa No. {{ $nomorPerjanjian }} &nbsp;·&nbsp; Paralkes
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════ --}}
{{-- HALAMAN 3 — Pasal 6 + 7 + 8 + 9 + Penutup + TTD              --}}
{{-- ══════════════════════════════════════════════════════════════ --}}
<div class="page-wrapper">
    <div class="page-content">

        <div class="kop">
            <div class="kop-left"><img src="{{ asset('images/logo-cop-paralkesplus2.png') }}" alt="Logo"></div>
            <div class="kop-center">
                <div class="nama-toko">Paralkes</div>
                <div class="tagline">Penyewaan &amp; Penjualan Alat Kesehatan</div>
                <div class="alamat-toko">Jl. Srikaton Selatan No.19, Purwoyoso, Kec. Ngaliyan, Kota Semarang, Jawa Tengah 50184</div>
            </div>
            <div class="kop-right"><img src="{{ asset('images/logo-cop-paralkesplus3.png') }}" alt="Logo"></div>
        </div>

        {{-- PASAL 6 --}}
        <div class="pasal no-break" style="margin-top:8px;">
            <div class="pasal-heading">Pasal 6</div>
            <div class="pasal-subheading">Masa Sewa</div>
            <div class="ayat-item">
                <span class="ayat-nomor">(1)</span>
                <span class="ayat-isi">Pihak Kedua berhak untuk menggunakan Alat Kesehatan untuk selama jangka waktu sesuai dengan yang tertuang di dalam <strong>Bukti Pembayaran Invoice</strong> atau yang dimulai sejak tanggal <u><strong>{{ $tglMulaiLabel }}</strong></u> dan berakhir pada tanggal <u><strong>{{ $tglSelesaiLabel }}</strong></u> ("<strong>Masa Sewa</strong>").</span>
            </div>
            <div class="ayat-item">
                <span class="ayat-nomor">(2)</span>
                <span class="ayat-isi">Pihak Kedua berhak untuk mengajukan <strong>perpanjangan Masa Sewa</strong> kepada Pihak Pertama dengan ketentuan:
                    <div class="sub-ayat-list">
                        <div class="sub-ayat-item"><span class="sub-huruf">a.</span><span class="sub-isi">Pihak Pertama berhak untuk mengajukan Harga Sewa, Masa Sewa serta syarat dan ketentuan Perjanjian yang baru.</span></div>
                        <div class="sub-ayat-item"><span class="sub-huruf">b.</span><span class="sub-isi">Pengajuan perpanjangan Masa Sewa tersebut wajib dilakukan oleh Pihak Kedua kepada Pihak Pertama dalam jangka waktu selambat-lambatnya 7 (Tujuh) hari kalender sebelum berakhirnya Masa Sewa.</span></div>
                    </div>
                </span>
            </div>
            <div class="ayat-item">
                <span class="ayat-nomor">(3)</span>
                <span class="ayat-isi">Para Pihak dengan ini sepakat bahwa pada prinsipnya <strong>pengakhiran Masa Sewa sebelum berakhirnya Masa Sewa</strong> hanya dapat dilakukan dengan kesepakatan bersama Para Pihak yang dibuat secara tertulis, tetapi masing-masing pihak dapat mengakhiri Masa Sewa secara sepihak dengan ketentuan: Dalam hal Pihak Kedua mengakhiri Masa Sewa secara sepihak sebelum berakhirnya Masa Sewa, Pihak Kedua tidak berhak untuk menuntut kepada Pihak Pertama atas pengembalian sebagian Harga Sewa untuk Masa Sewa yang belum digunakan.</span>
            </div>
            <div class="ayat-item">
                <span class="ayat-nomor">(4)</span>
                <span class="ayat-isi">Pihak Pertama berhak untuk mengakhiri Masa Sewa secara sepihak sesuai dengan ketentuan dalam Pasal 4 ayat (1) huruf b angka ii dan Pasal 4 ayat (6) Perjanjian ini.</span>
            </div>
        </div>

        {{-- PASAL 7 --}}
        <div class="pasal no-break">
            <div class="pasal-heading">Pasal 7</div>
            <div class="pasal-subheading">Harga Sewa</div>
            <div class="ayat-item">
                <span class="ayat-nomor">(1)</span>
                <span class="ayat-isi">Para Pihak dengan ini sepakat bahwa besarnya harga sewa adalah sebesar <strong>sesuai dengan Bukti Pembayaran / Invoice</strong> yang telah <strong>dilunasi</strong> oleh Pihak Kedua kepada Pihak Pertama dalam jangka waktu selambat-lambatnya pada saat dimulainya Masa Sewa ("<strong>Harga Sewa</strong>").</span>
            </div>
        </div>

        {{-- PASAL 8 --}}
        <div class="pasal no-break">
            <div class="pasal-heading">Pasal 8</div>
            <div class="pasal-subheading">Adendum</div>
            <p class="pasal-body">Segala perubahan ketentuan dan/atau penambahan ketentuan yang belum diatur dan/atau belum cukup diatur dalam Perjanjian ini akan disepakati lebih lanjut oleh Para Pihak dan hasilnya akan dituangkan ke dalam suatu adendum yang ditandatangani oleh Para Pihak yang merupakan satu kesatuan dan menjadi bagian yang tidak terpisahkan dari Perjanjian ini.</p>
        </div>

        {{-- PASAL 9 --}}
        <div class="pasal no-break">
            <div class="pasal-heading">Pasal 9</div>
            <div class="pasal-subheading">Penyelesaian Perselisihan</div>
            <p class="pasal-body">Dalam hal terjadi perselisihan diantara Para Pihak sebagai akibat dari pelaksanaan Perjanjian ini, Para Pihak dengan ini sepakat untuk menyelesaikannya secara musyawarah dan kekeluargaan.</p>
        </div>

        {{-- PENUTUP --}}
        <div class="pembuka" style="margin-top:10px;">
            Demikian Perjanjian ini dibuat disampaikan kepada Pelanggan dan ditandatangani di tempat dan pada
            waktu sebagaimana disebutkan di bagian awal Perjanjian ini dalam rangkap 2 (dua) dan bermeterai cukup,
            masing-masing Pihak memperoleh 1 (satu) rangkap asli yang kesemuanya memiliki kekuatan hukum yang sama.
        </div>

        {{-- TANDA TANGAN --}}
        <div class="ttd-section">
            <div class="ttd-para-pihak">Para Pihak,</div>
            <div class="ttd-row">

                {{-- Pihak Pertama: TTD dari file gambar --}}
                <div class="ttd-col">
                    <div class="ttd-label">Pihak Pertama,</div>
                    <img src="{{ asset('images/ttd.png') }}" alt="Tanda Tangan Adam Parakitri" class="ttd-img">
                    <div class="ttd-garis">ADAM PARAKITRI</div>
                    <div class="ttd-jabatan">Pemilik Usaha</div>
                </div>

                {{-- Logo tengah --}}
                <div class="ttd-logo-center">
                    <img src="{{ asset('images/logo-paralkes-white.png') }}" alt="Logo">
                </div>

                {{-- Pihak Kedua: ruang kosong untuk TTD manual --}}
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
        Halaman 3 dari 3 &nbsp;·&nbsp; Perjanjian Sewa No. {{ $nomorPerjanjian }} &nbsp;·&nbsp; Paralkes<br>
        Dokumen ini digenerate otomatis oleh sistem Paralkes pada {{ now('Asia/Jakarta')->translatedFormat('d F Y, H:i') }} WIB
    </div>
</div>

<script>
    // window.addEventListener('load', () => window.print());
</script>
</body>
</html>