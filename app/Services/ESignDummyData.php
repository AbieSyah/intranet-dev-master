<?php

namespace App\Services;

use App\Models\Employee;

class ESignDummyData
{
    public static function getDocuments(): array
    {
        return self::generateDocuments();
    }

    public static function getByStatus(string $status): array
    {
        return array_values(array_filter(self::getDocuments(), fn($d) => $d['status'] === $status));
    }

    public static function getCounts(): array
    {
        return [
            'total' => 100,
            'draft' => 30,
            'waiting' => 15,
            'signed' => 45,
            'rejected' => 10,
        ];
    }

    private static function getStatusDistribution(): array
    {
        $statuses = [];
        for ($i = 0; $i < 30; $i++) $statuses[] = 'Draft';
        for ($i = 0; $i < 15; $i++) $statuses[] = 'Waiting Signature';
        for ($i = 0; $i < 45; $i++) $statuses[] = 'Signed';
        for ($i = 0; $i < 10; $i++) $statuses[] = 'Rejected';
        return $statuses;
    }

    private static function getLetterTypeDefs(): array
    {
        return [
            ['prefix' => 'PKWT', 'name' => 'PKWT', 'count' => 25],
            ['prefix' => 'PROM', 'name' => 'Promosi', 'count' => 18],
            ['prefix' => 'MUT', 'name' => 'Mutasi', 'count' => 14],
            ['prefix' => 'DEM', 'name' => 'Demosi', 'count' => 8],
            ['prefix' => 'PPKWT', 'name' => 'Perpanjangan PKWT', 'count' => 13],
            ['prefix' => 'ANGKAT', 'name' => 'Pengangkatan Karyawan Tetap', 'count' => 12],
            ['prefix' => 'SP', 'name' => 'Surat Peringatan', 'count' => 10],
        ];
    }

    public static function getLetterTypes(): array
    {
        return [
            ['slug' => 'pkwt', 'icon' => 'ri-file-text-line', 'color' => 'primary', 'name' => 'PKWT', 'desc' => 'Perjanjian Kerja Waktu Tertentu'],
            ['slug' => 'promosi', 'icon' => 'ri-arrow-up-circle-line', 'color' => 'success', 'name' => 'Promosi', 'desc' => 'Surat Promosi Jabatan'],
            ['slug' => 'mutasi', 'icon' => 'ri-swap-line', 'color' => 'info', 'name' => 'Mutasi', 'desc' => 'Surat Mutasi Karyawan'],
            ['slug' => 'demosi', 'icon' => 'ri-arrow-down-circle-line', 'color' => 'warning', 'name' => 'Demosi', 'desc' => 'Surat Penurunan Jabatan'],
            ['slug' => 'perpanjangan-pkwt', 'icon' => 'ri-refresh-line', 'color' => 'purple', 'name' => 'Perpanjangan PKWT', 'desc' => 'Perpanjangan Perjanjian Kerja Waktu Tertentu'],
            ['slug' => 'pengangkatan', 'icon' => 'ri-user-star-line', 'color' => 'secondary', 'name' => 'Pengangkatan Karyawan Tetap', 'desc' => 'Surat Pengangkatan Karyawan Tetap'],
            ['slug' => 'surat-peringatan', 'icon' => 'ri-alert-line', 'color' => 'danger', 'name' => 'Surat Peringatan', 'desc' => 'Surat Peringatan Karyawan'],
        ];
    }

    public static function getLetterType(string $slug): ?array
    {
        foreach (self::getLetterTypes() as $type) {
            if ($type['slug'] === $slug) return $type;
        }
        return null;
    }

    public static function getTypeCounts(): array
    {
        return [
            'pkwt' => 25,
            'promosi' => 18,
            'mutasi' => 14,
            'demosi' => 8,
            'perpanjangan-pkwt' => 13,
            'pengangkatan' => 12,
            'surat-peringatan' => 10,
        ];
    }

    private static function generateDocuments(): array
    {
        $statuses = self::getStatusDistribution();
        $letterTypes = self::getLetterTypeDefs();

        $employees = Employee::with('department', 'position')
            ->whereNotNull('fullname')->where('fullname', '!=', '')
            ->whereNotNull('nik')->where('nik', '!=', '')
            ->orderBy('id')
            ->get();

        $documents = [];
        $docIndex = 0;
        $numberCounters = [];
        foreach ($letterTypes as $type) {
            $numberCounters[$type['prefix']] = 0;
        }

        $dates = ['01 Jul 2026','02 Jul 2026','03 Jul 2026','04 Jul 2026','05 Jul 2026','06 Jul 2026','28 Jun 2026','29 Jun 2026','30 Jun 2026'];

        foreach ($letterTypes as $type) {
            for ($i = 0; $i < $type['count']; $i++) {
                $numberCounters[$type['prefix']]++;
                $emp = $employees->isNotEmpty()
                    ? $employees[$docIndex % $employees->count()]
                    : null;
                $status = $statuses[$docIndex];
                $date = $dates[$docIndex % count($dates)];

                $documents[] = [
                    'id' => $docIndex + 1,
                    'nomor_surat' => sprintf('%s/2026/%03d', $type['prefix'], $numberCounters[$type['prefix']]),
                    'jenis_surat' => $type['name'],
                    'nik' => $emp?->nik ?? '23010' . str_pad((string)($docIndex + 1), 3, '0', STR_PAD_LEFT),
                    'nama' => $emp?->fullname ?? 'Employee ' . ($docIndex + 1),
                    'departemen' => $emp?->department?->name ?? 'HRD & GA',
                    'jabatan' => $emp?->position?->nama ?? 'Staff',
                    'tanggal' => $date,
                    'status' => $status,
                ];
                $docIndex++;
            }
        }

        return $documents;
    }
}
