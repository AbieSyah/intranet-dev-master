<?php

namespace App\Exports;

use App\Models\Clinic\Patient;
use App\Models\Clinic\Trkeluar;
use App\Models\Master\Drug;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Carbon\Carbon;

class PatientExport implements FromView
{
    private $data;

    function __construct(array $data = []) {
        $this->data = $data;
    }
    public function view(): View
    {
        $query = Patient::whereMonth('visit_date', $this->data['bulan'])->whereYear('visit_date', $this->data['tahun'])->get();
        $no = 0;
        foreach($query as $qry){
            $no++;
            $tr_keluar = Trkeluar::where('kode',$qry->kode)->get();
            $data['no'] = $no;
            $data['visit_date'] = $qry->visit_date ?? '-';
            $data['id_employee'] = $qry->employee->fullname ?? '-';
            $data['diagnosa'] = $qry->diagnosa ?? '-';
            $data['keluhan'] = $qry->keluhan ?? '-';
            $data['tensi'] = $qry->tensi ?? '-';
            $data['keterangan'] = $qry->keterangan ?? '-';
            $data['doctor'] = $qry->doctor->nama ?? '-';
            if($tr_keluar->isNotEmpty()){
                $arr_drug = $tr_keluar->pluck('jml_drug','id_drug');
                $arr_data = [];
                foreach($arr_drug as $key => $value){
                    $drug = Drug::find($key);
                    $arr_data[] = $drug->nama.' ('.$value.' pcs)';
                }
                $data['obat'] = implode("; ",$arr_data) ?? '-';
            }else{
                $data['obat'] = '-';
            }
            $data_all[] = $data;
        }
        return view('pages.hrd.clinic.pasien.export',['query' => $data_all]);
    }
}
