<?php

/**
 * File ini berisi fungsi-fungsi helper umum yang dapat digunakan
 * di seluruh aplikasi, terutama di dalam file Blade.
 */

if (!function_exists('format_rupiah')) {
    /**
     * Mengubah angka menjadi format mata uang Rupiah.
     * Contoh: 150000 akan menjadi "Rp 150.000".
     *
     * @param int|float|null $number Angka yang akan diformat.
     * @return string
     */
    function format_rupiah($number): string
    {
        if (is_null($number)) {
            return 'Rp 0';
        }
        return 'Rp' . number_format($number, 0, ',', '.');
    }
}

if (!function_exists('format_tanggal')) {
    /**
     * Mengubah tanggal menjadi format yang lebih mudah dibaca.
     * Contoh: '2025-06-17' akan menjadi "17 Juni 2025".
     *
     * @param \Carbon\Carbon|string|null $date Tanggal yang akan diformat.
     * @param string $format Format output yang diinginkan (default: 'D MMMM YYYY').
     * @return string
     */
    function format_tanggal($date, string $format = 'D MMMM YYYY'): string
    {
        if (is_null($date)) {
            return '-';
        }

        // Set locale ke bahasa Indonesia agar nama bulan tampil dalam bahasa Indonesia
        $carbonDate = \Carbon\Carbon::parse($date);
        $carbonDate->locale('id');

        return $carbonDate->isoFormat($format);
    }
}


if (!function_exists('format_diskon')) {
    /**
     * Mengubah angka diskon menjadi string dengan tanda persen.
     * Contoh: 10 menjadi "10%".
     * Jika null atau 0, bisa mengembalikan string kosong atau "0%".
     *
     * @param int|float|null $number Angka diskon.
     * @param bool $showZero Jika true, tampilkan "0%", jika false tampilkan string kosong.
     * @return string
     */
    function format_diskon($number, bool $showZero = true): string
    {
        if (is_null($number) || $number == 0) {
            return $showZero ? '0%' : '';
        }
        return rtrim(rtrim(number_format($number, 2, ',', ''), '0'), ',') . '%';
    }
}