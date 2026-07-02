<?php

namespace App\Http\Controllers\HRD;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Imports\MedicalsImport;
use App\Imports\MedicalHematologiImport;
use App\Imports\MedicalUrineImport;
use App\Imports\MedicalFaalImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RegulerMCUExport;
use App\Models\Medical;
use App\Models\Vendor;
use App\Models\Lab;
use App\Models\Log;
use App\Models\Employee;
use App\Models\Tempmedical;
use Illuminate\Support\Facades\Request as Input;
use Carbon\Carbon;
use Auth;
use PDF;
use Response;
use Yajra\DataTables\Facades\DataTables;

class MedicalController extends Controller
{
    //start reguler
    public function reguler_index(Request $request){       
        $query = Tempmedical::orderBy('tanggal_awal', 'desc')->get();
        if($query->isNotEmpty()){
            foreach($query as $qry){
                $jml_medical = Medical::where('id_template', $qry->id)->whereNotNull('kriteria_sehat')->count();
                $index = $qry->id;
                $data[$index] = array();
                $data[$index]['id'] = $qry->id;
                $data[$index]['tahun'] = date('Y', strtotime($qry->tanggal_awal));
                $data[$index]['vendor'] = $qry->vendor->nama;
                $data[$index]['total'] = $qry->total_employee;
                $data[$index]['date_range'] = date('d M Y', strtotime($qry->tanggal_awal))." - ".date('d M Y', strtotime($qry->tanggal_akhir));
                $data[$index]['tgl_range'] = $qry->tanggal_awal." to ".$qry->tanggal_akhir;
                $data[$index]['progress'] = number_format(($jml_medical/$qry->total_employee)*100,0);
                $data[$index]['jml_emp'] = $jml_medical;
            }
        }else{
            $data = array();
        }
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('progress', function ($data) {
                    if($data['total'] == $data['jml_emp']){
                        return '<span class="badge text-bg-success">Completed</span>';
                    }else{
                        return '<div class="d-flex align-items-center pb-2"><div class="flex-grow-1"><div class="progress animated-progress custom-progress progress-label"><div class="progress-bar bg-warning" role="progressbar" style="width: '.$data['progress'].'%" aria-valuenow="'.$data['progress'].'" aria-valuemin="0" aria-valuemax="100"><div class="label">'.($data['jml_emp']).'</div></div></div></div></div>';
                    }
                })
                ->addColumn('action', function ($data) {
                    if(\Auth::user()->can('hrd.medical-record.reguler.detail')){
                        $button = '<a href="'. route('reguler.detail', encrypt($data['id'])).'" data-toggle="tooltip" title="Detail" class="btn btn-info btn-sm"><i class="ri-eye-2-line"></i></a>';
                    }else{
                        $button = '';
                    }
                    $button .= '&nbsp;';
                    if(\Auth::user()->can('hrd.medical-record.reguler.update')){
                        $button .= '<a href="#" id="edit" data-id="'.encrypt($data['id']).'" data-bs-toggle="modal" data-bs-target="#modal-edit" title="Edit" class="btn btn-warning btn-sm"><i class="ri-quill-pen-line"></i></a><input type="hidden" class="form-control" id="tgl_periksa" name="tgl_periksa" value="'.$data['tgl_range'].'"/>';
                    }else{
                        $button .= '';
                    }
                    return $button;
                })
                ->rawColumns(['action','progress'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.medical.reguler.index');
    }

    public function reguler_detail(Request $request, $id){
        $kode = $id;
        $temp_medical = Tempmedical::find(decrypt($id));
        $year = Carbon::create($temp_medical->tanggal_awal)->format('Y');
        //total employee
        $total_emp['jml_medical'] = Medical::where('id_template', $temp_medical->id)->count();
        //get previous year
        $pre_year = Carbon::create($temp_medical->tanggal_awal)->subYear()->format('Y');
        $pre_temp_medical = Tempmedical::whereYear('tanggal_awal', $pre_year)->first();
        if(!empty($pre_temp_medical)){
            $total_emp['pre_jml_medical'] = Medical::where('id_template', $pre_temp_medical->id)->count();
        }else{
            $total_emp['pre_jml_medical'] = 0;
        }
        //presentase
        $total_emp['selisih'] = ($total_emp['jml_medical']-$total_emp['pre_jml_medical']);
        if($total_emp['pre_jml_medical'] == 0){
            $total_emp['persentase'] = 0;
        }else{
            $total_emp['persentase'] = number_format(($total_emp['selisih']/$total_emp['pre_jml_medical'])*100,2);

        }
        
        //resiko tinggi
        $total_rt['jml_medical'] = Medical::where('id_template', $temp_medical->id)->where('kriteria_sehat', 'RESIKO TINGGI')->count();
        //get previous year
        if(!empty($pre_temp_medical)){
            $total_rt['pre_jml_medical'] = Medical::where('id_template', $pre_temp_medical->id)->where('kriteria_sehat', 'RESIKO TINGGI')->count();
        }else{
            $total_rt['pre_jml_medical'] = 0;
        }
        //presentase
        $total_rt['selisih'] = ($total_rt['jml_medical']-$total_rt['pre_jml_medical']);
        if( $total_rt['pre_jml_medical'] == 0){
            $total_rt['persentase'] = 0;
        }else{
            $total_rt['persentase'] = number_format(($total_rt['selisih']/$total_rt['pre_jml_medical'])*100,2);
        }
        //keterangan
        $qry_rt = Medical::where('id_template', $temp_medical->id)->where('kriteria_sehat', 'RESIKO TINGGI')->pluck('id_employees');
        if(!empty($pre_temp_medical)){
            $qry_pre_rt = Medical::where('id_template', $pre_temp_medical->id)->whereIn('id_employees', $qry_rt)->get();
        }else{
            $qry_pre_rt = array();
        }
        if(!empty($qry_pre_rt)){
            $ket['rt_sehat'] = 0;
            $ket['rt_sdr'] = 0;
            $ket['rt_rt'] = 0;
            foreach($qry_pre_rt as $pre_rt){
                if($pre_rt->kriteria_sehat == 'SEHAT'){
                    $ket['rt_sehat']++;
                }
                if($pre_rt->kriteria_sehat == 'SEHAT DENGAN RESIKO'){
                    $ket['rt_sdr']++;
                }
                if($pre_rt->kriteria_sehat == 'RESIKO TINGGI'){
                    $ket['rt_rt']++;
                }
            }
        }else{
            $ket = null;
        }

        //sehat dengan resiko
        $total_sr['jml_medical'] = Medical::where('id_template', $temp_medical->id)->where('kriteria_sehat', 'SEHAT DENGAN RESIKO')->count();
        //get previous year
        if(!empty($pre_temp_medical)){
            $total_sr['pre_jml_medical'] = Medical::where('id_template', $pre_temp_medical->id)->where('kriteria_sehat', 'SEHAT DENGAN RESIKO')->count();
        }else{
            $total_sr['pre_jml_medical'] = 0;
        }
        //presentase
        $total_sr['selisih'] = ($total_sr['jml_medical']-$total_sr['pre_jml_medical']);
        if( $total_sr['pre_jml_medical'] == 0){
            $total_sr['persentase'] = 0;
        }else{
            $total_sr['persentase'] = number_format(($total_sr['selisih']/$total_sr['pre_jml_medical'])*100,2);
        }
        //keterangan
        $qry_sr = Medical::where('id_template', $temp_medical->id)->where('kriteria_sehat', 'SEHAT DENGAN RESIKO')->pluck('id_employees');
        if(!empty($pre_temp_medical)){
            $qry_pre_sr = Medical::where('id_template', $pre_temp_medical->id)->whereIn('id_employees', $qry_sr)->get();
        }else{
            $qry_pre_sr = array();
        }
        if(!empty($qry_pre_sr)){
            $ket['sr_sehat'] = 0;
            $ket['sr_sdr'] = 0;
            $ket['sr_rt'] = 0;
            foreach($qry_pre_sr as $pre_sr){
                if($pre_sr->kriteria_sehat == 'SEHAT'){
                    $ket['sr_sehat']++;
                }
                if($pre_sr->kriteria_sehat == 'SEHAT DENGAN RESIKO'){
                    $ket['sr_sdr']++;
                }
                if($pre_sr->kriteria_sehat == 'RESIKO TINGGI'){
                    $ket['sr_rt']++;
                }
            }
        }else{
            $ket = null;
        }

        //sehat
        $total_sehat['jml_medical'] = Medical::where('id_template', $temp_medical->id)->where('kriteria_sehat', 'SEHAT')->count();
        //get previous year
        if(!empty($pre_temp_medical)){
            $total_sehat['pre_jml_medical'] = Medical::where('id_template', $pre_temp_medical->id)->where('kriteria_sehat', 'SEHAT')->count();
        }else{
            $total_sehat['pre_jml_medical'] = 0;
        }
        //presentase
        $total_sehat['selisih'] = ($total_sehat['jml_medical']-$total_sehat['pre_jml_medical']);
        if( $total_sehat['pre_jml_medical'] == 0){
            $total_sehat['persentase'] = 0;
        }else{
            $total_sehat['persentase'] = number_format(($total_sehat['selisih']/$total_sehat['pre_jml_medical'])*100,2);
        }
        //keterangan
        $qry_s = Medical::where('id_template', $temp_medical->id)->where('kriteria_sehat', 'SEHAT')->pluck('id_employees');
        if(!empty($pre_temp_medical)){
            $qry_pre_s = Medical::where('id_template', $pre_temp_medical->id)->whereIn('id_employees', $qry_s)->get();
        }else{
            $qry_pre_s = array();
        }
        if(!empty($qry_pre_s)){
            $ket['s_sehat'] = 0;
            $ket['s_sdr'] = 0;
            $ket['s_rt'] = 0;
            foreach($qry_pre_s as $pre_s){
                if($pre_s->kriteria_sehat == 'SEHAT'){
                    $ket['s_sehat']++;
                }
                if($pre_s->kriteria_sehat == 'SEHAT DENGAN RESIKO'){
                    $ket['s_sdr']++;
                }
                if($pre_s->kriteria_sehat == 'RESIKO TINGGI'){
                    $ket['s_rt']++;
                }
            }
        }else{
            $ket = null;
        }

        if($request->ajax()){
            if(!empty($request->kriteria_sehat)){
                $query = Medical::where('id_template', decrypt($id))->where('kriteria_sehat', $request->kriteria_sehat)->get();
            }else{
                $query = Medical::where('id_template', decrypt($id))->get();
            }
            return DataTables::of($query)
                ->addColumn('no_lab', function($data){
                    if(!empty($data->no_lab)){
                        return $data->no_lab;
                    }else{
                        return '-';
                    }
                })
                ->addColumn('nik', function($data){
                    return $data->employee->nik ?? '-';
                })
                ->addColumn('fullname', function($data){
                    return $data->employee->fullname ?? '-';
                })
                ->addColumn('gender', function($data){
                    if(!empty($data->employee->gender)){
                        if($data->employee->gender == 'Female'){
                            return "P";
                        }elseif($data->employee->gender == 'Male'){
    
                            return "L";
                        }else{
    
                            return "-";
                        }
                    }else{
                        return "-";
                    }
                })

                ->addColumn('umur', function($data){
                    // $date_now = Carbon::now(); // Tanggal sekarang
                    // $b_day = Carbon::parse($data->employee->birthdate); // Tanggal Lahir
                    // $umur = $b_day->diff($date_now);  // Menghitung umur
                    // return $umur->y." Thn ".$umur->m." Bln";

                    if(!empty($data->umur)){
                        return $data->umur;
                    }else{
                        return '-';
                    }
                })
                ->addColumn('area', function($data){
                    return $data->employee->area->name ?? '-';
                })
                ->addColumn('department', function($data){
                    return $data->employee->department->name ?? '-';
                })
                ->addColumn('work_location', function($data){
                    if(!empty($data->employee->work_location)){
                        return $data->employee->work_location;
                    }else{
                        return '-';
                    }
                })
                ->addColumn('kriteria_sehat', function($data){
                    if($data->kriteria_sehat == 'SEHAT'){
                        return '<span class="badge text-bg-success">SEHAT</span>';
                    }elseif($data->kriteria_sehat == 'SEHAT DENGAN RESIKO'){
                        return '<span class="badge text-bg-warning">SEHAT DENGAN RESIKO</span>';
                    }elseif($data->kriteria_sehat == 'RESIKO TINGGI'){
                        return '<span class="badge text-bg-danger">RESIKO TINGGI</span>';
                    }else{
                        return '-';
                    }
                })
                ->addColumn('tanggal_mcu', function($data){
                    if(!empty($data->tanggal_mcu)){
                        return date('Y-m-d', strtotime($data->tanggal_mcu));
                    }else{
                        return '-';
                    }
                })
                ->addColumn('action', function ($data) {
                    // $button = '<a href="'.route('reguler.medical', encrypt($data->id)).'" class="btn btn-link waves-effect">View More</a>';
                    if(empty($data->kriteria_sehat)){
                        $cek_more = \Auth::user()->can('hrd.medical-record.reguler.detail.view-more');
                        $cek_upload = \Auth::user()->can('hrd.medical-record.reguler.detail.upload');
                        $cek_delete = \Auth::user()->can('hrd.medical-record.reguler.detail.delete');
                        if($cek_more == true){
                            $list_more = '<li><a href="'.route('reguler.medical', encrypt($data->id)).'" class="dropdown-item"><i class="ri-search-eye-line align-bottom me-2 text-muted"></i> View More</a></li>';
                        }else{
                            $list_more = '';
                        }
                        if($cek_upload == true){
                            $list_upload = '<li><a href="#" data-bs-toggle="modal" data-bs-target="#upload-reguler" class="dropdown-item"><i class="ri-file-upload-line align-bottom me-2 text-muted"></i> Upload File</a><input type="hidden" class="form-control" id="medical_id" name="medical_id" value="'.$data->id.'"/></li>';
                        }else{
                            $list_upload = '';
                        }
                        if($cek_delete == true){
                            $list_delete = '<li><a href="#" data-bs-toggle="modal" data-bs-target="#delete-reguler" class="dropdown-item"><i class="ri-delete-bin-line align-bottom me-2 text-muted"></i> Delete</a></li>';
                        }else{
                            $list_delete = '';
                        }
                        $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_more.$list_upload.$list_delete.'</ul></div>';
                    }else{
                        $cek_more = \Auth::user()->can('hrd.medical-record.reguler.detail.view-more');
                        $cek_edit = \Auth::user()->can('hrd.medical-record.reguler.detail.update');
                        if($cek_more == true){
                            $list_more = '<li><a href="'.route('reguler.medical', encrypt($data->id)).'" class="dropdown-item"><i class="ri-search-eye-line align-bottom me-2 text-muted"></i> View More</a></li>';
                        }else{
                            $list_more = '';
                        }
                        if($cek_edit == true){
                            $list_edit = '<li><a href="#" data-bs-toggle="modal" data-bs-target="#edit-reguler" class="dropdown-item"><i class="ri-quill-pen-line align-bottom me-2 text-muted"></i> Edit</a><input type="hidden" class="form-control" id="id_detail_medical" name="id_detail_medical" value="'.encrypt($data->id).'"/><input type="hidden" class="form-control" id="id_no_lab" name="id_no_lab" value="'.$data->no_lab.'"/><input type="hidden" class="form-control" id="id_status" name="id_status" value="'.$data->kriteria_sehat.'"/><input type="hidden" class="form-control" id="id_tgl" name="id_tgl" value="'.$data->tanggal_mcu.'"/><input type="hidden" id="preview" name="preview" value="'. route('lampiran.mcu',encrypt($data->id)) .'"></li>';
                        }else{
                            $list_edit = '';
                        }
                        $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_more.$list_edit.'</ul></div>';
                    }
                    return $button;
                })
                ->rawColumns(['action','kriteria_sehat'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.medical.reguler.detail', compact('kode','year','total_emp','total_rt','total_sr','total_sehat','ket'));
    }

    public function reguler_medical($id){
        $medical = Medical::find(decrypt($id));
        if(!empty($medical->lampiran_mcu)){
            $cek_pdf = 1;
        }else{
            $cek_pdf = 0;
        }
        // dd($cek_pdf);
        $lab = Lab::where('id_vendor', $medical->id_vendor)->get()->pluck('nilai_rujukan', 'pemeriksaan');
        return view('pages.hrd.medical.reguler.medical', compact('medical', 'lab','id','cek_pdf'));
    }

    public function lampiran_mcu($id){
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
    
    public function reguler_form(Request $request){
        $vendors = Vendor::where('tipe','medical')->get();
        $employees = Employee::all();
        return view('pages.hrd.medical.reguler.form', compact('vendors','employees'));
    }

    public function reguler_store(Request $request){
        switch($request->input('action')){
            case 'generate':
                $date_range = explode(" to ", $request->date_range);
                $temp_date = date('Y-m', strtotime($date_range[0]));
                // dd($temp_date);

                $temp_medical = Tempmedical::where('tanggal_awal', $date_range[0])->where('tanggal_akhir', $date_range[1])->first();
                if(!empty($temp_medical)){
                    $medical = Medical::where('id_template', $temp_medical->id)->get()->pluck('id_employees');
                    $employee_active = Employee::whereNot('status', 'TERMINATED')->whereNotIn('id', $medical)->orderBy('nik', 'asc')->get();
                }else{
                    $employee_active = Employee::whereNot('status', 'TERMINATED')->orderBy('nik', 'asc')->get();
                }
                // $medical = Medical::whereNotNull('id_employees');
                foreach($employee_active as $employee){
                    $cek_emp = Medical::where('id_employees', $employee->id)->orderBy('tanggal_mcu', 'desc')->first();
                    if(!empty($cek_emp->tanggal_mcu)){
                        $emp_date = date('Y-m', strtotime($cek_emp->tanggal_mcu));
    
                        $last_date = new Carbon($temp_date);
                        $date = new Carbon($emp_date);
                        $diff = $date->diff($last_date);
                        if($diff->y >= 1){
                            $employees[] = $employee;
                        }else{
                            $employees[] = null;
                            $employees2[] = $employee;
                            }
                        }else{
                        $employees[] = null;
                        $employees2[] = $employee;
                    }
                }
                // dd($employees);
                return back()->withInput()->with( ['employees' => $employees, 'employees2' => $employees2] );
            break;

            case 'save':
                if(!empty($request->employee)){
                    $arr_employee = array_unique($request->employee);
                }else{
                    $arr_employee = array();
                }
                if(!empty($request->employee2)){
                    $arr_employee2 = array_unique($request->employee2);
                }else{
                    $arr_employee2 = array();
                }
                if(empty($arr_employee2)){
                    $employee = $arr_employee;
                }else{
                    $employee = array_merge($arr_employee,$arr_employee2);
                }

                if(empty($arr_employee) && empty($arr_employee2)){
                    return redirect()->route('reguler.form')->with('error', 'The input form is empty, please check again.');
                }else{
                    $user = auth()->user();
                    $date = explode(' to ', $request->date_range);
                    $temp_medical = Tempmedical::where('tanggal_awal', $date[0])->where('tanggal_akhir', $date[1])->first();
                    if(!empty($temp_medical)){
                        $t_employee = count($employee);
                        $t_temp = $temp_medical->total_employee;
                        $s_employee = $t_employee+$t_temp;

                        $update = Tempmedical::where('id', $temp_medical->id)->update(['total_employee' => $s_employee]);
    
                        for($i = 0; $i < count($employee); $i++){
                            $insert_medical[] = [
                                'id_employees' => $employee[$i],
                                'id_vendor' => $request->vendor,
                                'paket' => 'mcu tahunan',
                                'id_template' => $temp_medical->id,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]; 
                        }
                        $post = Medical::insert($insert_medical);
    
                        //insert log user activity
                        $insert_log = new Log;
                        $insert_log->user_id = $user->id;
                        $insert_log->ip_address = $request->ip();
                        $insert_log->action = 'update';
                        $insert_log->description = 'Modify Detail Template Reguler Medical Checkup '.'"'.$temp_medical->tanggal_awal.'" '.'Sampai "'.$temp_medical->tanggal_akhir.'"';
                        $insert_log->save();
                        
                        return redirect()->route('reguler.index')->with('success', 'Update Medical Check Up Successfully.');

                    }else{

                        $insert = new Tempmedical;
                        $insert->id_vendor = $request->vendor;
                        $insert->total_employee = count($employee);
                        $insert->tanggal_awal = $date[0];
                        $insert->tanggal_akhir = $date[1];
                        $insert->save();
    
                        $query = Tempmedical::latest()->first();
    
                        for($i = 0; $i < count($employee); $i++){
                            $insert_medical[] = [
                                'id_employees' => $employee[$i],
                                'id_vendor' => $request->vendor,
                                'paket' => 'mcu tahunan',
                                'id_template' => $query->id,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ]; 
                        }
                        $post = Medical::insert($insert_medical);
    
                        //insert log user activity
                        $insert_log = new Log;
                        $insert_log->user_id = $user->id;
                        $insert_log->ip_address = $request->ip();
                        $insert_log->action = 'insert';
                        $insert_log->description = 'Create Template Reguler Medical Checkup '.'"'.$query->tanggal_awal.'" '.'Sampai "'.$query->tanggal_akhir.'"';
                        $insert_log->save();
                        
                        return redirect()->route('reguler.index')->with('success', 'Create Medical Check Up Successfully.');
                    }
                }
            break;
        }
        // $employee = array_unique($request->employee);       
    }

    public function reguler_edit(Request $request){
        $user = auth()->user();
        $tgl_range = explode(" to ", $request->date_range);
        $post = Tempmedical::where('id', decrypt($request->id))->update(['tanggal_awal' => $tgl_range[0], 'tanggal_akhir' => $tgl_range[1]]);

         //insert log user activity
         $insert_log = new Log;
         $insert_log->user_id = $user->id;
         $insert_log->ip_address = $request->ip();
         $insert_log->action = 'update';
         $insert_log->description = 'Modify Template Reguler Medical Checkup '.'"'.$tgl_range[0].'" '.'Sampai "'.$tgl_range[1].'"';
         $insert_log->save();

        return redirect()->route('reguler.index')->with('success', 'Modify Template Medical Check Up Successfully.');
    }
    //upload individual
    public function reguler_update(Request $request){
        $user = auth()->user();
        $medical = Medical::find($request->id_medical);
        if($medical->employee->gender == 'Male'){
            $jk = 'L';
        }else{
            $jk = 'P';
        }
        $mcu_file = $request->file('file');
        $file_mcu = strtoupper($request->no_lab).' - '.$medical->employee->fullname.'('.$jk.')'.'.'.$mcu_file->getClientOriginalExtension();
        $request->file->storeAs('public/mcu', $file_mcu);

        $date_now = Carbon::now(); // Tanggal sekarang
        $b_day = Carbon::parse($medical->employee->birthdate); // Tanggal Lahir
        $umur = $b_day->diff($date_now);  // Menghitung umur

        $post = Medical::where('id', $request->id_medical)->update([
            'no_lab' => strtoupper($request->no_lab),
            'umur' => $umur->y." Thn ".$umur->m." Bln",
            'kriteria_sehat' => $request->status,
            'tanggal_mcu' => $request->tanggal_mcu,
            'lampiran_mcu' => $file_mcu
        ]);

        //insert log user activity
        $insert = new Log;
        $insert->user_id = $user->id;
        $insert->ip_address = $request->ip();
        $insert->action = 'update';
        $insert->description = 'Modify reguler Medical Checkup '.'Nama "'.$medical->employee->fullname.'"';
        $insert->save();

        return redirect()->route('reguler.detail', encrypt($medical->id_template))->with('success', 'Upload File Medical Check Up Successfully.');
    }
    //edit upload individual
    public function reguler_api_update(Request $request){
        // dd('maintenance');
        $user = auth()->user();
        $medical = Medical::find(decrypt($request->id));
        if(!empty($request->ajx_file)){
            $cek_file = storage_path('app/public/mcu/'.$medical->lampiran_mcu);
            // dd($cek_file);
            if (File::exists($cek_file)) {
                File::delete($cek_file);
                if($request->ajx_status == 'none'){
                    $kriteria = null;
                }else{
                    $kriteria = $request->ajx_status;
                }
                if($medical->employee->gender == 'Male'){
                    $jk = 'L';
                }else{
                    $jk = 'P';
                }
                $mcu_file = $request->file('ajx_file');
                $file_name = $file_mcu = strtoupper($request->ajx_no_lab).' - '.$medical->employee->fullname.'('.$jk.')'.'.'.$mcu_file->getClientOriginalExtension();
                // dd($file_name);
                $request->ajx_file->storeAs('public/mcu', $file_name);

                $post = Medical::where('id', decrypt($request->id))->update([
                    'no_lab' => strtoupper($request->ajx_no_lab),
                    'kriteria_sehat' => $kriteria,
                    'tanggal_mcu' => $request->ajx_tanggal_mcu,
                    'lampiran_mcu' => $file_name,
                ]);
                
            }
        }else{
            // if(!empty($medical->lampiran_mcu)){
            //     if(!empty($request->ajx_no_lab)){
            //         $no_lab = strtoupper($request->ajx_no_lab);
            //     }else{
            //         $no_lab = $request->ajx_no_lab;
            //     }
            //     $cek_file = storage_path('app/public/mcu/'.$medical->lampiran_mcu);
            //     if (File::exists($cek_file)) {
            //         File::delete($cek_file);
            //     }

            //     if($request->ajx_status == 'none'){
            //         $kriteria = null;
            //     }else{
            //         $kriteria = $request->ajx_status;
            //     }

            //     $post = Medical::where('id', decrypt($request->id))->update([
            //         'no_lab' => $no_lab,
            //         'umur' => null,
            //         'kriteria_sehat' => $kriteria,
            //         'tanggal_mcu' => $request->ajx_tanggal_mcu,
            //         'lampiran_mcu' => null
            //     ]);
            // }else{
                $date_mcu = Carbon::parse($medical->tanggal_mcu); // Tanggal sekarang
                $b_day = Carbon::parse($medical->employee->birthdate); // Tanggal Lahir
                $umur = $b_day->diff($date_mcu);  // Menghitung umur

                if($request->ajx_status == 'none'){
                    $kriteria = null;
                }else{
                    $kriteria = $request->ajx_status;
                }
    
                $post = Medical::where('id', decrypt($request->id))->update([
                    'no_lab' => strtoupper($request->ajx_no_lab),
                    'umur' => $umur->y." Thn ".$umur->m." Bln",
                    'kriteria_sehat' => $kriteria,
                    'tanggal_mcu' => $request->ajx_tanggal_mcu,
                ]);
            // }
        }

        //insert log user activity
        $insert = new Log;
        $insert->user_id = $user->id;
        $insert->ip_address = $request->ip();
        $insert->action = 'update';
        $insert->description = 'Modify reguler Detail Medical Checkup '.'Nama "'.$medical->employee->fullname.'"';
        $insert->save();

        return redirect()->route('reguler.detail', encrypt($medical->id_template))->with('success', 'Edit Medical Check Up Successfully.');
    } 
    
    public function reguler_destroy(Request $request){
        $user = auth()->user();
        $medical = Medical::find($request->del_medical);
        //insert log user activity
        $insert = new Log;
        $insert->user_id = $user->id;
        $insert->ip_address = $request->ip();
        $insert->action = 'delete';
        $insert->description = 'Delete reguler Medical Checkup '.'Nama "'.$medical->employee->fullname.'"';
        $insert->save();

        $temp_medical = Tempmedical::where('id', $medical->id_template)->first();
        $t_employee = $temp_medical->total_employee;
        $total_employee = $t_employee-1;
        $update = Tempmedical::where('id', $medical->id_template)->update(['total_employee' => $total_employee]);
        $delete = Medical::where('id', $request->del_medical)->delete();

        return redirect()->route('reguler.detail', encrypt($temp_medical->id))->with('success', 'Delete Medical Check Up Successfully.');
    }

    public function reguler_upload(Request $request, $id){
        $kode = decrypt($id);
        return view('pages.hrd.medical.reguler.upload', compact('kode','id'));
    }

    public function api_reguler_upload(Request $request){
        $file = Input::file('file');
        $arr_medicals = Excel::toArray(new MedicalsImport,$file);
        foreach($arr_medicals[0] as $key_medical => $val_medical){
            $index = $key_medical;
            $data[$index]['no_lab'] = $val_medical['no_lab'];
            $data[$index]['nama'] = $val_medical['nama'];
            $data[$index]['jk'] = $val_medical['jk'];
            $data[$index]['umur'] = $val_medical['umur'];
            $data[$index]['lab'] = $val_medical['lab'];
            $data[$index]['foto_thorax'] = $val_medical['foto_thorax'];
            $data[$index]['audiometri'] = $val_medical['audiometri'];
            $data[$index]['fisik_dokter'] = $val_medical['fisik_dokter'];
            $data[$index]['kesimpulan'] = $val_medical['kesimpulan'];
            $data[$index]['saran'] = $val_medical['saran'];
            $data[$index]['skor_framigham'] = $val_medical['skor_framigham'];
            $data[$index]['kriteria_sehat'] = $val_medical['kriteria_sehat'];
        }
        return response()->json($data);
    }

    public function import(Request $request){
        $kode = encrypt($request->id_temp);
        // $post = Excel::import(new MedicalsImport,request()->file('file'));
        // $arr_medicals[0] = Excel::toArray(new MedicalsImport,request()->file('file'));
        // $arr_medicals[0] = Excel::toArray(new MedicalHematologiImport,request()->file('file'));
        $file = Input::file('file');
        // $employees = Employee::all();
        $nama_file = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        if($nama_file == 'form-global-medical-checkup'){
            $arr_medicals = Excel::toArray(new MedicalsImport,$file);
            foreach($arr_medicals[0] as $key_medical => $val_medical){
                $index = $key_medical;
                // $employee = Employee::where('fullname', $val_medical['nama'])->first();
                // if(!empty($employee)){
                //     $id_employee = $employee->id;
                    // $insert[] = [
                    //     'id_employees' => $val_medical['nama'],
                    //     'id_vendor' => 1,
                    //     'paket' => 'mcu tahunan',
                    //     'no_lab' => $val_medical['no_lab'],
                    //     'lab' => $val_medical['lab'],
                    //     'foto_thorax' => $val_medical['foto_thorax'],
                    //     'audiometri' => $val_medical['audiometri'],
                    //     'fisik_dokter' => $val_medical['fisik_dokter'],
                    //     'kesimpulan' => $val_medical['kesimpulan'],
                    //     'saran' => $val_medical['saran'],
                    //     'skor_framigham' => $val_medical['skor_framigham'],
                    //     'kriteria_sehat' => $val_medical['kriteria_sehat'],                  
                    //     'id_template' => $val_medical['id_template']                    
                    // ];
                // }
                $update = Medical::where('no_lab', $val_medical['no_lab'])->where('id_template', $request->id_temp)->update([
                    'lab' => $val_medical['lab'],
                    'foto_thorax' => $val_medical['foto_thorax'],
                    'ekg' => $val_medical['ekg'],
                    'audiometri' => $val_medical['audiometri'],
                    'fisik_dokter' => $val_medical['fisik_dokter'],
                    'kesimpulan' => $val_medical['kesimpulan'],
                    'saran' => $val_medical['saran'],
                    'skor_framigham' => $val_medical['skor_framigham']
                    // 'kriteria_sehat' => $val_medical['kriteria_sehat']
                ]); 
            }
            $temp_medical = Tempmedical::where('id', $request->id_temp)->first();
            $user = auth()->user();
            //insert log user activity
            $insert_log = new Log;
            $insert_log->user_id = $user->id;
            $insert_log->ip_address = $request->ip();
            $insert_log->action = 'update';
            $insert_log->description = 'Modify Detail Reguler Medical Checkup '.'"'.$temp_medical->tanggal_awal.'" '.'Sampai "'.$temp_medical->tanggal_akhir.'"';
            $insert_log->save();
            // $post = Medical::insert($insert);
            return redirect()->route('reguler.upload', $kode)->with('success', 'Upload Data successfully.');
        }elseif($nama_file == 'form-lab-medical-hematologi'){
            $arr_medicals = Excel::toArray(new MedicalHematologiImport,$file);
            foreach($arr_medicals[0] as $key_medical => $val_medical){
                $index = $key_medical;
                // $employee = Employee::where('fullname', $val_medical['nama'])->first();
                // if(!empty($employee)){
                    // $id_employee = $employee->id;
                    $update = Medical::where('no_lab', $val_medical['no_lab'])->where('id_template', $request->id_temp)->update([
                        'hm_hemoglobin' => $val_medical['hm_hemoglobin'],
                        'hm_eritrosit' => $val_medical['hm_eritrosit'],
                        'hm_hematokrit' => $val_medical['hm_hematokrit'],
                        'hm_mcv' => $val_medical['hm_mcv'],
                        'hm_mch' => $val_medical['hm_mch'],
                        'hm_mchc' => $val_medical['hm_mchc'],
                        'hm_rdw' => $val_medical['hm_rdw'],
                        'hm_leukosit' => $val_medical['hm_leukosit'],
                        'hm_eos' => $val_medical['hm_eos'],
                        'hm_baso' => $val_medical['hm_baso'],
                        'hm_neutro' => $val_medical['hm_neutro'],
                        'hm_limfo' => $val_medical['hm_limfo'],
                        'hm_mono' => $val_medical['hm_mono'],
                        'hm_eos_absolut' => $val_medical['hm_eos_absolut'],
                        'hm_baso_absolut' => $val_medical['hm_baso_absolut'],
                        'hm_neutro_absolut' => $val_medical['hm_neutro_absolut'],
                        'hm_limfo_absolut' => $val_medical['hm_limfo_absolut'],
                        'hm_mono_absolut' => $val_medical['hm_mono_absolut'],
                        'hm_trombosit' => $val_medical['hm_trombosit'],
                        'hm_led' => $val_medical['hm_led']
                    ]); 
                // }
            }
            $temp_medical = Tempmedical::where('id', $request->id_temp)->first();
            $user = auth()->user();
            //insert log user activity
            $insert_log = new Log;
            $insert_log->user_id = $user->id;
            $insert_log->ip_address = $request->ip();
            $insert_log->action = 'update';
            $insert_log->description = 'Modify Detail Reguler Medical Checkup '.'"'.$temp_medical->tanggal_awal.'" '.'Sampai "'.$temp_medical->tanggal_akhir.'"';
            $insert_log->save();
            return redirect()->route('reguler.upload',$kode)->with('success', 'Upload Data successfully.');
        }elseif($nama_file == 'form-lab-medical-urine'){
            $arr_medicals = Excel::toArray(new MedicalUrineImport,$file);
            foreach($arr_medicals[0] as $key_medical => $val_medical){
                $index = $key_medical;
                // $employee = Employee::where('fullname', $val_medical['nama'])->first();
                // if(!empty($employee)){
                //     $id_employee = $employee->id;
                    $update = Medical::where('no_lab', $val_medical['no_lab'])->where('id_template', $request->id_temp)->update([
                        // 'umur' => $val_medical['umur'],
                        'u_warna' => $val_medical['u_warna'],
                        'u_kejernihan' => $val_medical['u_kejernihan'],
                        'u_berat_jenis' => $val_medical['u_berat_jenis'],
                        'u_ph' => $val_medical['u_ph'],
                        'u_protein_albumin' => $val_medical['u_protein_albumin'],
                        'u_glukosa' => $val_medical['u_glukosa'],
                        'u_keton' => $val_medical['u_keton'],
                        'u_bilirubin' => $val_medical['u_bilirubin'],
                        'u_urobilinogen' => $val_medical['u_urobilinogen'],
                        'u_nitrit' => $val_medical['u_nitrit'],
                        'u_leukosit_esterase' => $val_medical['u_leukosit_esterase'],
                        'u_darah_haem' => $val_medical['u_darah_haem'],
                        'u_eri' => $val_medical['u_eri'],
                        'u_leuko' => $val_medical['u_leuko'],
                        'u_epithel' => $val_medical['u_epithel'],
                        'u_silinder' => $val_medical['u_silinder'],
                        'u_kristal' => $val_medical['u_kristal'],
                        'u_lain' => $val_medical['u_lain']
                    ]); 
                // }
            }
            $temp_medical = Tempmedical::where('id', $request->id_temp)->first();
            $user = auth()->user();
            //insert log user activity
            $insert_log = new Log;
            $insert_log->user_id = $user->id;
            $insert_log->ip_address = $request->ip();
            $insert_log->action = 'update';
            $insert_log->description = 'Modify Detail Reguler Medical Checkup '.'"'.$temp_medical->tanggal_awal.'" '.'Sampai "'.$temp_medical->tanggal_akhir.'"';
            $insert_log->save();
            return redirect()->route('reguler.upload',$kode)->with('success', 'Upload Data successfully.');
        }elseif($nama_file == 'form-lab-medical-faal'){
            $arr_medicals = Excel::toArray(new MedicalFaalImport,$file);
            foreach($arr_medicals[0] as $key_medical => $val_medical){
                $index = $key_medical;
                // $employee = Employee::where('fullname', $val_medical['nama'])->first();
                // if(!empty($employee)){
                //     $id_employee = $employee->id;
                    $update = Medical::where('no_lab', $val_medical['no_lab'])->where('id_template', $request->id_temp)->update([
                        'fh_sgot' => $val_medical['fh_sgot'],
                        'fh_sgpt' => $val_medical['fh_sgpt'],
                        'fl_kolesterol_total' => $val_medical['fl_kolesterol_total'],
                        'fl_hdl_kolesterol' => $val_medical['fl_hdl_kolesterol'],
                        'fl_ldl_kolesterol' => $val_medical['fl_ldl_kolesterol'],
                        'fl_trigliserida' => $val_medical['fl_trigliserida'],
                        'gd_glukosa_puasa' => $val_medical['gd_glukosa_puasa'],
                        'gd_jpp' => $val_medical['gd_jpp'],
                        'fg_bun' => $val_medical['fg_bun'],
                        'fg_ureum' => $val_medical['fg_ureum'],
                        'fg_kreatinin' => $val_medical['fg_kreatinin'],
                        'fg_egfr' => $val_medical['fg_egfr'],
                        'asam_urat' => $val_medical['asam_urat'],
                        'hbsag' => $val_medical['hbsag']
                    ]); 
                // }
            }
            $temp_medical = Tempmedical::where('id', $request->id_temp)->first();
            $user = auth()->user();
            //insert log user activity
            $insert_log = new Log;
            $insert_log->user_id = $user->id;
            $insert_log->ip_address = $request->ip();
            $insert_log->action = 'update';
            $insert_log->description = 'Modify Detail Reguler Medical Checkup '.'"'.$temp_medical->tanggal_awal.'" '.'Sampai "'.$temp_medical->tanggal_akhir.'"';
            $insert_log->save();
            return redirect()->route('reguler.upload',$kode)->with('success', 'Upload Data successfully.');
        }else{
            return redirect()->route('reguler.upload',$kode)->with('error', 'Incorrect file format, please check again.');
        }
        // $arr_medicals[0] = Excel::toArray(new MedicalUrineImport,request()->file('file'));
        // dd($file->getClientOriginalName());

        // // dd($arr_medicals);
        // foreach($arr_medicals[0] as $key_medical => $val_medical){
        //     $index = $key_medical;
        //     $medicals[$index]['no_lab'] = $val_medical['no_lab'];
        // }
        // dd($medicals);

        // return redirect()->route('reguler.index')->with('success', 'Upload Medical Record Successfully.');
        // return view('pages.hrd.medical.reguler.upload', compact('medicals'));
    }

    public function reguler_export($id){
        // dd('maintenance');
        $id = decrypt($id);
        // $temp_mcu = Tempmedical::find($id);
        // $start_year = Carbon::create($temp_mcu->tanggal_awal)->subYear(2)->format('Y'); 
        // $selected_year = date('Y', strtotime($temp_mcu->tanggal_awal));

        // $query = Medical::whereYear('tanggal_mcu', '>=', $start_year)->whereYear('tanggal_mcu', '<=', $selected_year)->whereNotNull('id_template')->get()->unique('id_employees')->pluck('id_employees');
        // foreach($query as $id_employees){
        //     $emp = Employee::find($id_employees);
        //     $data['nama'] = $emp->fullname;
        //     $data['lokasi'] = $emp->area->name;
        //     $data['bagian'] = $emp->department->name;
        //     $medical = Medical::where('id_employees', $id_employees)->whereYear('tanggal_mcu', '>=', $start_year)->whereYear('tanggal_mcu', '<=', $selected_year)->whereNotNull('id_template')->get();
        //     $tahun = [];
        //     $kriteria = [];
        //     $kesimpulan = [];
        //     foreach($medical as $mcu){
        //         $count = $medical->count();
        //         if($count == 3){
        //             //tahun
        //             $arr_tahun = date('Y', strtotime($mcu->tanggal_mcu));
        //             $tahun[] = $arr_tahun;
        //             //kriteria
        //             $arr_kriteria = $mcu->kriteria_sehat;
        //             $kriteria[] = $arr_kriteria;
        //             //kesimpulan
        //             $arr_kesimpulan = $mcu->kesimpulan;
        //             $kesimpulan[] = $arr_kesimpulan;
        //         }elseif($count == 2){
        //             $arr_tahun = date('Y', strtotime($mcu->tanggal_mcu));
        //             $tahun[] = $arr_tahun;
        //             array_push($tahun, "-");
        //             unset($tahun[3]);
        //             //kriteria
        //             $arr_kriteria = $mcu->kriteria_sehat;
        //             $kriteria[] = $arr_kriteria;
        //             array_push($kriteria, "-");
        //             unset($kriteria[3]);
        //             //kesimpulan
        //             $arr_kesimpulan = $mcu->kesimpulan;
        //             $kesimpulan[] = $arr_kesimpulan;
        //             array_push($kesimpulan, "-");
        //             unset($kesimpulan[3]);
        //         }else{
        //             $arr_tahun = date('Y', strtotime($mcu->tanggal_mcu));
        //             $tahun[] = $arr_tahun;
        //             array_push($tahun, "-","-");
        //             //kriteria
        //             $arr_kriteria = $mcu->kriteria_sehat;
        //             $kriteria[] = $arr_kriteria;
        //             array_push($kriteria, "-","-");
        //             //kesimpulan
        //             $arr_kesimpulan = $mcu->kesimpulan;
        //             $kesimpulan[] = $arr_kesimpulan;
        //             array_push($kesimpulan, "-","-");
        //         }                 
        //     } 
        //     arsort($tahun);       
        //     arsort($kriteria);       
        //     arsort($kesimpulan);       
        //     $data['tahun'] = $tahun;
        //     $data['kriteria'] = $kriteria;
        //     $data['kesimpulan'] = $kesimpulan;
        //     $data_export[] = $data;
        // }
        // dd($data_export);
        // foreach($data_export as $dt_exp){
            // dd($data_export);
        //     $arr_tahun[] = count($dt_exp['tahun']);
        // }
        return Excel::download(new RegulerMCUExport($id), 'Resume High Risk MCU.xlsx');
    }
    //end reguler

    //start ireguler
    public function ireguler_index(Request $request){
        $query = Medical::where('paket','!=', 'mcu tahunan')->orderBy('tanggal_mcu','desc')->get();
        if($query->isNotEmpty()){
            foreach($query as $qry){
                $index = $qry->id;
                $data[$index] = array();
                $data[$index]['id'] = $qry->id;
                $data[$index]['nama'] = $qry->nama;
                $data[$index]['umur'] = $qry->umur;
                if($qry->paket == 'pria'){
                    $data[$index]['paket'] = 'Karyawan Pria';
                }elseif($qry->paket == 'wanita'){
                    $data[$index]['paket'] = 'Karyawan Wanita';
                }else{
                    $data[$index]['paket'] = 'Calon Karyawan';
                }
                $data[$index]['jk'] = $qry->jk;
                $data[$index]['tanggal_mcu'] = $qry->tanggal_mcu;
                $data[$index]['lab'] = $qry->medicalvendor->nama;
                if(!empty($qry->lampiran_mcu)){
                    $data[$index]['status'] = $qry->kriteria_sehat;
                }else{
                    $data[$index]['status'] = 'proses';
                }
            }
        }else{
            $data = array();
        }
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('status', function ($data) {
                    if($data['status'] == 'proses'){
                        return '<span class="badge text-bg-warning">PROSES</span>';
                    }elseif($data['status'] == 'FIT'){
                        return '<span class="badge text-bg-success">'.$data['status'].'</span>';
                    }elseif($data['status'] == 'UNFIT'){
                        return '<span class="badge text-bg-warning">'.$data['status'].'</span>';                        
                    }else{
                        return '<span class="badge text-bg-danger">'.$data['status'].'</span>';
                    }
                })
                ->addColumn('action', function ($data) {
                    if($data['status'] == 'proses'){
                        if(\Auth::user()->can('hrd.medical-record.ireguler.upload')){
                            $button = '<button type="button" id="upload_file" data-bs-toggle="modal" data-bs-target="#upload-ireguler" class="btn btn-danger btn-sm"><i class="ri-file-upload-line align-bottom"></i> Upload</button> <input type="hidden" class="form-control" id="medical_id" name="medical_id" value="'.$data['id'].'"/>';
                        }else{
                            $button = '';
                        }
                    }else{
                        if(\Auth::user()->can('hrd.medical-record.ireguler.view-pdf')){
                            $button = '<button type="button" id="preview_file" data-bs-toggle="modal" data-bs-target="#modal-preview" class="btn btn-info btn-sm"><i class="ri-zoom-in-line align-bottom"></i> Preview</button> <input type="hidden" class="form-control" id="id_preview" name="id_preview" value="'.route('lampiran.mcu',encrypt($data['id'])).'"/>';         
                           
                        }else{
                            $button = '';
                        }
                    }
                    $button .= '&nbsp;';
                    if(\Auth::user()->can('hrd.medical-record.ireguler.surat-pelaksanaan-mcu')){
                        $button .= '<button type="button" data-bs-toggle="modal" data-bs-target="#modal-surat" class="btn btn-success btn-sm"><i class="ri-eye-2-line align-bottom"></i> Unduh Surat</button> <input type="hidden" class="form-control" id="id_surat" name="id_surat" value="'.encrypt($data['id']).'"/>';
                    }
                    return $button;
                })
                ->rawColumns(['action','status'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.medical.ireguler.index');
    }

    public function ireguler_form(Request $request){
        $employees = Employee::all();
        $vendors = Vendor::where('tipe','medical')->get();
        return view('pages.hrd.medical.ireguler.form', compact('employees','vendors'));
    }

    public function generate_pdf(Request $request){
        $query = Medical::find(decrypt($request->surat_id));
        //jadwal
        $day = date('d', strtotime($query->tanggal_mcu));
        $month = date('m', strtotime($query->tanggal_mcu));
        $year = date('Y', strtotime($query->tanggal_mcu));
        //nama hari
        $n_day = date('l', strtotime($query->tanggal_mcu));
        $day_name = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu'
        ];
        //nama bulan
        $month_name = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        ];

        if($query->paket == 'pria'){
            $paket = 'Karyawan Pria';
        }elseif($query->paket == 'wanita'){
            $paket = 'Karyawan Wanita';
        }else{
            $paket = 'Calon Karyawan';
        }

        $day_letter = date('d', strtotime($query->created_at));
        $month_letter = date('m', strtotime($query->created_at));
        $year_letter = date('Y', strtotime($query->created_at));

        $data = [
            'title' => 'Surat Pengantar MCU',
            'kop_surat' => public_path('assets/images/kop-surat.jpg'),
            'nomor_surat' => $request->no_surat,
            'nama' => $query->nama,
            'area' => $query->area_mcu,
            'nama_hari' => $day_name[$n_day],
            'hari' => $day_name[$n_day],
            'tanggal' => $day.' '.$month_name[$month].' '.$year,
            'paket' => $paket,
            'tanggal_surat' => $day_letter.' '.$month_name[$month_letter].' '.$year_letter
        ]; 
        $pdf = PDF::loadView('pages.hrd.medical.ireguler.surat', $data);
     
        return $pdf->stream('SURAT PENGANTAR MCU - '.$query->nama.'.pdf');
        // return $pdf->download('surat pengantar.pdf');
    }

    public function ireguler_store(Request $request){
        $user = auth()->user();
        if($request->cek_employee == 1){
            $employee = Employee::find($request->employee);
            $date_mcu = Carbon::parse($request->tgl_periksa_1); // Tanggal periksa
            $b_day = Carbon::parse($employee->birthdate); // Tanggal Lahir
            $umur = $b_day->diff($date_mcu);  // Menghitung umur
            $tgl_lahir = $umur->y." Thn ".$umur->m." Bln";

            //gender
            if($employee->gender == 'Male'){
                $gender = 'L';
            }else{
                $gender = 'P';
            }

            $insert = new Medical;
            $insert->id_employees = $request->employee;
            $insert->nama = $employee->fullname;
            $insert->jk = $gender;
            $insert->id_vendor = $request->vendor_1;
            $insert->umur = $tgl_lahir;
            $insert->paket = $request->paket_1;
            $insert->area_mcu = strtoupper($request->apm_1);
            // $insert->no_lab = strtoupper($request->no_lab_1);
            $insert->tanggal_mcu = $request->tgl_periksa_1;
            $insert->save();

            //insert log user activity
            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'insert';
            $insert->description = 'Create "Karyawan" Ireguler Medical Checkup NIK '.'"'.$employee->nik.'" '.'Nama "'.$employee->fullname.'"';
            $insert->save();
        }else{
            $date_mcu = Carbon::parse($request->tgl_periksa_2); // Tanggal periksa
            $b_day = Carbon::parse($request->tgl_lahir); // Tanggal Lahir
            $umur = $b_day->diff($date_mcu);  // Menghitung umur
            $tgl_lahir = $umur->y." Thn ".$umur->m." Bln";

            $insert = new Medical;
            $insert->nama = strtoupper($request->nama_karyawan);
            $insert->jk = $request->jk;
            $insert->ktp = $request->no_ktp;
            $insert->umur = $tgl_lahir;
            $insert->tanggal_mcu = $request->tgl_periksa_2;
            $insert->paket = $request->paket_2;
            $insert->area_mcu = strtoupper($request->apm_2);
            // $insert->no_lab = strtoupper($request->no_lab_2);
            $insert->id_vendor = $request->vendor_2;
            $insert->save();

            //insert log user activity
            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'insert';
            $insert->description = 'Create "Non karyawan" Ireguler Medical Checkup NIK KTP '.'"'.$request->no_ktp.'" '.'Nama "'.$request->nama_karyawan.'"';
            $insert->save();
        }

        return redirect()->route('ireguler.index')->with('success', 'Create Medical Check Up Successfully.');
    }

    public function ireguler_update(Request $request){
        $user = auth()->user();
        $medical = Medical::find($request->id_medical);
        if(!empty($medical->jk)){
            $jk = $medical->jk;

            $mcu_file = $request->file('file');
            $file_mcu = strtoupper($request->no_lab).' - '.$medical->nama.'('.$jk.')'.'.'.$mcu_file->getClientOriginalExtension();
            $request->file->storeAs('public/mcu', $file_mcu);
        }else{
            if($medical->employee->gender == 'Male'){
                $jk = 'L';
            }else{
                $jk = 'P';
            }

            $mcu_file = $request->file('file');
            $file_mcu = strtoupper($request->no_lab).' - '.$medical->employee->fullname.'('.$jk.')'.'.'.$mcu_file->getClientOriginalExtension();
            $request->file->storeAs('public/mcu', $file_mcu);
        }
        
        $post = Medical::where('id', $request->id_medical)->update([
            'no_lab' => strtoupper($request->no_lab),
            'kriteria_sehat' => $request->status,
            'lampiran_mcu' => $file_mcu
        ]);

        if(!empty($medical->jk)){
            //insert log user activity
            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'update';
            $insert->description = 'Modify "Non karyawan" Ireguler Medical Checkup '.'Nama "'.$medical->nama.'"';
            $insert->save();
        }else{
            //insert log user activity
            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'update';
            $insert->description = 'Modify "Karyawan" Ireguler Medical Checkup '.'Nama "'.$medical->employee->fullname.'"';
            $insert->save();
        }

        return redirect()->route('ireguler.index')->with('success', 'Update Medical Check Up Successfully.');
    }
    //end ireguler

    //start emp
    public function emp_index(Request $request){
        $user = auth()->user();
        $date = Carbon::now()->format('Y-m-d');
        // dd($date);
        $cek_medical = Medical::where('id_employees', $user->employee->id)->latest('tanggal_mcu')->first();
        if(!empty($cek_medical)){
            $medical = $cek_medical;
        }else{
            $medical = Medical::where('nama', $user->employee->fullname)->latest('tanggal_mcu')->first();
        }
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
        $tanggal_mcu = Medical::where('id_employees', $user->employee->id)->orderBy('tanggal_mcu','desc')->get()->pluck('tanggal_mcu','id');
        if($tanggal_mcu->isNotEmpty()){
            foreach($tanggal_mcu as $key_mcu => $val_mcu){
                if(!empty($val_mcu)){
                    $list_tanggal[$key_mcu] = date('Y', strtotime($val_mcu)); 
                }
            }
            $arr_tanggal = $list_tanggal;
        }else{
            $tanggal_mcu2 = Medical::where('nama', $user->employee->fullname)->orderBy('tanggal_mcu','desc')->get()->pluck('tanggal_mcu','id');
            if($tanggal_mcu2->isNotEmpty()){
                foreach($tanggal_mcu2 as $key_mcu2 => $val_mcu2){
                    if(!empty($val_mcu2)){
                        $list_tanggal2[$key_mcu2] = date('Y', strtotime($val_mcu2)); 
                    }
                }
                $arr_tanggal = $list_tanggal2;
            }else{
                $arr_tanggal = array();
            }
        }
        return view('pages.employee.medical.index', compact('user','medical','lab', 'latest_mcu', 'arr_tanggal'));
    }
    //end emp
}
