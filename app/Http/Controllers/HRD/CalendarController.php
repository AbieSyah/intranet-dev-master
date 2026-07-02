<?php

namespace App\Http\Controllers\HRD;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Exception;
use Illuminate\Http\Request;
use App\Models\Calendar;
use App\Models\Tempcalendar;
use App\Models\Leave;
use App\Models\Log;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Response;
use Auth;
use Illuminate\Support\Facades\URL;

class CalendarController extends Controller
{
    public function index(Request $request){
        $arr_year = Tempcalendar::get()->pluck('tahun')->toArray();
        $min_year = min($arr_year);
        $max_year = max($arr_year)+1;        

        if ($request->ajax()) {
            $query = Tempcalendar::all();
            return DataTables::of($query)
                ->addColumn('action', function ($data) {
                    if(\Auth::user()->can('hrd.calendar.template.update')){
                        $button = '<button data-toggle="tooltip" title="Edit" data-id="' . encrypt($data['id']) . '" data-original-title="Edit" class="btn btn-warning btn-sm edit-btn"><i class="ri-edit-line align-bottom"></i> Edit</button>';
                    }else{
                        $button = '';
                    }
                    $button .= '&nbsp;';
                    if(\Auth::user()->can('hrd.calendar.template.detail')){
                        $button .= '<a href="'. route('calendar.detail', encrypt($data['id'])).'" title="Detail" class="btn btn-primary btn-sm"><i class="ri-calendar-event-fill align-bottom"></i> Calendar</a>';
                    }else{
                        $button .= '';
                    }
                    $button .= '&nbsp;';
                    if(\Auth::user()->can('hrd.calendar.template.view')){
                        $button .= '<button type="button" id="preview_file" data-bs-toggle="modal" data-bs-target="#modal-preview" class="btn btn-info btn-sm"><i class="ri-zoom-in-line align-bottom"></i> View Calendar</button> <input type="hidden" class="form-control" id="id_preview" name="id_preview" value="'.route('calendar.pdf', encrypt($data['id'])).'"/>';
                        // $button .= '<a href="https://docs.google.com/viewerng/viewer?url='.$url.'" target="_blank" title="View Calendar" class="btn btn-info btn-sm"><i class="ri-zoom-in-line align-bottom"></i> View Calendar</a>';
                    }else{
                        $button .= '';
                    }
                    if(\Auth::user()->can('hrd.calendar.template.upload')){
                        $button .= '<button type="button" id="upload_file" data-bs-toggle="modal" data-bs-target="#upload-calendar" class="btn btn-danger btn-sm"><i class="ri-file-upload-line align-bottom"></i> Upload</button><input type="hidden" class="form-control" id="calendar_id" name="calendar_id" value="'.encrypt($data['id']).'"/><input type="hidden" class="form-control" id="tahun_upload" name="tahun_upload" value="'.$data['tahun'].'"/>';
                    }else{
                        $button .= '';
                    }
                    // if(!empty($data['file_calendar'])){
                    //     // $url = URL::asset('storage/calendar/'.$data['file_calendar']);
                    //     if(\Auth::user()->can('hrd.calendar.template.view')){
                    //         $button .= '<button type="button" id="preview_file" data-bs-toggle="modal" data-bs-target="#modal-preview" class="btn btn-info btn-sm"><i class="ri-zoom-in-line align-bottom"></i> View Calendar</button> <input type="hidden" class="form-control" id="id_preview" name="id_preview" value="'.route('calendar.pdf', encrypt($data['id'])).'"/>';
                    //         // $button .= '<a href="https://docs.google.com/viewerng/viewer?url='.$url.'" target="_blank" title="View Calendar" class="btn btn-info btn-sm"><i class="ri-zoom-in-line align-bottom"></i> View Calendar</a>';
                    //     }else{
                    //         $button .= '';
                    //     }
                    //     if(\Auth::user()->can('hrd.calendar.template.upload')){
                    //         $button .= '<button type="button" id="upload_file" data-bs-toggle="modal" data-bs-target="#upload-calendar" class="btn btn-danger btn-sm"><i class="ri-file-upload-line align-bottom"></i> Upload</button><input type="hidden" class="form-control" id="calendar_id" name="calendar_id" value="'.encrypt($data['id']).'"/><input type="hidden" class="form-control" id="tahun_upload" name="tahun_upload" value="'.$data['tahun'].'"/>';
                    //     }else{
                    //         $button .= '';
                    //     }
                    // }else{
                    //     if(\Auth::user()->can('hrd.calendar.template.upload')){
                    //         $button .= '<button type="button" id="upload_file" data-bs-toggle="modal" data-bs-target="#upload-calendar" class="btn btn-danger btn-sm"><i class="ri-file-upload-line align-bottom"></i> Upload</button><input type="hidden" class="form-control" id="calendar_id" name="calendar_id" value="'.encrypt($data['id']).'"/><input type="hidden" class="form-control" id="tahun_upload" name="tahun_upload" value="'.$data['tahun'].'"/>';
                    //     }else{
                    //         $button .= '';
                    //     }
                    // }
                    return $button;
                })
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.calendar.index', compact('min_year','max_year'));
    }
    public function view_pdf($id){
        $query = Tempcalendar::find(decrypt($id));
        $file_calendar = public_path('storage/calendar/'.$query->file_calendar);
        
        // return response()->file($lampiran_rule);
        $file = File::get($file_calendar);
        $response = Response::make($file, 200);
        $response->header('Content-Type', 'application/pdf');
        $response->header('Content-Disposition', 'filename=' . '"'.$query->tahun.'.pdf"');
        $response->header('Content-Transfer-Encoding', 'binary');
        return $response;
    }

    public function edit(Request $request){
        $id = decrypt($request->id);
        $query = Tempcalendar::find($id);

        if (!$query) {
            return response()->json(['message' => 'Data not found'], 404);
        }
        return response()->json($query);
    }

    public function store(Request $request){
        DB::beginTransaction();

        try {
            $user = auth()->user();
            $id = $request->id;
            $tahun = $request->tahun;
            $query = Tempcalendar::updateOrCreate(['id' => $id], ['tahun' => $tahun]);

            DB::commit();

            //insert log user activity
            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'insert';
            $insert->description = 'Create Template Calendar '.'"'.$tahun.'"';
            $insert->save();

            return response()->json(['message' => "Template calendar $query->tahun has been created"], 200);
        } catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    public function detail(Request $request, $id){
        if(!empty($request->select_type)){
            $type = $request->select_type;
        }else{
            $type = 2;
        }
        $data_type = [
            '1' => 'HEAD OFFICE / SALES, MARKETING',
            '2' => 'HEADQUARTERS / FACTORY'
        ];
        $kode = $id;
        $leaves = Leave::all();
        $temp_calendar = Tempcalendar::find(decrypt($id));
        $tahun = $temp_calendar->tahun;

        $date_now = date('Y-m-d');

        //view calendar
        $query = Calendar::where('id_temp_calendar', decrypt($id))->where('type', $type)->orderBy('tanggal_awal','asc')->get();
        if($query->isNotEmpty()){
            foreach($query as $qry){
                if(!empty($qry->tanggal_akhir)){
                    $data['id'] = $qry->id;
                    $data['title'] = $qry->event;
                    $data['start'] = $qry->tanggal_awal;
                    $data['end'] = date('Y-m-d',strtotime($qry->tanggal_akhir . "+1 days"));
                    $data['className'] = $qry->leave->badge;
                    $start = date('d M Y', strtotime($qry->tanggal_awal));
                    $end = date('d M Y',strtotime($qry->tanggal_akhir));
                    $data['dateup'] = $start.' to '.$end;
                }else{
                    $data['id'] = $qry->id;
                    $data['title'] = $qry->event;
                    $data['start'] = $qry->tanggal_awal;
                    $data['end'] = null;
                    $data['className'] = $qry->leave->badge;
                    $start = date('d M Y', strtotime($qry->tanggal_awal));
                    $data['dateup'] = $start;
                }
                $data_all[] = $data;
            }
        }else{
            $data_all = array();
        }
        // dd($data_all);
        return view('pages.hrd.calendar.detail', compact('data_type','type','leaves','tahun','kode','data_all','date_now'));
    }

    public function upload(Request $request){
        DB::beginTransaction();

        try {
            $user = auth()->user();

            $calendar = Tempcalendar::where('id', decrypt($request->id_calendar))->first();
            DB::commit();
            if(!empty($request->file('file'))){
                $cek_file = storage_path('app/public/calendar/'.$calendar->file_calendar);
                if (File::exists($cek_file)) {
                    File::delete($cek_file);
                }
                $file = $request->file('file');
                $nama_file = $calendar->tahun.'.'.$file->getClientOriginalExtension();
                $request->file->storeAs('public/calendar', $nama_file);
                //update calendar
                $post = Tempcalendar::where('id', decrypt($request->id_calendar))->update(['file_calendar' => $nama_file]);
                // DB::table('temp_calendar')->where('id',decrypt($request->id_calendar))->update(['file_calendar' => $nama_file]);
                //insert log user activity
                $insert = new Log;
                $insert->user_id = $user->id;
                $insert->ip_address = $request->ip();
                $insert->action = 'upload';
                $insert->description = 'Upload file calendar tahun "'.$calendar->tahun.'"';
                $insert->save();
            }
            return response()->json(['message' => "Calendar file $calendar->tahun has been upload"], 200);
        } catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    // public function lampiran_calendar($id){
    //     $temp_calendar = Tempcalendar::find(decrypt($id));
    //     $test = URL::asset('storage/calendar/'.$temp_calendar->file_calendar);
    //     // dd($test);
    //     $lampiran_calendar = public_path('storage/calendar/'.$temp_calendar->file_calendar);
    //     // $file = File::get($lampiran_calendar);
    //     // $response = Response::make($file, 200);
    //     // $response->header('Content-Type', 'application/pdf');
    //     // $response->header('Content-Disposition', 'filename=' . '"'.$temp_calendar->tahun.'.pdf"');
    //     // $response->header('Content-Transfer-Encoding', 'binary');
    //     return response()->json($test);
    // }

    public function download($id){
        $query = Tempcalendar::find(decrypt($id));
        $unduh = public_path('storage/calendar/'.$query->file_calendar);
        return response()->download($unduh);
    }

    public function event_calendar_store(Request $request){   
        DB::beginTransaction();

        try {
            $user = auth()->user();
            for($i = 0; $i < count($request->type); $i++){
                $leave = Leave::find($request->leave);
                $id = decrypt($request->id_template);
                $id_leave = $request->leave;
                if(!empty($request->event) || $request->event != ''){
                    $event = $request->event;
                }else{
                    $event = $leave->nama;
                }
                $type = $request->type[$i];
                if(str_contains($request->tanggal, 'to')){
                    $exp = explode(' to ', $request->tanggal);
                    $tanggal_awal = $exp[0];
                    $tanggal_akhir = $exp[1];
                }else{
                    $tanggal_awal = $request->tanggal;
                    $tanggal_akhir = null;
                }
                //insert event
                $insert = new Calendar;
                $insert->id_temp_calendar = $id;
                $insert->id_leave = $id_leave;
                $insert->event = $event;
                $insert->type = $type;
                $insert->tanggal_awal = $tanggal_awal;
                $insert->tanggal_akhir = $tanggal_akhir;
                $insert->save();
    
                DB::commit();

                if($type == '1'){
                    $type_nama = 'HEAD OFFICE / SALES, MARKETING';
                }else{
                    $type_nama = 'HEADQUARTERS / FACTORY';
                }
    
                //insert log user activity
                $insert = new Log;
                $insert->user_id = $user->id;
                $insert->ip_address = $request->ip();
                $insert->action = 'insert';
                if(str_contains($request->tanggal, 'to')){
                    $insert->description = 'Add Event Calendar '.'"'.$event.'" type "'.$type_nama.'" Tanggal "'.$tanggal_awal.'" sampai "'.$tanggal_akhir.'"';
                }else{
                    $insert->description = 'Add Event Calendar '.'"'.$event.'" type "'.$type_nama.'" Tanggal "'.$tanggal_awal.'"';
                }
                $insert->save();
            }

            return response()->json(['message' => "$event has been created"], 200);
        } catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    public function view(Request $request){
        $query = Calendar::where('id', $request->id_calendar)->first();
        $query['leave'] = $query->leave->nama;
        if(!empty($query->tanggal_akhir)){
            $tgl = date('d F Y', strtotime($query->tanggal_awal));
            $tgl2 = date('d F Y', strtotime($query->tanggal_akhir));
            $query['tgl'] = $tgl." sampai ".$tgl2;
            $query['range'] = $query->tanggal_awal." to ".$query->tanggal_akhir;
        }else{
            $tgl = date('d F Y', strtotime($query->tanggal_awal));
            $query['tgl'] = $tgl;
            $query['range'] = $query->tanggal_awal;
        }
        return response()->json($query);
    }

    public function event_calendar_update(Request $request){
        DB::beginTransaction();

        try {
            $user = auth()->user();
            //cek date range
            if(str_contains($request->edit_tgl, 'to')){
                $exp = explode(' to ', $request->edit_tgl);
                $tanggal_awal = $exp[0];
                $tanggal_akhir = $exp[1];
            }else{
                $tanggal_awal = $request->edit_tgl;
                $tanggal_akhir = null;
            }
            $query = Calendar::where('id', $request->id_edit)->update([
                'event' => $request->edit_event,
                'tanggal_awal' => $tanggal_awal,
                'tanggal_akhir' => $tanggal_akhir
            ]);
            $calendar = Calendar::where('id', $request->id_edit)->first();
            if($calendar->type == 1){
                $type = 'HEAD OFFICE / SALES, MARKETING';
            }else{
                $type = 'HEADQUARTERS / FACTORY';
            }
            DB::commit();
            //insert log user activity
            $insert_log = new Log;
            $insert_log->user_id = $user->id;
            $insert_log->ip_address = $request->ip();
            $insert_log->action = 'update';
            if(str_contains($request->edit_tgl, 'to')){
                $insert_log->description = 'modify calendar event '.'"'.$calendar->event.'" tanggal "'.$tanggal_awal.'" sampai "'.$tanggal_akhir.'" type "'.$type.'"';
            }else{
                $insert_log->description = 'modify calendar event '.'"'.$calendar->event.'" tanggal "'.$tanggal_awal.'" type "'.$type.'"';

            }
            $insert_log->save();

            return response()->json(['message' => "$calendar->event has been updated"], 200);
        } catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    public function event_calendar_delete(Request $request){
        DB::beginTransaction();

        try {
            $calendar = Calendar::find($request->id_delete);
            if($calendar->type == 1){
                $type = 'HEAD OFFICE / SALES, MARKETING';
            }else{
                $type = 'HEADQUARTERS / FACTORY';
            }
            //insert log user activity
            $user = auth()->user();
            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'delete';
            if(!empty($calendar->tanggal_akhir)){
                $insert->description = 'Remove calendar event'.'"'.$calendar->event.'" tanggal '.'"'.$calendar->tanggal_awal.'" sampai "'.$calendar->tanggal_akhir.'" type "'.$type.'"';
            }else{
                $insert->description = 'Remove calendar event'.'"'.$calendar->event.'" tanggal '.'"'.$calendar->tanggal_awal.'" type "'.$type.'"';
            }
            $insert->save();
            //delete event calendar
            $post = Calendar::where('id', $request->id_delete)->delete();
            DB::commit();

            return response()->json(['message' => "$calendar->event has been removed"], 200);
        } catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    //employee view
    // public function emp_index(Request $request){
    //     $user = auth()->user();
    //     if ($request->ajax()) {
    //         $query = Tempcalendar::all();
    //         return DataTables::of($query)
    //             ->addColumn('action', function ($data) {
    //                 $button = '<a href="'. route('calendar.emp.detail', encrypt($data['id'])).'" title="Detail Calendar" class="btn btn-primary btn-sm"><i class="ri-calendar-event-fill align-middle"></i> Show</a>';
    //                 return $button;
    //             })
    //             ->addIndexColumn()
    //             ->make(true);
    //     }
    //     return view('pages.employee.calendar.index', compact('user'));
    // }
    public function emp_index(Request $request){
        $user = auth()->user();
        $data_type = [
            '1' => 'HEAD OFFICE / SALES, MARKETING',
            '2' => 'HEADQUARTERS / FACTORY'
        ];
        //filter calendar
        if(!empty($request->select_type)){
            $type = $request->select_type;
        }else{
            $area_id = $user->employee->area_id ?? null;
            if($area_id == 1){
                $type = 2;
            }else{
                $type = 1;
            }
        }
        $arr_tahun = Tempcalendar::get()->pluck('tahun')->toArray();
        // dd($arr_tahun);
        $min =  min($arr_tahun);
        $max = max($arr_tahun)+1;
        //ambil tahun dipilih
        $from_year = $request->select_tahun;
        //ambil tahun sekarang
        $year_now = date('Y');
        if(!empty($from_year)){
            $tahun = $from_year;
            $temp_calendar = Tempcalendar::where('tahun', $tahun)->first();
            if(!empty($temp_calendar)){
                $kode = encrypt($temp_calendar->id);
                //view calendar
                $query = Calendar::where('id_temp_calendar', decrypt($kode))->where('type', $type)->orderBy('tanggal_awal','asc')->get();
            }else{
                $kode = '';
                //view calendar
                $query = Calendar::where('id_temp_calendar', $kode)->where('type', $type)->orderBy('tanggal_awal','asc')->get();
            }
        }else{
            $tahun = $year_now;
            $temp_calendar = Tempcalendar::where('tahun', $tahun)->first();
            if(!empty($temp_calendar)){
                $kode = encrypt($temp_calendar->id);
                //view calendar
                $query = Calendar::where('id_temp_calendar', decrypt($kode))->where('type', $type)->orderBy('tanggal_awal','asc')->get();
            }else{
                $kode = '';
                //view calendar
                $query = Calendar::where('id_temp_calendar', $kode)->where('type', $type)->orderBy('tanggal_awal','asc')->get();
            }
        }
        if($query->isNotEmpty()){
            foreach($query as $qry){
                if(!empty($qry->tanggal_akhir)){
                    $data['id'] = $qry->id;
                    $data['title'] = $qry->event;
                    $data['start'] = $qry->tanggal_awal;
                    $data['end'] = date('Y-m-d',strtotime($qry->tanggal_akhir . "+1 days"));
                    $data['className'] = $qry->leave->badge;
                    $start = date('d M Y', strtotime($qry->tanggal_awal));
                    $end = date('d M Y',strtotime($qry->tanggal_akhir));
                    $data['dateup'] = $start.' to '.$end;
                }else{
                    $data['id'] = $qry->id;
                    $data['title'] = $qry->event;
                    $data['start'] = $qry->tanggal_awal;
                    $data['end'] = null;
                    $data['className'] = $qry->leave->badge;
                    $start = date('d M Y', strtotime($qry->tanggal_awal));
                    $data['dateup'] = $start;
                }
                $data_all[] = $data;
            }
        }else{
            $data_all = array();
        }
        $date_now = date('Y-m-d');
        return view('pages.employee.calendar.index', compact('user','kode','min','max','data_type','type','from_year','year_now','tahun','data_all','date_now'));
    }

    // public function emp_detail(Request $request, $id){
    //     $user = auth()->user();
    //     $kode = $id;
    //     if(!empty($request->select_type)){
    //         $type = $request->select_type;
    //     }else{
    //         if($user->employee->area_id == 1){
    //             $type = 2;
    //         }else{
    //             $type = 1;
    //         }
    //     }
    //     $data_type = [
    //         '1' => 'HEAD OFFICE / SALES, MARKETING',
    //         '2' => 'HEADQUARTERS / FACTORY'
    //     ];
    //     $temp_calendar = Tempcalendar::find(decrypt($id));
    //     $tahun = $temp_calendar->tahun;
    //     //view calendar
    //     $query = Calendar::where('id_temp_calendar', decrypt($id))->where('type', $type)->get();
    //     if($query->isNotEmpty()){
    //         foreach($query as $qry){
    //             if(!empty($qry->tanggal_akhir)){
    //                 $data['id'] = $qry->id;
    //                 $data['title'] = $qry->event;
    //                 $data['start'] = $qry->tanggal_awal;
    //                 $data['end'] = date('Y-m-d',strtotime($qry->tanggal_akhir . "+1 days"));
    //                 $data['className'] = $qry->leave->badge;
    //             }else{
    //                 $data['id'] = $qry->id;
    //                 $data['title'] = $qry->event;
    //                 $data['start'] = $qry->tanggal_awal;
    //                 $data['end'] = null;
    //                 $data['className'] = $qry->leave->badge;
    //             }
    //             $data_all[] = $data;
    //         }
    //     }else{
    //         $data_all = array();
    //     }
    //     return view('pages.employee.calendar.calendar', compact('user','kode','data_type','type','tahun','data_all'));
    // }

    public function emp_download($id){
        $query = Tempcalendar::find(decrypt($id));
        $unduh = public_path('storage/calendar/'.$query->file_calendar);
        return response()->download($unduh);
    }

    public function emp_view(Request $request){
        $query = Calendar::where('id', $request->id_calendar)->first();
        $query['leave'] = $query->leave->nama;
        if(!empty($query->tanggal_akhir)){
            $tgl = date('d F Y', strtotime($query->tanggal_awal));
            $tgl2 = date('d F Y', strtotime($query->tanggal_akhir));
            $query['tgl'] = $tgl." sampai ".$tgl2;
            $query['range'] = $query->tanggal_awal." to ".$query->tanggal_akhir;
        }else{
            $tgl = date('d F Y', strtotime($query->tanggal_awal));
            $query['tgl'] = $tgl;
            $query['range'] = $query->tanggal_awal;
        }
        return response()->json($query);
    }
}
