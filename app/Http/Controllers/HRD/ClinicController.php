<?php

namespace App\Http\Controllers\HRD;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Models\Medical;
use App\Models\Lab;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PatientExport;
use App\Models\Clinic\Trmasuk;
use App\Models\Clinic\Trkeluar;
use App\Models\Clinic\Patient;
use App\Models\Clinic\Prestock;
use App\Models\Master\Doctoraccount;
use App\Models\Master\Drug;
use App\Models\Employee;
use App\Models\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Response;
use Yajra\DataTables\Facades\DataTables;

class ClinicController extends Controller
{
    //start stock
    public function index_stock(Request $request){
        //prev date
        $prev_date = date('Y-m', strtotime("-1 months"));
        $prev_month = date('m', strtotime($prev_date));
        $prev_year = date('Y', strtotime($prev_date));
        //next date
        $next_date = date('Y-m');
        $next_month = date('m', strtotime($next_date));
        $next_year = date('Y', strtotime($next_date));

        $query = Prestock::whereYear('tanggal', $prev_year)->whereMonth('tanggal', $prev_month)->get();
        if($query->isNotEmpty()){
            foreach($query as $qry){
                $jml_in = Trmasuk::where('id_drug', $qry->id_drug)
                    ->whereYear('tr_tanggal', $next_year)
                    ->whereMonth('tr_tanggal', $next_month)
                    ->sum('jml_drug');
                $jml_out = Trkeluar::where('id_drug', $qry->id_drug)
                    ->whereYear('tr_tanggal', $next_year)
                    ->whereMonth('tr_tanggal', $next_month)
                    ->sum('jml_drug');
                $index = $qry->id;
                $document[$index] = array();
                $document[$index]['drug'] = $qry->drug->nama;
                $document[$index]['prestock'] = $qry->jml_drug;
                $document[$index]['in'] = $jml_in;
                $document[$index]['out'] = $jml_out;
                $document[$index]['ending'] = ($qry->jml_drug+$jml_in)-$jml_out;
            }
        }else{
            $document = array();
        }
        if ($request->ajax()) {
            return DataTables::of($document)
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.clinic.stock.index');
    }
    //end stock

    //start transaction in
    public function index_masuk(Request $request){
        $drugs = Drug::get();
        $query = Trmasuk::orderBy('tr_tanggal', 'desc')->get();
        if($query->isNotEmpty()){
            foreach($query as $qry){
                $index = $qry->id;
                $document[$index] = array();
                $document[$index]['id'] = $qry->id;
                $document[$index]['kategori'] = $qry->kategori;
                $document[$index]['tr_tanggal'] = $qry->tr_tanggal;
                $document[$index]['id_drug'] = $qry->drug->nama;
                $document[$index]['jml_drug'] = $qry->jml_drug;
            }
        }else{
            $document = array();
        }
        if ($request->ajax()) {
            return DataTables::of($document)
                ->addColumn('action', function ($document) {
                    if(\Auth::user()->can('hrd.clinic.masuk.delete')){
                        $button = '<button data-toggle="tooltip" title="Hapus" data-id="' . encrypt($document['id']) . '" data-original-title="Hapus" class="btn btn-danger btn-sm delete-btn"><i class="ri-delete-bin-line"></i></button>';
                    }else{
                        $button = '-';
                    }
                    return $button;
                })
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.clinic.masuk.index', compact('drugs'));
    }

    public function create_masuk(Request $request){
        $user = auth()->user();
        $drugs = Drug::get();
        return view('pages.hrd.clinic.masuk.create', compact('user','drugs'));
    }

    public function store_masuk(Request $request){
        $user = auth()->user();
        $date_now = Carbon::now()->format('Y-m-d H:i:s');
        $kode = strtotime($date_now);
        for($i = 0; $i < count($request->id_drug); $i++){
            $insert[] = [
                'kategori' => 'MASUK',
                'tr_tanggal' => $request->tr_tanggal,
                'kode' => $kode,
                'id_drug' => $request->id_drug[$i],
                'jml_drug' =>$request->jml_drug[$i],
                'id_user' => $request->id_user,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }
        //insert drug in
        $post = Trmasuk::insert($insert);
        //insert log user activity
        $insert_log = new Log;
        $insert_log->user_id = $user->id;
        $insert_log->ip_address = $request->ip();
        $insert_log->action = 'insert';
        $insert_log->description = 'Create new transaction in kode'.'"'.$kode.'" date "'.$request->tr_tanggal.'"';
        $insert_log->save();

        return redirect()->route('clinic.masuk.index')->with('success', 'Add transaction in successfully.');
    }
    //end transaction in

    public function destroy_masuk(Request $request){
        DB::beginTransaction();

        try {
            $user = auth()->user();
            $id = decrypt($request->id);
            $query = Trmasuk::find($id);
            //insert log user activity
            $insert_log = new Log;
            $insert_log->user_id = $user->id;
            $insert_log->ip_address = $request->ip();
            $insert_log->action = 'delete';
            $insert_log->description = 'Delete transaction in '.'"'.$query->drug->nama.'" date "'.$query->tr_tanggal.'"';
            $insert_log->save();

            $delete = Trmasuk::where('id', $id)->delete();
            
            DB::commit();

            return response()->json(['message' => "Transaction has been removed"], 200);

        }catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    //start transaction out
    public function index_keluar(Request $request){
        $year = date('Y');
        $min = $year - 3;
        $max = $year;
        $month = date('m');
        
        $drugs = Drug::get();
        $employees = Employee::get();
        if(!empty($request->bulan) && !empty($request->tahun)){
            $form_bulan = $request->bulan;
            $form_tahun = $request->tahun;
            $query = Trkeluar::whereYear('tr_tanggal', $form_tahun)->whereMonth('tr_tanggal', $form_bulan)->orderBy('tr_tanggal', 'desc')->get();
        }else{
            $query = Trkeluar::whereYear('tr_tanggal', $year)->whereMonth('tr_tanggal', $month)->orderBy('tr_tanggal', 'desc')->get();
        }
        if($query->isNotEmpty()){
            foreach($query as $qry){
                $index = $qry->id;
                $document[$index] = array();
                $document[$index]['id'] = $qry->id;
                $document[$index]['kategori'] = $qry->kategori;
                $document[$index]['tr_tanggal'] = $qry->tr_tanggal;
                $document[$index]['id_employee'] = $qry->employee->fullname ?? '-';
                $document[$index]['id_drug'] = $qry->drug->nama;
                $document[$index]['jml_drug'] = $qry->jml_drug;
            }
        }else{
            $document = array();
        }
        if ($request->ajax()) {
            return DataTables::of($document)
                ->addColumn('action', function ($document) {
                    if(\Auth::user()->can('hrd.clinic.keluar.delete')){
                        $button = '<button data-toggle="tooltip" title="Hapus" data-id="' . encrypt($document['id']) . '" data-original-title="Hapus" class="btn btn-danger btn-sm delete-btn"><i class="ri-delete-bin-line"></i></button>';
                    }else{
                        $button = '-';
                    }
                    return $button;
                })
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.clinic.keluar.index', compact('drugs','employees','min','max','month','year'));
    }

    public function create_keluar(Request $request){
        $user = auth()->user();
        $drugs = Drug::get();
        return view('pages.hrd.clinic.keluar.create', compact('user','drugs'));
    }

    public function store_keluar(Request $request){
        $user = auth()->user();
        $date_now = Carbon::now()->format('Y-m-d H:i:s');
        $kode = strtotime($date_now);
        for($i = 0; $i < count($request->id_drug); $i++){
            $insert[] = [
                'kategori' => 'KELUAR',
                'tr_tanggal' => $request->tr_tanggal,
                'kode' => $kode,
                'id_drug' => $request->id_drug[$i],
                'jml_drug' => $request->jml_drug[$i],
                'id_user' => $request->id_user,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
        }
        //insert drug out
        $post = Trkeluar::insert($insert);
        //insert log user activity
        $insert_log = new Log;
        $insert_log->user_id = $user->id;
        $insert_log->ip_address = $request->ip();
        $insert_log->action = 'insert';
        $insert_log->description = 'Create new transaction out kode'.'"'.$kode.'" date "'.$request->tr_tanggal.'"';
        $insert_log->save();

        return redirect()->route('clinic.keluar.index')->with('success', 'Add transaction out successfully.');
    }

    public function destroy_keluar(Request $request){
        DB::beginTransaction();

        try {
            $user = auth()->user();
            $id = decrypt($request->id);
            $query = Trkeluar::find($id);
            //insert log user activity
            $insert_log = new Log;
            $insert_log->user_id = $user->id;
            $insert_log->ip_address = $request->ip();
            $insert_log->action = 'delete';
            $insert_log->description = 'Delete transaction out '.'"'.$query->drug->nama.'" date "'.$query->tr_tanggal.'"';
            $insert_log->save();

            $delete = Trkeluar::where('id', $id)->delete();
            
            DB::commit();

            return response()->json(['message' => "Transaction has been removed"], 200);

        }catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }
    //end transaction out

    //start transaction patient
    public function index_patient(Request $request){
        $user = auth()->user();
        $year = date('Y');
        $min = $year - 3;
        $max = $year;
        $month = date('m');
        $doctors = Doctoraccount::get();

        if(!empty($request->bulan) && !empty($request->tahun)){
            $form_bulan = $request->bulan;
            $form_tahun = $request->tahun;
            $query = Patient::whereYear('visit_date', $form_tahun)->whereMonth('visit_date', $form_bulan)->orderBy('visit_date', 'desc')->get();
        }else{
            $query = Patient::whereYear('visit_date', $year)->whereMonth('visit_date', $month)->orderBy('visit_date', 'desc')->get();
        }

        if($query->isNotEmpty()){
            foreach($query as $qry){
                $index = $qry->id;
                $document[$index] = array();
                $document[$index]['id'] = $qry->id;
                $document[$index]['kode'] = $qry->kode;
                $document[$index]['id_dokter'] = $qry->doctor->nama ?? '-';
                $document[$index]['visit_date'] = $qry->visit_date;
                $document[$index]['nik'] = $qry->employee->nik ?? '-';
                $document[$index]['id_employee'] = $qry->employee->fullname ?? '-';
                $document[$index]['diagnosa'] = $qry->diagnosa ?? '-';
                $document[$index]['keluhan'] = $qry->keluhan ?? '-';
                $document[$index]['tensi'] = $qry->tensi ?? '-';
                $document[$index]['keterangan'] = $qry->keterangan ?? '-';
            }
        }else{
            $document = array();
        }
        if ($request->ajax()) {
            return DataTables::of($document)
                ->addColumn('action', function ($document) {
                    if(\Auth::user()->can('hrd.clinic.patient.delete')){
                        $button = '<button data-toggle="tooltip" title="Hapus" data-id="' . encrypt($document['id']) . '" data-original-title="Hapus" class="btn btn-danger btn-sm delete-btn"><i class="ri-delete-bin-line"></i></button>';
                    }else{
                        $button = '-';
                    }
                    return $button;
                })
                ->addColumn('medicine', function($document){
                    $query = Trkeluar::where('kode',$document['kode'])->get();
                    if($query->isNotEmpty()){
                        $medicine = '<div class="col-lg-12"><table class="table table-bordered" style="table-layout: fixed; width:100%;">';
                        $medicine .= '
                            <thead>
                                <tr>
                                    <th colspan="2" style="text-align: center;">Penggunaan Obat</th>
                                </tr>
                                <tr>
                                    <th style="text-align: center;">Medicine</th>
                                    <th style="text-align: center;">Qty</th>
                                </tr>
                            </thead>
                            ';
                        $medicine .= '<tbody>';
                        foreach($query as $qry){
                            $medicine .= '<tr>';                  
                            $medicine .= '<td>'.$qry->drug->nama.'</td>';     
                            $medicine .= '<td>'.$qry->jml_drug.'</td>';    
                            $medicine .= '</tr>';
                        } 
                        $medicine .= '</tbody></table></div>';
                    }else{
                        $medicine = '';
                    }
                    return $medicine;
                })
                ->rawColumns(['action','medicine'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('pages.hrd.clinic.pasien.index', compact('user','min','max','month','year','doctors'));
    }

    public function create_patient(Request $request){
        $user = auth()->user();
        $drugs = Drug::get();
        $employees = Employee::whereNot('status', 'TERMINATED')->get();
        
        return view('pages.hrd.clinic.pasien.create', compact('user','drugs','employees'));
    }

    public function medical_year_patient(Request $request){
        //medical checkup
        $date = Carbon::now()->format('Y-m-d');
        $medical = Medical::where('id_employees', $request->id_emp)->latest('tanggal_mcu')->first();
        if(!empty($medical)){
            $lab = Lab::where('id_vendor', $medical->id_vendor)->get()->pluck('nilai_rujukan', 'pemeriksaan');
        }else{
            $lab = array();
        }
        if(!empty($medical->tanggal_mcu)){
            $latest_mcu = date('Y', strtotime($medical->tanggal_mcu));
        }else{
            $latest_mcu = array();
        }
        // dd($latest_mcu);
        $tanggal_mcu = Medical::where('id_employees', $request->id_emp)->get()->pluck('tanggal_mcu');
        if($tanggal_mcu->isNotEmpty()){
            foreach($tanggal_mcu as $key_mcu => $val_mcu){
                if(!empty($val_mcu)){
                    $list_tanggal[] = date('Y', strtotime($val_mcu)); 
                }
            }
            $arr_tanggal = array_unique($list_tanggal);
        }else{
            $arr_tanggal = array();
        }
        rsort($arr_tanggal);

        return response()->json($arr_tanggal);
    }

    public function medical_patient(Request $request){
        if(!empty($request->year_mcu)){
            $medical = Medical::where('id_employees', $request->id_employee)->whereYear('tanggal_mcu', $request->year_mcu)->first();
            if(!empty($medical)){
                $medical['fullname'] = $medical->employee->fullname;
                $medical['gender'] = $medical->employee->gender;
                if(!empty($medical->tanggal_mcu)){
                    $medical['tgl_mcu'] = Carbon::parse($medical->tanggal_mcu)->format('d F Y');
                }else{
                    $medical['tgl_mcu'] = '-';
                }
                $medical['master_lab'] = Lab::where('id_vendor', $medical->id_vendor)->get()->pluck('nilai_rujukan', 'pemeriksaan');
            }else{
                $medical = '';
            }
        }else{
            $medical = '';
        }
        
        return response()->json($medical);
    }

    public function store_patient(Request $request){
        $user = auth()->user();
        $date_now = Carbon::now()->format('Y-m-d H:i:s');
        $kode = strtotime($date_now);
        if(!empty($request->id_drug)){
            for($i = 0; $i < count($request->id_drug); $i++){
                $insert[] = [
                    'kategori' => 'PASIEN',
                    'tr_tanggal' => $request->tr_tanggal,
                    'kode' => $kode,
                    'id_employee' => $request->id_employee,
                    'id_drug' => $request->id_drug[$i],
                    'jml_drug' => $request->jml_drug[$i],
                    'id_user' => $request->id_user,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }
    
            //insert patien
            $insert_pasien = new Patient;
            $insert_pasien->visit_date = $request->tr_tanggal;
            $insert_pasien->id_dokter = $request->id_user;
            $insert_pasien->id_employee = $request->id_employee;
            $insert_pasien->keluhan = $request->keluhan;
            $insert_pasien->diagnosa = $request->diagnosa;
            $insert_pasien->tensi = $request->tensi;
            $insert_pasien->kode = $kode;
            $insert_pasien->save();
            
            //insert drug out
            $post = Trkeluar::insert($insert);
    
            //insert log user activity
            $insert_log = new Log;
            $insert_log->user_id = $user->id;
            $insert_log->ip_address = $request->ip();
            $insert_log->action = 'insert';
            $insert_log->description = 'Create new transaction patient kode'.'"'.$kode.'" date "'.$request->tr_tanggal.'"';
            $insert_log->save();
        }else{    
            //insert patien
            $insert_pasien = new Patient;
            $insert_pasien->visit_date = $request->tr_tanggal;
            $insert_pasien->id_dokter = $request->id_user;
            $insert_pasien->id_employee = $request->id_employee;
            $insert_pasien->keluhan = $request->keluhan;
            $insert_pasien->diagnosa = $request->diagnosa;
            $insert_pasien->tensi = $request->tensi;
            $insert_pasien->kode = $kode;
            $insert_pasien->save();
    
            //insert log user activity
            $insert_log = new Log;
            $insert_log->user_id = $user->id;
            $insert_log->ip_address = $request->ip();
            $insert_log->action = 'insert';
            $insert_log->description = 'Create new transaction patient kode'.'"'.$kode.'" date "'.$request->tr_tanggal.'"';
            $insert_log->save();
        }

        return redirect()->route('clinic.patient.index')->with('success', 'Add transaction patient successfully.');
    }

    public function export_patient(Request $request){
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $data = [
            'bulan' => $bulan,
            'tahun' => $tahun
        ];

        return Excel::download(new PatientExport($data), 'Resume Patien.xlsx');
    }

    public function log_patient(Request $request){
        if(!empty($request->employee_id)){
            $query = Patient::where('id_employee', $request->employee_id)->orderBy('id','desc')->get();
            if($query->isNotEmpty()){
                foreach($query as $qry){
                    $index = $qry->id;
                    $document[$index] = array();
                    $document[$index]['id'] = $qry->id;
                    $document[$index]['kode'] = $qry->kode;
                    $document[$index]['id_dokter'] = $qry->doctor->nama;
                    $document[$index]['visit_date'] = date('d M Y', strtotime($qry->visit_date));
                    $document[$index]['nik'] = $qry->employee->nik ?? '-';
                    $document[$index]['id_employee'] = $qry->employee->fullname ?? '-';
                    $document[$index]['diagnosa'] = $qry->diagnosa ?? '-';
                    $document[$index]['keluhan'] = $qry->keluhan ?? '-';
                    $document[$index]['tensi'] = $qry->tensi ?? '-';
                    $document[$index]['keterangan'] = $qry->keterangan ?? '-';
                }
            }else{
                $document = array();
            }
        }else{
            $document = array();
        }
        if ($request->ajax()) {
            return DataTables::of($document)
                ->addColumn('medicine', function($document){
                    $query = Trkeluar::where('kode',$document['kode'])->get();
                    if($query->isNotEmpty()){
                        $medicine = '<div class="col-lg-12"><table class="table table-bordered" style="table-layout: fixed; width:100%;">';
                        $medicine .= '
                            <thead>
                                <tr>
                                    <th colspan="2" style="text-align: center;">Penggunaan Obat</th>
                                </tr>
                                <tr>
                                    <th style="text-align: center;">Medicine</th>
                                    <th style="text-align: center;">Qty</th>
                                </tr>
                            </thead>
                            ';
                        $medicine .= '<tbody>';
                        foreach($query as $qry){
                            $medicine .= '<tr>';                  
                            $medicine .= '<td>'.$qry->drug->nama.'</td>';     
                            $medicine .= '<td>'.$qry->jml_drug.'</td>';    
                            $medicine .= '</tr>';
                        } 
                        $medicine .= '</tbody></table></div>';
                    }else{
                        $medicine = '';
                    }
                    return $medicine;
                })
                ->rawColumns(['medicine'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('pages.hrd.clinic.pasien.create');
    }

    public function destroy_patient(Request $request){
        DB::beginTransaction();

        try {
            $user = auth()->user();
            $id = decrypt($request->id);
            $query = Patient::find($id);
            //insert log user activity
            $insert_log = new Log;
            $insert_log->user_id = $user->id;
            $insert_log->ip_address = $request->ip();
            $insert_log->action = 'delete';
            $insert_log->description = 'Delete transaction patient '.'"'.$query->employee->fullname.'" date "'.$query->visit_date.'"';
            $insert_log->save();

            $delete = Patient::where('id', $id)->delete();
            
            DB::commit();

            return response()->json(['message' => "Transaction has been removed"], 200);
        }catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }
    public function medical_patient_mcu_pdf(Request $request){
        $medical = Medical::where('paket', 'mcu tahunan')->where('id_employees', $request->id_employee)->whereYear('tanggal_mcu', $request->date_mcu)->first();
        if(!empty($medical)){
            $data['url_mcu'] = route('lampiran.patient.mcu',encrypt($medical->id));
        }else{
            $data['url_mcu'] = null;
        }
        return response()->json($data);
    }
    public function lampiran_patient_mcu($id){
        $medical = Medical::find(decrypt($id));
        $lampiran_mcu = public_path('storage/mcu/'.$medical->lampiran_mcu);
        // return response()->file($lampiran_mcu);
        $file = File::get($lampiran_mcu);
        $response = Response::make($file, 200);
        $response->header('Content-Type', 'application/pdf');
        if(!empty($medical->employee->nik)){
            $response->header('Content-Disposition', 'filename=' . '"'.$medical->employee->nik.' - '.$medical->employee->fullname.'.pdf"');
        }else{
            $response->header('Content-Disposition', 'filename=' . '"'.$medical->nama.'.pdf"');
        }
        $response->header('Content-Transfer-Encoding', 'binary');
        return $response;
    }
    //end transaction patient

    //start transaction opname
    public function index_opname(Request $request){
        dd('maintenance');
    }

    public function select_stock_opname(Request $request){
        //prev date
        $prev_date = date('Y-m', strtotime("-1 months"));
        $prev_month = date('m', strtotime($prev_date));
        $prev_year = date('Y', strtotime($prev_date));
        //next date
        $next_date = date('Y-m');
        $next_month = date('m', strtotime($next_date));
        $next_year = date('Y', strtotime($next_date));

        $id_drug = $request->id_drug;
        $prestock = Prestock::where('id_drug', $id_drug)->whereYear('tanggal', $prev_year)->whereMonth('tanggal', $prev_month)->sum('jml_drug');
        $jml_in = Trmasuk::where('id_drug', $id_drug)
            ->whereYear('tr_tanggal', $next_year)
            ->whereMonth('tr_tanggal', $next_month)
            ->sum('jml_drug');
        $jml_out = Trkeluar::where('id_drug', $id_drug)
            ->whereYear('tr_tanggal', $next_year)
            ->whereMonth('tr_tanggal', $next_month)
            ->sum('jml_drug');
        
        $data = ($prestock+$jml_in)-$jml_out;
        return response()->json($data);

    }

    public function create_opname(Request $request){
        $user = auth()->user();
        $drugs = Drug::get();

        return view('pages.hrd.clinic.opname.create', compact('user','drugs'));
    }

    public function store_opname(Request $request){
        //prev date
        $prev_date = date('Y-m', strtotime("-1 months"));
        $prev_month = date('m', strtotime($prev_date));
        $prev_year = date('Y', strtotime($prev_date));
        //next date
        $next_date = date('Y-m');
        $next_month = date('m', strtotime($next_date));
        $next_year = date('Y', strtotime($next_date));

        $user = auth()->user();
        $date_now = Carbon::now()->format('Y-m-d H:i:s');
        $kode = strtotime($date_now);
        for($i = 0; $i < count($request->id_drug); $i++){
            $prestock = Prestock::where('id_drug', $request->id_drug[$i])->whereYear('tanggal', $prev_year)->whereMonth('tanggal', $prev_month)->sum('jml_drug');
            $stock_in = Trmasuk::where('id_drug', $request->id_drug[$i])
                ->whereYear('tr_tanggal', $next_year)
                ->whereMonth('tr_tanggal', $next_month)
                ->sum('jml_drug');
            $stock_out = Trkeluar::where('id_drug', $request->id_drug[$i])
                ->whereYear('tr_tanggal', $next_year)
                ->whereMonth('tr_tanggal', $next_month)
                ->sum('jml_drug');
            $jml_stock = ($prestock+$stock_in)-$stock_out;

            $end_stock = $request->jml_drug[$i]-$jml_stock;
            if($end_stock == 0){
                //continue
            }elseif($end_stock > 0){
                //insert in
                $insert_in = new Trmasuk;
                $insert_in->kategori = 'OPNAME'; 
                $insert_in->tr_tanggal = $request->tr_tanggal; 
                $insert_in->kode = $kode; 
                $insert_in->id_drug = $request->id_drug[$i]; 
                $insert_in->jml_drug = abs($end_stock); 
                $insert_in->id_user = $request->id_user; 
                $insert_in->save(); 

                //insert log user activity
                $insert_log_in = new Log;
                $insert_log_in->user_id = $user->id;
                $insert_log_in->ip_address = $request->ip();
                $insert_log_in->action = 'insert';
                $insert_log_in->description = 'Create new transaction in kode'.'"'.$kode.'" date "'.$request->tr_tanggal.'"';
                $insert_log_in->save();
            }else{
                //insert out
                $insert_out = new Trkeluar;
                $insert_out->kategori = 'OPNAME'; 
                $insert_out->tr_tanggal = $request->tr_tanggal; 
                $insert_out->kode = $kode; 
                $insert_out->id_drug = $request->id_drug[$i]; 
                $insert_out->jml_drug = abs($end_stock); 
                $insert_out->id_user = $request->id_user; 
                $insert_out->save(); 

                //insert log user activity
                $insert_log_out = new Log;
                $insert_log_out->user_id = $user->id;
                $insert_log_out->ip_address = $request->ip();
                $insert_log_out->action = 'insert';
                $insert_log_out->description = 'Create new transaction out kode'.'"'.$kode.'" date "'.$request->tr_tanggal.'"';
                $insert_log_out->save();
            }
        }        

        return redirect()->route('clinic.opname.create')->with('success', 'Add transaction opname successfully.');
    }
    //end transaction opname
}
