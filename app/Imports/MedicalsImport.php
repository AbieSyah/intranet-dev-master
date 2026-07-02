<?php

namespace App\Imports;

use App\Models\Medical;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MedicalsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Medical([
            'no_lab' => $row['no_lab'] ?? '-',
            'nama' => $row['nama'] ?? '-',
            'jk' => $row['jk'] ?? '-',
            'umur' => $row['umur'] ?? '-',
            'lab' => $row['lab'] ?? '-',
            'foto_thorax' => $row['foto_thorax'] ?? '-',
            'ekg' => $row['ekg'] ?? '-',
            'audiometri' => $row['audiometri'] ?? '-',
            'fisik_dokter' => $row['fisik_dokter'] ?? '-',
            'kesimpulan' => $row['kesimpulan'] ?? '-',
            'saran' => $row['saran'] ?? '-',
            'skor_framigham' => $row['skor_framigham'] ?? '-',
            'kriteria_sehat' => $row['kriteria_sehat'] ?? '-',
            'id_template' => $row['id_template'] ?? '-',
            'tanggal_mcu' => $row['tanggal_mcu'] ?? '-',
        ]);
    }

    public function headingRow(): int
    {
        return 6;
    }
}
