<?php

namespace App\Services;

use App\Models\ESign;
use App\Models\LetterType;
use Illuminate\Support\Facades\DB;

class LetterNumberService
{
    /**
     * Generate nomor surat berikutnya untuk suatu jenis surat.
     *
     * Format: {RUNNING_NUMBER}/{PREFIX}/{MONTH_ROMAN}/{YEAR}
     * Contoh: 001/ST/VII/2026
     *
     * Running number di-reset setiap tahun dan dihitung per prefix.
     *
     * @param LetterType $letterType Instance model LetterType (mengandung prefix)
     * @return string Nomor surat yang telah digenerated
     */
    public function generate(LetterType $letterType): string
    {
        $prefix   = $letterType->prefix;
        $year     = now()->format('Y');
        $roman    = $this->getRomanMonth((int) now()->format('n'));

        $runningNumber = $this->getRunningNumber($prefix, $year);

        return $this->formatNumber($runningNumber, $prefix, $roman, $year);
    }

    /**
     * Mendapatkan nomor urut terakhir berdasarkan prefix dan tahun berjalan,
     * kemudian mengembalikan nomor urut berikutnya.
     *
     * Pencarian dilakukan dengan mencocokkan pola nomor surat
     * {RUNNING_NUMBER}/{PREFIX}/{MONTH_ROMAN}/{YEAR}.
     *
     * @param string $prefix Kode surat (contoh: ST, SK, SKET)
     * @param string $year   Tahun berjalan (contoh: 2026)
     * @return int Nomor urut berikutnya (dimulai dari 1)
     */
    private function getRunningNumber(string $prefix, string $year): int
    {
        // Cari dokumen terakhir dengan prefix dan tahun yang sama
        // Pola: %/{PREFIX}/%/{YEAR}
        $lastDoc = ESign::where('nomor_surat', 'LIKE', "%/{$prefix}/%/{$year}")
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastDoc) {
            return 1;
        }

        // Ekstrak nomor urut dari segment pertama (sebelum slash pertama)
        $parts    = explode('/', $lastDoc->nomor_surat);
        $lastNumber = (int) ($parts[0] ?? 0);

        return $lastNumber + 1;
    }

    /**
     * Konversi angka bulan (1-12) ke numeral Romawi.
     *
     * Contoh:
     * 1  => I
     * 2  => II
     * 12 => XII
     *
     * @param int $month Angka bulan (1 s.d. 12)
     * @return string Representasi Romawi dari bulan
     */
    private function getRomanMonth(int $month): string
    {
        $romanMap = [
            1  => 'I',
            2  => 'II',
            3  => 'III',
            4  => 'IV',
            5  => 'V',
            6  => 'VI',
            7  => 'VII',
            8  => 'VIII',
            9  => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];

        return $romanMap[$month] ?? '';
    }

    /**
     * Memformat komponen nomor surat ke dalam string final.
     *
     * Format: {RUNNING_NUMBER}/{PREFIX}/{MONTH_ROMAN}/{YEAR}
     * Contoh: 001/ST/VII/2026
     *
     * @param int    $number    Nomor urut (contoh: 1, 2, 15)
     * @param string $prefix    Kode surat (contoh: ST, SK, SKET)
     * @param string $romanMonth Bulan dalam Romawi (contoh: VII)
     * @param string $year      Tahun (contoh: 2026)
     * @return string Nomor surat yang telah diformat
     */
    private function formatNumber(int $number, string $prefix, string $romanMonth, string $year): string
    {
        $padded = str_pad((string) $number, 3, '0', STR_PAD_LEFT);

        return "{$padded}/{$prefix}/{$romanMonth}/{$year}";
    }
}
