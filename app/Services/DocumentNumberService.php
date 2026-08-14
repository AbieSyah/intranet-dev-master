<?php

namespace App\Services;

use App\Models\ESign;
use App\Models\ESignBatch;
use Illuminate\Support\Facades\DB;

class DocumentNumberService
{
    /**
     * Generate nomor surat untuk satu dokumen (saat surat dirilis/dikirim).
     *
     * Format tetap: {URUTAN}/ASK/HRD/{BULAN}/{TAHUN}
     * Contoh: 001/ASK/HRD/08/2026
     *
     * Kode 'ASK' dan 'HRD' di-hardcode. Urutan bersifat GLOBAL (satu urutan
     * untuk semua jenis surat) dan di-reset setiap tahun (saat tahun berganti).
     *
     * Method ini TIDAK membungkus diri dengan DB::transaction() karena
     * harus dipanggil dari DALAM transaksi yang sudah dibuka oleh pemanggil
     * (ESignService).
     *
     * @param string $jenisSuratSlug
     * @return string nomor_surat generated
     *
     * @throws \RuntimeException jika gagal mendapatkan lock
     */
    public function generateNextNumber(string $jenisSuratSlug): string
    {
        $tahun = now()->format('Y');
        $bulan = now()->format('m');
        $lockName = 'esign_number_global_' . $tahun;

        // Ambil lock — timeout 10 detik
        $gotLock = DB::select("SELECT GET_LOCK(?, 10) AS locked", [$lockName]);
        $locked = (int) ($gotLock[0]->locked ?? 0);

        if (!$locked) {
            throw new \RuntimeException(
                "Gagal mendapatkan lock untuk generate nomor surat tahun {$tahun}. " .
                "Silakan coba lagi."
            );
        }

        try {
            // Urutan global: 001/ASK/HRD/BULAN/TAHUN, reset tiap tahun
            $lastSeq = $this->getLastGlobalSequence($tahun);
            $seq = $lastSeq + 1;

            return sprintf('%03d/ASK/HRD/%s/%s', $seq, $bulan, $tahun);
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
     * Generate the next batch number for a given letter type slug.
     *
     * Format: {PREFIX}/{DEPT}/{BULAN}/{TAHUN}/{URUTAN}
     * Contoh: PKWT/HRD/07/2026/001
     *
     * Dept default dari config 'esign.batch_dept_segment' (default 'HRD').
     * Urutan di-reset setiap bulan (per prefix+dept+bulan+tahun) agar unik.
     * Memakai advisory lock (GET_LOCK) untuk mencegah duplikasi bersamaan.
     * Metode TIDAK membungkus dengan transaksi — harus dipanggil dari dalam
     * transaksi yang sudah dibuka oleh pemanggil (ESignService::sendBatch).
     *
     * @param string $jenisSuratSlug
     * @return string nomor batch yang di-generate
     *
     * @throws \RuntimeException jika gagal mendapatkan lock
     */
    public function generateNextBatchNumber(string $jenisSuratSlug): string
    {
        $tahun = now()->format('Y');
        $bulan = now()->format('m');

        // Gunakan lock & urutan global yang sama dengan surat tunggal
        $lockName = 'esign_number_global_' . $tahun;

        // Ambil lock — timeout 10 detik
        $gotLock = DB::select("SELECT GET_LOCK(?, 10) AS locked", [$lockName]);
        $locked = (int) ($gotLock[0]->locked ?? 0);

        if (!$locked) {
            throw new \RuntimeException(
                "Gagal mendapatkan lock untuk generate nomor batch tahun {$tahun}. " .
                "Silakan coba lagi."
            );
        }

        try {
            // Urutan global: 001/ASK/HRD/BULAN/TAHUN, reset tiap tahun
            $lastSeq = $this->getLastGlobalSequence($tahun);
            $seq = $lastSeq + 1;

            return sprintf('%03d/ASK/HRD/%s/%s', $seq, $bulan, $tahun);
        } finally {
            // Lepas lock — dijamin jalan walaupun terjadi exception
            DB::statement("SELECT RELEASE_LOCK(?)", [$lockName]);
        }
    }

    /**
     * Cari nomor urut global terakhir untuk suatu tahun pada format 001/ASK/HRD/MM/YYYY.
     * Urutan dihitung dari baris depan (segmen pertama) nomor surat.
     * Nomor batch juga ikut dihitung agar satu urutan global untuk semua surat.
     */
    public function getLastGlobalSequence(string $tahun): int
    {
        $pattern = '%/ASK/HRD/%/' . $tahun;

        $docs = ESign::where('nomor_surat', 'LIKE', $pattern)
            ->lockForUpdate()
            ->pluck('nomor_surat');

        $batches = ESignBatch::where('nomor_surat', 'LIKE', $pattern)
            ->lockForUpdate()
            ->pluck('nomor_surat');

        $max = 0;
        foreach ($docs->merge($batches) as $n) {
            $seq = (int) explode('/', $n)[0];
            if ($seq > $max) {
                $max = $seq;
            }
        }

        return $max;
    }

    /**
     * Get the prefix for a given letter type slug.
     *
     * Prioritas:
     * 1. Field `prefix` pada tabel `letter_types` (data master jenis surat).
     * 2. Konfigurasi `esign.prefixes` (fallback untuk slug yang belum ada di master).
     * 3. `esign.fallback_prefix` (default 'DOC').
     *
     * @param string $jenisSuratSlug
     * @return string
     */
    public function getPrefix(string $jenisSuratSlug): string
    {
        $letterType = \App\Models\LetterType::where('slug', $jenisSuratSlug)->first();
        if ($letterType && !empty($letterType->prefix)) {
            return strtoupper($letterType->prefix);
        }

        $prefixes = config('esign.prefixes', []);
        return $prefixes[$jenisSuratSlug] ?? config('esign.fallback_prefix', 'DOC');
    }
}
