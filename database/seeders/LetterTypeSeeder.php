<?php

namespace Database\Seeders;

use App\Models\LetterType;
use Illuminate\Database\Seeder;

class LetterTypeSeeder extends Seeder
{
    /**
     * Seed the letter_types table from config/esign.php prefixes.
     */
    public function run(): void
    {
        $prefixes = config('esign.prefixes', []);
        $icons = [
            'pkwt' => 'ri-file-text-line',
            'promosi' => 'ri-arrow-up-line',
            'mutasi' => 'ri-swap-line',
            'demosi' => 'ri-arrow-down-line',
            'perpanjangan-pkwt' => 'ri-file-copy-2-line',
            'pengangkatan' => 'ri-user-star-line',
            'surat-peringatan' => 'ri-alert-line',
        ];
        $colors = [
            'pkwt' => 'primary',
            'promosi' => 'success',
            'mutasi' => 'info',
            'demosi' => 'warning',
            'perpanjangan-pkwt' => 'primary',
            'pengangkatan' => 'success',
            'surat-peringatan' => 'danger',
        ];
        $names = [
            'pkwt' => 'PKWT',
            'promosi' => 'Promosi',
            'mutasi' => 'Mutasi',
            'demosi' => 'Demosi',
            'perpanjangan-pkwt' => 'Perpanjangan PKWT',
            'pengangkatan' => 'Pengangkatan',
            'surat-peringatan' => 'Surat Peringatan',
        ];
        $descriptions = [
            'pkwt' => 'Perjanjian Kerja Waktu Tertentu',
            'promosi' => 'Surat Kenaikan Jabatan',
            'mutasi' => 'Surat Mutasi Karyawan',
            'demosi' => 'Surat Penurunan Jabatan',
            'perpanjangan-pkwt' => 'Perpanjangan Masa Kontrak PKWT',
            'pengangkatan' => 'Surat Pengangkatan Karyawan',
            'surat-peringatan' => 'Surat Peringatan Karyawan',
        ];

        foreach ($prefixes as $slug => $prefix) {
            LetterType::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $names[$slug] ?? ucwords(str_replace('-', ' ', $slug)),
                    'prefix' => $prefix,
                    'description' => $descriptions[$slug] ?? null,
                    'icon' => $icons[$slug] ?? 'ri-file-text-line',
                    'color' => $colors[$slug] ?? 'primary',
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Letter types seeded: ' . count($prefixes) . ' types created/updated.');
    }
}
