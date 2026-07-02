<?php

namespace App\Imports;

use App\Models\Medical;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MedicalUrineImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Medical([
            'no_lab' => $row['no_lab'] ?? '-',
            'nama' => $row['nama'] ?? '-',
            'umur' => $row['umur'] ?? '-',
            'u_warna' => $row['u_warna'] ?? '-',
            'u_kejernihan' => $row['u_kejernihan'] ?? '-',
            'u_berat_jenis' => $row['u_berat_jenis'] ?? '-',
            'u_ph' => $row['u_ph'] ?? '-',
            'u_protein_albumin' => $row['u_protein_albumin'] ?? '-',
            'u_glukosa' => $row['u_glukosa'] ?? '-',
            'u_keton' => $row['u_keton'] ?? '-',
            'u_bilirubin' => $row['u_bilirubin'] ?? '-',
            'u_urobilinogen' => $row['u_urobilinogen'] ?? '-',
            'u_nitrit' => $row['u_nitrit'] ?? '-',
            'u_leukosit_esterase' => $row['u_leukosit_esterase'] ?? '-',
            'u_darah_haem' => $row['u_darah_haem'] ?? '-',
            'u_eri' => $row['u_eri'] ?? '-',
            'u_leuko' => $row['u_leuko'] ?? '-',
            'u_epithel' => $row['u_epithel'] ?? '-',
            'u_silinder' => $row['u_silinder'] ?? '-',
            'u_kristal' => $row['u_kristal'] ?? '-',
            'u_lain' => $row['u_lain'] ?? '-',
        ]);
    }

    public function headingRow(): int
    {
        return 9;
    }
}
