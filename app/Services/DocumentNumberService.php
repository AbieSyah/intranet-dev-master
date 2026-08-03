<?php

namespace App\Services;

use App\Models\ESign;
use Illuminate\Support\Facades\DB;

class DocumentNumberService
{
    /**
     * Generate the next document number for a given letter type slug.
     *
     * Format: {PREFIX}/{TAHUN}/{URUTAN}
     * Contoh: PKWT/2026/001
     *
     * Nomor di-reset setiap tahun. Urutan dihitung per (prefix, tahun).
     *
     * @param string $jenisSuratSlug
     * @return string nomor_surat generated
     */
    /**
     * Generate the next document number for a given letter type slug.
     *
     * Format: {PREFIX}/{TAHUN}/{URUTAN}
     * Contoh: PKWT/2026/001
     *
     * Nomor di-reset setiap tahun. Urutan dihitung per (prefix, tahun).
     *
     * Method ini TIDAK membungkus diri dengan DB::transaction() karena
     * harus dipanggil dari DALAM transaksi yang sudah dibuka oleh pemanggil
     * (ESignService::storeDraft).
     *
     * @param string $jenisSuratSlug
     * @return string nomor_surat generated
     *
     * @throws \RuntimeException jika gagal mendapatkan lock
     */
    public function generateNextNumber(string $jenisSuratSlug): string
    {
        $prefix = $this->getPrefix($jenisSuratSlug);
        $tahun = now()->format('Y');
        $lockName = 'esign_number_' . $prefix . '_' . $tahun;

        // Ambil lock — timeout 10 detik
        $gotLock = DB::select("SELECT GET_LOCK(?, 10) AS locked", [$lockName]);
        $locked = (int) ($gotLock[0]->locked ?? 0);

        if (!$locked) {
            throw new \RuntimeException(
                "Gagal mendapatkan lock untuk generate nomor surat: {$prefix}/{$tahun}. " .
                "Silakan coba lagi."
            );
        }

        try {
            // Cari nomor terakhir dengan prefix dan tahun yang sama
            $lastNumber = $this->getLastSequentialNumber($prefix, $tahun);

            // Increment
            $nextNumber = $lastNumber + 1;

            // Format dengan padding 3 digit
            $nomorSurat = sprintf('%s/%s/%03d', $prefix, $tahun, $nextNumber);

            return $nomorSurat;
        } finally {
            // Lepas lock — dijamin jalan walaupun terjadi exception
            DB::statement("SELECT RELEASE_LOCK(?)", [$lockName]);
        }
    }

    /**
     * Get the last sequential number for a given prefix and year.
     *
     * @param string $prefix
     * @param string $tahun
     * @return int
     */
    public function getLastSequentialNumber(string $prefix, string $tahun): int
    {
        // Cari nomor surat terakhir dengan prefix dan tahun yang sesuai
        $lastDoc = ESign::where('nomor_surat', 'LIKE', "{$prefix}/{$tahun}/%")
            ->orderBy('nomor_surat', 'desc')
            ->lockForUpdate()
            ->first();

        if (!$lastDoc) {
            return 0;
        }

        // Ekstrak nomor urut dari format {PREFIX}/{TAHUN}/{URUTAN}
        $parts = explode('/', $lastDoc->nomor_surat);
        $lastNumber = (int) end($parts);

        return $lastNumber;
    }

    /**
     * Get the prefix for a given letter type slug.
     *
     * @param string $jenisSuratSlug
     * @return string
     */
    public function getPrefix(string $jenisSuratSlug): string
    {
        $prefixes = config('esign.prefixes', []);
        $fallback = config('esign.fallback_prefix', 'DOC');

        return $prefixes[$jenisSuratSlug] ?? $fallback;
    }
}
