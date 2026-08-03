<?php

namespace Database\Seeders;

use App\Models\ESignTemplate;
use Illuminate\Database\Seeder;

class ESignTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $template = ESignTemplate::firstOrNew(['jenis_surat_slug' => 'pkwt']);

        if (!$template->exists) {
            $template->fill([
                'title' => 'Perjanjian Kerja Waktu Tertentu',
                'content' => 'PT HISAMITSU PHARMA INDONESIA

Nomor:
{{nomor_surat}}

Yang bertanda tangan:

Nama:
{{employee_name}}

NIK:
{{employee_nik}}

Departemen:
{{department}}

Jabatan:
{{employee_position}}

Masa kontrak:

{{tanggal_mulai}}
sampai
{{tanggal_akhir}}

Demikian surat ini dibuat.

Ini hanya contoh dummy.',
                'version' => 1,
                'is_active' => true,
            ]);
            $template->save();
            $this->command->info('Template PKWT berhasil dibuat.');
        } else {
            $this->command->info('Template PKWT sudah ada.');
        }
    }
}
