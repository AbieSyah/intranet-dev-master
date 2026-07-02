<?php

namespace App\Imports;

use App\Models\Medical;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MedicalHematologiImport implements ToModel, WithHeadingRow
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
            'hm_hemoglobin' => $row['hm_hemoglobin'] ?? '-',
            'hm_eritrosit' => $row['hm_eritrosit'] ?? '-',
            'hm_hematokrit' => $row['hm_hematokrit'] ?? '-',
            'hm_mcv' => $row['hm_mcv'] ?? '-',
            'hm_mch' => $row['hm_mch'] ?? '-',
            'hm_mchc' => $row['hm_mchc'] ?? '-',
            'hm_rdw' => $row['hm_rdw'] ?? '-',
            'hm_leukosit' => $row['hm_leukosit'] ?? '-',
            'hm_eos' => $row['hm_eos'] ?? '-',
            'hm_baso' => $row['hm_baso'] ?? '-',
            'hm_neutro' => $row['hm_neutro'] ?? '-',
            'hm_limfo' => $row['hm_limfo'] ?? '-',
            'hm_mono' => $row['hm_mono'] ?? '-',
            'hm_eos_absolut' => $row['hm_eos_absolut'] ?? '-',
            'hm_baso_absolut' => $row['hm_baso_absolut'] ?? '-',
            'hm_neutro_absolut' => $row['hm_neutro_absolut'] ?? '-',
            'hm_limfo_absolut' => $row['hm_limfo_absolut'] ?? '-',
            'hm_mono_absolut' => $row['hm_mono_absolut'] ?? '-',
            'hm_trombosit' => $row['hm_trombosit'] ?? '-',
            'hm_led' => $row['hm_led'] ?? '-',
        ]);
        
    }

    public function headingRow(): int
    {
        return 9;
    }
}
