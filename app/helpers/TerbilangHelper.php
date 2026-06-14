<?php

namespace App\Helpers;

class TerbilangHelper
{
    /**
     * Konversi angka ke teks terbilang Bahasa Indonesia
     * Contoh: 150000 → "seratus lima puluh ribu"
     */
    public static function convert(int|float $angka): string
    {
        $angka  = (int) abs($angka);
        $satuan = [
            '', 'satu', 'dua', 'tiga', 'empat', 'lima',
            'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh',
            'sebelas', 'dua belas', 'tiga belas', 'empat belas', 'lima belas',
            'enam belas', 'tujuh belas', 'delapan belas', 'sembilan belas',
        ];

        if ($angka < 20) {
            return $satuan[$angka];
        }

        if ($angka < 100) {
            $hasil = self::convert((int) ($angka / 10)) . ' puluh';
            if ($angka % 10 !== 0) {
                $hasil .= ' ' . self::convert($angka % 10);
            }
            return $hasil;
        }

        if ($angka < 200) {
            $hasil = 'seratus';
            if ($angka % 100 !== 0) {
                $hasil .= ' ' . self::convert($angka % 100);
            }
            return $hasil;
        }

        if ($angka < 1000) {
            $hasil = self::convert((int) ($angka / 100)) . ' ratus';
            if ($angka % 100 !== 0) {
                $hasil .= ' ' . self::convert($angka % 100);
            }
            return $hasil;
        }

        if ($angka < 2000) {
            $hasil = 'seribu';
            if ($angka % 1000 !== 0) {
                $hasil .= ' ' . self::convert($angka % 1000);
            }
            return $hasil;
        }

        if ($angka < 1_000_000) {
            $hasil = self::convert((int) ($angka / 1000)) . ' ribu';
            if ($angka % 1000 !== 0) {
                $hasil .= ' ' . self::convert($angka % 1000);
            }
            return $hasil;
        }

        if ($angka < 1_000_000_000) {
            $hasil = self::convert((int) ($angka / 1_000_000)) . ' juta';
            if ($angka % 1_000_000 !== 0) {
                $hasil .= ' ' . self::convert($angka % 1_000_000);
            }
            return $hasil;
        }

        if ($angka < 1_000_000_000_000) {
            $hasil = self::convert((int) ($angka / 1_000_000_000)) . ' miliar';
            if ($angka % 1_000_000_000 !== 0) {
                $hasil .= ' ' . self::convert($angka % 1_000_000_000);
            }
            return $hasil;
        }

        return (string) $angka; // fallback jika > 1 triliun
    }

    /**
     * Konversi ke terbilang dengan huruf kapital di awal + " Rupiah"
     * Contoh: 150000 → "Seratus Lima Puluh Ribu Rupiah"
     */
    public static function rupiah(int|float $angka): string
    {
        $terbilang = self::convert((int) $angka);
        return ucwords($terbilang) . ' Rupiah';
    }

    /**
     * Alias ucwords untuk dipakai langsung di blade
     * Contoh: TerbilangHelper::terbilang(75000)
     *         → "tujuh puluh lima ribu"
     */
    public static function terbilang(int|float $angka): string
    {
        return self::convert((int) $angka);
    }
}