<?php

namespace App\Exports;

use App\Models\Medical;
use App\Models\Employee;
use App\Models\Tempmedical;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;

class RegulerMCUExport implements FromView
{
    private $id;

    function __construct(int $id) {
        $this->id = $id;
    }

    public function view(): View
    {
        $temp_mcu = Tempmedical::find($this->id);
        $start_year = Carbon::create($temp_mcu->tanggal_awal)->subYear(2)->format('Y'); 
        $selected_year = date('Y', strtotime($temp_mcu->tanggal_awal));

        $query = Medical::whereYear('tanggal_mcu', '>=', $start_year)->whereYear('tanggal_mcu', '<=', $selected_year)->whereNotNull('id_template')->get()->unique('id_employees')->pluck('id_employees');
        foreach($query as $id_employees){
            $emp = Employee::find($id_employees);
            $data['nama'] = $emp->fullname;
            $data['lokasi'] = $emp->area->name;
            $data['bagian'] = $emp->department->name;
            $medical = Medical::where('id_employees', $id_employees)->whereYear('tanggal_mcu', '>=', $start_year)->whereYear('tanggal_mcu', '<=', $selected_year)->whereNotNull('id_template')->get();
            $tahun = [];
            $kriteria = [];
            $kesimpulan = [];
            foreach($medical as $mcu){
                $count = $medical->count();
                if($count == 3){
                    //tahun
                    $arr_tahun = date('Y', strtotime($mcu->tanggal_mcu));
                    $tahun[] = $arr_tahun;
                    //kriteria
                    $arr_kriteria = $mcu->kriteria_sehat;
                    $kriteria[] = $arr_kriteria;
                    //kesimpulan
                    $arr_kesimpulan = $mcu->kesimpulan;
                    $kesimpulan[] = $arr_kesimpulan;
                }elseif($count == 2){
                    $arr_tahun = date('Y', strtotime($mcu->tanggal_mcu));
                    $tahun[] = $arr_tahun;
                    array_push($tahun, "-");
                    unset($tahun[3]);
                    //kriteria
                    $arr_kriteria = $mcu->kriteria_sehat;
                    $kriteria[] = $arr_kriteria;
                    array_push($kriteria, "-");
                    unset($kriteria[3]);
                    //kesimpulan
                    $arr_kesimpulan = $mcu->kesimpulan;
                    $kesimpulan[] = $arr_kesimpulan;
                    array_push($kesimpulan, "-");
                    unset($kesimpulan[3]);
                }else{
                    $arr_tahun = date('Y', strtotime($mcu->tanggal_mcu));
                    $tahun[] = $arr_tahun;
                    array_push($tahun, "-","-");
                    //kriteria
                    $arr_kriteria = $mcu->kriteria_sehat;
                    $kriteria[] = $arr_kriteria;
                    array_push($kriteria, "-","-");
                    //kesimpulan
                    $arr_kesimpulan = $mcu->kesimpulan;
                    $kesimpulan[] = $arr_kesimpulan;
                    array_push($kesimpulan, "-","-");
                }                 
            } 
            arsort($tahun);       
            arsort($kriteria);       
            arsort($kesimpulan);       
            $data['tahun'] = $tahun;
            $data['kriteria'] = $kriteria;
            $data['kesimpulan'] = $kesimpulan;
            $data_export[] = $data;
        }
        return view('pages.hrd.medical.reguler.export', [
            'query' => $data_export
        ]);
    }
}
