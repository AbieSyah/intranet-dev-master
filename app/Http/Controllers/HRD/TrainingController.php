<?php

namespace App\Http\Controllers\HRD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Notifications\AccountNotification;
use App\Models\Trainingrecord;
use App\Models\Trainingfkt;
use App\Models\Trainingfpkt;
use App\Models\Trainingperiode;
use App\Models\Trainingevaluasi;
use App\Models\Logcatatantraining;
use App\Models\Qrcodefkt;
use App\Models\Qrcodefpkt;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Vendor;
use App\Models\Log;
use Carbon\Carbon;
use App\Models\User;
use Spatie\Permission\Models\Role;
use PDF;
use Response;
use Exception;
use Yajra\DataTables\Facades\DataTables;

class TrainingController extends Controller
{
    public function index(Request $request){
        $year_now = date('Y');
        $arr_year = Trainingrecord::select(DB::raw('YEAR(created_at) as year'))->whereNotNull('created_at')->distinct()->get()->pluck('year')->toArray();
        if(!empty($arr_year)){
            $min =  min($arr_year)-1;
            $max = max($arr_year)+1;
        }else{
            $min =  date('Y')-1;
            $max = date('Y')+1;
        }
        if(!empty($request->from_year)){
            $query = Trainingrecord::whereYear('start_date', $request->from_year)->get()->unique('id_employee')->pluck('id_employee');
        }else{
            $query = Trainingrecord::get()->unique('id_employee')->pluck('id_employee');
        }
        if($query->isNotEmpty()){
            foreach($query as $key => $val){
                $employee = Employee::find($val);
                if(!empty($request->from_year)){
                    $total_training = Trainingrecord::whereYear('start_date', $request->from_year)->where('id_employee', $val)->get()->count();
                    $total_notif = Trainingrecord::whereYear('start_date', $request->from_year)->where('id_employee', $val)->where('status','ON PROGRESS')->get()->count();
                }else{
                    $total_training = Trainingrecord::whereYear('start_date', date('Y'))->where('id_employee', $val)->get()->count();
                    $total_notif = Trainingrecord::whereYear('start_date', date('Y'))->where('id_employee', $val)->where('status','ON PROGRESS')->get()->count();
                }
                $index = $key;
                $data[$index] = array();
                $data[$index]['id'] = $val;
                $data[$index]['nama'] = $employee->fullname;
                $data[$index]['bagian'] = $employee->department->name;
                $data[$index]['total'] = $total_training;
                $data[$index]['notif_progress'] = $total_notif;
            }
        }else{
            $data = array();
        }
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('action', function ($data) {
                    if(\Auth::user()->can('hrd.training.record.detail')){
                        if($data['notif_progress'] > 0){
                            $button = '<a href="'. route('training.detail', encrypt($data['id'])).'" data-toggle="tooltip" title="Detail" class="btn btn-info btn-sm position-relative"><i class="ri-eye-2-line"></i><span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">'.$data['notif_progress'].' <span class="visually-hidden">unread messages</span></span></a>';
                        }else{
                            $button = '<a href="'. route('training.detail', encrypt($data['id'])).'" data-toggle="tooltip" title="Detail" class="btn btn-info btn-sm"><i class="ri-eye-2-line"></i></a>';
                        }
                    }
                    return $button;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.training.index', compact('min','max','year_now'));
    }

    public function periode(Request $request){
        $query = Trainingperiode::all();
        if($query->isNotEmpty()){
            foreach($query as $qry){
                $index = $qry->id;
                $data[$index] = array();
                $data[$index]['id'] = $qry->id;
                $data[$index]['periode'] = $qry->periode;
                $data[$index]['status'] = $qry->status;
            }
        }else{
            $data = array();
        }
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('status', function($data){
                    if($data['status'] == 1) return '<a href="#" <span class="badge text-bg-success"> Active</span></a>';
                    if($data['status'] == 0) return '<a href="#" <span class="badge text-bg-danger"> Inactive</span></a>';
                })
                ->addColumn('action', function ($data) {
                    $button = $button = '<button data-toggle="tooltip" title="Edit" data-id="' . encrypt($data['id']) . '" data-original-title="Edit" class="btn btn-warning btn-sm edit-btn"><i class="ri-quill-pen-line"></i></button>';
                    return $button;
                })
                ->rawColumns(['status','action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.training.periode');
    }

    public function periode_edit(Request $request){
        $id = decrypt($request->input('id'));
        $periode = Trainingperiode::find($id);

        return response()->json($periode);
    }
    public function periode_store(Request $request){
        $user = auth()->user();
        if(!empty($request->id)){
            $update = Trainingperiode::where('id', $request->id)->update([
                'periode' => $request->tahun,
                'status' => $request->status
            ]);

            //insert log user activity
            $insert_log = new Log;
            $insert_log->user_id = $user->id;
            $insert_log->ip_address = $request->ip();
            $insert_log->action = 'update';
            $insert_log->description = 'Modify periode training "'.$request->tahun.'"';
            $insert_log->save();

            return redirect(route('training.periode'))->with('status','Periode Training has been updated');
        }else{
            $insert = new Trainingperiode;
            $insert->periode = $request->tahun;
            $insert->status = $request->status;
            $insert->save();

            //insert log user activity
            $insert_log = new Log;
            $insert_log->user_id = $user->id;
            $insert_log->ip_address = $request->ip();
            $insert_log->action = 'insert';
            $insert_log->description = 'Create periode training "'.$request->tahun.'"';
            $insert_log->save();

            return redirect(route('training.periode'))->with('status','Periode Training has been created');
        }
    }

    public function laporan_index(Request $request){
        $user = auth()->user();
        if($user->roles()->pluck('id')->first() == '51'){
            $query = Trainingrecord::where(function ($query) use ($user){
                $query->where('ttd_direktur', $user->employee_id)
                    ->whereNull('tgl_ttd_direktur')
                    ->where('status', 13);
            })->get();
            if($query->isNotEmpty()){
                foreach($query as $qry){
                    //ttd direktur
                    $index = $qry->id;
                    $data[$index] = array();
                    $data[$index]['id'] = $qry->id;
                    $data[$index]['pemohon'] = $qry->employee->fullname;
                    $data[$index]['judul'] = $qry->judul;
                    $data[$index]['tgl_laporan'] = $qry->tgl_laporan ?? '-';            
                }
            }else{
                $data = array();
            }
            if ($request->ajax()) {
                return DataTables::of($data)
                    ->addColumn('action', function ($data) {
                        $button = '<a href="'.route('training.laporan.approval',encrypt($data['id'])).'" class="btn btn-secondary btn-sm waves-effect waves-light"><i class="ri-checkbox-circle-line align-bottom me-1"></i> Approve</a>';                  
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->addIndexColumn()
                    ->make(true);
            }
        }
        if($user->roles()->pluck('id')->first() == '49'){
            $query = Trainingrecord::where(function ($query) use ($user){
                $query->where('ttd_presiden', $user->employee_id)
                    ->whereNull('tgl_ttd_presiden')
                    ->where('status', 13);
            })->get();
            if($query->isNotEmpty()){
                foreach($query as $qry){
                    //ttd presiden
                    $index = $qry->id;
                    $data[$index] = array();
                    $data[$index]['id'] = $qry->id;
                    $data[$index]['pemohon'] = $qry->employee->fullname;
                    $data[$index]['judul'] = $qry->judul;
                    $data[$index]['tgl_laporan'] = $qry->tgl_laporan ?? '-';            
                }
            }else{
                $data = array();
            }
            if ($request->ajax()) {
                return DataTables::of($data)
                    ->addColumn('action', function ($data) {
                        $button = '<a href="'.route('training.laporan.approval',encrypt($data['id'])).'" class="btn btn-secondary btn-sm waves-effect waves-light"><i class="ri-checkbox-circle-line align-bottom me-1"></i> Approve</a>';                  
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->addIndexColumn()
                    ->make(true);
            }
        }
        if($user->roles()->pluck('id')->first() == '2'){
            $query = Trainingrecord::where(function ($query) use ($user){
                $query->whereNotNull('tgl_ttd_presiden')
                    ->where('ttd_pic', $user->employee_id)
                    ->whereNull('tgl_ttd_pic')
                    ->where('status', 13);
            })->get();
            if($query->isNotEmpty()){
                foreach($query as $qry){
                    //ttd presiden
                    $index = $qry->id;
                    $data[$index] = array();
                    $data[$index]['id'] = $qry->id;
                    $data[$index]['pemohon'] = $qry->employee->fullname;
                    $data[$index]['judul'] = $qry->judul;
                    $data[$index]['tgl_laporan'] = $qry->tgl_laporan ?? '-';            
                }
            }else{
                $data = array();
            }
            if ($request->ajax()) {
                return DataTables::of($data)
                    ->addColumn('action', function ($data) {
                        $button = '<a href="'.route('training.laporan.approval',encrypt($data['id'])).'" class="btn btn-secondary btn-sm waves-effect waves-light"><i class="ri-checkbox-circle-line align-bottom me-1"></i> Approve</a>';                  
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->addIndexColumn()
                    ->make(true);
            }
        }
        //role super user
        if($user->roles()->pluck('id')->first() == '1'){
            $query = Trainingrecord::where('status', 13)->get();
            if($query->isNotEmpty()){
                foreach($query as $qry){
                    //ttd presiden
                    $index = $qry->id;
                    $data[$index] = array();
                    $data[$index]['id'] = $qry->id;
                    $data[$index]['pemohon'] = $qry->employee->fullname;
                    $data[$index]['judul'] = $qry->judul;
                    $data[$index]['tgl_laporan'] = $qry->tgl_laporan ?? '-';            
                }
            }else{
                $data = array();
            }
            if ($request->ajax()) {
                return DataTables::of($data)
                    ->addColumn('action', function ($data) {
                        $button = '<a href="'.route('training.laporan.approval',encrypt($data['id'])).'" class="btn btn-secondary btn-sm waves-effect waves-light"><i class="ri-checkbox-circle-line align-bottom me-1"></i> Approve</a>';                  
                        return $button;
                    })
                    ->rawColumns(['action'])
                    ->addIndexColumn()
                    ->make(true);
            }
        }
        return view('pages.hrd.training.laporan.index');
    }

    public function laporan_approval($id){
        $user = auth()->user();
        $id = decrypt($id);
        $query = Trainingrecord::find($id);
        return view('pages.hrd.training.laporan.approval',compact('query','user'));
    }

    public function laporan_approval_store(Request $request){
        $user = auth()->user();
        $record = Trainingrecord::find($request->id_record);
        //ttd presiden
        if($user->employee_id == $record->ttd_presiden){
            if($user->employee_id == $record->ttd_atasan){
                $post = Trainingrecord::where('id', $record->id)->where('ttd_presiden', $user->employee_id)->update([
                    'tgl_ttd_atasan' => Carbon::now(),
                    'tgl_ttd_presiden' => Carbon::now()
                ]);
            }else{
                $post = Trainingrecord::where('id', $record->id)->where('ttd_presiden', $user->employee_id)->update([
                    'tgl_ttd_presiden' => Carbon::now()
                ]);
            }
            //insert log user activity
            $insert_log = new Log;
            $insert_log->user_id = $user->id;
            $insert_log->ip_address = $request->ip();
            $insert_log->action = 'approved';
            $insert_log->description = 'Approve laporan training dengan nama pemohon'.'"'.$record->employee->fullname.'" tanggal laporan "'.$record->tgl_laporan.'" oleh "President Director"';
            $insert_log->save();

            $new_record = Trainingrecord::where('id', $request->id_record)->first();
            if(!empty($new_record->tgl_ttd_presiden) && !empty($new_record->tgl_ttd_direktur) && !empty($new_record->tgl_ttd_general_manager) && !empty($new_record->tgl_ttd_manager) && !empty($new_record->tgl_ttd_atasan) && !empty($new_record->tgl_ttd_hrd_ga_gm) && !empty($new_record->tgl_ttd_pic)){
                $post2 = Trainingrecord::where('id', $new_record->id)->where('ttd_presiden', $user->employee_id)->update([
                    'status' => 14
                ]);
            }
            if(!empty($new_record->tgl_ttd_presiden) && empty($new_record->tgl_ttd_direktur) && !empty($new_record->tgl_ttd_general_manager) && !empty($new_record->tgl_ttd_manager) && !empty($new_record->tgl_ttd_atasan) && !empty($new_record->tgl_ttd_hrd_ga_gm) && !empty($new_record->tgl_ttd_pic)){
                $post2 = Trainingrecord::where('id', $new_record->id)->where('ttd_presiden', $user->employee_id)->update([
                    'status' => 14
                ]);
            }
            if(!empty($new_record->tgl_ttd_presiden) && !empty($new_record->tgl_ttd_direktur) && empty($new_record->tgl_ttd_general_manager) && empty($new_record->tgl_ttd_manager) && !empty($new_record->tgl_ttd_atasan) && !empty($new_record->tgl_ttd_hrd_ga_gm) && !empty($new_record->tgl_ttd_pic)){
                $post2 = Trainingrecord::where('id', $new_record->id)->where('ttd_presiden', $user->employee_id)->update([
                    'status' => 14
                ]);
            }
            if($new_record->ttd_pic != $user->employee_id){
                //notification pic
                if(empty($new_record->tgl_ttd_pic)){
                    if(!empty($new_record->pic_ttd->email)){
                        $qry_user = User::where('employee_id', $new_record->ttd_pic)->first();
                        $details = [
                            'greeting' => 'Hi '.$new_record->pic_ttd->fullname,
                            'subject' => 'Laporan Pelaksanaan Training',
                            'body' => 'Ingin Menginformasikan bahwa ada laporan pelaksanaan training topik "'.$new_record->judul.'" yang membutuhkan approval anda',
                            'actionText' => 'Silahkan Login',
                            'actionURL' => url('/hrd/training/laporan/'.encrypt($new_record->id).'/approval'),
                            'thanks' => 'Terimakasih atas perhatiannya!!'
                        ];
                        //send mail
                        $qry_user->notify(new AccountNotification($details));
                    }
                }
            }
        }
        //ttd direktur
        if($user->employee_id == $record->ttd_direktur){
            if($user->employee_id == $record->ttd_direktur){
                $post = Trainingrecord::where('id', $record->id)->where('ttd_direktur', $user->employee_id)->update([
                    'tgl_ttd_atasan' => Carbon::now(),
                    'tgl_ttd_direktur' => Carbon::now()
                ]);
            }else{
                $post = Trainingrecord::where('id', $record->id)->where('ttd_direktur', $user->employee_id)->update([
                    'tgl_ttd_direktur' => Carbon::now()
                ]);
            }
            //insert log user activity
            $insert_log = new Log;
            $insert_log->user_id = $user->id;
            $insert_log->ip_address = $request->ip();
            $insert_log->action = 'approved';
            $insert_log->description = 'Approve laporan training dengan nama pemohon'.'"'.$record->employee->fullname.'" tanggal laporan "'.$record->tgl_laporan.'" oleh "Production Director / Jr. Director"';
            $insert_log->save();

            $new_record = Trainingrecord::where('id', $request->id_record)->first();
            if(!empty($new_record->tgl_ttd_presiden) && !empty($new_record->tgl_ttd_direktur) && !empty($new_record->tgl_ttd_general_manager) && !empty($new_record->tgl_ttd_manager) && !empty($new_record->tgl_ttd_atasan) && !empty($new_record->tgl_ttd_hrd_ga_gm) && !empty($new_record->tgl_ttd_pic)){
                $post2 = Trainingrecord::where('id', $new_record->id)->where('ttd_presiden', $user->employee_id)->update([
                    'status' => 14
                ]);
            }
            if(!empty($new_record->tgl_ttd_presiden) && empty($new_record->tgl_ttd_direktur) && !empty($new_record->tgl_ttd_general_manager) && !empty($new_record->tgl_ttd_manager) && !empty($new_record->tgl_ttd_atasan) && !empty($new_record->tgl_ttd_hrd_ga_gm) && !empty($new_record->tgl_ttd_pic)){
                $post2 = Trainingrecord::where('id', $new_record->id)->where('ttd_presiden', $user->employee_id)->update([
                    'status' => 14
                ]);
            }
            if(!empty($new_record->tgl_ttd_presiden) && !empty($new_record->tgl_ttd_direktur) && empty($new_record->tgl_ttd_general_manager) && empty($new_record->tgl_ttd_manager) && !empty($new_record->tgl_ttd_atasan) && !empty($new_record->tgl_ttd_hrd_ga_gm) && !empty($new_record->tgl_ttd_pic)){
                $post2 = Trainingrecord::where('id', $new_record->id)->where('ttd_presiden', $user->employee_id)->update([
                    'status' => 14
                ]);
            }
            if($new_record->ttd_presiden != $user->employee_id){
                //notification presiden
                if(empty($new_record->tgl_ttd_presiden)){
                    if(!empty($new_record->presiden_ttd->email)){
                        $qry_user = User::where('employee_id', $new_record->ttd_presiden)->first();
                        $details = [
                            'greeting' => 'Hi '.$new_record->presiden_ttd->fullname,
                            'subject' => 'Laporan Pelaksanaan Training',
                            'body' => 'Ingin Menginformasikan bahwa ada laporan pelaksanaan training topik "'.$new_record->judul.'" yang membutuhkan approval anda',
                            'actionText' => 'Silahkan Login',
                            'actionURL' => url('/hrd/training/laporan/'.encrypt($new_record->id).'/approval'),
                            'thanks' => 'Terimakasih atas perhatiannya!!'
                        ];
                        //send mail
                        $qry_user->notify(new AccountNotification($details));
                    }
                }
            }
        }
        //ttd pic
        if($user->employee_id == $record->ttd_pic){
            $post = Trainingrecord::where('id', $record->id)->where('ttd_pic', $user->employee_id)->update([
                'tgl_ttd_pic' => Carbon::now()
            ]);
            //insert log user activity
            $insert_log = new Log;
            $insert_log->user_id = $user->id;
            $insert_log->ip_address = $request->ip();
            $insert_log->action = 'approved';
            $insert_log->description = 'Approve laporan training dengan nama pemohon'.'"'.$record->employee->fullname.'" tanggal laporan "'.$record->tgl_laporan.'" oleh "HRD PIC Pelatihan"';
            $insert_log->save();

            $new_record = Trainingrecord::where('id', $request->id_record)->first();
            if(!empty($new_record->tgl_ttd_presiden) && !empty($new_record->tgl_ttd_direktur) && !empty($new_record->tgl_ttd_general_manager) && !empty($new_record->tgl_ttd_manager) && !empty($new_record->tgl_ttd_atasan) && !empty($new_record->tgl_ttd_hrd_ga_gm) && !empty($new_record->tgl_ttd_pic)){
                $post2 = Trainingrecord::where('id', $new_record->id)->where('ttd_pic', $user->employee_id)->update([
                    'status' => 14
                ]);
            }
            if($new_record->ttd_hrd_ga_gm != $user->employee_id){
                //notification hrd & ga GM
                if(empty($new_record->tgl_ttd_hrd_ga_gm)){
                    if(!empty($new_record->hrd_ga_gm_ttd->email)){
                        $qry_user = User::where('employee_id', $new_record->ttd_hrd_ga_gm)->first();
                        if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                            $details = [
                                'greeting' => 'Hi '.$new_record->hrd_ga_gm_ttd->fullname,
                                'subject' => 'Laporan Pelaksanaan Training',
                                'body' => 'Ingin Menginformasikan bahwa ada laporan pelaksanaan training topik "'.$new_record->judul.'" yang membutuhkan approval anda',
                                'actionText' => 'Silahkan Login',
                                'actionURL' => url('/employee/training/approval/'.encrypt($new_record->id).'/laporan'),
                                'thanks' => 'Terimakasih atas perhatiannya!!'
                            ];
                        }else{
                            $details = [
                                'greeting' => 'Hi '.$new_record->hrd_ga_gm_ttd->fullname,
                                'subject' => 'Laporan Pelaksanaan Training',
                                'body' => 'Ingin Menginformasikan bahwa ada laporan pelaksanaan training topik "'.$new_record->judul.'" yang membutuhkan approval anda',
                                'actionText' => 'Silahkan Login',
                                'actionURL' => url('/mytraining/approval/'.encrypt($new_record->id).'/form'),
                                'thanks' => 'Terimakasih atas perhatiannya!!'
                            ];
                        }
                        //send mail
                        $qry_user->notify(new AccountNotification($details));
                    }
                }
            }
        }
        return redirect(route('training.laporan.index'))->with('status','Laporan pelaksanaan training has been approved');
    }

    public function store(Request $request){
        $user = auth()->user();
        $arr_fkt = Trainingfkt::where('kode', $request->kode_fkt)->where('judul', $request->judul_fkt)->get();
        foreach($arr_fkt as $fkt){
            $insert[] = [
                'id_employee' => $fkt->id_peserta,
                'judul' => $fkt->judul,
                'detail' => $fkt->judul,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'id_vendor' => $fkt->id_vendor,
                'lokasi' => $request->lokasi,
                'biaya' => str_replace(".","",$request->biaya),
                'exp_date' => $request->exp_date,
                'id_fkt' => $fkt->id,
                'status' => 'ON PROGRESS',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
            //insert log user activity
            $insert_log = new Log;
            $insert_log->user_id = $user->id;
            $insert_log->ip_address = $request->ip();
            $insert_log->action = 'insert';
            $insert_log->description = 'Create schedule training peserta "'.$fkt->peserta->fullname.'"';
            $insert_log->save();
        }
        $post = Trainingrecord::insert($insert);
        $update = Trainingfkt::where('kode', $request->kode_fkt)->where('judul', $request->judul_fkt)->update([
            'status' => 'DONE'
        ]);

        return redirect(route('training.index'))->with('scheduled','open tab')->with('status','Schedule Training has been created');
    }

    public function hrd_verified(Request $request){
        $arr_id = Trainingfpkt::where('status','Approved')->get()->unique('id_fkt')->pluck('id_fkt');
        if($arr_id){
            $query = Trainingfkt::select('id','id_pemohon','judul','status')->whereIn('id', $arr_id)->get()->unique('judul');
            if($query->isNotEmpty()){
                foreach($query as $qry){
                    $index = $qry->id;
                    $data[$index] = array();
                    $data[$index]['id'] = $qry->id;
                    $data[$index]['pemohon'] = $qry->pemohon->fullname;
                    $data[$index]['judul'] = $qry->judul;
                    $data[$index]['status'] = $qry->status;
                }
            }else{
                $data = array();
            }
        }else{
            $data = array();
        }
        if($request->ajax()){
            return DataTables::of($data)
                ->addColumn('status', function($data){
                    if($data['status'] == 'PROPOSED') return '<a href="#" <span class="badge text-bg-warning"><i class="ri-time-line align-bottom"></i> Waiting Verification</span></a>';
                })
                ->addColumn('action', function ($data) {
                    $button = '<a href="'. route('training.fpkt.form', encrypt($data['id'])).'" data-toggle="tooltip" title="Verification" class="btn btn-info btn-sm"><i class="ri-task-line"></i></a>';               
                    return $button;
                })
                ->rawColumns(['status','action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.training.index');
    }

    public function hrd_approved(Request $request){
        $query = Trainingfkt::where('status', 'FINISHED')->get()->unique('kode_judul');
        if($query->isNotEmpty()){
            foreach($query as $qry){
                $index = $qry->kode_judul;
                $data[$index] = array();
                $data[$index]['judul'] = $qry->judul;
                $data[$index]['kode'] = $qry->kode;
                $data[$index]['status'] = $qry->status;
            }
        }else{
            $data = array(); 
        }
        if($request->ajax()){
            return DataTables::of($data)
                ->addColumn('status', function($data){
                    if($data['status'] == 'FINISHED') return '<a href="#" <span class="badge text-bg-secondary"><i class="ri-checkbox-circle-line align-bottom"></i> Approved</span></a>';
                    if($data['status'] == 'DONE') return '<a href="#" <span class="badge text-bg-secondary"><i class="ri-checkbox-circle-line align-bottom"></i> Approved</span></a>';
                })
                ->addColumn('action', function ($data) {
                    $button = '<a href="#" data-bs-target="#secondmodal" data-bs-toggle="modal" data-toggle="tooltip" title="Schedule" class="btn btn-info btn-sm btn-schedule"><i class="ri-calendar-todo-line"></i></a><input type="hidden" id="btn-kode" value="'.$data['kode'].'"><input type="hidden" id="btn-judul" value="'.$data['judul'].'">';               
                    return $button;
                })
                ->addColumn('peserta', function($data){
                    $arr_fkt = Trainingfkt::where('kode', $data['kode'])->where('judul', $data['judul'])->get();
                    if($arr_fkt->isNotEmpty()){
                        $peserta = '<div class="col-lg-12"><table class="table table-bordered" style="table-layout: fixed; width:100%;">';
                        $peserta .= '
                            <thead>
                                <tr>
                                    <th colspan="4" style="text-align: center;">Peserta Program Training</th>
                                </tr>
                                <tr>
                                    <th style="text-align: center;">Peserta</th>
                                    <th style="text-align: center;">Pelatihan</th>
                                    <th style="text-align: center;">Sifat Pelatihan</th>
                                    <th style="text-align: center;">Alasan</th>
                                </tr>
                            </thead>
                            ';
                        $peserta .= '<tbody>';
                        foreach($arr_fkt as $fkt){
                            $peserta .= '<tr>';                  
                            $peserta .= '<td>'.$fkt->peserta->fullname.'</td>';    
                            $peserta .= '<td>'.$fkt->judul.'</td>';    
                            $peserta .= '<td>'.$fkt->sifat.'</td>';    
                            $peserta .= '<td>'.$fkt->alasan.'</td>';
                            $peserta .= '</tr>';
                        } 
                        $peserta .= '</tbody></table></div>';
                    }else{
                        $peserta = '';
                    }
                    return $peserta;
                })
                ->rawColumns(['status','action','peserta'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.training.index');
    }
    public function hrd_schedule(Request $request){
        $query = Trainingfkt::where('status', 'DONE')->get()->unique('kode_judul');
        if($query->isNotEmpty()){
            foreach($query as $qry){
                $jumlah = Trainingfkt::where('kode', $qry->kode)->where('judul', $qry->judul)->count();
                $training = Trainingrecord::where('id_fkt', $qry->id)->first();
                $index = $qry->kode_judul;
                $data[$index] = array();
                $data[$index]['judul'] = $qry->judul;
                $data[$index]['kode'] = $qry->kode;
                $data[$index]['jml_peserta'] = $jumlah;
                $data[$index]['tgl_mulai'] = $training->start_date;
                $data[$index]['tgl_akhir'] = $training->end_date;
                $data[$index]['vendor'] = $qry->vendor->nama;
            }
        }else{
            $data = array(); 
        }
        if($request->ajax()){
            return DataTables::of($data)
                ->addColumn('action', function ($data) {
                    $button = '-';               
                    return $button;
                })
                ->addColumn('peserta', function($data){
                    $arr_fkt = Trainingfkt::where('kode', $data['kode'])->where('judul', $data['judul'])->get();
                    if($arr_fkt->isNotEmpty()){
                        $peserta = '<div class="col-lg-12"><table class="table table-bordered" style="table-layout: fixed; width:100%;">';
                        $peserta .= '
                            <thead>
                                <tr>
                                    <th colspan="4" style="text-align: center;">Peserta Program Training</th>
                                </tr>
                                <tr>
                                    <th style="text-align: center;">Peserta</th>
                                    <th style="text-align: center;">Pelatihan</th>
                                    <th style="text-align: center;">Sifat Pelatihan</th>
                                    <th style="text-align: center;">Alasan</th>
                                </tr>
                            </thead>
                            ';
                        $peserta .= '<tbody>';
                        foreach($arr_fkt as $fkt){
                            $peserta .= '<tr>';                  
                            $peserta .= '<td>'.$fkt->peserta->fullname.'</td>';    
                            $peserta .= '<td>'.$fkt->judul.'</td>';    
                            $peserta .= '<td>'.$fkt->sifat.'</td>';    
                            $peserta .= '<td>'.$fkt->alasan.'</td>';
                            $peserta .= '</tr>';
                        } 
                        $peserta .= '</tbody></table></div>';
                    }else{
                        $peserta = '';
                    }
                    return $peserta;
                })
                ->rawColumns(['action','peserta'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.training.index');
    }
    public function fpkt_form(Request $request, string $id = null){
        $user = auth()->user();
        $id = decrypt($id);
        $fkt = Trainingfkt::where('id', $id)->first();
        $qry_fkt = Trainingfkt::where('kode', $fkt->kode)->where('judul', $fkt->judul)->get();
        $arr_id = implode(',', $qry_fkt->pluck('id')->toArray());
        $fpkt = Trainingfpkt::where('id_fkt', $id)->first(); 
        $arr_fpkt = Trainingfpkt::where('id_fkt', $id)->get();
        $arr_peserta = Employee::whereIn('id', $qry_fkt->pluck('id_peserta'))->get();
        foreach($arr_peserta as $peserta){
            if(!empty($peserta->position->nama)){
                $jabatan[] = $peserta->position->nama;
            }else{
                $jabatan[] = '-';
            }
            if(!empty($peserta->department->name)){
                $department[] = $peserta->department->name;
            }else{
                $department[] = '-';
            }
        }
        $arr_jabatan = $jabatan;
        $arr_dept = $department;
        return view('pages.hrd.training.form-fpkt', compact('user','fkt','fpkt','arr_fpkt','arr_peserta','arr_jabatan','arr_dept','arr_id'));
    }
    public function fpkt_form_store(Request $request){
        $user = auth()->user();
        $data = $request->input();
        $arr_id = explode(',', $data['id_fkt']);
        $fkt = Trainingfkt::whereIn('id', $arr_id)->first();
        foreach($arr_id as $key => $value){           
            $post = Trainingfpkt::where('id_fkt', $value)->update([
                'id_hrd' => $user->employee_id,
                'date_hrd' => date('Y-m-d H:i:s'),
                'status' => 'Finished'
            ]);
            //ttd hrd
            $date_qr = date('Ymd');
            $insert_fpkt_qr = new Qrcodefpkt;
            $insert_fpkt_qr->id_fkt = $value;
            $insert_fpkt_qr->qr = $date_qr.$user->employee_id;
            $insert_fpkt_qr->date_approval = date('Y-m-d H:i:s');
            $insert_fpkt_qr->type = 3;
            $insert_fpkt_qr->save();
        }       

        //update status fkt
        $query_fkt = Trainingfkt::where('kode', $fkt->kode)->get()->pluck('id')->toArray();
        foreach($query_fkt as $key => $value){
            $cek_fkt = Trainingfpkt::where('id_fkt', $value)->first();
            if(!empty($cek_fkt)){
                $jml_fpkt = Trainingfpkt::where('id_fkt', $value)->where('status', '!=', 'Finished')->get()->count();
                $cek_fpkt = $jml_fpkt;
            }else{
                $cek_fpkt = 1;
            }
            $cek_status[] = $cek_fpkt;
        }
        
        $jumlah_status = array_sum($cek_status);
        if($jumlah_status == 0){
            $update = Trainingfkt::where('kode', $fkt->kode)->update([
                'status' => 'VERIFIED'
            ]);
            //ttd checker
            // $date_qr = date('Ymd');
            // $insert_fpkt_qr = new Qrcodefkt;
            // $insert_fpkt_qr->kode_fkt = $fkt->kode;
            // $insert_fpkt_qr->qr = $date_qr.$fkt->id_checker;
            // $insert_fpkt_qr->date_approval = date('Y-m-d H:i:s');
            // $insert_fpkt_qr->type = 2;
            // $insert_fpkt_qr->save();
        }
        //send notification atasan departemen        

        //insert log user activity
        $insert_log = new Log;
        $insert_log->user_id = $user->id;
        $insert_log->ip_address = $request->ip();
        $insert_log->action = 'update';
        $insert_log->description = 'Verified HRD "'.$fkt->judul.'" dengan nama pemohon'.'"'.$fkt->pemohon->fullname.'"';
        $insert_log->save();
        
        return redirect(route('training.index'))->with('status','Formulir Penilaian Kebutuhan Training '.$fkt->pemohon->fullname.' has been updated');
    }  
    public function training_detail(Request $request, $id){
        $kode = $id;
        $employee = Employee::find(decrypt($id));
        $vendors = Vendor::where('tipe', 'training')->get();
        $total_training = Trainingrecord::where('id_employee', decrypt($id))->get()->count();
        
        $query = Trainingrecord::where('id_employee', decrypt($kode))->get();
        foreach($query as $qry){
            $index = $qry->id;
            $data[$index] = array();
            $data[$index]['id'] = $qry->id;
            $data[$index]['judul'] = $qry->judul;
            $data[$index]['start_date'] = date('d M Y', strtotime($qry->start_date));
            $data[$index]['end_date'] = date('d M Y', strtotime($qry->end_date));
            $data[$index]['lokasi'] = $qry->lokasi;
            $data[$index]['biaya'] = "Rp ".number_format($qry->biaya,2);
            $data[$index]['status'] = $qry->status;
            $data[$index]['sertifikat'] = $qry->sertifikat;
        }
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('status', function($document){
                    if($document['status'] == 'ON PROGRESS') return '<a href="#" <span class="badge text-bg-warning"><i class="ri-time-line align-bottom"></i> On Progress</span></a>';
                    if($document['status'] == 'FINISHED') return '<a href="#" <span class="badge text-bg-success"><i class="ri-checkbox-circle-line align-bottom"></i> Finished</span></a>';
                })
                ->addColumn('action', function ($data) {
                    if(\Auth::user()->can('hrd.training.record.detail.edit')){
                        $button = '<button data-toggle="tooltip" title="Edit" data-id="' . encrypt($data['id']) . '" data-original-title="Edit" class="btn btn-warning btn-sm edit-btn"><i class="ri-quill-pen-line"></i></button>';                  
                    }else{
                        $button = '';
                    }
                    if(\Auth::user()->can('hrd.training.record.detail.sertifikat')){
                        if(!empty($data['sertifikat'])){
                            $button .= '&nbsp;';
                            $button .= '<button data-id="'. route('lampiran.sertifikat',encrypt($data['id'])) .'" data-bs-toggle="modal" data-bs-target="#modalSertifikat" title="Sertifikat" class="btn btn-danger btn-sm view-btn"><i class="ri-file-pdf-line"></i></button>';
                        }
                    }else{
                        $button .= '';
                    }
                    return $button;
                })
                ->rawColumns(['action','status'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.training.detail', compact('kode','employee', 'total_training','vendors'));
    }

    public function lampiran_sertifikat($id){
        $query = Trainingrecord::find(decrypt($id));
        $lampiran_sertifikat = public_path('storage/sertifikat/'.$query->sertifikat);

        // $file = File::get($lampiran_sertifikat);
        // $response = Response::make($file, 200);
        // $response->header('Content-Type', 'application/pdf');
        // $response->header('Content-Disposition', 'filename=' . '"'.$query->employee->fullname.'.pdf"');
        // $response->header('Content-Transfer-Encoding', 'binary');
        return response()->download($lampiran_sertifikat);
    }

    public function edit(Request $request){
        $id = decrypt($request->input('id'));
        $data = Trainingrecord::find($id);

        return response()->json($data);
    }

    public function update(Request $request){
        $user = auth()->user();
        //upload file
        $training = Trainingrecord::find($request->id_training);
        if(!empty($training->sertifikat)){
            if(!empty($request->file('file_sertifikat'))){
                $cek_file = storage_path('app/public/sertifikat/'.$training->sertifikat);
                if (File::exists($cek_file)) {
                    File::delete($cek_file);
                }
                $sertifikat_file = $request->file('file_sertifikat');
                $sertifikat_name = time().'.'.$sertifikat_file->getClientOriginalExtension();
                $request->file_sertifikat->storeAs('public/sertifikat', $sertifikat_name);
                
                $query = Trainingrecord::where('id', $request->id_training)->update([
                    'judul' => $request->judul,
                    'detail' => $request->detail,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'id_vendor' => $request->id_vendor,
                    'lokasi' => $request->lokasi,
                    'biaya' => $request->biaya,
                    'exp_date' => $request->exp_date,
                    'sertifikat' => $sertifikat_name,
                    'status' => $request->status
                ]);
            }else{
                $query = Trainingrecord::where('id', $request->id_training)->update([
                    'judul' => $request->judul,
                    'detail' => $request->detail,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'id_vendor' => $request->id_vendor,
                    'lokasi' => $request->lokasi,
                    'biaya' => $request->biaya,
                    'exp_date' => $request->exp_date,
                    'status' => $request->status
                ]);
            }
        }else{
            if(!empty($request->file('file_sertifikat'))){
                $sertifikat_file = $request->file('file_sertifikat');
                $sertifikat_name = time().'.'.$sertifikat_file->getClientOriginalExtension();
                $request->file_sertifikat->storeAs('public/sertifikat', $sertifikat_name);
                
                $query = Trainingrecord::where('id', $request->id_training)->update([
                    'judul' => $request->judul,
                    'detail' => $request->detail,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'id_vendor' => $request->id_vendor,
                    'lokasi' => $request->lokasi,
                    'biaya' => $request->biaya,
                    'exp_date' => $request->exp_date,
                    'sertifikat' => $sertifikat_name,
                    'status' => $request->status
                ]);
            }else{
                $query = Trainingrecord::where('id', $request->id_training)->update([
                    'judul' => $request->judul,
                    'detail' => $request->detail,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'id_vendor' => $request->id_vendor,
                    'lokasi' => $request->lokasi,
                    'biaya' => $request->biaya,
                    'exp_date' => $request->exp_date,
                    'status' => $request->status
                ]);
            }
        }   
        //insert log user activity
        $insert = new Log;
        $insert->user_id = $user->id;
        $insert->ip_address = $request->ip();
        $insert->action = 'update';
        $insert->description = 'Modify training '.'"'.$training->judul.'" nama karyawan "'.$training->employee->fullname.'"';
        $insert->save();

        return redirect()->route('training.detail', encrypt($training->id_employee))->with('success', 'Update Training Successfully.');
    }

    //start data training
    public function index_data(Request $request){
        $year_now = date('Y');
        $arr_year = Trainingrecord::select(DB::raw('YEAR(created_at) as year'))->whereNotNull('created_at')->distinct()->get()->pluck('year')->toArray();
        if(!empty($arr_year)){
            $min =  min($arr_year)-1;
            $max = max($arr_year)+1;
        }else{
            $min =  date('Y')-1;
            $max = date('Y')+1;
        }
        if(!empty($request->from_year)){
            $query = Trainingrecord::whereYear('start_date', $request->from_year)->get()->unique('id_employee')->pluck('id_employee');
        }else{
            $query = Trainingrecord::get()->unique('id_employee')->pluck('id_employee');
        }
        if($query->isNotEmpty()){
            foreach($query as $key => $val){
                $employee = Employee::find($val);
                if(!empty($request->from_year)){
                    $total_training = Trainingrecord::whereYear('start_date', $request->from_year)->where('id_employee', $val)->get()->count();
                    $total_notif = Trainingrecord::whereYear('start_date', $request->from_year)->where('id_employee', $val)->where('status',13)->get()->count();
                }else{
                    $total_training = Trainingrecord::whereYear('start_date', date('Y'))->where('id_employee', $val)->get()->count();
                    $total_notif = Trainingrecord::whereYear('start_date', date('Y'))->where('id_employee', $val)->where('status',13)->get()->count();
                }
                $index = $key;
                $data[$index] = array();
                $data[$index]['id'] = $val;
                $data[$index]['nik'] = $employee->nik;
                $data[$index]['nama'] = $employee->fullname;
                $data[$index]['bagian'] = $employee->department->name;
                $data[$index]['total'] = $total_training;
                $data[$index]['notif_progress'] = $total_notif;
            }
        }else{
            $data = array();
        }
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('action', function ($data) {
                    if(\Auth::user()->can('hrd.training.record.detail')){
                        if($data['notif_progress'] > 0){
                            $button = '<a href="'. route('training.data.detail', encrypt($data['id'])).'" data-toggle="tooltip" title="Detail" class="btn btn-info btn-sm position-relative"><i class="ri-eye-2-line"></i><span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">'.$data['notif_progress'].' <span class="visually-hidden">unread messages</span></span></a>';
                        }else{
                            $button = '<a href="'. route('training.data.detail', encrypt($data['id'])).'" data-toggle="tooltip" title="Detail" class="btn btn-info btn-sm"><i class="ri-eye-2-line"></i></a>';
                        }
                    }else{
                        $button = '';
                    }
                    return $button;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.training.data.index', compact('min','max','year_now'));
    }

    public function training_data_detail(Request $request, $id){
        $kode = $id;
        $employee = Employee::find(decrypt($id));
        $vendors = Vendor::where('tipe', 'training')->get();
        $total_training = Trainingrecord::where('id_employee', decrypt($id))->get()->count();
        
        $query = Trainingrecord::where('id_employee', decrypt($kode))->get();
        foreach($query as $qry){
            $index = $qry->id;
            $data[$index] = array();
            $data[$index]['id'] = $qry->id;
            $data[$index]['judul'] = $qry->judul;
            $data[$index]['start_date'] = date('d M Y', strtotime($qry->start_date));
            $data[$index]['end_date'] = date('d M Y', strtotime($qry->end_date));
            $data[$index]['lokasi'] = $qry->lokasi;
            $data[$index]['biaya'] = "Rp ".number_format($qry->biaya,2);
            $data[$index]['status'] = $qry->status;
            $data[$index]['sertifikat'] = $qry->sertifikat ?? null;
        }
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('status', function($document){
                    if($document['status'] == 13) return '<a href="#" <span class="badge text-bg-warning"><i class="ri-time-line align-bottom"></i> On Progress</span></a>';
                    if($document['status'] == 14) return '<a href="#" <span class="badge text-bg-success"><i class="ri-checkbox-circle-line align-bottom"></i> Finished</span></a>';
                })
                ->addColumn('action', function ($data) {
                    if(\Auth::user()->can('hrd.training.record.detail.edit')){                 
                        $list_edit = '<li><a href="javascript:void(0);" data-id="' . encrypt($data['id']) . '" class="dropdown-item edit-btn"><i class="ri-quill-pen-line align-bottom me-1 text-muted"></i> Edit</a></li>';
                    }else{
                        $list_edit = '';
                    }
                    if(\Auth::user()->can('hrd.training.record.detail.sertifikat')){
                        if(!empty($data['sertifikat'])){
                            $list_sertifikat = '<li><a href="'.route('lampiran.sertifikat',encrypt($data['id'])).'" target="_blank" class="dropdown-item"><i class="ri-file-download-line align-bottom me-1 text-muted"></i> Sertifikat</a></li>';
                        }else{
                            $list_sertifikat = '';
                        }
                    }else{
                        $list_sertifikat = '';
                    }
                    $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_edit.$list_sertifikat.'</ul></div>';
                    return $button;
                })
                ->rawColumns(['action','status'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.training.data.detail', compact('kode','employee', 'total_training','vendors'));
    }

    public function update_data(Request $request){
        $user = auth()->user();
        //upload file
        $training = Trainingrecord::find($request->id_training);
        if(!empty($training->sertifikat)){
            if(!empty($request->file('file_sertifikat'))){
                $cek_file = storage_path('app/public/sertifikat/'.$training->sertifikat);
                if (File::exists($cek_file)) {
                    File::delete($cek_file);
                }
                $sertifikat_file = $request->file('file_sertifikat');
                $sertifikat_name = time().'.'.$sertifikat_file->getClientOriginalExtension();
                $request->file_sertifikat->storeAs('public/sertifikat', $sertifikat_name);
                
                $query = Trainingrecord::where('id', $request->id_training)->update([
                    'judul' => $request->judul,
                    'detail' => $request->detail,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'id_vendor' => $request->id_vendor,
                    'lokasi' => $request->lokasi,
                    'biaya' => $request->biaya,
                    'exp_date' => $request->exp_date,
                    'sertifikat' => $sertifikat_name,
                    'status' => $request->status
                ]);
            }else{
                $query = Trainingrecord::where('id', $request->id_training)->update([
                    'judul' => $request->judul,
                    'detail' => $request->detail,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'id_vendor' => $request->id_vendor,
                    'lokasi' => $request->lokasi,
                    'biaya' => $request->biaya,
                    'exp_date' => $request->exp_date,
                    'status' => $request->status
                ]);
            }
        }else{
            if(!empty($request->file('file_sertifikat'))){
                $sertifikat_file = $request->file('file_sertifikat');
                $sertifikat_name = time().'.'.$sertifikat_file->getClientOriginalExtension();
                $request->file_sertifikat->storeAs('public/sertifikat', $sertifikat_name);
                
                $query = Trainingrecord::where('id', $request->id_training)->update([
                    'judul' => $request->judul,
                    'detail' => $request->detail,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'id_vendor' => $request->id_vendor,
                    'lokasi' => $request->lokasi,
                    'biaya' => $request->biaya,
                    'exp_date' => $request->exp_date,
                    'sertifikat' => $sertifikat_name,
                    'status' => $request->status
                ]);
            }else{
                $query = Trainingrecord::where('id', $request->id_training)->update([
                    'judul' => $request->judul,
                    'detail' => $request->detail,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'id_vendor' => $request->id_vendor,
                    'lokasi' => $request->lokasi,
                    'biaya' => $request->biaya,
                    'exp_date' => $request->exp_date,
                    'status' => $request->status
                ]);
            }
        }   
        //insert log user activity
        $insert = new Log;
        $insert->user_id = $user->id;
        $insert->ip_address = $request->ip();
        $insert->action = 'update';
        $insert->description = 'Modify training '.'"'.$training->judul.'" nama karyawan "'.$training->employee->fullname.'"';
        $insert->save();

        return redirect()->route('training.data.detail', encrypt($training->id_employee))->with('success', 'Update Training Successfully.');
    }

    public function index_proggress(Request $request){
        $count_jml_verified = Trainingfkt::where('tipe','pti')->whereNotNull('date_checker')->whereNull('date_verified_pic')->where('status',2)->get()->unique('judul')->count(); 
        $count_jml_verified_ptt = Trainingfkt::where('tipe','ptt')->whereNotNull('date_checker')->whereNull('date_verified_pic')->where('status',2)->get()->unique('judul')->count(); 
        $query = Trainingfkt::where('tipe','pti')->where('status','!=', 1)->get()->unique('kode_judul');
        if($query->isNotEmpty()){
            foreach($query as $qry){
                $index = $qry->id;
                $data[$index] = array();
                $data[$index]['id'] = $qry->id;
                $data[$index]['pemohon'] = $qry->pemohon->fullname; 
                $data[$index]['judul'] = $qry->judul; 
                $data[$index]['jml_peserta'] = Trainingfkt::where('tipe','pti')->where('kode_judul', $qry->kode_judul)->count();
                $data[$index]['kode_judul'] = $qry->kode_judul; 
            }
        }else{
            $data = array();
        }
        if($request->ajax()){
            return DataTables::of($data)
                ->addColumn('action', function ($data) {
                    $list_print_fkt = '<li><a href="'.route('training.pti.fkt.pdf', encrypt($data['kode_judul'])).'" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FKT</a></li>';
                    $list_print_fpkt = '<li><a href="'.route('training.pti.fpkt.pdf', encrypt($data['kode_judul'])).'" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FPKT</a></li>';
                    $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_print_fkt.$list_print_fpkt.'</ul></div>';              
                    return $button;
                })
                ->addColumn('peserta', function($data){
                    $query = Trainingfkt::where('tipe','pti')->where('kode_judul', $data['kode_judul'])->get();
                    if($query->isNotEmpty()){
                        $peserta = '<div class="col-lg-12"><table class="table table-bordered" style="table-layout: fixed; width:100%;">';
                        $peserta .= '
                            <thead>
                                <tr>
                                    <th colspan="3" style="text-align: center;">Submission of Training Program</th>
                                </tr>
                                <tr>
                                    <th style="text-align: center;">Participant</th>
                                    <th style="text-align: center;">Month of Implementation</th>
                                    <th style="text-align: center;">Status</th>
                                </tr>
                            </thead>
                            ';
                        $peserta .= '<tbody>';
                        foreach($query as $qry){
                            $peserta .= '<tr>';                    
                            $peserta .= '<td>'.$qry->peserta->fullname.'</td>';    
                            $peserta .= '<td>'.$qry->bulan_pelaksanaan.'</td>';
                            if($qry->status == 2) $data_status = '<a href="#" <span class="badge text-bg-warning"><i class="ri-time-line align-bottom"></i> '.$qry->training_status->name.'</span></a>';
                            if($qry->status == 3) $data_status = '<a href="#" <span class="badge text-bg-info"><i class="ri-time-line align-bottom"></i> '.$qry->training_status->name.'</span></a>';
                            if($qry->status == 4) $data_status = '<a href="#" <span class="badge text-bg-info"><i class="ri-time-line align-bottom"></i> '.$qry->training_status->name.'</span></a>';
                            if($qry->status == 5) $data_status = '<a href="#" <span class="badge text-bg-info"><i class="ri-time-line align-bottom"></i> '.$qry->training_status->name.'</span></a>';
                            if($qry->status == 6) $data_status = '<a href="#" <span class="badge text-bg-success"><i class="ri-time-line align-bottom"></i> '.$qry->training_status->name.'</span></a>';
                            if($qry->status == 7) $data_status = '<a href="#" <span class="badge text-bg-success"><i class="ri-time-line align-bottom"></i> '.$qry->training_status->name.'</span></a>';
                            $peserta .= '<td>'.$data_status.'</td>';    
                            $peserta .= '</tr>';
                        } 
                        $peserta .= '</tbody></table></div>';
                    }else{
                        $peserta = '';
                    }
                    return $peserta;
                })
                ->rawColumns(['status','action','peserta'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.training.data.proggress', compact('count_jml_verified','count_jml_verified_ptt'));
    }
    public function index_verification_proggress(Request $request){
        $query = Trainingfkt::where('tipe','pti')->whereNotNull('date_checker')->whereNull('date_verified_pic')->where('status',2)->get()->unique('kode_judul');
        if($query->isNotEmpty()){
            foreach($query as $qry){
                $index = $qry->id;
                $data[$index] = array();
                $data[$index]['id'] = $qry->id;
                $data[$index]['pemohon'] = $qry->pemohon->fullname; 
                $data[$index]['judul'] = $qry->judul; 
                $data[$index]['jml_peserta'] = Trainingfkt::where('tipe','pti')->where('kode_judul', $qry->kode_judul)->count();
                $data[$index]['kode_judul'] = $qry->kode_judul; 
            }
        }else{
            $data = array();
        }
        if($request->ajax()){
            return DataTables::of($data)
                ->addColumn('action', function ($data) {
                    $list_approve = '<li><a href="#" data-id="'.encrypt($data['kode_judul']).'" data-bs-toggle="modal" data-bs-target=".bs-example-modal-center" class="dropdown-item view-approve"><i class="ri-checkbox-line align-bottom me-2 text-muted"></i> Approve</a></li>';
                    $list_print_fkt = '<li><a href="'.route('training.pti.fkt.pdf', encrypt($data['kode_judul'])).'" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FKT</a></li>';
                    $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_approve.$list_print_fkt.'</ul></div>';              
                    return $button;
                })
                ->addColumn('peserta', function($data){
                    $query = Trainingfkt::where('tipe','pti')->where('kode_judul', $data['kode_judul'])->get();
                    if($query->isNotEmpty()){
                        $peserta = '<div class="col-lg-12"><table class="table table-bordered" style="table-layout: fixed; width:100%;">';
                        $peserta .= '
                            <thead>
                                <tr>
                                    <th colspan="3" style="text-align: center;">Submission of Training Program</th>
                                </tr>
                                <tr>
                                    <th style="text-align: center;">Participant</th>
                                    <th style="text-align: center;">Month of Implementation</th>
                                    <th style="text-align: center;">Status</th>
                                </tr>
                            </thead>
                            ';
                        $peserta .= '<tbody>';
                        foreach($query as $qry){
                            $peserta .= '<tr>';                    
                            $peserta .= '<td>'.$qry->peserta->fullname.'</td>';    
                            $peserta .= '<td>'.$qry->bulan_pelaksanaan.'</td>';
                            if($qry->status == 2) $data_status = '<a href="#" <span class="badge text-bg-warning"><i class="ri-time-line align-bottom"></i> '.$qry->training_status->name.'</span></a>';
                            if($qry->status == 3) $data_status = '<a href="#" <span class="badge text-bg-info"><i class="ri-time-line align-bottom"></i> '.$qry->training_status->name.'</span></a>';
                            if($qry->status == 4) $data_status = '<a href="#" <span class="badge text-bg-info"><i class="ri-time-line align-bottom"></i> '.$qry->training_status->name.'</span></a>';
                            if($qry->status == 5) $data_status = '<a href="#" <span class="badge text-bg-info"><i class="ri-time-line align-bottom"></i> '.$qry->training_status->name.'</span></a>';
                            if($qry->status == 6) $data_status = '<a href="#" <span class="badge text-bg-success"><i class="ri-time-line align-bottom"></i> '.$qry->training_status->name.'</span></a>';
                            if($qry->status == 7) $data_status = '<a href="#" <span class="badge text-bg-success"><i class="ri-time-line align-bottom"></i> '.$qry->training_status->name.'</span></a>';
                            $peserta .= '<td>'.$data_status.'</td>';    
                            $peserta .= '</tr>';
                        } 
                        $peserta .= '</tbody></table></div>';
                    }else{
                        $peserta = '';
                    }
                    return $peserta;
                })
                ->rawColumns(['status','action','peserta'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.training.data.proggress');
    }
    public function store_verification_proggress(Request $request){
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $query = Trainingfkt::where('kode_judul', decrypt($request->kode_judul))->first();
             if($query->kategori == 'free'){
                $post = Trainingfkt::where('kode_judul', decrypt($request->kode_judul))->update([
                    'id_verified_pic' => $user->employee_id,
                    'date_verified_pic' => date('Y-m-d H:i:s')
                ]);
                //send email to pemohon
                if(!empty($query->pemohon->email)){
                    $qry_user = User::where('employee_id', $query->id_pemohon)->first();
                    if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                        $details = [
                            'greeting' => 'Hi '.$query->pemohon->fullname,
                            'subject' => 'Formulir Penilaian Kebutuhan Training',
                            'body' => 'Ingin Menginformasikan bahwa untuk melengkapi usulan topik training "'.$query->judul.'" perlu mengisi formulir penilaian kebutuhan training',
                            'actionText' => 'Silahkan Login',
                            'actionURL' => url('/employee/training/fpkt/pti/'.encrypt($query->id).'/form'),
                            'thanks' => 'Terimakasih atas perhatiannya!!'
                        ];
                    }else{
                        $details = [
                            'greeting' => 'Hi '.$query->pemohon->fullname,
                            'subject' => 'Formulir Penilaian Kebutuhan Training',
                            'body' => 'Ingin Menginformasikan bahwa untuk melengkapi usulan topik training "'.$query->judul.'" perlu mengisi formulir penilaian kebutuhan training',
                            'actionText' => 'Silahkan Login',
                            'actionURL' => url('/mytrainingfpkt/pti/'.encrypt($query->id).'/form'),
                            'thanks' => 'Terimakasih atas perhatiannya!!'
                        ];
                    }
                    //send mail
                    $qry_user->notify(new AccountNotification($details));
                }
                //send email to pic hrd
                // $users = User::whereHas(
                //     'roles', function($q){
                //         $q->where('id', 2);
                //     }
                // )->get();
                // if($users->isNotEmpty()){
                //     foreach($users as $key_user){
                //         if(!empty($key_user->email)){
                //             $qry_user = User::where('employee_id', $key_user->employee_id)->first();
                //             $details = [
                //                 'greeting' => 'Hi '.$qry_user->name,
                //                 'subject' => 'Scheduling Training (PTI)',
                //                 'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$query->judul.'" yang sudah disetujui dan perlu dijadwalkan',
                //                 'actionText' => 'Silahkan Login',
                //                 'actionURL' => url('/hrd/training/pti'),
                //                 'thanks' => 'Terimakasih atas perhatiannya!!'
                //             ];
                //             //send mail
                //             $qry_user->notify(new AccountNotification($details));
                //         }
                //     }
                // }
            }else{
                if($query->pemohon->department->approval_code == 2){
                    $post = Trainingfkt::where('kode_judul', decrypt($request->kode_judul))->update([
                        'id_verified_pic' => $user->employee_id,
                        'date_verified_pic' => date('Y-m-d H:i:s'),
                        'status' => 5
                    ]);
                    //send email to mr. sakurai
                    $users = User::whereHas(
                        'roles', function($q){
                            $q->where('id', 49);
                        }
                    )->get();
                    if($users->isNotEmpty()){
                        foreach($users as $key_user){
                            if(!empty($key_user->email)){
                                $qry_user = User::where('employee_id', $key_user->employee_id)->first();
                                $details = [
                                    'greeting' => 'Hi '.$qry_user->name,
                                    'subject' => 'Approval Training (PTI)',
                                    'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$query->judul.'" yang memerlukan persetujuan anda',
                                    'actionText' => 'Silahkan Login',
                                    'actionURL' => url('/hrd/training/pti'),
                                    'thanks' => 'Terimakasih atas perhatiannya!!'
                                ];
                                //send mail
                                $qry_user->notify(new AccountNotification($details));
                            }
                        }
                    }
                }else{
                    $post = Trainingfkt::where('kode_judul', decrypt($request->kode_judul))->update([
                        'id_verified_pic' => $user->employee_id,
                        'date_verified_pic' => date('Y-m-d H:i:s'),
                        'status' => 4
                    ]);
                    //send email to mr. mizukami
                    $users = User::whereHas(
                        'roles', function($q){
                            $q->where('id', 51);
                        }
                    )->get();
                    if($users->isNotEmpty()){
                        foreach($users as $key_user){
                            if(!empty($key_user->email)){
                                $qry_user = User::where('employee_id', $key_user->employee_id)->first();
                                $details = [
                                    'greeting' => 'Hi '.$qry_user->name,
                                    'subject' => 'Approval Training (PTI)',
                                    'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$query->judul.'" yang memerlukan persetujuan anda',
                                    'actionText' => 'Silahkan Login',
                                    'actionURL' => url('/hrd/training/pti'),
                                    'thanks' => 'Terimakasih atas perhatiannya!!'
                                ];
                                //send mail
                                $qry_user->notify(new AccountNotification($details));
                            }
                        }
                    }
                }
            }

            //update log user activity
            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'verified';
            $insert->description = 'Verified formulir kebutuhan training dengan nama pemohon'.'"'.$query->pemohon->fullname.'" tujuan "Program Training Insidentil (PTI)"';
            $insert->save();

            DB::commit();

            return response()->json(['message' => "$query->judul has been verified"], 200);
        }catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }
    public function index_proggress_ptt(Request $request){
        $query = Trainingfkt::where('tipe', 'ptt')->where('status','!=',1)->get()->unique('tahun_usulan');
        if($query->isNotEmpty()){
            foreach($query as $qry){
                $index = $qry->id;
                $document = array();
                $document[$index]['tahun_usulan'] = $qry->tahun_usulan;
                $document[$index]['jumlah_usulan'] = Trainingfkt::where('tahun_usulan', $qry->tahun_usulan)->where('tipe','ptt')->count();
            }
        }else{
            $document = array();
        }
        if ($request->ajax()) {          
            return DataTables::of($document)
                ->addColumn('action', function ($document) {   
                    // $list_print_fkt = '<li><a href="'.route('training.ptt.fkt.pdf', encrypt($document['kode_judul'])).'" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FKT</a></li>';
                    // $list_print_fpkt = '<li><a href="'.route('training.ptt.fpkt.pdf', encrypt($document['kode_judul'])).'" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FKT</a></li>';
                    // $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_print_fkt.$list_print_fpkt.'</ul></div>';              
                    $button = '<a href="'. route('training.data.proggress.ptt.detail', encrypt($document['tahun_usulan'])).'" data-toggle="tooltip" title="Detail" class="btn btn-info btn-sm"><i class="ri-eye-line"></i></a>';
                    return $button;
                })                
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        } 
    }

    public function index_proggress_ptt_detail($id){
        $user = auth()->user();
        $tahun_usulan = decrypt($id);
        $query_fkt = Trainingfkt::where('tahun_usulan', $tahun_usulan)->where('tipe','ptt')
            ->whereNotNull('date_checker')
            ->get()->unique('kode_judul');
            return view('pages.hrd.training.data.progress-detail-ptt', compact('user','tahun_usulan','query_fkt'));
    }

    public function store_verification_proggress_ptt(Request $request){
        DB::beginTransaction();
        try {
            if($request->tipe_submit == 'hrd'){
                $user = auth()->user();
                $query = Trainingfkt::where('kode_judul', decrypt($request->kode_judul))->first();
                if($query->kategori == 'free'){
                    $post = Trainingfkt::where('kode_judul', decrypt($request->kode_judul))->update([
                        'id_verified_pic' => $user->employee_id,
                        'date_verified_pic' => date('Y-m-d H:i:s')
                    ]);
                    //send email to pemohon
                    if(!empty($query->pemohon->email)){
                        $qry_user = User::where('employee_id', $query->id_pemohon)->first();
                        if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                            $details = [
                                'greeting' => 'Hi '.$query->pemohon->fullname,
                                'subject' => 'Formulir Penilaian Kebutuhan Training',
                                'body' => 'Ingin Menginformasikan bahwa untuk melengkapi usulan topik training "'.$query->judul.'" perlu mengisi formulir penilaian kebutuhan training',
                                'actionText' => 'Silahkan Login',
                                'actionURL' => url('/employee/training/fpkt/ptt/'.encrypt($query->id).'/form'),
                                'thanks' => 'Terimakasih atas perhatiannya!!'
                            ];
                        }else{
                            $details = [
                                'greeting' => 'Hi '.$query->pemohon->fullname,
                                'subject' => 'Formulir Penilaian Kebutuhan Training',
                                'body' => 'Ingin Menginformasikan bahwa untuk melengkapi usulan topik training "'.$query->judul.'" perlu mengisi formulir penilaian kebutuhan training',
                                'actionText' => 'Silahkan Login',
                                'actionURL' => url('/mytrainingfpkt/ptt/'.encrypt($query->id).'/form'),
                                'thanks' => 'Terimakasih atas perhatiannya!!'
                            ];
                        }
                        //send mail
                        $qry_user->notify(new AccountNotification($details));
                    }
                }else{
                    if($query->pemohon->department->approval_code == 2){
                        $post = Trainingfkt::where('kode_judul', decrypt($request->kode_judul))->update([
                            'id_verified_pic' => $user->employee_id,
                            'date_verified_pic' => date('Y-m-d H:i:s'),
                            'is_notif' => 1,
                            'status' => 5
                        ]);
                        //send email to mr. sakurai
                        $users = User::whereHas(
                            'roles', function($q){
                                $q->where('id', 49);
                            }
                        )->get();
                        if($users->isNotEmpty()){
                            foreach($users as $key_user){
                                if(!empty($key_user->email)){
                                    $qry_user = User::where('employee_id', $key_user->employee_id)->first();
                                    $details = [
                                        'greeting' => 'Hi '.$qry_user->name,
                                        'subject' => 'Approval Training (PTT)',
                                        'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$query->judul.'" yang memerlukan persetujuan anda',
                                        'actionText' => 'Silahkan Login',
                                        'actionURL' => url('/hrd/training/ptt'),
                                        'thanks' => 'Terimakasih atas perhatiannya!!'
                                    ];
                                    //send mail
                                    $qry_user->notify(new AccountNotification($details));
                                }
                            }
                        }
                    }else{
                        $post = Trainingfkt::where('kode_judul', decrypt($request->kode_judul))->update([
                            'id_verified_pic' => $user->employee_id,
                            'date_verified_pic' => date('Y-m-d H:i:s'),
                            'status' => 4
                        ]);
                        //send email to mr. mizukami
                        $users = User::whereHas(
                            'roles', function($q){
                                $q->where('id', 51);
                            }
                        )->get();
                        if($users->isNotEmpty()){
                            foreach($users as $key_user){
                                if(!empty($key_user->email)){
                                    $qry_user = User::where('employee_id', $key_user->employee_id)->first();
                                    $details = [
                                        'greeting' => 'Hi '.$qry_user->name,
                                        'subject' => 'Approval Training (PTT)',
                                        'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$query->judul.'" yang memerlukan persetujuan anda',
                                        'actionText' => 'Silahkan Login',
                                        'actionURL' => url('/hrd/training/ptt'),
                                        'thanks' => 'Terimakasih atas perhatiannya!!'
                                    ];
                                    //send mail
                                    $qry_user->notify(new AccountNotification($details));
                                }
                            }
                        }
                    }
                }
                //update log user activity
                $insert = new Log;
                $insert->user_id = $user->id;
                $insert->ip_address = $request->ip();
                $insert->action = 'verified';
                $insert->description = 'Verified formulir kebutuhan training dengan nama pemohon'.'"'.$query->pemohon->fullname.'" tujuan "Program Training Tahunan (PTT)"';
                $insert->save();
            }

            DB::commit();

            return response()->json(['message' => "$query->judul has been verified"], 200);
        }catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    public function index_verification_proggress_ptt(Request $request){
        $query = Trainingfkt::where('tipe', 'ptt')->whereNull('date_verified_pic')->where('status',2)->get()->unique('tahun_usulan');
        if($query->isNotEmpty()){
            foreach($query as $qry){
                $index = $qry->id;
                $document = array();
                $document[$index]['tahun_usulan'] = $qry->tahun_usulan;
                $document[$index]['jumlah_usulan'] = Trainingfkt::where('tahun_usulan', $qry->tahun_usulan)->where('tipe','ptt')->count();
            }
        }else{
            $document = array();
        }
        if ($request->ajax()) {          
            return DataTables::of($document)
                ->addColumn('action', function ($document) {              
                    $button = '<a href="'. route('training.data.proggress.ptt.detail.verified', encrypt($document['tahun_usulan'])).'" data-toggle="tooltip" title="Detail" class="btn btn-info btn-sm"><i class="ri-task-line"></i></a>';
                    return $button;
                })                
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        } 
    }

    public function index_proggress_ptt_detail_verified($id){
        $user = auth()->user();
        $tahun_usulan = decrypt($id);
        $query_fkt = Trainingfkt::where('tahun_usulan', $tahun_usulan)->where('tipe','ptt')
            ->whereNotNull('date_checker')
            ->whereNull('date_verified_pic')
            ->where('status',2)
            ->get()->unique('kode_judul');
            return view('pages.hrd.training.data.progress-detail-verified-ptt', compact('user','tahun_usulan','query_fkt'));
    }
    public function index_proggress_ptt_detail_back(Request $request){
        return redirect(route('training.data.proggress'))->with('tab_ptt_all','open tab');
    }
    public function index_proggress_ptt_detail_verified_back(Request $request){
        return redirect(route('training.data.proggress'))->with('ptt-tab-verified','open tab');
    }
    //end data training

    //start scheduled training
    public function index_scheduled(Request $request){
        $vendors = Vendor::where('tipe','training')->get();
        $training_record = Trainingrecord::get()->unique('kode_fkt');
        $date_now = date('Y-m-d');
        if($training_record->isNotEmpty()){
            foreach($training_record as $record){
                $data['id'] = $record->id;
                $data['title'] = $record->judul;
                $data['start'] = $record->start_date;
                $data['end'] = date('Y-m-d',strtotime($record->end_date . "+1 days"));
                if($record->status == 13){
                    $data['className'] = 'bg-soft-warning border-warning';
                }else{
                    $data['className'] = 'bg-soft-success border-success';
                }
                $start = date('d M Y', strtotime($record->start_date));
                $end = date('d M Y',strtotime($record->end_date . "+1 days"));
                $data['dateup'] = $start.' to '.$end;

                $data_all[] = $data;
            }
        }else{
            $data_all = array();
        }
        return view('pages.hrd.training.scheduled.index', compact('date_now','data_all','vendors'));
    }

    public function view_scheduled(Request $request){
        $query = Trainingrecord::where('id', $request->id_record)->first();
        $fkt = Trainingfkt::find($query->id_fkt);
        $arr_query = Trainingrecord::where('kode_fkt', $query->kode_fkt)->get()->pluck('id_employee');
        $arr_emp = Employee::whereIn('id', $arr_query)->get();
        $start_date = date('d M Y', strtotime($query->start_date));
        $end_date = date('d M Y', strtotime($query->end_date));
        $query['date'] = $start_date.' to '.$end_date;
        foreach($arr_emp as $emp){
            $data['nama'] = $emp->fullname;
            $data['dept'] = $emp->department->name;
            $data_all[] = $data;
        }
        $query['detail'] = $data_all;
        if(!empty($fkt)){
            $query['tipe'] = $fkt->tipe;
        }else{
            $query['tipe'] = '-';
        }

        return response()->json($query);
    }
    
    public function update_scheduled(Request $request){
        DB::beginTransaction();

        try {
            $user = auth()->user();
            $id = $request->id_edit;
            $query = Trainingrecord::where('kode_fkt', $id)->first();
            $post = Trainingrecord::where('kode_fkt', $id)->update([
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'id_vendor' => $request->id_vendor,
                'lokasi' => $request->lokasi,
                'biaya' => $request->biaya
            ]);
    
            //insert log user activity
            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'update';
            $insert->description = 'Modify training '.'"'.$query->judul.'"';
            $insert->save();

            DB::commit();

            return response()->json(['message' => "$query->judul has been updated"], 200);
        }catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }
    //end scheduled training

    //start ptt training
    public function index_ptt(Request $request){
        $user = auth()->user();
        $vendors = Vendor::where('tipe', 'training')->get();
        $data = Trainingfkt::whereNotNull(columns: 'date_checker')->where('status',3)->get()->unique('kode');
        if ($request->ajax()) {          
            return DataTables::of($data)
                ->addColumn('kode', function($data){
                    return $data['kode'];
                })
                ->addColumn('pemohon', function($data){
                    return $data->pemohon->fullname;
                })
                ->addColumn('jml_peserta', function($data){
                    $jml_peserta = Trainingfkt::where('kode', $data['kode'])->count();
                    return $jml_peserta;
                })
                ->addColumn('total_biaya', function($data){
                    $jml_biaya = Trainingfkt::where('kode', $data['kode'])->sum('biaya_fkt');
                    return 'Rp '.number_format($jml_biaya,2,',','.');
                })
                ->addColumn('status', function($data){
                    if($data['status'] == 1) return '<a href="#" <span class="badge text-bg-primary"><i class="ri-edit-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 2) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#modal-status" <span class="badge text-bg-warning view-status"><i class="ri-time-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 3) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#modal-status" <span class="badge text-bg-info view-status"><i class="ri-time-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 4) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-info view-status"><i class="ri-time-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 5) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-secondary view-status"><i class="ri-time-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 6) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-success view-status"><i class="ri-checkbox-circle-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 7) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-success view-status"><i class="ri-checkbox-circle-line align-bottom"></i> Finished</span></a>';
                    if($data['status'] == 15) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#modal-status" <span class="badge text-bg-secondary view-status"><i class="ri-error-warning-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 16) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#modal-status" <span class="badge text-bg-secondary view-status"><i class="ri-error-warning-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 17) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#modal-status" <span class="badge text-bg-danger view-status"><i class="ri-close-circle-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 18) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#modal-status" <span class="badge text-bg-danger view-status"><i class="ri-close-circle-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                })
                ->addColumn('action', function ($data) {            
                    $cek_user = auth()->user();                  
                    $list_approve = '<li><a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target=".bs-example-modal-approve" class="dropdown-item view-approve"><i class="ri-checkbox-line align-bottom me-2 text-muted"></i> Approve</a></li>';
                    $list_revise = '<li><a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target=".bs-example-modal-revise" class="dropdown-item view-revise"><i class="ri-error-warning-line align-bottom me-2 text-muted"></i> Revise</a></li>';
                    $list_reject = '<li><a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target=".bs-example-modal-reject" class="dropdown-item view-reject"><i class="ri-close-circle-line align-bottom me-2 text-muted"></i> Reject</a></li>';
                    $list_print_fkt = '<li><a href="'.route('training.ptt.fkt.pdf', encrypt($data['kode'])).'" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FKP</a></li>';
                    $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_approve.$list_revise.$list_reject.$list_print_fkt.'</ul></div>';

                    return $button;
                })
                ->addColumn('peserta', function($data){
                    $query = Trainingfkt::where('kode', $data['kode'])->get();
                    if($query->isNotEmpty()){
                        $peserta = '<div class="col-lg-12"><table class="table table-bordered" style="table-layout: fixed; width:100%;">';
                        $peserta .= '
                            <thead>
                                <tr>
                                    <th colspan="4" style="text-align: center;">Pengajuan Program Pelatihan</th>
                                </tr>
                                <tr>
                                    <th style="text-align: center;">Pelatihan</th>
                                    <th style="text-align: center;">Peserta</th>
                                    <th style="text-align: center;">Pelaksanaan</th>
                                    <th style="text-align: center;">Biaya</th>
                                </tr>
                            </thead>
                            ';
                        $peserta .= '<tbody>';
                        foreach($query as $qry){
                            $bulan = \Carbon\Carbon::create()->month($qry->bulan_pelaksanaan)->format('F');
                            $peserta .= '<tr>';                  
                            $peserta .= '<td>'.$qry->judul.'</td>';     
                            $peserta .= '<td>'.$qry->peserta->fullname.'</td>';      
                            $peserta .= '<td>'.$bulan.' '.$qry->tahun_pelaksanaan.'</td>';      
                            $peserta .= '<td> Rp '.number_format($qry->biaya_fkt,2,',','.').'</td>';      
                            $peserta .= '</tr>';
                        } 
                        $peserta .= '</tbody></table></div>';
                    }else{
                        $peserta = '';
                    }
                    return $peserta;
                })
                ->rawColumns(['action','status','kode','peserta'])
                ->addIndexColumn()
                ->make(true);
        }   

        return view('pages.hrd.training.ptt.index', compact('user'));
    }
    // public function index_ptt(Request $request){
    //     $user = auth()->user();
    //     $vendors = Vendor::where('tipe', 'training')->get();
    //     if($user->roles()->pluck('id')->first() == '2'){
    //         $query = Trainingfkt::where('tipe', 'ptt')->whereNotNull('date_penilai')->where('status',2)->get()->unique('tahun_usulan');
    //         if(count($query) > 0){
    //             foreach($query as $qry){
    //                 $index = $qry->id;
    //                 $document = array();
    //                 $document[$index]['tahun_usulan'] = $qry->tahun_usulan;
    //                 $document[$index]['jumlah_usulan'] = Trainingfkt::where('tahun_usulan', $qry->tahun_usulan)->where('tipe','ptt')
    //                     ->whereNotNull('date_penilai')
    //                     ->where('status', 2)->count();
    //                 //hrd
    //                 $document[$index]['jml_approve'] = Trainingfkt::where('tahun_usulan', $qry->tahun_usulan)->where('tipe','ptt')
    //                     ->whereNotNull('date_penilai')
    //                     ->where('status', 2)->count();
    //                 //schedule
    //                 $document[$index]['jml_approve4'] = Trainingfkt::where('tahun_usulan', $qry->tahun_usulan)->where('tipe','ptt')
    //                     ->whereNotNull('date_penilai')
    //                     ->where('status', 6)->count();
                    
    //                 $jml_hrd_active_ptt =  $document[$index]['jml_approve'];
    //                 $jml_hrd_finished_ptt =  $document[$index]['jml_approve4'];
    //                 $jml_bod1_ptt = 0;
    //                 $jml_bod2_ptt = 0;
    //             }
    //         }else{
    //             $arr_tahun = Trainingfkt::where('tipe', 'ptt')->whereNotNull('date_penilai')->where('status',6)->get()->unique('tahun_usulan')->pluck('tahun_usulan');
    //             //schedule
    //             $jml_schedule = Trainingfkt::whereIn('tahun_usulan', $arr_tahun)->where('tipe','ptt')
    //             ->whereNotNull('date_penilai')
    //             ->where('status', 6)->count();
    //             $document = array();
    //             $jml_hrd_active_ptt =  0;
    //             $jml_hrd_finished_ptt =  $jml_schedule;
    //             $jml_bod1_ptt = 0;
    //             $jml_bod2_ptt = 0;
    //         }
    //         if ($request->ajax()) {          
    //             return DataTables::of($document)
    //                 ->addColumn('action', function ($document) {
    //                     $qry_user = auth()->user();
    //                     //hrd button
    //                     if(\Auth::user()->can('hrd.training.ptt.verification')){
    //                         if($document['jml_approve'] > 0){
    //                             $button = '<a href="'. route('training.ptt.form', encrypt($document['tahun_usulan'])).'" data-toggle="tooltip" title="Verification" class="btn btn-info btn-sm"><i class="ri-task-line"></i> <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">'.$document['jml_approve'].' <span class="visually-hidden">unread messages</span></span></a>';
    //                         }else{
    //                             $button = '<a href="'. route('training.ptt.form', encrypt($document['tahun_usulan'])).'" data-toggle="tooltip" title="Verification" class="btn btn-info btn-sm"><i class="ri-task-line"></i></a>';
    //                         }
    //                     }else{
    //                         $button = '';
    //                     }                             
    //                     // if(\Auth::user()->can('hrd.training.ptt.notification.direktur-produksi')){
    //                     //     $button .= '&nbsp;';
    //                     //     $button .= '&nbsp;';
    //                     //     $button .= '<a href="#" data-id="'.$document['tahun_usulan'].'" data-bs-toggle="modal" data-bs-target=".bs-notification-modal-center" data-toggle="tooltip" title="Notification Direktur Produksi" class="btn btn-primary btn-sm notif-direktur-view"><i class="ri-mail-send-line"></i></a>';
    //                     // }else{
    //                     //     $button .= '';
    //                     // }
    //                     return $button;
    //                 })                
    //                 ->rawColumns(['action'])
    //                 ->addIndexColumn()
    //                 ->make(true);
    //         }  
    //         //cek notif view
    //         $notif_hrd_active_ptt = $jml_hrd_active_ptt;
    //         $notif_hrd_finished_ptt = $jml_hrd_finished_ptt;
    //         $notif_bod1_ptt = $jml_bod1_ptt;
    //         $notif_bod2_ptt = $jml_bod2_ptt;
    //     }else{
    //         if($user->roles()->pluck('id')->first() == '51'){
    //             $query = Trainingfkt::where('tipe', 'ptt')
    //             ->whereNotNull('date_checker')
    //             ->where('is_notif','>', '0')
    //             ->where('status',4)->get()->unique('tahun_usulan');

    //             if(count($query) > 0){
    //                 foreach($query as $qry){
    //                     $index = $qry->id;
    //                     $document = array();
    //                     $document[$index]['tahun_usulan'] = $qry->tahun_usulan;
    //                     $document[$index]['jumlah_usulan'] = Trainingfkt::where('tahun_usulan', $qry->tahun_usulan)->where('tipe','ptt')
    //                         ->whereNotNull('date_checker')
    //                         ->where('is_notif','>', '0')
    //                         ->where('status', 4)->count();
    //                     //direktur
    //                     $document[$index]['jml_approve2'] = Trainingfkt::where('tahun_usulan', $qry->tahun_usulan)->where('tipe','ptt')
    //                         ->whereNotNull('date_checker')
    //                         ->where('is_notif','>', '0')
    //                         ->where('status', 4)->count();
                        
    //                     $jml_hrd_active_ptt =  0;
    //                     $jml_hrd_finished_ptt =  0;
    //                     $jml_bod1_ptt = $document[$index]['jml_approve2'];
    //                     $jml_bod2_ptt = 0;
    //                 }
    //             }else{
    //                 $document = array();
    //                 $jml_hrd_active_ptt =  0;
    //                 $jml_hrd_finished_ptt =  0;
    //                 $jml_bod1_ptt = 0;
    //                 $jml_bod2_ptt = 0;
    //             }
    //             if ($request->ajax()) {          
    //                 return DataTables::of($document)
    //                     ->addColumn('action', function ($document) {
    //                         $qry_user = auth()->user();
    //                         if(\Auth::user()->can('hrd.training.ptt.approve.direktur-produksi')){
    //                             if($document['jml_approve2'] > 0){
    //                                 $button = '<a href="'. route('training.ptt.form', encrypt($document['tahun_usulan'])).'" data-toggle="tooltip" title="Approve" class="btn btn-info btn-sm"><i class="ri-task-line"></i> <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">'.$document['jml_approve2'].' <span class="visually-hidden">unread messages</span></span></a>';
    //                             }else{
    //                                 $button = '<a href="'. route('training.ptt.form', encrypt($document['tahun_usulan'])).'" data-toggle="tooltip" title="Approve" class="btn btn-info btn-sm"><i class="ri-task-line"></i></a>';
    //                             }
    //                         }else{
    //                             $button = '';
    //                         }                                       
    //                         return $button;
    //                     })                
    //                     ->rawColumns(['action'])
    //                     ->addIndexColumn()
    //                     ->make(true);
    //             }  
    //             //cek notif view
    //             $notif_hrd_active_ptt = $jml_hrd_active_ptt;
    //             $notif_hrd_finished_ptt = $jml_hrd_finished_ptt;
    //             $notif_bod1_ptt = $jml_bod1_ptt;
    //             $notif_bod2_ptt = $jml_bod2_ptt;
    //         }else{
    //             if($user->roles()->pluck('id')->first() == '49'){
    //                 $query = Trainingfkt::where('tipe', 'ptt')
    //                 ->whereNotNull('date_checker')
    //                 ->where('is_notif','>', '0')
    //                 ->where('status',5)->get()->unique('tahun_usulan');

    //                 if(count($query) > 0){
    //                     foreach($query as $qry){
    //                         $index = $qry->id;
    //                         $document = array();
    //                         $document[$index]['tahun_usulan'] = $qry->tahun_usulan;
    //                         $document[$index]['jumlah_usulan'] = Trainingfkt::where('tahun_usulan', $qry->tahun_usulan)->where('tipe','ptt')
    //                             ->whereNotNull('date_checker')
    //                             ->where('is_notif','>', '0')
    //                             ->where('status', 5)->count();
    //                         //presiden
    //                         $document[$index]['jml_approve3'] = Trainingfkt::where('tahun_usulan', $qry->tahun_usulan)->where('tipe','ptt')
    //                         ->whereNotNull('date_checker')
    //                         ->where('is_notif','>', '0')
    //                         ->where('status', 5)->count();
                            
    //                         $jml_hrd_active_ptt =  0;
    //                         $jml_hrd_finished_ptt =  0;
    //                         $jml_bod1_ptt = 0;
    //                         $jml_bod2_ptt = $document[$index]['jml_approve3'];
    //                     }
    //                 }else{
    //                     $document = array();
    //                     $jml_hrd_active_ptt =  0;
    //                     $jml_hrd_finished_ptt =  0;
    //                     $jml_bod1_ptt = 0;
    //                     $jml_bod2_ptt = 0;
    //                 }
    //                 if ($request->ajax()) {          
    //                     return DataTables::of($document)
    //                         ->addColumn('action', function ($document) {
    //                             $qry_user = auth()->user();                  
    //                             if(\Auth::user()->can('hrd.training.ptt.approve.presiden-direktur')){
    //                                 if($document['jml_approve3'] > 0){
    //                                     $button = '<a href="'. route('training.ptt.form', encrypt($document['tahun_usulan'])).'" data-toggle="tooltip" title="Approve" class="btn btn-info btn-sm"><i class="ri-task-line"></i> <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">'.$document['jml_approve3'].' <span class="visually-hidden">unread messages</span></span></a>';
    //                                 }else{
    //                                     $button = '<a href="'. route('training.ptt.form', encrypt($document['tahun_usulan'])).'" data-toggle="tooltip" title="Approve" class="btn btn-info btn-sm"><i class="ri-task-line"></i></a>';
    //                                 }
    //                             }else{
    //                                 if($document['jml_approve3'] > 0){
    //                                     $button = '<a href="'. route('training.ptt.form', encrypt($document['tahun_usulan'])).'" data-toggle="tooltip" title="Approve" class="btn btn-info btn-sm"><i class="ri-task-line"></i> <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">'.$document['jml_approve3'].' <span class="visually-hidden">unread messages</span></span></a>';
    //                                 }else{
    //                                     $button = '<a href="'. route('training.ptt.form', encrypt($document['tahun_usulan'])).'" data-toggle="tooltip" title="Approve" class="btn btn-info btn-sm"><i class="ri-task-line"></i></a>';
    //                                 }
    //                                 // $button = '';
    //                             }                    
    //                             return $button;
    //                         })                
    //                         ->rawColumns(['action'])
    //                         ->addIndexColumn()
    //                         ->make(true);
    //                 }  
    //                 //cek notif view
    //                 $notif_hrd_active_ptt = $jml_hrd_active_ptt;
    //                 $notif_hrd_finished_ptt = $jml_hrd_finished_ptt;
    //                 $notif_bod1_ptt = $jml_bod1_ptt;
    //                 $notif_bod2_ptt = $jml_bod2_ptt;
    //             }else{
    //                 if($user->roles()->pluck('id')->first() == '1'){
    //                     $query = Trainingfkt::where('tipe', 'ptt')->whereNotNull('date_checker')->whereIn('status', [2,4,5])->get()->unique('tahun_usulan');
    //                     if(count($query) > 0){
    //                         foreach($query as $qry){
    //                             $index = $qry->id;
    //                             $document = array();
    //                             $document[$index]['tahun_usulan'] = $qry->tahun_usulan;
    //                             $document[$index]['jumlah_usulan'] = Trainingfkt::where('tahun_usulan', $qry->tahun_usulan)->where('tipe','ptt')
    //                                 ->whereNotNull('date_checker')
    //                                 ->whereIn('status', [2,4,5])->count();
    //                             //hrd and direktur and presiden
    //                             $document[$index]['jml_approve'] = Trainingfkt::where('tahun_usulan', $qry->tahun_usulan)->where('tipe','ptt')
    //                                 ->whereNotNull('date_checker')
    //                                 ->whereIn('status', [2,4,5])->count();
    //                             //schedule
    //                             $document[$index]['jml_approve4'] = Trainingfkt::where('tahun_usulan', $qry->tahun_usulan)->where('tipe','ptt')
    //                                 ->whereNotNull('date_checker')
    //                                 ->where('status', 6)->count();
                                
    //                             $jml_hrd_active_ptt =  $document[$index]['jml_approve'];
    //                             $jml_hrd_finished_ptt =  $document[$index]['jml_approve4'];
    //                             $jml_bod1_ptt = 0;
    //                             $jml_bod2_ptt = 0;
    //                         }
    //                     }else{
    //                         $document = array();
    //                         $jml_hrd_active_ptt =  0;
    //                         $jml_hrd_finished_ptt =  0;
    //                         $jml_bod1_ptt = 0;
    //                         $jml_bod2_ptt = 0;
    //                     }
    //                     if ($request->ajax()) {          
    //                         return DataTables::of($document)
    //                             ->addColumn('action', function ($document) {
    //                                 $qry_user = auth()->user();
    //                                 //hrd button
    //                                 if(\Auth::user()->can('hrd.training.ptt.verification') || \Auth::user()->can('hrd.training.ptt.approve.direktur-produksi') || \Auth::user()->can('hrd.training.ptt.approve.presiden-direktur')){
    //                                     if($document['jml_approve'] > 0){
    //                                         $button = '<a href="'. route('training.ptt.form', encrypt($document['tahun_usulan'])).'" data-toggle="tooltip" title="Approval" class="btn btn-info btn-sm"><i class="ri-task-line"></i> <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">'.$document['jml_approve'].' <span class="visually-hidden">unread messages</span></span></a>';
    //                                     }else{
    //                                         $button = '<a href="'. route('training.ptt.form', encrypt($document['tahun_usulan'])).'" data-toggle="tooltip" title="Approval" class="btn btn-info btn-sm"><i class="ri-task-line"></i></a>';
    //                                     }
    //                                 }else{
    //                                     $button = '';
    //                                 }                             
    //                                 if(\Auth::user()->can('hrd.training.ptt.notification.direktur-produksi')){
    //                                     $button .= '&nbsp;';
    //                                     $button .= '&nbsp;';
    //                                     $button .= '<a href="#" data-id="'.$document['tahun_usulan'].'" data-bs-toggle="modal" data-bs-target=".bs-notification-modal-center" data-toggle="tooltip" title="Notification Direktur Produksi" class="btn btn-primary btn-sm notif-direktur-view"><i class="ri-mail-send-line"></i></a>';
    //                                 }else{
    //                                     $button .= '';
    //                                 }
    //                                 return $button;
    //                             })                
    //                             ->rawColumns(['action'])
    //                             ->addIndexColumn()
    //                             ->make(true);
    //                     }  
    //                     //cek notif view
    //                     $notif_hrd_active_ptt = $jml_hrd_active_ptt;
    //                     $notif_hrd_finished_ptt = $jml_hrd_finished_ptt;
    //                     $notif_bod1_ptt = $jml_bod1_ptt;
    //                     $notif_bod2_ptt = $jml_bod2_ptt;
    //                 }else{
    //                     $document = array();
    //                     if ($request->ajax()) {          
    //                         return DataTables::of($document)
    //                             ->addColumn('action', function ($document) {
    //                                 $button = '';
    //                                 return $button;
    //                             })                
    //                             ->rawColumns(['action'])
    //                             ->addIndexColumn()
    //                             ->make(true);
    //                     }  
    //                     //cek notif view
    //                     $notif_hrd_active_ptt = 0;
    //                     $notif_hrd_finished_ptt = 0;
    //                     $notif_bod1_ptt = 0;
    //                     $notif_bod2_ptt = 0;
    //                 }
    //             }
    //         }
    //     }      

    //     return view('pages.hrd.training.ptt.index', compact('user','vendors','notif_hrd_active_ptt','notif_hrd_finished_ptt','notif_bod1_ptt','notif_bod2_ptt'));
    // }

    public function ptt_finished(Request $request){
        $query = Trainingfkt::where('tipe','ptt')->where('status', 6)->get()->unique('kode_judul');
        if($query->isNotEmpty()){
            foreach($query as $qry){
                $index = $qry->kode_judul;
                $document[$index] = array();
                $document[$index]['judul'] = $qry->judul;
                $document[$index]['kode'] = $qry->kode;
                $document[$index]['status'] = $qry->status;
                if(empty($qry->id_vendor) && empty($qry->nama_vendor)){
                    $document[$index]['nama_vendor'] = null;
                }else{
                    $document[$index]['nama_vendor'] = $qry->vendor->nama ?? $qry->nama_vendor;
                }
            }
        }else{
            $document = array(); 
        }
        if($request->ajax()){
            return DataTables::of($document)
                ->addColumn('status', function($document){
                    if($document['status'] == 6) return '<a href="#" <span class="badge text-bg-secondary"><i class="ri-checkbox-circle-line align-bottom"></i> Approved</span></a>';
                    if($document['status'] == 7) return '<a href="#" <span class="badge text-bg-secondary"><i class="ri-checkbox-circle-line align-bottom"></i> Approved</span></a>';
                })
                ->addColumn('action', function ($document) {
                    if(\Auth::user()->can('hrd.training.ptt.schedule')){
                        $button = '<a href="#" data-bs-target="#secondmodal" data-bs-toggle="modal" data-toggle="tooltip" title="Schedule" class="btn btn-info btn-sm btn-schedule"><i class="ri-calendar-todo-line"></i></a><input type="hidden" id="btn-kode" value="'.$document['kode'].'"><input type="hidden" id="btn-judul" value="'.$document['judul'].'"><input type="hidden" id="request-vendor" value="'.$document['nama_vendor'].'">';               
                    }else{
                        $button = '';
                    }
                    return $button;
                })
                ->addColumn('peserta', function($document){
                    $arr_fkt = Trainingfkt::where('kode', $document['kode'])->where('judul', $document['judul'])->get();
                    if($arr_fkt->isNotEmpty()){
                        $peserta = '<div class="col-lg-12"><table class="table table-bordered" style="table-layout: fixed; width:100%;">';
                        $peserta .= '
                            <thead>
                                <tr>
                                    <th colspan="4" style="text-align: center;">Peserta Program Training</th>
                                </tr>
                                <tr>
                                    <th style="text-align: center;">Peserta</th>
                                    <th style="text-align: center;">Pelatihan</th>
                                    <th style="text-align: center;">Sifat Pelatihan</th>
                                    <th style="text-align: center;">Alasan</th>
                                </tr>
                            </thead>
                            ';
                        $peserta .= '<tbody>';
                        foreach($arr_fkt as $fkt){
                            $peserta .= '<tr>';                  
                            $peserta .= '<td>'.$fkt->peserta->fullname.'</td>';    
                            $peserta .= '<td>'.$fkt->judul.'</td>';    
                            $peserta .= '<td>'.$fkt->sifat.'</td>';    
                            $peserta .= '<td>'.$fkt->alasan.'</td>';
                            $peserta .= '</tr>';
                        } 
                        $peserta .= '</tbody></table></div>';
                    }else{
                        $peserta = '';
                    }
                    return $peserta;
                })
                ->rawColumns(['status','action','peserta'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.training.ptt.index');
    }

    public function ptt_schedule_store(Request $request){
        $user = auth()->user();
        $arr_fkt = Trainingfkt::where('kode', $request->kode_fkt)->where('judul', $request->judul_fkt)->get();
        if($request->id_vendor != 'other'){
            $vendor = $request->id_vendor;
        }else{
            $insert_vendor = Vendor::insert([
                'nama' => $request->nama_vendor,
                'alamat' => '-',
                'tipe' => 'training',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            $query_vendor = Vendor::where('nama', $request->nama_vendor)
                ->where('tipe', 'training')
                ->first();
            $vendor = $query_vendor->id;
        }
        foreach($arr_fkt as $fkt){
            $insert[] = [
                'id_employee' => $fkt->id_peserta,
                'judul' => $fkt->judul,
                'detail' => $fkt->judul,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'id_vendor' => $vendor,
                'lokasi' => $request->lokasi,
                'biaya' => str_replace(".","",$request->biaya),
                'exp_date' => null,
                'id_fkt' => $fkt->id,
                'kode_fkt' => $fkt->kode_judul,
                'status' => 13,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];

            //notification peserta training record
            if(!empty($fkt->peserta->email)){
                $qry_user = User::where('employee_id', $fkt->id_peserta)->first();
                if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                    $details = [
                        'greeting' => 'Hi '.$fkt->peserta->fullname,
                        'subject' => 'Pelaksanaan Training',
                        'body' => 'Ingin Menginformasikan bahwa training anda dengan topik "'.$fkt->judul.'" sudah dijadwalkan',
                        'actionText' => 'Silahkan Login',
                        'actionURL' => url('/employee/training'),
                        'thanks' => 'Terimakasih atas perhatiannya!!'
                    ];
                }else{
                    $details = [
                        'greeting' => 'Hi '.$fkt->peserta->fullname,
                        'subject' => 'Pelaksanaan Training',
                        'body' => 'Ingin Menginformasikan bahwa training anda dengan topik "'.$fkt->judul.'" sudah dijadwalkan',
                        'actionText' => 'Silahkan Login',
                        'actionURL' => url('/mytraining'),
                        'thanks' => 'Terimakasih atas perhatiannya!!'
                    ];
                }
                //send mail
                $qry_user->notify(new AccountNotification($details));
            }

            //insert log user activity
            $insert_log = new Log;
            $insert_log->user_id = $user->id;
            $insert_log->ip_address = $request->ip();
            $insert_log->action = 'insert';
            $insert_log->description = 'Create schedule training peserta "'.$fkt->peserta->fullname.'"';
            $insert_log->save();
        }
        $post = Trainingrecord::insert($insert);
        $update = Trainingfkt::where('kode', $request->kode_fkt)->where('judul', $request->judul_fkt)->update([
            'id_vendor' => $vendor,
            'status' => 7
        ]);

        return redirect(route('training.ptt.index'))->with('scheduled','open tab')->with('status','Schedule Training has been created');
    }

    public function ptt_schedule(Request $request){
        $query = Trainingfkt::where('tipe','ptt')->where('status', 7)->get()->unique('kode_judul');
        if($query->isNotEmpty()){
            foreach($query as $qry){
                $jumlah = Trainingfkt::where('kode', $qry->kode)->where('judul', $qry->judul)->count();
                $training = Trainingrecord::where('id_fkt', $qry->id)->first();
                $index = $qry->kode_judul;
                $data[$index] = array();
                $data[$index]['judul'] = $qry->judul;
                $data[$index]['kode'] = $qry->kode;
                $data[$index]['jml_peserta'] = $jumlah;
                $data[$index]['tgl_mulai'] = $training->start_date;
                $data[$index]['tgl_akhir'] = $training->end_date;
                $data[$index]['vendor'] = $qry->vendor->nama;
            }
        }else{
            $data = array(); 
        }
        if($request->ajax()){
            return DataTables::of($data)
                ->addColumn('action', function ($data) {
                    $button = '-';               
                    return $button;
                })
                ->addColumn('peserta', function($data){
                    $arr_fkt = Trainingfkt::where('kode', $data['kode'])->where('judul', $data['judul'])->get();
                    if($arr_fkt->isNotEmpty()){
                        $peserta = '<div class="col-lg-12"><table class="table table-bordered" style="table-layout: fixed; width:100%;">';
                        $peserta .= '
                            <thead>
                                <tr>
                                    <th colspan="4" style="text-align: center;">Peserta Program Training</th>
                                </tr>
                                <tr>
                                    <th style="text-align: center;">Peserta</th>
                                    <th style="text-align: center;">Pelatihan</th>
                                    <th style="text-align: center;">Sifat Pelatihan</th>
                                    <th style="text-align: center;">Alasan</th>
                                </tr>
                            </thead>
                            ';
                        $peserta .= '<tbody>';
                        foreach($arr_fkt as $fkt){
                            $peserta .= '<tr>';                  
                            $peserta .= '<td>'.$fkt->peserta->fullname.'</td>';    
                            $peserta .= '<td>'.$fkt->judul.'</td>';    
                            $peserta .= '<td>'.$fkt->sifat.'</td>';    
                            $peserta .= '<td>'.$fkt->alasan.'</td>';
                            $peserta .= '</tr>';
                        } 
                        $peserta .= '</tbody></table></div>';
                    }else{
                        $peserta = '';
                    }
                    return $peserta;
                })
                ->rawColumns(['action','peserta'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.training.ptt.index');
    }
    public function form_ptt(Request $request, $id){
        $user = auth()->user();
        $tahun_usulan = decrypt($id);
        if($user->roles()->pluck('id')->first() == '2'){
            $title = 'hrd';
            $query_fkt = Trainingfkt::where('tahun_usulan', $tahun_usulan)->where('tipe','ptt')
                ->whereNotNull('date_checker')
                ->where('status',2)
                ->get()->unique('kode_judul');
        }else{
            if($user->roles()->pluck('id')->first() == '51'){
                $title = 'direktur';
                $query_fkt = Trainingfkt::where('tahun_usulan', $tahun_usulan)->where('tipe','ptt')
                    ->whereNotNull('date_checker')
                    ->where('is_notif','>','0')
                    ->where('status',4)
                    ->get()->unique('kode_judul');
            }else{
                if($user->roles()->pluck('id')->first() == '49'){
                    $title = 'presiden';
                    $query_fkt = Trainingfkt::where('tahun_usulan', $tahun_usulan)->where('tipe','ptt')
                        ->whereNotNull('date_checker')
                        ->where('is_notif','>','0')
                        ->where('status',5)
                        ->get()->unique('kode_judul');
                }else{
                    $title = 'admin';
                    $query_fkt = Trainingfkt::where('tahun_usulan', $tahun_usulan)->where('tipe','ptt')
                        ->whereNotNull('date_checker')
                        ->whereIn('status',[2,4,5])
                        ->get()->unique('kode_judul');
                }
            }
        }
        return view('pages.hrd.training.ptt.form', compact('user','tahun_usulan','query_fkt','title'));
    }
    public function store_ptt(Request $request){
        DB::beginTransaction();
        try {

            $user = auth()->user();

            if($request->tipe == 'approve'){
                $query = Trainingfkt::where('kode', decrypt($request->kode))->first();
                $post = Trainingfkt::where('kode', decrypt($request->kode))->update([
                    'id_verified' => $user->employee_id,
                    'date_verified' => date('Y-m-d H:i:s'),
                    'status' => 6
                ]);
                //hrd ttd
                $date_qr = date('Ymd');
                $insert_approved_qr = new Qrcodefkt;
                $insert_approved_qr->kode_fkt = $query->kode;
                $insert_approved_qr->qr = $date_qr.$user->employee_id;
                $insert_approved_qr->date_approval = date('Y-m-d H:i:s');
                $insert_approved_qr->type = 3;
                $insert_approved_qr->save();

                 //update log user activity
                $insert = new Log;
                $insert->user_id = $user->id;
                $insert->ip_address = $request->ip();
                $insert->action = 'approved';
                $insert->description = 'Approved formulir kebutuhan pelatihan dengan nama pemohon'.'"'.$query->pemohon->fullname.'" tujuan "Pengajuan Biaya Pelatihan Tahunan"';
                $insert->save();

                DB::commit();
    
                return response()->json(['message' => "$query->kode has been approved"], 200);
            }
            if($request->tipe == 'revise'){
                $query = Trainingfkt::where('kode', decrypt($request->kode))->first();
                $post = Trainingfkt::where('kode', decrypt($request->kode))->update([
                    'status' => 16
                ]);

                //notification pemohon
                $qry_user = User::where('employee_id', $query->id_checker)->first();
                if(!empty($qry_user->email)){
                    if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                        $details = [
                            'greeting' => 'Hi '.$qry_user->name,
                            'subject' => 'PROGRAM PELATIHAN TAHUNAN',
                            'body' => 'Ingin Menginformasikan bahwa ada usulan program pelatihan tahunan dengan nomor dokumen "'.$query->kode.'" pemohon "'.$query->pemohon->fullname.'" yang direvisi oleh HRD',
                            'actionText' => 'Silahkan Login',
                            'actionURL' => route('training.emp.fkt.ptt.back'),
                            'thanks' => 'Terimakasih atas perhatiannya!!'
                        ];
                    }else{
                        $details = [
                            'greeting' => 'Hi '.$qry_user->name,
                            'subject' => 'PROGRAM PELATIHAN TAHUNAN',
                            'body' => 'Ingin Menginformasikan bahwa ada usulan program pelatihan tahunan dengan nomor dokumen "'.$query->kode.'" pemohon "'.$query->pemohon->fullname.'" yang direvisi oleh HRD',
                            'actionText' => 'Silahkan Login',
                            'actionURL' => route('profile.back.fkt.ptt'),
                            'thanks' => 'Terimakasih atas perhatiannya!!'
                        ];
                    }
                    //send mail
                    $qry_user->notify(new AccountNotification($details));
                }

                //catatan revisi
                $insert_ctt = new Logcatatantraining;
                $insert_ctt->id_user = $user->employee_id;
                $insert_ctt->kode_fkt = $query->kode;
                $insert_ctt->ip_address = $request->ip();
                $insert_ctt->action = 'revise hrd';
                $insert_ctt->catatan = $request->catatan_revise;
                $insert_ctt->save();

                //update log user activity
                $insert = new Log;
                $insert->user_id = $user->id;
                $insert->ip_address = $request->ip();
                $insert->action = 'revise';
                $insert->description = 'Revise formulir kebutuhan pelatihan dengan nama pemohon'.'"'.$query->pemohon->fullname.'" tujuan "Pengajuan Biaya Pelatihan Tahunan"';
                $insert->save();

                DB::commit();
    
                return response()->json(['message' => "$query->kode has been revised"], 200);
            }
            if($request->tipe == 'reject'){
                $query = Trainingfkt::where('kode', decrypt($request->kode))->first();
                $post = Trainingfkt::where('kode', decrypt($request->kode))->update([
                    'status' => 18
                ]);

                //notification pemohon
                $qry_user = User::where('employee_id', $query->id_checker)->first();
                if(!empty($qry_user->email)){
                    if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                        $details = [
                            'greeting' => 'Hi '.$qry_user->name,
                            'subject' => 'PROGRAM PELATIHAN TAHUNAN',
                            'body' => 'Ingin Menginformasikan bahwa ada usulan program pelatihan tahunan dengan nomor dokumen "'.$query->kode.'" pemohon "'.$query->pemohon->fullname.'" yang direject oleh HRD',
                            'actionText' => 'Silahkan Login',
                            'actionURL' => route('training.emp.fkt.ptt.back'),
                            'thanks' => 'Terimakasih atas perhatiannya!!'
                        ];
                    }else{
                        $details = [
                            'greeting' => 'Hi '.$qry_user->name,
                            'subject' => 'PROGRAM PELATIHAN TAHUNAN',
                            'body' => 'Ingin Menginformasikan bahwa ada usulan program pelatihan tahunan dengan nomor dokumen "'.$query->kode.'" pemohon "'.$query->pemohon->fullname.'" yang direject oleh HRD',
                            'actionText' => 'Silahkan Login',
                            'actionURL' => route('profile.back.fkt.ptt'),
                            'thanks' => 'Terimakasih atas perhatiannya!!'
                        ];
                    }
                    //send mail
                    $qry_user->notify(new AccountNotification($details));
                }

                //catatan reject
                $insert_ctt = new Logcatatantraining;
                $insert_ctt->id_user = $user->employee_id;
                $insert_ctt->kode_fkt = $query->kode;
                $insert_ctt->ip_address = $request->ip();
                $insert_ctt->action = 'reject hrd';
                $insert_ctt->catatan = $request->catatan_reject;
                $insert_ctt->save();

                //update log user activity
                $insert = new Log;
                $insert->user_id = $user->id;
                $insert->ip_address = $request->ip();
                $insert->action = 'reject';
                $insert->description = 'Reject formulir kebutuhan pelatihan dengan nama pemohon'.'"'.$query->pemohon->fullname.'" tujuan "Pengajuan Biaya Pelatihan Tahunan"';
                $insert->save();

                DB::commit();
    
                return response()->json(['message' => "$query->kode has been rejected"], 200);
            }
            
        }catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }
    public function fkt_pdf($id){
        $fkt = Trainingfkt::where('kode', decrypt($id))->first();
        //get qrcode
        $all_qrcode = Qrcodefkt::where('kode_fkt', $fkt->kode)->get();

        //pemohon ttd
        $pemohon = $fkt->pemohon->fullname;
	    $cp_pemohon = Str::lower($pemohon);
        $pemohon_ttd = ucwords($cp_pemohon);
        
        $pos_pemohon = $fkt->pemohon->position->nama ?? '-';
	    $cp_pos_pemohon = Str::lower($pos_pemohon);
        $pos_pemohon_ttd = ucwords($cp_pos_pemohon);

        $qr_1 =  $all_qrcode->whereStrict('type', 1)->first();
        if(!empty($qr_1)){
            $pemohon_qr = $qr_1->qr;
            $pemohon_kode_qr = str_replace("/","-",$qr_1->kode_fkt);

            $link_qr_pemohon = route('public.training.qrcode.fkp.pemohon', ['code' => $pemohon_qr, 'id' => $pemohon_kode_qr]);
        }else{
            $pemohon_qr = null;
            $pemohon_kode_qr = null;

            $link_qr_pemohon = '';
        }
        
        //checker ttd
        $checker = $fkt->checker->fullname;
	    $cp_checker = Str::lower($checker);
        $checker_ttd = ucwords($cp_checker);

        $pos_checker = $fkt->checker->position->nama ?? '-';
	    $cp_pos_checker = Str::lower($pos_checker);
        $pos_checker_ttd = ucwords($cp_pos_checker);

        $qr_2 =  $all_qrcode->whereStrict('type', 5)->first();
        if(!empty($qr_2)){
            $checker_qr = $qr_2->qr;
            $checker_kode_qr = str_replace("/","-",$qr_2->kode_fkt);

            $link_qr_checker = route('public.training.qrcode.fkp.checker', ['code' => $checker_qr, 'id' => $checker_kode_qr]);
        }else{
            $checker_qr = null;
            $checker_kode_qr = null;

            $link_qr_checker = '';
        }

        //hrd verified ttd
        $verified = $fkt->verified->fullname ?? '-';
	    $cp_verified = Str::lower($verified);
        $verified_ttd = ucwords($cp_verified);

        $pos_verified = $fkt->verified->position->nama ?? '-';
	    $cp_pos_verified = Str::lower($pos_verified);
        $pos_verified_ttd = ucwords($cp_pos_verified);

        $qr_3 =  $all_qrcode->whereStrict('type', 3)->first();
        if(!empty($qr_3)){
            $verified_qr = $qr_3->qr;
            $verified_kode_qr = str_replace("/","-",$qr_3->kode_fkt);

            $link_qr_verified = route('public.training.qrcode.fkp.verified', ['code' => $verified_qr, 'id' => $verified_kode_qr]);
        }else{
            $verified_qr = null;
            $verified_kode_qr = null;

            $link_qr_verified = '';
        }

        $arr_fkt = Trainingfkt::where('kode', decrypt($id))->get();
        $data = [
            'title' => 'FORMULIR RENCANA PELATIHAN TAHUNAN',
            'fkt' => $fkt,
            'arr_fkt' => $arr_fkt,
            'pemohon_ttd' => $pemohon_ttd,
            'pos_pemohon_ttd' => $pos_pemohon_ttd,
            'checker_ttd' => $checker_ttd,
            'pos_checker_ttd' => $pos_checker_ttd,
            'verified_ttd' => $verified_ttd,
            'pos_verified_ttd' => $pos_verified_ttd,
            'link_qr_pemohon' => $link_qr_pemohon,
            'link_qr_checker' => $link_qr_checker,
            'link_qr_verified' => $link_qr_verified
        ];
        $pdf = PDF::loadView('pages.hrd.training.ptt.print-fkt', $data)->setPaper('a4', 'landscape');
        $pdf->set_option("isPhpEnabled", true);
        return $pdf->stream('FORMULIR RENCANA PELATIHAN TAHUNAN - '.$fkt->pemohon->fullname.'.pdf');
    }
    //qrcode fkt
    public function qrcode_pemohon($code,$id){
        $kode_fkt = str_replace("-","/",$id);
        $query = Qrcodefkt::where('kode_fkt', $kode_fkt)->where('qr', $code)->where('type', 1)->first();
        if($query->fkt->tipe == 'ptt'){
            $usulan = 'Program Training Tahunan';
        }else{
            $usulan = 'Program Training Insidentil';
        }
        return view('pages.hrd.training.ptt.codeqr-pemohon', compact('query','usulan'));
    }
    public function qrcode_checker($code,$id){
        $kode_fkt = str_replace("-","/",$id);
        $query = Qrcodefkt::where('kode_fkt', $kode_fkt)->where('qr', $code)->where('type', 5)->first();
        if($query->fkt->tipe == 'ptt'){
            $usulan = 'Program Training Tahunan';
        }else{
            $usulan = 'Program Training Insidentil';
        }
        return view('pages.hrd.training.ptt.codeqr-checker', compact('query','usulan'));
    }
    public function qrcode_verified($code,$id){
        $kode_fkt = str_replace("-","/",$id);
        $query = Qrcodefkt::where('kode_fkt', $kode_fkt)->where('qr', $code)->where('type', 3)->first();
        if($query->fkt->tipe == 'ptt'){
            $usulan = 'Program Training Tahunan';
        }else{
            $usulan = 'Program Training Insidentil';
        }
        return view('pages.hrd.training.ptt.codeqr-verified', compact('query','usulan'));
    }
    public function qrcode_approval($code,$id){
        $kode_fkt = str_replace("-","/",$id);
        $query = Qrcodefkt::where('kode_fkt', $kode_fkt)->where('qr', $code)->where('type', 4)->first();
        if($query->fkt->tipe == 'ptt'){
            $usulan = 'Program Training Tahunan';
        }else{
            $usulan = 'Program Training Insidentil';
        }
        return view('pages.hrd.training.ptt.codeqr-approval', compact('query','usulan'));
    } 
    public function fpkt_pdf($id){
        $arr_fkt = Trainingfkt::where('kode_judul', decrypt($id))->whereNotNull('date_penilai')->get();
        $html = '';
        foreach($arr_fkt as $fkt){
            $fpkt = Trainingfpkt::where('id_fkt', $fkt->id)->get();
            if($fpkt->isNotEmpty()){
                //ttd peserta
                $qr_1 = Qrcodefpkt::where('id_fkt', $fkt->id)->where('type', 1)->first();
                if(!empty($qr_1)){
                    $peserta_qr = $qr_1->qr;
                    $peserta_fkt_id = $qr_1->id_fkt;
                    $link_qr_peserta = route('training.ptt.qrcode.fpkt', ['code' => $peserta_qr, 'id' => $peserta_fkt_id]);
                }else{
                    $peserta_qr = null;
                    $peserta_fkt_id = null;
                    $link_qr_peserta = '';
                }
                //ttd atasan
                $qr_2 = Qrcodefpkt::where('id_fkt', $fkt->id)->where('type', 2)->first();
                if(!empty($qr_2)){
                    $atasan_qr = $qr_2->qr;
                    $atasan_fkt_id = $qr_2->id_fkt;
                    $link_qr_atasan = route('training.ptt.qrcode.fpkt', ['code' => $atasan_qr, 'id' => $atasan_fkt_id]);
                }else{
                    $atasan_qr = null;
                    $atasan_fkt_id = null;
                    $link_qr_atasan = '';
                }
                //ttd hrd
                $qr_3 = Qrcodefpkt::where('id_fkt', $fkt->id)->where('type', 3)->first();
                if(!empty($qr_3)){
                    $hrd_qr = $qr_3->qr;
                    $hrd_fkt_id = $qr_3->id_fkt;
                    $link_qr_hrd = route('training.ptt.qrcode.fpkt', ['code' => $hrd_qr, 'id' => $hrd_fkt_id]);
                }else{
                    $hrd_qr = null;
                    $hrd_fkt_id = null;
                    $link_qr_hrd = '';
                }
                $sum_rata = $fpkt->sum('level_rata');
                $jml_fpkt = $fpkt->count();
                if($sum_rata > 0 && $jml_fpkt > 0){
                    $skor = floor($sum_rata/$jml_fpkt);
                }else{
                    $skor = 0;
                }
                
                $data = [
                    'title' => 'Formulir Pelaksanaan Pelatihan',
                    'fkt' => $fkt,
                    'fpkt' => $fpkt,
                    'skor' => $skor,
                    'link_qr_peserta' => $link_qr_peserta,
                    'link_qr_atasan' => $link_qr_atasan,
                    'link_qr_hrd' => $link_qr_hrd
                ];
                $view = view('pages.hrd.training.ptt.print-fpkt')->with(compact('data'));
                $html .= $view->render();
            }
        }
        $pdf = PDF::set_option("isPhpEnabled", false);
        $pdf->loadHTML($html);
        $pdf->setPaper('a4', 'landscape');
        return $pdf->stream('FORMULIR PELAKSANAAN PELATIHAN.pdf');
    }
    //qrcode fpkt
    public function qrcode_fpkt($code,$id){
        $query = Qrcodefpkt::where('id_fkt', $id)->where('qr', $code)->first();
        return view('pages.hrd.training.ptt.codeqr-fpkt', compact('query'));
    }
    // notification mr.mizukami and mr.sakurai
    public function notification_ptt(Request $request){
        DB::beginTransaction();
        try {
            if($request->id_approval == 'direktur'){
                //update fkt
                $query = Trainingfkt::where('tahun_usulan', $request->tahun_usulan)
                    ->where('tipe', 'ptt')
                    ->whereNotNull('date_checker')
                    ->where('status', 2)
                    ->get()->unique('id')->pluck('id');
                //role direktur produksi
                $user = User::whereHas(
                    'roles', function($q){
                        $q->where('id', 51);
                    }
                )->first();
                if(!empty($user->employee_id)){
                    foreach($query as $key => $value){
                        $fkt = Trainingfkt::where('id', $value)->first();
                        $fpkt = Trainingfpkt::where('id_fkt', $value)->first();
                        if($fpkt->status == 12){
                            if($fkt->pemohon->department->approval_code == '2'){
                                $post = Trainingfkt::where('id', $value)
                                    ->update([
                                        'status' => 5,
                                        'is_notif' => '0'
                                    ]);
                            }else{
                                $post = Trainingfkt::where('id', $value)
                                    ->update([
                                        'status' => 4,
                                        'is_notif' => '1'
                                    ]);    
                            }
                        }
                    }
                    //notification mr.mizukami
                    $qry_user = User::where('employee_id', $user->employee_id)->first();
                    $details = [
                        'greeting' => 'Hi '.$qry_user->name,
                        'subject' => 'PROGRAM TRAINING TAHUNAN',
                        'body' => 'Ingin Menginformasikan bahwa ada Program Training Tahunan yang membutuhkan approval anda',
                        'actionText' => 'Silahkan Login',
                        'actionURL' => url('/hrd/training/ptt/'.encrypt($request->tahun_usulan).'/form'),
                        'thanks' => 'Terimakasih atas perhatiannya!!'
                    ];
                    //send mail
                    $qry_user->notify(new AccountNotification($details));
                }else{
                    return response()->json(['message' => "Data Employee Not Found"], 500);
                }
            }
            
            if($request->id_approval == 'presiden'){
                //update fkt
                $post_1 = Trainingfkt::where('tahun_usulan', $request->tahun_usulan)
                    ->where('tipe', 'ptt')
                    ->whereNotNull('date_checker')
                    ->where('status', 5)
                    ->where('is_notif','0')
                    ->update([
                        'is_notif' => '1'
                    ]);

                $post_2 = Trainingfkt::where('tahun_usulan', $request->tahun_usulan)
                    ->where('tipe', 'ptt')
                    ->whereNotNull('date_verified')
                    ->where('status', 4)
                    ->where('is_notif','1')
                    ->update([
                        'status' => 5
                    ]);

                //role presiden direktur
                $user = User::whereHas(
                    'roles', function($q){
                        $q->where('id', 49);
                    }
                )->first();
                if(!empty($user->employee_id)){
                    //notification mr.sakurai
                    $qry_user = User::where('employee_id', $user->employee_id)->first();
                        $details = [
                            'greeting' => 'Hi '.$qry_user->name,
                            'subject' => 'PROGRAM TRAINING TAHUNAN',
                            'body' => 'Ingin Menginformasikan bahwa ada Program Training Tahunan yang membutuhkan approval anda',
                            'actionText' => 'Silahkan Login',
                            'actionURL' => url('/hrd/training/ptt/'.encrypt($request->tahun_usulan).'/form'),
                            'thanks' => 'Terimakasih atas perhatiannya!!'
                        ];
                        //send mail
                        $qry_user->notify(new AccountNotification($details));
                }
            }

            DB::commit();

            return response()->json(['message' => "Email has been send"], 200);
        }catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }
    //end ptt training

    //Start pti training
    //active
    public function index_pti(Request $request){
        $users = auth()->user();
        // $test = $users->roles()->pluck('id')->first();
        // dd(vars: $test);
        $vendors = Vendor::where('tipe', 'training')->get();
        if($users->roles()->pluck('id')->first() == '1'){
            $query = Trainingfpkt::whereIn('status', [11,4,5]);
            $notif_pti = $query->count();
            $data = $query->get()->unique('kode_judul_fpkt');
        }elseif($users->roles()->pluck('id')->first() == '2'){
            $query = Trainingfpkt::where('status', 11);
            $notif_pti = $query->count();
            $data = $query->get()->unique('kode_judul_fpkt');
        }elseif($users->roles()->pluck('id')->first() == '51'){
            $query = Trainingfpkt::where('status', 4);
            $notif_pti = $query->count();
            $data = $query->get()->unique('kode_judul_fpkt');
        }elseif($users->roles()->pluck('id')->first() == '49'){
            $query = Trainingfpkt::where('status', 5);
            $notif_pti = $query->count();
            $data = $query->get()->unique('kode_judul_fpkt');
        }
        if ($request->ajax()) {          
            return DataTables::of($data)
                ->addColumn('judul_fpkt', function($data){
                    return $data['judul_fpkt'];
                })
                ->addColumn('vendor', function($data){
                    if($data->id_vendor == null){
                        return $data->nama_vendor;
                    }else{
                        return $data->vendor->nama;
                    }
                })
                ->addColumn('total_biaya', function($data){
                    $jml_biaya = Trainingfpkt::where('kode_judul_fpkt', $data['kode_judul_fpkt'])->sum('biaya_fpkt');
                    return 'Rp '.number_format($jml_biaya,2,',','.');
                })
                ->addColumn('status', function($data){
                    if($data['status'] == 1) return '<a href="#" <span class="badge text-bg-primary"><i class="ri-edit-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 2) return '<a href="#" data-id="'.encrypt($data['kode_judul_fpkt']).'" data-bs-toggle="modal" data-bs-target="#modal-status" <span class="badge text-bg-warning view-status"><i class="ri-time-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 3) return '<a href="#" data-id="'.encrypt($data['kode_judul_fpkt']).'" data-bs-toggle="modal" data-bs-target="#modal-status" <span class="badge text-bg-info view-status"><i class="ri-time-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 4) return '<a href="#" <span class="badge text-bg-info view-status"><i class="ri-time-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 5) return '<a href="#" <span class="badge text-bg-secondary view-status"><i class="ri-time-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 6) return '<a href="#" data-id="'.encrypt($data['kode_judul_fpkt']).'" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-success view-status"><i class="ri-checkbox-circle-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 7) return '<a href="#" data-id="'.encrypt($data['kode_judul_fpkt']).'" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-success view-status"><i class="ri-checkbox-circle-line align-bottom"></i> Finished</span></a>';
                    if($data['status'] == 11) return '<a href="#" <span class="badge text-bg-secondary"><i class="ri-checkbox-circle-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 15) return '<a href="#" data-id="'.encrypt($data['kode_judul_fpkt']).'" data-bs-toggle="modal" data-bs-target="#modal-status" <span class="badge text-bg-secondary view-status"><i class="ri-error-warning-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 16) return '<a href="#" data-id="'.encrypt($data['kode_judul_fpkt']).'" data-bs-toggle="modal" data-bs-target="#modal-status" <span class="badge text-bg-secondary view-status"><i class="ri-error-warning-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 17) return '<a href="#" data-id="'.encrypt($data['kode_judul_fpkt']).'" data-bs-toggle="modal" data-bs-target="#modal-status" <span class="badge text-bg-danger view-status"><i class="ri-close-circle-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 18) return '<a href="#" data-id="'.encrypt($data['kode_judul_fpkt']).'" data-bs-toggle="modal" data-bs-target="#modal-status" <span class="badge text-bg-danger view-status"><i class="ri-close-circle-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                })
                ->addColumn('action', function ($data) {                             
                    // $list_revise = '<li><a href="#" data-id="'.encrypt($data['kode_judul_fpkt']).'" data-bs-toggle="modal" data-bs-target=".bs-example-modal-revise" class="dropdown-item view-revise"><i class="ri-error-warning-line align-bottom me-2 text-muted"></i> Revise</a></li>';
                    // $list_reject = '<li><a href="#" data-id="'.encrypt($data['kode_judul_fpkt']).'" data-bs-toggle="modal" data-bs-target=".bs-example-modal-reject" class="dropdown-item view-reject"><i class="ri-close-circle-line align-bottom me-2 text-muted"></i> Reject</a></li>';
                    $list_approve = '<li><a href="#" data-id="'.encrypt($data['kode_judul_fpkt']).'" data-bs-toggle="modal" data-bs-target=".bs-example-modal-approve" class="dropdown-item view-approve"><i class="ri-checkbox-line align-bottom me-2 text-muted"></i> Approve</a></li>';
                    $list_revise = '';
                    $list_reject = '';
                    $list_print_fkt = '<li><a href="'.route('training.pti.fpkt.pdf', encrypt($data['kode_judul_fpkt'])).'" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FPKP</a></li>';
                    $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_approve.$list_revise.$list_reject.$list_print_fkt.'</ul></div>';

                    return $button;
                })
                ->addColumn('peserta', function($data){
                    $query = Trainingfpkt::where('kode_judul_fpkt', $data['kode_judul_fpkt'])->get();
                    if($query->isNotEmpty()){
                        $peserta = '<div class="col-lg-12"><table class="table table-bordered" style="table-layout: fixed; width:100%;">';
                        $peserta .= '
                            <thead>
                                <tr>
                                    <th colspan="4" style="text-align: center;">Pengajuan Program Pelatihan</th>
                                </tr>
                                <tr>
                                    <th style="text-align: center;">Peserta</th>
                                    <th style="text-align: center;">Pelaksanaan</th>
                                    <th style="text-align: center;">Biaya</th>
                                    <th style="text-align: center;">Status</th>
                                </tr>
                            </thead>
                            ';
                        $peserta .= '<tbody>';
                        foreach($query as $qry){
                            $peserta .= '<tr>';                  
                            $peserta .= '<td>'.$qry->peserta->fullname.'</td>';    
                            $peserta .= '<td>'.date('d M Y', strtotime($qry->date_pelaksanaan)).'</td>';    
                            $peserta .= '<td> Rp '.number_format($qry->biaya_fpkt,2,',','.').'</td>';        
                            $peserta .= '<td><a href="javascript:void(0)" data-id="'.encrypt($qry->id).'" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-info view-status">cek status</span></a>';     
                            $peserta .= '</tr>';
                        } 
                        $peserta .= '</tbody></table></div>';
                    }else{
                        $peserta = '';
                    }
                    return $peserta;
                })
                ->rawColumns(['action','status','peserta'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.training.pti.index', compact('users','notif_pti'));
    }

    public function form_fpkt_pti(Request $request, $id){
        $user = auth()->user();
        $id = decrypt($id);
        $fkt = Trainingfkt::where('id', $id)->first();
        $qry_fkt = Trainingfkt::where('kode', $fkt->kode)->where('judul', $fkt->judul)->get();
        $arr_id = implode(',', $qry_fkt->pluck('id')->toArray());
        $fpkt = Trainingfpkt::where('id_fkt', $id)->first(); 
        $arr_fpkt = Trainingfpkt::where('id_fkt', $id)->get();
        $arr_peserta = Employee::whereIn('id', $qry_fkt->pluck('id_peserta'))->get();
        foreach($arr_peserta as $peserta){
            if(!empty($peserta->position->nama)){
                $jabatan[] = $peserta->position->nama;
            }else{
                $jabatan[] = '-';
            }
            if(!empty($peserta->department->name)){
                $department[] = $peserta->department->name;
            }else{
                $department[] = '-';
            }
        }
        $arr_jabatan = $jabatan;
        $arr_dept = $department;
        return view('pages.hrd.training.pti.form-fpkt', compact('user','fkt','fpkt','arr_fpkt','arr_peserta','arr_jabatan','arr_dept','arr_id'));
    }

    public function pti_store(Request $request){
        DB::beginTransaction();
        try {
            $user = auth()->user();
            //super user
            if($user->roles()->pluck('id')->first() == '1'){
                if($request->tipe == 'approve'){
                    $query = Trainingfpkt::where('kode_judul_fpkt', decrypt($request->kode))
                        ->where('status', 5);
                    $fpkt = $query->first();
                    $arr_id = $query->get()->pluck('id');
                    // finished
                    $post = Trainingfpkt::whereIn('id', $arr_id)->update([
                        'id_bod2' => $user->employee_id,
                        'date_bod2' => date('Y-m-d H:i:s'),
                        'status' => 12
                    ]);

                    //record pelatihan
                    $finished_fpkt = Trainingfpkt::whereIn('id', $arr_id)->get();
                    foreach($finished_fpkt as $record_fpkt){
                        // insert
                        $insert_record = new Trainingrecord;
                        $insert_record->id_employee = $record_fpkt->id_peserta;
                        $insert_record->judul = $record_fpkt->judul_fpkt;
                        $insert_record->id_vendor = $record_fpkt->id_vendor;
                        $insert_record->biaya = $record_fpkt->biaya_fpkt;
                        $insert_record->id_fkt = $record_fpkt->id_fkt ?? null;
                        $insert_record->id_fpkt = $record_fpkt->id;
                        $insert_record->status = '13'; //on progress
                        $insert_record->save();

                        $qry_user = User::where('employee_id', $record_fpkt->id_peserta)->first();
                        if(!empty($qry_user->email)){
                            if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                                $details = [
                                    'greeting' => 'Hi '.$qry_user->name,
                                    'subject' => 'Congratulations, your training application has been approved',
                                    'body' => 'Ingin Menginformasikan bahwa usulan topik training "'.$record_fpkt->judul_fpkt.'" telah disetujui silahkan dicek dan dilengkapi data anda',
                                    'actionText' => 'Silahkan Login',
                                    'actionURL' => route('training.emp.index'),
                                    'thanks' => 'Terimakasih atas perhatiannya!!'
                                ];
                            }else{
                                $details = [
                                    'greeting' => 'Hi '.$qry_user->name,
                                    'subject' => 'Congratulations, your training application has been approved',
                                    'body' => 'Ingin Menginformasikan bahwa usulan topik training "'.$record_fpkt->judul_fpkt.'" telah disetujui silahkan dicek dan dilengkapi data anda',
                                    'actionText' => 'Silahkan Login',
                                    'actionURL' => route('training.emp.index'),
                                    'thanks' => 'Terimakasih atas perhatiannya!!'
                                ];
                            }
                            //send mail
                            $qry_user->notify(new AccountNotification($details));
                        }
                    }
                    
                    //sato ryo ttd
                    foreach($arr_id as $key => $val){
                        $date_qr = date('Ymd');
                        $insert_fpkt_qr = new Qrcodefpkt;
                        $insert_fpkt_qr->id_fpkt = $val;
                        $insert_fpkt_qr->qr = $date_qr.$user->employee_id;
                        $insert_fpkt_qr->date_approval = date('Y-m-d H:i:s');
                        $insert_fpkt_qr->type = 5;
                        $insert_fpkt_qr->save();        
                    }
                     //update log user activity
                    $insert = new Log;
                    $insert->user_id = $user->id;
                    $insert->ip_address = $request->ip();
                    $insert->action = 'approved';
                    $insert->description = 'Approved formulir pelaksanaan kebutuhan pelatihan dengan nama pemohon'.'"'.$fpkt->pemohon->fullname.'" tujuan "Pelaksanaan Pelatihan"';
                    $insert->save();
    
                    DB::commit();
        
                    return response()->json(['message' => "$fpkt->judul_fpkt has been approved"], 200);
                }

                // if($request->tipe == 'approve'){
                //     $query = Trainingfpkt::where('kode_judul_fpkt', decrypt($request->kode))
                //         ->where('status', 11);
                //     $fpkt = $query->first();
                //     $arr_id = $query->get()->pluck('id');
                //     if($query->sum('biaya_fpkt') > 0){
                //         if($fpkt->pemohon->department->id == 3 || $fpkt->pemohon->department->id == 4 || $fpkt->pemohon->department->id == 5 || $fpkt->pemohon->department->id == 6 || $fpkt->pemohon->department->id == 8 || $fpkt->pemohon->department->id == 9 || $fpkt->pemohon->department->id == 10){
                //             // mr. mizukami
                //             $post = Trainingfpkt::whereIn('id', $arr_id)->update([
                //                 'id_hrd' => $user->employee_id,
                //                 'date_hrd' => date('Y-m-d H:i:s'),
                //                 'status' => 4
                //             ]);
                //             //send email mr. mizukami
                //             $users = User::whereHas(
                //                 'roles', function($q){
                //                     $q->where('id', 1); //role bod 1
                //                 }
                //             )->get();
                //             if($users->isNotEmpty()){
                //                 foreach($users as $key_user){
                //                     if(!empty($key_user->email)){
                //                         $qry_user = User::where('employee_id', $key_user->employee_id)->first();
                //                         $details = [
                //                             'greeting' => 'Hi '.$qry_user->name,
                //                             'subject' => 'Pengajuan Pelaksanaan Pelatihan',
                //                             'body' => 'Ingin Menginformasikan bahwa ada usulan pelatihan "'.$fpkt->judul_fpkt.'" yang membutuhkan approval anda',
                //                             'actionText' => 'Silahkan Login',
                //                             'actionURL' => url('/hrd/training/pti'),
                //                             'thanks' => 'Terimakasih atas perhatiannya!!'
                //                         ];
                //                         //send mail
                //                         $qry_user->notify(new AccountNotification($details));
                //                     }
                //                 }
                //             }
                //         }else{
                //             // mr. sato ryo
                //             $post = Trainingfpkt::whereIn('id', $arr_id)->update([
                //                 'id_hrd' => $user->employee_id,
                //                 'date_hrd' => date('Y-m-d H:i:s'),
                //                 'status' => 5
                //             ]);

                //             //send email mr. sato ryo
                //             $users = User::whereHas(
                //                 'roles', function($q){
                //                     $q->where('id', 1); //role bod 2
                //                 }
                //             )->get();
                //             if($users->isNotEmpty()){
                //                 foreach($users as $key_user){
                //                     if(!empty($key_user->email)){
                //                         $qry_user = User::where('employee_id', $key_user->employee_id)->first();
                //                         $details = [
                //                             'greeting' => 'Hi '.$qry_user->name,
                //                             'subject' => 'Pengajuan Pelaksanaan Pelatihan',
                //                             'body' => 'Ingin Menginformasikan bahwa ada usulan pelatihan "'.$fpkt->judul_fpkt.'" yang membutuhkan approval anda',
                //                             'actionText' => 'Silahkan Login',
                //                             'actionURL' => url('/hrd/training/pti'),
                //                             'thanks' => 'Terimakasih atas perhatiannya!!'
                //                         ];
                //                         //send mail
                //                         $qry_user->notify(new AccountNotification($details));
                //                     }
                //                 }
                //             }
                //         }
                //     }else{
                //         //finished
                //         $post = Trainingfpkt::whereIn('id', $arr_id)->update([
                //             'id_hrd' => $user->employee_id,
                //             'date_hrd' => date('Y-m-d H:i:s'),
                //             'status' => 12
                //         ]);
                //     }
                //     //hrd ttd
                //     foreach($arr_id as $key => $val){
                //         $date_qr = date('Ymd');
                //         $insert_fpkt_qr = new Qrcodefpkt;
                //         $insert_fpkt_qr->id_fpkt = $val;
                //         $insert_fpkt_qr->qr = $date_qr.$user->employee_id;
                //         $insert_fpkt_qr->date_approval = date('Y-m-d H:i:s');
                //         $insert_fpkt_qr->type = 6;
                //         $insert_fpkt_qr->save();        
                //     }

                //     //update log user activity
                //     $insert = new Log;
                //     $insert->user_id = $user->id;
                //     $insert->ip_address = $request->ip();
                //     $insert->action = 'approved';
                //     $insert->description = 'Approved formulir pelaksanaan kebutuhan pelatihan dengan nama pemohon'.'"'.$fpkt->pemohon->fullname.'" tujuan "Pelaksanaan Pelatihan"';
                //     $insert->save();
    
                //     DB::commit();
        
                //     return response()->json(['message' => "$fpkt->judul_fpkt has been approved"], 200);
                // }
            }
            //hrd
            if($user->roles()->pluck('id')->first() == '2'){
                if($request->tipe == 'approve'){
                    $query = Trainingfpkt::where('kode_judul_fpkt', decrypt($request->kode))
                        ->where('status', 11);
                    $fpkt = $query->first();
                    $arr_id = $query->get()->pluck('id');
                    if($query->sum('biaya_fpkt') > 0){
                        //jika departemen ke mr. mizukami
                        if($fpkt->pemohon->department->id == 3 || $fpkt->pemohon->department->id == 4 || $fpkt->pemohon->department->id == 5 || $fpkt->pemohon->department->id == 6 || $fpkt->pemohon->department->id == 8 || $fpkt->pemohon->department->id == 9 || $fpkt->pemohon->department->id == 10){
                            // mr. mizukami
                            $post = Trainingfpkt::whereIn('id', $arr_id)->update([
                                'id_hrd' => $user->employee_id,
                                'date_hrd' => date('Y-m-d H:i:s'),
                                'status' => 4
                            ]);
                            //send email mr. mizukami
                            $users = User::whereHas(
                                'roles', function($q){
                                    $q->where('id', 51); //role bod production director
                                }
                            )->get();
                            if($users->isNotEmpty()){
                                foreach($users as $key_user){
                                    if(!empty($key_user->email)){
                                        $qry_user = User::where('employee_id', $key_user->employee_id)->first();
                                        $details = [
                                            'greeting' => 'Hi '.$qry_user->name,
                                            'subject' => 'Pengajuan Pelaksanaan Pelatihan',
                                            'body' => 'Ingin Menginformasikan bahwa ada usulan pelatihan "'.$fpkt->judul_fpkt.'" yang membutuhkan approval anda',
                                            'actionText' => 'Silahkan Login',
                                            'actionURL' => url('/hrd/training/pti'),
                                            'thanks' => 'Terimakasih atas perhatiannya!!'
                                        ];
                                        //send mail
                                        $qry_user->notify(new AccountNotification($details));
                                    }
                                }
                            }
                        }else{
                            // mr. sato ryo
                            $post = Trainingfpkt::whereIn('id', $arr_id)->update([
                                'id_hrd' => $user->employee_id,
                                'date_hrd' => date('Y-m-d H:i:s'),
                                'status' => 5
                            ]);

                            //send email mr. sato ryo
                            $users = User::whereHas(
                                'roles', function($q){
                                    $q->where('id', 49); //role bod president director
                                }
                            )->get();
                            if($users->isNotEmpty()){
                                foreach($users as $key_user){
                                    if(!empty($key_user->email)){
                                        $qry_user = User::where('employee_id', $key_user->employee_id)->first();
                                        $details = [
                                            'greeting' => 'Hi '.$qry_user->name,
                                            'subject' => 'Pengajuan Pelaksanaan Pelatihan',
                                            'body' => 'Ingin Menginformasikan bahwa ada usulan pelatihan "'.$fpkt->judul_fpkt.'" yang membutuhkan approval anda',
                                            'actionText' => 'Silahkan Login',
                                            'actionURL' => url('/hrd/training/pti'),
                                            'thanks' => 'Terimakasih atas perhatiannya!!'
                                        ];
                                        //send mail
                                        $qry_user->notify(new AccountNotification($details));
                                    }
                                }
                            }
                        }
                    }else{
                        //finished
                        $post = Trainingfpkt::whereIn('id', $arr_id)->update([
                            'id_hrd' => $user->employee_id,
                            'date_hrd' => date('Y-m-d H:i:s'),
                            'status' => 12
                        ]);
                    }
                    //hrd ttd
                    foreach($arr_id as $key => $val){
                        $date_qr = date('Ymd');
                        $insert_fpkt_qr = new Qrcodefpkt;
                        $insert_fpkt_qr->id_fpkt = $val;
                        $insert_fpkt_qr->qr = $date_qr.$user->employee_id;
                        $insert_fpkt_qr->date_approval = date('Y-m-d H:i:s');
                        $insert_fpkt_qr->type = 6;
                        $insert_fpkt_qr->save();        
                    }
                     //update log user activity
                    $insert = new Log;
                    $insert->user_id = $user->id;
                    $insert->ip_address = $request->ip();
                    $insert->action = 'approved';
                    $insert->description = 'Approved formulir pelaksanaan kebutuhan pelatihan dengan nama pemohon'.'"'.$fpkt->pemohon->fullname.'" tujuan "Pelaksanaan Pelatihan"';
                    $insert->save();
    
                    DB::commit();
        
                    return response()->json(['message' => "$fpkt->judul_fpkt has been approved"], 200);
                }
            }            
            //mr. mizukami      
            if($user->roles()->pluck('id')->first() == '51'){
                if($request->tipe == 'approve'){
                    $query = Trainingfpkt::where('kode_judul_fpkt', decrypt($request->kode))
                        ->where('status', 4);
                    $fpkt = $query->first();
                    $arr_id = $query->get()->pluck('id');
                    // mr. sato ryo
                    $post = Trainingfpkt::whereIn('id', $arr_id)->update([
                        'id_bod1' => $user->employee_id,
                        'date_bod1' => date('Y-m-d H:i:s'),
                        'status' => 5
                    ]);
                    
                    //mr. mizukami ttd
                    foreach($arr_id as $key => $val){
                        $date_qr = date('Ymd');
                        $insert_fpkt_qr = new Qrcodefpkt;
                        $insert_fpkt_qr->id_fpkt = $val;
                        $insert_fpkt_qr->qr = $date_qr.$user->employee_id;
                        $insert_fpkt_qr->date_approval = date('Y-m-d H:i:s');
                        $insert_fpkt_qr->type = 4;
                        $insert_fpkt_qr->save();        
                    }
                     //update log user activity
                    $insert = new Log;
                    $insert->user_id = $user->id;
                    $insert->ip_address = $request->ip();
                    $insert->action = 'approved';
                    $insert->description = 'Approved formulir pelaksanaan kebutuhan pelatihan dengan nama pemohon'.'"'.$fpkt->pemohon->fullname.'" tujuan "Pelaksanaan Pelatihan"';
                    $insert->save();
    
                    DB::commit();
        
                    return response()->json(['message' => "$fpkt->judul_fpkt has been approved"], 200);
                }
            }    
            //mr. sato ryo
            if($user->roles()->pluck('id')->first() == '49'){
                if($request->tipe == 'approve'){
                    $query = Trainingfpkt::where('kode_judul_fpkt', decrypt($request->kode))
                        ->where('status', 5);
                    $fpkt = $query->first();
                    $arr_id = $query->get()->pluck('id');
                    // finished
                    $post = Trainingfpkt::whereIn('id', $arr_id)->update([
                        'id_bod2' => $user->employee_id,
                        'date_bod2' => date('Y-m-d H:i:s'),
                        'status' => 12
                    ]);

                    //record pelatihan
                    $finished_fpkt = Trainingfpkt::whereIn('id', $arr_id)->get();
                    foreach($finished_fpkt as $record_fpkt){
                        // insert
                        $insert_record = new Trainingrecord;
                        $insert_record->id_employee = $record_fpkt->id_peserta;
                        $insert_record->judul = $record_fpkt->judul_fpkt;
                        $insert_record->id_vendor = $record_fpkt->id_vendor;
                        $insert_record->biaya = $record_fpkt->biaya_fpkt;
                        $insert_record->id_fkt = $record_fpkt->id_fkt ?? null;
                        $insert_record->id_fpkt = $record_fpkt->id;
                        $insert_record->status = '13'; //on progress
                        $insert_record->save();

                        $qry_user = User::where('employee_id', $record_fpkt->id_peserta)->first();
                        if(!empty($qry_user->email)){
                            if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                                $details = [
                                    'greeting' => 'Hi '.$qry_user->name,
                                    'subject' => 'Congratulations, your training application has been approved',
                                    'body' => 'Ingin Menginformasikan bahwa usulan topik training "'.$record_fpkt->judul_fpkt.'" telah disetujui silahkan dicek dan dilengkapi data anda',
                                    'actionText' => 'Silahkan Login',
                                    'actionURL' => route('training.emp.index'),
                                    'thanks' => 'Terimakasih atas perhatiannya!!'
                                ];
                            }else{
                                $details = [
                                    'greeting' => 'Hi '.$qry_user->name,
                                    'subject' => 'Congratulations, your training application has been approved',
                                    'body' => 'Ingin Menginformasikan bahwa usulan topik training "'.$record_fpkt->judul_fpkt.'" telah disetujui silahkan dicek dan dilengkapi data anda',
                                    'actionText' => 'Silahkan Login',
                                    'actionURL' => route('training.emp.index'),
                                    'thanks' => 'Terimakasih atas perhatiannya!!'
                                ];
                            }
                            //send mail
                            $qry_user->notify(new AccountNotification($details));
                        }
                    }
                    
                    //sato ryo ttd
                    foreach($arr_id as $key => $val){
                        $date_qr = date('Ymd');
                        $insert_fpkt_qr = new Qrcodefpkt;
                        $insert_fpkt_qr->id_fpkt = $val;
                        $insert_fpkt_qr->qr = $date_qr.$user->employee_id;
                        $insert_fpkt_qr->date_approval = date('Y-m-d H:i:s');
                        $insert_fpkt_qr->type = 5;
                        $insert_fpkt_qr->save();        
                    }
                     //update log user activity
                    $insert = new Log;
                    $insert->user_id = $user->id;
                    $insert->ip_address = $request->ip();
                    $insert->action = 'approved';
                    $insert->description = 'Approved formulir pelaksanaan kebutuhan pelatihan dengan nama pemohon'.'"'.$fpkt->pemohon->fullname.'" tujuan "Pelaksanaan Pelatihan"';
                    $insert->save();
    
                    DB::commit();
        
                    return response()->json(['message' => "$fpkt->judul_fpkt has been approved"], 200);
                }  
            }        
        }catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    public function fkt_pti_pdf($id){
        $fkt = Trainingfkt::where('kode_judul', decrypt($id))->first();
        //get qrcode
        $all_qrcode = Qrcodefkt::where('kode_fkt', $fkt->kode)->get();

        //pemohon ttd
        $pemohon = $fkt->pemohon->fullname;
	    $cp_pemohon = Str::lower($pemohon);
        $pemohon_ttd = ucwords($cp_pemohon);
        
        $pos_pemohon = $fkt->pemohon->position->nama ?? '-';
	    $cp_pos_pemohon = Str::lower($pos_pemohon);
        $pos_pemohon_ttd = ucwords($cp_pos_pemohon);

        $qr_1 =  $all_qrcode->whereStrict('type', 1)->first();
        if(!empty($qr_1)){
            $pemohon_qr = $qr_1->qr;
            $pemohon_kode_qr = str_replace("/","-",$qr_1->kode_fkt);

            $link_qr_pemohon = route('training.pti.qrcode.pemohon', ['code' => $pemohon_qr, 'id' => $pemohon_kode_qr]);
        }else{
            $pemohon_qr = null;
            $pemohon_kode_qr = null;

            $link_qr_pemohon = '';
        }
        
        //checker ttd
        $checker = $fkt->checker->fullname;
	    $cp_checker = Str::lower($checker);
        $checker_ttd = ucwords($cp_checker);

        $pos_checker = $fkt->checker->position->nama ?? '-';
	    $cp_pos_checker = Str::lower($pos_checker);
        $pos_checker_ttd = ucwords($cp_pos_checker);

        $qr_2 =  $all_qrcode->whereStrict('type', 5)->first();
        if(!empty($qr_2)){
            $checker_qr = $qr_2->qr;
            $checker_kode_qr = str_replace("/","-",$qr_2->kode_fkt);

            $link_qr_checker = route('training.pti.qrcode.checker', ['code' => $checker_qr, 'id' => $checker_kode_qr]);
        }else{
            $checker_qr = null;
            $checker_kode_qr = null;

            $link_qr_checker = '';
        }

        //mr.mizukami ttd
        $verified = $fkt->verified->fullname ?? '-';
	    $cp_verified = Str::lower($verified);
        $verified_ttd = ucwords($cp_verified);

        $pos_verified = $fkt->verified->position->nama ?? '-';
	    $cp_pos_verified = Str::lower($pos_verified);
        $pos_verified_ttd = ucwords($cp_pos_verified);

        $qr_3 =  $all_qrcode->whereStrict('type', 3)->first();
        if(!empty($qr_3)){
            $verified_qr = $qr_3->qr;
            $verified_kode_qr = str_replace("/","-",$qr_3->kode_fkt);

            $link_qr_verified = route('training.pti.qrcode.verified', ['code' => $verified_qr, 'id' => $verified_kode_qr]);
        }else{
            $verified_qr = null;
            $verified_kode_qr = null;

            $link_qr_verified = '';
        }

        $qr_4 =  $all_qrcode->whereStrict('type', 4)->first();
        if(!empty($qr_4)){
            $approval_qr = $qr_4->qr;
            $approval_kode_qr = str_replace("/","-",$qr_4->kode_fkt);

            $link_qr_approval = route('training.pti.qrcode.approval', ['code' => $approval_qr, 'id' => $approval_kode_qr]);
        }else{
            $approval_qr = null;
            $approval_kode_qr = null;

            $link_qr_approval = '';
        }

        $arr_fkt = Trainingfkt::where('kode_judul', decrypt($id))->get();
        $data = [
            'title' => 'FORMULIR KEBUTUHAN TRAINING',
            'fkt' => $fkt,
            'arr_fkt' => $arr_fkt,
            'pemohon_ttd' => $pemohon_ttd,
            'pos_pemohon_ttd' => $pos_pemohon_ttd,
            'checker_ttd' => $checker_ttd,
            'pos_checker_ttd' => $pos_checker_ttd,
            'link_qr_pemohon' => $link_qr_pemohon,
            'link_qr_checker' => $link_qr_checker,
            'link_qr_verified' => $link_qr_verified,
            'link_qr_approval' => $link_qr_approval
        ];
        $pdf = PDF::loadView('pages.hrd.training.pti.print-fkt', $data)->setPaper('a4', 'landscape');
        $pdf->set_option("isPhpEnabled", true);
        return $pdf->stream('FORMULIR KEBUTUHAN TRAINING - '.$fkt->pemohon->fullname.'.pdf');
    }

    public function qrcode_pemohon_pti($code,$id){
        $kode_fkt = str_replace("-","/",$id);
        $query = Qrcodefkt::where('kode_fkt', $kode_fkt)->where('qr', $code)->where('type', 1)->first();
        if($query->fkt->tipe == 'ptt'){
            $usulan = 'Program Training Tahunan';
        }else{
            $usulan = 'Program Training Insidentil';
        }
        return view('pages.hrd.training.pti.codeqr-pemohon', compact('query','usulan'));
    }

    public function qrcode_checker_pti($code,$id){
        $kode_fkt = str_replace("-","/",$id);
        $query = Qrcodefkt::where('kode_fkt', $kode_fkt)->where('qr', $code)->where('type', 5)->first();
        if($query->fkt->tipe == 'ptt'){
            $usulan = 'Program Training Tahunan';
        }else{
            $usulan = 'Program Training Insidentil';
        }
        return view('pages.hrd.training.pti.codeqr-checker', compact('query','usulan'));
    }

    public function qrcode_verified_pti($code,$id){
        $kode_fkt = str_replace("-","/",$id);
        $query = Qrcodefkt::where('kode_fkt', $kode_fkt)->where('qr', $code)->where('type', 3)->first();
        if($query->fkt->tipe == 'ptt'){
            $usulan = 'Program Training Tahunan';
        }else{
            $usulan = 'Program Training Insidentil';
        }
        return view('pages.hrd.training.pti.codeqr-verified', compact('query','usulan'));
    }

    public function qrcode_approval_pti($code,$id){
        $kode_fkt = str_replace("-","/",$id);
        $query = Qrcodefkt::where('kode_fkt', $kode_fkt)->where('qr', $code)->where('type', 4)->first();
        if($query->fkt->tipe == 'ptt'){
            $usulan = 'Program Training Tahunan';
        }else{
            $usulan = 'Program Training Insidentil';
        }
        return view('pages.hrd.training.pti.codeqr-approval', compact('query','usulan'));
    }

    public function fpkt_pti_pdf($id){
        $arr_fpkt = Trainingfpkt::where('kode_judul_fpkt', decrypt($id))->get();
        $html = '';
        foreach($arr_fpkt as $fpkt){
            $arr_tujuan = $fpkt->tujuan;
            $arr_kompetensi = $fpkt->kompetensi;
            $arr_skill = json_decode($fpkt->skill, true);
            $arr_peserta = json_decode($fpkt->level_peserta, true);
            $arr_atasan = json_decode($fpkt->level_atasan, true);
            $arr_rata = json_decode($fpkt->level_rata, true);
            $arr_kebutuhan = json_decode($fpkt->level_kebutuhan, true);
            if(isset($arr_skill)){
                $jumlah = count($arr_skill);
                for($i = 0; $i < count($arr_skill); $i++){
                    $data['tujuan'] = $arr_tujuan;
                    $data['kompetensi'] = $arr_kompetensi;
                    $data['skill'] = $arr_skill[$i];
                    if(!empty($arr_peserta)){
                        $data['level_peserta'] = $arr_peserta[$i];
                    }else{
                        $data['level_peserta'] = '';
                    }
                    if(!empty($arr_atasan)){
                        $data['level_atasan'] = $arr_atasan[$i];
                    }else{
                        $data['level_atasan'] = '';
                    }
                    if(!empty($arr_rata)){
                        $data['level_rata'] = $arr_rata[$i];
                    }else{
                        $data['level_rata'] = '';
                    }
                    if(!empty($arr_kebutuhan)){
                        $data['level_kebutuhan'] = $arr_kebutuhan[$i];
                    }else{
                        $data['level_kebutuhan'] = '';
                    }
                    $arr_data[] = $data;
                }
            }else{
                $jumlah = 0;
                $data['tujuan'] = '-';
                $data['kompetensi'] = '-';
                $data['skill'] = '-';
                $data['level_peserta'] = '-';
                $data['level_atasan'] = '-';
                $data['level_rata'] = '-';
                $data['level_kebutuhan'] = '-';
                $arr_data[] = $data;
            }
            //ttd peserta
            $qr_1 = Qrcodefpkt::where('id_fpkt', $fpkt->id)->where('type', 1)->first();
            if(!empty($qr_1)){
                $peserta_qr = $qr_1->qr;
                $peserta_fpkt_id = $qr_1->id_fpkt;
                $link_qr_peserta = route('public.training.qrcode.fpkp.peserta', ['code' => $peserta_qr, 'id' => $peserta_fpkt_id]);
            }else{
                $peserta_qr = null;
                $peserta_fpkt_id = null;
                $link_qr_peserta = '';
            }
            //ttd atasan
            $qr_2 = Qrcodefpkt::where('id_fpkt', $fpkt->id)->where('type', 2)->first();
            if(!empty($qr_2)){
                $atasan_qr = $qr_2->qr;
                $atasan_fpkt_id = $qr_2->id_fpkt;
                $link_qr_atasan = route('public.training.qrcode.fpkp.atasan', ['code' => $atasan_qr, 'id' => $atasan_fpkt_id]);
            }else{
                $atasan_qr = null;
                $atasan_fpkt_id = null;
                $link_qr_atasan = '';
            }
            //ttd dept head
            $qr_3 = Qrcodefpkt::where('id_fpkt', $fpkt->id)->where('type', 3)->first();
            if(!empty($qr_3)){
                $dept_qr = $qr_3->qr;
                $dept_fpkt_id = $qr_3->id_fpkt;
                $link_qr_dept = route('public.training.qrcode.fpkp.dept-head', ['code' => $dept_qr, 'id' => $dept_fpkt_id]);
            }else{
                $dept_qr = null;
                $dept_fpkt_id = null;
                $link_qr_dept = '';
            }
            //ttd mr. mizukami
            $qr_4 = Qrcodefpkt::where('id_fpkt', $fpkt->id)->where('type', 4)->first();
            if(!empty($qr_4)){
                $bod1_qr = $qr_4->qr;
                $bod1_fpkt_id = $qr_4->id_fpkt;
                $link_qr_bod1 = route('public.training.qrcode.fpkp.dept-head', ['code' => $bod1_qr, 'id' => $bod1_fpkt_id]);
            }else{
                $bod1_qr = null;
                $bod1_fpkt_id = null;
                $link_qr_bod1 = '';
            }
            //ttd mr. sakurai
            $qr_5 = Qrcodefpkt::where('id_fpkt', $fpkt->id)->where('type', 5)->first();
            if(!empty($qr_5)){
                $bod2_qr = $qr_5->qr;
                $bod2_fpkt_id = $qr_5->id_fpkt;
                $link_qr_bod2 = route('public.training.qrcode.fpkp.dept-head', ['code' => $bod2_qr, 'id' => $bod2_fpkt_id]);
            }else{
                $bod2_qr = null;
                $bod2_fpkt_id = null;
                $link_qr_bod2 = '';
            }
            //ttd hrd
            $qr_6 = Qrcodefpkt::where('id_fpkt', $fpkt->id)->where('type', 6)->first();
            if(!empty($qr_6)){
                $hrd_qr = $qr_6->qr;
                $hrd_fpkt_id = $qr_6->id_fpkt;
                $link_qr_hrd = route('public.training.qrcode.fpkp.dept-head', ['code' => $hrd_qr, 'id' => $hrd_fpkt_id]);
            }else{
                $hrd_qr = null;
                $hrd_fpkt_id = null;
                $link_qr_hrd = '';
            }
            //skor kebutuhan training
            if(!empty($arr_rata)){
                $sum_rata = array_sum($arr_rata);
            }else{
                $sum_rata = 0;
            }
            
            if($sum_rata > 0 && $jumlah > 0){
                $skor = floor($sum_rata/$jumlah);
            }else{
                $skor = 0;
            }

            $data = [
                'title' => 'Formulir Pelaksanaan Pelatihan',
                'fpkt' => $fpkt,
                'arr_fpkt' => $arr_data,
                'skor' => $skor,
                'jumlah' => $jumlah,
                'link_qr_peserta' => $link_qr_peserta,
                'link_qr_atasan' => $link_qr_atasan,
                'link_qr_dept' => $link_qr_dept,
                'link_qr_bod1' => $link_qr_bod1,
                'link_qr_bod2' => $link_qr_bod2,
                'link_qr_hrd' => $link_qr_hrd,
            ];
            $view = view('pages.profile.fpkt')->with(compact('data'));
            $html .= $view->render();
        }
        $pdf = PDF::set_option("isPhpEnabled", false);
        $pdf->loadHTML($html);
        $pdf->setPaper('a4', 'landscape');
        return $pdf->stream('FORMULIR PELAKSANAAN PELATIHAN.pdf');
    }

    public function qrcode_fpkt_pti($code,$id){
        $query = Qrcodefpkt::where('id_fkt', $id)->where('qr', $code)->first();
        return view('pages.hrd.training.pti.codeqr-fpkt', compact('query'));
    }

    public function pti_status(Request $request){
        if(!empty($request->kode)){
            $query = Trainingfpkt::where('id', decrypt($request->kode))->first();
            //jika departemen ke mr. mizukami
            if($query->pemohon->department->id == 3 || $query->pemohon->department->id == 4 || $query->pemohon->department->id == 5 || $query->pemohon->department->id == 6 || $query->pemohon->department->id == 8 || $query->pemohon->department->id == 9 || $query->pemohon->department->id == 10){
                $data['cek_dept'] = 'ada';
            }else{
                $data['cek_dept'] = 'kosong';
            }
            
            $data['judul_fpkt'] = $query->judul_fpkt;
            if(!empty($query->id_fkt)){
                $data['tujuan_usulan_fpkt'] = 'Program Pelatihan Tahunan';
            }else{
                $data['tujuan_usulan_fpkt'] = 'Program Pelatihan Insidentil';
            }
            $data['nama_pemohon_fpkt'] = $query->pemohon->fullname;
            $data['date_pemohon_fpkt'] = date('d M Y H:i', strtotime($query->date_pemohon));
            $data['nama_peserta_fpkt'] = $query->peserta->fullname;
            $data['date_pelaksanaan_fpkt'] = date('d M Y', strtotime($query->date_pelaksanaan));
            $data['id_status_fpkt'] = $query->status;
            $data['nama_status_fpkt'] = $query->training_status->name;
            
            //status ttd fpkt
            if(!empty($query->date_peserta)){
                $data['id_peserta_fpkt'] = $query->peserta->fullname;
                $data['date_peserta_fpkt'] = date('d M Y H:i', strtotime($query->date_peserta));
            }else{
                $data['id_peserta_fpkt'] = $query->peserta->fullname;
                $data['date_peserta_fpkt'] = null;
            }
            if(!empty($query->date_atasan)){
                $data['id_atasan_fpkt'] = $query->atasan->fullname;
                $data['date_atasan_fpkt'] = date('d M Y H:i', strtotime($query->date_atasan));
            }else{
                $data['id_atasan_fpkt'] = null;
                $data['date_atasan_fpkt'] = null;
            }
            if(!empty($query->date_dept_head)){
                $data['atasan_dept_fpkt'] = $query->atasan_dept->fullname;
                $data['date_atasan_dept_fpkt'] = date('d M Y H:i', strtotime($query->date_dept_head));
            }else{
                $data['atasan_dept_fpkt'] = null;
                $data['date_atasan_dept_fpkt'] = null;
            }
            if(!empty($query->date_hrd)){
                $data['verified_hrd_fpkt'] = $query->hrd->fullname;
                $data['date_verified_hrd_fpkt'] = date('d M Y H:i', strtotime($query->date_hrd));
            }else{
                $data['verified_hrd_fpkt'] = null;
                $data['date_verified_hrd_fpkt'] = null;
            }
            if(!empty($query->date_bod1)){
                $data['bod1_fpkt'] = $query->bod1->fullname;
                $data['date_bod1_fpkt'] = date('d M Y H:i', strtotime($query->date_bod1));
            }else{
                $data['bod1_fpkt'] = null;
                $data['date_bod1_fpkt'] = null;
            }
            if(!empty($query->date_bod2)){
                $data['bod2_fpkt'] = $query->bod2->fullname;
                $data['date_bod2_fpkt'] = date('d M Y H:i', strtotime($query->date_bod2));
            }else{
                $data['bod2_fpkt'] = null;
                $data['date_bod2_fpkt'] = null;
            }
            //catatan fkp
            $query_ctt = Logcatatantraining::where('id_fpkt', $query->id)->get();
            if($query_ctt->isNotEmpty()){
                foreach($query_ctt as $qry_ctt){
                    $dt['id_user'] = $qry_ctt->employee->fullname;
                    $dt['action'] = $qry_ctt->action;
                    $dt['catatan'] = $qry_ctt->catatan;
                    $dt['tgl_ctt'] = date('d M Y H:i', strtotime($qry_ctt->created_at));
                    $dt_all[] = $dt;
                }
                $data['ctt_fpkt'] = $dt_all;
            }else{
                $data['ctt_fpkt'] = null;
            }
        }
        
        return response()->json($data);
    }

    //finished
    public function pti_finished(Request $request){
        $query = Trainingfkt::where('tipe','pti')->where('status', 6)->get()->unique('kode_judul');
        if($query->isNotEmpty()){
            foreach($query as $qry){
                $index = $qry->kode_judul;
                $document[$index] = array();
                $document[$index]['judul'] = $qry->judul;
                $document[$index]['kode'] = $qry->kode;
                $document[$index]['kode_judul'] = $qry->kode_judul;
                $document[$index]['tahun_usulan'] = $qry->tahun_usulan;
                $document[$index]['status'] = $qry->status;
                if(empty($qry->id_vendor) && empty($qry->nama_vendor)){
                    $document[$index]['nama_vendor'] = null;
                }else{
                    $document[$index]['nama_vendor'] = $qry->vendor->nama ?? $qry->nama_vendor;
                }
            }
        }else{
            $document = array(); 
        }
        if($request->ajax()){
            return DataTables::of($document)
                ->addColumn('status', function($document){
                    if($document['status'] == 6) return '<a href="#" <span class="badge text-bg-secondary"><i class="ri-checkbox-circle-line align-bottom"></i> Approved</span></a>';
                    if($document['status'] == 7) return '<a href="#" <span class="badge text-bg-secondary"><i class="ri-checkbox-circle-line align-bottom"></i> Approved</span></a>';
                })
                ->addColumn('action', function ($document) {
                    $button = '<a href="#" data-bs-target="#secondmodal" data-bs-toggle="modal" data-toggle="tooltip" title="Schedule" class="btn btn-info btn-sm btn-schedule"><i class="ri-calendar-todo-line"></i></a><input type="hidden" id="btn-kode" value="'.$document['kode'].'"><input type="hidden" id="btn-judul" value="'.$document['judul'].'"><input type="hidden" id="request-vendor" value="'.$document['nama_vendor'].'">';               
                    return $button;
                })
                ->addColumn('peserta', function($document){
                    $arr_fkt = Trainingfkt::where('kode', $document['kode'])->where('judul', $document['judul'])->get();
                    if($arr_fkt->isNotEmpty()){
                        $peserta = '<div class="col-lg-12"><table class="table table-bordered" style="table-layout: fixed; width:100%;">';
                        $peserta .= '
                            <thead>
                                <tr>
                                    <th colspan="4" style="text-align: center;">Peserta Program Training</th>
                                </tr>
                                <tr>
                                    <th style="text-align: center;">Peserta</th>
                                    <th style="text-align: center;">Pelatihan</th>
                                    <th style="text-align: center;">Sifat Pelatihan</th>
                                    <th style="text-align: center;">Alasan</th>
                                    <th style="text-align: center;">#</th>
                                </tr>
                            </thead>
                            ';
                        $peserta .= '<tbody>';
                        foreach($arr_fkt as $fkt){
                            $button = '<div class="dropdown d-inline-block">';
                            $button .= '<button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button>';
                            $button .= '<ul class="dropdown-menu dropdown-menu-end">';
                                    $button .= '<li><a href="'.route('training.pti.fkt.pdf', encrypt($document['kode_judul'])).'" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FKT</a></li>';
                                    $button .= '<li><a href="'.route('training.pti.fpkt.pdf', encrypt($document['kode_judul'])).'" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FPKT</a></li>';
                                $button .= '</ul>';
                            $button .= '</div>';
                            $peserta .= '<tr>';                  
                            $peserta .= '<td>'.$fkt->peserta->fullname.'</td>';    
                            $peserta .= '<td>'.$fkt->judul.'</td>';    
                            $peserta .= '<td>'.$fkt->sifat.'</td>';    
                            $peserta .= '<td>'.$fkt->alasan.'</td>';
                            $peserta .= '<td>'.$button.'</td>';
                            $peserta .= '</tr>';
                        } 
                        $peserta .= '</tbody></table></div>';
                    }else{
                        $peserta = '';
                    }
                    return $peserta;
                })
                ->rawColumns(['status','action','peserta'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.training.pti.index');
    }

    public function pti_schedule_store(Request $request){
        $user = auth()->user();
        $arr_fkt = Trainingfkt::where('kode', $request->kode_fkt)->where('judul', $request->judul_fkt)->get();
        if($request->id_vendor != 'other'){
            $vendor = $request->id_vendor;
        }else{
            $insert_vendor = Vendor::insert([
                'nama' => $request->nama_vendor,
                'alamat' => '-',
                'tipe' => 'training',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);

            $query_vendor = Vendor::where('nama', $request->nama_vendor)
                ->where('tipe', 'training')
                ->first();
            $vendor = $query_vendor->id;
        }
        foreach($arr_fkt as $fkt){
            $insert[] = [
                'id_employee' => $fkt->id_peserta,
                'judul' => $fkt->judul,
                'detail' => $fkt->judul,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'id_vendor' => $vendor,
                'lokasi' => $request->lokasi,
                'biaya' => str_replace(".","",$request->biaya),
                'exp_date' => null,
                'id_fkt' => $fkt->id,
                'kode_fkt' => $fkt->kode_judul,
                'status' => 13,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];

            //notification peserta training record
            if(!empty($fkt->peserta->email)){
                $qry_user = User::where('employee_id', $fkt->id_peserta)->first();
                if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                    $details = [
                        'greeting' => 'Hi '.$fkt->peserta->fullname,
                        'subject' => 'Pelaksanaan Training',
                        'body' => 'Ingin Menginformasikan bahwa training anda dengan topik "'.$fkt->judul.'" sudah dijadwalkan',
                        'actionText' => 'Silahkan Login',
                        'actionURL' => url('/employee/training'),
                        'thanks' => 'Terimakasih atas perhatiannya!!'
                    ];
                }else{
                    $details = [
                        'greeting' => 'Hi '.$fkt->peserta->fullname,
                        'subject' => 'Pelaksanaan Training',
                        'body' => 'Ingin Menginformasikan bahwa training anda dengan topik "'.$fkt->judul.'" sudah dijadwalkan',
                        'actionText' => 'Silahkan Login',
                        'actionURL' => url('/mytraining'),
                        'thanks' => 'Terimakasih atas perhatiannya!!'
                    ];
                }
                //send mail
                $qry_user->notify(new AccountNotification($details));
            }

            //insert log user activity
            $insert_log = new Log;
            $insert_log->user_id = $user->id;
            $insert_log->ip_address = $request->ip();
            $insert_log->action = 'insert';
            $insert_log->description = 'Create schedule training peserta "'.$fkt->peserta->fullname.'"';
            $insert_log->save();
        }
        $post = Trainingrecord::insert($insert);
        $update = Trainingfkt::where('kode', $request->kode_fkt)->where('judul', $request->judul_fkt)->update([
            'id_vendor' => $vendor,
            'status' => 7
        ]);

        return redirect(route('training.pti.index'))->with('scheduled','open tab')->with('status','Schedule Training has been created');
    }

    public function pti_schedule(Request $request){
        $query = Trainingfkt::where('tipe','pti')->where('status', 7)->get()->unique('kode_judul');
        if($query->isNotEmpty()){
            foreach($query as $qry){
                $jumlah = Trainingfkt::where('kode', $qry->kode)->where('judul', $qry->judul)->count();
                $training = Trainingrecord::where('id_fkt', $qry->id)->first();
                $index = $qry->kode_judul;
                $data[$index] = array();
                $data[$index]['judul'] = $qry->judul;
                $data[$index]['kode'] = $qry->kode;
                $data[$index]['jml_peserta'] = $jumlah;
                $data[$index]['tgl_mulai'] = $training->start_date;
                $data[$index]['tgl_akhir'] = $training->end_date;
                $data[$index]['vendor'] = $qry->vendor->nama;
            }
        }else{
            $data = array(); 
        }
        if($request->ajax()){
            return DataTables::of($data)
                ->addColumn('action', function ($data) {
                    $button = '-';               
                    return $button;
                })
                ->addColumn('peserta', function($data){
                    $arr_fkt = Trainingfkt::where('kode', $data['kode'])->where('judul', $data['judul'])->get();
                    if($arr_fkt->isNotEmpty()){
                        $peserta = '<div class="col-lg-12"><table class="table table-bordered" style="table-layout: fixed; width:100%;">';
                        $peserta .= '
                            <thead>
                                <tr>
                                    <th colspan="4" style="text-align: center;">Peserta Program Training</th>
                                </tr>
                                <tr>
                                    <th style="text-align: center;">Peserta</th>
                                    <th style="text-align: center;">Pelatihan</th>
                                    <th style="text-align: center;">Sifat Pelatihan</th>
                                    <th style="text-align: center;">Alasan</th>
                                </tr>
                            </thead>
                            ';
                        $peserta .= '<tbody>';
                        foreach($arr_fkt as $fkt){
                            $peserta .= '<tr>';                  
                            $peserta .= '<td>'.$fkt->peserta->fullname.'</td>';    
                            $peserta .= '<td>'.$fkt->judul.'</td>';    
                            $peserta .= '<td>'.$fkt->sifat.'</td>';    
                            $peserta .= '<td>'.$fkt->alasan.'</td>';
                            $peserta .= '</tr>';
                        } 
                        $peserta .= '</tbody></table></div>';
                    }else{
                        $peserta = '';
                    }
                    return $peserta;
                })
                ->rawColumns(['action','peserta'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.training.pti.index');
    }
    //End pti training
    //start employee view training
    //data training
    public function emp_index(Request $request){
        $user = auth()->user();
        //cek notif
        $jml_record = Trainingrecord::where('id_employee', $user->employee_id)
            ->where('status',13)->count();
        //cek notif approve laporan
        $jml_gm = Trainingrecord::where('ttd_general_manager', $user->employee_id)
            ->whereNull('tgl_ttd_general_manager')
            ->where('status',13)->count();
        $jml_manager = Trainingrecord::where('ttd_manager', $user->employee_id)
            ->whereNull('tgl_ttd_manager')
            ->where('status',13)->count();
        $jml_atasan = Trainingrecord::where('ttd_atasan', $user->employee_id)
            ->whereNull('tgl_ttd_atasan')
            ->where('status',13)->count();
        $jml_hrd_ga_gm = Trainingrecord::where('ttd_hrd_ga_gm', $user->employee_id)
            ->whereNull('tgl_ttd_hrd_ga_gm')
            ->where('status',13)->count();
        $jml_approve = $jml_gm+$jml_manager+$jml_atasan+$jml_hrd_ga_gm;

        //notif ptt
        $jml_approve_checker_ptt = Trainingfkt::where('id_checker', $user->employee_id)
            ->whereNull('date_checker')->where('status',2)->get()->unique('kode')->count();
        $count_jml_approve_ptt = $jml_approve_checker_ptt;
        //notif pti
        // $jml_approve_checker_pti = Trainingfkt::where('id_checker', $user->employee_id)
        //     ->where('tipe', 'pti')->whereNotNull('date_pemohon')
        //     ->whereNull('date_checker')->where('status',3)->get()->unique('judul')->count();
        // $jml_approve_penilai_pti = Trainingfkt::where('id_penilai', $user->employee_id)
        //     ->where('tipe','pti')->whereNotNull('date_peserta')
        //     ->whereNull('date_penilai')->where('status',2)->get()->unique('judul')->count();
        $count_jml_approve_pti = 0;

        $query = Trainingrecord::where('id_employee', $user->employee_id)->get();
        if($query->isNotEmpty()){
            foreach($query as $qry){
                $index = $qry->id;
                $data[$index] = array();
                $data[$index]['id'] = $qry->id;
                $data[$index]['kode_fkt'] = $qry->kode_fkt;
                $data[$index]['judul'] = $qry->judul;
                if(!empty($qry->start_date)){
                    $data[$index]['start_date'] = date('d M Y', strtotime($qry->start_date));
                }else{
                    $data[$index]['start_date'] = '-';
                }
                if(!empty($qry->end_date)){
                    $data[$index]['end_date'] = date('d M Y', strtotime($qry->end_date));
                }else{
                    $data[$index]['end_date'] = '-';
                }
                $data[$index]['lokasi'] = $qry->lokasi ?? '-';
                $data[$index]['biaya'] = "Rp ".number_format($qry->biaya,2);
                $data[$index]['status'] = $qry->training_status->id;
                $data[$index]['nama_status'] = $qry->training_status->name;
                $data[$index]['tgl_laporan'] = $qry->tgl_laporan;
                $data[$index]['sertifikat'] = $qry->sertifikat;                
                $data[$index]['materi'] = $qry->materi;                
                $data[$index]['lokasi'] = $qry->lokasi;                
            }
        }else{
            $data = array();
        }
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('status', function($data){
                    if($data['status'] == 13) return '<a href="#" <span class="badge text-bg-warning"><i class="ri-time-line align-bottom"></i> '.$data['nama_status'].'</span></a>';
                    if($data['status'] == 14) return '<a href="#" <span class="badge text-bg-success"><i class="ri-checkbox-circle-line align-bottom"></i> '.$data['nama_status'].'</span></a>';
                })
                ->addColumn('status_laporan', function($data){
                    if(!empty($data['tgl_laporan'])){                       
                        //button canvas
                        $laporan = '<button class="btn btn-light btn-sm" type="button" data-id="'.encrypt($data['id']).'" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"><i class="ri-survey-line me-1 align-middle"></i> Status <i class="ri-arrow-right-s-line align-middle ms-1"></i></button>';
                        return  $laporan;
                    }else{
                        return '-';
                    }
                })
                ->addColumn('action', function ($data) {
                    if(!empty($data['tgl_laporan'])){
                        if($data['status'] == 13){
                            if(!empty($data['sertifikat'])){
                                $list_sertifikat = '<li><a href="'.route('profile.training.sertifikat',encrypt($data['id'])).'" target="_blank" class="dropdown-item"><i class="ri-file-download-line align-bottom me-1 text-muted"></i> Sertifikat</a></li>';            
                            }else{
                                $list_sertifikat = '';
                            }
                            if(!empty($data['materi'])){
                                $list_materi = '<li><a href="'.route('profile.training.materi',encrypt($data['id'])).'" target="_blank" class="dropdown-item"><i class="ri-file-download-line align-bottom me-1 text-muted"></i> Materi</a></li>';            
                            }else{
                                $list_materi = '';
                            }
                            $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_sertifikat.$list_materi.'</ul></div>';
                        }else{
                            if(!empty($data['sertifikat'])){
                                $list_sertifikat = '<li><a href="'.route('profile.training.sertifikat',encrypt($data['id'])).'" target="_blank" class="dropdown-item"><i class="ri-file-download-line align-bottom me-1 text-muted"></i> Sertifikat</a></li>';            
                            }else{
                                $list_sertifikat = '';
                            }
                            if(!empty($data['materi'])){
                                $list_materi = '<li><a href="'.route('profile.training.materi',encrypt($data['id'])).'" target="_blank" class="dropdown-item"><i class="ri-file-download-line align-bottom me-1 text-muted"></i> Materi</a></li>';            
                            }else{
                                $list_materi = '';
                            }
                            $list_print = '<li><a href="'.route('profile.training.laporan.pdf',encrypt($data['id'])).'" target="_blank" class="dropdown-item"><i class="ri-file-pdf-line align-bottom me-1 text-muted"></i> PDF Laporan</a></li>';            

                            $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_sertifikat.$list_materi.$list_print.'</ul></div>';
                        }
                    }else{
                        if($data['status'] == 13){
                            if($data['lokasi'] === null || $data['lokasi'] === ''){
                                $list_jadwal = '<li><a href="javascript:void(0);" data-id="'.encrypt($data['id']).'" data-bs-toggle="modal" data-bs-target="#modal-jadwal" class="dropdown-item jadwal-btn"><i class="ri-calendar-2-line align-bottom me-1 text-muted"></i> Jadwal Pelaksanaan</a></li>';
                            }else{
                                $list_jadwal = '';
                            }
                            $list_laporan = '<li><a href="'.route('training.emp.create.laporan',encrypt($data['id'])).'" class="dropdown-item"><i class="ri-contacts-book-2-line align-bottom me-1 text-muted"></i> Buat Laporan</a></li>';
                            $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_jadwal.$list_laporan.'</ul></div>';
                        }else{
                            $button = '-';                  
                        }
                    }
                    return $button;
                })
                ->rawColumns(['action','status','status_laporan'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.employee.training.index', compact('user','jml_record','jml_approve','count_jml_approve_ptt','count_jml_approve_pti'));
    }

    public function emp_jadwal_store(Request $request){
        DB::beginTransaction();
        try {
            $id_record = decrypt($request->id_record);
            $post = Trainingrecord::where('id', $id_record)->update([
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'lokasi' => $request->lokasi
            ]);

            $query = Trainingrecord::where('id', $id_record)->first();
            //update log user activity
            $user = auth()->user();
            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'update';
            $insert->description = 'Modify Record Pelatihan '.'"'.$query->judul.'"';
            $insert->save();

            DB::commit();
        }catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    public function emp_index_laporan(Request $request){
        $user = auth()->user();
        $query = Trainingrecord::where(function ($query) use ($user){
            $query->where('ttd_presiden', $user->employee_id)
                ->orWhere('ttd_direktur', $user->employee_id)
                ->orWhere('ttd_general_manager', $user->employee_id)
                ->orWhere('ttd_manager', $user->employee_id)
                ->orWhere('ttd_atasan', $user->employee_id)
                ->orWhere('ttd_hrd_ga_gm', $user->employee_id)
                ->orWhere('ttd_pic', $user->employee_id);
        })->where('status',13)->get();
        if($query->isNotEmpty()){
            foreach($query as $qry){
                //ttd presiden
                if($qry->ttd_presiden == $user->employee_id){
                    if($qry->tgl_ttd_presiden == null){
                        $index = $qry->id;
                        $data[$index] = array();
                        $data[$index]['id'] = $qry->id;
                        $data[$index]['pemohon'] = $qry->employee->fullname;
                        $data[$index]['judul'] = $qry->judul;
                        $data[$index]['tgl_laporan'] = $qry->tgl_laporan;
                    }else{
                        $data = array();
                    }
                }
                //ttd direktur
                if($qry->ttd_direktur == $user->employee_id){
                    if($qry->tgl_ttd_direktur == null){
                        if($qry->ttd_general_manager == $user->employee_id){
                            $index = $qry->id;
                            $data[$index] = array();
                            $data[$index]['id'] = $qry->id;
                            $data[$index]['pemohon'] = $qry->employee->fullname;
                            $data[$index]['judul'] = $qry->judul;
                            $data[$index]['tgl_laporan'] = $qry->tgl_laporan;
                        }else{
                            if(!empty($qry->tgl_ttd_general_manager)){
                                $index = $qry->id;
                                $data[$index] = array();
                                $data[$index]['id'] = $qry->id;
                                $data[$index]['pemohon'] = $qry->employee->fullname;
                                $data[$index]['judul'] = $qry->judul;
                                $data[$index]['tgl_laporan'] = $qry->tgl_laporan;
                            }else{
                                $data = array();
                            }
                        }
                    }else{
                        $data = array();
                    }
                }
                //ttd general manager
                if($qry->ttd_general_manager == $user->employee_id){
                    if($qry->tgl_ttd_general_manager == null){
                        if($qry->ttd_manager == $user->employee_id){
                            $index = $qry->id;
                            $data[$index] = array();
                            $data[$index]['id'] = $qry->id;
                            $data[$index]['pemohon'] = $qry->employee->fullname;
                            $data[$index]['judul'] = $qry->judul;
                            $data[$index]['tgl_laporan'] = $qry->tgl_laporan;
                        }else{
                            if(!empty($qry->tgl_ttd_manager)){
                                $index = $qry->id;
                                $data[$index] = array();
                                $data[$index]['id'] = $qry->id;
                                $data[$index]['pemohon'] = $qry->employee->fullname;
                                $data[$index]['judul'] = $qry->judul;
                                $data[$index]['tgl_laporan'] = $qry->tgl_laporan;
                            }else{
                                $data = array();
                            }
                        }
                    }else{
                        $data = array();
                    }
                }                
                //ttd manager
                if($qry->ttd_manager == $user->employee_id){
                    if($qry->tgl_ttd_manager == null){
                        if($qry->ttd_atasan == $user->employee_id){
                            $index = $qry->id;
                            $data[$index] = array();
                            $data[$index]['id'] = $qry->id;
                            $data[$index]['pemohon'] = $qry->employee->fullname;
                            $data[$index]['judul'] = $qry->judul;
                            $data[$index]['tgl_laporan'] = $qry->tgl_laporan;
                        }else{
                            if(!empty($qry->tgl_ttd_atasan)){
                                $index = $qry->id;
                                $data[$index] = array();
                                $data[$index]['id'] = $qry->id;
                                $data[$index]['pemohon'] = $qry->employee->fullname;
                                $data[$index]['judul'] = $qry->judul;
                                $data[$index]['tgl_laporan'] = $qry->tgl_laporan;
                            }else{
                                $data = array();
                            }
                        }
                    }else{
                        $data = array();
                    }
                }

                //ttd atasan langsung
                if($qry->ttd_atasan == $user->employee_id){
                    if($qry->tgl_ttd_atasan == null){
                        $index = $qry->id;
                        $data[$index] = array();
                        $data[$index]['id'] = $qry->id;
                        $data[$index]['pemohon'] = $qry->employee->fullname;
                        $data[$index]['judul'] = $qry->judul;
                        $data[$index]['tgl_laporan'] = $qry->tgl_laporan;
                    }else{
                        $data = array();
                    }
                }
                
                //ttd hrd ga gm 
                if($qry->ttd_hrd_ga_gm == $user->employee_id){
                    if($qry->tgl_ttd_hrd_ga_gm == null){
                        if($qry->ttd_pic == $user->employee_id){
                            $index = $qry->id;
                            $data[$index] = array();
                            $data[$index]['id'] = $qry->id;
                            $data[$index]['pemohon'] = $qry->employee->fullname;
                            $data[$index]['judul'] = $qry->judul;
                            $data[$index]['tgl_laporan'] = $qry->tgl_laporan;
                        }else{
                            if(!empty($qry->tgl_ttd_pic)){
                                $index = $qry->id;
                                $data[$index] = array();
                                $data[$index]['id'] = $qry->id;
                                $data[$index]['pemohon'] = $qry->employee->fullname;
                                $data[$index]['judul'] = $qry->judul;
                                $data[$index]['tgl_laporan'] = $qry->tgl_laporan;
                            }else{
                                $data = array();
                            }
                        }
                    }else{
                        $data = array();
                    }
                }
                //ttd pic
                if($qry->ttd_pic == $user->employee_id){
                    if($qry->tgl_ttd_pic == null){
                        if($qry->ttd_presiden == $user->employee_id){
                            $index = $qry->id;
                            $data[$index] = array();
                            $data[$index]['id'] = $qry->id;
                            $data[$index]['pemohon'] = $qry->employee->fullname;
                            $data[$index]['judul'] = $qry->judul;
                            $data[$index]['tgl_laporan'] = $qry->tgl_laporan;
                        }else{
                            if(!empty($qry->tgl_ttd_presiden)){
                                $index = $qry->id;
                                $data[$index] = array();
                                $data[$index]['id'] = $qry->id;
                                $data[$index]['pemohon'] = $qry->employee->fullname;
                                $data[$index]['judul'] = $qry->judul;
                                $data[$index]['tgl_laporan'] = $qry->tgl_laporan;
                            }else{
                                $data = array();
                            }
                        }
                    }else{
                        $data = array();
                    }
                }
            }
        }else{
            $data = array();
        }
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('action', function ($data) {
                    $button = '<a href="'.route('training.emp.approval.laporan',encrypt($data['id'])).'" class="btn btn-secondary btn-sm waves-effect waves-light"><i class="ri-checkbox-circle-line align-bottom me-1"></i> Approve</a>';                  
                    return $button;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
    }
    public function emp_approval_laporan($id){
        $user = auth()->user();
        $query = Trainingrecord::where('id', decrypt($id))->first();
        return view('pages.employee.training.laporan.approval', compact('user','query'));
    }
    public function emp_store_approval_laporan(Request $request){
        $user = auth()->user();
        $record = Trainingrecord::find($request->id_record);
        //ttd presiden
        if($user->employee_id == $record->ttd_presiden){
            $post = Trainingrecord::where('id', $record->id)->where('ttd_presiden', $user->employee_id)->update([
                'tgl_ttd_presiden' => Carbon::now()
            ]);
            //insert log user activity
            $insert_log = new Log;
            $insert_log->user_id = $user->id;
            $insert_log->ip_address = $request->ip();
            $insert_log->action = 'approved';
            $insert_log->description = 'Approve laporan training dengan nama pemohon'.'"'.$record->employee->fullname.'" tanggal laporan "'.$record->tgl_laporan.'" oleh "President Director"';
            $insert_log->save();

            $new_record = Trainingrecord::where('id', $request->id_record)->first();
            if(!empty($new_record->tgl_ttd_presiden) && !empty($new_record->tgl_ttd_direktur) && !empty($new_record->tgl_ttd_general_manager) && !empty($new_record->tgl_ttd_manager) && !empty($new_record->tgl_ttd_atasan) && !empty($new_record->tgl_ttd_hrd_ga_gm) && !empty($new_record->tgl_ttd_pic)){
                $post2 = Trainingrecord::where('id', $new_record->id)->where('ttd_presiden', $user->employee_id)->update([
                    'status' => 14
                ]);
            }
            if(!empty($new_record->tgl_ttd_presiden) && empty($new_record->tgl_ttd_direktur) && !empty($new_record->tgl_ttd_general_manager) && !empty($new_record->tgl_ttd_manager) && !empty($new_record->tgl_ttd_atasan) && !empty($new_record->tgl_ttd_hrd_ga_gm) && !empty($new_record->tgl_ttd_pic)){
                $post2 = Trainingrecord::where('id', $new_record->id)->where('ttd_presiden', $user->employee_id)->update([
                    'status' => 14
                ]);
            }
            if($new_record->ttd_pic != $user->employee_id){
                //notification pic hrd
                if(empty($new_record->tgl_ttd_pic)){
                    if(!empty($new_record->pic_ttd->email)){
                        $qry_user = User::where('employee_id', $new_record->ttd_pic)->first();
                        $details = [
                            'greeting' => 'Hi '.$new_record->pic_ttd->fullname,
                            'subject' => 'Laporan Pelaksanaan Training',
                            'body' => 'Ingin Menginformasikan bahwa ada laporan pelaksanaan training topik "'.$new_record->judul.'" yang membutuhkan approval anda',
                            'actionText' => 'Silahkan Login',
                            'actionURL' => url('/hrd/training/laporan/'.encrypt($new_record->id).'/approval'),
                            'thanks' => 'Terimakasih atas perhatiannya!!'
                        ];
                        //send mail
                        $qry_user->notify(new AccountNotification($details));
                    }
                }
            }
        }
        //ttd direktur
        if($user->employee_id == $record->ttd_direktur){
            $post = Trainingrecord::where('id', $record->id)->where('ttd_direktur', $user->employee_id)->update([
                'tgl_ttd_direktur' => Carbon::now()
            ]);
            //insert log user activity
            $insert_log = new Log;
            $insert_log->user_id = $user->id;
            $insert_log->ip_address = $request->ip();
            $insert_log->action = 'approved';
            $insert_log->description = 'Approve laporan training dengan nama pemohon'.'"'.$record->employee->fullname.'" tanggal laporan "'.$record->tgl_laporan.'" oleh "Production Director / Jr. Director"';
            $insert_log->save();

            $new_record = Trainingrecord::where('id', $request->id_record)->first();
            if(!empty($new_record->tgl_ttd_presiden) && !empty($new_record->tgl_ttd_direktur) && !empty($new_record->tgl_ttd_general_manager) && !empty($new_record->tgl_ttd_manager) && !empty($new_record->tgl_ttd_atasan) && !empty($new_record->tgl_ttd_hrd_ga_gm) && !empty($new_record->tgl_ttd_pic)){
                $post2 = Trainingrecord::where('id', $new_record->id)->where('ttd_direktur', $user->employee_id)->update([
                    'status' => 14
                ]);
            }
            if($new_record->ttd_presiden != $user->employee_id){
                //notification presiden
                if(empty($new_record->tgl_ttd_presiden)){
                    if(!empty($new_record->presiden_ttd->email)){
                        $qry_user = User::where('employee_id', $new_record->ttd_presiden)->first();
                        $details = [
                            'greeting' => 'Hi '.$new_record->presiden_ttd->fullname,
                            'subject' => 'Laporan Pelaksanaan Training',
                            'body' => 'Ingin Menginformasikan bahwa ada laporan pelaksanaan training topik "'.$new_record->judul.'" yang membutuhkan approval anda',
                            'actionText' => 'Silahkan Login',
                            'actionURL' => url('/hrd/training/laporan/'.encrypt($new_record->id).'/approval'),
                            'thanks' => 'Terimakasih atas perhatiannya!!'
                        ];
                        //send mail
                        $qry_user->notify(new AccountNotification($details));
                    }
                }
            }
        }
        //ttd general manager
        if($user->employee_id == $record->ttd_general_manager){
            $post = Trainingrecord::where('id', $record->id)->where('ttd_general_manager', $user->employee_id)->update([
                'tgl_ttd_general_manager' => Carbon::now()
            ]);
            //insert log user activity
            $insert_log = new Log;
            $insert_log->user_id = $user->id;
            $insert_log->ip_address = $request->ip();
            $insert_log->action = 'approved';
            $insert_log->description = 'Approve laporan training dengan nama pemohon'.'"'.$record->employee->fullname.'" tanggal laporan "'.$record->tgl_laporan.'" oleh "General Manager"';
            $insert_log->save();

            $new_record = Trainingrecord::where('id', $request->id_record)->first();
            if(!empty($new_record->tgl_ttd_presiden) && !empty($new_record->tgl_ttd_direktur) && !empty($new_record->tgl_ttd_general_manager) && !empty($new_record->tgl_ttd_manager) && !empty($new_record->tgl_ttd_atasan) && !empty($new_record->tgl_ttd_hrd_ga_gm) && !empty($new_record->tgl_ttd_pic)){
                $post2 = Trainingrecord::where('id', $new_record->id)->where('ttd_general_manager', $user->employee_id)->update([
                    'status' => 14
                ]);
            }
            if($new_record->ttd_direktur != $user->employee_id){
                //notification direktur
                if(empty($new_record->tgl_ttd_direktur)){
                    if(!empty($new_record->direktur_ttd->email)){
                        $qry_user = User::where('employee_id', $new_record->ttd_direktur)->first();
                        $details = [
                            'greeting' => 'Hi '.$new_record->direktur_ttd->fullname,
                            'subject' => 'Laporan Pelaksanaan Training',
                            'body' => 'Ingin Menginformasikan bahwa ada laporan pelaksanaan training topik "'.$new_record->judul.'" yang membutuhkan approval anda',
                            'actionText' => 'Silahkan Login',
                            'actionURL' => url('/hrd/training/laporan/'.encrypt($new_record->id).'/approval'),
                            'thanks' => 'Terimakasih atas perhatiannya!!'
                        ];
                        //send mail
                        $qry_user->notify(new AccountNotification($details));
                    }
                }
            }
        }
        //ttd manager
        if($user->employee_id == $record->ttd_manager){
            $post = Trainingrecord::where('id', $record->id)->where('ttd_manager', $user->employee_id)->update([
                'tgl_ttd_manager' => Carbon::now()
            ]);
            //insert log user activity
            $insert_log = new Log;
            $insert_log->user_id = $user->id;
            $insert_log->ip_address = $request->ip();
            $insert_log->action = 'approved';
            $insert_log->description = 'Approve laporan training dengan nama pemohon'.'"'.$record->employee->fullname.'" tanggal laporan "'.$record->tgl_laporan.'" oleh "Manager"';
            $insert_log->save();

            $new_record = Trainingrecord::where('id', $request->id_record)->first();
            if(!empty($new_record->tgl_ttd_presiden) && !empty($new_record->tgl_ttd_direktur) && !empty($new_record->tgl_ttd_general_manager) && !empty($new_record->tgl_ttd_manager) && !empty($new_record->tgl_ttd_atasan) && !empty($new_record->tgl_ttd_hrd_ga_gm) && !empty($new_record->tgl_ttd_pic)){
                $post2 = Trainingrecord::where('id', $new_record->id)->where('ttd_manager', $user->employee_id)->update([
                    'status' => 14
                ]);
            }
            if($new_record->ttd_general_manager != $user->employee_id){
                //notification general manager
                if(empty($new_record->tgl_ttd_general_manager)){
                    if(!empty($new_record->gm_ttd->email)){
                        $qry_user = User::where('employee_id', $new_record->ttd_general_manager)->first();
                        if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                            $details = [
                                'greeting' => 'Hi '.$new_record->gm_ttd->fullname,
                                'subject' => 'Laporan Pelaksanaan Training',
                                'body' => 'Ingin Menginformasikan bahwa ada laporan pelaksanaan training topik "'.$new_record->judul.'" yang membutuhkan approval anda',
                                'actionText' => 'Silahkan Login',
                                'actionURL' => url('/employee/training/approval/'.encrypt($new_record->id).'/laporan'),
                                'thanks' => 'Terimakasih atas perhatiannya!!'
                            ];
                        }else{
                            $details = [
                                'greeting' => 'Hi '.$new_record->gm_ttd->fullname,
                                'subject' => 'Laporan Pelaksanaan Training',
                                'body' => 'Ingin Menginformasikan bahwa ada laporan pelaksanaan training topik "'.$new_record->judul.'" yang membutuhkan approval anda',
                                'actionText' => 'Silahkan Login',
                                'actionURL' => url('/mytraining/approval/'.encrypt($new_record->id).'/form'),
                                'thanks' => 'Terimakasih atas perhatiannya!!'
                            ];
                        }
                        //send mail
                        $qry_user->notify(new AccountNotification($details));
                    }
                }
            }
        }
        //ttd atasan
        if($user->employee_id == $record->ttd_atasan){
            $post = Trainingrecord::where('id', $record->id)->where('ttd_atasan', $user->employee_id)->update([
                'hasil' => $request->hasil,
                'tgl_ttd_atasan' => Carbon::now()
            ]);
            //insert log user activity
            $insert_log = new Log;
            $insert_log->user_id = $user->id;
            $insert_log->ip_address = $request->ip();
            $insert_log->action = 'approved';
            $insert_log->description = 'Approve laporan training dengan nama pemohon'.'"'.$record->employee->fullname.'" tanggal laporan "'.$record->tgl_laporan.'" oleh "Atasan Langsung"';
            $insert_log->save();

            $new_record = Trainingrecord::where('id', $request->id_record)->first();
            if(!empty($new_record->tgl_ttd_presiden) && !empty($new_record->tgl_ttd_direktur) && !empty($new_record->tgl_ttd_general_manager) && !empty($new_record->tgl_ttd_manager) && !empty($new_record->tgl_ttd_atasan) && !empty($new_record->tgl_ttd_hrd_ga_gm) && !empty($new_record->tgl_ttd_pic)){
                $post2 = Trainingrecord::where('id', $new_record->id)->where('ttd_atasan', $user->employee_id)->update([
                    'status' => 14
                ]);
            }
            if($new_record->ttd_manager != $user->employee_id){
                //notification manager
                if(!empty($new_record->manager_ttd->email)){
                    $qry_user = User::where('employee_id', $new_record->ttd_manager)->first();
                    if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                        $details = [
                            'greeting' => 'Hi '.$new_record->manager_ttd->fullname,
                            'subject' => 'Laporan Pelaksanaan Training',
                            'body' => 'Ingin Menginformasikan bahwa ada laporan pelaksanaan training topik "'.$new_record->judul.'" yang membutuhkan approval anda',
                            'actionText' => 'Silahkan Login',
                            'actionURL' => url('/employee/training/approval/'.encrypt($new_record->id).'/laporan'),
                            'thanks' => 'Terimakasih atas perhatiannya!!'
                        ];
                    }else{
                        $details = [
                            'greeting' => 'Hi '.$new_record->manager_ttd->fullname,
                            'subject' => 'Laporan Pelaksanaan Training',
                            'body' => 'Ingin Menginformasikan bahwa ada laporan pelaksanaan training topik "'.$new_record->judul.'" yang membutuhkan approval anda',
                            'actionText' => 'Silahkan Login',
                            'actionURL' => url('/mytraining/approval/'.encrypt($new_record->id).'/form'),
                            'thanks' => 'Terimakasih atas perhatiannya!!'
                        ];
                    }
                    //send mail
                    $qry_user->notify(new AccountNotification($details));
                }
            }
        }
        //ttd hrd_ga_gm
        if($user->employee_id == $record->ttd_hrd_ga_gm){
            $post = Trainingrecord::where('id', $record->id)->where('ttd_hrd_ga_gm', $user->employee_id)->update([
                'tgl_ttd_hrd_ga_gm' => Carbon::now()
            ]);
            //insert log user activity
            $insert_log = new Log;
            $insert_log->user_id = $user->id;
            $insert_log->ip_address = $request->ip();
            $insert_log->action = 'approved';
            $insert_log->description = 'Approve laporan training dengan nama pemohon'.'"'.$record->employee->fullname.'" tanggal laporan "'.$record->tgl_laporan.'" oleh "HRD & GA General Manager"';
            $insert_log->save();

            $new_record = Trainingrecord::where('id', $request->id_record)->first();
            if(!empty($new_record->tgl_ttd_presiden) && !empty($new_record->tgl_ttd_direktur) && !empty($new_record->tgl_ttd_general_manager) && !empty($new_record->tgl_ttd_manager) && !empty($new_record->tgl_ttd_atasan) && !empty($new_record->tgl_ttd_hrd_ga_gm) && !empty($new_record->tgl_ttd_pic)){
                $post2 = Trainingrecord::where('id', $new_record->id)->where('ttd_hrd_ga_gm', $user->employee_id)->update([
                    'status' => 14
                ]);
            }
        }
        //ttd pic
        if($user->employee_id == $record->ttd_pic){
            $post = Trainingrecord::where('id', $record->id)->where('ttd_pic', $user->employee_id)->update([
                'tgl_ttd_pic' => Carbon::now()
            ]);
            //insert log user activity
            $insert_log = new Log;
            $insert_log->user_id = $user->id;
            $insert_log->ip_address = $request->ip();
            $insert_log->action = 'approved';
            $insert_log->description = 'Approve laporan training dengan nama pemohon'.'"'.$record->employee->fullname.'" tanggal laporan "'.$record->tgl_laporan.'" oleh "HRD PIC Pelatihan"';
            $insert_log->save();

            $new_record = Trainingrecord::where('id', $request->id_record)->first();
            if(!empty($new_record->tgl_ttd_presiden) && !empty($new_record->tgl_ttd_direktur) && !empty($new_record->tgl_ttd_general_manager) && !empty($new_record->tgl_ttd_manager) && !empty($new_record->tgl_ttd_atasan) && !empty($new_record->tgl_ttd_hrd_ga_gm) && !empty($new_record->tgl_ttd_pic)){
                $post2 = Trainingrecord::where('id', $new_record->id)->where('ttd_pic', $user->employee_id)->update([
                    'status' => 14
                ]);
            }
            if($new_record->ttd_hrd_ga_gm != $user->employee_id){
                //notification hrd & ga general manager
                if(empty($new_record->tgl_ttd_hrd_ga_gm)){
                    if(!empty($new_record->hrd_ga_gm_ttd->email)){
                        $qry_user = User::where('employee_id', $new_record->ttd_hrd_ga_gm)->first();
                        if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                            $details = [
                                'greeting' => 'Hi '.$new_record->hrd_ga_gm_ttd->fullname,
                                'subject' => 'Laporan Pelaksanaan Training',
                                'body' => 'Ingin Menginformasikan bahwa ada laporan pelaksanaan training topik "'.$new_record->judul.'" yang membutuhkan approval anda',
                                'actionText' => 'Silahkan Login',
                                'actionURL' => url('/employee/training/approval/'.encrypt($new_record->id).'/laporan'),
                                'thanks' => 'Terimakasih atas perhatiannya!!'
                            ];
                        }else{
                            $details = [
                                'greeting' => 'Hi '.$new_record->hrd_ga_gm_ttd->fullname,
                                'subject' => 'Laporan Pelaksanaan Training',
                                'body' => 'Ingin Menginformasikan bahwa ada laporan pelaksanaan training topik "'.$new_record->judul.'" yang membutuhkan approval anda',
                                'actionText' => 'Silahkan Login',
                                'actionURL' => url('/mytraining/approval/'.encrypt($new_record->id).'/form'),
                                'thanks' => 'Terimakasih atas perhatiannya!!'
                            ];
                        }
                        //send mail
                        $qry_user->notify(new AccountNotification($details));
                    }
                }
            }
        }

        return redirect(route('training.emp.index'))->with('status','Laporan pelaksanaan training has been approved')->with('tab_approval','open tab');
    }
    public function emp_back_approval_laporan(Request $request){
        return redirect(route('training.emp.index'))->with('tab_approval','open tab');
    }
    public function emp_create_laporan($id){
        $user = auth()->user();
        $training_record = Trainingrecord::where('id', decrypt($id))
            ->where('id_employee', $user->employee_id)->first();
        $employees = Employee::whereNot('status', 'TERMINATED')->get();
        return view('pages.employee.training.laporan.create', compact('user','training_record','employees'));
    }
    public function emp_store_laporan(Request $request){
        $validated = $request->validate([
            'cek_evaluasi' => 'required',
        ]);
        $user = auth()->user();
        $record = Trainingrecord::find($request->id_record);
        //upload sertifikat
        if(!empty($request->file('file_sertifikat'))){
            $cek_file = storage_path('app/public/sertifikat/'.$record->sertifikat);
            if(File::exists($cek_file)){
                File::delete($cek_file);
            }
            $sertifikat_file = $request->file('file_sertifikat');
            $sertifikat_name = time().'.'.$sertifikat_file->getClientOriginalExtension();
            $request->file('file_sertifikat')->storeAs('public/sertifikat', $sertifikat_name);
            //cek upload materi
            if(!empty($request->file('file_materi'))){
                $cek_materi = storage_path('app/public/materi/'.$record->materi);
                if(File::exists($cek_materi)){
                    File::delete($cek_materi);
                }
                $materi_file = $request->file('file_materi');
                $materi_name = time().'.'.$materi_file->getClientOriginalExtension();
                $request->file('file_materi')->storeAs('public/materi', $materi_name);
            }else{
                $materi_name = null;
            }
            if($record->employee->department->id == 3 || $record->employee->department->id == 4 || $record->employee->department->id == 5 || $record->employee->department->id == 6 || $record->employee->department->id == 8 || $record->employee->department->id == 9 || $record->employee->department->id == 10){
                $ttd_direktur = $request->ttd_direktur;
            }else{
                $ttd_direktur = null;
            }
            $post = Trainingrecord::where('id', $request->id_record)->update([
                'sertifikat' => $sertifikat_name,
                'materi' => $materi_name,
                'tgl_laporan' => $request->tgl_laporan,
                'isi_pelatihan' => $request->isi_pelatihan,
                'dipelajari' => $request->dipelajari,
                'implementasi' => $request->implementasi,
                'ttd_presiden' => $request->ttd_presiden,
                'ttd_direktur' => $ttd_direktur,
                'ttd_general_manager' => $request->ttd_general_manager,
                'ttd_manager' => $request->ttd_manager,
                'ttd_atasan' => $request->ttd_atasan,
                'ttd_hrd_ga_gm' => $request->ttd_hrd_ga,
                'ttd_pic' => $request->ttd_pic
            ]);

            //insert log user activity
            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'insert';
            $insert->description = 'Create formulir laporan pelaksanaan training dengan nama peserta'.'"'.$record->employee->fullname.'" pelatihan "'.$record->judul.'"';
            $insert->save();

            $new_record = Trainingrecord::where('id', $request->id_record)->first();

            //notification atasan langsung
            if(!empty($new_record->atasan_ttd->email)){
                $qry_user = User::where('employee_id', $new_record->ttd_atasan)->first();
                if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                    $details = [
                        'greeting' => 'Hi '.$new_record->atasan_ttd->fullname,
                        'subject' => 'Laporan Pelaksanaan Training',
                        'body' => 'Ingin Menginformasikan bahwa ada laporan pelaksanaan training topik "'.$new_record->judul.'" yang membutuhkan approval anda',
                        'actionText' => 'Silahkan Login',
                        'actionURL' => url('/employee/training/approval/'.encrypt($new_record->id).'/laporan'),
                        'thanks' => 'Terimakasih atas perhatiannya!!'
                    ];
                }else{
                    $details = [
                        'greeting' => 'Hi '.$new_record->atasan_ttd->fullname,
                        'subject' => 'Laporan Pelaksanaan Training',
                        'body' => 'Ingin Menginformasikan bahwa ada laporan pelaksanaan training topik "'.$new_record->judul.'" yang membutuhkan approval anda',
                        'actionText' => 'Silahkan Login',
                        'actionURL' => url('/mytraining/approval/'.encrypt($new_record->id).'/form'),
                        'thanks' => 'Terimakasih atas perhatiannya!!'
                    ];
                }
                //send mail
                $qry_user->notify(new AccountNotification($details));
            }

            return redirect(route('training.emp.index'))->with('status','Laporan pelaksanaan training has been created');
        }else{
            //cek upload materi
            if(!empty($request->file('file_materi'))){
                $cek_materi = storage_path('app/public/materi/'.$record->materi);
                if(File::exists($cek_materi)){
                    File::delete($cek_materi);
                }
                $materi_file = $request->file('file_materi');
                $materi_name = time().'.'.$materi_file->getClientOriginalExtension();
                $request->file('file_materi')->storeAs('public/materi', $materi_name);
            }else{
                $materi_name = null;
            }
            if($record->employee->department->id == 3 || $record->employee->department->id == 4 || $record->employee->department->id == 5 || $record->employee->department->id == 6 || $record->employee->department->id == 8 || $record->employee->department->id == 9 || $record->employee->department->id == 10){
                $ttd_direktur = $request->ttd_direktur;
            }else{
                $ttd_direktur = null;
            }

            $post = Trainingrecord::where('id', $request->id_record)->update([
                'sertifikat' => null,
                'materi' => $materi_name,
                'tgl_laporan' => $request->tgl_laporan,
                'isi_pelatihan' => $request->isi_pelatihan,
                'dipelajari' => $request->dipelajari,
                'implementasi' => $request->implementasi,
                'ttd_presiden' => $request->ttd_presiden,
                'ttd_direktur' => $ttd_direktur,
                'ttd_general_manager' => $request->ttd_general_manager,
                'ttd_manager' => $request->ttd_manager,
                'ttd_atasan' => $request->ttd_atasan,
                'ttd_hrd_ga_gm' => $request->ttd_hrd_ga,
                'ttd_pic' => $request->ttd_pic
            ]);

            //insert log user activity
            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'insert';
            $insert->description = 'Create formulir laporan pelaksanaan training dengan nama peserta'.'"'.$record->employee->fullname.'" pelatihan "'.$record->judul.'"';
            $insert->save();

            $new_record = Trainingrecord::where('id', $request->id_record)->first();

            //notification atasan langsung
            if(!empty($new_record->atasan_ttd->email)){
                $qry_user = User::where('employee_id', $new_record->ttd_atasan)->first();
                if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                    $details = [
                        'greeting' => 'Hi '.$new_record->atasan_ttd->fullname,
                        'subject' => 'Laporan Pelaksanaan Training',
                        'body' => 'Ingin Menginformasikan bahwa ada laporan pelaksanaan training topik "'.$new_record->judul.'" yang membutuhkan approval anda',
                        'actionText' => 'Silahkan Login',
                        'actionURL' => url('/employee/training/approval/'.encrypt($new_record->id).'/laporan'),
                        'thanks' => 'Terimakasih atas perhatiannya!!'
                    ];
                }else{
                    $details = [
                        'greeting' => 'Hi '.$new_record->atasan_ttd->fullname,
                        'subject' => 'Laporan Pelaksanaan Training',
                        'body' => 'Ingin Menginformasikan bahwa ada laporan pelaksanaan training topik "'.$new_record->judul.'" yang membutuhkan approval anda',
                        'actionText' => 'Silahkan Login',
                        'actionURL' => url('/mytraining/approval/'.encrypt($new_record->id).'/form'),
                        'thanks' => 'Terimakasih atas perhatiannya!!'
                    ];
                }
                //send mail
                $qry_user->notify(new AccountNotification($details));
            }

            return redirect(route('training.emp.index'))->with('status','Laporan pelaksanaan training has been created');
        }
    }
    public function emp_check_evaluasi_laporan(Request $request){
        $evaluasi = Trainingevaluasi::where('id_training_record', decrypt($request->id_record))->first();
        if(!empty($evaluasi)){
            $data = 'ya';
        }else{
            $data = 'tidak';
        }
        return response()->json($data);
    }
    public function emp_evaluasi_laporan($id){
        $kode = decrypt($id);
        $user = auth()->user();
        $evaluasi = Trainingevaluasi::where('id_training_record',$kode)->first();
        return view('pages.employee.training.laporan.evaluasi', compact('user','kode','evaluasi'));
    }
    public function emp_store_evaluasi_laporan(Request $request){
        DB::beginTransaction();
        try{
            $user = auth()->user();
            $training = Trainingrecord::find($request->id);
            $post = Trainingevaluasi::updateOrCreate(['id_training_record' => $request->id],[
                'dt_1' => $request->md_rating_dt_1,
                'dt_2' => $request->md_rating_dt_2,
                'dt_3' => $request->md_rating_dt_3,
                'dt_4' => $request->md_rating_dt_4,
                'dt_5' => $request->md_rating_dt_5,
                'fap_1' => $request->md_rating_fap_1,
                'fap_2' => $request->md_rating_fap_2,
                'fap_3' => $request->md_rating_fap_3,
                'fap_4' => $request->md_rating_fap_4,
                'trainer_1' => $request->trainer_1,
                'et_1' => $request->md_rating_et_1,
                'et_2' => $request->md_rating_et_2,
                'et_3' => $request->md_rating_et_3,
                'et_4' => $request->md_rating_et_4,
                'trainer_2' => $request->trainer_2,
                'et_5' => $request->md_rating_et_5,
                'et_6' => $request->md_rating_et_6,
                'et_7' => $request->md_rating_et_7,
                'et_8' => $request->md_rating_et_8,
                'trainer_3' => $request->trainer_3,
                'et_9' => $request->md_rating_et_9,
                'et_10' => $request->md_rating_et_10,
                'et_11' => $request->md_rating_et_11,
                'et_12' => $request->md_rating_et_12
            ]);
            if($post->wasRecentlyCreated){
                //insert log user activity
                $insert = new Log;
                $insert->user_id = $user->id;
                $insert->ip_address = $request->ip();
                $insert->action = 'insert';
                $insert->description = 'Create evaluasi training'.'"'.$training->judul.'" peserta "'.$training->employee->fullname.'"';
                $insert->save();
    
                DB::commit();
        
                return response()->json(['message' => "Training ".$training->judul." has been evaluation"], 200);
            }else{
                //insert log user activity
                $insert = new Log;
                $insert->user_id = $user->id;
                $insert->ip_address = $request->ip();
                $insert->action = 'update';
                $insert->description = 'Update evaluasi training'.'"'.$training->judul.'" peserta "'.$training->employee->fullname.'"';
                $insert->save();
    
                DB::commit();
        
                return response()->json(['message' => "Training ".$training->judul." has been evaluation"], 200);
            }
        }catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }
    //start employee ptt
    ///fkt pengajuan
    public function emp_index_fkt_ptt(Request $request){
        $user = auth()->user();
        $query = Trainingfkt::where(function ($query) use ($user) {
            $query->where('id_pemohon', $user->employee_id)
                  ->orWhere('id_peserta', $user->employee_id);
        })->orderBy('date_pemohon','asc')->get()->unique('kode');
        if($query->isNotEmpty()){
            foreach($query as $qry){
                $jml_peserta = Trainingfkt::where('kode', $qry->kode)->count();
                $jml_biaya = Trainingfkt::where('kode', $qry->kode)->sum('biaya_fkt');
                $index = $qry->id;
                $data[$index] = array();
                $data[$index]['kode'] = $qry->kode;
                $data[$index]['date_pemohon'] = date('d M Y', strtotime($qry->date_pemohon));
                $data[$index]['id_pemohon'] = $qry->id_pemohon;
                $data[$index]['pemohon'] = $qry->pemohon->fullname;
                $data[$index]['jml_peserta'] = $jml_peserta;
                $data[$index]['total_biaya'] = 'Rp '.number_format($jml_biaya,2,',','.');
                $data[$index]['status'] = $qry->status;
                $data[$index]['nama_status'] = $qry->training_status->name;
            }
        }else{
            $data = array();
        }
        if ($request->ajax()) {          
            return DataTables::of($data)
                ->addColumn('status', function($data){
                    if($data['status'] == 1) return '<a href="#" <span class="badge text-bg-primary"><i class="ri-edit-line align-bottom"></i> '.$data['nama_status'].'</span></a>';
                    if($data['status'] == 2) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#modal-status" <span class="badge text-bg-warning view-status"><i class="ri-time-line align-bottom"></i> '.$data['nama_status'].'</span></a>';
                    if($data['status'] == 3) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#modal-status" <span class="badge text-bg-info view-status"><i class="ri-time-line align-bottom"></i> '.$data['nama_status'].'</span></a>';
                    if($data['status'] == 4) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#modal-status" <span class="badge text-bg-info view-status"><i class="ri-time-line align-bottom"></i> '.$data['nama_status'].'</span></a>';
                    if($data['status'] == 5) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#modal-status" <span class="badge text-bg-secondary view-status"><i class="ri-time-line align-bottom"></i> '.$data['nama_status'].'</span></a>';
                    if($data['status'] == 6) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#modal-status" <span class="badge text-bg-success view-status"><i class="ri-checkbox-circle-line align-bottom"></i> '.$data['nama_status'].'</span></a>';
                    if($data['status'] == 7) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#modal-status" <span class="badge text-bg-success view-status"><i class="ri-checkbox-circle-line align-bottom"></i> Finished</span></a>';
                    if($data['status'] == 15) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#modal-status" <span class="badge text-bg-secondary view-status"><i class="ri-error-warning-line align-bottom"></i> '.$data['nama_status'].'</span></a>';
                    if($data['status'] == 16) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#modal-status" <span class="badge text-bg-secondary view-status"><i class="ri-error-warning-line align-bottom"></i> '.$data['nama_status'].'</span></a>';
                    if($data['status'] == 17) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#modal-status" <span class="badge text-bg-danger view-status"><i class="ri-close-circle-line align-bottom"></i> '.$data['nama_status'].'</span></a>';
                    if($data['status'] == 18) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#modal-status" <span class="badge text-bg-danger view-status"><i class="ri-close-circle-line align-bottom"></i> '.$data['nama_status'].'</span></a>';
                })
                ->addColumn('action', function ($data) {            
                    $cek_user = auth()->user();                  
                    $list_edit = '<li><a href="'.route('training.emp.fkt.ptt.edit',encrypt($data['kode'])).'" class="dropdown-item"><i class="ri-edit-line align-bottom me-2 text-muted"></i> Edit</a></li>';
                    $list_detail = '<li><a href="'.route('profile.training.fkt.ptt.detail',encrypt($data['kode'])).'" class="dropdown-item"><i class="ri-file-edit-line align-bottom me-2 text-muted"></i> Buat FPKP</a></li>';
                    $list_print_fkt = '<li><a href="'.route('public.training.fkp.pdf', encrypt($data['kode'])).'" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FKP</a></li>';
                    if($data['status'] == 1){      
                        if($data['id_pemohon'] == $cek_user->employee_id){
                            $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_edit.$list_print_fkt.'</ul></div>';
                        }else{
                            $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_print_fkt.'</ul></div>';
                        }
                    }elseif($data['status'] == 6){
                        $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_print_fkt.'</ul></div>';
                    }elseif($data['status'] == 15 || $data['status'] == 16){
                        if($data['id_pemohon'] == $cek_user->employee_id){
                            $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_edit.$list_print_fkt.'</ul></div>';
                        }else{
                            $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_print_fkt.'</ul></div>';
                        }
                    }elseif($data['status'] == 17 || $data['status'] == 18){
                        $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end"></ul></div>';
                    }else{
                        $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_print_fkt.'</ul></div>';
                    }
                    return $button;
                })
                ->addColumn('peserta', function($data){
                    $cek_user = auth()->user();
                    if($cek_user->employee_id == $data['id_pemohon']){
                        $query = Trainingfkt::where('kode', $data['kode'])->where('id_pemohon', $cek_user->employee_id)->get();
                    }else{
                        $query = Trainingfkt::where('kode', $data['kode'])->where('id_peserta', $cek_user->employee_id)->get();
                    }
                    if($query->isNotEmpty()){
                        $peserta = '<div class="col-lg-12"><table class="table table-bordered" style="table-layout: fixed; width:100%;">';
                        $peserta .= '
                            <thead>
                                <tr>
                                    <th colspan="4" style="text-align: center;">Pengajuan Program Pelatihan</th>
                                </tr>
                                <tr>
                                    <th style="text-align: center;">Pelatihan</th>
                                    <th style="text-align: center;">Peserta</th>
                                    <th style="text-align: center;">Pelaksanaan</th>
                                    <th style="text-align: center;">Biaya</th>
                                </tr>
                            </thead>
                            ';
                        $peserta .= '<tbody>';
                        foreach($query as $qry){
                            $bulan = \Carbon\Carbon::create()->month($qry->bulan_pelaksanaan)->format('F');
                            $peserta .= '<tr>';                  
                            $peserta .= '<td>'.$qry->judul.'</td>';     
                            $peserta .= '<td>'.$qry->peserta->fullname.'</td>';      
                            $peserta .= '<td>'.$bulan.' '.$qry->tahun_pelaksanaan.'</td>';      
                            $peserta .= '<td> Rp '.number_format($qry->biaya_fkt,2,',','.').'</td>';      
                            $peserta .= '</tr>';
                        } 
                        $peserta .= '</tbody></table></div>';
                    }else{
                        $peserta = '';
                    }
                    return $peserta;
                })
                ->rawColumns(['action','status','peserta'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.employee.training.index', compact('user'));
    }
    public function emp_fkt_ptt_status(Request $request){
        if(!empty($request->kode)){
            $query = Trainingfkt::where('kode', decrypt($request->kode))->first();
            $data['judul'] = $query->judul;
            $data['tujuan_usulan'] = 'Program Pelatihan Tahunan';
            $data['nama_pemohon'] = $query->pemohon->fullname;
            $data['date_pemohon'] = date('d M Y H:i', strtotime($query->date_pemohon));
            $data['bulan_pelaksanaan'] = Carbon::create()->month($query->bulan_pelaksanaan)->format('F') ?? '-';
            $data['tahun_pelaksanaan'] = $query->tahun_pelaksanaan;
            $data['id_status_fkt'] = $query->status;
            $data['nama_status_fkt'] = $query->training_status->name;
            
            //status fkp
            if(!empty($query->date_checker)){
                $data['atasan_dept'] = $query->checker->fullname;
                $data['date_atasan_dept'] = date('d M Y H:i', strtotime($query->date_checker));
            }else{
                $data['atasan_dept'] = null;
                $data['date_atasan_dept'] = null;
            }
            if(!empty($query->date_verified)){
                $data['verified_hrd'] = $query->verified->fullname;
                $data['date_verified_hrd'] = date('d M Y H:i', strtotime($query->date_verified));
            }else{
                $data['verified_hrd'] = null;
                $data['date_verified_hrd'] = null;
            }
            //catatan fkp
            $query_ctt = Logcatatantraining::where('kode_fkt', $query->kode)->get();
            if($query_ctt->isNotEmpty()){
                foreach($query_ctt as $qry_ctt){
                    $dt['id_user'] = $qry_ctt->employee->fullname;
                    $dt['action'] = $qry_ctt->action;
                    $dt['catatan'] = $qry_ctt->catatan;
                    $dt['tgl_ctt'] = date('d M Y H:i', strtotime($qry_ctt->created_at));
                    $dt_all[] = $dt;
                }
                $data['ctt'] = $dt_all;
            }else{
                $data['ctt'] = null;
            }
            //status revisi atasan fkp
            $atasan_dept_ctt_rev = Logcatatantraining::where('kode_fkt', $query->kode)->where('action', 'revise atasan')->orderBy('created_at', 'desc')->first();
            if(!empty($atasan_dept_ctt_rev)){
                $adc_rev['id_user'] = $atasan_dept_ctt_rev->employee->fullname;
                $adc_rev['action'] = $atasan_dept_ctt_rev->action;
                $adc_rev['tgl_ctt'] = date('d M Y H:i', strtotime($atasan_dept_ctt_rev->created_at));
                $data['atasan_revise_ctt'] = $adc_rev;
            }else{
                $data['atasan_revise_ctt'] = null;
            }
            //status reject atasan fkp
            $atasan_dept_ctt_rj = Logcatatantraining::where('kode_fkt', $query->kode)->where('action', 'reject atasan')->orderBy('created_at', 'desc')->first();
            if(!empty($atasan_dept_ctt_rj)){
                $adc_rj['id_user'] = $atasan_dept_ctt_rj->employee->fullname;
                $adc_rj['action'] = $atasan_dept_ctt_rj->action;
                $adc_rj['tgl_ctt'] = date('d M Y H:i', strtotime($atasan_dept_ctt_rj->created_at));
                $data['atasan_reject_ctt'] = $adc_rj;
            }else{
                $data['atasan_reject_ctt'] = null;
            }
            //status revisi hrd fkp
            $hrd_ctt_rev = Logcatatantraining::where('kode_fkt', $query->kode)->where('action', 'revise hrd')->orderBy('created_at', 'desc')->first();
            if(!empty($hrd_ctt_rev)){
                $hrd_rev['id_user'] = $hrd_ctt_rev->employee->fullname;
                $hrd_rev['action'] = $hrd_ctt_rev->action;
                $hrd_rev['tgl_ctt'] = date('d M Y H:i', strtotime($hrd_ctt_rev->created_at));
                $data['hrd_revise_ctt'] = $hrd_rev;
            }else{
                $data['hrd_revise_ctt'] = null;
            }
            //status reject hrd fkp
            $hrd_ctt_rj =  Logcatatantraining::where('kode_fkt', $query->kode)->where('action', 'reject hrd')->orderBy('created_at', 'desc')->first();
            if(!empty($hrd_ctt_rj)){
                $hrd_rj['id_user'] = $hrd_ctt_rj->employee->fullname;
                $hrd_rj['action'] = $hrd_ctt_rj->action;
                $hrd_rj['tgl_ctt'] = date('d M Y H:i', strtotime($hrd_ctt_rj->created_at));
                $data['hrd_reject_ctt'] = $hrd_rj;
            }else{
                $data['hrd_reject_ctt'] = null;
            }
        }
        
        return response()->json($data);
    }
    public function emp_fkt_ptt_create(Request $request){
        $user = auth()->user();
        $year_now = date('Y');
        
        $periode = Trainingperiode::where('status','1')->get();
        $employees = Employee::whereNot('status', 'TERMINATED')->get();
        $vendors = Vendor::where('tipe','training')->get();
        return view('pages.employee.training.ptt.form-fkt', compact('user','year_now','employees','vendors','periode'));
    }
    public function emp_fkt_ptt_edit(Request $request, $id){
        $user = auth()->user();
        $training_fkt  = Trainingfkt::where('kode', decrypt($id))->first();
        $year_now = date('Y');
        
        $periode = Trainingperiode::where('status','1')->get();
        $employees = Employee::whereNot('status', 'TERMINATED')->get();
        $vendors = Vendor::where('tipe','training')->get();
        
        $query = Trainingfkt::where('kode', decrypt($id))->get()->unique('judul')->pluck('judul');

        foreach($query as $key => $value){
            $query2 = Trainingfkt::where('kode', decrypt($id))->where('judul', $value)->get();
            $data[$key]['id_peserta'][] = $query2->pluck('id_peserta')->toArray();
            $data[$key]['id_penilai'][] = $query2->pluck('id_penilai')->toArray();
            $data[$key]['judul'] = $value;
            $data[$key]['sifat'] = $query2->unique('sifat')->pluck('sifat')->toArray();
            $data[$key]['jenis_pelatihan'] = $query2->unique('jenis_pelatihan')->pluck('jenis_pelatihan')->toArray();
            $data[$key]['alasan'] = $query2->unique('alasan')->pluck('alasan')->toArray();
            $data[$key]['bulan_pelaksana'] = $query2->unique('bulan_pelaksanaan')->pluck('bulan_pelaksanaan')->toArray();
            $data[$key]['id_vendor'] = $query2->unique('id_vendor')->pluck('id_vendor')->toArray();
            $data[$key]['nama_vendor'] = $query2->unique('nama_vendor')->pluck('nama_vendor')->toArray();
            $data[$key]['biaya_fkt'] = $query2->unique('biaya_fkt')->pluck('biaya_fkt')->toArray();
            $data[$key]['penginapan'] = $query2->unique('penginapan')->pluck('penginapan')->toArray();
            $data[$key]['transportasi'] = $query2->unique('transportasi')->pluck('transportasi')->toArray();
        }
        $data_all = $data;
        return view('pages.employee.training.ptt.edit-fkt', compact('user','year_now','employees','vendors','training_fkt','data_all','periode'));
    }
    public function emp_fkt_ptt_detail(Request $request, $id){
        $user = auth()->user();
        $fkt = Trainingfkt::where('kode', decrypt($id))->first();
        $query_fkt = Trainingfkt::where('kode', decrypt($id))->get();
        $jml_pemohon = Trainingfkt::where('kode', decrypt($id))->where('id_pemohon', $user->employee_id)->get()->count();
        $jml_peserta = Trainingfkt::where('kode', decrypt($id))->where('id_peserta', $user->employee_id)->get()->count();
        $jml_penilai = Trainingfkt::where('kode', decrypt($id))->where('id_penilai', $user->employee_id)->get()->count();
        $total_fkt = $jml_pemohon+$jml_peserta+$jml_penilai;

        return view('pages.employee.training.ptt.form-fkt-detail', compact('user','fkt','query_fkt','total_fkt'));
    }
    public function emp_fkt_ptt_store(Request $request){
        DB::beginTransaction();
        try {
            if($request->action == 'draft'){
                $user = auth()->user();
                $month_now = date("m");
                $year_now = date("y");
                $month_name = array(
                    '01' => 'I',
                    '02' => 'II',
                    '03' => 'III',
                    '04' => 'IV',
                    '05' => 'V',
                    '06' => 'VI',
                    '07' => 'VII',
                    '08' => 'VIII',
                    '09' => 'IX',
                    '10' => 'X',
                    '11' => 'XI',
                    '12' => 'XII',
                );
                $nama_pemohon = $request->nama_pemohon;
                if(isset($request->no_urut)){
                    $data = $request->input();
                    $query = Trainingfkt::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->latest('kode')->first();
                    if(!empty($query)){
                        $arr = explode("/", $query->kode);
                        $no = intval($arr[0])+1;
                        if(strlen($no) == 1){
                            $no = ["00".$no];
                        }elseif(strlen($no) == 2){
                            $no = ["0".$no];
                        }else{
                            $no = array($no);
                        }
                        $fkt = $arr[1];
                        $ptt = $arr[2];
                        $bulan = $arr[3];
                        $tahun = $arr[4];
                        $kode = $no[0].'/'.$fkt.'/'.$ptt.'/'.$bulan.'/'.$tahun;
                    }else{
                        $no = '001';
                        $fkt = 'FKT';
                        $ptt = 'PTT';
                        $bulan = $month_name[$month_now];
                        $tahun = $year_now;
                        $kode = $no.'/'.$fkt.'/'.$ptt.'/'.$bulan.'/'.$tahun;
                    }
    
                    for($i = 0; $i < count($data['no_urut']); $i++){
                        if($data['id_peserta-'.$data['no_urut'][$i]] && $data['judul-'.$data['no_urut'][$i]] && $data['jenis_pelatihan-'.$data['no_urut'][$i]] && $data['sifat-'.$data['no_urut'][$i]] && $data['alasan-'.$data['no_urut'][$i]] && $data['bulan_pelaksanaan-'.$data['no_urut'][$i]] && $data['id_vendor-'.$data['no_urut'][$i]] && $data['vendor_other-'.$data['no_urut'][$i]] && $data['biaya_fkt-'.$data['no_urut'][$i]] && $data['penginapan-'.$data['no_urut'][$i]] && $data['transportasi-'.$data['no_urut'][$i]]){
                            $id_peserta = $data['id_peserta-'.$data['no_urut'][$i]];
                            $judul = $data['judul-'.$data['no_urut'][$i]];
                            $jenis = $data['jenis_pelatihan-'.$data['no_urut'][$i]];
                            $sifat = $data['sifat-'.$data['no_urut'][$i]];
                            $alasan = $data['alasan-'.$data['no_urut'][$i]];
                            $bulan_pelaksanaan = $data['bulan_pelaksanaan-'.$data['no_urut'][$i]];
                            $id_vendor = $data['id_vendor-'.$data['no_urut'][$i]];
                            $vendor_other = $data['vendor_other-'.$data['no_urut'][$i]];
                            $biaya_fkt = str_replace(".","",$data['biaya_fkt-'.$data['no_urut'][$i]]);
                            $penginapan = $data['penginapan-'.$data['no_urut'][$i]];
                            $transportasi = $data['transportasi-'.$data['no_urut'][$i]];
                        }
                        $arr_data = [
                            'id_pemohon' => $data['id_pemohon'],
                            'tahun_usulan' => $data['tahun_usulan'],
                            'tahun_pelaksanaan' => $data['tahun_pelaksanaan'],
                            'id_peserta' => $id_peserta,
                            'judul' => $judul,
                            'jenis' => $jenis,
                            'sifat' => $sifat,
                            'alasan' => $alasan,
                            'bulan_pelaksanaan' => $bulan_pelaksanaan,
                            'id_vendor' => $id_vendor,
                            'vendor_other' => $vendor_other,
                            'biaya_fkt' => $biaya_fkt,
                            'penginapan' => $penginapan,
                            'transportasi' => $transportasi
                        ];
    
                        for($n = 0; $n < count($arr_data['id_peserta']); $n++){
                            if($arr_data['id_vendor'][0] != 'other'){
                                $vendor_id = $arr_data['id_vendor'][0];
                            }else{
                                $vendor_id = null;
                            }
    
                            $insert[] = [
                                'id_pemohon' => $arr_data['id_pemohon'],
                                'date_pemohon' => date('Y-m-d H:i:s'),
                                'tahun_usulan' => $arr_data['tahun_usulan'],
                                'tahun_pelaksanaan' => $arr_data['tahun_pelaksanaan'],
                                'kode' => $kode,
                                'id_peserta' => $arr_data['id_peserta'][$n],
                                'judul' => $arr_data['judul'][0],
                                'jenis_pelatihan' => $arr_data['jenis'][0],
                                'sifat' => $arr_data['sifat'][0],
                                'alasan' => $arr_data['alasan'][0],
                                'bulan_pelaksanaan' => $arr_data['bulan_pelaksanaan'][0],
                                'id_vendor' => $vendor_id,
                                'nama_vendor' => $arr_data['vendor_other'][0],
                                'biaya_fkt' => $arr_data['biaya_fkt'][0],
                                'penginapan' => $arr_data['penginapan'][0],
                                'transportasi' => $arr_data['transportasi'][0],
                                'status' => 1,
                                'id_checker' => $request->id_checker,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ];
                        }
                    }
                    $post = Trainingfkt::insert($insert);
    
                    //insert log user activity
                    $insert = new Log;
                    $insert->user_id = $user->id;
                    $insert->ip_address = $request->ip();
                    $insert->action = 'insert';
                    $insert->description = 'Draft new formulir kebutuhan pelatihan dengan nomor dokumen "'.$kode.'" nama pemohon'.'"'.$data['nama_pemohon'].'" tujuan "Program Training Tahunan (PTT)"';
                    $insert->save();

                    DB::commit();

                    return redirect(route('training.emp.index'))->with('tab_ptt','open tab')->with('status','Draft Formulir Kebutuhan Training has been created');
                }else{
                    return redirect(route('training.emp.index'))->with('tab_ptt','open tab')->with('error','Formulir Kebutuhan Pelatihan no changes');
                }

            }

            if($request->action == 'submit'){
                $user = auth()->user();
                $month_now = date("m");
                $year_now = date("y");
                $month_name = array(
                    '01' => 'I',
                    '02' => 'II',
                    '03' => 'III',
                    '04' => 'IV',
                    '05' => 'V',
                    '06' => 'VI',
                    '07' => 'VII',
                    '08' => 'VIII',
                    '09' => 'IX',
                    '10' => 'X',
                    '11' => 'XI',
                    '12' => 'XII',
                );
                $nama_pemohon = $request->nama_pemohon;
                if(isset($request->no_urut)){
                    $data = $request->input();
                    $query = Trainingfkt::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->latest('kode')->first();
                    if(!empty($query)){
                        $arr = explode("/", $query->kode);
                        $no = intval($arr[0])+1;
                        if(strlen($no) == 1){
                            $no = ["00".$no];
                        }elseif(strlen($no) == 2){
                            $no = ["0".$no];
                        }else{
                            $no = array($no);
                        }
                        $fkt = $arr[1];
                        $ptt = $arr[2];
                        $bulan = $arr[3];
                        $tahun = $arr[4];
                        $kode = $no[0].'/'.$fkt.'/'.$ptt.'/'.$bulan.'/'.$tahun;
                    }else{
                        $no = '001';
                        $fkt = 'FKT';
                        $ptt = 'PTT';
                        $bulan = $month_name[$month_now];
                        $tahun = $year_now;
                        $kode = $no.'/'.$fkt.'/'.$ptt.'/'.$bulan.'/'.$tahun;
                    }

                    for($i = 0; $i < count($data['no_urut']); $i++){
                        $code_random = random_int(100000, 999999);
                        if($data['id_peserta-'.$data['no_urut'][$i]] && $data['judul-'.$data['no_urut'][$i]] && $data['jenis_pelatihan-'.$data['no_urut'][$i]] && $data['sifat-'.$data['no_urut'][$i]] && $data['alasan-'.$data['no_urut'][$i]] && $data['bulan_pelaksanaan-'.$data['no_urut'][$i]] && $data['id_vendor-'.$data['no_urut'][$i]] && $data['vendor_other-'.$data['no_urut'][$i]] && $data['biaya_fkt-'.$data['no_urut'][$i]] && $data['penginapan-'.$data['no_urut'][$i]] && $data['transportasi-'.$data['no_urut'][$i]]){
                            $id_peserta = $data['id_peserta-'.$data['no_urut'][$i]];
                            $judul = $data['judul-'.$data['no_urut'][$i]];
                            $jenis = $data['jenis_pelatihan-'.$data['no_urut'][$i]];
                            $sifat = $data['sifat-'.$data['no_urut'][$i]];
                            $alasan = $data['alasan-'.$data['no_urut'][$i]];
                            $bulan_pelaksanaan = $data['bulan_pelaksanaan-'.$data['no_urut'][$i]];
                            $id_vendor = $data['id_vendor-'.$data['no_urut'][$i]];
                            $vendor_other = $data['vendor_other-'.$data['no_urut'][$i]];
                            $biaya_fkt = str_replace(".","",$data['biaya_fkt-'.$data['no_urut'][$i]]);
                            $penginapan = $data['penginapan-'.$data['no_urut'][$i]];
                            $transportasi = $data['transportasi-'.$data['no_urut'][$i]];
                        }
                        $arr_data = [
                            'id_pemohon' => $data['id_pemohon'],
                            'tahun_usulan' => $data['tahun_usulan'],
                            'tahun_pelaksanaan' => $data['tahun_pelaksanaan'],
                            'id_peserta' => $id_peserta,
                            'judul' => $judul,
                            'jenis' => $jenis,
                            'sifat' => $sifat,
                            'alasan' => $alasan,
                            'bulan_pelaksanaan' => $bulan_pelaksanaan,
                            'id_vendor' => $id_vendor,
                            'vendor_other' => $vendor_other,
                            'biaya_fkt' => $biaya_fkt,
                            'penginapan' => $penginapan,
                            'transportasi' => $transportasi
                        ];
    
                        for($n = 0; $n < count($arr_data['id_peserta']); $n++){
                            $employee = Employee::where('id', $arr_data['id_pemohon'])->first();
                            if($arr_data['id_vendor'][0] != 'other'){
                                $vendor_id = $arr_data['id_vendor'][0];
                            }else{
                                $vendor_id = null;
                            }
                            if($arr_data['id_pemohon'] == $request->id_checker){
                                $insert[] = [
                                    'id_pemohon' => $arr_data['id_pemohon'],
                                    'dept_pemohon' => $employee->department_id,
                                    'date_pemohon' => date('Y-m-d H:i:s'),
                                    'tahun_usulan' => $arr_data['tahun_usulan'],
                                    'tahun_pelaksanaan' => $arr_data['tahun_pelaksanaan'],
                                    'kode' => $kode,
                                    'id_peserta' => $arr_data['id_peserta'][$n],
                                    'kode_judul' => $year_now.$code_random,
                                    'judul' => $arr_data['judul'][0],
                                    'jenis_pelatihan' => $arr_data['jenis'][0],
                                    'sifat' => $arr_data['sifat'][0],
                                    'alasan' => $arr_data['alasan'][0],
                                    'bulan_pelaksanaan' => $arr_data['bulan_pelaksanaan'][0],
                                    'id_vendor' => $vendor_id,
                                    'nama_vendor' => $arr_data['vendor_other'][0],
                                    'biaya_fkt' => $arr_data['biaya_fkt'][0],
                                    'penginapan' => $arr_data['penginapan'][0],
                                    'transportasi' => $arr_data['transportasi'][0],
                                    'status' => 3,
                                    'id_checker' => $request->id_checker,
                                    'date_checker' => date('Y-m-d H:i:s'),
                                    'created_at' => Carbon::now(),
                                    'updated_at' => Carbon::now()
                                ];
                            }else{
                                $insert[] = [
                                    'id_pemohon' => $arr_data['id_pemohon'],
                                    'dept_pemohon' => $employee->department_id,
                                    'date_pemohon' => date('Y-m-d H:i:s'),
                                    'tahun_usulan' => $arr_data['tahun_usulan'],
                                    'tahun_pelaksanaan' => $arr_data['tahun_pelaksanaan'],
                                    'kode' => $kode,
                                    'id_peserta' => $arr_data['id_peserta'][$n],
                                    'kode_judul' => $year_now.$code_random,
                                    'judul' => $arr_data['judul'][0],
                                    'jenis_pelatihan' => $arr_data['jenis'][0],
                                    'sifat' => $arr_data['sifat'][0],
                                    'alasan' => $arr_data['alasan'][0],
                                    'bulan_pelaksanaan' => $arr_data['bulan_pelaksanaan'][0],
                                    'id_vendor' => $vendor_id,
                                    'nama_vendor' => $arr_data['vendor_other'][0],
                                    'biaya_fkt' => $arr_data['biaya_fkt'][0],
                                    'penginapan' => $arr_data['penginapan'][0],
                                    'transportasi' => $arr_data['transportasi'][0],
                                    'status' => 2,
                                    'id_checker' => $request->id_checker,
                                    'created_at' => Carbon::now(),
                                    'updated_at' => Carbon::now()
                                ];
                            }
                        }
    
                        if($arr_data['id_pemohon'] == $request->id_checker){
                            if($i == 0){
                                //notification pic hrd
                                $users = User::whereHas(
                                    'roles', function($q){
                                        $q->where('id', 2);
                                    }
                                )->get();
                                if($users->isNotEmpty()){
                                    foreach($users as $key_user){
                                        if(!empty($key_user->email)){
                                            $qry_user = User::where('employee_id', $key_user->employee_id)->first();
                                            $details = [
                                                'greeting' => 'Hi '.$qry_user->name,
                                                'subject' => 'Verification Program Pelatihan Tahunan (PPT)',
                                                'body' => 'Ingin Menginformasikan bahwa ada usulan program pelatihan tahunan dengan nomor dokumen "'.$kode.'" pemohon "'.$nama_pemohon.'" yang membutuhkan verification anda',
                                                'actionText' => 'Silahkan Login',
                                                'actionURL' => url('/hrd/training/ptt'),
                                                'thanks' => 'Terimakasih atas perhatiannya!!'
                                            ];
                                            //send mail
                                            $qry_user->notify(new AccountNotification($details));
                                        }
                                    }
                                }
                            }
                        }else{
                            //notification atasan departemen
                            if($i == 0){
                                $qry_user = User::where('employee_id', $request->id_checker)->first();
                                if(!empty($qry_user->email)){
                                    if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                                        $details = [
                                            'greeting' => 'Hi '.$qry_user->name,
                                            'subject' => 'PROGRAM PELATIHAN TAHUNAN',
                                            'body' => 'Ingin Menginformasikan bahwa ada usulan program pelatihan tahunan dengan nomor dokumen "'.$kode.'" pemohon "'.$nama_pemohon.'" yang membutuhkan approval anda',
                                            'actionText' => 'Silahkan Login',
                                            'actionURL' => url('/employee/training/fkt/ptt/approved/'.encrypt($arr_data['tahun_usulan'][0]).'/form'),
                                            'thanks' => 'Terimakasih atas perhatiannya!!'
                                        ];
                                    }else{
                                        $details = [
                                            'greeting' => 'Hi '.$qry_user->name,
                                            'subject' => 'PROGRAM PELATIHAN TAHUNAN',
                                            'body' => 'Ingin Menginformasikan bahwa ada usulan program pelatihan tahunan dengan nomor dokumen "'.$kode.'" pemohon "'.$nama_pemohon.'" yang membutuhkan approval anda',
                                            'actionText' => 'Silahkan Login',
                                            'actionURL' => url('/mytraining/fkt/ptt/approved/'.encrypt($arr_data['tahun_usulan'][0]).'/form'),
                                            'thanks' => 'Terimakasih atas perhatiannya!!'
                                        ];
                                    }
                                    //send mail
                                    $qry_user->notify(new AccountNotification($details));
                                }
                            }
                        }
                    }
                    $post = Trainingfkt::insert($insert);

                    if($request->id_pemohon == $request->id_checker){
                        //ttd atasan departemen
                        $date_qr = date('Ymd');
                        $insert_approved_qr = new Qrcodefkt;
                        $insert_approved_qr->kode_fkt = $query->kode;
                        $insert_approved_qr->qr = $date_qr.$user->employee_id;
                        $insert_approved_qr->date_approval = date('Y-m-d H:i:s');
                        $insert_approved_qr->type = 5;
                        $insert_approved_qr->save();
                    }
                    //ttd pemohon
                    $date_qr = date('Ymd');
                    $insert = new Qrcodefkt;
                    $insert->kode_fkt = $kode;
                    $insert->qr = $date_qr.$user->employee_id;
                    $insert->date_approval = date('Y-m-d H:i:s');
                    $insert->type = 1;
                    $insert->save();
    
                    //insert log user activity
                    $insert = new Log;
                    $insert->user_id = $user->id;
                    $insert->ip_address = $request->ip();
                    $insert->action = 'insert';
                    $insert->description = 'Propose new formulir kebutuhan pelatihan dengan nomor dokumen "'.$kode.'" nama pemohon'.'"'.$data['nama_pemohon'].'" tujuan "Program Training Tahunan (PTT)"';
                    $insert->save();

                    DB::commit();

                    return redirect(route('training.emp.index'))->with('tab_ptt','open tab')->with('status','Formulir Kebutuhan Pelatihan '.$nama_pemohon.' has been updated');
                }else{
                    return redirect(route('training.emp.index'))->with('tab_ptt','open tab')->with('error','Formulir Kebutuhan Pelatihan no changes');
                }
            }
        } catch (Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }
    public function emp_fkt_ptt_update(Request $request){
        DB::beginTransaction();
        try {
            if($request->action == "draft"){
                $user = auth()->user();
                $data = $request->input();
                $nama_pemohon = $request->nama_pemohon;
                if(isset($request->no_urut)){
                    $delete_fkt_ptt = Trainingfkt::where('kode', $data['kode'])->delete();
                    for($i = 0; $i < count($data['no_urut']); $i++){
                        if($data['id_peserta-'.$data['no_urut'][$i]] && $data['judul-'.$data['no_urut'][$i]] && $data['jenis_pelatihan-'.$data['no_urut'][$i]] && $data['sifat-'.$data['no_urut'][$i]] && $data['alasan-'.$data['no_urut'][$i]] && $data['bulan_pelaksanaan-'.$data['no_urut'][$i]] && $data['id_vendor-'.$data['no_urut'][$i]] && $data['vendor_other-'.$data['no_urut'][$i]] && $data['biaya_fkt-'.$data['no_urut'][$i]] && $data['penginapan-'.$data['no_urut'][$i]] && $data['transportasi-'.$data['no_urut'][$i]]){
                            $id_peserta = $data['id_peserta-'.$data['no_urut'][$i]];
                            $judul = $data['judul-'.$data['no_urut'][$i]];
                            $jenis = $data['jenis_pelatihan-'.$data['no_urut'][$i]];
                            $sifat = $data['sifat-'.$data['no_urut'][$i]];
                            $alasan = $data['alasan-'.$data['no_urut'][$i]];
                            $bulan_pelaksanaan = $data['bulan_pelaksanaan-'.$data['no_urut'][$i]];
                            $id_vendor = $data['id_vendor-'.$data['no_urut'][$i]];
                            $vendor_other = $data['vendor_other-'.$data['no_urut'][$i]];
                            $biaya_fkt = str_replace(".","",$data['biaya_fkt-'.$data['no_urut'][$i]]);
                            $penginapan = $data['penginapan-'.$data['no_urut'][$i]];
                            $transportasi = $data['transportasi-'.$data['no_urut'][$i]];
                        }
                        $arr_data = [
                            'id_pemohon' => $data['id_pemohon'],
                            'tahun_usulan' => $data['tahun_usulan'],
                            'tahun_pelaksanaan' => $data['tahun_pelaksanaan'],
                            'id_peserta' => $id_peserta,
                            'judul' => $judul,
                            'jenis' => $jenis,
                            'sifat' => $sifat,
                            'alasan' => $alasan,
                            'bulan_pelaksanaan' => $bulan_pelaksanaan,
                            'id_vendor' => $id_vendor,
                            'vendor_other' => $vendor_other,
                            'biaya_fkt' => $biaya_fkt,
                            'penginapan' => $penginapan,
                            'transportasi' => $transportasi
                        ];
        
                        for($n = 0; $n < count($arr_data['id_peserta']); $n++){
                            if($arr_data['id_vendor'][0] != 'other'){
                                $vendor_id = $arr_data['id_vendor'][0];
                            }else{
                                $vendor_id = null;
                            }
        
                            $insert[] = [
                                'id_pemohon' => $arr_data['id_pemohon'],
                                'date_pemohon' => date('Y-m-d H:i:s'),
                                'tahun_usulan' => $arr_data['tahun_usulan'],
                                'tahun_pelaksanaan' => $arr_data['tahun_pelaksanaan'],
                                'kode' => $data['kode'],
                                'id_peserta' => $arr_data['id_peserta'][$n],
                                'judul' => $arr_data['judul'][0],
                                'jenis_pelatihan' => $arr_data['jenis'][0],
                                'sifat' => $arr_data['sifat'][0],
                                'alasan' => $arr_data['alasan'][0],
                                'bulan_pelaksanaan' => $arr_data['bulan_pelaksanaan'][0],
                                'id_vendor' => $vendor_id,
                                'nama_vendor' => $arr_data['vendor_other'][0],
                                'biaya_fkt' => $arr_data['biaya_fkt'][0],
                                'penginapan' => $arr_data['penginapan'][0],
                                'transportasi' => $arr_data['transportasi'][0],
                                'status' => 1,
                                'id_checker' => $request->id_checker,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ];
                        }
                    }
                    $post = Trainingfkt::insert($insert);
        
                    //insert log user activity
                    $insert_log = new Log;
                    $insert_log->user_id = $user->id;
                    $insert_log->ip_address = $request->ip();
                    $insert_log->action = 'insert';
                    $insert_log->description = 'Modify formulir kebutuhan pelatihan dengan nomor dokumen "'.$data['kode'].'" nama pemohon'.'"'.$data['nama_pemohon'].'" tujuan "Program Training Tahunan (PTT)"';
                    $insert_log->save();
    
                    DB::commit();
    
                    return redirect(route('training.emp.index'))->with('tab_ptt','open tab')->with('status','Draft Formulir Kebutuhan Pelatihan has been updated');
                }else{
                    return redirect(route('training.emp.index'))->with('tab_ptt','open tab')->with('error','Formulir Kebutuhan Pelatihan no changes');
                }
    
            }
    
            if($request->action == "submit"){
                $user = auth()->user();
                $data = $request->input();
                $nama_pemohon = $request->nama_pemohon;
                $year_now = date("y");
                if(isset($request->no_urut)){
                    $delete_fkt_ptt = Trainingfkt::where('kode', $data['kode'])->delete();
                    for($i = 0; $i < count($data['no_urut']); $i++){
                        $code_random = random_int(100000, 999999);
                        if($data['id_peserta-'.$data['no_urut'][$i]] && $data['judul-'.$data['no_urut'][$i]] && $data['jenis_pelatihan-'.$data['no_urut'][$i]] && $data['sifat-'.$data['no_urut'][$i]] && $data['alasan-'.$data['no_urut'][$i]] && $data['bulan_pelaksanaan-'.$data['no_urut'][$i]] && $data['id_vendor-'.$data['no_urut'][$i]] && $data['vendor_other-'.$data['no_urut'][$i]] && $data['biaya_fkt-'.$data['no_urut'][$i]] && $data['penginapan-'.$data['no_urut'][$i]] && $data['transportasi-'.$data['no_urut'][$i]]){
                            $id_peserta = $data['id_peserta-'.$data['no_urut'][$i]];
                            $judul = $data['judul-'.$data['no_urut'][$i]];
                            $jenis = $data['jenis_pelatihan-'.$data['no_urut'][$i]];
                            $sifat = $data['sifat-'.$data['no_urut'][$i]];
                            $alasan = $data['alasan-'.$data['no_urut'][$i]];
                            $bulan_pelaksanaan = $data['bulan_pelaksanaan-'.$data['no_urut'][$i]];
                            $id_vendor = $data['id_vendor-'.$data['no_urut'][$i]];
                            $vendor_other = $data['vendor_other-'.$data['no_urut'][$i]];
                            $biaya_fkt = str_replace(".","",$data['biaya_fkt-'.$data['no_urut'][$i]]);
                            $penginapan = $data['penginapan-'.$data['no_urut'][$i]];
                            $transportasi = $data['transportasi-'.$data['no_urut'][$i]];
                        }
                        $arr_data = [
                            'id_pemohon' => $data['id_pemohon'],
                            'tahun_usulan' => $data['tahun_usulan'],
                            'tahun_pelaksanaan' => $data['tahun_pelaksanaan'],
                            'id_peserta' => $id_peserta,
                            'judul' => $judul,
                            'jenis' => $jenis,
                            'sifat' => $sifat,
                            'alasan' => $alasan,
                            'bulan_pelaksanaan' => $bulan_pelaksanaan,
                            'id_vendor' => $id_vendor,
                            'vendor_other' => $vendor_other,
                            'biaya_fkt' => $biaya_fkt,
                            'penginapan' => $penginapan,
                            'transportasi' => $transportasi
                        ];
        
                        for($n = 0; $n < count($arr_data['id_peserta']); $n++){
                            $employee = Employee::where('id', $arr_data['id_pemohon'])->first();
                            if($arr_data['id_vendor'][0] != 'other'){
                                $vendor_id = $arr_data['id_vendor'][0];
                            }else{
                                $vendor_id = null;
                            }
                            if($arr_data['id_pemohon'] == $request->id_checker){
                                $insert[] = [
                                    'id_pemohon' => $arr_data['id_pemohon'],
                                    'dept_pemohon' => $employee->department_id,
                                    'date_pemohon' => date('Y-m-d H:i:s'),
                                    'tahun_usulan' => $arr_data['tahun_usulan'],
                                    'tahun_pelaksanaan' => $arr_data['tahun_pelaksanaan'],
                                    'kode' => $data['kode'],
                                    'id_peserta' => $arr_data['id_peserta'][$n],
                                    'kode_judul' => $year_now.$code_random,
                                    'judul' => $arr_data['judul'][0],
                                    'jenis_pelatihan' => $arr_data['jenis'][0],
                                    'sifat' => $arr_data['sifat'][0],
                                    'alasan' => $arr_data['alasan'][0],
                                    'bulan_pelaksanaan' => $arr_data['bulan_pelaksanaan'][0],
                                    'id_vendor' => $vendor_id,
                                    'nama_vendor' => $arr_data['vendor_other'][0],
                                    'biaya_fkt' => $arr_data['biaya_fkt'][0],
                                    'penginapan' => $arr_data['penginapan'][0],
                                    'transportasi' => $arr_data['transportasi'][0],
                                    'status' => 3,
                                    'id_checker' => $request->id_checker,
                                    'date_checker' => date('Y-m-d H:i:s'),
                                    'created_at' => Carbon::now(),
                                    'updated_at' => Carbon::now()
                                ];
                            }else{
                                $insert[] = [
                                    'id_pemohon' => $arr_data['id_pemohon'],
                                    'dept_pemohon' => $employee->department_id,
                                    'date_pemohon' => date('Y-m-d H:i:s'),
                                    'tahun_usulan' => $arr_data['tahun_usulan'],
                                    'tahun_pelaksanaan' => $arr_data['tahun_pelaksanaan'],
                                    'kode' => $data['kode'],
                                    'id_peserta' => $arr_data['id_peserta'][$n],
                                    'kode_judul' => $year_now.$code_random,
                                    'judul' => $arr_data['judul'][0],
                                    'jenis_pelatihan' => $arr_data['jenis'][0],
                                    'sifat' => $arr_data['sifat'][0],
                                    'alasan' => $arr_data['alasan'][0],
                                    'bulan_pelaksanaan' => $arr_data['bulan_pelaksanaan'][0],
                                    'id_vendor' => $vendor_id,
                                    'nama_vendor' => $arr_data['vendor_other'][0],
                                    'biaya_fkt' => $arr_data['biaya_fkt'][0],
                                    'penginapan' => $arr_data['penginapan'][0],
                                    'transportasi' => $arr_data['transportasi'][0],
                                    'status' => 2,
                                    'id_checker' => $request->id_checker,
                                    'created_at' => Carbon::now(),
                                    'updated_at' => Carbon::now()
                                ];
                            }
                        }
                        if($arr_data['id_pemohon'] == $request->id_checker){
                            if($i == 0){
                                //notification pic hrd
                                $users = User::whereHas(
                                    'roles', function($q){
                                        $q->where('id', 2);
                                    }
                                )->get();
                                if($users->isNotEmpty()){
                                    foreach($users as $key_user){
                                        if(!empty($key_user->email)){
                                            $qry_user = User::where('employee_id', $key_user->employee_id)->first();
                                            $details = [
                                                'greeting' => 'Hi '.$qry_user->name,
                                                'subject' => 'Verification Program Pelatihan Tahunan (PPT)',
                                                'body' => 'Ingin Menginformasikan bahwa ada usulan program pelatihan tahunan dengan nomor dokumen "'.$data['kode'].'" pemohon "'.$nama_pemohon.'" yang membutuhkan verification anda',
                                                'actionText' => 'Silahkan Login',
                                                'actionURL' => url('/hrd/training/ptt'),
                                                'thanks' => 'Terimakasih atas perhatiannya!!'
                                            ];
                                            //send mail
                                            $qry_user->notify(new AccountNotification($details));
                                        }
                                    }
                                }
                            }
                        }else{
                            //notification atasan departemen
                            if($i == 0){
                                $qry_user = User::where('employee_id', $request->id_checker)->first();
                                if(!empty($qry_user->email)){
                                    if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                                        $details = [
                                            'greeting' => 'Hi '.$qry_user->name,
                                            'subject' => 'PROGRAM PELATIHAN TAHUNAN',
                                            'body' => 'Ingin Menginformasikan bahwa ada usulan program pelatihan tahunan dengan nomor dokumen "'.$data['kode'].'" pemohon "'.$nama_pemohon.'" yang membutuhkan approval anda',
                                            'actionText' => 'Silahkan Login',
                                            'actionURL' => route('training.emp.fkt.ptt.approve.back'),
                                            'thanks' => 'Terimakasih atas perhatiannya!!'
                                        ];
                                    }else{
                                        $details = [
                                            'greeting' => 'Hi '.$qry_user->name,
                                            'subject' => 'PROGRAM PELATIHAN TAHUNAN',
                                            'body' => 'Ingin Menginformasikan bahwa ada usulan program pelatihan tahunan dengan nomor dokumen "'.$data['kode'].'" pemohon "'.$nama_pemohon.'" yang membutuhkan approval anda',
                                            'actionText' => 'Silahkan Login',
                                            'actionURL' => route('profile.back.approve.fkt.ptt'),
                                            'thanks' => 'Terimakasih atas perhatiannya!!'
                                        ];
                                    }
                                    //send mail
                                    $qry_user->notify(new AccountNotification($details));
                                }
                            }
                        }
                    }
                    $post = Trainingfkt::insert($insert);
        
                    //ttd pemohon
                    $date_qr = date('Ymd');
                    $insert_qr = new Qrcodefkt;
                    $insert_qr->kode_fkt = $data['kode'];
                    $insert_qr->qr = $date_qr.$user->employee_id;
                    $insert_qr->date_approval = date('Y-m-d H:i:s');
                    $insert_qr->type = 1;
                    $insert_qr->save();
        
                    //ttd atasan departemen
                    if($request->id_pemohon == $request->id_checker){
                        $date_qr = date('Ymd');
                        $insert_approved_qr = new Qrcodefkt;
                        $insert_approved_qr->kode_fkt = $request->kode;
                        $insert_approved_qr->qr = $date_qr.$user->employee_id;
                        $insert_approved_qr->date_approval = date('Y-m-d H:i:s');
                        $insert_approved_qr->type = 5;
                        $insert_approved_qr->save();
                    }
        
                    //insert log user activity
                    $insert_log = new Log;
                    $insert_log->user_id = $user->id;
                    $insert_log->ip_address = $request->ip();
                    $insert_log->action = 'insert';
                    $insert_log->description = 'Modify formulir kebutuhan pelatihan dengan nomor dokumen "'.$data['kode'].'" nama pemohon'.'"'.$nama_pemohon.'" tujuan "Program Training Tahunan (PTT)"';
                    $insert_log->save();
    
                    DB::commit();
    
                    return redirect(route('training.emp.index'))->with('tab_ptt','open tab')->with('status','Formulir Kebutuhan Training '.$nama_pemohon.' has been updated');
                }else{
                    return redirect(route('training.emp.index'))->with('tab_ptt','open tab')->with('error','Formulir Kebutuhan Pelatihan no changes');
                }
    
            }

            if($request->action == "revise"){
                $user = auth()->user();
                $data = $request->input();
                $cek_fkt = Trainingfkt::where('kode', $request->kode)->first();
                $nama_pemohon = $data['nama_pemohon'];
                $year_now = date("y");
                if(isset($request->no_urut)){
                    if($cek_fkt->status == 15 || $cek_fkt->status == 16){ //revise kepala departemen
                        $post_destroy = Trainingfkt::where('kode', $data['kode'])->delete();
                        for($i = 0; $i < count($data['no_urut']); $i++){
                            $code_random = random_int(100000, 999999);
                            if($data['id_peserta-'.$data['no_urut'][$i]] && $data['judul-'.$data['no_urut'][$i]] && $data['jenis_pelatihan-'.$data['no_urut'][$i]] && $data['sifat-'.$data['no_urut'][$i]] && $data['alasan-'.$data['no_urut'][$i]] && $data['bulan_pelaksanaan-'.$data['no_urut'][$i]] && $data['id_vendor-'.$data['no_urut'][$i]] && $data['vendor_other-'.$data['no_urut'][$i]] && $data['biaya_fkt-'.$data['no_urut'][$i]] && $data['penginapan-'.$data['no_urut'][$i]] && $data['transportasi-'.$data['no_urut'][$i]]){
                                $id_peserta = $data['id_peserta-'.$data['no_urut'][$i]];
                                $judul = $data['judul-'.$data['no_urut'][$i]];
                                $jenis = $data['jenis_pelatihan-'.$data['no_urut'][$i]];
                                $sifat = $data['sifat-'.$data['no_urut'][$i]];
                                $alasan = $data['alasan-'.$data['no_urut'][$i]];
                                $bulan_pelaksanaan = $data['bulan_pelaksanaan-'.$data['no_urut'][$i]];
                                $id_vendor = $data['id_vendor-'.$data['no_urut'][$i]];
                                $vendor_other = $data['vendor_other-'.$data['no_urut'][$i]];
                                $biaya_fkt = str_replace(".","",$data['biaya_fkt-'.$data['no_urut'][$i]]);
                                $penginapan = $data['penginapan-'.$data['no_urut'][$i]];
                                $transportasi = $data['transportasi-'.$data['no_urut'][$i]];
                            }
                            $arr_data = [
                                'id_pemohon' => $data['id_pemohon'],
                                'tahun_usulan' => $data['tahun_usulan'],
                                'tahun_pelaksanaan' => $data['tahun_pelaksanaan'],
                                'id_peserta' => $id_peserta,
                                'judul' => $judul,
                                'jenis' => $jenis,
                                'sifat' => $sifat,
                                'alasan' => $alasan,
                                'bulan_pelaksanaan' => $bulan_pelaksanaan,
                                'id_vendor' => $id_vendor,
                                'vendor_other' => $vendor_other,
                                'biaya_fkt' => $biaya_fkt,
                                'penginapan' => $penginapan,
                                'transportasi' => $transportasi
                            ];
            
                            for($n = 0; $n < count($arr_data['id_peserta']); $n++){
                                if($arr_data['id_vendor'][0] != 'other'){
                                    $vendor_id = $arr_data['id_vendor'][0];
                                }else{
                                    $vendor_id = null;
                                }
                                if($arr_data['id_pemohon'] == $request->id_checker){
                                    $insert[] = [
                                        'id_pemohon' => $arr_data['id_pemohon'],
                                        'date_pemohon' => date('Y-m-d H:i:s'),
                                        'tahun_usulan' => $arr_data['tahun_usulan'],
                                        'tahun_pelaksanaan' => $arr_data['tahun_pelaksanaan'],
                                        'kode' => $data['kode'],
                                        'id_peserta' => $arr_data['id_peserta'][$n],
                                        'kode_judul' => $year_now.$code_random,
                                        'judul' => $arr_data['judul'][0],
                                        'jenis_pelatihan' => $arr_data['jenis'][0],
                                        'sifat' => $arr_data['sifat'][0],
                                        'alasan' => $arr_data['alasan'][0],
                                        'bulan_pelaksanaan' => $arr_data['bulan_pelaksanaan'][0],
                                        'id_vendor' => $vendor_id,
                                        'nama_vendor' => $arr_data['vendor_other'][0],
                                        'biaya_fkt' => $arr_data['biaya_fkt'][0],
                                        'penginapan' => $arr_data['penginapan'][0],
                                        'transportasi' => $arr_data['transportasi'][0],
                                        'status' => 3,
                                        'id_checker' => $request->id_checker,
                                        'date_checker' => date('Y-m-d H:i:s'),
                                        'created_at' => Carbon::now(),
                                        'updated_at' => Carbon::now()
                                    ];
                                }else{
                                    $insert[] = [
                                        'id_pemohon' => $arr_data['id_pemohon'],
                                        'date_pemohon' => date('Y-m-d H:i:s'),
                                        'tahun_usulan' => $arr_data['tahun_usulan'],
                                        'tahun_pelaksanaan' => $arr_data['tahun_pelaksanaan'],
                                        'kode' => $data['kode'],
                                        'id_peserta' => $arr_data['id_peserta'][$n],
                                        'kode_judul' => $year_now.$code_random,
                                        'judul' => $arr_data['judul'][0],
                                        'jenis_pelatihan' => $arr_data['jenis'][0],
                                        'sifat' => $arr_data['sifat'][0],
                                        'alasan' => $arr_data['alasan'][0],
                                        'bulan_pelaksanaan' => $arr_data['bulan_pelaksanaan'][0],
                                        'id_vendor' => $vendor_id,
                                        'nama_vendor' => $arr_data['vendor_other'][0],
                                        'biaya_fkt' => $arr_data['biaya_fkt'][0],
                                        'penginapan' => $arr_data['penginapan'][0],
                                        'transportasi' => $arr_data['transportasi'][0],
                                        'status' => 2,
                                        'id_checker' => $request->id_checker,
                                        'date_checker' => null,
                                        'created_at' => Carbon::now(),
                                        'updated_at' => Carbon::now()
                                    ];
                                }
                            }
                            if($arr_data['id_pemohon'] == $request->id_checker){
                                if($i == 0){
                                    //notification pic hrd
                                    $users = User::whereHas(
                                        'roles', function($q){
                                            $q->where('id', 2);
                                        }
                                    )->get();
                                    if($users->isNotEmpty()){
                                        foreach($users as $key_user){
                                            if(!empty($key_user->email)){
                                                $qry_user = User::where('employee_id', $key_user->employee_id)->first();
                                                $details = [
                                                    'greeting' => 'Hi '.$qry_user->name,
                                                    'subject' => 'Verification Program Pelatihan Tahunan (PPT)',
                                                    'body' => 'Ingin Menginformasikan bahwa ada usulan program pelatihan tahunan dengan nomor dokumen "'.$data['kode'].'" pemohon "'.$nama_pemohon.'" yang membutuhkan verification anda',
                                                    'actionText' => 'Silahkan Login',
                                                    'actionURL' => url('/hrd/training/ptt'),
                                                    'thanks' => 'Terimakasih atas perhatiannya!!'
                                                ];
                                                //send mail
                                                $qry_user->notify(new AccountNotification($details));
                                            }
                                        }
                                    }
                                }
                            }else{
                                //notification atasan departemen
                                if($i == 0){
                                    $qry_user = User::where('employee_id', $request->id_checker)->first();
                                    if(!empty($qry_user->email)){
                                        if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                                            $details = [
                                                'greeting' => 'Hi '.$qry_user->name,
                                                'subject' => 'PROGRAM PELATIHAN TAHUNAN',
                                                'body' => 'Ingin Menginformasikan bahwa ada usulan program pelatihan tahunan dengan nomor dokumen "'.$data['kode'].'" pemohon "'.$nama_pemohon.'" yang membutuhkan approval anda',
                                                'actionText' => 'Silahkan Login',
                                                'actionURL' => route('training.emp.fkt.ptt.approve.back'),
                                                'thanks' => 'Terimakasih atas perhatiannya!!'
                                            ];
                                        }else{
                                            $details = [
                                                'greeting' => 'Hi '.$qry_user->name,
                                                'subject' => 'PROGRAM PELATIHAN TAHUNAN',
                                                'body' => 'Ingin Menginformasikan bahwa ada usulan program pelatihan tahunan dengan nomor dokumen "'.$data['kode'].'" pemohon "'.$nama_pemohon.'" yang membutuhkan approval anda',
                                                'actionText' => 'Silahkan Login',
                                                'actionURL' => route('profile.back.approve.fkt.ptt'),
                                                'thanks' => 'Terimakasih atas perhatiannya!!'
                                            ];
                                        }
                                        //send mail
                                        $qry_user->notify(new AccountNotification($details));
                                    }
                                }
                            }
                        }
                        $post = Trainingfkt::insert($insert);
                        //ttd pemohon
                        $delete_ttd = Qrcodefkt::where('kode_fkt', $data['kode'])->delete();
                        $date_qr = date('Ymd');
                        $insert_qr = new Qrcodefkt;
                        $insert_qr->kode_fkt = $data['kode'];
                        $insert_qr->qr = $date_qr.$user->employee_id;
                        $insert_qr->date_approval = date('Y-m-d H:i:s');
                        $insert_qr->type = 1;
                        $insert_qr->save();
                        //ttd atasan departemen
                        if($request->id_pemohon == $request->id_checker){
                            $insert_approved_qr = new Qrcodefkt;
                            $insert_approved_qr->kode_fkt = $data['kode'];
                            $insert_approved_qr->qr = $date_qr.$user->employee_id;
                            $insert_approved_qr->date_approval = date('Y-m-d H:i:s');
                            $insert_approved_qr->type = 5;
                            $insert_approved_qr->save();
                        }
                        //insert log user activity
                        $insert_log = new Log;
                        $insert_log->user_id = $user->id;
                        $insert_log->ip_address = $request->ip();
                        $insert_log->action = 'insert';
                        $insert_log->description = 'Modify formulir kebutuhan pelatihan dengan nomor dokumen "'.$data['kode'].'" nama pemohon'.'"'.$nama_pemohon.'" tujuan "Program Training Tahunan (PTT)"';
                        $insert_log->save();

                    }

                    DB::commit();

                    return redirect(route('training.emp.index'))->with('tab_ptt','open tab')->with('status','Formulir Kebutuhan Training '.$nama_pemohon.' has been updated');
                }else{
                    return redirect(route('training.emp.index'))->with('tab_ptt','open tab')->with('error','Formulir Kebutuhan Pelatihan no changes');
                }
            }
        } catch (Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }
    ///fkt approve
    public function emp_fkt_ptt_approved(Request $request){
        $user = auth()->user();
        $cek_approve_checker = Trainingfkt::where('id_checker', $user->employee_id)
        ->whereNotNull('date_pemohon')
        ->whereNull('date_checker')->count();
    
        if($cek_approve_checker > 0){
            $data = Trainingfkt::where(function ($data) use ($user) {
                $data->where('id_checker', $user->employee_id);
            })->whereNotNull('date_pemohon')->where('status',2)->get()->unique('tahun_usulan');
        }else{
            $data = array();            
        }
        if ($request->ajax()) {          
            return DataTables::of($data)
                ->addColumn('tahun_usulan', function($data){
                    return $data['tahun_usulan'];
                })
                ->addColumn('total_pengajuan', function($data){
                    $qry_user = auth()->user();
                    $jml_usulan_checker = Trainingfkt::where('id_checker', $qry_user->employee_id)
                        ->where('tahun_usulan', $data['tahun_usulan'])
                        ->whereNotNull('date_pemohon')
                        ->whereNull('date_checker')->where('status', 2)->get()->unique('kode')->count();
                    $total = $jml_usulan_checker;
                    return $total;
                })
                ->addColumn('action', function ($data) {
                    $qry_user = auth()->user();  
                    $jml_approve_checker = Trainingfkt::where('id_checker', $qry_user->employee_id)
                        ->where('tahun_usulan', $data['tahun_usulan'])
                        ->whereNotNull('date_pemohon')
                        ->whereNull('date_checker')
                        ->where('status', 2)
                        ->get()->unique('kode')->count();
                    $count_jml = $jml_approve_checker;
                    if($count_jml > 0){
                        $button = '<a href="'. route('training.emp.fkt.ptt.approved.form', encrypt($data['tahun_usulan'])).'" data-toggle="tooltip" title="Approved" class="btn btn-info btn-sm"><i class="ri-task-line"></i> <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">'.$count_jml.' <span class="visually-hidden">unread messages</span></span></a>';
                    }else{
                        $button = '<a href="'. route('training.emp.fkt.ptt.approved.form', encrypt($data['tahun_usulan'])).'" data-toggle="tooltip" title="Approved" class="btn btn-info btn-sm"><i class="ri-task-line"></i></a>';
                    }
                    return $button;
                })                
                ->rawColumns(['action','tahun_usulan','jumlah_usulan'])
                ->addIndexColumn()
                ->make(true);
        }
    }
    public function emp_fkt_ptt_approved_form(Request $request, $id){
        $user = auth()->user();
        $tahun_usulan = decrypt($id);
        $cek_approve_checker = Trainingfkt::where('id_checker', $user->employee_id)
            ->whereNotNull('date_pemohon')
            ->whereNull('date_checker')->count();

        if($cek_approve_checker > 0){
            $query_fkt = Trainingfkt::where(function ($query_fkt) use ($user) {
                $query_fkt->where('id_checker', $user->employee_id);
            })->where('tahun_usulan', $tahun_usulan)
            ->whereNotNull('date_pemohon')
            ->where('status',2)->get()->unique('kode');
        }else{
            $query_fkt = array();
        }

        return view('pages.employee.training.ptt.form-fkt-approve', compact('user','tahun_usulan','query_fkt'));
    }
    public function emp_fpkt_ptt_approved_store(Request $request){
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $query = Trainingfkt::where('kode', decrypt($request->kode))->first();
            $post = Trainingfkt::where('kode', decrypt($request->kode))->update([
                'date_checker' => date('Y-m-d H:i:s'),
                'status' => 3
            ]);
            //atasan departemen ttd
            $date_qr = date('Ymd');
            $insert_approved_qr = new Qrcodefkt;
            $insert_approved_qr->kode_fkt = $query->kode;
            $insert_approved_qr->qr = $date_qr.$user->employee_id;
            $insert_approved_qr->date_approval = date('Y-m-d H:i:s');
            $insert_approved_qr->type = 5;
            $insert_approved_qr->save();

            //notification pic hrd
            $users = User::whereHas(
                'roles', function($q){
                    $q->where('id', 2);
                }
            )->get();
            if($users->isNotEmpty()){
                foreach($users as $key_user){
                    if(!empty($key_user->email)){
                        $qry_user = User::where('employee_id', $key_user->employee_id)->first();
                        $details = [
                            'greeting' => 'Hi '.$qry_user->name,
                            'subject' => 'Pengajuan Biaya Pelatihan Tahunan',
                            'body' => 'Ingin Menginformasikan bahwa ada pengajuan pelatihan "'.$query->judul.'" yang membutuhkan verifikasi anda',
                            'actionText' => 'Silahkan Login',
                            'actionURL' => url('/hrd/training/ptt'),
                            'thanks' => 'Terimakasih atas perhatiannya!!'
                        ];
                        //send mail
                        $qry_user->notify(new AccountNotification($details));
                    }
                }
            }

            //update log user activity
            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'approved';
            $insert->description = 'Approved formulir kebutuhan pelatihan dengan nama pemohon'.'"'.$query->pemohon->fullname.'" tujuan "Pengajuan Biaya Pelatihan Tahunan"';
            $insert->save();

            DB::commit();

            return response()->json(['message' => "$query->kode has been approved"], 200);
        }catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }
    public function emp_fpkt_ptt_revised_store(Request $request){
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $query = Trainingfkt::where('kode', decrypt($request->kode))->first();
            //update status revisi
            $post = Trainingfkt::where('kode', decrypt($request->kode))->update([
                'status' => 15
            ]);
            //catatan revisi
            $insert_ctt = new Logcatatantraining;
            $insert_ctt->id_user = $user->employee_id;
            $insert_ctt->kode_fkt = $query->kode;
            $insert_ctt->ip_address = $request->ip();
            $insert_ctt->action = 'revise atasan';
            $insert_ctt->catatan = $request->catatan_revise;
            $insert_ctt->save();

            //notification pemohon
            $qry_user = User::where('employee_id', $query->id_pemohon)->first();
            if(!empty($qry_user->email)){
                if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                    $details = [
                        'greeting' => 'Hi '.$qry_user->name,
                        'subject' => 'PROGRAM TRAINING INSIDENTIL',
                        'body' => 'Ingin Menginformasikan bahwa ada usulan program pelatihan tahunan dengan nomor dokumen "'.$query->kode.'" pemohon "'.$query->pemohon->fullname.'" yang direvisi oleh Atasan Departemen',
                        'actionText' => 'Silahkan Login',
                        'actionURL' => route('training.emp.fkt.ptt.back'),
                        'thanks' => 'Terimakasih atas perhatiannya!!'
                    ];
                }else{
                    $details = [
                        'greeting' => 'Hi '.$qry_user->name,
                        'subject' => 'PROGRAM TRAINING INSIDENTIL',
                        'body' => 'Ingin Menginformasikan bahwa ada usulan program pelatihan tahunan dengan nomor dokumen "'.$query->kode.'" pemohon "'.$query->pemohon->fullname.'" yang direvisi oleh Atasan Departemen',
                        'actionText' => 'Silahkan Login',
                        'actionURL' => route('profile.back.fkt.ptt'),
                        'thanks' => 'Terimakasih atas perhatiannya!!'
                    ];
                }
                //send mail
                $qry_user->notify(new AccountNotification($details));
            }

            //update log user activity
            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'revise';
            $insert->description = 'Revise formulir kebutuhan pelatihan dengan nama pemohon'.'"'.$query->pemohon->fullname.'" tujuan "Pengajuan Biaya Pelatihan Tahunan"';
            $insert->save();

            DB::commit();

            return response()->json(['message' => "$query->kode has been revised"], 200);
        }catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }
    public function emp_fpkt_ptt_rejected_store(Request $request){
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $query = Trainingfkt::where('kode', decrypt($request->kode))->first();
            //update status revisi
            $post = Trainingfkt::where('kode', decrypt($request->kode))->update([
                'status' => 17
            ]);
            //catatan revisi
            $insert_ctt = new Logcatatantraining;
            $insert_ctt->id_user = $user->employee_id;
            $insert_ctt->kode_fkt = $query->kode;
            $insert_ctt->ip_address = $request->ip();
            $insert_ctt->action = 'reject atasan';
            $insert_ctt->catatan = $request->catatan_reject;
            $insert_ctt->save();

            //notification pemohon
            $qry_user = User::where('employee_id', $query->id_pemohon)->first();
            if(!empty($qry_user->email)){
                if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                    $details = [
                        'greeting' => 'Hi '.$qry_user->name,
                        'subject' => 'PROGRAM TRAINING INSIDENTIL',
                        'body' => 'Ingin Menginformasikan bahwa ada usulan program pelatihan tahunan dengan nomor dokumen "'.$query->kode.'" pemohon "'.$query->pemohon->fullname.'" yang direject oleh Atasan Departemen',
                        'actionText' => 'Silahkan Login',
                        'actionURL' => route('training.emp.fkt.ptt.back'),
                        'thanks' => 'Terimakasih atas perhatiannya!!'
                    ];
                }else{
                    $details = [
                        'greeting' => 'Hi '.$qry_user->name,
                        'subject' => 'PROGRAM TRAINING INSIDENTIL',
                        'body' => 'Ingin Menginformasikan bahwa ada usulan program pelatihan tahunan dengan nomor dokumen "'.$query->kode.'" pemohon "'.$query->pemohon->fullname.'" yang direject oleh Atasan Departemen',
                        'actionText' => 'Silahkan Login',
                        'actionURL' => route('profile.back.fkt.ptt'),
                        'thanks' => 'Terimakasih atas perhatiannya!!'
                    ];
                }
                //send mail
                $qry_user->notify(new AccountNotification($details));
            }

            //update log user activity
            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'reject';
            $insert->description = 'Reject formulir kebutuhan pelatihan dengan nama pemohon'.'"'.$query->pemohon->fullname.'" tujuan "Pengajuan Biaya Pelatihan Tahunan"';
            $insert->save();

            DB::commit();

            return response()->json(['message' => "$query->kode has been rejected"], 200);
        }catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }
    ///back ptt
    public function emp_fkt_ptt_back(Request $request){
        return redirect(route('training.emp.index'))->with('tab_ptt','open tab');
    }
    public function  emp_fkt_ptt_approve_back(Request $request){
        return redirect(route('training.emp.index'))->with('tab_approve_ptt','open tab');
    }
    ///fpkt
    public function emp_fpkt_ptt_form(Request $request, $id){
        $user = auth()->user();
        $kode = $id;
        $id = decrypt($id);
        $fkt = Trainingfkt::where('id', $id)->first();
        $fpkt = Trainingfpkt::where('id_fkt', $id)->first(); 
        $arr_fpkt = Trainingfpkt::where('id_fkt', $id)->get(); 
        $cek_pemohon = Trainingfkt::where('id', $id)->where('id_pemohon', $user->employee_id)->first(); 
        $cek_peserta = Trainingfkt::where('id', $id)->where('id_peserta', $user->employee_id)->first(); 
        $cek_atasan = Trainingfkt::where('id', $id)->where('id_penilai', $user->employee_id)->first(); 
        return view('pages.employee.training.ptt.form-fpkt', compact('kode','user','fkt','fpkt','cek_pemohon','cek_peserta','cek_atasan','arr_fpkt'));
    }
    public function emp_fpkt_ptt_store(Request $request){
        DB::beginTransaction();
        try {
            if($request->action == 'pemohon'){
                $user = auth()->user();
                $data = $request->input();
                $fkt = Trainingfkt::find($data['id_fkt']);
    
                $code_random = random_int(100000, 999999);
                $post_update = Trainingfkt::where('kode', $fkt->kode)->where('judul', $fkt->judul)->update([
                    'kode_judul' => $code_random
                ]);
    
                if(isset($data['no_urut'])){
                    for($i = 0; $i < count($data['no_urut']); $i++){
                        if($data['tujuan-'.$data['no_urut'][$i]] && $data['kompetensi-'.$data['no_urut'][$i]] && $data['skill-'.$data['no_urut'][$i]] && $data['level_peserta-'.$data['no_urut'][$i]] && $data['level_atasan-'.$data['no_urut'][$i]] && $data['level_rata-'.$data['no_urut'][$i]] && $data['level_kebutuhan-'.$data['no_urut'][$i]]){
                            $tujuan = $data['tujuan-'.$data['no_urut'][$i]];
                            $kompetensi = $data['kompetensi-'.$data['no_urut'][$i]];
                            $skill = $data['skill-'.$data['no_urut'][$i]];
                            $level_peserta = $data['level_peserta-'.$data['no_urut'][$i]];
                            $arr_peserta[] = $data['level_peserta-'.$data['no_urut'][$i]];
                            $level_atasan = $data['level_atasan-'.$data['no_urut'][$i]];
                            $arr_atasan[] = $data['level_atasan-'.$data['no_urut'][$i]];
                            $level_rata = $data['level_rata-'.$data['no_urut'][$i]];
                            $level_kebutuhan = $data['level_kebutuhan-'.$data['no_urut'][$i]];
                            
                            $arr_data[] = [
                                'id_fkt' => $data['id_fkt'],
                                'latar_belakang' => $data['latar_belakang'],
                                'tujuan' => $tujuan[0],
                                'kompetensi' => $kompetensi[0],
                                'skill' => $skill[0],
                                'level_peserta' => $level_peserta[0],
                                'level_atasan' => $level_atasan[0],
                                'level_rata' => $level_rata[0],
                                'level_kebutuhan' => $level_kebutuhan[0],
                                'catatan' => $data['catatan'],
                                'analisa_satu' => $data['catatan_satu'],
                                'analisa_dua' => $data['catatan_dua'],
                                'analisa_tiga' => $data['catatan_tiga']
                            ];
                        }
                    }

                    $sum_atasan = array_sum(array_column($arr_atasan,'0'));
                    $sum_peserta = array_sum(array_column($arr_peserta,'0'));
                    if($sum_peserta > 0){          
                        $fkt->update([
                            'date_peserta' => date('Y-m-d H:i:s')
                        ]);
                        foreach($arr_data as $key => $value){
                            $insert = new Trainingfpkt;
                            $insert->id_fkt = $value['id_fkt'];
                            $insert->latar_belakang = $value['latar_belakang'];
                            $insert->tujuan = $value['tujuan'];
                            $insert->kompetensi = $value['kompetensi'];
                            $insert->skill = $value['skill'];
                            $insert->level_peserta = $value['level_peserta'];
                            $insert->level_atasan = $value['level_atasan'];
                            $insert->level_rata = $value['level_rata'];
                            $insert->level_kebutuhan = $value['level_kebutuhan'];
                            $insert->catatan = $value['catatan'];
                            $insert->analisa_satu = $value['analisa_satu'];
                            $insert->analisa_dua = $value['analisa_dua'];
                            $insert->analisa_tiga = $value['analisa_tiga'];
                            $insert->id_peserta = $fkt->id_peserta;
                            $insert->date_peserta = $fkt->date_peserta;
                            $insert->status = 10;
                            $insert->save();
                        }
                        //ttd peserta
                        //ttd fpkt
                        $date_qr = date('Ymd');
                        $insert_fpkt_qr = new Qrcodefpkt;
                        $insert_fpkt_qr->id_fkt = $fkt->id;
                        $insert_fpkt_qr->qr = $date_qr.$fkt->id_peserta;
                        $insert_fpkt_qr->date_approval = date('Y-m-d H:i:s');
                        $insert_fpkt_qr->type = 1;
                        $insert_fpkt_qr->save();
    
                        //notification atasan penilai
                        if(!empty($fkt->penilai->email)){
                            $qry_user = User::where('employee_id', $fkt->id_penilai)->first();
                            if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                                $details = [
                                    'greeting' => 'Hi '.$fkt->penilai->fullname,
                                    'subject' => 'Penilaian Kebutuhan Training',
                                    'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$fkt->judul.'" yang membutuhkan penilaian anda',
                                    'actionText' => 'Silahkan Login',
                                    'actionURL' => url('/employee/training/fpkt/ptt/'.encrypt($fkt->id).'/form'),
                                    'thanks' => 'Terimakasih atas perhatiannya!!'
                                ];
                            }else{
                                $details = [
                                    'greeting' => 'Hi '.$fkt->penilai->fullname,
                                    'subject' => 'Penilaian Kebutuhan Training',
                                    'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$fkt->judul.'" yang membutuhkan penilaian anda',
                                    'actionText' => 'Silahkan Login',
                                    'actionURL' => url('/mytrainingfpkt/ptt/'.encrypt($fkt->id).'/form'),
                                    'thanks' => 'Terimakasih atas perhatiannya!!'
                                ];
                            }
                            //send mail
                            $qry_user->notify(new AccountNotification($details));
                        }
    
                        //insert log user activity
                        $insert_log = new Log;
                        $insert_log->user_id = $user->id;
                        $insert_log->ip_address = $request->ip();
                        $insert_log->action = 'update';
                        $insert_log->description = 'Modify "'.$fkt->judul.'" dengan nama pemohon'.'"'.$fkt->pemohon->fullname.'"';
                        $insert_log->save();
                    }else{
                        if($sum_atasan > 0){
                            $fkt->update([
                                'date_penilai' => date('Y-m-d H:i:s')
                            ]);
                            foreach($arr_data as $key => $value){
                                $insert = new Trainingfpkt;
                                $insert->id_fkt = $value['id_fkt'];
                                $insert->latar_belakang = $value['latar_belakang'];
                                $insert->tujuan = $value['tujuan'];
                                $insert->kompetensi = $value['kompetensi'];
                                $insert->skill = $value['skill'];
                                $insert->level_peserta = $value['level_peserta'];
                                $insert->level_atasan = $value['level_atasan'];
                                $insert->level_rata = $value['level_rata'];
                                $insert->level_kebutuhan = $value['level_kebutuhan'];
                                $insert->catatan = $value['catatan'];
                                $insert->analisa_satu = $value['analisa_satu'];
                                $insert->analisa_dua = $value['analisa_dua'];
                                $insert->analisa_tiga = $value['analisa_tiga'];
                                $insert->id_atasan = $fkt->id_penilai;
                                $insert->date_atasan = $fkt->date_penilai;
                                $insert->status = 10;
                                $insert->save();
                            }
                            //ttd atasan
                            //ttd fpkt
                            $date_qr = date('Ymd');
                            $insert_fpkt_qr = new Qrcodefpkt;
                            $insert_fpkt_qr->id_fkt = $fkt->id;
                            $insert_fpkt_qr->qr = $date_qr.$fkt->id_penilai;
                            $insert_fpkt_qr->date_approval = date('Y-m-d H:i:s');
                            $insert_fpkt_qr->type = 2;
                            $insert_fpkt_qr->save();

                            //notification peserta
                            if(!empty($fkt->peserta->email)){
                                $qry_user = User::where('employee_id', $fkt->id_peserta)->first();
                                if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                                    $details = [
                                        'greeting' => 'Hi '.$fkt->peserta->fullname,
                                        'subject' => 'Penilaian Kebutuhan Training',
                                        'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$fkt->judul.'" yang membutuhkan penilaian anda',
                                        'actionText' => 'Silahkan Login',
                                        'actionURL' => url('/employee/training/fpkt/ptt/'.encrypt($fkt->id).'/form'),
                                        'thanks' => 'Terimakasih atas perhatiannya!!'
                                    ];
                                }else{
                                    $details = [
                                        'greeting' => 'Hi '.$fkt->peserta->fullname,
                                        'subject' => 'Penilaian Kebutuhan Training',
                                        'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$fkt->judul.'" yang membutuhkan penilaian anda',
                                        'actionText' => 'Silahkan Login',
                                        'actionURL' => url('/mytrainingfpkt/ptt/'.encrypt($fkt->id).'/form'),
                                        'thanks' => 'Terimakasih atas perhatiannya!!'
                                    ];
                                }
                                //send mail
                                $qry_user->notify(new AccountNotification($details));
                            }
        
                            //insert log user activity
                            $insert_log = new Log;
                            $insert_log->user_id = $user->id;
                            $insert_log->ip_address = $request->ip();
                            $insert_log->action = 'update';
                            $insert_log->description = 'Modify "'.$fkt->judul.'" dengan nama pemohon'.'"'.$fkt->pemohon->fullname.'"';
                            $insert_log->save();
                        }else{
                            foreach($arr_data as $key => $value){
                                $insert = new Trainingfpkt;
                                $insert->id_fkt = $value['id_fkt'];
                                $insert->latar_belakang = $value['latar_belakang'];
                                $insert->tujuan = $value['tujuan'];
                                $insert->kompetensi = $value['kompetensi'];
                                $insert->skill = $value['skill'];
                                $insert->level_peserta = $value['level_peserta'];
                                $insert->level_atasan = $value['level_atasan'];
                                $insert->level_rata = $value['level_rata'];
                                $insert->level_kebutuhan = $value['level_kebutuhan'];
                                $insert->catatan = $value['catatan'];
                                $insert->analisa_satu = $value['analisa_satu'];
                                $insert->analisa_dua = $value['analisa_dua'];
                                $insert->analisa_tiga = $value['analisa_tiga'];
                                $insert->status = 9;
                                $insert->save();
                            }  
                            
                            //notification peserta
                            if(!empty($fkt->peserta->email)){
                                $qry_user = User::where('employee_id', $fkt->id_peserta)->first();
                                if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                                    $details = [
                                        'greeting' => 'Hi '.$fkt->peserta->fullname,
                                        'subject' => 'Penilaian Kebutuhan Training',
                                        'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$fkt->judul.'" yang membutuhkan penilaian anda',
                                        'actionText' => 'Silahkan Login',
                                        'actionURL' => url('/employee/training/fpkt/ptt/'.encrypt($fkt->id).'/form'),
                                        'thanks' => 'Terimakasih atas perhatiannya!!'
                                    ];
                                }else{
                                    $details = [
                                        'greeting' => 'Hi '.$fkt->peserta->fullname,
                                        'subject' => 'Penilaian Kebutuhan Training',
                                        'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$fkt->judul.'" yang membutuhkan penilaian anda',
                                        'actionText' => 'Silahkan Login',
                                        'actionURL' => url('/mytrainingfpkt/ptt/'.encrypt($fkt->id).'/form'),
                                        'thanks' => 'Terimakasih atas perhatiannya!!'
                                    ];
                                }
                                //send mail
                                $qry_user->notify(new AccountNotification($details));
                            }
        
                            //notification atasan penilai
                            if(!empty($fkt->penilai->email)){
                                $qry_user = User::where('employee_id', $fkt->id_penilai)->first();
                                if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                                    $details = [
                                        'greeting' => 'Hi '.$fkt->penilai->fullname,
                                        'subject' => 'Penilaian Kebutuhan Training',
                                        'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$fkt->judul.'" yang membutuhkan penilaian anda',
                                        'actionText' => 'Silahkan Login',
                                        'actionURL' => url('/employee/training/fpkt/ptt/'.encrypt($fkt->id).'/form'),
                                        'thanks' => 'Terimakasih atas perhatiannya!!'
                                    ];
                                }else{
                                    $details = [
                                        'greeting' => 'Hi '.$fkt->penilai->fullname,
                                        'subject' => 'Penilaian Kebutuhan Training',
                                        'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$fkt->judul.'" yang membutuhkan penilaian anda',
                                        'actionText' => 'Silahkan Login',
                                        'actionURL' => url('/mytrainingfpkt/ptt/'.encrypt($fkt->id).'/form'),
                                        'thanks' => 'Terimakasih atas perhatiannya!!'
                                    ];
                                }
                                //send mail
                                $qry_user->notify(new AccountNotification($details));
                            }
        
                            //insert log user activity
                            $insert_log = new Log;
                            $insert_log->user_id = $user->id;
                            $insert_log->ip_address = $request->ip();
                            $insert_log->action = 'updated';
                            $insert_log->description = 'Modify "'.$fkt->judul.'" dengan nama pemohon'.'"'.$fkt->pemohon->fullname.'"';
                            $insert_log->save();
                        }
                    }
                    
                    $page = redirect(route('training.emp.fkt.ptt.detail', encrypt($fkt->kode)))->with('status','Formulir Penilaian Kebutuhan Training '.$fkt->pemohon->fullname.' has been updated');
                }else{
                    $page = redirect(route('training.emp.fkt.ptt.detail', encrypt($fkt->kode)))->with('error','Formulir Penilaian Kebutuhan Training no changes');
                }
            }

            if($request->action == 'peserta'){
                $user = auth()->user();
                $data = $request->input();
                $fkt = Trainingfkt::find($data['id_fkt']);            
                
                for($i = 0; $i < count($data['no_urut']); $i++){
                    if($data['tujuan-'.$data['no_urut'][$i]] && $data['kompetensi-'.$data['no_urut'][$i]] && $data['skill-'.$data['no_urut'][$i]] && $data['level_peserta-'.$data['no_urut'][$i]] && $data['level_atasan-'.$data['no_urut'][$i]] && $data['level_rata-'.$data['no_urut'][$i]] && $data['level_kebutuhan-'.$data['no_urut'][$i]]){
                        $tujuan = $data['tujuan-'.$data['no_urut'][$i]];
                        $kompetensi = $data['kompetensi-'.$data['no_urut'][$i]];
                        $skill = $data['skill-'.$data['no_urut'][$i]];
                        $level_peserta = $data['level_peserta-'.$data['no_urut'][$i]];
                        $arr_peserta[] = $data['level_peserta-'.$data['no_urut'][$i]];
                        $level_atasan = $data['level_atasan-'.$data['no_urut'][$i]];
                        $arr_atasan[] = $data['level_atasan-'.$data['no_urut'][$i]];
                        $level_rata = $data['level_rata-'.$data['no_urut'][$i]];
                        $level_kebutuhan = $data['level_kebutuhan-'.$data['no_urut'][$i]];
                        
                        $arr_data[] = [
                            'id_fkt' => $data['id_fkt'],
                            'latar_belakang' => $data['latar_belakang'],
                            'tujuan' => $tujuan[0],
                            'kompetensi' => $kompetensi[0],
                            'skill' => $skill[0],
                            'level_peserta' => $level_peserta[0],
                            'level_atasan' => $level_atasan[0],
                            'level_rata' => $level_rata[0],
                            'level_kebutuhan' => $level_kebutuhan[0],
                            'catatan' => $data['catatan'],
                            'analisa_satu' => $data['catatan_satu'],
                            'analisa_dua' => $data['catatan_dua'],
                            'analisa_tiga' => $data['catatan_tiga']
                        ];
                    }
                }
                $sum_peserta = array_sum(array_column($arr_peserta,'0'));
                $sum_atasan = array_sum(array_column($arr_atasan,'0'));
                if($sum_peserta > 0){
                    $delete = Trainingfpkt::where('id_fkt', $data['id_fkt'])->delete();
                    $fkt->update([
                        'date_peserta' => date('Y-m-d H:i:s')
                    ]);
                    foreach($arr_data as $key => $value){
                        $insert = new Trainingfpkt;
                        $insert->id_fkt = $value['id_fkt'];
                        $insert->latar_belakang = $value['latar_belakang'];
                        $insert->tujuan = $value['tujuan'];
                        $insert->kompetensi = $value['kompetensi'];
                        $insert->skill = $value['skill'];
                        $insert->level_peserta = $value['level_peserta'];
                        $insert->level_atasan = $value['level_atasan'];
                        $insert->level_rata = $value['level_rata'];
                        $insert->level_kebutuhan = $value['level_kebutuhan'];
                        $insert->catatan = $value['catatan'];
                        $insert->analisa_satu = $value['analisa_satu'];
                        $insert->analisa_dua = $value['analisa_dua'];
                        $insert->analisa_tiga = $value['analisa_tiga'];
                        $insert->id_peserta = $fkt->id_peserta;
                        $insert->date_peserta = $fkt->date_peserta;
                        if($sum_atasan > 0){
                            $insert->id_atasan = $fkt->id_penilai;
                            $insert->date_atasan = $fkt->date_penilai;
                            $insert->status = 11;
                        }else{
                            $insert->status = 10;
                        }
                        $insert->save();
                    }
                    //ttd peserta
                    //ttd fpkt
                    $date_qr = date('Ymd');
                    $insert_fpkt_qr = new Qrcodefpkt;
                    $insert_fpkt_qr->id_fkt = $fkt->id;
                    $insert_fpkt_qr->qr = $date_qr.$fkt->id_peserta;
                    $insert_fpkt_qr->date_approval = date('Y-m-d H:i:s');
                    $insert_fpkt_qr->type = 1;
                    $insert_fpkt_qr->save();

                    if($sum_atasan > 0){
                        //notification atasan departemen
                        if(!empty($fkt->checker->email)){
                            $qry_user = User::where('employee_id', $fkt->id_checker)->first();
                            if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                                $details = [
                                    'greeting' => 'Hi '.$fkt->checker->fullname,
                                    'subject' => 'Penilaian Kebutuhan Training',
                                    'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$fkt->judul.'" yang membutuhkan approval anda',
                                    'actionText' => 'Silahkan Login',
                                    'actionURL' => url('/employee/training/fkt/ptt/approved/'.encrypt($fkt->tahun_usulan).'/form'),
                                    'thanks' => 'Terimakasih atas perhatiannya!!'
                                ];
                            }else{
                                $details = [
                                    'greeting' => 'Hi '.$fkt->checker->fullname,
                                    'subject' => 'Penilaian Kebutuhan Training',
                                    'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$fkt->judul.'" yang membutuhkan approval anda',
                                    'actionText' => 'Silahkan Login',
                                    'actionURL' => url('/mytraining/fkt/ptt/approved/'.encrypt($fkt->tahun_usulan).'/form'),
                                    'thanks' => 'Terimakasih atas perhatiannya!!'
                                ];
                            }
                            //send mail
                            $qry_user->notify(new AccountNotification($details));
                        }
                    }

                    //insert log user activity
                    $insert_log = new Log;
                    $insert_log->user_id = $user->id;
                    $insert_log->ip_address = $request->ip();
                    $insert_log->action = 'update';
                    $insert_log->description = 'Modify "'.$fkt->judul.'" dengan nama pemohon'.'"'.$fkt->pemohon->fullname.'"';
                    $insert_log->save();

                    $page =  redirect(route('training.emp.fkt.ptt.detail', encrypt($fkt->kode)))->with('status','Formulir Penilaian Kebutuhan Training '.$fkt->pemohon->fullname.' has been updated');
                }else{
                    $page =  redirect(route('training.emp.fkt.ptt.detail', encrypt($fkt->kode)))->with('error','Formulir Penilaian Kebutuhan Training no changes');
                }
            }

            if($request->action == 'atasan'){
                $user = auth()->user();
                $data = $request->input();
                $fkt = Trainingfkt::find($data['id_fkt']);

                for($i = 0; $i < count($data['no_urut']); $i++){
                    if($data['tujuan-'.$data['no_urut'][$i]] && $data['kompetensi-'.$data['no_urut'][$i]] && $data['skill-'.$data['no_urut'][$i]] && $data['level_peserta-'.$data['no_urut'][$i]] && $data['level_atasan-'.$data['no_urut'][$i]] && $data['level_rata-'.$data['no_urut'][$i]] && $data['level_kebutuhan-'.$data['no_urut'][$i]]){
                        $tujuan = $data['tujuan-'.$data['no_urut'][$i]];
                        $kompetensi = $data['kompetensi-'.$data['no_urut'][$i]];
                        $skill = $data['skill-'.$data['no_urut'][$i]];
                        $level_peserta = $data['level_peserta-'.$data['no_urut'][$i]];
                        $arr_peserta[] = $data['level_peserta-'.$data['no_urut'][$i]];
                        $level_atasan = $data['level_atasan-'.$data['no_urut'][$i]];
                        $arr_atasan[] = $data['level_atasan-'.$data['no_urut'][$i]];
                        $level_rata = $data['level_rata-'.$data['no_urut'][$i]];
                        $level_kebutuhan = $data['level_kebutuhan-'.$data['no_urut'][$i]];
                        
                        $arr_data[] = [
                            'id_fkt' => $data['id_fkt'],
                            'latar_belakang' => $data['latar_belakang'],
                            'tujuan' => $tujuan[0],
                            'kompetensi' => $kompetensi[0],
                            'skill' => $skill[0],
                            'level_peserta' => $level_peserta[0],
                            'level_atasan' => $level_atasan[0],
                            'level_rata' => $level_rata[0],
                            'level_kebutuhan' => $level_kebutuhan[0],
                            'catatan' => $data['catatan'],
                            'analisa_satu' => $data['catatan_satu'],
                            'analisa_dua' => $data['catatan_dua'],
                            'analisa_tiga' => $data['catatan_tiga']
                        ];
                    }
                }

                $sum_atasan = array_sum(array_column($arr_atasan,'0'));
                $sum_peserta = array_sum(array_column($arr_peserta,'0'));
                if($sum_atasan > 0){
                    $delete = Trainingfpkt::where('id_fkt', $data['id_fkt'])->delete();
                    $fkt->update([
                        'date_penilai' => date('Y-m-d H:i:s')
                    ]);
                    foreach($arr_data as $key => $value){
                        $insert = new Trainingfpkt;
                        $insert->id_fkt = $value['id_fkt'];
                        $insert->latar_belakang = $value['latar_belakang'];
                        $insert->tujuan = $value['tujuan'];
                        $insert->kompetensi = $value['kompetensi'];
                        $insert->skill = $value['skill'];
                        $insert->level_peserta = $value['level_peserta'];
                        $insert->level_atasan = $value['level_atasan'];
                        $insert->level_rata = $value['level_rata'];
                        $insert->level_kebutuhan = $value['level_kebutuhan'];
                        $insert->catatan = $value['catatan'];
                        $insert->analisa_satu = $value['analisa_satu'];
                        $insert->analisa_dua = $value['analisa_dua'];
                        $insert->analisa_tiga = $value['analisa_tiga'];
                        $insert->id_atasan = $fkt->id_penilai;
                        $insert->date_atasan = $fkt->date_penilai;
                        if($sum_peserta > 0){
                            $insert->id_peserta = $fkt->id_peserta;
                            $insert->date_peserta = $fkt->date_peserta;
                            $insert->status = 11;
                        }else{
                            $insert->status = 10;
                        }
                        $insert->save();
                    }

                    //ttd atasan
                    //ttd fpkt
                    $date_qr = date('Ymd');
                    $insert_fpkt_qr = new Qrcodefpkt;
                    $insert_fpkt_qr->id_fkt = $fkt->id;
                    $insert_fpkt_qr->qr = $date_qr.$fkt->id_penilai;
                    $insert_fpkt_qr->date_approval = date('Y-m-d H:i:s');
                    $insert_fpkt_qr->type = 2;
                    $insert_fpkt_qr->save();

                    //cek training
                    $cek_total_peserta = Trainingfkt::where('kode', $fkt->kode)->where('judul', $fkt->judul)->whereNull('date_penilai')->count();
                    if($cek_total_peserta > 0){
                        //kosong
                    }else{
                        //notification atasan departemen
                        if(!empty($fkt->checker->email)){
                            $qry_user = User::where('employee_id', $fkt->id_checker)->first();
                            if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                                $details = [
                                    'greeting' => 'Hi '.$fkt->checker->fullname,
                                    'subject' => 'Penilaian Kebutuhan Training',
                                    'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$fkt->judul.'" yang membutuhkan approval anda',
                                    'actionText' => 'Silahkan Login',
                                    'actionURL' => url('/employee/training/fkt/ptt/approved/'.encrypt($fkt->tahun_usulan).'/form'),
                                    'thanks' => 'Terimakasih atas perhatiannya!!'
                                ];
                            }else{
                                $details = [
                                    'greeting' => 'Hi '.$fkt->checker->fullname,
                                    'subject' => 'Penilaian Kebutuhan Training',
                                    'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$fkt->judul.'" yang membutuhkan approval anda',
                                    'actionText' => 'Silahkan Login',
                                    'actionURL' => url('/mytraining/fkt/ptt/approved/'.encrypt($fkt->tahun_usulan).'/form'),
                                    'thanks' => 'Terimakasih atas perhatiannya!!'
                                ];
                            }
                            //send mail
                            $qry_user->notify(new AccountNotification($details));
                        }
                    }
                    
                    //insert log user activity
                    $insert_log = new Log;
                    $insert_log->user_id = $user->id;
                    $insert_log->ip_address = $request->ip();
                    $insert_log->action = 'approved';
                    $insert_log->description = 'Approved "'.$fkt->judul.'" dengan nama pemohon'.'"'.$fkt->pemohon->fullname.'"';
                    $insert_log->save();

                    $page =  redirect(route('training.emp.fkt.ptt.detail', encrypt($fkt->kode)))->with('status','Formulir Penilaian Kebutuhan Training '.$fkt->pemohon->fullname.' has been updated');
                }else{
                    $page =  redirect(route('training.emp.fkt.ptt.detail', encrypt($fkt->kode)))->with('error','Formulir Penilaian Kebutuhan Training no changes');
                }
            }
            
            if($request->action == 'collective'){
                $user = auth()->user();
                $data = $request->input();
                $arr_id = explode(',', $data['id_fkt']);
                $fkt = Trainingfkt::with('pemohon')->whereIn('id', $arr_id)->get();
                $judul = $fkt->unique('judul')->pluck('judul')->toArray();
                $kode = $fkt->unique('kode')->pluck('kode')->toArray();
                $emp = Employee::whereIn('id', $fkt->pluck('id_pemohon'))->first();
                $code_random = random_int(100000, 999999);
                if(isset($data['no_urut'])){
                    for($i = 0; $i < count($data['no_urut']); $i++){
                        if($data['tujuan-'.$data['no_urut'][$i]] && $data['kompetensi-'.$data['no_urut'][$i]] && $data['skill-'.$data['no_urut'][$i]] && $data['level_peserta-'.$data['no_urut'][$i]] && $data['level_atasan-'.$data['no_urut'][$i]] && $data['level_rata-'.$data['no_urut'][$i]] && $data['level_kebutuhan-'.$data['no_urut'][$i]]){
                            $tujuan = $data['tujuan-'.$data['no_urut'][$i]];
                            $kompetensi = $data['kompetensi-'.$data['no_urut'][$i]];
                            $skill = $data['skill-'.$data['no_urut'][$i]];
                            $level_peserta = $data['level_peserta-'.$data['no_urut'][$i]];
                            $arr_peserta[] = $data['level_peserta-'.$data['no_urut'][$i]];
                            $level_atasan = $data['level_atasan-'.$data['no_urut'][$i]];
                            $arr_atasan[] = $data['level_atasan-'.$data['no_urut'][$i]];
                            $level_rata = $data['level_rata-'.$data['no_urut'][$i]];
                            $level_kebutuhan = $data['level_kebutuhan-'.$data['no_urut'][$i]];
                        }
                        for($n = 0; $n < count($arr_id); $n++){
                            $qry_fkt = Trainingfkt::find($arr_id[$n]);
                            $jml_atasan = array_sum(array_column($arr_atasan,'0'));
                            $jml_peserta = array_sum(array_column($arr_peserta,'0'));
                            if($jml_atasan > 0){
                                $id_atasan = $qry_fkt->id_penilai;
                                $date_penilai = date('Y-m-d H:i:s');
                            }else{
                                $id_atasan = null;
                                $date_penilai = null;
                            }
                            if($jml_peserta > 0){
                                $id_peserta = $qry_fkt->id_peserta;
                                $date_peserta = date('Y-m-d H:i:s');
                            }else{
                                $id_peserta = null;
                                $date_peserta = null;
                            }
                            $qry_fkt->update([
                                'kode_judul' => $code_random,
                                'date_peserta' => $date_peserta,
                                'date_penilai' => $date_penilai
                            ]);
                            //cek status
                            if($jml_atasan > 0 && $jml_peserta > 0){
                                $status_fpkt = 11;
                            }else{
                                $status_fpkt = 10;
                            }
                            $insert[] = [
                                'id_fkt' => $arr_id[$n],
                                'latar_belakang' => $data['latar_belakang'],
                                'tujuan' => $tujuan[0],
                                'kompetensi' => $kompetensi[0],
                                'skill' => $skill[0],
                                'level_peserta' => $level_peserta[0],
                                'level_atasan' => $level_atasan[0],
                                'level_rata' => $level_rata[0],
                                'level_kebutuhan' => $level_kebutuhan[0],
                                'catatan' => $data['catatan'],
                                'analisa_satu' => $data['catatan_satu'],
                                'analisa_dua' => $data['catatan_dua'],
                                'analisa_tiga' => $data['catatan_tiga'],
                                'id_peserta' => $id_peserta,
                                'date_peserta' => $date_peserta,
                                'id_atasan' => $id_atasan,
                                'date_atasan' => $date_penilai,
                                'status' => $status_fpkt,
                                'created_at' => Carbon::now(),
                                'updated_at' => Carbon::now()
                            ];
                            //delete qrcode fpkt sebelumnya
                            $cek_qr_fpkt = Qrcodefpkt::where('id_fkt', $qry_fkt->id)->get();
                            if($cek_qr_fpkt->isNotEmpty()){
                                $delete_qr_fpkt = Qrcodefpkt::where('id_fkt', $qry_fkt->id)->whereIn('type',[1,2])->delete();
                            }
                            //ttd atasan and peserta
                            //ttd fpkt
                            if($jml_peserta > 0){
                                $date_qr = date('Ymd');
                                $insert_peserta_qr = new Qrcodefpkt;
                                $insert_peserta_qr->id_fkt = $qry_fkt->id;
                                $insert_peserta_qr->qr = $date_qr.$qry_fkt->id_peserta;
                                $insert_peserta_qr->date_approval = date('Y-m-d H:i:s');
                                $insert_peserta_qr->type = 1;
                                $insert_peserta_qr->save();
                            }
                            if($jml_atasan > 0){
                                $date_qr = date('Ymd');
                                $insert_atasan_qr = new Qrcodefpkt;
                                $insert_atasan_qr->id_fkt = $qry_fkt->id;
                                $insert_atasan_qr->qr = $date_qr.$qry_fkt->id_penilai;
                                $insert_atasan_qr->date_approval = date('Y-m-d H:i:s');
                                $insert_atasan_qr->type = 2;
                                $insert_atasan_qr->save();
                            }
                        }                
                    }
                    $fpkt = Trainingfpkt::whereIn('id_fkt', $arr_id)->get();
                    if($fpkt->isNotEmpty()){
                        $delete = Trainingfpkt::whereIn('id_fkt', $arr_id)->delete();
                        $post = Trainingfpkt::insert($insert);
                    }else{
                        $post = Trainingfpkt::insert($insert);
                    }

                    $sum_atasan = array_sum(array_column($arr_atasan,'0'));
                    $sum_peserta = array_sum(array_column($arr_peserta,'0'));                    
                    //notification atasan departemen and pic hrd
                    if($sum_peserta > 0){
                        if($sum_atasan > 0){
                            //notification atasan departemen
                            foreach($fkt as $key_fkt){
                                if(!empty($key_fkt->checker->email)){
                                    $qry_user = User::where('employee_id', $key_fkt->id_checker)->first();
                                    if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                                        $details = [
                                            'greeting' => 'Hi '.$key_fkt->checker->fullname,
                                            'subject' => 'Penilaian Kebutuhan Training',
                                            'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$key_fkt->judul.'" yang membutuhkan approval anda',
                                            'actionText' => 'Silahkan Login',
                                            'actionURL' => url('/employee/training/fkt/ptt/approved/'.encrypt($key_fkt->tahun_usulan).'/form'),
                                            'thanks' => 'Terimakasih atas perhatiannya!!'
                                        ];
                                    }else{
                                        $details = [
                                            'greeting' => 'Hi '.$key_fkt->checker->fullname,
                                            'subject' => 'Penilaian Kebutuhan Training',
                                            'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$key_fkt->judul.'" yang membutuhkan approval anda',
                                            'actionText' => 'Silahkan Login',
                                            'actionURL' => url('/mytraining/fkt/ptt/approved/'.encrypt($key_fkt->tahun_usulan).'/form'),
                                            'thanks' => 'Terimakasih atas perhatiannya!!'
                                        ];
                                    }
                                    //send mail
                                    $qry_user->notify(new AccountNotification($details));
                                }
                            }
                        }else{
                            //notification atasan penilai
                            foreach($fkt as $key_fkt){
                                if(!empty($key_fkt->penilai->email)){
                                    $qry_user = User::where('employee_id', $key_fkt->id_penilai)->first();
                                    if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                                        $details = [
                                            'greeting' => 'Hi '.$key_fkt->penilai->fullname,
                                            'subject' => 'Penilaian Kebutuhan Training',
                                            'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$key_fkt->judul.'" yang membutuhkan penilaian anda',
                                            'actionText' => 'Silahkan Login',
                                            'actionURL' => url('/employee/training/fpkt/ptt/'.encrypt($key_fkt->id).'/form'),
                                            'thanks' => 'Terimakasih atas perhatiannya!!'
                                        ];
                                    }else{
                                        $details = [
                                            'greeting' => 'Hi '.$key_fkt->penilai->fullname,
                                            'subject' => 'Penilaian Kebutuhan Training',
                                            'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$key_fkt->judul.'" yang membutuhkan penilaian anda',
                                            'actionText' => 'Silahkan Login',
                                            'actionURL' => url('/mytrainingfpkt/ptt/'.encrypt($key_fkt->id).'/form'),
                                            'thanks' => 'Terimakasih atas perhatiannya!!'
                                        ];
                                    }
                                    //send mail
                                    $qry_user->notify(new AccountNotification($details));
                                }
                            }
                        }
                    }else{
                        if($sum_atasan > 0){
                            //notification peserta
                            foreach($fkt as $key_fkt){
                                if(!empty($key_fkt->peserta->email)){
                                    $qry_user = User::where('employee_id', $key_fkt->id_peserta)->first();
                                    if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                                        $details = [
                                            'greeting' => 'Hi '.$key_fkt->peserta->fullname,
                                            'subject' => 'Penilaian Kebutuhan Training',
                                            'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$key_fkt->judul.'" yang membutuhkan penilaian anda',
                                            'actionText' => 'Silahkan Login',
                                            'actionURL' => url('/employee/training/fpkt/ptt/'.encrypt($key_fkt->id).'/form'),
                                            'thanks' => 'Terimakasih atas perhatiannya!!'
                                        ];
                                    }else{
                                        $details = [
                                            'greeting' => 'Hi '.$key_fkt->peserta->fullname,
                                            'subject' => 'Penilaian Kebutuhan Training',
                                            'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$key_fkt->judul.'" yang membutuhkan penilaian anda',
                                            'actionText' => 'Silahkan Login',
                                            'actionURL' => url('/mytrainingfpkt/ptt/'.encrypt($key_fkt->id).'/form'),
                                            'thanks' => 'Terimakasih atas perhatiannya!!'
                                        ];
                                    }
                                    //send mail
                                    $qry_user->notify(new AccountNotification($details));
                                }
                            }
                        }else{
                            foreach($fkt as $key_fkt){
                                //notification peserta
                                if(!empty($key_fkt->peserta->email)){
                                    $qry_user = User::where('employee_id', $key_fkt->id_peserta)->first();
                                    if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                                        $details = [
                                            'greeting' => 'Hi '.$key_fkt->peserta->fullname,
                                            'subject' => 'Penilaian Kebutuhan Training',
                                            'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$key_fkt->judul.'" yang membutuhkan penilaian anda',
                                            'actionText' => 'Silahkan Login',
                                            'actionURL' => url('/employee/training/fpkt/ptt/'.encrypt($key_fkt->id).'/form'),
                                            'thanks' => 'Terimakasih atas perhatiannya!!'
                                        ];
                                    }else{
                                        $details = [
                                            'greeting' => 'Hi '.$key_fkt->peserta->fullname,
                                            'subject' => 'Penilaian Kebutuhan Training',
                                            'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$key_fkt->judul.'" yang membutuhkan penilaian anda',
                                            'actionText' => 'Silahkan Login',
                                            'actionURL' => url('/mytrainingfpkt/ptt/'.encrypt($key_fkt->id).'/form'),
                                            'thanks' => 'Terimakasih atas perhatiannya!!'
                                        ];
                                    }
                                    //send mail
                                    $qry_user->notify(new AccountNotification($details));
                                }
            
                                //notification atasan penilai
                                if(!empty($key_fkt->penilai->email)){
                                    $qry_user = User::where('employee_id', $key_fkt->id_penilai)->first();
                                    if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                                        $details = [
                                            'greeting' => 'Hi '.$key_fkt->penilai->fullname,
                                            'subject' => 'Penilaian Kebutuhan Training',
                                            'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$key_fkt->judul.'" yang membutuhkan penilaian anda',
                                            'actionText' => 'Silahkan Login',
                                            'actionURL' => url('/employee/training/fpkt/ptt/'.encrypt($key_fkt->id).'/form'),
                                            'thanks' => 'Terimakasih atas perhatiannya!!'
                                        ];
                                    }else{
                                        $details = [
                                            'greeting' => 'Hi '.$key_fkt->penilai->fullname,
                                            'subject' => 'Penilaian Kebutuhan Training',
                                            'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$key_fkt->judul.'" yang membutuhkan penilaian anda',
                                            'actionText' => 'Silahkan Login',
                                            'actionURL' => url('/mytrainingfpkt/ptt/'.encrypt($key_fkt->id).'/form'),
                                            'thanks' => 'Terimakasih atas perhatiannya!!'
                                        ];
                                    }
                                    //send mail
                                    $qry_user->notify(new AccountNotification($details));
                                }
                            }
                        }
                    }     
                    
                    //insert log user activity
                    $insert_log = new Log;
                    $insert_log->user_id = $user->id;
                    $insert_log->ip_address = $request->ip();
                    $insert_log->action = 'approved';
                    $insert_log->description = 'Approved collective"'.$judul[0].'" dengan nama pemohon'.'"'.$emp->fullname.'"';
                    $insert_log->save();
        
                    $page = redirect(route('training.emp.fkt.ptt.detail', encrypt($kode[0])))->with('status','Formulir Penilaian Kebutuhan Training '.$emp->fullname.' has been updated');
                }else{
                    $page = redirect(route('training.emp.fkt.ptt.detail', encrypt($kode[0])))->with('error','Formulir Penilaian Kebutuhan Training no changes');
                }
            }
            
            if($request->action == 'collective_approve'){
                $user = auth()->user();
                $data = $request->input();
                $arr_id = explode(',', $data['id_fkt']);
                $fkt = Trainingfkt::with('pemohon')->whereIn('id', $arr_id)->get();
                $judul = $fkt->unique('judul')->pluck('judul')->toArray();
                $kode = $fkt->unique('kode')->pluck('kode')->toArray();
                $emp = Employee::whereIn('id', $fkt->pluck('id_pemohon'))->first();
                for($i = 0; $i < count($data['no_urut']); $i++){
                    if($data['tujuan-'.$data['no_urut'][$i]] && $data['kompetensi-'.$data['no_urut'][$i]] && $data['skill-'.$data['no_urut'][$i]] && $data['level_peserta-'.$data['no_urut'][$i]] && $data['level_atasan-'.$data['no_urut'][$i]] && $data['level_rata-'.$data['no_urut'][$i]] && $data['level_kebutuhan-'.$data['no_urut'][$i]]){
                        $tujuan = $data['tujuan-'.$data['no_urut'][$i]];
                        $kompetensi = $data['kompetensi-'.$data['no_urut'][$i]];
                        $skill = $data['skill-'.$data['no_urut'][$i]];
                        $level_peserta = $data['level_peserta-'.$data['no_urut'][$i]];
                        $arr_peserta[] = $data['level_peserta-'.$data['no_urut'][$i]];
                        $level_atasan = $data['level_atasan-'.$data['no_urut'][$i]];
                        $arr_atasan[] = $data['level_atasan-'.$data['no_urut'][$i]];
                        $level_rata = $data['level_rata-'.$data['no_urut'][$i]];
                        $level_kebutuhan = $data['level_kebutuhan-'.$data['no_urut'][$i]];
                    }
                    for($n = 0; $n < count($arr_id); $n++){
                        $qry_fkt = Trainingfkt::find($arr_id[$n]);
                        $qry_fkt->update([
                            'date_penilai' => date('Y-m-d H:i:s')
                        ]);
                        $insert[] = [
                            'id_fkt' => $arr_id[$n],
                            'latar_belakang' => $data['latar_belakang'],
                            'tujuan' => $tujuan[0],
                            'kompetensi' => $kompetensi[0],
                            'skill' => $skill[0],
                            'level_peserta' => $level_peserta[0],
                            'level_atasan' => $level_atasan[0],
                            'level_rata' => $level_rata[0],
                            'level_kebutuhan' => $level_kebutuhan[0],
                            'catatan' => $data['catatan'],
                            'analisa_satu' => $data['catatan_satu'],
                            'analisa_dua' => $data['catatan_dua'],
                            'analisa_tiga' => $data['catatan_tiga'],
                            'id_peserta' => $qry_fkt->id_peserta,
                            'date_peserta' => $qry_fkt->date_peserta,
                            'id_atasan' => $qry_fkt->id_penilai,
                            'date_atasan' => date('Y-m-d H:i:s'),
                            'status' => 11,
                            'created_at' => $qry_fkt->created_at,
                            'updated_at' => Carbon::now()
                        ];
                        //ttd atasan
                        //ttd fpkt
                        $date_qr = date('Ymd');
                        $insert_atasan_qr = new Qrcodefpkt;
                        $insert_atasan_qr->id_fkt = $qry_fkt->id;
                        $insert_atasan_qr->qr = $date_qr.$qry_fkt->id_penilai;
                        $insert_atasan_qr->date_approval = date('Y-m-d H:i:s');
                        $insert_atasan_qr->type = 2;
                        $insert_atasan_qr->save();

                        //notification atasan departemen
                        if(!empty($qry_fkt->checker->email)){
                            $qry_user = User::where('employee_id', $qry_fkt->id_checker)->first();
                            if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                                $details = [
                                    'greeting' => 'Hi '.$qry_fkt->checker->fullname,
                                    'subject' => 'Penilaian Kebutuhan Training',
                                    'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$qry_fkt->judul.'" yang membutuhkan approval anda',
                                    'actionText' => 'Silahkan Login',
                                    'actionURL' => url('/employee/training/fkt/ptt/approved/'.encrypt($qry_fkt->tahun_usulan).'/form'),
                                    'thanks' => 'Terimakasih atas perhatiannya!!'
                                ];
                            }else{
                                $details = [
                                    'greeting' => 'Hi '.$qry_fkt->checker->fullname,
                                    'subject' => 'Penilaian Kebutuhan Training',
                                    'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$qry_fkt->judul.'" yang membutuhkan approval anda',
                                    'actionText' => 'Silahkan Login',
                                    'actionURL' => url('/mytraining/fkt/ptt/approved/'.encrypt($qry_fkt->tahun_usulan).'/form'),
                                    'thanks' => 'Terimakasih atas perhatiannya!!'
                                ];
                            }
                            //send mail
                            $qry_user->notify(new AccountNotification($details));
                        }
                    }                
                }
                $fpkt = Trainingfpkt::whereIn('id_fkt', $arr_id)->get();
                if($fpkt->isNotEmpty()){
                    $delete = Trainingfpkt::whereIn('id_fkt', $arr_id)->delete();
                    $post = Trainingfpkt::insert($insert);
                }else{
                    $post = Trainingfpkt::insert($insert);
                }            

                //insert log user activity
                $insert_log = new Log;
                $insert_log->user_id = $user->id;
                $insert_log->ip_address = $request->ip();
                $insert_log->action = 'approved';
                $insert_log->description = 'Approved collective"'.$judul[0].'" dengan nama pemohon'.'"'.$emp->fullname.'"';
                $insert_log->save();
                
                $page = redirect(route('training.emp.fkt.ptt.detail', encrypt($kode[0])))->with('status','Formulir Penilaian Kebutuhan Training '.$emp->fullname.' has been updated');
            }    

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();             
            $message = $e->getMessage();
            var_dump('Exception Message: '. $message);  
            exit;
        } 
        return $page;
    }
    ///collective ptt
    public function emp_fpkt_ptt_collective(Request $request, $id){
        $user = auth()->user();
        $fkt = Trainingfkt::where('kode',decrypt($id))->where('judul', $request->pelatihan)->first();
        $qry_fkt = Trainingfkt::where('kode',decrypt($id))->where('judul', $request->pelatihan)->get();
        $arr_peserta = Employee::whereIn('id', $qry_fkt->pluck('id_peserta'))->get();
        return view('pages.employee.training.ptt.form-collective', compact('user','fkt','qry_fkt','arr_peserta'));
    }    
    public function emp_fpkt_ptt_collective_approve(Request $request, $id){
        $user = auth()->user();
        $kode = $id;
        $id = decrypt($id);
        $fkt = Trainingfkt::where('kode', $id)->first();
        $fpkt = Trainingfpkt::where('id_fkt', $fkt->id)->first(); 
        $arr_fpkt = Trainingfpkt::where('id_fkt', $fkt->id)->get(); 
        $cek_pemohon = Trainingfkt::where('kode', $id)->where('id_pemohon', $user->employee_id)->first(); 
        $cek_peserta = Trainingfkt::where('kode', $id)->where('id_peserta', $user->employee_id)->first(); 
        $cek_atasan = Trainingfkt::where('kode', $id)->where('id_penilai', $user->employee_id)->first(); 
        
        $qry_fkt = Trainingfkt::where('kode',$id)->where('judul', $fkt->judul)->get();
        $arr_peserta = Employee::whereIn('id', $qry_fkt->pluck('id_peserta'))->get();

        return view('pages.employee.training.ptt.form-collective-approve', compact('kode','user','fkt','fpkt','cek_pemohon','cek_peserta','cek_atasan','arr_fpkt','qry_fkt','arr_peserta'));
    }

    //start employee pti
    ///fkt pengajuan
    public function emp_index_fkt_pti(Request $request){
        $user = auth()->user();
        $data = Trainingfpkt::where(function ($data) use ($user) {
            $data->where('id_pemohon', $user->employee_id)
                  ->orWhere('id_peserta', $user->employee_id);
        })->get()->unique('kode_judul_fpkt');
        if ($request->ajax()) {          
            return DataTables::of($data)
                ->addColumn('judul_fpkt', function($data){
                    return $data['judul_fpkt'];
                })
                ->addColumn('vendor', function($data){
                    if($data->id_vendor == null){
                        return $data->nama_vendor ?? '-';
                    }else{
                        return $data->vendor->nama ?? '-';
                    }
                })
                ->addColumn('total_biaya', function($data){
                    $jml_biaya = Trainingfpkt::where('kode_judul_fpkt', $data['kode_judul_fpkt'])->sum('biaya_fpkt');
                    return 'Rp '.number_format($jml_biaya,2,',','.');
                })
                ->addColumn('status', function($data){
                    // if($data['status'] == 1) return '<a href="#" <span class="badge text-bg-primary"><i class="ri-edit-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    // if($data['status'] == 2) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-warning view-status"><i class="ri-time-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    // if($data['status'] == 3) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-info view-status"><i class="ri-time-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 4) return '<a href="javascript:void(0)" <span class="badge text-bg-info"><i class="ri-time-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 5) return '<a href="javascript:void(0)" <span class="badge text-bg-secondary"><i class="ri-time-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    // if($data['status'] == 6) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-success view-status"><i class="ri-checkbox-circle-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    // if($data['status'] == 7) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-success view-status"><i class="ri-checkbox-circle-line align-bottom"></i> Finished</span></a>';
                    if($data['status'] == 9) return '<a href="javascript:void(0)" <span class="badge text-bg-warning view-status-fpkt"><i class="ri-time-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 10) return '<a href="javascript:void(0)" <span class="badge text-bg-secondary view-status-fpkt"><i class="ri-time-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 11) return '<a href="javascript:void(0)" <span class="badge text-bg-info view-status-fpkt"><i class="ri-time-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 12) return '<a href="javascript:void(0)" <span class="badge text-bg-success"><i class="ri-time-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                })
                ->addColumn('action', function ($data) {        
                    // $list_edit = '<li><a href="'.route('training.emp.fkt.pti.edit',encrypt($data['kode'])).'" class="dropdown-item"><i class="ri-edit-line align-bottom me-2 text-muted"></i> Edit</a></li>';
                    $list_detail = '<li><a href="'.route('training.emp.fkt.pti.detail',encrypt($data['kode_judul_fpkt'])).'" class="dropdown-item"><i class="ri-file-edit-line align-bottom me-2 text-muted"></i> Detail FPKP</a></li>';
                    if(!empty($data['id_fkt'])){
                        $list_print_fkt = '<li><a href="'.route('public.training.fkp.pdf', encrypt($data->fkt->kode)).'" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FKP</a></li>';
                        $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_detail.$list_print_fkt.'</ul></div>';
                    }else{
                        $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_detail.'</ul></div>';
                    }
                    
                    // if($data['status'] == 2){
                    //     if(!empty($data['date_verified_pic'])){
                    //         $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_detail.$list_print_fkt.'</ul></div>';
                    //     }else{
                    //         $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_print_fkt.'</ul></div>';
                    //     }
                    // }else{
                    //     $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_print_fkt.'</ul></div>';
                    // }
                    return $button;
                })
                ->addColumn('peserta', function($data){
                    $cek_user = auth()->user();
                    if($cek_user->employee_id == $data['id_pemohon']){
                        $query = Trainingfpkt::where('kode_judul_fpkt', $data['kode_judul_fpkt'])->where('id_pemohon', $cek_user->employee_id)->get();
                    }else{
                        $query = Trainingfpkt::where('kode_judul_fpkt', $data['kode_judul_fpkt'])->where('id_peserta', $cek_user->employee_id)->get();
                    }
                    if($query->isNotEmpty()){
                        $peserta = '<div class="col-lg-12"><table class="table table-bordered" style="table-layout: fixed; width:100%;">';
                        $peserta .= '
                            <thead>
                                <tr>
                                    <th colspan="3" style="text-align: center;">Pengajuan Program Pelatihan</th>
                                </tr>
                                <tr>
                                    <th style="text-align: center;">Peserta</th>
                                    <th style="text-align: center;">Pelaksanaan</th>
                                    <th style="text-align: center;">Biaya</th>
                                </tr>
                            </thead>
                            ';
                        $peserta .= '<tbody>';
                        foreach($query as $qry){
                            $peserta .= '<tr>';                  
                            $peserta .= '<td>'.$qry->peserta->fullname.'</td>';    
                            $peserta .= '<td>'.date('d M Y', strtotime($qry->date_pelaksanaan)).'</td>';    
                            $peserta .= '<td> Rp '.number_format($qry->biaya_fpkt,2,',','.').'</td>';    
                            $peserta .= '</tr>';
                        } 
                        $peserta .= '</tbody></table></div>';
                    }else{
                        $peserta = '';
                    }
                    return $peserta;
                })
                ->rawColumns(['action','status','peserta'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.employee.training.index', compact('user'));
    } 
    public function emp_select_usulan_pti(Request $request){
        $tipe = $request->tipe;
        $employee_id = $request->id_user;
        $cek_radio = $request->cek_radio;
        if($tipe == 'ptt'){
            $data = [];
            $all_data = [];
            $employee = Employee::find($employee_id);
            if($cek_radio == 'pemohon'){
                $query = Trainingfkt::where('dept_pemohon', $employee->department_id)->where('status', 6)->get()->unique('id')->toArray();
                foreach($query as $key => $value){
                    $fpkt = Trainingfpkt::where('id_fkt', $value['id'])->first();
                    if(empty($fpkt)){
                        $all_data[$value['kode_judul']] = $value['judul'];
                    }
                }
            }else{
                $query = Trainingfkt::where('dept_pemohon', $employee->department_id)->where('id_peserta', $employee->id)->where('status', 6)->get()->unique('id')->toArray();
                foreach($query as $key => $value){
                    $fpkt = Trainingfpkt::where('id_fkt', $value['id'])->first();
                    if(empty($fpkt)){
                        $all_data[$value['kode_judul']] = $value['judul'];
                    }
                }
            }
            $data = $all_data;
        }else{
            $data = null;
        }
        return response()->json($data);

    }
    public function emp_select_pelatihan_pti(Request $request){
        $employee_id = $request->id_user;
        $kode_judul = $request->judul;
        $cek_radio = $request->cek_radio;
        $employee = Employee::find($employee_id);
        // dd($employee->department_id);
        if($cek_radio == 'pemohon'){
            $subqueryIds = Trainingfkt::select('id_fkt')->from('training_fpkt')->whereNotNull('id_fkt')->pluck('id_fkt')->toArray();
            // Check if the subquery returned any IDs
            if (empty($subqueryIds)) {
                // Handle the case where the subquery is null
                $get_peserta = Trainingfkt::where('dept_pemohon', $employee->department_id)->where('kode_judul', $kode_judul)->where('status', 6)->get()->unique('id_peserta')->pluck('id_peserta')->toArray();
            } else {
                // Proceed with the main query
                $get_peserta = Trainingfkt::whereNotIn('id', $subqueryIds)
                    ->where('dept_pemohon', $employee->department_id)
                    ->where('kode_judul', $kode_judul)
                    ->where('status', 6)
                    ->get()
                    ->unique('id_peserta')
                    ->pluck('id_peserta')
                    ->toArray();
            }
            // $get_peserta = Trainingfkt::whereNotIn('id', function($get_peserta) {
            //     $get_peserta->select('id_fkt')->from('training_fpkt');
            // })->where('dept_pemohon', $employee->department_id)->where('kode_judul', $kode_judul)->where('status', 6)->get()->unique('id_peserta')->pluck('id_peserta')->toArray();
            // $get_peserta = Trainingfkt::where('dept_pemohon', $employee->department_id)->where('kode_judul', $kode_judul)->where('status', 6)->get()->unique('id_peserta')->pluck('id_peserta')->toArray();
            $nama_emp = Employee::whereIn('id', $get_peserta)->get();
            $arr_peserta = implode(', ', $nama_emp->pluck('fullname')->toArray()) ?? '-';
            $all_data['id_peserta'] = implode(', ', $nama_emp->pluck('id')->toArray());
            $all_data['nama_emp'] = $arr_peserta;
        }else{
            $query = Trainingfkt::where('dept_pemohon', $employee->department_id)->where('id_peserta', $employee->id)->where('kode_judul', $kode_judul)->where('status', 6)->get()->unique('id')->toArray();
            foreach($query as $key => $value){
                $fpkt = Trainingfpkt::where('id_fkt', $value['id'])->first();
                    if(empty($fpkt)){
                        $emp_peserta = Employee::find($value['id_peserta']);
                        $all_data['id_peserta'] = $emp_peserta->id;
                        $all_data['nama_emp'] = $emp_peserta->fullname;
                    }
            }
        }
        $data = $all_data;
        
        // if($cek_radio == 'pemohon'){
        //     $query = Trainingfkt::where('id_pemohon', $employee_id)->where('status', 6)->get();
        //     $nama_emp = Employee::whereIn('id', $query->pluck('id_peserta')->toArray())->get();
        //     $arr_peserta = implode(', ', $nama_emp->pluck('fullname')->toArray()) ?? '-';
        //     $data['id_peserta'] = implode(', ', $nama_emp->pluck('id')->toArray());
        //     $data['nama_emp'] = $arr_peserta;
        // }else{
        //     $query = Trainingfkt::where('id_peserta', $employee_id)->where('kode_judul', $judul)->where('status', 6)->first();
        //     $data['id_peserta'] = $query->id_peserta;
        //     $data['nama_emp'] = $query->peserta->fullname;
        // }
        return response()->json($data);
    }
    public function emp_fkt_pti_select_create(Request $request){
        $user = auth()->user();
        $cek_radio = $request->cek_radio;
        $year_now = date('Y');
        $next_year = $year_now+1;
        
        $employees = Employee::whereNot('status', 'TERMINATED')->get();
        $vendors = Vendor::where('tipe','training')->get();

        return view('pages.employee.training.pti.form-fkt', compact('user','year_now','employees','vendors','next_year','cek_radio'));
    }
    public function emp_fkt_pti_status(Request $request){
        if(!empty($request->kode)){
            $query = Trainingfpkt::where('id', decrypt($request->kode))->first();
            
            $data['judul_fpkt'] = $query->judul_fpkt;
            if(!empty($query->id_fkt)){
                $data['tujuan_usulan_fpkt'] = 'Program Pelatihan Tahunan';
            }else{
                $data['tujuan_usulan_fpkt'] = 'Program Pelatihan Insidentil';
            }
            $data['nama_pemohon_fpkt'] = $query->pemohon->fullname;
            $data['date_pemohon_fpkt'] = date('d M Y H:i', strtotime($query->date_pemohon));
            $data['nama_peserta_fpkt'] = $query->peserta->fullname;
            $data['date_pelaksanaan_fpkt'] = date('d M Y', strtotime($query->date_pelaksanaan));
            $data['id_status_fpkt'] = $query->status;
            $data['nama_status_fpkt'] = $query->training_status->name;
            
            //status ttd fpkt
            if(!empty($query->date_peserta)){
                $data['id_peserta_fpkt'] = $query->peserta->fullname;
                $data['date_peserta_fpkt'] = date('d M Y H:i', strtotime($query->date_peserta));
            }else{
                $data['id_peserta_fpkt'] = $query->peserta->fullname;
                $data['date_peserta_fpkt'] = null;
            }
            if(!empty($query->date_atasan)){
                $data['id_atasan_fpkt'] = $query->atasan->fullname;
                $data['date_atasan_fpkt'] = date('d M Y H:i', strtotime($query->date_atasan));
            }else{
                $data['id_atasan_fpkt'] = null;
                $data['date_atasan_fpkt'] = null;
            }
            if(!empty($query->date_dept_head)){
                $data['atasan_dept_fpkt'] = $query->atasan_dept->fullname;
                $data['date_atasan_dept_fpkt'] = date('d M Y H:i', strtotime($query->date_dept_head));
            }else{
                $data['atasan_dept_fpkt'] = null;
                $data['date_atasan_dept_fpkt'] = null;
            }
            if(!empty($query->date_hrd)){
                $data['verified_hrd_fpkt'] = $query->hrd->fullname;
                $data['date_verified_hrd_fpkt'] = date('d M Y H:i', strtotime($query->date_hrd));
            }else{
                $data['verified_hrd_fpkt'] = null;
                $data['date_verified_hrd_fpkt'] = null;
            }
            if(!empty($query->date_bod1)){
                $data['bod1_fpkt'] = $query->bod1->fullname;
                $data['date_bod1_fpkt'] = date('d M Y H:i', strtotime($query->date_bod1));
            }else{
                $data['bod1_fpkt'] = null;
                $data['date_bod1_fpkt'] = null;
            }
            if(!empty($query->date_bod2)){
                $data['bod2_fpkt'] = $query->bod2->fullname;
                $data['date_bod2_fpkt'] = date('d M Y H:i', strtotime($query->date_bod2));
            }else{
                $data['bod2_fpkt'] = null;
                $data['date_bod2_fpkt'] = null;
            }
            //catatan fkp
            $query_ctt = Logcatatantraining::where('id_fpkt', $query->id)->get();
            if($query_ctt->isNotEmpty()){
                foreach($query_ctt as $qry_ctt){
                    $dt['id_user'] = $qry_ctt->employee->fullname;
                    $dt['action'] = $qry_ctt->action;
                    $dt['catatan'] = $qry_ctt->catatan;
                    $dt['tgl_ctt'] = date('d M Y H:i', strtotime($qry_ctt->created_at));
                    $dt_all[] = $dt;
                }
                $data['ctt_fpkt'] = $dt_all;
            }else{
                $data['ctt_fpkt'] = null;
            }
        }
        
        return response()->json($data);
    }
    // public function emp_fkt_pti_create(Request $request){
    //     $user = auth()->user();
    //     $year_now = date('Y');
    //     $next_year = $year_now+1;
        
    //     $employees = Employee::whereNot('status', 'TERMINATED')->get();
    //     $vendors = Vendor::where('tipe','training')->get();
    //     return view('pages.employee.training.pti.form-fkt', compact('user','year_now','employees','vendors','year_now','next_year'));
    // }
    public function emp_fkt_pti_edit(Request $request, $id){
        $user = auth()->user();
        $training_fkt  = Trainingfkt::where('kode', decrypt($id))->first();
        $min = (date('Y')-1);
        $max = (date('Y')+1);
        $year_now = date('Y');
        
        $employees = Employee::whereNot('status', 'TERMINATED')->get();
        $vendors = Vendor::where('tipe','training')->get();
        
        $query = Trainingfkt::where('kode', decrypt($id))->get()->unique('judul')->pluck('judul');

        foreach($query as $key => $value){
            $query2 = Trainingfkt::where('kode', decrypt($id))->where('judul', $value)->get();
            $data[$key]['id_peserta'][] = $query2->pluck('id_peserta')->toArray();
            $data[$key]['id_penilai'][] = $query2->pluck('id_penilai')->toArray();
            $data[$key]['judul'] = $value;
            $data[$key]['sifat'] = $query2->unique('sifat')->pluck('sifat')->toArray();
            $data[$key]['jenis_pelatihan'] = $query2->unique('jenis_pelatihan')->pluck('jenis_pelatihan')->toArray();
            $data[$key]['alasan'] = $query2->unique('alasan')->pluck('alasan')->toArray();
            $data[$key]['bulan_pelaksana'] = $query2->unique('bulan_pelaksanaan')->pluck('bulan_pelaksanaan')->toArray();
            $data[$key]['id_vendor'] = $query2->unique('id_vendor')->pluck('id_vendor')->toArray();
            $data[$key]['nama_vendor'] = $query2->unique('nama_vendor')->pluck('nama_vendor')->toArray();
            $data[$key]['biaya_fkt'] = $query2->unique('biaya_fkt')->pluck('biaya_fkt')->toArray();
            $data[$key]['penginapan'] = $query2->unique('penginapan')->pluck('penginapan')->toArray();
            $data[$key]['transportasi'] = $query2->unique('transportasi')->pluck('transportasi')->toArray();
        }
        $data_all = $data;
        return view('pages.employee.training.pti.edit-fkt', compact('user','min','max','year_now','employees','vendors','training_fkt','data_all'));
    }
    public function emp_fkt_pti_detail(Request $request, $id){
        $user = auth()->user();
        $fpkt = Trainingfpkt::where('kode_judul_fpkt', decrypt($id))->first();
        $query_fpkt = Trainingfpkt::where('kode_judul_fpkt', decrypt($id))->get();
        
        $total_fkt = $query_fpkt->count();

        return view('pages.employee.training.pti.form-fkt-detail', compact('user','fpkt','query_fpkt','total_fkt'));
    }
    public function emp_fkt_pti_store(Request $request){     
        DB::beginTransaction();
        try {
            $user = auth()->user();
            $usulan_program = $request->usulan_program;
            $cek_radio = $request->cek_radio;
            if($usulan_program == 'ptt'){
                $kode_judul = $request->judul_pelatihan;
                $arr_peserta = explode(',', $request->id_peserta);
                $biaya_fpkt = str_replace(".","",$request->biaya_fpkt);
                for($i = 0; $i < count(array_unique($arr_peserta)); $i++){
                    if($cek_radio == 'pemohon'){
                        $fkt = Trainingfkt::where('id_pemohon', $user->employee_id)
                            ->where('id_peserta', $arr_peserta[$i])
                            ->where('kode_judul', $kode_judul)
                            ->where('status', 6)->first();
                    }else{
                        $fkt = Trainingfkt::where('id_peserta', $arr_peserta[$i])
                            ->where('kode_judul', $kode_judul)
                            ->where('status', 6)->first();
                    }
                    //insert fpkt
                    $insert = new Trainingfpkt;
                    $insert->id_fkt = $fkt->id ?? null;
                    $insert->latar_belakang = $request->latar_belakang;
                    $insert->biaya_fpkt = $biaya_fpkt;
                    if($request->id_vendor != 'other'){
                        $insert->id_vendor = $request->id_vendor;
                    }else{
                        $insert->nama_vendor = $request->nama_vendor;
                    }
                    $insert->date_pelaksanaan = $request->date_pelaksanaan;
                    $insert->kode_judul_fpkt = $fkt->kode_judul ?? null;
                    $insert->judul_fpkt = $fkt->judul ?? null;
                    $insert->jenis_fpkt = $fkt->jenis_pelatihan ?? null;
                    $insert->id_pemohon = $user->employee_id;
                    $insert->date_pemohon = date('Y-m-d H:i:s');
                    $insert->id_peserta = $arr_peserta[$i];
                    $insert->id_atasan = $request->id_atasan;
                    $insert->id_dept_head = $request->id_dept_head;
                    $insert->status = 9; //waiting assessment
                    $insert->save();

                    //insert log user activity
                    $insert = new Log;
                    $insert->user_id = $user->id;
                    $insert->ip_address = $request->ip();
                    $insert->action = 'insert';
                    $insert->description = 'Proposed formulir kebutuhan pelaksanaan pelatihan "'.$fkt->judul.'" dengan nama peserta'.'"'.$fkt->peserta->fullname.'" tujuan "Program Training Tahunan (PTT)"';
                    $insert->save();
                }

                DB::commit();
                return redirect(route('training.emp.fkt.pti.back'))->with('status','Proposed Formulir Kebutuhan Pelaksanaan Pelatihan has been created');
            }

            if($usulan_program == 'pti'){
                $month_now = date("m");
                $year_now = date("y");
                $month_name = array(
                    '01' => 'I',
                    '02' => 'II',
                    '03' => 'III',
                    '04' => 'IV',
                    '05' => 'V',
                    '06' => 'VI',
                    '07' => 'VII',
                    '08' => 'VIII',
                    '09' => 'IX',
                    '10' => 'X',
                    '11' => 'XI',
                    '12' => 'XII',
                );
                $kode_judul = $request->judul_fpkt;
                $arr_peserta = $request->id_emp;
                $biaya_fpkt = str_replace(".","",$request->biaya_fpkt);
                
                $code_random = random_int(100000, 999999);
                $cek_urut = Trainingfpkt::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->whereNotNull('kode_fpkt')->latest('kode_fpkt')->first();
                if(!empty($cek_urut)){
                    $arr = explode("/", $cek_urut->kode_fpkt);
                    $no = intval($arr[0]+1);
                    if(strlen($no) == 1){
                        $no = ["00".$no];
                    }elseif(strlen($no) == 2){
                        $no = ["0".$no];
                    }else{
                        $no = array($no);
                    }
                    $fpkt = $arr[1];
                    $pti = $arr[2];
                    $bulan = $arr[3];
                    $tahun = $arr[4];
                    $kode = $no[0].'/'.$fpkt.'/'.$pti.'/'.$bulan.'/'.$tahun;
                }else{
                    $no = '001';
                    $fpkt = 'FPKT';
                    $pti = 'PTI';
                    $bulan = $month_name[$month_now];
                    $tahun = $year_now;
                    $kode = $no.'/'.$fpkt.'/'.$pti.'/'.$bulan.'/'.$tahun;
                }
                for($i = 0; $i < count(array_unique($arr_peserta)); $i++){
                    //insert fpkt
                    $insert = new Trainingfpkt;
                    $insert->kode_fpkt = $kode;
                    $insert->latar_belakang = $request->latar_belakang;
                    $insert->biaya_fpkt = $biaya_fpkt;
                    if($request->id_vendor != 'other'){
                        $insert->id_vendor = $request->id_vendor;
                    }else{
                        $insert->nama_vendor = $request->nama_vendor;
                    }
                    $insert->date_pelaksanaan = $request->date_pelaksanaan;
                    $insert->kode_judul_fpkt = $code_random;
                    $insert->judul_fpkt = $request->judul_fpkt;
                    $insert->jenis_fpkt = $request->jenis_pelatihan;
                    $insert->id_pemohon = $user->employee_id;
                    $insert->date_pemohon = date('Y-m-d H:i:s');
                    $insert->id_peserta = $arr_peserta[$i];
                    $insert->id_atasan = $request->id_atasan;
                    $insert->id_dept_head = $request->id_dept_head;
                    $insert->alasan_pti = $request->alasan_pti ?? null;
                    $insert->status = 9; //waiting assessment
                    $insert->save();

                    $peserta = Employee::where('id', $arr_peserta[$i])->first();

                    //insert log user activity
                    $insert = new Log;
                    $insert->user_id = $user->id;
                    $insert->ip_address = $request->ip();
                    $insert->action = 'insert';
                    $insert->description = 'Proposed formulir kebutuhan pelaksanaan pelatihan "'.$request->judul_fpkt.'" dengan nama peserta'.'"'.$peserta->fullname.'" tujuan "Program Training Insidentil (PTI)"';
                    $insert->save();
                }
                
                DB::commit();
                return redirect(route('training.emp.index'))->with('tab_pti','open tab')->with('status','Proposed Formulir Kebutuhan Pelaksanaan Pelatihan has been created');
            }
        }catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
    public function emp_fkt_pti_update(Request $request){
        if($request->action == "draft"){
            $user = auth()->user();
            $data = $request->input();
            $delete_fkt_ptt = Trainingfkt::where('kode', $data['kode'])->delete();
            $nama_pemohon = $request->nama_pemohon;
            for($i = 0; $i < count($data['no_urut']); $i++){
                if($data['id_peserta-'.$data['no_urut'][$i]] && $data['id_penilai-'.$data['no_urut'][$i]] && $data['judul-'.$data['no_urut'][$i]] && $data['jenis_pelatihan-'.$data['no_urut'][$i]] && $data['sifat-'.$data['no_urut'][$i]] && $data['alasan-'.$data['no_urut'][$i]] && $data['bulan_pelaksanaan-'.$data['no_urut'][$i]] && $data['id_vendor-'.$data['no_urut'][$i]] && $data['vendor_other-'.$data['no_urut'][$i]] && $data['biaya_fkt-'.$data['no_urut'][$i]] && $data['penginapan-'.$data['no_urut'][$i]] && $data['transportasi-'.$data['no_urut'][$i]]){
                    $id_peserta = $data['id_peserta-'.$data['no_urut'][$i]];
                    $id_penilai = $data['id_penilai-'.$data['no_urut'][$i]];
                    $judul = $data['judul-'.$data['no_urut'][$i]];
                    $jenis = $data['jenis_pelatihan-'.$data['no_urut'][$i]];
                    $sifat = $data['sifat-'.$data['no_urut'][$i]];
                    $alasan = $data['alasan-'.$data['no_urut'][$i]];
                    $bulan_pelaksanaan = $data['bulan_pelaksanaan-'.$data['no_urut'][$i]];
                    $id_vendor = $data['id_vendor-'.$data['no_urut'][$i]];
                    $vendor_other = $data['vendor_other-'.$data['no_urut'][$i]];
                    $biaya_fkt = str_replace(".","",$data['biaya_fkt-'.$data['no_urut'][$i]]);
                    $penginapan = $data['penginapan-'.$data['no_urut'][$i]];
                    $transportasi = $data['transportasi-'.$data['no_urut'][$i]];
                }
                $arr_data = [
                    'id_pemohon' => $data['id_pemohon'],
                    'tahun_usulan' => $data['tahun_usulan'],
                    'tahun_pelaksanaan' => $data['tahun_pelaksanaan'],
                    'tipe' => $data['tipe'],
                    'id_peserta' => $id_peserta,
                    'id_penilai' => $id_penilai,
                    'judul' => $judul,
                    'jenis' => $jenis,
                    'sifat' => $sifat,
                    'alasan' => $alasan,
                    'bulan_pelaksanaan' => $bulan_pelaksanaan,
                    'id_vendor' => $id_vendor,
                    'vendor_other' => $vendor_other,
                    'biaya_fkt' => $biaya_fkt,
                    'penginapan' => $penginapan,
                    'transportasi' => $transportasi
                ];

                for($n = 0; $n < count($arr_data['id_peserta']); $n++){
                    if($arr_data['id_vendor'][0] != 'other'){
                        $vendor_id = $arr_data['id_vendor'][0];
                    }else{
                        $vendor_id = null;
                    }
                    if($arr_data['biaya_fkt'][0] > 0){
                        $insert[] = [
                            'id_pemohon' => $arr_data['id_pemohon'],
                            'date_pemohon' => date('Y-m-d H:i:s'),
                            'tahun_usulan' => $arr_data['tahun_usulan'],
                            'tahun_pelaksanaan' => $arr_data['tahun_pelaksanaan'],
                            'tipe' => $arr_data['tipe'],
                            'kode' => $data['kode'],
                            'id_peserta' => $arr_data['id_peserta'][$n],
                            'id_penilai' => $arr_data['id_penilai'][0],
                            'judul' => $arr_data['judul'][0],
                            'jenis_pelatihan' => $arr_data['jenis'][0],
                            'sifat' => $arr_data['sifat'][0],
                            'alasan' => $arr_data['alasan'][0],
                            'bulan_pelaksanaan' => $arr_data['bulan_pelaksanaan'][0],
                            'id_vendor' => $vendor_id,
                            'nama_vendor' => $arr_data['vendor_other'][0],
                            'biaya_fkt' => $arr_data['biaya_fkt'][0],
                            'penginapan' => $arr_data['penginapan'][0],
                            'transportasi' => $arr_data['transportasi'][0],
                            'kategori' => 'paid',
                            'status' => 1,
                            'id_checker' => $request->id_checker,
                            'alasan_pti' => $request->alasan_pti,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ];
                    }else{
                        $insert[] = [
                            'id_pemohon' => $arr_data['id_pemohon'],
                            'date_pemohon' => date('Y-m-d H:i:s'),
                            'tahun_usulan' => $arr_data['tahun_usulan'],
                            'tahun_pelaksanaan' => $arr_data['tahun_pelaksanaan'],
                            'tipe' => $arr_data['tipe'],
                            'kode' => $data['kode'],
                            'id_peserta' => $arr_data['id_peserta'][$n],
                            'id_penilai' => $arr_data['id_penilai'][0],
                            'judul' => $arr_data['judul'][0],
                            'jenis_pelatihan' => $arr_data['jenis'][0],
                            'sifat' => $arr_data['sifat'][0],
                            'alasan' => $arr_data['alasan'][0],
                            'bulan_pelaksanaan' => $arr_data['bulan_pelaksanaan'][0],
                            'id_vendor' => $vendor_id,
                            'nama_vendor' => $arr_data['vendor_other'][0],
                            'biaya_fkt' => $arr_data['biaya_fkt'][0],
                            'penginapan' => $arr_data['penginapan'][0],
                            'transportasi' => $arr_data['transportasi'][0],
                            'kategori' => 'free',
                            'status' => 1,
                            'id_checker' => $request->id_checker,
                            'alasan_pti' => $request->alasan_pti,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ];
                    }
                }
            }
            $post = Trainingfkt::insert($insert);

            //insert log user activity
            $insert_log = new Log;
            $insert_log->user_id = $user->id;
            $insert_log->ip_address = $request->ip();
            $insert_log->action = 'insert';
            $insert_log->description = 'Modify formulir kebutuhan training dengan nama pemohon'.'"'.$data['nama_pemohon'].'" tujuan "Program Training Insidentil (PTI)"';
            $insert_log->save();

            return redirect(route('training.emp.index'))->with('tab_pti','open tab')->with('status','Draft Formulir Kebutuhan Training has been updated');
        }

        if($request->action == "submit"){
            $user = auth()->user();
            $data = $request->input();
            $delete_fkt_ptt = Trainingfkt::where('kode', $data['kode'])->delete();
            $nama_pemohon = $request->nama_pemohon;
            $code_random = random_int(100000, 999999);
            for($i = 0; $i < count($data['no_urut']); $i++){
                if($data['id_peserta-'.$data['no_urut'][$i]] && $data['id_penilai-'.$data['no_urut'][$i]] && $data['judul-'.$data['no_urut'][$i]] && $data['jenis_pelatihan-'.$data['no_urut'][$i]] && $data['sifat-'.$data['no_urut'][$i]] && $data['alasan-'.$data['no_urut'][$i]] && $data['bulan_pelaksanaan-'.$data['no_urut'][$i]] && $data['id_vendor-'.$data['no_urut'][$i]] && $data['vendor_other-'.$data['no_urut'][$i]] && $data['biaya_fkt-'.$data['no_urut'][$i]] && $data['penginapan-'.$data['no_urut'][$i]] && $data['transportasi-'.$data['no_urut'][$i]]){
                    $id_peserta = $data['id_peserta-'.$data['no_urut'][$i]];
                    $id_penilai = $data['id_penilai-'.$data['no_urut'][$i]];
                    $judul = $data['judul-'.$data['no_urut'][$i]];
                    $jenis = $data['jenis_pelatihan-'.$data['no_urut'][$i]];
                    $sifat = $data['sifat-'.$data['no_urut'][$i]];
                    $alasan = $data['alasan-'.$data['no_urut'][$i]];
                    $bulan_pelaksanaan = $data['bulan_pelaksanaan-'.$data['no_urut'][$i]];
                    $id_vendor = $data['id_vendor-'.$data['no_urut'][$i]];
                    $vendor_other = $data['vendor_other-'.$data['no_urut'][$i]];
                    $biaya_fkt = str_replace(".","",$data['biaya_fkt-'.$data['no_urut'][$i]]);
                    $penginapan = $data['penginapan-'.$data['no_urut'][$i]];
                    $transportasi = $data['transportasi-'.$data['no_urut'][$i]];
                }
                $arr_data = [
                    'id_pemohon' => $data['id_pemohon'],
                    'tahun_usulan' => $data['tahun_usulan'],
                    'tahun_pelaksanaan' => $data['tahun_pelaksanaan'],
                    'tipe' => $data['tipe'],
                    'id_peserta' => $id_peserta,
                    'id_penilai' => $id_penilai,
                    'judul' => $judul,
                    'jenis' => $jenis,
                    'sifat' => $sifat,
                    'alasan' => $alasan,
                    'bulan_pelaksanaan' => $bulan_pelaksanaan,
                    'id_vendor' => $id_vendor,
                    'vendor_other' => $vendor_other,
                    'biaya_fkt' => $biaya_fkt,
                    'penginapan' => $penginapan,
                    'transportasi' => $transportasi
                ];

                for($n = 0; $n < count($arr_data['id_peserta']); $n++){
                    if($arr_data['id_vendor'][0] != 'other'){
                        $vendor_id = $arr_data['id_vendor'][0];
                    }else{
                        $vendor_id = null;
                    }
                    if($arr_data['biaya_fkt'][0] > 0){
                        $insert[] = [
                            'id_pemohon' => $arr_data['id_pemohon'],
                            'date_pemohon' => date('Y-m-d H:i:s'),
                            'tahun_usulan' => $arr_data['tahun_usulan'],
                            'tahun_pelaksanaan' => $arr_data['tahun_pelaksanaan'],
                            'tipe' => $arr_data['tipe'],
                            'kode' => $data['kode'],
                            'id_peserta' => $arr_data['id_peserta'][$n],
                            'id_penilai' => $arr_data['id_penilai'][0],
                            'kode_judul' => $code_random,
                            'judul' => $arr_data['judul'][0],
                            'jenis_pelatihan' => $arr_data['jenis'][0],
                            'sifat' => $arr_data['sifat'][0],
                            'alasan' => $arr_data['alasan'][0],
                            'bulan_pelaksanaan' => $arr_data['bulan_pelaksanaan'][0],
                            'id_vendor' => $vendor_id,
                            'nama_vendor' => $arr_data['vendor_other'][0],
                            'biaya_fkt' => $arr_data['biaya_fkt'][0],
                            'penginapan' => $arr_data['penginapan'][0],
                            'transportasi' => $arr_data['transportasi'][0],
                            'kategori' => 'paid',
                            'status' => 3,
                            'id_checker' => $request->id_checker,
                            'alasan_pti' => $request->alasan_pti,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ];
                    }else{
                        $insert[] = [
                            'id_pemohon' => $arr_data['id_pemohon'],
                            'date_pemohon' => date('Y-m-d H:i:s'),
                            'tahun_usulan' => $arr_data['tahun_usulan'],
                            'tahun_pelaksanaan' => $arr_data['tahun_pelaksanaan'],
                            'tipe' => $arr_data['tipe'],
                            'kode' => $data['kode'],
                            'id_peserta' => $arr_data['id_peserta'][$n],
                            'id_penilai' => $arr_data['id_penilai'][0],
                            'kode_judul' => $code_random,
                            'judul' => $arr_data['judul'][0],
                            'jenis_pelatihan' => $arr_data['jenis'][0],
                            'sifat' => $arr_data['sifat'][0],
                            'alasan' => $arr_data['alasan'][0],
                            'bulan_pelaksanaan' => $arr_data['bulan_pelaksanaan'][0],
                            'id_vendor' => $vendor_id,
                            'nama_vendor' => $arr_data['vendor_other'][0],
                            'biaya_fkt' => $arr_data['biaya_fkt'][0],
                            'penginapan' => $arr_data['penginapan'][0],
                            'transportasi' => $arr_data['transportasi'][0],
                            'kategori' => 'free',
                            'status' => 3,
                            'id_checker' => $request->id_checker,
                            'alasan_pti' => $request->alasan_pti,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ];
                    }
                }
                //notification atasan departemen
                if($i == 0){
                    $qry_user = User::where('employee_id', $request->id_checker)->first();
                    if(!empty($qry_user->email)){
                        if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                            $details = [
                                'greeting' => 'Hi '.$qry_user->name,
                                'subject' => 'PROGRAM TRAINING INSIDENTIL',
                                'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$arr_data['judul'][0].'" yang membutuhkan approval anda',
                                'actionText' => 'Silahkan Login',
                                'actionURL' => route('training.emp.fkt.pti.approve.back'),
                                'thanks' => 'Terimakasih atas perhatiannya!!'
                            ];
                        }else{
                            $details = [
                                'greeting' => 'Hi '.$qry_user->name,
                                'subject' => 'PROGRAM TRAINING INSIDENTIL',
                                'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$arr_data['judul'][0].'" yang membutuhkan approval anda',
                                'actionText' => 'Silahkan Login',
                                'actionURL' => route('profile.back.fkt.approve.pti'),
                                'thanks' => 'Terimakasih atas perhatiannya!!'
                            ];
                        }
                        //send mail
                        $qry_user->notify(new AccountNotification($details));
                    }
                }
            }
            $post = Trainingfkt::insert($insert);

            //ttd pemohon
            $date_qr = date('Ymd');
            $cek_qr = Qrcodefkt::where('kode_fkt', $data['kode'])->where('type', 1)->first();
            if(!empty($cek_qr)){
                $update_qr = Qrcodefkt::where('kode_fkt', $data['kode'])->where('type','1')->update([
                    'kode_fkt' => $data['kode'],
                    'qr' => $date_qr.$data['id_pemohon'],
                    'date_approval' => date('Y-m-d H:i:s')
                ]);
            }else{
                $insert_qr = new Qrcodefkt;
                $insert_qr->kode_fkt = $data['kode'];
                $insert_qr->qr = $date_qr.$user->employee_id;
                $insert_qr->date_approval = date('Y-m-d H:i:s');
                $insert_qr->type = 1;
                $insert_qr->save();
            }

            //insert log user activity
            $insert_log = new Log;
            $insert_log->user_id = $user->id;
            $insert_log->ip_address = $request->ip();
            $insert_log->action = 'insert';
            $insert_log->description = 'Modify formulir kebutuhan training dengan nama pemohon'.'"'.$nama_pemohon.'" tujuan "Program Training Insidentil (PTI)"';
            $insert_log->save();

            return redirect(route('training.emp.fkt.pti.back'))->with('status','Formulir Kebutuhan Training '.$nama_pemohon.' has been updated');
        }
    }
    ///fkt approve
    public function emp_fkt_pti_approved(Request $request){
        $user = auth()->user();
        $cek_dept = Trainingfpkt::where('id_dept_head', $user->employee_id)
        ->whereNotNull('date_atasan')
        ->whereNull('date_dept_head')->count();

        $cek_atasan = Trainingfpkt::where('id_atasan', $user->employee_id)
        ->whereNotNull('date_peserta')
        ->whereNull('date_atasan')->count();
        
        if($cek_dept > 0 && $cek_atasan > 0){            
            $data = Trainingfpkt::where(function ($data) use ($user) {
                $data->where('id_dept_head', $user->employee_id);
                $data->orWhere('id_atasan', $user->employee_id);
            })->where(function ($data) {
                $data->where('date_atasan','!=', null);
                $data->orWhere('date_peserta','!=', null);
            })->where(function ($data) {
                $data->where('status', 10);
                $data->orWhere('status', 9);
            })->get();
        }else{
            if($cek_dept > 0){
                $data = Trainingfpkt::where(function ($data) use ($user) {
                    $data->where('id_dept_head', $user->employee_id);
                })->whereNotNull('date_atasan')->where('status', 10)->get();
            }else{
                if($cek_atasan > 0){
                    $data = Trainingfpkt::where(function ($data) use ($user) {
                        $data->where('id_atasan', $user->employee_id);
                    })->whereNotNull('date_peserta')->where('status',9)->get();
                }else{
                    $data = array();
                }
            }
        }
        if ($request->ajax()) {          
            return DataTables::of($data)
                ->addColumn('pelatihan', function($data){
                    return $data->judul_fpkt;
                })
                ->addColumn('peserta', function($data){
                    return $data->peserta->fullname;
                })
                ->addColumn('provider', function($data){
                    if($data->id_vendor == null){
                        return $data->nama_vendor ?? '-';
                    }else{
                        return $data->vendor->nama ?? '-';
                    }
                })
                ->addColumn('biaya', function($data){
                    return 'Rp '.number_format($data->biaya_fpkt,2,',','.');
                })
                ->addColumn('status', function($data){
                    if($data['status'] == 9) return '<a href="#" data-id="'.encrypt($data->id).'" data-bs-toggle="modal" data-bs-target="#modal-status-fpkt" <span class="badge text-bg-warning view-status-fpkt"><i class="ri-time-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 10) return '<a href="#" data-id="'.encrypt($data->id).'" data-bs-toggle="modal" data-bs-target="#modal-status-fpkt" <span class="badge text-bg-secondary view-status-fpkt"><i class="ri-time-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                })
                ->addColumn('action', function ($data) {
                    $button = '<div class="dropdown d-inline-block">';
                        $button .= '<button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button>';
                        $button .= '<ul class="dropdown-menu dropdown-menu-end">';
                            if($data->status == 9){
                                $button .= '<li><a href="'. route('training.emp.fpkt.pti.form', encrypt($data->id)).'" data-toggle="tooltip" title="Beri Nilai" class="dropdown-item"><i class="ri-edit-line align-bottom me-2 text-muted"></i> Beri Nilai</a></li>';
                            }else{
                                $button .= '<li><a href="#" data-id="'.encrypt($data->id).'" data-bs-toggle="modal" data-bs-target=".bs-example-modal-center" class="dropdown-item view-approve"><i class="ri-checkbox-line align-bottom me-2 text-muted"></i> Approve</a></li>';
                            }
                            if(!empty($data->id_fkt)){
                                $button .= '<li><a href="'.route('public.training.fkp.pdf', encrypt($data->fkt->kode)).'" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FKP</a></li>';
                            }
                            $button .= '<li><a href="'.route('public.training.fpkp.pdf', encrypt($data->id)).'" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FPKP</a></li>';
                        $button .= '</ul>';
                    $button .= '</div>';
                    return $button;
                })                
                ->rawColumns(['action','status'])
                ->addIndexColumn()
                ->make(true);
        }
    }
    public function emp_fpkt_pti_approved_store(Request $request){
        DB::beginTransaction();
        try {

            $user = auth()->user();
            $query = Trainingfpkt::where('id', decrypt($request->id_fpkt))->first();
            $post = Trainingfpkt::where('id', decrypt($request->id_fpkt))->update([
                'date_dept_head' => date('Y-m-d H:i:s'),
                'status' => 11
            ]);            
            //atasan departemen ttd
            $date_qr = date('Ymd');
            $insert_approved_qr = new Qrcodefpkt;
            $insert_approved_qr->id_fpkt = $query->id;
            $insert_approved_qr->qr = $date_qr.$user->employee_id;
            $insert_approved_qr->date_approval = date('Y-m-d H:i:s');
            $insert_approved_qr->type = 3;
            $insert_approved_qr->save();

            //send email to pic hrd
            $users = User::whereHas(
                'roles', function($q){
                    $q->where('id', 2);
                }
            )->get();
            if($users->isNotEmpty()){
                foreach($users as $key_user){
                    if(!empty($key_user->email)){
                        $qry_user = User::where('employee_id', $key_user->employee_id)->first();
                        $details = [
                            'greeting' => 'Hi '.$qry_user->name,
                            'subject' => 'Pengajuan Pelaksanaan Pelatihan',
                            'body' => 'Ingin Menginformasikan bahwa ada usulan pelatihan "'.$query->judul_fpkt.'" yang sudah disetujui dan memerlukan verifikasi anda',
                            'actionText' => 'Silahkan Login',
                            'actionURL' => url('/hrd/training/pti'),
                            'thanks' => 'Terimakasih atas perhatiannya!!'
                        ];
                        //send mail
                        $qry_user->notify(new AccountNotification($details));
                    }
                }
            }
    
            //update log user activity
            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'approved';
            $insert->description = 'Approved dept head formulir kebutuhan Pelatihan '.'"'.$query->judul_fpkt.'"';
            $insert->save();

            DB::commit();

            return response()->json(['message' => "$query->judul_fpkt has been approved"], 200);
        }catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }
    ///back pti
    public function emp_fkt_pti_back(request $request){
        return redirect(route('training.emp.index'))->with('tab_pti','open tab');
    }
    public function emp_fkt_pti_approve_back(Request $request){
        return redirect(route('training.emp.index'))->with('tab_approve_pti','open tab');
    }
    ///fpkt
    public function emp_fpkt_pti_form(Request $request, $id){
        $user = auth()->user();
        $kode = $id;
        $id = decrypt($id);
        $fpkt = Trainingfpkt::where('id', $id)->first();
        $arr_tujuan = $fpkt->tujuan;
        $arr_kompetensi = $fpkt->kompetensi;
        $arr_skill = json_decode($fpkt->skill, true);
        $arr_peserta = json_decode($fpkt->level_peserta, true);
        $arr_atasan = json_decode($fpkt->level_atasan, true);
        $arr_rata = json_decode($fpkt->level_rata, true);
        $arr_kebutuhan = json_decode($fpkt->level_kebutuhan, true);

        if(isset($arr_skill)){
            for($i = 0; $i < count($arr_skill); $i++){
                $arr_fpkt['tujuan'] = $arr_tujuan;
                $arr_fpkt['kompetensi'] = $arr_kompetensi;
                $arr_fpkt['skill'] = $arr_skill[$i];
                if(!empty($arr_peserta)){
                    $arr_fpkt['level_peserta'] = $arr_peserta[$i];
                }else{
                    $arr_fpkt['level_peserta'] = '';
                }
                if(!empty($arr_atasan)){
                    $arr_fpkt['level_atasan'] = $arr_atasan[$i];
                }else{
                    $arr_fpkt['level_atasan'] = '';
                }
                if(!empty($arr_rata)){
                    $arr_fpkt['level_rata'] = $arr_rata[$i];
                }else{
                    $arr_fpkt['level_rata'] = '';
                }
                if(!empty($arr_kebutuhan)){
                    $arr_fpkt['level_kebutuhan'] = $arr_kebutuhan[$i];
                }else{
                    $arr_fpkt['level_kebutuhan'] = '';
                }
                $arr_data[] = $arr_fpkt;
            }
        }else{
            $arr_data = array();
        }
        $cek_peserta = Trainingfpkt::where('id', $id)->where('id_peserta', $user->employee_id)->first(); 
        $cek_atasan = Trainingfpkt::where('id', $id)->where('id_atasan', $user->employee_id)->first();  
        return view('pages.employee.training.pti.form-fpkt', compact('kode','user','fpkt','cek_peserta','cek_atasan','arr_data'));
    }
    public function emp_fpkt_pti_store(request $request){
        DB::beginTransaction();
        try {
            if($request->action == 'penilaian'){
                $user = auth()->user();
                $data = $request->input();
                // dd($data);
                $fpkt = Trainingfpkt::find($data['id_fpkt']);
                if(isset($data['no_urut'])){
                    //approve penilai
                    $sum_peserta = array_sum($data['level_peserta']);
                    $sum_atasan = array_sum($data['level_atasan']);
                    if($sum_peserta > 0){
                        if(empty($fpkt->date_peserta)){
                            $date_peserta = date('Y-m-d H:i:s');
                            //ttd peserta
                            $date_qr = date('Ymd');
                            $insert_fpkt_qr = new Qrcodefpkt;
                            $insert_fpkt_qr->id_fpkt = $fpkt->id;
                            $insert_fpkt_qr->qr = $date_qr.$fpkt->id_peserta;
                            $insert_fpkt_qr->date_approval = date('Y-m-d H:i:s');
                            $insert_fpkt_qr->type = 1;
                            $insert_fpkt_qr->save();
    
                            if(empty($fpkt->date_atasan)){
                                //notification atasan langsung
                                if(!empty($fpkt->atasan->email)){
                                    $qry_user = User::where('employee_id', $fpkt->id_atasan)->first();
                                    if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                                        $details = [
                                            'greeting' => 'Hi '.$fpkt->atasan->fullname,
                                            'subject' => 'Penilaian Kebutuhan Pelatihan',
                                            'body' => 'Ingin Menginformasikan bahwa ada usulan pelatihan "'.$fpkt->judul_fpkt.'" yang membutuhkan penilaian anda',
                                            'actionText' => 'Silahkan Login',
                                            'actionURL' => url('/employee/training/fpkt/pti/'.encrypt($fpkt->id).'/form'),
                                            'thanks' => 'Terimakasih atas perhatiannya!!'
                                        ];
                                    }else{
                                        $details = [
                                            'greeting' => 'Hi '.$fpkt->atasan->fullname,
                                            'subject' => 'Penilaian Kebutuhan Pelatihan',
                                            'body' => 'Ingin Menginformasikan bahwa ada usulan pelatihan "'.$fpkt->judul_fpkt.'" yang membutuhkan penilaian anda',
                                            'actionText' => 'Silahkan Login',
                                            'actionURL' => url('/mytrainingfpkt/pti/'.encrypt($fpkt->id).'/form'),
                                            'thanks' => 'Terimakasih atas perhatiannya!!'
                                        ];
                                    }
                                    //send mail
                                    $qry_user->notify(new AccountNotification($details));
                                }
                            }
                        }else{
                            $date_peserta = $fpkt->date_peserta;
                        }
                    }else{
                        $date_peserta = null;
                    }
                    if($sum_atasan > 0){
                        if(empty($fpkt->date_atasan)){
                            $date_atasan = date('Y-m-d H:i:s');
                            //ttd atasan
                            $date_qr = date('Ymd');
                            $insert_fpkt_qr = new Qrcodefpkt;
                            $insert_fpkt_qr->id_fpkt = $fpkt->id;
                            $insert_fpkt_qr->qr = $date_qr.$fpkt->id_atasan;
                            $insert_fpkt_qr->date_approval = date('Y-m-d H:i:s');
                            $insert_fpkt_qr->type = 2;
                            $insert_fpkt_qr->save();
    
                            if(empty($fpkt->date_peserta)){
                                //notification peserta
                                if(!empty($fpkt->peserta->email)){
                                    $qry_user = User::where('employee_id', $fpkt->id_peserta)->first();
                                    if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                                        $details = [
                                            'greeting' => 'Hi '.$fpkt->peserta->fullname,
                                            'subject' => 'Penilaian Kebutuhan Pelatihan',
                                            'body' => 'Ingin Menginformasikan bahwa ada usulan pelatihan "'.$fpkt->judul_fpkt.'" yang membutuhkan penilaian anda',
                                            'actionText' => 'Silahkan Login',
                                            'actionURL' => url('/employee/training/fpkt/pti/'.encrypt($fpkt->id).'/form'),
                                            'thanks' => 'Terimakasih atas perhatiannya!!'
                                        ];
                                    }else{
                                        $details = [
                                            'greeting' => 'Hi '.$fpkt->peserta->fullname,
                                            'subject' => 'Penilaian Kebutuhan Pelatihan',
                                            'body' => 'Ingin Menginformasikan bahwa ada usulan pelatihan "'.$fpkt->judul_fpkt.'" yang membutuhkan penilaian anda',
                                            'actionText' => 'Silahkan Login',
                                            'actionURL' => url('/mytrainingfpkt/pti/'.encrypt($fpkt->id).'/form'),
                                            'thanks' => 'Terimakasih atas perhatiannya!!'
                                        ];
                                    }
                                    //send mail
                                    $qry_user->notify(new AccountNotification($details));
                                }
                            }
                        }else{
                            $date_atasan = $fpkt->date_atasan;
                        }
                    }else{
                        $date_atasan = null;
                    }
                    //function level peserta
                    $arr_level_peserta = collect($data['level_peserta'])->every(function ($value) {
                        return is_null($value);
                    });
                    if($arr_level_peserta) {
                        $level_peserta = null;
                    }else{
                        $level_peserta = json_encode($data['level_peserta']);
                    }
    
                    //function level atasan
                    $arr_level_atasan = collect($data['level_atasan'])->every(function ($value) {
                        return is_null($value);
                    });
                    if($arr_level_atasan) {
                        $level_atasan = null;
                    }else{
                        $level_atasan = json_encode($data['level_atasan']);
                    }
    
                    //function level rata
                    $arr_level_rata = collect($data['level_rata'])->every(function ($value) {
                        return is_null($value);
                    });
                    if($arr_level_rata) {
                        $level_rata = null;
                    }else{
                        $level_rata = json_encode($data['level_rata']);
                    }
    
                    //function level kebutuhan
                    $arr_level_kebutuhan = collect($data['level_kebutuhan'])->every(function ($value) {
                        return is_null($value);
                    });
                    if($arr_level_kebutuhan) {
                        $level_kebutuhan = null;
                    }else{
                        $level_kebutuhan = json_encode($data['level_kebutuhan']);
                    }
                    //function quisioner
                    if(!empty($fpkt->analisa_satu)){
                        $analisa_satu = $fpkt->analisa_satu;
                    }else{
                        $analisa_satu = $data['catatan_satu'];
                    }
                    if(!empty($fpkt->analisa_dua)){
                        $analisa_dua = $fpkt->analisa_dua;
                    }else{
                        $analisa_dua = $data['catatan_dua'];
                    }
                    if(!empty($fpkt->analisa_tiga)){
                        $analisa_tiga = $fpkt->analisa_tiga;
                    }else{
                        $analisa_tiga = $data['catatan_tiga'];
                    }
                    //function status
                    if($sum_peserta > 0 && $sum_atasan > 0){
                        $status = 10;
                        //notification atasan departemen
                        if(!empty($fpkt->atasan_dept->email)){
                            $qry_user = User::where('employee_id', $fpkt->id_dept_head)->first();
                            if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                                $details = [
                                    'greeting' => 'Hi '.$fpkt->atasan_dept->fullname,
                                    'subject' => 'Penilaian Kebutuhan Pelatihan',
                                    'body' => 'Ingin Menginformasikan bahwa ada usulan pelatihan "'.$fpkt->judul_fpkt.'" yang membutuhkan penilaian anda',
                                    'actionText' => 'Silahkan Login',
                                    'actionURL' => route('training.emp.fkt.pti.approve.back'),
                                    'thanks' => 'Terimakasih atas perhatiannya!!'
                                ];
                            }else{
                                $details = [
                                    'greeting' => 'Hi '.$fpkt->atasan_dept->fullname,
                                    'subject' => 'Penilaian Kebutuhan Pelatihan',
                                    'body' => 'Ingin Menginformasikan bahwa ada usulan pelatihan "'.$fpkt->judul_fpkt.'" yang membutuhkan penilaian anda',
                                    'actionText' => 'Silahkan Login',
                                    'actionURL' => url('/mytrainingfpkt/pti/'.encrypt($fpkt->id).'/form'),
                                    'thanks' => 'Terimakasih atas perhatiannya!!'
                                ];
                            }
                            //send mail
                            $qry_user->notify(new AccountNotification($details));
                        }
                    }else{
                        $status = 9;
                    }
                    $post = Trainingfpkt::where('id', $data['id_fpkt'])->update([
                        'tujuan' => $data['tujuan'],
                        'kompetensi' => $data['kompetensi'],
                        'skill' => json_encode($data['skill']),
                        'level_peserta' => $level_peserta,
                        'level_atasan' => $level_atasan,
                        'level_rata' => $level_rata,
                        'level_kebutuhan' => $level_kebutuhan,
                        'catatan' => $data['catatan'],
                        'analisa_satu' => $analisa_satu,
                        'analisa_dua' => $analisa_dua,
                        'analisa_tiga' => $analisa_tiga,
                        'date_peserta' => $date_peserta,
                        'date_atasan' => $date_atasan,
                        'status' => $status
                    ]);

                    DB::commit();

                    if($fpkt->id_atasan == $user->employee_id && $fpkt->date_peserta != null){
                        $page = redirect(route('training.emp.fkt.pti.approve.back'))->with('status','Formulir Penilaian Kebutuhan Pelatihan '.$fpkt->judul_fpkt.' has been updated');
                    }else{
                        $page = redirect(route('training.emp.fkt.pti.detail', encrypt($fpkt->kode_judul_fpkt)))->with('status','Formulir Penilaian Kebutuhan Pelatihan '.$fpkt->judul_fpkt.' has been updated');
                    }
                }else{
                    $page = redirect(route('training.emp.fkt.pti.detail', encrypt($fpkt->kode_judul_fpkt)))->with('error','Formulir Penilaian Kebutuhan Pelatihan no changes');
                }
            }
        }catch (Exception $e) {
            DB::rollback();             
            $message = $e->getMessage();
            var_dump('Exception Message: '. $message);  
            exit;
        }
        return $page;
    }
    ///collective pti
    public function emp_fpkt_pti_collective(Request $request, $id){
        $user = auth()->user();
        $fkt = Trainingfkt::where('kode',decrypt($id))->where('judul', $request->pelatihan)->first();
        $qry_fkt = Trainingfkt::where('kode',decrypt($id))->where('judul', $request->pelatihan)->get();
        $arr_peserta = Employee::whereIn('id', $qry_fkt->pluck('id_peserta'))->get();
        return view('pages.employee.training.pti.form-collective', compact('user','fkt','qry_fkt','arr_peserta'));
    }
    public function emp_fpkt_pti_collective_approve(Request $request, $id){
        $user = auth()->user();
        $kode = $id;
        $id = decrypt($id);
        $fkt = Trainingfkt::where('kode', $id)->first();
        $fpkt = Trainingfpkt::where('id_fkt', $fkt->id)->first(); 
        $arr_fpkt = Trainingfpkt::where('id_fkt', $fkt->id)->get(); 
        $cek_pemohon = Trainingfkt::where('kode', $id)->where('id_pemohon', $user->employee_id)->first(); 
        $cek_peserta = Trainingfkt::where('kode', $id)->where('id_peserta', $user->employee_id)->first(); 
        $cek_atasan = Trainingfkt::where('kode', $id)->where('id_penilai', $user->employee_id)->first(); 
        
        $qry_fkt = Trainingfkt::where('kode',$id)->where('judul', $fkt->judul)->get();
        $arr_peserta = Employee::whereIn('id', $qry_fkt->pluck('id_peserta'))->get();

        return view('pages.employee.training.pti.form-collective-approve', compact('kode','user','fkt','fpkt','cek_pemohon','cek_peserta','cek_atasan','arr_fpkt','qry_fkt','arr_peserta'));
    } 
    //end employee view training
}