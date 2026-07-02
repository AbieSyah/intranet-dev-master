<?php

namespace App\Imports;

use App\Models\Medical;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MedicalFaalImport implements ToModel, WithHeadingRow
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
            'fh_sgot' => $row['fh_sgot'] ?? '-',
            'fh_sgpt' => $row['fh_sgpt'] ?? '-',
            'fl_kolesterol_total' => $row['fl_kolesterol_total'] ?? '-',
            'fl_hdl_kolesterol' => $row['fl_hdl_kolesterol'] ?? '-',
            'fl_ldl_kolesterol' => $row['fl_ldl_kolesterol'] ?? '-',
            'fl_trigliserida' => $row['fl_trigliserida'] ?? '-',
            'gd_glukosa_puasa' => $row['gd_glukosa_puasa'] ?? '-',
            'gd_jpp' => $row['gd_jpp'] ?? '-',
            'fg_bun' => $row['fg_bun'] ?? '-',
            'fg_ureum' => $row['fg_ureum'] ?? '-',
            'fg_kreatinin' => $row['fg_kreatinin'] ?? '-',
            'fg_egfr' => $row['fg_egfr'] ?? '-',
            'asam_urat' => $row['asam_urat'] ?? '-',
            'hbsag' => $row['hbsag'] ?? '-',
        ]);
    }

    public function headingRow(): int
    {
        return 9;
    }
}
