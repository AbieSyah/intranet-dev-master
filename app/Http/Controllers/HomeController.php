<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Notifications\AccountNotification;
use App\Models\News;
use App\Models\Pkb;
use App\Models\Medical;
use App\Models\Employee;
use App\Models\Internalrule;
use App\Models\Calendar;
use App\Models\Tempcalendar;
use App\Models\Permissioninternalrule;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Evaluation;
use App\Models\Trainingrecord;
use App\Models\Trainingfkt;
use App\Models\Trainingfpkt;
use App\Models\Trainingperiode;
use App\Models\Trainingevaluasi;
use App\Models\Logcatatantraining;
use App\Models\Qrcodefkt;
use App\Models\Qrcodefpkt;
use App\Models\Log;
use App\Models\Lab;
use App\Models\Vendor;
use App\Models\User;
use App\Models\Master\Doctoraccount;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Carbon\CarbonInterval;
use Auth;
use PDF;
use Response;
use Exception;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Security\Guest;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $date_now  = 'Y-m-d';
        $date_last = date('Y-m-d', strtotime('-1 year'));        

        if ($user->can('emp.menu')) {
            if(empty($user->last_update_password)){
                return view('indexpassword');
            }else{
                if($user->last_update_password < $date_now && $user->last_update_password > $date_last){
                    $year = date('Y');
                    $date_now = date('Y-m-d');
                    $news_event = News::where('status','release')->orderBy('tanggal_news','desc')->paginate(5);
                    $query = Calendar::where('id_leave', 4)->whereYear('created_at', $year)->orderBy('tanggal_awal','asc')->get()->unique('event');
                    if($query->isNotEmpty()){
                        foreach($query as $qry){
                            if(!empty($qry->tanggal_akhir)){
                                $data['id'] = $qry->id;
                                $data['title'] = $qry->event;
                                $data['start'] = $qry->tanggal_awal;
                                $data['end'] = date('d M Y',strtotime($qry->tanggal_akhir . "+1 days"));
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
                    // Akun super user / admin tetap tampilkan sidebar kiri di halaman Home
                    $isSuperUser = $user->hasRole(['super_admin', 'Super User']) || Gate::allows('hrd.menu');
                    if ($isSuperUser) {
                        return view('home', compact('user','news_event','data_all','date_now'));
                    }
                    return view('emphome', compact('user','news_event','data_all','date_now'));
                }else{
                    return view('indexpassword');
                }
            }
        }else{
            if(empty($user->last_update_password)){
                return view('indexpassword');
            }else{
                if($user->last_update_password < $date_now && $user->last_update_password > $date_last){
                    $year = date('Y');
                    $date_now = date('Y-m-d');
                    $news_event = News::where('status', 'release')->orderBy('tanggal_news','desc')->paginate(5);
                    $query = Calendar::where('id_leave', 4)->whereYear('created_at', $year)->orderBy('tanggal_awal','asc')->get()->unique('event');
                    if($query->isNotEmpty()){
                        foreach($query as $qry){
                            if(!empty($qry->tanggal_akhir)){
                                $data['id'] = $qry->id;
                                $data['title'] = $qry->event;
                                $data['start'] = $qry->tanggal_awal;
                                $data['end'] = date('d M Y',strtotime($qry->tanggal_akhir . "+1 days"));
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
                    if(!empty(auth()->user()->employee->id)){
                        $cek_employee = Employee::where('id', auth()->user()->employee->id)->first();
                        if(!empty($cek_employee)){             
                            return view('home', compact('user','news_event','data_all','date_now'));
                        }else{
                            if(auth()->user()->name == 'Security HPI'){
                                return redirect()->route('booking-room.index');
                            }else{
                                return redirect()->route('clinic.patient.index');
                            }
                        }
                    }else{
                        if(auth()->user()->name == 'Security HPI'){
                            return redirect()->route('booking-room.index');
                        }else{
                            return redirect()->route('clinic.patient.index');
                        }
                    }
                }else{
                    return view('indexpassword');
                }
            }
        }
    }      

    public function home_lampiran($id){
        $query = News::find(decrypt($id));
        $lampiran = public_path('storage/lampiran_konten/'.$query->lampiran);
        
        // return response()->file($lampiran);
        $file = File::get($lampiran);
        $response = Response::make($file, 200);
        $response->header('Content-Type', 'application/pdf');
        $response->header('Content-Disposition', 'filename=' . '"'.$query->judul.'.pdf"');
        $response->header('Content-Transfer-Encoding', 'binary');
        return $response;
    }

    public function search(Request $request){
        $user = auth()->user();
        $year = date('Y');
        $date_now = date('Y-m-d');
		$cari = $request->cari;
        $news_event = News::where('judul','like',"%".$cari."%")->where('status', 'release')->orderBy('tanggal_news','desc')->paginate();
        $query = Calendar::where('id_leave', 4)->whereYear('created_at', $year)->orderBy('tanggal_awal','asc')->get()->unique('event');
        if($query->isNotEmpty()){
            foreach($query as $qry){
                if(!empty($qry->tanggal_akhir)){
                    $data['id'] = $qry->id;
                    $data['title'] = $qry->event;
                    $data['start'] = $qry->tanggal_awal;
                    $data['end'] = date('d M Y',strtotime($qry->tanggal_akhir . "+1 days"));
                    $data['className'] = $qry->leave->badge;
                    $start = date('d M Y', strtotime($qry->tanggal_awal));
                    $end = date('d M Y',strtotime($qry->tanggal_akhir . "+1 days"));
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

        return view('home',['news_event' => $news_event, 'user' => $user, 'data_all' => $data_all, 'date_now' => $date_now]);
    }

    public function profile_search(Request $request){
        $user = auth()->user();
        $year = date('Y');
        $date_now = date('Y-m-d');
		$cari = $request->cari;
        $news_event = News::where('judul','like',"%".$cari."%")->where('status', 'release')->orderBy('tanggal_news','desc')->paginate();
        $query = Calendar::where('id_leave', 4)->whereYear('created_at', $year)->orderBy('tanggal_awal','asc')->get()->unique('event');
        if($query->isNotEmpty()){
            foreach($query as $qry){
                if(!empty($qry->tanggal_akhir)){
                    $data['id'] = $qry->id;
                    $data['title'] = $qry->event;
                    $data['start'] = $qry->tanggal_awal;
                    $data['end'] = date('d M Y',strtotime($qry->tanggal_akhir . "+1 days"));
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
  

        return view('pages.profile.profile-home',['news_event' => $news_event, 'user' => $user, 'data_all' => $data_all, 'date_now' => $date_now]);
    }

    public function emp_search(Request $request){
        $user = auth()->user();
        $year = date('Y');
        $date_now = date('Y-m-d');
		$cari = $request->cari;
        $news_event = News::where('judul','like',"%".$cari."%")->where('status', 'release')->orderBy('tanggal_news','desc')->paginate();
        $query = Calendar::where('id_leave', 4)->whereYear('created_at', $year)->orderBy('tanggal_awal','asc')->get()->unique('event');
        if($query->isNotEmpty()){
            foreach($query as $qry){
                if(!empty($qry->tanggal_akhir)){
                    $data['id'] = $qry->id;
                    $data['title'] = $qry->event;
                    $data['start'] = $qry->tanggal_awal;
                    $data['end'] = date('d M Y',strtotime($qry->tanggal_akhir . "+1 days"));
                    $data['className'] = $qry->leave->badge;
                    $start = date('d M Y', strtotime($qry->tanggal_awal));
                    $end = date('d M Y',strtotime($qry->tanggal_akhir . "+1 days"));
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
        return view('emphome',['news_event' => $news_event, 'user' => $user, 'data_all' => $data_all, 'date_now' => $date_now]);
    }

    public function emp_detail(Request $request, $id){
        $user = auth()->user();
        $news = News::find(decrypt($id));
        if(!empty($news->gambar)){
            $arr_konten = explode(',', $news->gambar);
        }else{
            $arr_konten = null;
        }
        $lampiran = route('home.lampiran', encrypt($news->id));
        return view('emp_detail', compact('user','news','arr_konten','lampiran'));
    }

    public function profile_home_detail(Request $request, $id){
        $user = auth()->user();
        $news = News::find(decrypt($id));
        if(!empty($news->gambar)){
            $arr_konten = explode(',', $news->gambar);
        }else{
            $arr_konten = null;
        }
        $lampiran = route('home.lampiran', encrypt($news->id));

        return view('pages.profile.profile-home-detail', compact('user','news','arr_konten','lampiran',));
    }

    public function detail(Request $request, $id){
        $news = News::find(decrypt($id));
        if(!empty($news->gambar)){
            $arr_konten = explode(',', $news->gambar);
        }else{
            $arr_konten = null;
        }
        $lampiran = route('home.lampiran', encrypt($news->id));
        return view('detail', compact('news','arr_konten','lampiran'));
    }
    

    public function index_profile(Request $request){
        $user = auth()->user();
        $countEval = Evaluation::where('employee_id', $user->employee->id)->where('status', 'DONE')->count();
        return view('index', compact('user','countEval'));
    }

    public function privacy_policy(Request $request){
        $user = auth()->user();
        $post = User::where('id', $user->id)->update(['disclaimer' => $request->id_dis]);
        return redirect()->route('home');
    }

    public function select_doctor(Request $request){
        $user = auth()->user();
        $query = Doctoraccount::where('id_dokter', $request->id_doctor)->first();
        $post = $user->update([
            'name' => $query->nama,
            'employee_id' => $query->id_dokter
        ]);
        return redirect(route('clinic.patient.index'))->with('disclaimer','open tab');
    }

    public function profile_upload(Request $request){
        // dd($request->image_base64);
        $user = auth()->user();
        if(!empty($request->image_base64)){
            $cek_file = storage_path('app/public/avatars/'.$user->employee->avatar);
            if (File::exists($cek_file)) {
                File::delete($cek_file);
            }
            $data_avatar = $this->storeBase64($request->image_base64);

            $post = Employee::where('id', $user->employee_id)->update(['avatar' => $data_avatar]);
        }
        //insert log user activity
        $insert = new Log;
        $insert->user_id = $user->id;
        $insert->ip_address = $request->ip();
        $insert->action = 'update';
        $insert->description = 'Modify Avatar Employee '.'"'.$user->employee->fullname.'"';
        $insert->save();

        if (Auth::user()->can('emp.menu')) {
            return redirect()->route('home');
        }else{
            return redirect()->route('profile');
        }
    }

    private function storeBase64($imageBase64)
    {
        list($type, $imageBase64) = explode(';', $imageBase64);
        list(, $imageBase64)      = explode(',', $imageBase64);
        $imageBase64 = base64_decode($imageBase64);
        $imageName= time().'.png';
        $path = storage_path() . "/app/public/avatars/" . $imageName;
  
        file_put_contents($path, $imageBase64);
          
        return $imageName;
    }

    public function disclaimer(){
        $user = auth()->user();
        return view('privacypolicy', compact('user'));
    }

    public function comingsoon(){
        return view('errors.comingsoon');
    }

    public function profile_home(){
        $user = auth()->user();
        $year = date('Y');
        $date_now = date('Y-m-d');
        $news_event = News::where('status', 'release')->orderBy('tanggal_news','desc')->paginate(5);
        $query = Calendar::where('id_leave', 4)->whereYear('created_at', $year)->orderBy('tanggal_awal','asc')->get()->unique('event');
        if($query->isNotEmpty()){
            foreach($query as $qry){
                if(!empty($qry->tanggal_akhir)){
                    $data['id'] = $qry->id;
                    $data['title'] = $qry->event;
                    $data['start'] = $qry->tanggal_awal;
                    $data['end'] = date('d M Y',strtotime($qry->tanggal_akhir . "+1 days"));
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
        return view('pages.profile.profile-home', compact('user','news_event','data_all','date_now'));
    }

    public function profile(){
        $user = auth()->user();
        // $pkb = Pkb::where('status','active')->first();
        // $url_pkb = URL::asset('storage/pkb/'.$pkb->file_pkb); 
        $countEval = Evaluation::where('employee_id', $user->employee->id)->where('status', 'DONE')->count();      
        return view('pages.profile.index', compact('user','countEval'));
    }

    public function profile_internal_rule(Request $request){
        $user = auth()->user();
        // $permission_rule = Permissioninternalrule::whereIn('id_dept', [$user->employee->department_id,'all'])
        //     ->whereIn('id_level', [$user->employee->level_id,'all'])->get();
        // $arr_rule = $permission_rule->pluck('id_internal_rule');
        // $query = Internalrule::whereIn('id', $arr_rule)->get();
        $query = Internalrule::get();
        if($query->isNotEmpty()){
            foreach ($query as $qry) {
                $index = $qry->id;
                $data[$index] = array();
                $data[$index]['id'] = $qry->id;
                $data[$index]['nama'] = $qry->nama;
                $data[$index]['tgl_berlaku'] = date('d F Y', strtotime($qry->tgl_berlaku));
                $data[$index]['isi'] = $qry->isi;
                $data[$index]['url'] = route('lampiran.rule', encrypt($qry->id));
                // if(!empty($qry->file)){
                //     // $url = URL::asset('storage/rules/'.$qry->file);
                //     // $data[$index]['url'] = 'https://docs.google.com/viewer?url='.$url.'&embedded=true';
                // }else{
                //     $data[$index]['url'] = 0;
                // }
            }
        }else{
            $data = array();
        }
        if($request->ajax()){
            return DataTables::of($data)
            ->addColumn('action', function ($data) {
                $button = '<button data-id="'. $data['url'] .'" data-bs-toggle="modal" data-bs-target="#modal-preview" title="Preview" class="btn btn-danger btn-sm preview-btn"><i class="ri-file-pdf-line"></i> Show</button>';               
                $button .= '&nbsp;';
                $button .= '<a href="'.route('download.rule', encrypt($data['id'])).'" class="btn btn-success btn-sm"><i class="ri-file-pdf-line me-1 align-bottom"></i> Download</a>';
                return $button;
            })
            ->rawColumns(['action'])
            ->addIndexColumn()
            ->make(true);
        }

        return view('pages.profile.internal-rule', compact('user'));
    }

    public function profile_benefit(Request $request){
        $user = auth()->user();
        $emp_id = $user->employee->id;
        $emp_area = $user->employee->area_id;
        $emp_level = $user->employee->level_id;
        $benefits = Permissioninternalrule::with('internalrule','area','employee','level')->whereIn('id_employee', [$emp_id,'all'])->whereIn('id_area', [$emp_area,'all'])->whereIn('id_level', [$emp_level,'all'])->get();
        return view('pages.profile.benefit', compact('user','benefits'));
    }

    public function profile_pkb(Request $request){
        $user = auth()->user();
        $pkb = Pkb::where('status','active')->first();
        // $url_pkb = URL::asset('storage/pkb/'.$pkb->file_pkb);
        $url_pkb = 'NA';
        return view('pages.profile.pkb', compact('user','url_pkb'));
    }

    public function profile_pkb_download(Request $request){
        $query = Pkb::where('status','active')->first();
        $unduh = public_path('storage/pkb/'.$query->file_pkb);
        return response()->download($unduh);
    }

    // public function profile_calendar(Request $request){
    //     $user = auth()->user();
    //     if ($request->ajax()) {
    //         $query = Tempcalendar::all();
    //         return DataTables::of($query)
    //             ->addColumn('action', function ($data) {
    //                 $button = '<a href="'. route('profile.calendar.detail', encrypt($data['id'])).'" title="Detail Calendar" class="btn btn-primary btn-sm"><i class="ri-calendar-event-fill align-middle"></i> Show</a>';
    //                 return $button;
    //             })
    //             ->addIndexColumn()
    //             ->make(true);
    //     }
    //     return view('pages.profile.calendar', compact('user'));
    // }
    public function profile_calendar(Request $request){
        $user = auth()->user();
        $data_type = [
            '1' => 'HEAD OFFICE / SALES, MARKETING',
            '2' => 'HEADQUARTERS / FACTORY'
        ];
        //filter calendar
        if(!empty($request->select_type)){
            $type = $request->select_type;
        }else{
            if($user->employee->area_id == 1){
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
        return view('pages.profile.calendar', compact('user','kode','min','max','data_type','type','from_year','year_now','tahun','data_all','date_now'));
    }

    // public function profile_calendar_detail(Request $request, $id){
    //     // dd('test');
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
    //     return view('pages.profile.detail-calendar', compact('user','kode','data_type','type','tahun','data_all'));
    // }

    public function profile_calendar_view(Request $request){
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

    public function profile_calendar_download($id){
        $query = Tempcalendar::find(decrypt($id));
        $unduh = public_path('storage/calendar/'.$query->file_calendar);
        return response()->download($unduh);
    }

    public function profile_medical(Request $request){
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
        return view('pages.profile.medical', compact('user','medical','lab', 'latest_mcu', 'arr_tanggal'));
    }
    
    public function profile_lampiran_mcu(Request $request, $id){
        $user = auth()->user();
        $medical = Medical::find($id);
        // $lampiran_mcu = public_path('mcu/'.$medical->lampiran_mcu);
        $lampiran_mcu = public_path('storage/mcu/'.$medical->lampiran_mcu);
        return response()->file($lampiran_mcu);
    }

    public function profile_download_mcu($id){
        $query = Medical::find(decrypt($id));
        $unduh = public_path('storage/mcu/'.$query->lampiran_mcu);
        return response()->download($unduh);
    }

    public function profile_lampiran_pdf(Request $request){
        $user = auth()->user();
        // $medical = Medical::where('id_employees', $user->employee->id)->whereYear('tanggal_mcu', $request->date_mcu)->first();
        $medical = Medical::where('id', $request->date_mcu)->first();
        $medical['fullname'] = $medical->employee->fullname;
        $medical['gender'] = $medical->employee->gender;
        if(!empty($medical->tanggal_mcu)){
            $medical['tgl_mcu'] = Carbon::parse($medical->tanggal_mcu)->format('d F Y');
        }else{
            $medical['tgl_mcu'] = '-';
        }
        if(!empty($medical)){
            $medical['pdf_mcu'] = route('profile.lampiran.mcu',$medical->id);
            // $url = URL::asset('storage/rules/'.$medical->lampiran_mcu);
            // $medical['pdf_mcu'] = 'https://docs.google.com/viewer?url='.$url.'&embedded=true';
            $medical['unduh_mcu'] = route('profile.download.mcu', encrypt($medical->id));
        }else{
            $medical['pdf_mcu'] = 0;
            $medical['unduh_mcu'] = 0;
        }
        $medical['master_lab'] = Lab::where('id_vendor', $medical->id_vendor)->get()->pluck('nilai_rujukan', 'pemeriksaan');
        return response()->json($medical);
    }

    public function profile_booking(Request $request){
        $user = auth()->user();
        $rooms = Room::get();
        if($rooms->isNotEmpty()){
            foreach($rooms as $room){
                $document['id'] = $room->id;
                $document['title'] = $room->nama;
                $data[] = $document;
            }
            $data_room = $data;
        }else{
            $data_room = array();
        }

        $bookings = Booking::get();
        if($bookings->isNotEmpty()){
            foreach($bookings as $booking){
                $document['id'] = $booking->id;
                $document['resourceId'] = $booking->room->id;
                $document['title'] = $booking->brief_description;
                $document['start'] = $booking->date_start;
                $document['end'] = $booking->date_end;
                if($booking->tipe == 'internal'){
                    $document['className'] = 'bg-success border-success';
                }else{
                    $document['className'] = 'bg-info border-info';
                }
                $data_array[] = $document;
            }
            $data_booking = $data_array;
        }else{
            $data_booking = array();
        }

        return view('pages.profile.booking', compact('user', 'data_room', 'data_booking'));
    }

    public function profile_booking_store(Request $request){
        DB::beginTransaction();
        try{
            $user = auth()->user();
            if($request->cek_repeat == 'none'){
                $date_start = $request->start_date.' '.$request->start_time;
                $date_end = $request->end_date.' '.$request->end_time;
                $query  = Booking::where('room_id', $request->room)
                    ->where('date_start', '>=', $date_start)
                    ->where('date_start', '<', $date_end)->get();                    
                if($query->isEmpty()){
                    //insert booking
                    $insert = new Booking;
                    $insert->brief_description = $request->brief_description;
                    $insert->full_description = $request->full_description;
                    $insert->date_start = $request->start_date.' '.$request->start_time;
                    $insert->date_end = $request->end_date.' '.$request->end_time;
                    $insert->room_id = $request->room;
                    $insert->employee_id = $user->employee_id;
                    $insert->tipe = $request->cek_type;
                    $insert->repeat_status = 'None';
                    $insert->save();

                    //room
                    $room = Room::find($request->room);
                    //insert log user activity
                    $insert = new Log;
                    $insert->user_id = $user->id;
                    $insert->ip_address = $request->ip();
                    $insert->action = 'insert';
                    $insert->description = 'Create new brief '.'"'.$request->brief_description.'" room "'.$room->nama.'"';
                    $insert->save();

                    DB::commit();
            
                    return response()->json(['message' => "Create $request->brief_description has been created"], 200);
                }else{
                    $arr_query = $query->pluck('brief_description')->toArray();
                    $merge_arr = implode(",",$arr_query);
                    return response()->json(['message' => "The new booking will conflict with the following entries: $merge_arr please check again"], 500);
                }
            }
            elseif ($request->cek_repeat == 'daily') {                
                $period = CarbonPeriod::create($request->start_date, $request->repeat_date);
                foreach ($period as $tanggal) {
                    $tgl =  $tanggal->format('Y-m-d');

                    $date_start = $tgl.' '.$request->start_time;
                    $date_end = $tgl.' '.$request->end_time;
                    $query  = Booking::where('room_id', $request->room)
                        ->where('date_start', '>=', $date_start)
                        ->where('date_start', '<', $date_end)->get();
                    $total[] = $query->count();                   
                }

                if(array_sum($total) > 0){
                    foreach ($period as $tanggal2) {
                        $tgl2 =  $tanggal2->format('Y-m-d');
            
                        $date_start2 = $tgl2.' '.$request->start_time;;
                        $date_end2 = $tgl2.' '.$request->end_time;
                        $query2 = Booking::where('room_id', $request->room)
                        ->where('date_start', '>=', $date_start2)
                        ->where('date_start', '<', $date_end2)->get();
                        if($query2->isNotEmpty()){
                            $merge = $query2->pluck('brief_description')->toArray();
                            $tampung[] = implode(",",$merge);
                        }
                    }
                    $merge_arr = implode(",",$tampung);
                    return response()->json(['message' => "The new booking will conflict with the following entries: $merge_arr please check again"], 500);
                }else{                    
                    foreach ($period as $date) {
                        $tgl_period =  $date->format('Y-m-d');
                        //insert booking
                        $insert = new Booking;
                        $insert->brief_description = $request->brief_description;
                        $insert->full_description = $request->full_description;
                        $insert->date_start = $tgl_period.' '.$request->start_time;
                        $insert->date_end = $tgl_period.' '.$request->end_time;
                        $insert->room_id = $request->room;
                        $insert->employee_id = $user->employee_id;
                        $insert->tipe = $request->cek_type;
                        $insert->kode = time();
                        $insert->repeat_status = 'Daily';
                        $insert->save();
                    }
                    //room
                    $room = Room::find($request->room);
                    //insert log user activity
                    $insert = new Log;
                    $insert->user_id = $user->id;
                    $insert->ip_address = $request->ip();
                    $insert->action = 'insert';
                    $insert->description = 'Create new brief '.'"'.$request->brief_description.'" room "'.$room->nama.'"';
                    $insert->save();
    
                    DB::commit();
    
                    return response()->json(['message' => "Create $request->brief_description has been created"], 200);
                }
            }
            elseif($request->cek_repeat == 'weekly'){
                $start_date = $request->start_date;
                $end_repeat_date = $request->repeat_date;
                $days = $request->cek_repeat_day;
                $weeks = $request->cek_repeat_week;
                foreach (CarbonPeriod::create($start_date, CarbonInterval::weeks($weeks), $end_repeat_date, CarbonPeriod::IMMUTABLE) as $baseDate) {
                    foreach ($days as $dayName) {
                        $date = $baseDate->is($dayName) ? $baseDate : $baseDate->next($dayName);
                        $period[] = Carbon::create($date)->format('Y-m-d');
                    }
                }

                //cek konflik
                foreach($period as $key => $tanggal){
                    $tgl =  $tanggal;

                    $date_start = $tgl.' '.$request->start_time;
                    $date_end = $tgl.' '.$request->end_time;
                    $query  = Booking::where('room_id', $request->room)
                        ->where('date_start', '>=', $date_start)
                        ->where('date_start', '<', $date_end)->get();
                    $total[] = $query->count();
                }
                
                if(array_sum($total) > 0){
                    foreach ($period as $key => $tanggal2) {
                        $tgl2 =  $tanggal2;
            
                        $date_start2 = $tgl2.' '.$request->start_time;
                        $date_end2 = $tgl2.' '.$request->end_time;
                        $query2 = Booking::where('room_id', $request->room)
                        ->where('date_start', '>=', $date_start2)
                        ->where('date_start', '<', $date_end2)->get();
                        if($query2->isNotEmpty()){
                            $merge = $query2->pluck('brief_description')->toArray();
                            $tampung[] = implode(",",$merge);
                        }
                    }
                    $merge_arr = implode(",",$tampung);
                    return response()->json(['message' => "The new booking will conflict with the following entries: $merge_arr please check again"], 500);
                }else{
                    foreach($period as $key => $value){
                        $tgl_period = $value;
        
                        //insert booking
                        $insert = new Booking;
                        $insert->brief_description = $request->brief_description;
                        $insert->full_description = $request->full_description;
                        $insert->date_start = $tgl_period.' '.$request->start_time;
                        $insert->date_end = $tgl_period.' '.$request->end_time;
                        $insert->room_id = $request->room;
                        $insert->employee_id = $user->employee_id;
                        $insert->tipe = $request->cek_type;
                        $insert->repeat_day = json_encode($days);
                        $insert->repeat_week = $weeks;
                        $insert->kode = time();
                        $insert->repeat_status = 'Weekly';
                        $insert->save();
                    }

                    //room
                    $room = Room::find($request->room);
                    //insert log user activity
                    $insert = new Log;
                    $insert->user_id = $user->id;
                    $insert->ip_address = $request->ip();
                    $insert->action = 'insert';
                    $insert->description = 'Create new brief '.'"'.$request->brief_description.'" room "'.$room->nama.'"';
                    $insert->save();

                    DB::commit();
            
                    return response()->json(['message' => "Create $request->brief_description has been created"], 200);    
                }
            }else{
                $start_date = $request->start_date;
                $end_date = $request->repeat_date;
                $day = $request->on_day;

                $period = CarbonPeriod::create($start_date, '1 month' , $end_date);
                foreach ($period as $date) {
                    if($date->day($day)->format('d') == $day){
                        $dates = $date->day($day)->format('Y-m-d');
                    }else{
                        $dates = $date->subMonth()->endOfMonth()->format('Y-m-d');
                    }
                    $period_date[] = $dates;
                }

                //cek konflik
                foreach($period_date as $key => $tanggal){
                    $tgl =  $tanggal;

                    $date_start = $tgl.' '.$request->start_time;
                    $date_end = $tgl.' '.$request->end_time;
                    $query  = Booking::where('room_id', $request->room)
                        ->where('date_start', '>=', $date_start)
                        ->where('date_start', '<', $date_end)->get();
                    $total[] = $query->count();
                }

                if(array_sum($total) > 0){
                    foreach ($period_date as $key => $tanggal2) {
                        $tgl2 =  $tanggal2;
            
                        $date_start2 = $tgl2.' '.$request->start_time;
                        $date_end2 = $tgl2.' '.$request->end_time;
                        $query2 = Booking::where('room_id', $request->room)
                        ->where('date_start', '>=', $date_start2)
                        ->where('date_start', '<', $date_end2)->get();
                        if($query2->isNotEmpty()){
                            $merge = $query2->pluck('brief_description')->toArray();
                            $tampung[] = implode(",",$merge);
                        }
                    }
                    $merge_arr = implode(",",$tampung);
                    return response()->json(['message' => "The new booking will conflict with the following entries: $merge_arr please check again"], 500);
                }else{
                    foreach($period_date as $key => $value){
                        $tgl_period = $value;
    
                        //insert booking
                        $insert = new Booking;
                        $insert->brief_description = $request->brief_description;
                        $insert->full_description = $request->full_description;
                        $insert->date_start = $tgl_period.' '.$request->start_time;
                        $insert->date_end = $tgl_period.' '.$request->end_time;
                        $insert->room_id = $request->room;
                        $insert->employee_id = $user->employee_id;
                        $insert->tipe = $request->cek_type;
                        $insert->kode = time();
                        $insert->repeat_month = $day;
                        $insert->repeat_status = 'monthly';
                        $insert->save();
                    }
    
                    //room
                    $room = Room::find($request->room);
                    //insert log user activity
                    $insert = new Log;
                    $insert->user_id = $user->id;
                    $insert->ip_address = $request->ip();
                    $insert->action = 'insert';
                    $insert->description = 'Create new brief '.'"'.$request->brief_description.'" room "'.$room->nama.'"';
                    $insert->save();
    
                    DB::commit();
            
                    return response()->json(['message' => "Create $request->brief_description has been created"], 200);
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

    public function profile_booking_view(Request $request){
        $query = Booking::with('room')->where('id', $request->id_event)->first();
        $data['time_start'] = date('H:i', strtotime($query->date_start));
        $data['date_start'] = date('l d M Y', strtotime($query->date_start));
        $data['time_end'] = date('H:i', strtotime($query->date_end));
        $data['date_end'] = date('l d M Y', strtotime($query->date_end));
        $data['duration'] = abs(strtotime($query->date_start) - strtotime($query->date_end))/3600;
        $data['room'] = $query->room->nama;
        $data['time_last_update'] = date('H:i', strtotime($query->updated_at));
        $data['date_last_update'] = date('l d M Y', strtotime($query->updated_at));
        $data['tipe'] = $query->tipe;
        $data['description'] = $query->full_description;
        $data['kode'] = $query->kode;
        $data['create_by'] = $query->employee->fullname;
        $data['employee_id'] = $query->employee_id;
        $data['repeat_status'] = $query->repeat_status;
        //edit
        $data['id_edit_booking'] = $query->id;
        $data['edit_brief_description'] = $query->brief_description;
        $data['edit_full_description'] = $query->full_description;
        $data['edit_start_date'] = date('Y-m-d', strtotime($query->date_start));
        $data['edit_end_date'] = date('Y-m-d', strtotime($query->date_end));
        $data['edit_start_time'] = date('H:i', strtotime($query->date_start));
        $data['edit_end_time'] = date('H:i', strtotime($query->date_end));
        $data['edit_room'] = $query->room_id;
        $data['edit_tipe'] = $query->tipe;
        //delete
        $data['id_delete_booking'] = $query->id;
        //series repeat date set
        $data['end_date'] = date('Y-m-d', strtotime("+3 months", strtotime($query->date_start)));
        if(!empty($query->kode)){
            $query2 = Booking::where('kode', $query->kode)->orderBy('date_end','desc')->first();
            $query3 = Booking::where('kode', $query->kode)->orderBy('date_end','asc')->first();
            $data['start_date'] = $query3->date_start;
            $data['series_start_date'] = date('Y-m-d', strtotime($query3->date_start));
            $data['series_end_date'] = date('Y-m-d', strtotime($query3->date_end));
            $data['series_start_time'] = date('H:i', strtotime($query3->date_start));
            $data['series_end_time'] = date('H:i', strtotime($query3->date_end));
            $data['last_date_series'] = date('Y-m-d', strtotime($query2->date_end));
            $data['repeat_day'] = $query3->repeat_day;
            $data['repeat_week'] = $query3->repeat_week;
            $data['repeat_month'] = $query3->repeat_month;
        }
        
        return response()->json($data);
    }

    public function profile_booking_update(Request $request){
        DB::beginTransaction();
        try{
            $user = auth()->user();
            $id = $request->id_edit_booking;
            $brief = $request->edit_brief_description;
            $brief_full = $request->edit_full_description;
            $start_date = $request->edit_start_date.' '.$request->edit_start_time;
            $end_date = $request->edit_end_date.' '.$request->edit_end_time;
            $room = $request->edit_room;
            $tipe = $request->cek_edit_type;
            $query = Booking::find($id);
            $query2 = Booking::where('room_id', $room)
                ->where('date_start', '>=', $start_date)
                ->where('date_start', '<', $end_date)->get();
            if($query->date_start == $start_date.':00'){
                $post = Booking::where('id', $id)->update([
                    'brief_description' => $brief,
                    'full_description' => $brief_full,
                    'date_start' => $start_date,
                    'date_end' => $end_date,
                    'room_id' => $room,
                    'tipe' => $tipe
                ]);
    
                //insert log user activity
                $insert = new Log;
                $insert->user_id = $user->id;
                $insert->ip_address = $request->ip();
                $insert->action = 'update';
                $insert->description = 'Modify brief '.'"'.$query->brief_description.'" start date "'.$query->date_start.'" end date "'.$query->date_end.'" room "'.$query->room->nama.'"';
                $insert->save();
    
                DB::commit();
        
                return response()->json(['message' => "Modify $query->brief_description has been updated"], 200);
            }else{
                if($query2->isEmpty()){
                    $post = Booking::where('id', $id)->update([
                        'brief_description' => $brief,
                        'full_description' => $brief_full,
                        'date_start' => $start_date,
                        'date_end' => $end_date,
                        'room_id' => $room,
                        'tipe' => $tipe
                    ]);
        
                    //insert log user activity
                    $insert = new Log;
                    $insert->user_id = $user->id;
                    $insert->ip_address = $request->ip();
                    $insert->action = 'update';
                    $insert->description = 'Modify brief '.'"'.$query->brief_description.'" start date "'.$query->date_start.'" end date "'.$query->date_end.'" room "'.$query->room->nama.'"';
                    $insert->save();
        
                    DB::commit();
            
                    return response()->json(['message' => "Modify $query->brief_description has been updated"], 200);
                }else{
                    $arr_query = $query2->pluck('brief_description')->toArray();
                    $merge_arr = implode(",",$arr_query);
                    return response()->json(['message' => "The new booking will conflict with the following entries: $merge_arr please check again"], 500);
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

    public function profile_booking_delete(Request $request){
        DB::beginTransaction();
        try{
            $user = auth()->user();
            $id = $request->id_delete_booking;
            $query = Booking::find($id);
            
            //insert log user activity
            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'delete';
            $insert->description = 'Delete brief '.'"'.$query->brief_description.'" start date "'.$query->date_start.'" end date "'.$query->date_end.'" room "'.$query->room->nama.'"';
            $insert->save();

            $post = Booking::where('id', $id)->delete();

            DB::commit();
    
            return response()->json(['message' => "Meeting $query->brief_description has been deleted"], 200);
        }catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    public function profile_booking_update_series(Request $request){
        DB::beginTransaction();
        try{
            $user = auth()->user();
            $id = $request->id_edit_series;
            $brief = $request->edit_series_brief_description;
            $brief_full = $request->edit_series_full_description;
            $start_date = $request->edit_series_start_date.' '.$request->edit_series_start_time;
            $end_date = $request->edit_series_end_date.' '.$request->edit_series_end_time;
            $room = $request->edit_series_room;
            $tipe = $request->cek_edit_series_type;
            $repeat_date = $request->repeat_series_date;
            $tipe_repeat = $request->cek_series_repeat;

            $delete_series = Booking::where('kode', $id)->delete();

            if($tipe_repeat == 'daily'){
                $period = CarbonPeriod::create($request->edit_series_start_date, $repeat_date);
                //cek konflik
                foreach ($period as $tanggal) {
                    $tgl =  $tanggal->format('Y-m-d');

                    $date_start = $tgl.' '.$request->edit_series_start_time;
                    $date_end = $tgl.' '.$request->edit_series_end_time;
                    $qry  = Booking::where('room_id', $room)
                        ->where('date_start', '>=', $date_start)
                        ->where('date_start', '<', $date_end)->get();
                    $total[] = $qry->count();                   
                }
                if(array_sum($total) > 0){
                    foreach ($period as $tanggal2) {
                        $tgl2 =  $tanggal2->format('Y-m-d');
            
                        $date_start2 = $tgl2.' '.$request->edit_series_start_time;
                        $date_end2 = $tgl2.' '.$request->edit_series_end_time;
                        $query2 = Booking::where('room_id', $room)
                        ->where('date_start', '>=', $date_start2)
                        ->where('date_start', '<', $date_end2)->get();
                        if($query2->isNotEmpty()){
                            $merge = $query2->pluck('brief_description')->toArray();
                            $tampung[] = implode(",",$merge);
                        }
                    }
                    $merge_arr = implode(",",$tampung);
                    return response()->json(['message' => "The new booking will conflict with the following entries: $merge_arr please check again"], 500);
                }else{
                    foreach($period as $date){
                        $tgl_period = $date->format('Y-m-d');
    
                        //insert booking
                        $insert = new Booking;
                        $insert->brief_description = $brief;
                        $insert->full_description = $brief_full;
                        $insert->date_start = $tgl_period.' '.$request->edit_series_start_time;
                        $insert->date_end = $tgl_period.' '.$request->edit_series_end_time;
                        $insert->room_id = $room;
                        $insert->employee_id = $user->employee_id;
                        $insert->tipe = $tipe;
                        $insert->kode = $id;
                        $insert->repeat_status = 'Daily';
                        $insert->save();
                    }
    
                    $query = Booking::where('kode', $id)->first();
    
                    //insert log user activity
                    $insert = new Log;
                    $insert->user_id = $user->id;
                    $insert->ip_address = $request->ip();
                    $insert->action = 'update';
                    $insert->description = 'Modify Series brief '.'"'.$query->brief_description.'" start date "'.$query->date_start.'" end date "'.$repeat_date.'" room "'.$query->room->nama.'"';
                    $insert->save();
    
                    DB::commit();
            
                    return response()->json(['message' => "Modify Series $query->brief_description has been updated"], 200);
                }     
            }
            elseif ($tipe_repeat == 'weekly') {
                $end_repeat_date = $repeat_date;
                $days = $request->cek_series_repeat_day;
                $weeks = $request->cek_series_repeat_week;
                foreach (CarbonPeriod::create($request->edit_series_start_date, CarbonInterval::weeks($weeks), $end_repeat_date, CarbonPeriod::IMMUTABLE) as $baseDate) {
                    foreach ($days as $dayName) {
                        $date = $baseDate->is($dayName) ? $baseDate : $baseDate->next($dayName);
                        $period[] = Carbon::create($date)->format('Y-m-d');
                    }
                }

                //cek konflik
                foreach($period as $key => $tanggal){
                    $tgl =  $tanggal;

                    $date_start = $tgl.' '.$request->edit_series_start_time;
                    $date_end = $tgl.' '.$request->edit_series_end_time;
                    $qry  = Booking::where('room_id', $room)
                        ->where('date_start', '>=', $date_start)
                        ->where('date_start', '<', $date_end)->get();
                    $total[] = $qry->count();
                }
                if(array_sum($total) > 0){
                    foreach ($period as $key => $tanggal2) {
                        $tgl2 =  $tanggal2;
            
                        $date_start2 = $tgl2.' '.$request->edit_series_start_time;
                        $date_end2 = $tgl2.' '.$request->edit_series_end_time;
                        $query2 = Booking::where('room_id', $room)
                        ->where('date_start', '>=', $date_start2)
                        ->where('date_start', '<', $date_end2)->get();
                        if($query2->isNotEmpty()){
                            $merge = $query2->pluck('brief_description')->toArray();
                            $tampung[] = implode(",",$merge);
                        }
                    }
                    $merge_arr = implode(",",$tampung);
                    return response()->json(['message' => "The new booking will conflict with the following entries: $merge_arr please check again"], 500);
                }else{
                    foreach($period as $key => $value){
                        $tgl_period = $value;
        
                        //insert booking
                        $insert = new Booking;
                        $insert->brief_description = $brief;
                        $insert->full_description = $brief_full;
                        $insert->date_start = $tgl_period.' '.$request->edit_series_start_time;
                        $insert->date_end = $tgl_period.' '.$request->edit_series_end_time;
                        $insert->room_id = $room;
                        $insert->employee_id = $user->employee_id;
                        $insert->tipe = $tipe;
                        $insert->repeat_day = json_encode($days);
                        $insert->repeat_week = $weeks;
                        $insert->kode = $id;
                        $insert->repeat_status = 'Weekly';
                        $insert->save();
                    }
    
                    $query = Booking::where('kode', $id)->first();
    
                    //insert log user activity
                    $insert = new Log;
                    $insert->user_id = $user->id;
                    $insert->ip_address = $request->ip();
                    $insert->action = 'update';
                    $insert->description = 'Modify Series brief '.'"'.$query->brief_description.'" start date "'.$query->date_start.'" end date "'.$repeat_date.'" room "'.$query->room->nama.'"';
                    $insert->save();
    
                    DB::commit();
            
                    return response()->json(['message' => "Modify Series $query->brief_description has been updated"], 200);
                }
            }else{
                $day = $request->series_on_day;

                $period = CarbonPeriod::create($request->edit_series_start_date, '1 month' , $repeat_date);
                foreach ($period as $date) {
                    if($date->day($day)->format('d') == $day){
                        $dates = $date->day($day)->format('Y-m-d');
                    }else{
                        $dates = $date->subMonth()->endOfMonth()->format('Y-m-d');
                    }
                    $period_date[] = $dates;
                }

                //cek konflik
                foreach($period_date as $key => $tanggal){
                    $tgl =  $tanggal;

                    $date_start = $tgl.' '.$request->edit_series_start_time;
                    $date_end = $tgl.' '.$request->edit_series_end_time;
                    $qry  = Booking::where('room_id', $room)
                        ->where('date_start', '>=', $date_start)
                        ->where('date_start', '<', $date_end)->get();
                    $total[] = $qry->count();
                }
                if(array_sum($total) > 0){
                    foreach ($period_date as $key => $tanggal2) {
                        $tgl2 =  $tanggal2;
            
                        $date_start2 = $tgl2.' '.$request->edit_series_start_time;
                        $date_end2 = $tgl2.' '.$request->edit_series_end_time;
                        $query2 = Booking::where('room_id', $room)
                        ->where('date_start', '>=', $date_start2)
                        ->where('date_start', '<', $date_end2)->get();
                        if($query2->isNotEmpty()){
                            $merge = $query2->pluck('brief_description')->toArray();
                            $tampung[] = implode(",",$merge);
                        }
                    }
                    $merge_arr = implode(",",$tampung);
                    return response()->json(['message' => "The new booking will conflict with the following entries: $merge_arr please check again"], 500);
                }else{
                    foreach($period_date as $key => $value){
                        $tgl_period = $value;
    
                        //insert booking
                        $insert = new Booking;
                        $insert->brief_description = $brief;
                        $insert->full_description = $brief_full;
                        $insert->date_start = $tgl_period.' '.$request->edit_series_start_time;
                        $insert->date_end = $tgl_period.' '.$request->edit_series_end_time;
                        $insert->room_id = $room;
                        $insert->employee_id = $user->employee_id;
                        $insert->tipe = $tipe;
                        $insert->kode = $id;
                        $insert->repeat_month = $day;
                        $insert->repeat_status = 'monthly';
                        $insert->save();
                    }
    
                    $query = Booking::where('kode', $id)->first();
    
                    //insert log user activity
                    $insert = new Log;
                    $insert->user_id = $user->id;
                    $insert->ip_address = $request->ip();
                    $insert->action = 'update';
                    $insert->description = 'Modify Series brief '.'"'.$query->brief_description.'" start date "'.$query->date_start.'" end date "'.$repeat_date.'" room "'.$query->room->nama.'"';
                    $insert->save();
    
                    DB::commit();
            
                    return response()->json(['message' => "Modify Series $query->brief_description has been updated"], 200);
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

    public function profile_booking_delete_series(Request $request){
        DB::beginTransaction();
        try{
            $user = auth()->user();
            $id = $request->id_delete_series;
            $query = Booking::where('kode', $id)->first();
            
            //insert log user activity
            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'delete';
            $insert->description = 'Delete series brief '.'"'.$query->brief_description.'" room "'.$query->room->nama.'"';
            $insert->save();

            $post = Booking::where('kode', $id)->delete();

            DB::commit();
    
            return response()->json(['message' => "Meeting $query->brief_description has been deleted"], 200);
        }catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    public function profile_training(Request $request){
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
        $count_jml_approve = $jml_approve_checker_ptt;

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
                $data[$index]['start_date'] = date('d M Y', strtotime($qry->start_date));
                $data[$index]['end_date'] = date('d M Y', strtotime($qry->end_date));
                $data[$index]['lokasi'] = $qry->lokasi;
                $data[$index]['biaya'] = "Rp ".number_format($qry->biaya,2);
                $data[$index]['status'] = $qry->status;
                $data[$index]['nama_status'] = $qry->training_status->name;
                $data[$index]['tgl_laporan'] = $qry->tgl_laporan;
                $data[$index]['sertifikat'] = $qry->sertifikat;
                $data[$index]['materi'] = $qry->materi;
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
                            // $list_laporan = '<li><a href="'.route('profile.training.create.laporan',encrypt($data['kode_fkt'])).'" class="dropdown-item"><i class="ri-contacts-book-2-line align-bottom me-1 text-muted"></i> Buat Laporan</a></li>';
                            // $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_laporan.'</ul></div>';
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
                            $list_laporan = '<li><a href="'.route('profile.training.create.laporan',encrypt($data['kode_fkt'])).'" class="dropdown-item"><i class="ri-contacts-book-2-line align-bottom me-1 text-muted"></i> Buat Laporan</a></li>';
                            $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_laporan.'</ul></div>';
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
        return view('pages.profile.training', compact('user','count_jml_approve','count_jml_approve_pti','jml_record','jml_approve'));
    }
    public function profile_training_status($id){
        $user = auth()->user();
        // $fkt = Trainingfkt::where()
        return view('pages.profile.training.status', compact('user'));
    }
    public function profile_training_detail($id){
        $user = auth()->user();
        $data = [
            'title' => 'FORMULIR KEBUTUHAN TRAINING'
        ];
        $pdf = PDF::loadView('pages.profile.fkt', $data);
     
        return $pdf->stream('FORMULIR KEBUTUHAN TRAINING - '.$user->employee->fullname.'.pdf');
    }

    public function profile_training_sertifikat($id){
        $query = Trainingrecord::find(decrypt($id));
        $lampiran_sertifikat = public_path('storage/sertifikat/'.$query->sertifikat);

        // $file = File::get($lampiran_sertifikat);
        // $response = Response::make($file, 200);
        // $response->header('Content-Type', 'application/pdf');
        // $response->header('Content-Disposition', 'filename=' . '"'.$query->employee->fullname.'.pdf"');
        // $response->header('Content-Transfer-Encoding', 'binary');

        return response()->download($lampiran_sertifikat);
    }

    public function profile_training_materi($id){
        $query = Trainingrecord::find(decrypt($id));
        $lampiran_materi = public_path('storage/materi/'.$query->materi);

        return response()->download($lampiran_materi);
    }
    //start laporan training
    public function profile_training_laporan(Request $request){
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
                        if($qry->ttd_direktur == $user->employee_id){
                            $index = $qry->id;
                            $data[$index] = array();
                            $data[$index]['id'] = $qry->id;
                            $data[$index]['pemohon'] = $qry->employee->fullname;
                            $data[$index]['judul'] = $qry->judul;
                            $data[$index]['tgl_laporan'] = $qry->tgl_laporan;
                        }else{
                            if(!empty($qry->tgl_ttd_direktur)){
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
                    $button = '<a href="'.route('profile.training.approval.laporan',encrypt($data['id'])).'" class="btn btn-secondary btn-sm waves-effect waves-light"><i class="ri-checkbox-circle-line align-bottom me-1"></i> Approve</a>';                  
                    return $button;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
    }
    public function profile_training_create_laporan($id){
        // dd(decrypt($id));
        $user = auth()->user();
        $training_record = Trainingrecord::where('kode_fkt', decrypt($id))
            ->where('id_employee', $user->employee_id)->first();
        $employees = Employee::whereNot('status', 'TERMINATED')->get();
        return view('pages.profile.training.laporan.create', compact('user','training_record','employees'));
    }
    public function profile_training_laporan_store(Request $request){
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
            $post = Trainingrecord::where('id', $request->id_record)->update([
                'sertifikat' => $sertifikat_name,
                'materi' => $materi_name,
                'tgl_laporan' => $request->tgl_laporan,
                'isi_pelatihan' => $request->isi_pelatihan,
                'dipelajari' => $request->dipelajari,
                'implementasi' => $request->implementasi,
                'ttd_presiden' => $request->ttd_presiden,
                'ttd_direktur' => $request->ttd_direktur,
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

            return redirect(route('profile.training'))->with('status','Laporan pelaksanaan training has been created');
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
            $post = Trainingrecord::where('id', $request->id_record)->update([
                'sertifikat' => null,
                'materi' => $materi_name,
                'tgl_laporan' => $request->tgl_laporan,
                'isi_pelatihan' => $request->isi_pelatihan,
                'dipelajari' => $request->dipelajari,
                'implementasi' => $request->implementasi,
                'ttd_presiden' => $request->ttd_presiden,
                'ttd_direktur' => $request->ttd_direktur,
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

            return redirect(route('profile.training'))->with('status','Laporan pelaksanaan training has been created');
        }
    }
    public function profile_training_evaluasi_laporan($id){
        // dd(decrypt($id));
        $kode = decrypt($id);
        $user = auth()->user();
        $evaluasi = Trainingevaluasi::where('id_training_record',$kode)->first();
        return view('pages.profile.training.laporan.evaluasi', compact('user','kode','evaluasi'));
    }
    public function profile_training_evaluasi_laporan_check(Request $request){
        $evaluasi = Trainingevaluasi::where('id_training_record', decrypt($request->id_record))->first();
        if(!empty($evaluasi)){
            $data = 'ya';
        }else{
            $data = 'tidak';
        }
        return response()->json($data);
    }
    public function profile_training_evaluasi_laporan_store(Request $request){
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
    public function profile_training_approval_laporan($id){
        $user = auth()->user();
        $query = Trainingrecord::where('id', decrypt($id))->first();
        return view('pages.profile.training.laporan.approval', compact('user','query'));
    }
    public function profile_training_laporan_approval_store(Request $request){
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
                //notification hrd ga genaral manager
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

        return redirect(route('profile.training'))->with('status','Laporan pelaksanaan training has been approved')->with('tab_approval','open tab');
    }
    public function profile_training_laporan_pdf($id){
        $kode = decrypt($id);
        $record = Trainingrecord::find($kode);
        //qrcode ttd presiden
        if(!empty($record->tgl_ttd_presiden)){
            $link_qr_code_ttd = route('public.laporan-training.qrcode-ttd', ['id' => $kode, 'ttd1' => $record->ttd_presiden]);
        }else{
            $link_qr_code_ttd = null;
        }
        //qrcode ttd direktur
        if(!empty($record->tgl_ttd_direktur)){
            $link_qr_code_ttd2 = route('public.laporan-training.qrcode-ttd2', ['id' => $kode, 'ttd2' => $record->ttd_direktur]);
        }else{
            $link_qr_code_ttd2 = null;
        }
        //qrcode ttd general manager
        if(!empty($record->tgl_ttd_general_manager)){
            $link_qr_code_ttd3 = route('public.laporan-training.qrcode-ttd3', ['id' => $kode, 'ttd3' => $record->ttd_general_manager]);
        }else{
            $link_qr_code_ttd3 = null;
        }
        //qrcode ttd manager
        if(!empty($record->tgl_ttd_manager)){
            $link_qr_code_ttd4 = route('public.laporan-training.qrcode-ttd4', ['id' => $kode, 'ttd4' => $record->ttd_manager]);
        }else{
            $link_qr_code_ttd4 = null;
        }
        //qrcode ttd atasan
        if(!empty($record->tgl_ttd_atasan)){
            $link_qr_code_ttd5 = route('public.laporan-training.qrcode-ttd5', ['id' => $kode, 'ttd5' => $record->ttd_atasan]);
        }else{
            $link_qr_code_ttd5 = null;
        }
        //qrcode ttd hrd & ga general manager
        if(!empty($record->tgl_ttd_hrd_ga_gm)){
            $link_qr_code_ttd6 = route('public.laporan-training.qrcode-ttd6', ['id' => $kode, 'ttd6' => $record->ttd_hrd_ga_gm]);
        }else{
            $link_qr_code_ttd6 = null;
        }
        //qrcode ttd pic
        if(!empty($record->tgl_ttd_pic)){
            $link_qr_code_ttd7 = route('public.laporan-training.qrcode-ttd7', ['id' => $kode, 'ttd7' => $record->ttd_pic]);
        }else{
            $link_qr_code_ttd7 = null;
        }
        //tanggal ttd presiden
        if(!empty($record->tgl_ttd_presiden)){
            $tgl_ttd1 = date('d/m/Y', strtotime($record->tgl_ttd_presiden));
        }else{
            $tgl_ttd1 = '-';
        }
        //tanggal ttd direktur
        if(!empty($record->tgl_ttd_direktur)){
            $tgl_ttd2 = date('d/m/Y', strtotime($record->tgl_ttd_direktur));
        }else{
            $tgl_ttd2 = '-';
        }
        //tanggal ttd general manager
        if(!empty($record->tgl_ttd_general_manager)){
            $tgl_ttd3 = date('d/m/Y', strtotime($record->tgl_ttd_general_manager));
        }else{
            $tgl_ttd3 = '-';
        }
        //tanggal ttd manager
        if(!empty($record->tgl_ttd_manager)){
            $tgl_ttd4 = date('d/m/Y', strtotime($record->tgl_ttd_manager));
        }else{
            $tgl_ttd4 = '-';
        }
        //tanggal ttd atasan
        if(!empty($record->tgl_ttd_atasan)){
            $tgl_ttd5 = date('d/m/Y', strtotime($record->tgl_ttd_atasan));
        }else{
            $tgl_ttd5 = '-';
        }
        //tanggal ttd hrd & ga general manager
        if(!empty($record->tgl_ttd_hrd_ga_gm)){
            $tgl_ttd6 = date('d/m/Y', strtotime($record->tgl_ttd_hrd_ga_gm));
        }else{
            $tgl_ttd6 = '-';
        }
        //tanggal ttd pic
        if(!empty($record->tgl_ttd_pic)){
            $tgl_ttd7 = date('d/m/Y', strtotime($record->tgl_ttd_pic));
        }else{
            $tgl_ttd7 = '-';
        }

        $data = [
            'title' => 'FORMULIR LAPORAN PELAKSANAAN TRAINING',
            'nama_ttd1' => $record->presiden_ttd->fullname ?? '-',
            'nama_ttd2' => $record->direktur_ttd->fullname ?? '-',
            'nama_ttd3' => $record->gm_ttd->fullname ?? '-',
            'nama_ttd4' => $record->manager_ttd->fullname ?? '-',
            'nama_ttd5' => $record->atasan_ttd->fullname ?? '-',
            'nama_ttd6' => $record->hrd_ga_gm_ttd->fullname ?? '-',
            'nama_ttd7' => $record->pic_ttd->fullname ?? '-',
            'tgl_ttd1' => $tgl_ttd1,
            'tgl_ttd2' => $tgl_ttd2,
            'tgl_ttd3' => $tgl_ttd3,
            'tgl_ttd4' => $tgl_ttd4,
            'tgl_ttd5' => $tgl_ttd5,
            'tgl_ttd6' => $tgl_ttd6,
            'tgl_ttd7' => $tgl_ttd7,
            'link_qr_code_ttd' => $link_qr_code_ttd,
            'link_qr_code_ttd2' => $link_qr_code_ttd2,
            'link_qr_code_ttd3' => $link_qr_code_ttd3,
            'link_qr_code_ttd4' => $link_qr_code_ttd4,
            'link_qr_code_ttd5' => $link_qr_code_ttd5,
            'link_qr_code_ttd6' => $link_qr_code_ttd6,
            'link_qr_code_ttd7' => $link_qr_code_ttd7,
            'record' => $record
        ];

        $pdf = PDF::loadView('pages.profile.training.laporan.print', $data);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('formulir laporan pelaksanaan training.pdf');
    }   
    public function profile_training_status_laporan(Request $request){
        $id = decrypt($request->id);
        $record = Trainingrecord::find($id);
        //canvas view
        if(!empty($record->atasan_ttd->avatar)){
            //status atasan
            $data['avatar_atasan'] = $record->atasan_ttd->avatar;
            $data['nama_atasan'] = $record->atasan_ttd->fullname ?? '';
            $data['area_atasan'] = $record->atasan_ttd->area->name ?? '';
            $data['departemen_atasan'] = $record->atasan_ttd->department->name ?? '';
            $data['position_atasan'] = $record->atasan_ttd->position->nama ?? '';
            if(!empty($record->tgl_ttd_atasan)){
                $data['tgl_ttd_atasan'] = date('d M, Y H:i', strtotime($record->tgl_ttd_atasan));
                $data['status_ttd_atasan'] = 'Approved';
            }else{
                $data['tgl_ttd_atasan'] = 'NA';
                $data['status_ttd_atasan'] = 'Waiting Approval';
            }
        }else{
            //status atasan
            $data['avatar_atasan'] = null;
            $data['nama_atasan'] = $record->atasan_ttd->fullname ?? '';
            $data['area_atasan'] = $record->atasan_ttd->area->name ?? '';
            $data['departemen_atasan'] = $record->atasan_ttd->department->name ?? '';
            $data['position_atasan'] = $record->atasan_ttd->position->nama ?? '';
            if(!empty($record->tgl_ttd_atasan)){
                $data['tgl_ttd_atasan'] = date('d M, Y H:i', strtotime($record->tgl_ttd_atasan));
                $data['status_ttd_atasan'] = 'Approved';
            }else{
                $data['tgl_ttd_atasan'] = 'NA';
                $data['status_ttd_atasan'] = 'Waiting Approval';
            }
        }

        if(!empty($record->manager_ttd->avatar)){
            //status manager
            $data['avatar_manager'] = $record->manager_ttd->avatar;
            $data['nama_manager'] = $record->manager_ttd->fullname ?? '';
            $data['area_manager'] = $record->manager_ttd->area->name ?? '';
            $data['departemen_manager'] = $record->manager_ttd->department->name ?? '';
            $data['position_manager'] = $record->manager_ttd->position->nama ?? '';
            if(!empty($record->tgl_ttd_manager)){
                $data['tgl_ttd_manager'] = date('d M, Y H:i', strtotime($record->tgl_ttd_manager));
                $data['status_ttd_manager'] = 'Approved';
            }else{
                $data['tgl_ttd_manager'] = 'NA';
                $data['status_ttd_manager'] = 'Waiting Approval';
            }
        }else{
            //status manager
            $data['avatar_manager'] = null;
            $data['nama_manager'] = $record->manager_ttd->fullname ?? '';
            $data['area_manager'] = $record->manager_ttd->area->name ?? '';
            $data['departemen_manager'] = $record->manager_ttd->department->name ?? '';
            $data['position_manager'] = $record->manager_ttd->position->nama ?? '';
            if(!empty($record->tgl_ttd_manager)){
                $data['tgl_ttd_manager'] = date('d M, Y H:i', strtotime($record->tgl_ttd_manager));
                $data['status_ttd_manager'] = 'Approved';
            }else{
                $data['tgl_ttd_manager'] = 'NA';
                $data['status_ttd_manager'] = 'Waiting Approval';
            }
        }

        if(!empty($record->gm_ttd->avatar)){
            //status general manager
            $data['avatar_gm'] = $record->gm_ttd->avatar;
            $data['nama_gm'] = $record->gm_ttd->fullname ?? '';
            $data['area_gm'] = $record->gm_ttd->area->name ?? '';
            $data['departemen_gm'] = $record->gm_ttd->department->name ?? '';
            $data['position_gm'] = $record->gm_ttd->position->nama ?? '';
            if(!empty($record->tgl_ttd_general_manager)){
                $data['tgl_ttd_general_manager'] = date('d M, Y H:i', strtotime($record->tgl_ttd_general_manager));
                $data['status_ttd_general_manager'] = 'Approved';
            }else{
                $data['tgl_ttd_general_manager'] = 'NA';
                $data['status_ttd_general_manager'] = 'Waiting Approval';
            }
        }else{
            //status general manager
            $data['avatar_gm'] = null;
            $data['nama_gm'] = $record->gm_ttd->fullname ?? '';
            $data['area_gm'] = $record->gm_ttd->area->name ?? '';
            $data['departemen_gm'] = $record->gm_ttd->department->name ?? '';
            $data['position_gm'] = $record->gm_ttd->position->nama ?? '';
            if(!empty($record->tgl_ttd_general_manager)){
                $data['tgl_ttd_general_manager'] = date('d M, Y H:i', strtotime($record->tgl_ttd_general_manager));
                $data['status_ttd_general_manager'] = 'Approved';
            }else{
                $data['tgl_ttd_general_manager'] = 'NA';
                $data['status_ttd_general_manager'] = 'Waiting Approval';
            }
        }

        if(!empty($record->pic_ttd->avatar)){
            //status pic
            $data['avatar_pic'] = $record->pic_ttd->avatar;
            $data['nama_pic'] = $record->pic_ttd->fullname ?? '';
            $data['area_pic'] = $record->pic_ttd->area->name ?? '';
            $data['departemen_pic'] = $record->pic_ttd->department->name ?? '';
            $data['position_pic'] = $record->pic_ttd->position->nama ?? '';
            if(!empty($record->tgl_ttd_pic)){
                $data['tgl_ttd_pic'] = date('d M, Y H:i', strtotime($record->tgl_ttd_pic));
                $data['status_ttd_pic'] = 'Approved';
            }else{
                $data['tgl_ttd_pic'] = 'NA';
                $data['status_ttd_pic'] = 'Waiting Approval';
            }
        }else{
            //status pic
            $data['avatar_pic'] = null;
            $data['nama_pic'] = $record->pic_ttd->fullname ?? '';
            $data['area_pic'] = $record->pic_ttd->area->name ?? '';
            $data['departemen_pic'] = $record->pic_ttd->department->name ?? '';
            $data['position_pic'] = $record->pic_ttd->position->nama ?? '';
            if(!empty($record->tgl_ttd_pic)){
                $data['tgl_ttd_pic'] = date('d M, Y H:i', strtotime($record->tgl_ttd_pic));
                $data['status_ttd_pic'] = 'Approved';
            }else{
                $data['tgl_ttd_pic'] = 'NA';
                $data['status_ttd_pic'] = 'Waiting Approval';
            }
        }

        if(!empty($record->hrd_ga_gm_ttd->avatar)){
            //status hrd & ga general manager
            $data['avatar_hrd_ga_gm'] = $record->hrd_ga_gm_ttd->avatar;
            $data['nama_hrd_ga_gm'] = $record->hrd_ga_gm_ttd->fullname ?? '';
            $data['area_hrd_ga_gm'] = $record->hrd_ga_gm_ttd->area->name ?? '';
            $data['departemen_hrd_ga_gm'] = $record->hrd_ga_gm_ttd->department->name ?? '';
            $data['position_hrd_ga_gm'] = $record->hrd_ga_gm_ttd->position->nama ?? '';
            if(!empty($record->tgl_ttd_hrd_ga_gm)){
                $data['tgl_ttd_hrd_ga_gm'] = date('d M, Y H:i', strtotime($record->tgl_ttd_hrd_ga_gm));
                $data['status_ttd_hrd_ga_gm'] = 'Approved';
            }else{
                $data['tgl_ttd_hrd_ga_gm'] = 'NA';
                $data['status_ttd_hrd_ga_gm'] = 'Waiting Approval';
            }
        }else{
            //status hrd_ga_gm
            $data['avatar_hrd_ga_gm'] = null;
            $data['nama_hrd_ga_gm'] = $record->hrd_ga_gm_ttd->fullname ?? '';
            $data['area_hrd_ga_gm'] = $record->hrd_ga_gm_ttd->area->name ?? '';
            $data['departemen_hrd_ga_gm'] = $record->hrd_ga_gm_ttd->department->name ?? '';
            $data['position_hrd_ga_gm'] = $record->hrd_ga_gm_ttd->position->nama ?? '';
            if(!empty($record->tgl_ttd_hrd_ga_gm)){
                $data['tgl_ttd_hrd_ga_gm'] = date('d M, Y H:i', strtotime($record->tgl_ttd_hrd_ga_gm));
                $data['status_ttd_hrd_ga_gm'] = 'Approved';
            }else{
                $data['tgl_ttd_hrd_ga_gm'] = 'NA';
                $data['status_ttd_hrd_ga_gm'] = 'Waiting Approval';
            }
        }
        //mr.mizukami
        if($record->direktur_ttd == null){
            $data['cek_direktur'] = 'kosong';
        }else{
            $data['cek_direktur'] = 'ada';
            $data['avatar_direktur'] = null;
            $data['nama_direktur'] = $record->direktur_ttd->fullname ?? '';
            $data['area_direktur'] = $record->direktur_ttd->area->name ?? '';
            $data['departemen_direktur'] = $record->direktur_ttd->department->name ?? '';
            $data['position_direktur'] = $record->direktur_ttd->position->nama ?? '';
            if(!empty($record->tgl_ttd_direktur)){
                $data['tgl_ttd_direktur'] = date('d M, Y H:i', strtotime($record->tgl_ttd_direktur));
                $data['status_ttd_direktur'] = 'Approved';
            }else{
                $data['tgl_ttd_direktur'] = 'NA';
                $data['status_ttd_direktur'] = 'Waiting Approval';
            }
        }
        //mr.sakurai
        $data['avatar_presiden'] = null;
        $data['nama_presiden'] = $record->presiden_ttd->fullname ?? '';
        $data['area_presiden'] = $record->presiden_ttd->area->name ?? '';
        $data['departemen_presiden'] = $record->presiden_ttd->department->name ?? '';
        $data['position_presiden'] = $record->presiden_ttd->position->nama ?? '';
        if(!empty($record->tgl_ttd_presiden)){
            $data['tgl_ttd_presiden'] = date('d M, Y H:i', strtotime($record->tgl_ttd_presiden));
            $data['status_ttd_presiden'] = 'Approved';
        }else{
            $data['tgl_ttd_presiden'] = 'NA';
            $data['status_ttd_presiden'] = 'Waiting Approval';
        }

        if(!empty($data['avatar_atasan'])){
            $data['url_atasan'] = asset('storage/avatars/'.$data['avatar_atasan']);
        }else{
            $data['url_atasan'] = null;
        }
        if(!empty($data['avatar_manager'])){
            $data['url_manager'] = asset('storage/avatars/'.$data['avatar_manager']);
        }else{
            $data['url_manager'] = null;
        }
        if(!empty($data['avatar_gm'])){
            $data['url_gm'] = asset('storage/avatars/'.$data['avatar_gm']);
        }else{
            $data['url_gm'] = null;
        }
        if(!empty($data['avatar_pic'])){
            $data['url_pic'] = asset('storage/avatars/'.$data['avatar_pic']);
        }else{
            $data['url_pic'] = null;
        }
        if(!empty($data['avatar_hrd_ga_gm'])){
            $data['url_hrd_ga_gm'] = asset('storage/avatars/'.$data['avatar_hrd_ga_gm']);
        }else{
            $data['url_hrd_ga_gm'] = null;
        }
        return response()->json($data);
    } 
    public function profile_back_approval_laporan(Request $request){
        return redirect(route('profile.training'))->with('tab_approval','open tab');
    }    
    //end laporan training
    public function profile_training_fpkt(Request $request, string $id = null){
        // dd('maintenance');
        $user = auth()->user();
        $kode = $id;
        if ($id) $id = decrypt($id);
        $fkt = Trainingfkt::where('id', $id)->first();
        $fpkt = Trainingfpkt::where('id_fkt', $id)->first(); 
        $arr_fpkt = Trainingfpkt::where('id_fkt', $id)->get(); 
        $cek_pemohon = Trainingfkt::where('id', $id)->where('id_pemohon', $user->employee_id)->first(); 
        $cek_peserta = Trainingfkt::where('id', $id)->where('id_peserta', $user->employee_id)->first(); 
        $cek_atasan = Trainingfkt::where('id', $id)->where('id_penilai', $user->employee_id)->first(); 
        return view('pages.profile.form-fpkt', compact('kode','user','fkt','fpkt','cek_pemohon','cek_peserta','cek_atasan','arr_fpkt'));
    }

    public function profile_training_collective(Request $request, $id){
        // dd($request->pelatihan);
        $user = auth()->user();
        $fkt = Trainingfkt::where('kode',decrypt($id))->where('judul', $request->pelatihan)->first();
        $qry_fkt = Trainingfkt::where('kode',decrypt($id))->where('judul', $request->pelatihan)->get();
        $arr_peserta = Employee::whereIn('id', $qry_fkt->pluck('id_peserta'))->get();
        return view('pages.profile.form-collective', compact('user','fkt','qry_fkt','arr_peserta'));

    }

    public function profile_training_fpkt_store(Request $request){
        // dd('maintenance');
        if($request->action == 'pemohon'){
            $user = auth()->user();
            $data = $request->input();
            $fkt = Trainingfkt::find($data['id_fkt']);

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
                            'catatan' => $data['catatan']
                        ];
                    }
                }
    
                $sum_peserta = array_sum(array_column($arr_peserta,'0'));
                // $sum_atasan = array_sum(array_column($arr_atasan,'0'));
                if($sum_peserta > 0){          
                    $fkt->update([
                        'date_peserta' => date('Y-m-d')
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
                        $insert->id_peserta = $fkt->id_peserta;
                        $insert->date_peserta = $fkt->date_peserta;
                        $insert->status = 'Waiting Approval';
                        $insert->save();
                    }
                    //ttd atasan
                    //ttd fpkt
                    $date_qr = date('Ymd');
                    $insert_fpkt_qr = new Qrcodefpkt;
                    $insert_fpkt_qr->id_fkt = $fkt->id;
                    $insert_fpkt_qr->qr = $date_qr.$fkt->id_peserta;
                    $insert_fpkt_qr->date_approval = date('Y-m-d H:i:s');
                    $insert_fpkt_qr->type = 1;
                    $insert_fpkt_qr->save();

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
                        $insert->status = 'On Proggress';
                        $insert->save();
                    }                    
                    //insert log user activity
                    $insert_log = new Log;
                    $insert_log->user_id = $user->id;
                    $insert_log->ip_address = $request->ip();
                    $insert_log->action = 'updated';
                    $insert_log->description = 'Modify "'.$fkt->judul.'" dengan nama pemohon'.'"'.$fkt->pemohon->fullname.'"';
                    $insert_log->save();
                }
                
                return redirect(route('profile.training.fkt.detail', encrypt($fkt->kode)))->with('status','Formulir Penilaian Kebutuhan Training '.$fkt->pemohon->fullname.' has been updated');
            }else{
                return redirect(route('profile.training.fkt.detail', encrypt($fkt->kode)))->with('error','Formulir Penilaian Kebutuhan Training no changes');
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
                        'catatan' => $data['catatan']
                    ];
                }
            }
            $sum_peserta = array_sum(array_column($arr_peserta,'0'));
            $sum_atasan = array_sum(array_column($arr_atasan,'0'));
            if($sum_peserta > 0){
                $delete = Trainingfpkt::where('id_fkt', $data['id_fkt'])->delete();
                $fkt->update([
                    'date_peserta' => date('Y-m-d')
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
                    $insert->id_peserta = $fkt->id_peserta;
                    $insert->date_peserta = $fkt->date_peserta;
                    if($sum_atasan > 0){
                        $insert->status = 'Approved';
                    }else{
                        $insert->status = 'Waiting Approval';
                    }
                    $insert->save();
                }
                //ttd atasan
                //ttd fpkt
                $date_qr = date('Ymd');
                $insert_fpkt_qr = new Qrcodefpkt;
                $insert_fpkt_qr->id_fkt = $fkt->id;
                $insert_fpkt_qr->qr = $date_qr.$fkt->id_peserta;
                $insert_fpkt_qr->date_approval = date('Y-m-d H:i:s');
                $insert_fpkt_qr->type = 1;
                $insert_fpkt_qr->save();
                //insert log user activity
                $insert_log = new Log;
                $insert_log->user_id = $user->id;
                $insert_log->ip_address = $request->ip();
                $insert_log->action = 'update';
                $insert_log->description = 'Modify "'.$fkt->judul.'" dengan nama pemohon'.'"'.$fkt->pemohon->fullname.'"';
                $insert_log->save();

                return redirect(route('profile.training.fkt.detail', encrypt($fkt->kode)))->with('status','Formulir Penilaian Kebutuhan Training '.$fkt->pemohon->fullname.' has been updated');
            }else{
                return redirect(route('profile.training.fkt.detail', encrypt($fkt->kode)))->with('error','Formulir Penilaian Kebutuhan Training no changes');
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
                        'catatan' => $data['catatan']
                    ];
                }
            }

            $sum_atasan = array_sum(array_column($arr_atasan,'0'));
            $sum_peserta = array_sum(array_column($arr_peserta,'0'));
            if($sum_atasan > 0){
                $delete = Trainingfpkt::where('id_fkt', $data['id_fkt'])->delete();
                $fkt->update([
                    'date_penilai' => date('Y-m-d')
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
                    $insert->id_peserta = $fkt->id_peserta;
                    $insert->date_peserta = $fkt->date_peserta;
                    $insert->id_atasan = $fkt->id_penilai;
                    $insert->date_atasan = $fkt->date_penilai;
                    if($sum_peserta > 0){
                        $insert->status = 'Approved';
                    }else{
                        $insert->status = 'Waiting Approval';
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
                
                //insert log user activity
                $insert_log = new Log;
                $insert_log->user_id = $user->id;
                $insert_log->ip_address = $request->ip();
                $insert_log->action = 'approved';
                $insert_log->description = 'Approved "'.$fkt->judul.'" dengan nama pemohon'.'"'.$fkt->pemohon->fullname.'"';
                $insert_log->save();

                return redirect(route('profile.training.fkt.detail', encrypt($fkt->kode)))->with('status','Formulir Penilaian Kebutuhan Training '.$fkt->pemohon->fullname.' has been updated');
            }else{
                return redirect(route('profile.training.fkt.detail', encrypt($fkt->kode)))->with('error','Formulir Penilaian Kebutuhan Training no changes');
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
                        'date_peserta' => date('Y-m-d'),
                        'date_penilai' => date('Y-m-d')
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
                        'id_peserta' => $qry_fkt->id_peserta,
                        'date_peserta' => date('Y-m-d'),
                        'id_atasan' => $qry_fkt->id_penilai,
                        'date_atasan' => date('Y-m-d'),
                        'status' => 'Approved',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                    //ttd atasan
                    //ttd fpkt
                    $date_qr = date('Ymd');
                    $insert_peserta_qr = new Qrcodefpkt;
                    $insert_peserta_qr->id_fkt = $qry_fkt->id;
                    $insert_peserta_qr->qr = $date_qr.$qry_fkt->id_peserta;
                    $insert_peserta_qr->date_approval = date('Y-m-d H:i:s');
                    $insert_peserta_qr->type = 1;
                    $insert_peserta_qr->save();

                    $insert_atasan_qr = new Qrcodefpkt;
                    $insert_atasan_qr->id_fkt = $qry_fkt->id;
                    $insert_atasan_qr->qr = $date_qr.$qry_fkt->id_penilai;
                    $insert_atasan_qr->date_approval = date('Y-m-d H:i:s');
                    $insert_atasan_qr->type = 2;
                    $insert_atasan_qr->save();
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
            
            return redirect(route('profile.training.fkt.detail', encrypt($kode[0])))->with('status','Formulir Penilaian Kebutuhan Training '.$emp->fullname.' has been updated');
        }
    }

    public function back_profile_fkt(){
        return redirect(route('profile.training'))->with('pengajuan','open tab');
    }
    
    public function back_profile_training(){
        return redirect(route('profile.training'))->with('verification','open tab');
    }

    //verified fkt and fpkt
    public function profile_training_verified(Request $request){
        $user = auth()->user();
        $data = Trainingfkt::whereIn('status', ['VERIFIED','APPROVED','VERIFIED 1'])->get()->unique('kode');
        if ($request->ajax()) {          
            return DataTables::of($data)
                ->addColumn('kode', function($data){
                    return $data['kode'];
                })
                ->addColumn('pemohon', function($data){
                    return $data->pemohon->fullname;
                })
                ->addColumn('tipe', function($data){
                    if($data['tipe'] == 'ptt'){
                        return 'Program Training Tahunan';
                    }else{
                        return 'Program Training Insidentil';
                    }
                })
                ->addColumn('status', function($data){
                    if($data['status'] == 'VERIFIED') return '<a href="#" <span class="badge text-bg-info"><i class="ri-time-line align-bottom"></i> Waiting Approval Atasan</span></a>';
                    if($data['status'] == 'VERIFIED 1') return '<a href="#" <span class="badge text-bg-info"><i class="ri-time-line align-bottom"></i> Waiting Approval BOD 1</span></a>';
                    if($data['status'] == 'APPROVED') return '<a href="#" <span class="badge text-bg-secondary"><i class="ri-time-line align-bottom"></i> Waiting Approval BOD 2</span></a>';
                    if($data['status'] == 'FINISHED') return '<a href="#" <span class="badge text-bg-success"><i class="ri-checkbox-circle-line align-bottom"></i> Finished</span></a>';
                    if($data['status'] == 'DONE') return '<a href="#" <span class="badge text-bg-success"><i class="ri-checkbox-circle-line align-bottom"></i> Finished</span></a>';
                })
                ->addColumn('action', function ($data) {
                    $data_user = auth()->user();            
                    if($data['status'] == 'VERIFIED'){      
                        $list_detail = '<li><a href="'.route('profile.training.verified.detail',encrypt($data['kode'])).'" class="dropdown-item"><i class="ri-file-edit-line align-bottom me-2 text-muted"></i> Approval</a></li>';
                        // $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_detail.'</ul></div>';
                    }
                    if($data['status'] == 'VERIFIED 1'){
                        if($data_user->employee_id == $data['id_checker']){
                            if(!empty($data['date_checker'])){
                                $list_detail = '<li><a href="'.route('profile.training.verified.detail',encrypt($data['kode'])).'" class="dropdown-item"><i class="ri-file-edit-line align-bottom me-2 text-muted"></i> Detail</a></li>';
                            }else{
                                $list_detail = '<li><a href="'.route('profile.training.verified.detail',encrypt($data['kode'])).'" class="dropdown-item"><i class="ri-file-edit-line align-bottom me-2 text-muted"></i> Approval</a></li>';
                            }
                        }else{
                            $list_detail = '<li><a href="'.route('profile.training.verified.detail',encrypt($data['kode'])).'" class="dropdown-item"><i class="ri-file-edit-line align-bottom me-2 text-muted"></i> Approval</a></li>';
                        }  
                        // $list_detail = '-';  
                        // $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_detail.'</ul></div>';
                    }
                    if($data['status'] == 'APPROVED'){
                        $list_detail = '<li><a href="'.route('profile.training.verified.detail',encrypt($data['kode'])).'" class="dropdown-item"><i class="ri-file-edit-line align-bottom me-2 text-muted"></i> Approval</a></li>';
                        // $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_detail.'</ul></div>';
                    }
                    if($data['status'] == 'FINISHED'){
                        $list_detail = '<li><a href="'.route('profile.training.verified.detail',encrypt($data['kode'])).'" class="dropdown-item"><i class="ri-file-edit-line align-bottom me-2 text-muted"></i> Detail</a></li>';
                        // $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_detail.'</ul></div>';
                    }
                    if($data['status'] == 'DONE'){
                        $list_detail = '<li><a href="'.route('profile.training.verified.detail',encrypt($data['kode'])).'" class="dropdown-item"><i class="ri-file-edit-line align-bottom me-2 text-muted"></i> Detail</a></li>';
                        // $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_detail.'</ul></div>';
                    }
                    $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_detail.'</ul></div>';
                    return $button;
                })
                ->addColumn('peserta', function($data){
                    $query = Trainingfkt::where('kode', $data['kode'])->get();
                    if($query->isNotEmpty()){
                        $peserta = '<div class="col-lg-12"><table class="table table-bordered" style="table-layout: fixed; width:100%;">';
                        $peserta .= '
                            <thead>
                                <tr>
                                    <th colspan="4" style="text-align: center;">Pengajuan Program Training</th>
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
                        foreach($query as $qry){
                            $peserta .= '<tr>';                  
                            $peserta .= '<td>'.$qry->peserta->fullname.'</td>';    
                            $peserta .= '<td>'.$qry->judul.'</td>';    
                            $peserta .= '<td>'.$qry->sifat.'</td>';    
                            $peserta .= '<td>'.$qry->alasan.'</td>';
                            $peserta .= '</tr>';
                        } 
                        $peserta .= '</tbody></table></div>';
                    }else{
                        $peserta = '';
                    }
                    return $peserta;
                })
                ->rawColumns(['action','status','tipe','kode','peserta'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.profile.training', compact('user'));
    }

    public function profile_training_verified_detail(Request $request, $id){
        $user = auth()->user();
        $kode = $id;
        $fkt = Trainingfkt::where('kode', decrypt($id))->first();
        
        return view('pages.profile.form-training-detail', compact('fkt','user','kode'));
    }

    public function profile_training_verified_store(Request $request){
        $user = auth()->user();
        //approved atasan
        if($user->employee_id == $request->id_checker){
            $update = Trainingfkt::where('kode', $request->kode_fkt)->update([
                'id_checker' => $user->employee_id,
                'date_checker' => date('Y-m-d'),
                'status' => 'VERIFIED 1'
            ]);

            //atasan ttd
            $date_qr = date('Ymd');
            $insert_verified_qr = new Qrcodefkt;
            $insert_verified_qr->kode_fkt = $request->kode_fkt;
            $insert_verified_qr->qr = $date_qr.$user->employee_id;
            $insert_verified_qr->date_approval = date('Y-m-d H:i:s');
            $insert_verified_qr->type = 5;
            $insert_verified_qr->save();

            //insert log user activity
            $insert_log = new Log;
            $insert_log->user_id = $user->id;
            $insert_log->ip_address = $request->ip();
            $insert_log->action = 'approved';
            $insert_log->description = 'Approved atasan training dengan no form "'.$request->kode_fkt.'"';
            $insert_log->save();
        }

        //approved mr. mizukami
        if($user->employee_id == '634'){
            $update = Trainingfkt::where('kode', $request->kode_fkt)->update([
                'id_verified' => $user->employee_id,
                'date_verified' => date('Y-m-d'),
                'status' => 'APPROVED'
            ]);

            //mr.mizukami ttd
            $date_qr = date('Ymd');
            $insert_verified_qr = new Qrcodefkt;
            $insert_verified_qr->kode_fkt = $request->kode_fkt;
            $insert_verified_qr->qr = $date_qr.$user->employee_id;
            $insert_verified_qr->date_approval = date('Y-m-d H:i:s');
            $insert_verified_qr->type = 3;
            $insert_verified_qr->save();

            //insert log user activity
            $insert_log = new Log;
            $insert_log->user_id = $user->id;
            $insert_log->ip_address = $request->ip();
            $insert_log->action = 'approved';
            $insert_log->description = 'Approved training dengan no form "'.$request->kode_fkt.'"';
            $insert_log->save();
        }
        //approved mr. sakurai
        if($user->employee_id == '634'){
            $arr_fkt = Trainingfkt::where('kode', $request->kode_fkt)->get()->unique('judul')->pluck('judul');
            foreach($arr_fkt as $key => $judul){
                $code_random = random_int(100000, 999999);
                $post = Trainingfkt::where('kode', $request->kode_fkt)->where('judul', $judul)->update([
                    'kode_judul' => $code_random
                ]);
            }
            $update = Trainingfkt::where('kode', $request->kode_fkt)->update([
                'id_approval' => $user->employee_id,
                'date_approval' => date('Y-m-d'),
                'status' => 'FINISHED'
            ]);
            //  mr.sakurai ttd
             $date_qr = date('Ymd');
             $insert_verified_qr = new Qrcodefkt;
             $insert_verified_qr->kode_fkt = $request->kode_fkt;
             $insert_verified_qr->qr = $date_qr.$user->employee_id;
             $insert_verified_qr->date_approval = date('Y-m-d H:i:s');
             $insert_verified_qr->type = 4;
             $insert_verified_qr->save();

            // insert log user activity
            $insert_log = new Log;
            $insert_log->user_id = $user->id;
            $insert_log->ip_address = $request->ip();
            $insert_log->action = 'approved';
            $insert_log->description = 'Approved training dengan no form "'.$request->kode_fkt.'"';
            $insert_log->save();
        }
        return redirect(route('profile.training'))->with('status','Training has been approved')->with('verification','open tab');
    }

    public function profile_training_fkt_pdf($id){
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

            $link_qr_pemohon = route('profile.training.qrcode.pemohon', ['code' => $pemohon_qr, 'id' => $pemohon_kode_qr]);
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

            $link_qr_checker = route('profile.training.qrcode.checker', ['code' => $checker_qr, 'id' => $checker_kode_qr]);
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

            $link_qr_verified = route('profile.training.qrcode.verified', ['code' => $verified_qr, 'id' => $verified_kode_qr]);
        }else{
            $verified_qr = null;
            $verified_kode_qr = null;

            $link_qr_verified = '';
        }

        $qr_4 =  $all_qrcode->whereStrict('type', 4)->first();
        if(!empty($qr_4)){
            $approval_qr = $qr_4->qr;
            $approval_kode_qr = str_replace("/","-",$qr_4->kode_fkt);

            $link_qr_approval = route('profile.training.qrcode.approval', ['code' => $approval_qr, 'id' => $approval_kode_qr]);
        }else{
            $approval_qr = null;
            $approval_kode_qr = null;

            $link_qr_approval = '';
        }

        $arr_fkt = Trainingfkt::where('kode', decrypt($id))->get();
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
        $pdf = PDF::loadView('pages.profile.fkt', $data)->setPaper('a4', 'landscape');
        $pdf->set_option("isPhpEnabled", true);
        return $pdf->stream('FORMULIR KEBUTUHAN TRAINING - '.$fkt->pemohon->fullname.'.pdf');
    }

    public function profile_training_fpkt_pdf($id){
        // dd(decrypt($id));
        $arr_fkt = Trainingfkt::where('kode', decrypt($id))->get();
        $html = '';
        foreach($arr_fkt as $fkt){
            $fpkt = Trainingfpkt::where('id_fkt', $fkt->id)->get();
            if($fpkt->isNotEmpty()){
                //ttd peserta
                $qr_1 = Qrcodefpkt::where('id_fkt', $fkt->id)->where('type', 1)->first();
                if(!empty($qr_1)){
                    $peserta_qr = $qr_1->qr;
                    $peserta_fkt_id = $qr_1->id_fkt;
                    $link_qr_peserta = route('profile.training.qrcode.fpkt', ['code' => $peserta_qr, 'id' => $peserta_fkt_id]);
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
                    $link_qr_atasan = route('profile.training.qrcode.fpkt', ['code' => $atasan_qr, 'id' => $atasan_fkt_id]);
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
                    $link_qr_hrd = route('profile.training.qrcode.fpkt', ['code' => $hrd_qr, 'id' => $hrd_fkt_id]);
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
                $view = view('pages.profile.fpkt')->with(compact('data'));
                $html .= $view->render();
            }
        }
        $pdf = PDF::set_option("isPhpEnabled", false);
        $pdf->loadHTML($html);
        $pdf->setPaper('a4', 'landscape');
        return $pdf->stream('FORMULIR PELAKSANAAN PELATIHAN.pdf');
    }

    public function qr_code_pemohon($code,$id){
        $kode_fkt = str_replace("-","/",$id);
        $query = Qrcodefkt::where('kode_fkt', $kode_fkt)->where('qr', $code)->where('type', 1)->first();
        if($query->fkt->tipe == 'ptt'){
            $usulan = 'Program Training Tahunan';
        }else{
            $usulan = 'Program Training Insidentil';
        }
        return view('pages.profile.codeqr-pemohon', compact('query','usulan'));
    }

    public function qr_code_checker($code,$id){
        $kode_fkt = str_replace("-","/",$id);
        $query = Qrcodefkt::where('kode_fkt', $kode_fkt)->where('qr', $code)->where('type', 5)->first();
        if($query->fkt->tipe == 'ptt'){
            $usulan = 'Program Training Tahunan';
        }else{
            $usulan = 'Program Training Insidentil';
        }
        return view('pages.profile.codeqr-checker', compact('query','usulan'));
    }

    public function qr_code_verified($code,$id){
        $kode_fkt = str_replace("-","/",$id);
        $query = Qrcodefkt::where('kode_fkt', $kode_fkt)->where('qr', $code)->where('type', 3)->first();
        if($query->fkt->tipe == 'ptt'){
            $usulan = 'Program Training Tahunan';
        }else{
            $usulan = 'Program Training Insidentil';
        }
        return view('pages.profile.codeqr-verified', compact('query','usulan'));
    }

    public function qr_code_approval($code,$id){
        $kode_fkt = str_replace("-","/",$id);
        $query = Qrcodefkt::where('kode_fkt', $kode_fkt)->where('qr', $code)->where('type', 4)->first();
        if($query->fkt->tipe == 'ptt'){
            $usulan = 'Program Training Tahunan';
        }else{
            $usulan = 'Program Training Insidentil';
        }
        return view('pages.profile.codeqr-approval', compact('query','usulan'));
    }

    public function qr_code_fpkt($code,$id){
        $query = Qrcodefpkt::where('id_fkt', $id)->where('qr', $code)->first();
        return view('pages.profile.codeqr-fpkt', compact('query'));
    }

    //Start training fkt ptt
    public function profile_training_fkt_ptt(Request $request){
        $user = auth()->user();
        $data = Trainingfkt::where(function ($data) use ($user) {
            $data->where('id_pemohon', $user->employee_id)
                  ->orWhere('id_peserta', $user->employee_id);
        })->get()->unique('kode');
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
                    if($data['status'] == 6) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#modal-status" <span class="badge text-bg-success view-status"><i class="ri-checkbox-circle-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 7) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-success view-status"><i class="ri-checkbox-circle-line align-bottom"></i> Finished</span></a>';
                    if($data['status'] == 15) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#modal-status" <span class="badge text-bg-secondary view-status"><i class="ri-error-warning-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 16) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#modal-status" <span class="badge text-bg-secondary view-status"><i class="ri-error-warning-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 17) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#modal-status" <span class="badge text-bg-danger view-status"><i class="ri-close-circle-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 18) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#modal-status" <span class="badge text-bg-danger view-status"><i class="ri-close-circle-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                })
                ->addColumn('action', function ($data) {            
                    $cek_user = auth()->user();                  
                    $list_edit = '<li><a href="'.route('profile.training.fkt.edit.ptt',encrypt($data['kode'])).'" class="dropdown-item"><i class="ri-edit-line align-bottom me-2 text-muted"></i> Edit</a></li>';
                    $list_detail = '<li><a href="'.route('profile.training.fkt.ptt.detail',encrypt($data['kode'])).'" class="dropdown-item"><i class="ri-file-edit-line align-bottom me-2 text-muted"></i> Buat FPKP</a></li>';
                    $list_print_fkt = '<li><a href="'.route('profile.training.fkt.ptt.pdf', encrypt($data['kode'])).'" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FKP</a></li>';
                    if($data['status'] == 1){      
                        if($data['id_pemohon'] == $cek_user->employee_id){
                            $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_edit.$list_print_fkt.'</ul></div>';
                        }else{
                            $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_print_fkt.'</ul></div>';
                        }
                    }elseif($data['status'] == 6){
                        $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_detail.$list_print_fkt.'</ul></div>';
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
                ->rawColumns(['action','status','kode','peserta'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.profile.training', compact('user'));
    }

    public function profile_training_fkt_ptt_approved(Request $request){
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
                        $button = '<a href="'. route('profile.training.fkt.ptt.approved.form', encrypt($data['tahun_usulan'])).'" data-toggle="tooltip" title="Approved" class="btn btn-info btn-sm"><i class="ri-task-line"></i> <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">'.$count_jml.' <span class="visually-hidden">unread messages</span></span></a>';
                    }else{
                        $button = '<a href="'. route('profile.training.fkt.ptt.approved.form', encrypt($data['tahun_usulan'])).'" data-toggle="tooltip" title="Approved" class="btn btn-info btn-sm"><i class="ri-task-line"></i></a>';
                    }
                    return $button;
                })                
                ->rawColumns(['action','tahun_usulan','jumlah_usulan'])
                ->addIndexColumn()
                ->make(true);
        }        
    }

    public function profile_training_fkt_ptt_approved_form(Request $request, $id){
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

        return view('pages.profile.training.ptt.form-fkt-approve', compact('user','tahun_usulan','query_fkt'));
    }

    public function profile_training_fkt_ptt_approved_store(Request $request){
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
    public function profile_training_fkt_ptt_revised_store(Request $request){
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
    public function profile_training_fkt_ptt_rejected_store(Request $request){
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
    //start print or pdf ptt fkt or fpkt
    public function profile_training_fkt_ptt_pdf($id){
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

            $link_qr_pemohon = route('profile.training.qrcode.pemohon', ['code' => $pemohon_qr, 'id' => $pemohon_kode_qr]);
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

            $link_qr_checker = route('profile.training.qrcode.checker', ['code' => $checker_qr, 'id' => $checker_kode_qr]);
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

            $link_qr_verified = route('profile.training.qrcode.verified', ['code' => $verified_qr, 'id' => $verified_kode_qr]);
        }else{
            $verified_qr = null;
            $verified_kode_qr = null;

            $link_qr_verified = '';
        }

        $arr_fkt = Trainingfkt::where('kode', decrypt($id))->get();
        $data = [
            'title' => 'FORMULIR KEBUTUHAN PELATIHAN',
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
        $pdf = PDF::loadView('pages.profile.fkt', $data)->setPaper('a4', 'landscape');
        $pdf->set_option("isPhpEnabled", true);
        return $pdf->stream('FORMULIR KEBUTUHAN PELATIHAN - '.$fkt->pemohon->fullname.'.pdf');
    }

    public function profile_training_fpkt_ptt_pdf($id){
        $arr_fkt = Trainingfkt::where('kode_judul', decrypt($id))->get();
        $html = '';
        foreach($arr_fkt as $fkt){
            $fpkt = Trainingfpkt::where('id_fkt', $fkt->id)->get();
            if($fpkt->isNotEmpty()){
                //ttd peserta
                $qr_1 = Qrcodefpkt::where('id_fkt', $fkt->id)->where('type', 1)->first();
                if(!empty($qr_1)){
                    $peserta_qr = $qr_1->qr;
                    $peserta_fkt_id = $qr_1->id_fkt;
                    $link_qr_peserta = route('profile.training.qrcode.fpkt', ['code' => $peserta_qr, 'id' => $peserta_fkt_id]);
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
                    $link_qr_atasan = route('profile.training.qrcode.fpkt', ['code' => $atasan_qr, 'id' => $atasan_fkt_id]);
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
                    $link_qr_hrd = route('profile.training.qrcode.fpkt', ['code' => $hrd_qr, 'id' => $hrd_fkt_id]);
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
                    'title' => 'Formulir Penilaian Kebutuhan Training',
                    'fkt' => $fkt,
                    'fpkt' => $fpkt,
                    'skor' => $skor,
                    'link_qr_peserta' => $link_qr_peserta,
                    'link_qr_atasan' => $link_qr_atasan,
                    'link_qr_hrd' => $link_qr_hrd
                ];
                $view = view('pages.profile.fpkt')->with(compact('data'));
                $html .= $view->render();
            }
        }
        $pdf = PDF::set_option("isPhpEnabled", false);
        $pdf->loadHTML($html);
        $pdf->setPaper('a4', 'landscape');
        return $pdf->stream('FORMULIR PENILAIAN KEBUTUHAN TRAINING.pdf');
    }

    public function profile_training_fpkt_ptt_print($id){
        $fkt = Trainingfkt::where('id', decrypt($id))->first();
        $html = '';
        $fpkt = Trainingfpkt::where('id_fkt', $fkt->id)->get();
        if($fpkt->isNotEmpty()){
            //ttd peserta
            $qr_1 = Qrcodefpkt::where('id_fkt', $fkt->id)->where('type', 1)->first();
            if(!empty($qr_1)){
                $peserta_qr = $qr_1->qr;
                $peserta_fkt_id = $qr_1->id_fkt;
                $link_qr_peserta = route('profile.training.qrcode.fpkt', ['code' => $peserta_qr, 'id' => $peserta_fkt_id]);
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
                $link_qr_atasan = route('profile.training.qrcode.fpkt', ['code' => $atasan_qr, 'id' => $atasan_fkt_id]);
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
                $link_qr_hrd = route('profile.training.qrcode.fpkt', ['code' => $hrd_qr, 'id' => $hrd_fkt_id]);
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
                'title' => 'Formulir Penilaian Kebutuhan Training',
                'fkt' => $fkt,
                'fpkt' => $fpkt,
                'skor' => $skor,
                'link_qr_peserta' => $link_qr_peserta,
                'link_qr_atasan' => $link_qr_atasan,
                'link_qr_hrd' => $link_qr_hrd
            ];
            $view = view('pages.profile.fpkt')->with(compact('data'));
            $html .= $view->render();
        }else{
            $data = [
                'title' => 'Formulir Penilaian Kebutuhan Training',
                'fkt' => $fkt,
                'fpkt' => '',
                'skor' => '',
                'link_qr_peserta' => '',
                'link_qr_atasan' => '',
                'link_qr_hrd' => ''
            ];
            $view = view('pages.profile.fpkt')->with(compact('data'));
            $html .= $view->render();
        }
        $pdf = PDF::set_option("isPhpEnabled", false);
        $pdf->loadHTML($html);
        $pdf->setPaper('a4', 'landscape');
        return $pdf->stream('FORMULIR PENILAIAN KEBUTUHAN TRAINING.pdf');
    }
    //end print or pdf ptt fkt or fpkt
    
    public function profile_training_fkt_form_ptt(Request $request){
        $user = auth()->user();
        $year_now = date('Y');
        
        $periode = Trainingperiode::where('status','1')->get();
        $employees = Employee::whereNot('status', 'TERMINATED')->get();
        $vendors = Vendor::where('tipe','training')->get();
        return view('pages.profile.training.ptt.form-fkt', compact('user','year_now','employees','vendors','periode'));
    }

    public function profile_training_fkt_edit_ptt(Request $request, $id){
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
        return view('pages.profile.training.ptt.edit-fkt', compact('user','year_now','employees','vendors','training_fkt','data_all','periode'));
    }

    public function profile_training_fkt_ptt_detail(Request $request, $id){
        $user = auth()->user();
        $fkt = Trainingfkt::where('kode', decrypt($id))->first();
        $query_fkt = Trainingfkt::where('kode', decrypt($id))->get();
        $jml_pemohon = Trainingfkt::where('kode', decrypt($id))->where('id_pemohon', $user->employee_id)->get()->count();
        $jml_peserta = Trainingfkt::where('kode', decrypt($id))->where('id_peserta', $user->employee_id)->get()->count();
        $total_fkt = $jml_pemohon+$jml_peserta;

        return view('pages.profile.training.ptt.form-fkt-detail', compact('user','fkt','query_fkt','total_fkt'));
    }

    public function profile_training_fkt_ptt_store(Request $request){
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
    
                    return redirect(route('profile.training'))->with('tab_ptt','open tab')->with('status','Draft Formulir Kebutuhan Pelatihan has been created');
                }else{
                    return redirect(route('profile.training'))->with('tab_ptt','open tab')->with('error','Formulir Kebutuhan Pelatihan no changes');
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
    
                    return redirect(route('profile.training'))->with('tab_ptt','open tab')->with('status','Formulir Kebutuhan Pelatihan '.$nama_pemohon.' has been updated');
                }else{
                    return redirect(route('profile.training'))->with('tab_ptt','open tab')->with('error','Formulir Kebutuhan Pelatihan no changes');
                }
            }
        } catch (Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function profile_training_fkt_ptt_update(Request $request){
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
        
                    return redirect(route('profile.training'))->with('tab_ptt','open tab')->with('status','Draft Formulir Kebutuhan Pelatihan has been updated');
                }else{
                    return redirect(route('profile.training'))->with('tab_ptt','open tab')->with('error','Formulir Kebutuhan Pelatihan no changes');
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
        
                    return redirect(route('profile.training'))->with('tab_ptt','open tab')->with('status','Formulir Kebutuhan Pelatihan '.$nama_pemohon.' has been updated');
                }else{
                    return redirect(route('profile.training'))->with('tab_ptt','open tab')->with('error','Formulir Kebutuhan Pelatihan no changes');
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

                    return redirect(route('profile.training'))->with('tab_ptt','open tab')->with('status','Formulir Kebutuhan Pelatihan '.$nama_pemohon.' has been updated');
                }else{
                    return redirect(route('profile.training'))->with('tab_ptt','open tab')->with('error','Formulir Kebutuhan Pelatihan no changes');
                }
            }
        } catch (Exception $e){
            DB::rollback();
            return $e->getMessage();
        }
    }

    public function profile_status_fkt_ptt(Request $request){
        if(!empty($request->kode)){
            $query = Trainingfkt::where('kode', decrypt($request->kode))->first();
            $data['judul'] = $query->kode;
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

    public function profile_back_fkt_ptt(Request $request){
        return redirect(route('profile.training'))->with('tab_ptt','open tab');
    }
    
    public function profile_back_approve_fkt_ptt(Request $request){
        return redirect(route('profile.training'))->with('tab_approve_ptt','open tab');
    }
    //End training fkt ptt

    //Start training fpkt ptt
    public function profile_training_fpkt_ptt(Request $request, $id){
        $user = auth()->user();
        $kode = $id;
        $id = decrypt($id);
        $vendors = Vendor::where('tipe','training')->get();
        $employees = Employee::whereNot('status', 'TERMINATED')->get();
        $fkt = Trainingfkt::where('id', $id)->first();
        $fpkt = Trainingfpkt::where('id_fkt', $id)->first(); 
        $arr_fpkt = Trainingfpkt::where('id_fkt', $id)->get(); 
        $cek_pemohon = Trainingfkt::where('id', $id)->where('id_pemohon', $user->employee_id)->first(); 
        $cek_peserta = Trainingfkt::where('id', $id)->where('id_peserta', $user->employee_id)->first(); 
        return view('pages.profile.training.ptt.form-fpkt', compact('kode','user','fkt','fpkt','cek_pemohon','cek_peserta','arr_fpkt','vendors','employees'));
    }

    public function profile_training_fpkt_ptt_store(Request $request){
        dd($request->all());
        if($request->action == 'pemohon'){
            $user = auth()->user();
            $data = $request->input();
            $fkt = Trainingfkt::find($data['id_fkt']);
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
                    foreach($arr_data as $key => $value){
                        $insert = new Trainingfpkt;
                        $insert->id_fkt = $value['id_fkt'];
                        $insert->latar_belakang = $value['latar_belakang'];
                        $insert->biaya_fpkt = $value['biaya_fpkt'];
                        $insert->id_vendor = $value['id_vendor'];
                        $insert->nama_vendor = $value['vendor_other'];
                        $insert->date_pelaksanaan = $value['date_pelaksanaan'];
                        $insert->judul_fpkt = $fkt->judul;
                        $insert->jenis_fpkt = $fkt->jenis_pelatihan;
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
                
                return redirect(route('profile.training.fkt.ptt.detail', encrypt($fkt->kode)))->with('status','Formulir Penilaian Kebutuhan Training '.$fkt->pemohon->fullname.' has been updated');
            }else{
                return redirect(route('profile.training.fkt.ptt.detail', encrypt($fkt->kode)))->with('error','Formulir Penilaian Kebutuhan Training no changes');
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
                    // //notification atasan departemen
                    // if(!empty($fkt->checker->email)){
                    //     $qry_user = User::where('employee_id', $fkt->id_checker)->first();
                    //     if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                    //         $details = [
                    //             'greeting' => 'Hi '.$fkt->checker->fullname,
                    //             'subject' => 'Penilaian Kebutuhan Training',
                    //             'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$fkt->judul.'" yang membutuhkan approval anda',
                    //             'actionText' => 'Silahkan Login',
                    //             'actionURL' => url('/employee/training/fkt/ptt/approved/'.encrypt($fkt->tahun_usulan).'/form'),
                    //             'thanks' => 'Terimakasih atas perhatiannya!!'
                    //         ];
                    //     }else{
                    //         $details = [
                    //             'greeting' => 'Hi '.$fkt->checker->fullname,
                    //             'subject' => 'Penilaian Kebutuhan Training',
                    //             'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$fkt->judul.'" yang membutuhkan approval anda',
                    //             'actionText' => 'Silahkan Login',
                    //             'actionURL' => url('/mytraining/fkt/ptt/approved/'.encrypt($fkt->tahun_usulan).'/form'),
                    //             'thanks' => 'Terimakasih atas perhatiannya!!'
                    //         ];
                    //     }
                    //     //send mail
                    //     $qry_user->notify(new AccountNotification($details));
                    // }

                    // send email to pic hrd
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
                                    'subject' => 'Scheduling Training (PTT)',
                                    'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$fkt->judul.'" yang sudah disetujui dan perlu diverifikasi',
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

                //insert log user activity
                $insert_log = new Log;
                $insert_log->user_id = $user->id;
                $insert_log->ip_address = $request->ip();
                $insert_log->action = 'update';
                $insert_log->description = 'Modify "'.$fkt->judul.'" dengan nama pemohon'.'"'.$fkt->pemohon->fullname.'"';
                $insert_log->save();

                return redirect(route('profile.training.fkt.ptt.detail', encrypt($fkt->kode)))->with('status','Formulir Penilaian Kebutuhan Training '.$fkt->pemohon->fullname.' has been updated');
            }else{
                return redirect(route('profile.training.fkt.ptt.detail', encrypt($fkt->kode)))->with('error','Formulir Penilaian Kebutuhan Training no changes');
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
                    // //notification atasan departemen
                    // if(!empty($fkt->checker->email)){
                    //     $qry_user = User::where('employee_id', $fkt->id_checker)->first();
                    //     if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                    //         $details = [
                    //             'greeting' => 'Hi '.$fkt->checker->fullname,
                    //             'subject' => 'Penilaian Kebutuhan Training',
                    //             'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$fkt->judul.'" yang membutuhkan approval anda',
                    //             'actionText' => 'Silahkan Login',
                    //             'actionURL' => url('/employee/training/fkt/ptt/approved/'.encrypt($fkt->tahun_usulan).'/form'),
                    //             'thanks' => 'Terimakasih atas perhatiannya!!'
                    //         ];
                    //     }else{
                    //         $details = [
                    //             'greeting' => 'Hi '.$fkt->checker->fullname,
                    //             'subject' => 'Penilaian Kebutuhan Training',
                    //             'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$fkt->judul.'" yang membutuhkan approval anda',
                    //             'actionText' => 'Silahkan Login',
                    //             'actionURL' => url('/mytraining/fkt/ptt/approved/'.encrypt($fkt->tahun_usulan).'/form'),
                    //             'thanks' => 'Terimakasih atas perhatiannya!!'
                    //         ];
                    //     }
                    //     //send mail
                    //     $qry_user->notify(new AccountNotification($details));
                    // }

                    // send email to pic hrd
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
                                    'subject' => 'Scheduling Training (PTT)',
                                    'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$fkt->judul.'" yang sudah disetujui dan perlu diverifikasi',
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
                
                //insert log user activity
                $insert_log = new Log;
                $insert_log->user_id = $user->id;
                $insert_log->ip_address = $request->ip();
                $insert_log->action = 'approved';
                $insert_log->description = 'Approved "'.$fkt->judul.'" dengan nama pemohon'.'"'.$fkt->pemohon->fullname.'"';
                $insert_log->save();

                return redirect(route('profile.training.fkt.ptt.detail', encrypt($fkt->kode)))->with('status','Formulir Penilaian Kebutuhan Training '.$fkt->pemohon->fullname.' has been updated');
            }else{
                return redirect(route('profile.training.fkt.ptt.detail', encrypt($fkt->kode)))->with('error','Formulir Penilaian Kebutuhan Training no changes');
            }
        }

        if($request->action == 'collective'){
            $user = auth()->user();
            $data = $request->input();
            dd($data);
            $arr_id = explode(',', $data['id_fkt']);
            $fkt = Trainingfkt::with('pemohon')->whereIn('id', $arr_id)->get();
            $query_fkt = Trainingfkt::with('pemohon')->whereIn('id', $arr_id)->first();
            $emp = Employee::whereIn('id', $fkt->pluck('id_pemohon'))->first();
            // $code_random = random_int(100000, 999999);
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
                            $id_atasan = $user->employee_id;
                            $date_atasan = date('Y-m-d H:i:s');
                        }else{
                            $id_atasan = null;
                            $date_atasan = null;
                        }
                        if($jml_peserta > 0){
                            $id_peserta = $qry_fkt->id_peserta;
                            $date_peserta = date('Y-m-d H:i:s');
                        }else{
                            $id_peserta = null;
                            $date_peserta = null;
                        }
                        //cek status
                        if($jml_atasan > 0 && $jml_peserta > 0){
                            $status_fpkt = 19;
                        }else{
                            $status_fpkt = 10;
                        }
                        //cek vendor
                        if($data['id_vendor'] != 'other'){
                            $id_vendor = $data['id_vendor'];
                        }else{
                            $id_vendor = $data['vendor_other'];
                        }
                        $insert[] = [
                            'id_fkt' => $arr_id[$n],
                            'latar_belakang' => $data['latar_belakang'],
                            'biaya_fpkt' => $data['biaya_fpkt'],
                            'id_vendor' => $id_vendor,
                            'date_pelaksanaan' => $data['date_pelaksanaan'],
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
                            'date_atasan' => $date_atasan,
                            'status' => $status_fpkt,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ];
                        //delete qrcode fpkt sebelumnya
                        $cek_qr_fpkt = Qrcodefpkt::where('id_fkt', $qry_fkt->id)->get();
                        if($cek_qr_fpkt->isNotEmpty()){
                            $delete_qr_fpkt = Qrcodefpkt::where('id_fkt', $qry_fkt->id)->whereIn('type',[1,2])->delete();
                        }
                        //ttd atasan
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
                            $insert_atasan_qr->qr = $date_qr.$user->employee_id;
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
                if($sum_peserta > 0){
                    if($sum_atasan > 0){
                        //notification atasan departemen
                        // $qry_user = User::where('employee_id', $query_fkt->id_checker)->first();
                        // if(!empty($qry_user->email)){
                        //     if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                        //         $details = [
                        //             'greeting' => 'Hi '.$qry_user->name,
                        //             'subject' => 'PELAKSANAAN PROGRAM PELATIHAN TAHUNAN',
                        //             'body' => 'Ingin Menginformasikan bahwa ada usulan pelaksanaan program pelatihan tahunan dengan nomor dokumen "'.$query_fkt->kode.'" pemohon "'.$query_fkt->pemohon->fullname.'" yang membutuhkan approval anda',
                        //             'actionText' => 'Silahkan Login',
                        //             'actionURL' => url('/employee/training/fkt/ptt/approved/'.encrypt($arr_data['tahun_usulan'][0]).'/form'),
                        //             'thanks' => 'Terimakasih atas perhatiannya!!'
                        //         ];
                        //     }else{
                        //         $details = [
                        //             'greeting' => 'Hi '.$qry_user->name,
                        //             'subject' => 'PROGRAM PELATIHAN TAHUNAN',
                        //             'body' => 'Ingin Menginformasikan bahwa ada usulan program pelatihan tahunan dengan nomor dokumen "'.$kode.'" pemohon "'.$nama_pemohon.'" yang membutuhkan approval anda',
                        //             'actionText' => 'Silahkan Login',
                        //             'actionURL' => url('/mytraining/fkt/ptt/approved/'.encrypt($arr_data['tahun_usulan'][0]).'/form'),
                        //             'thanks' => 'Terimakasih atas perhatiannya!!'
                        //         ];
                        //     }
                        //     //send mail
                        //     $qry_user->notify(new AccountNotification($details));
                        // }

                        // send email to pic hrd
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
                        //                 'subject' => 'Scheduling Training (PTT)',
                        //                 'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$judul[0].'" yang sudah disetujui dan perlu diverifikasi',
                        //                 'actionText' => 'Silahkan Login',
                        //                 'actionURL' => url('/hrd/training/ptt'),
                        //                 'thanks' => 'Terimakasih atas perhatiannya!!'
                        //             ];
                        //             //send mail
                        //             $qry_user->notify(new AccountNotification($details));
                        //         }
                        //     }
                        // }
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
                $insert_log->description = 'Approved collective"'.$query_fkt->judul.'" dengan nama pemohon'.'"'.$emp->fullname.'"';
                $insert_log->save();
                
                return redirect(route('profile.training.fkt.ptt.detail', encrypt($query_fkt->kode)))->with('status','Formulir Penilaian Kebutuhan Training '.$emp->fullname.' has been updated');
            }else{
                return redirect(route('profile.training.fkt.ptt.detail', encrypt($query_fkt->kode)))->with('error','Formulir Penilaian Kebutuhan Training no changes');
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
                    // if(!empty($qry_fkt->checker->email)){
                    //     $qry_user = User::where('employee_id', $qry_fkt->id_checker)->first();
                    //     if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
                    //         $details = [
                    //             'greeting' => 'Hi '.$qry_fkt->checker->fullname,
                    //             'subject' => 'Penilaian Kebutuhan Training',
                    //             'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$qry_fkt->judul.'" yang membutuhkan approval anda',
                    //             'actionText' => 'Silahkan Login',
                    //             'actionURL' => url('/employee/training/fkt/ptt/approved/'.encrypt($qry_fkt->tahun_usulan).'/form'),
                    //             'thanks' => 'Terimakasih atas perhatiannya!!'
                    //         ];
                    //     }else{
                    //         $details = [
                    //             'greeting' => 'Hi '.$qry_fkt->checker->fullname,
                    //             'subject' => 'Penilaian Kebutuhan Training',
                    //             'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$qry_fkt->judul.'" yang membutuhkan approval anda',
                    //             'actionText' => 'Silahkan Login',
                    //             'actionURL' => url('/mytraining/fkt/ptt/approved/'.encrypt($qry_fkt->tahun_usulan).'/form'),
                    //             'thanks' => 'Terimakasih atas perhatiannya!!'
                    //         ];
                    //     }
                    //     //send mail
                    //     $qry_user->notify(new AccountNotification($details));
                    // }
                    
                    // send email to pic hrd
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
                                    'subject' => 'Scheduling Training (PTT)',
                                    'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$judul[0].'" yang sudah disetujui dan perlu diverifikasi',
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
            
            return redirect(route('profile.training.fkt.ptt.detail', encrypt($kode[0])))->with('status','Formulir Penilaian Kebutuhan Training '.$emp->fullname.' has been updated');
        }
    }
    //End training fpkt ptt

    //Start training collective ptt
    public function profile_training_collective_ptt(Request $request, $id){
        $user = auth()->user();
        $vendors = Vendor::where('tipe','training')->get();
        $fkt = Trainingfkt::where('kode',decrypt($id))->where('judul', $request->pelatihan)->first();
        $qry_fkt = Trainingfkt::where('kode',decrypt($id))->where('judul', $request->pelatihan)->get();
        $arr_peserta = Employee::whereIn('id', $qry_fkt->pluck('id_peserta'))->get();
        return view('pages.profile.training.ptt.form-collective', compact('user','fkt','qry_fkt','arr_peserta','vendors'));
    }

    public function profile_training_collective_approve_ptt(Request $request, $id){
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

        return view('pages.profile.training.ptt.form-collective-approve', compact('kode','user','fkt','fpkt','cek_pemohon','cek_peserta','cek_atasan','arr_fpkt','qry_fkt','arr_peserta'));
    }
    //End training collective ptt

    //Start training fkt pti
    public function profile_training_fkt_pti(Request $request){
        $user = auth()->user();
        $data = Trainingfpkt::where(function ($data) use ($user) {
            $data->where('id_pemohon', $user->employee_id)
                  ->orWhere('id_peserta', $user->employee_id);
        })->get()->unique('kode_judul_fpkt');
        if ($request->ajax()) {          
            return DataTables::of($data)
                ->addColumn('kode_judul', function($data){
                    return $data['kode_judul'];
                })
                ->addColumn('pemohon', function($data){
                    return $data->pemohon->fullname;
                })
                ->addColumn('jml_peserta', function($data){
                    $jml_peserta = Trainingfpkt::where('kode_judul_fpkt', $data['kode_judul_fpkt'])->count();
                    return $jml_peserta;
                })
                ->addColumn('status', function($data){
                    if($data['status'] == 1) return '<a href="#" <span class="badge text-bg-primary"><i class="ri-edit-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 2) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-warning view-status"><i class="ri-time-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 3) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-info view-status"><i class="ri-time-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 4) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-info view-status"><i class="ri-time-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 5) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-secondary view-status"><i class="ri-time-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 6) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-success view-status"><i class="ri-checkbox-circle-line align-bottom"></i> '.$data->training_status->name.'</span></a>';
                    if($data['status'] == 7) return '<a href="#" data-id="'.encrypt($data['kode']).'" data-bs-toggle="modal" data-bs-target="#statusModal" <span class="badge text-bg-success view-status"><i class="ri-checkbox-circle-line align-bottom"></i> Finished</span></a>';
                })
                ->addColumn('action', function ($data) {            
                    $list_edit = '<li><a href="'.route('profile.training.fkt.edit.pti',encrypt($data['kode'])).'" class="dropdown-item"><i class="ri-edit-line align-bottom me-2 text-muted"></i> Edit</a></li>';
                    $list_detail = '<li><a href="'.route('profile.training.fkt.pti.detail',encrypt($data['kode'])).'" class="dropdown-item"><i class="ri-file-edit-line align-bottom me-2 text-muted"></i> Buat FPKT</a></li>';
                    $list_print_fkt = '<li><a href="'.route('profile.training.fkt.pti.pdf', encrypt($data['kode'])).'" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Print FKT</a></li>';
                    if($data['status'] == 1){      
                        $cek_user = auth()->user();                  
                        if($data['id_pemohon'] == $cek_user->employee_id){
                            $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_edit.$list_print_fkt.'</ul></div>';
                        }else{
                            $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_print_fkt.'</ul></div>';
                        }
                    }elseif($data['status'] == 2){
                        $cek_user = auth()->user();
                        if(!empty($data['date_peserta']) && $data['id_penilai'] == $cek_user->employee_id){
                            $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_print_fkt.'</ul></div>';
                        }else{
                            if(!empty($data['date_verified_pic'])){
                                $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_detail.$list_print_fkt.'</ul></div>';
                            }else{
                                $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_print_fkt.'</ul></div>';
                            }
                        }
                    }else{
                        $button = '<div class="dropdown d-inline-block"><button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button><ul class="dropdown-menu dropdown-menu-end">'.$list_print_fkt.'</ul></div>';
                    }
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
                            $peserta .= '<td>'.$qry->date_pelaksanaan.'</td>';    
                            $peserta .= '<td>'.$qry->biaya_fpkt.'</td>';    
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
        return view('pages.profile.training', compact('user'));
    }

    public function profile_status_fkt_pti(Request $request){
        if(!empty($request->kode)){
            $query = Trainingfkt::where('kode', decrypt($request->kode))->first();
            $cek_status = Trainingfkt::where('kode', decrypt($request->kode))->where('kategori','free')->count();
            $data['kode'] = $request->kode;
            $data['nama_pemohon'] = $query->pemohon->fullname;
            $data['date_pemohon'] = date('d M Y H:i', strtotime($query->date_pemohon));
            $data['status_fkt'] = $query->status;
            $data['nama_status_fkt'] = $query->training_status->name;
            $data['cek_status'] = $cek_status;
            if($query->status == 3){
                $data['cek_fpkt'] = '-';
                $data['nama_checker'] = 'Waiting Approval';
                $data['date_checker'] = '-';
                $data['nama_verified'] = '-';
                $data['date_verified'] = '-';
                $data['nama_approved'] = '-';
                $data['date_approved'] = '-';
            }else{
                if($query->status == 2 || $query->status == 4 || $query->status == 5){
                    $data['cek_fpkt'] = 'Waiting Approval';
                }else{
                    $data['cek_fpkt'] = 'Approved';
                }
                if(!empty($query->date_checker)){
                    $data['nama_checker'] = $query->checker->fullname;
                    $data['date_checker'] = date('d M Y H:i', strtotime($query->date_checker));
                }else{
                    $data['nama_checker'] = 'Waiting Approval';
                    $data['date_checker'] = '-';
                }
                if(!empty($query->date_verified)){
                    $data['nama_verified'] = $query->verified->fullname;
                    $data['date_verified'] = date('d M Y H:i', strtotime($query->date_verified));
                }else{
                    $data['nama_verified'] = 'Waiting Approval';
                    $data['date_verified'] = '-';
                }
                if(!empty($query->date_approval)){
                    $data['nama_approved'] = $query->approval->fullname;
                    $data['date_approved'] = date('d M Y H:i', strtotime($query->date_approval));
                }else{
                    $data['nama_approved'] = 'Waiting Approval';
                    $data['date_approved'] = '-';
                }
            }
        }
        if(!empty($request->id)){
            $query2 = Trainingfkt::where('id', decrypt($request->id))->first();
            $data['status_fpkt'] = $query2->fpkt->training_status->id;
            $data['nama_status_fpkt'] = $query2->fpkt->training_status->name;
            if(!empty($query2->fpkt->date_peserta)){
                $data['nama_peserta'] = $query2->fpkt->peserta->fullname;
                $data['date_peserta'] = date('d M Y H:i', strtotime($query2->fpkt->date_peserta));
            }else{
                $data['nama_peserta'] = 'Waiting Approval';
                $data['date_peserta'] = '-';
            }
            if(!empty($query2->fpkt->date_atasan)){
                $data['nama_penilai'] = $query2->fpkt->atasan->fullname;
                $data['date_penilai'] = date('d M Y H:i', strtotime($query2->fpkt->date_atasan));
            }else{
                $data['nama_penilai'] = 'Waiting Approval';
                $data['date_penilai'] = '-';
            }
            $data['nama_hrd'] = $query2->fpkt->hrd->fullname ?? 'Waiting Approval';
            if(!empty($query2->fpkt->date_hrd)){
                $data['date_hrd'] = date('d M Y H:i', strtotime($query2->fpkt->date_hrd));
            }else{
                $data['date_hrd'] = '-';
            }         
        }
        
        return response()->json($data);
    }

    public function profile_training_fkt_pti_detail(Request $request, $id){
        $user = auth()->user();
        $fkt = Trainingfkt::where('kode', decrypt($id))->first();
        $query_fkt = Trainingfkt::where('kode', decrypt($id))->get();
        $jml_pemohon = Trainingfkt::where('kode', decrypt($id))->where('id_pemohon', $user->employee_id)->get()->count();
        $jml_peserta = Trainingfkt::where('kode', decrypt($id))->where('id_peserta', $user->employee_id)->get()->count();
        $jml_penilai = Trainingfkt::where('kode', decrypt($id))->where('id_penilai', $user->employee_id)->get()->count();
        $total_fkt = $jml_pemohon+$jml_peserta+$jml_penilai;

        return view('pages.profile.training.pti.form-fkt-detail', compact('user','fkt','query_fkt','total_fkt'));
    }

    public function profile_training_fkt_pti_approved(Request $request){
        $user = auth()->user();
        $cek_approve_checker = Trainingfkt::where('id_checker', $user->employee_id)
            ->where('tipe', 'pti')
            ->whereNotNull('date_pemohon')
            ->whereNull('date_checker')->count();
        $cek_approve_penilai = Trainingfkt::where('id_penilai', $user->employee_id)
            ->where('tipe', 'pti')
            ->whereNotNull('date_peserta')
            ->whereNull('date_penilai')->count();

        // $cek_jml_approve_pti = $cek_approve_checker+$cek_approve_penilai;
        if($cek_approve_checker > 0){
            $data = Trainingfkt::where(function ($data) use ($user) {
                $data->where('id_checker', $user->employee_id);
            })->where('tipe', 'pti')->whereNotNull('date_pemohon')->where('status',3)->get()->unique('kode_judul');
        }else{
            if($cek_approve_penilai > 0){
                $data = Trainingfkt::where(function ($data) use ($user) {
                    $data->Where('id_penilai', $user->employee_id);
                })->where('tipe', 'pti')->whereNotNull('date_peserta')->where('status',2)->get()->unique('kode_judul');
            }else{
                $data = array();
            }
        }
        if ($request->ajax()) {          
            return DataTables::of($data)
                ->addColumn('tahun_usulan', function($data){
                    return $data->tahun_usulan;
                })
                ->addColumn('pemohon', function($data){
                    return $data->pemohon->fullname;
                })
                ->addColumn('judul', function($data){
                    return $data->judul;
                })
                ->addColumn('jenis', function($data){
                    return $data->jenis_pelatihan;
                })
                ->addColumn('jumlah_peserta', function($data){
                    $jml_peserta = Trainingfkt::where('kode_judul', $data['kode_judul'])->count();
                    return $jml_peserta;
                })
                ->addColumn('status', function($data){
                    return '<span class="badge text-bg-warning">Waiting Approval</span>';
                })
                ->addColumn('action', function ($data) {
                    $qry_user = auth()->user();  
                    $jml_approve_checker = Trainingfkt::where('id_checker', $qry_user->employee_id)
                        ->where('tahun_usulan', $data['tahun_usulan'])->where('tipe','pti')
                        ->whereNotNull('date_pemohon')
                        ->whereNull('date_checker')
                        ->where('status',3)->get()->count(); 
                    $jml_approve_penilai = Trainingfkt::where('id_penilai', $qry_user->employee_id)
                        ->where('tahun_usulan', $data['tahun_usulan'])->where('tipe','pti')
                        ->whereNotNull('date_peserta')
                        ->whereNull('date_penilai')
                        ->get()->count();

                    $count_jml = $jml_approve_checker+$jml_approve_penilai;
                    if(empty($data['date_penilai']) && $qry_user->employee_id == $data['id_penilai']){
                        if($count_jml > 0){
                            $button = '<div class="dropdown d-inline-block">';
                                $button .= '<button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i><span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">'.$count_jml.' <span class="visually-hidden">unread messages</span></span></button>';
                                $button .= '<ul class="dropdown-menu dropdown-menu-end">';
                                    $button .= '<li><a href="'. route('profile.training.fkt.pti.detail', encrypt($data->kode)).'" data-toggle="tooltip" title="Approved" class="dropdown-item"><i class="ri-checkbox-line align-bottom me-2 text-muted"></i> Approve</a></li>';
                                    $button .= '<li><a href="'.route('profile.training.fkt.pti.pdf', encrypt($data->kode)).'" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FKT</a></li>';
                                    $button .= '<li><a href="'.route('profile.training.fpkt.pti.pdf', encrypt($data->kode_judul)).'" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FPKT</a></li>';
                                $button .= '</ul>';
                            $button .= '</div>';
                        }else{
                            $button = '<div class="dropdown d-inline-block">';
                                $button .= '<button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button>';
                                $button .= '<ul class="dropdown-menu dropdown-menu-end">';
                                    $button .= '<li><a href="'. route('profile.training.fkt.pti.detail', encrypt($data->kode)).'" data-toggle="tooltip" title="Approved" class="dropdown-item"><i class="ri-checkbox-line align-bottom me-2 text-muted"></i> Approve</a></li>';
                                    $button .= '<li><a href="'.route('profile.training.fkt.pti.pdf', encrypt($data->kode)).'" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FKT</a></li>';
                                    $button .= '<li><a href="'.route('profile.training.fpkt.pti.pdf', encrypt($data->kode_judul)).'" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FPKT</a></li>';
                                $button .= '</ul>';
                            $button .= '</div>';
                        }
                    }else{
                        if(empty($data->date_checker) && $qry_user->employee_id == $data->id_checker){
                            if($count_jml > 0){
                                $button = '<div class="dropdown d-inline-block">';
                                    $button .= '<button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i><span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">'.$count_jml.' <span class="visually-hidden">unread messages</span></span></button>';
                                    $button .= '<ul class="dropdown-menu dropdown-menu-end">';
                                        $button .= '<li><a href="#" data-id="'.encrypt($data->kode_judul).'" data-bs-toggle="modal" data-bs-target=".bs-example-modal-center" class="dropdown-item view-approve"><i class="ri-checkbox-line align-bottom me-2 text-muted"></i> Approve</a></li>';
                                        $button .= '<li><a href="'.route('profile.training.fkt.pti.pdf', encrypt($data->kode)).'" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FKT</a></li>';
                                        // $button .= '<li><a href="'.route('profile.training.fpkt.pti.pdf', encrypt($data->kode_judul)).'" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FPKT</a></li>';
                                    $button .= '</ul>';
                                $button .= '</div>';
                            }else{
                                $button = '<div class="dropdown d-inline-block">';
                                    $button .= '<button class="btn btn-soft-primary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="ri-more-fill align-middle"></i></button>';
                                    $button .= '<ul class="dropdown-menu dropdown-menu-end">';
                                        $button .= '<li><a href="#" data-id="'.encrypt($data->kode_judul).'" data-bs-toggle="modal" data-bs-target=".bs-example-modal-center" class="dropdown-item view-approve"><i class="ri-checkbox-line align-bottom me-2 text-muted"></i> Approve</a></li>';
                                        $button .= '<li><a href="'.route('profile.training.fkt.pti.pdf', encrypt($data->kode)).'" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FKT</a></li>';
                                        // $button .= '<li><a href="'.route('profile.training.fpkt.pti.pdf', encrypt($data->kode_judul)).'" class="dropdown-item" target="_blank"><i class="ri-file-pdf-line align-bottom me-2 text-muted"></i> Document FPKT</a></li>';
                                    $button .= '</ul>';
                                $button .= '</div>';
                            }
                        }else{
                            $button = '-';
                        }
                    }
                    return $button;
                })                
                ->rawColumns(['action','status'])
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function profile_training_fkt_pti_approved_form(Request $request, $id){
        $user = auth()->user();
        $tahun_usulan = decrypt($id);
        $query_fkt = Trainingfkt::where('tahun_usulan', $tahun_usulan)->where('tipe','pti')->where('id_checker', $user->employee_id)->whereNotNull('date_penilai')->where('status','VERIFIED')->get()->unique('kode_judul');
        return view('pages.profile.training.pti.form-fkt-approve', compact('user','tahun_usulan','query_fkt'));
    }

    public function profile_training_fkt_pti_approved_store(Request $request){
        DB::beginTransaction();
        try {

            $user = auth()->user();
            $query = Trainingfkt::where('kode_judul', decrypt($request->kode_judul))->first();
            $post = Trainingfkt::where('kode_judul', decrypt($request->kode_judul))->update([
                'date_checker' => date('Y-m-d H:i:s'),
                'status' => 2
            ]);
            // if($query->kategori == 'free'){
            //     $post = Trainingfkt::where('kode_judul', decrypt($request->kode_judul))->update([
            //         'date_checker' => date('Y-m-d H:i:s'),
            //         'status' => 2
            //     ]);
            //     //send email to pemohon
            //     if(!empty($query->pemohon->email)){
            //         $qry_user = User::where('employee_id', $query->id_pemohon)->first();
            //         if($qry_user->roles()->pluck('id')->first() == 4 || $qry_user->roles()->pluck('id')->first() == 38 || $qry_user->roles()->pluck('id')->first() == 39 || $qry_user->roles()->pluck('id')->first() == 40 || $qry_user->roles()->pluck('id')->first() == 41){
            //             $details = [
            //                 'greeting' => 'Hi '.$query->pemohon->fullname,
            //                 'subject' => 'Formulir Penilaian Kebutuhan Training',
            //                 'body' => 'Ingin Menginformasikan bahwa untuk melengkapi usulan topik training "'.$query->judul.'" perlu mengisi formulir penilaian kebutuhan training',
            //                 'actionText' => 'Silahkan Login',
            //                 'actionURL' => url('/employee/training/fpkt/pti/'.encrypt($query->id).'/form'),
            //                 'thanks' => 'Terimakasih atas perhatiannya!!'
            //             ];
            //         }else{
            //             $details = [
            //                 'greeting' => 'Hi '.$query->pemohon->fullname,
            //                 'subject' => 'Formulir Penilaian Kebutuhan Training',
            //                 'body' => 'Ingin Menginformasikan bahwa untuk melengkapi usulan topik training "'.$query->judul.'" perlu mengisi formulir penilaian kebutuhan training',
            //                 'actionText' => 'Silahkan Login',
            //                 'actionURL' => url('/mytrainingfpkt/pti/'.encrypt($query->id).'/form'),
            //                 'thanks' => 'Terimakasih atas perhatiannya!!'
            //             ];
            //         }
            //         //send mail
            //         $qry_user->notify(new AccountNotification($details));
            //     }
            //     //send email to pic hrd
            //     // $users = User::whereHas(
            //     //     'roles', function($q){
            //     //         $q->where('id', 2);
            //     //     }
            //     // )->get();
            //     // if($users->isNotEmpty()){
            //     //     foreach($users as $key_user){
            //     //         if(!empty($key_user->email)){
            //     //             $qry_user = User::where('employee_id', $key_user->employee_id)->first();
            //     //             $details = [
            //     //                 'greeting' => 'Hi '.$qry_user->name,
            //     //                 'subject' => 'Scheduling Training (PTI)',
            //     //                 'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$query->judul.'" yang sudah disetujui dan perlu dijadwalkan',
            //     //                 'actionText' => 'Silahkan Login',
            //     //                 'actionURL' => url('/hrd/training/pti'),
            //     //                 'thanks' => 'Terimakasih atas perhatiannya!!'
            //     //             ];
            //     //             //send mail
            //     //             $qry_user->notify(new AccountNotification($details));
            //     //         }
            //     //     }
            //     // }
            // }else{
            //     if($query->pemohon->department->approval_code == 2){
            //         $post = Trainingfkt::where('kode_judul', decrypt($request->kode_judul))->update([
            //             'date_checker' => date('Y-m-d H:i:s'),
            //             'status' => 5
            //         ]);
            //         //send email to mr. sakurai
            //         $users = User::whereHas(
            //             'roles', function($q){
            //                 $q->where('id', 49);
            //             }
            //         )->get();
            //         if($users->isNotEmpty()){
            //             foreach($users as $key_user){
            //                 if(!empty($key_user->email)){
            //                     $qry_user = User::where('employee_id', $key_user->employee_id)->first();
            //                     $details = [
            //                         'greeting' => 'Hi '.$qry_user->name,
            //                         'subject' => 'Approval Training (PTI)',
            //                         'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$query->judul.'" yang memerlukan persetujuan anda',
            //                         'actionText' => 'Silahkan Login',
            //                         'actionURL' => url('/hrd/training/pti'),
            //                         'thanks' => 'Terimakasih atas perhatiannya!!'
            //                     ];
            //                     //send mail
            //                     $qry_user->notify(new AccountNotification($details));
            //                 }
            //             }
            //         }
            //     }else{
            //         $post = Trainingfkt::where('kode_judul', decrypt($request->kode_judul))->update([
            //             'date_checker' => date('Y-m-d H:i:s'),
            //             'status' => 4
            //         ]);
            //         //send email to mr. mizukami
            //         $users = User::whereHas(
            //             'roles', function($q){
            //                 $q->where('id', 51);
            //             }
            //         )->get();
            //         if($users->isNotEmpty()){
            //             foreach($users as $key_user){
            //                 if(!empty($key_user->email)){
            //                     $qry_user = User::where('employee_id', $key_user->employee_id)->first();
            //                     $details = [
            //                         'greeting' => 'Hi '.$qry_user->name,
            //                         'subject' => 'Approval Training (PTI)',
            //                         'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$query->judul.'" yang memerlukan persetujuan anda',
            //                         'actionText' => 'Silahkan Login',
            //                         'actionURL' => url('/hrd/training/pti'),
            //                         'thanks' => 'Terimakasih atas perhatiannya!!'
            //                     ];
            //                     //send mail
            //                     $qry_user->notify(new AccountNotification($details));
            //                 }
            //             }
            //         }
            //     }
            // }
            //atasan departemen ttd
            $date_qr = date('Ymd');
            $insert_approved_qr = new Qrcodefkt;
            $insert_approved_qr->kode_fkt = $query->kode;
            $insert_approved_qr->qr = $date_qr.$user->employee_id;
            $insert_approved_qr->date_approval = date('Y-m-d H:i:s');
            $insert_approved_qr->type = 5;
            $insert_approved_qr->save();
    
            //update log user activity
            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'approved';
            $insert->description = 'Approved formulir kebutuhan training dengan nama pemohon'.'"'.$query->pemohon->fullname.'" tujuan "Program Training Insidentil (PTI)"';
            $insert->save();

            DB::commit();

            return response()->json(['message' => "$query->judul has been approved"], 200);
        }catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    public function profile_training_fkt_form_pti(Request $request){
        $user = auth()->user();
        $year_now = date('Y');
        $next_year = $year_now+1;
        
        $employees = Employee::whereNot('status', 'TERMINATED')->get();
        $vendors = Vendor::where('tipe','training')->get();
        return view('pages.profile.training.pti.form-fkt', compact('user','year_now','employees','vendors','year_now','next_year'));
    }

    public function profile_training_fkt_pti_store(Request $request){
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
            if(!empty($request->no_urut)){
                $data = $request->input();
                $query = Trainingfkt::whereMonth('date_pemohon', date('m'))->whereYear('date_pemohon', date('Y'))->where('tipe','pti')->latest('date_pemohon')->first();
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
                    $ptt = 'PTI';
                    $bulan = $month_name[$month_now];
                    $tahun = $year_now;
                    $kode = $no.'/'.$fkt.'/'.$ptt.'/'.$bulan.'/'.$tahun;
                }

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
                                'kode' => $kode,
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
                                'kode' => $kode,
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
                $insert = new Log;
                $insert->user_id = $user->id;
                $insert->ip_address = $request->ip();
                $insert->action = 'insert';
                $insert->description = 'Draft new formulir kebutuhan training dengan nama pemohon'.'"'.$data['nama_pemohon'].'" tujuan "Program Training Insidentil (PTI)"';
                $insert->save();
            }

            return redirect(route('profile.training'))->with('tab_pti','open tab')->with('status','Draft Formulir Kebutuhan Training has been created');
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
            if(!empty($request->no_urut)){
                $data = $request->input();
                $query = Trainingfkt::whereMonth('date_pemohon', date('m'))->whereYear('date_pemohon', date('Y'))->where('tipe','pti')->latest('date_pemohon')->first();
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
                    $ptt = 'PTI';
                    $bulan = $month_name[$month_now];
                    $tahun = $year_now;
                    $kode = $no.'/'.$fkt.'/'.$ptt.'/'.$bulan.'/'.$tahun;
                }
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
                                'kode' => $kode,
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
                                'kode' => $kode,
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
                $insert->description = 'Propose new formulir kebutuhan training dengan nama pemohon'.'"'.$data['nama_pemohon'].'" tujuan "Program Training Insidentil (PTI)"';
                $insert->save();
            }

            // return redirect(route('profile.training.fkt.pti.detail', encrypt($kode)))->with('status','Proposed Formulir Kebutuhan Training has been created');
            return redirect(route('profile.training'))->with('tab_pti','open tab')->with('status','Formulir Kebutuhan Training '.$nama_pemohon.' has been updated');
        }
    }

    public function profile_training_fkt_edit_pti(Request $request, $id){
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
        return view('pages.profile.training.pti.edit-fkt', compact('user','min','max','year_now','employees','vendors','training_fkt','data_all'));
    }

    public function profile_training_fkt_pti_update(Request $request){
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

            return redirect(route('profile.training'))->with('tab_pti','open tab')->with('status','Draft Formulir Kebutuhan Training has been updated');
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

            // return redirect(route('profile.training.fkt.pti.detail', encrypt($data['kode'])))->with('status','Formulir Kebutuhan Training '.$nama_pemohon.' has been updated');
            return redirect(route('profile.training'))->with('tab_pti','open tab')->with('status','Formulir Kebutuhan Training '.$nama_pemohon.' has been updated');
        }
    }

    public function profile_training_fkt_pti_pdf($id){
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

            $link_qr_pemohon = route('profile.training.qrcode.pemohon', ['code' => $pemohon_qr, 'id' => $pemohon_kode_qr]);
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

            $link_qr_checker = route('profile.training.qrcode.checker', ['code' => $checker_qr, 'id' => $checker_kode_qr]);
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

            $link_qr_verified = route('profile.training.qrcode.verified', ['code' => $verified_qr, 'id' => $verified_kode_qr]);
        }else{
            $verified_qr = null;
            $verified_kode_qr = null;

            $link_qr_verified = '';
        }

        $qr_4 =  $all_qrcode->whereStrict('type', 4)->first();
        if(!empty($qr_4)){
            $approval_qr = $qr_4->qr;
            $approval_kode_qr = str_replace("/","-",$qr_4->kode_fkt);

            $link_qr_approval = route('profile.training.qrcode.approval', ['code' => $approval_qr, 'id' => $approval_kode_qr]);
        }else{
            $approval_qr = null;
            $approval_kode_qr = null;

            $link_qr_approval = '';
        }

        $arr_fkt = Trainingfkt::where('kode', decrypt($id))->get();
        $data = [
            'title' => 'FORMULIR KEBUTUHAN PELATIHAN',
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
        $pdf = PDF::loadView('pages.profile.fkt', $data)->setPaper('a4', 'landscape');
        $pdf->set_option("isPhpEnabled", true);
        return $pdf->stream('FORMULIR KEBUTUHAN TRAINING - '.$fkt->pemohon->fullname.'.pdf');
    }

    public function profile_back_fkt_pti(Request $request){
        return redirect(route('profile.training'))->with('tab_pti','open tab');
    }
    public function profile_back_fkt_approve_pti(Request $request){
        return redirect(route('profile.training'))->with('tab_approve_pti','open tab');
    }
    //End training fkt pti

    //Start training fpkt pti
    public function profile_training_fpkt_pti(Request $request, $id){
        $user = auth()->user();
        $kode = $id;
        $id = decrypt($id);
        $fkt = Trainingfkt::where('id', $id)->first();
        $fpkt = Trainingfpkt::where('id_fkt', $id)->first(); 
        $arr_fpkt = Trainingfpkt::where('id_fkt', $id)->get(); 
        $cek_pemohon = Trainingfkt::where('id', $id)->where('id_pemohon', $user->employee_id)->first(); 
        $cek_peserta = Trainingfkt::where('id', $id)->where('id_peserta', $user->employee_id)->first(); 
        $cek_atasan = Trainingfkt::where('id', $id)->where('id_penilai', $user->employee_id)->first(); 
        return view('pages.profile.training.pti.form-fpkt', compact('kode','user','fkt','fpkt','cek_pemohon','cek_peserta','cek_atasan','arr_fpkt'));
    }

    public function profile_training_fpkt_pti_store(Request $request){
        if($request->action == 'pemohon'){
            $user = auth()->user();
            $data = $request->input();
            $fkt = Trainingfkt::find($data['id_fkt']);

            // $code_random = random_int(100000, 999999);
            // $post_update = Trainingfkt::where('kode', $fkt->kode)->where('judul', $fkt->judul)->update([
            //     'kode_judul' => $code_random
            // ]);

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
                                'actionURL' => url('/employee/training/fpkt/pti/'.encrypt($fkt->id).'/form'),
                                'thanks' => 'Terimakasih atas perhatiannya!!'
                            ];
                        }else{
                            $details = [
                                'greeting' => 'Hi '.$fkt->penilai->fullname,
                                'subject' => 'Penilaian Kebutuhan Training',
                                'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$fkt->judul.'" yang membutuhkan penilaian anda',
                                'actionText' => 'Silahkan Login',
                                'actionURL' => url('/mytrainingfpkt/pti/'.encrypt($fkt->id).'/form'),
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
                            $insert->id_atasan = $fkt->id_atasan;
                            $insert->date_atasan = $fkt->date_atasan;
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
                                    'actionURL' => url('/employee/training/fpkt/pti/'.encrypt($fkt->id).'/form'),
                                    'thanks' => 'Terimakasih atas perhatiannya!!'
                                ];
                            }else{
                                $details = [
                                    'greeting' => 'Hi '.$fkt->peserta->fullname,
                                    'subject' => 'Penilaian Kebutuhan Training',
                                    'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$fkt->judul.'" yang membutuhkan penilaian anda',
                                    'actionText' => 'Silahkan Login',
                                    'actionURL' => url('/mytrainingfpkt/pti/'.encrypt($fkt->id).'/form'),
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
                                    'actionURL' => url('/employee/training/fpkt/pti/'.encrypt($fkt->id).'/form'),
                                    'thanks' => 'Terimakasih atas perhatiannya!!'
                                ];
                            }else{
                                $details = [
                                    'greeting' => 'Hi '.$fkt->peserta->fullname,
                                    'subject' => 'Penilaian Kebutuhan Training',
                                    'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$fkt->judul.'" yang membutuhkan penilaian anda',
                                    'actionText' => 'Silahkan Login',
                                    'actionURL' => url('/mytrainingfpkt/pti/'.encrypt($fkt->id).'/form'),
                                    'thanks' => 'Terimakasih atas perhatiannya!!'
                                ];
                            }
                            //send mail
                            $qry_user->notify(new AccountNotification($details));
                        }
    
                        //notification atasan penilai
                        if(!empty($fkt->penilai->email)){
                            $qry_user = User::where('employee_id', $fkt->id_penilai)->first();
                            $details = [
                                'greeting' => 'Hi '.$fkt->penilai->fullname,
                                'subject' => 'Penilaian Kebutuhan Training',
                                'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$fkt->judul.'" yang membutuhkan penilaian anda',
                                'actionText' => 'Silahkan Login',
                                'actionURL' => url('/mytrainingfpkt/pti/'.encrypt($fkt->id).'/form'),
                                'thanks' => 'Terimakasih atas perhatiannya!!'
                            ];
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
                
                return redirect(route('profile.training.fkt.pti.detail', encrypt($fkt->kode)))->with('status','Formulir Penilaian Kebutuhan Training '.$fkt->pemohon->fullname.' has been updated');
            }else{
                return redirect(route('profile.training.fkt.pti.detail', encrypt($fkt->kode)))->with('error','Formulir Penilaian Kebutuhan Training no changes');
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
                        $insert->id_atasan = $fkt->id_atasan;
                        $insert->date_atasan = $fkt->date_atasan;
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
                                    'subject' => 'Verification Training (PTI)',
                                    'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$fkt->judul.'" yang membutuhkan verification anda',
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

                //insert log user activity
                $insert_log = new Log;
                $insert_log->user_id = $user->id;
                $insert_log->ip_address = $request->ip();
                $insert_log->action = 'update';
                $insert_log->description = 'Modify "'.$fkt->judul.'" dengan nama pemohon'.'"'.$fkt->pemohon->fullname.'"';
                $insert_log->save();

                return redirect(route('profile.training.fkt.pti.detail', encrypt($fkt->kode)))->with('status','Formulir Penilaian Kebutuhan Training '.$fkt->pemohon->fullname.' has been updated');
            }else{
                return redirect(route('profile.training.fkt.pti.detail', encrypt($fkt->kode)))->with('error','Formulir Penilaian Kebutuhan Training no changes');
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
                                    'subject' => 'Penilaian Kebutuhan Training',
                                    'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$fkt->judul.'" yang membutuhkan approval anda',
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
                
                //insert log user activity
                $insert_log = new Log;
                $insert_log->user_id = $user->id;
                $insert_log->ip_address = $request->ip();
                $insert_log->action = 'approved';
                $insert_log->description = 'Approved "'.$fkt->judul.'" dengan nama pemohon'.'"'.$fkt->pemohon->fullname.'"';
                $insert_log->save();

                return redirect(route('profile.training.fkt.pti.detail', encrypt($fkt->kode)))->with('status','Formulir Penilaian Kebutuhan Training '.$fkt->pemohon->fullname.' has been updated');
            }else{
                return redirect(route('profile.training.fkt.pti.detail', encrypt($fkt->kode)))->with('error','Formulir Penilaian Kebutuhan Training no changes');
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
            // $code_random = random_int(100000, 999999);            
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
                            // 'kode_judul' => $code_random,
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
    
                        //ttd atasan
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
                //cek notification atasan penilai and pic hrd
                if($sum_peserta > 0){
                    if($sum_atasan > 0){
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
                                        'subject' => 'Penilaian Kebutuhan Training',
                                        'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$judul[0].'" yang membutuhkan approval anda',
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
                        //notification atasan penilai
                        foreach($fkt as $key_fkt){
                            if(!empty($key_fkt->penilai->email)){
                                $qry_user = User::where('employee_id', $key_fkt->id_penilai)->first();
                                $details = [
                                    'greeting' => 'Hi '.$key_fkt->penilai->fullname,
                                    'subject' => 'Penilaian Kebutuhan Training',
                                    'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$key_fkt->judul.'" yang membutuhkan penilaian anda',
                                    'actionText' => 'Silahkan Login',
                                    'actionURL' => url('/mytrainingfpkt/pti/'.encrypt($key_fkt->id).'/form'),
                                    'thanks' => 'Terimakasih atas perhatiannya!!'
                                ];
                                //send mail
                                $qry_user->notify(new AccountNotification($details));
                            }
                        }
                    } 
                }else{
                    if($sum_atasan > 0){
                        foreach($fkt as $key_fkt){
                            //notification peserta
                            if(!empty($key_fkt->peserta->email)){
                                $qry_user = User::where('employee_id', $key_fkt->id_peserta)->first();
                                $details = [
                                    'greeting' => 'Hi '.$key_fkt->peserta->fullname,
                                    'subject' => 'Penilaian Kebutuhan Training',
                                    'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$key_fkt->judul.'" yang membutuhkan penilaian anda',
                                    'actionText' => 'Silahkan Login',
                                    'actionURL' => url('/mytrainingfpkt/pti/'.encrypt($key_fkt->id).'/form'),
                                    'thanks' => 'Terimakasih atas perhatiannya!!'
                                ];
                                //send mail
                                $qry_user->notify(new AccountNotification($details));
                            }
                        }
                    }else{
                        foreach($fkt as $key_fkt){
                            //notification peserta
                            if(!empty($key_fkt->peserta->email)){
                                $qry_user = User::where('employee_id', $key_fkt->id_peserta)->first();
                                $details = [
                                    'greeting' => 'Hi '.$key_fkt->peserta->fullname,
                                    'subject' => 'Penilaian Kebutuhan Training',
                                    'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$key_fkt->judul.'" yang membutuhkan penilaian anda',
                                    'actionText' => 'Silahkan Login',
                                    'actionURL' => url('/mytrainingfpkt/pti/'.encrypt($key_fkt->id).'/form'),
                                    'thanks' => 'Terimakasih atas perhatiannya!!'
                                ];
                                //send mail
                                $qry_user->notify(new AccountNotification($details));
                            }
        
                            //notification atasan penilai
                            if(!empty($key_fkt->penilai->email)){
                                $qry_user = User::where('employee_id', $key_fkt->id_penilai)->first();
                                $details = [
                                    'greeting' => 'Hi '.$key_fkt->penilai->fullname,
                                    'subject' => 'Penilaian Kebutuhan Training',
                                    'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$key_fkt->judul.'" yang membutuhkan penilaian anda',
                                    'actionText' => 'Silahkan Login',
                                    'actionURL' => url('/mytrainingfpkt/pti/'.encrypt($key_fkt->id).'/form'),
                                    'thanks' => 'Terimakasih atas perhatiannya!!'
                                ];
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
                
                return redirect(route('profile.training.fkt.pti.detail', encrypt($kode[0])))->with('status','Formulir Penilaian Kebutuhan Training '.$emp->fullname.' has been updated');
            }else{
                return redirect(route('profile.training.fkt.pti.detail', encrypt($kode[0])))->with('error','Formulir Penilaian Kebutuhan Training no changes');
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

                        $sum_atasan = array_sum(array_column($arr_atasan,'0'));
                        if($sum_atasan > 0){
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
                                            'subject' => 'Penilaian Kebutuhan Training',
                                            'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$qry_fkt->judul.'" yang membutuhkan approval anda',
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
                
                return redirect(route('profile.training.fkt.pti.detail', encrypt($kode[0])))->with('status','Formulir Penilaian Kebutuhan Training '.$emp->fullname.' has been updated');
            }else{
                return redirect(route('profile.training.fkt.pti.detail', encrypt($kode[0])))->with('error','Formulir Penilaian Kebutuhan Training no changes');
            }
        } 
    }

    public function profile_training_fpkt_pti_pdf($id){
        $arr_fkt = Trainingfkt::where('kode_judul', decrypt($id))->whereNotNull('date_peserta')->get();
        $html = '';
        foreach($arr_fkt as $fkt){
            $fpkt = Trainingfpkt::where('id_fkt', $fkt->id)->get();
            if($fpkt->isNotEmpty()){
                //ttd peserta
                $qr_1 = Qrcodefpkt::where('id_fkt', $fkt->id)->where('type', 1)->first();
                if(!empty($qr_1)){
                    $peserta_qr = $qr_1->qr;
                    $peserta_fkt_id = $qr_1->id_fkt;
                    $link_qr_peserta = route('profile.training.qrcode.fpkt', ['code' => $peserta_qr, 'id' => $peserta_fkt_id]);
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
                    $link_qr_atasan = route('profile.training.qrcode.fpkt', ['code' => $atasan_qr, 'id' => $atasan_fkt_id]);
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
                    $link_qr_hrd = route('profile.training.qrcode.fpkt', ['code' => $hrd_qr, 'id' => $hrd_fkt_id]);
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
                    'title' => 'Formulir Penilaian Kebutuhan Training',
                    'fkt' => $fkt,
                    'fpkt' => $fpkt,
                    'skor' => $skor,
                    'link_qr_peserta' => $link_qr_peserta,
                    'link_qr_atasan' => $link_qr_atasan,
                    'link_qr_hrd' => $link_qr_hrd
                ];
                $view = view('pages.profile.fpkt')->with(compact('data'));
                $html .= $view->render();
            }
        }
        $pdf = PDF::set_option("isPhpEnabled", false);
        $pdf->loadHTML($html);
        $pdf->setPaper('a4', 'landscape');
        return $pdf->stream('FORMULIR PENILAIAN KEBUTUHAN TRAINING.pdf');
    }

    public function profile_training_fpkt_pti_print($id){
        // dd(decrypt($id));
        $fkt = Trainingfkt::where('id', 23)->first();
        $html = '';
        $fpkt = Trainingfpkt::where('id_fkt', $fkt->id)->get();
        if($fpkt->isNotEmpty()){
            //ttd peserta
            $qr_1 = Qrcodefpkt::where('id_fkt', $fkt->id)->where('type', 1)->first();
            if(!empty($qr_1)){
                $peserta_qr = $qr_1->qr;
                $peserta_fkt_id = $qr_1->id_fkt;
                $link_qr_peserta = route('profile.training.qrcode.fpkt', ['code' => $peserta_qr, 'id' => $peserta_fkt_id]);
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
                $link_qr_atasan = route('profile.training.qrcode.fpkt', ['code' => $atasan_qr, 'id' => $atasan_fkt_id]);
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
                $link_qr_hrd = route('profile.training.qrcode.fpkt', ['code' => $hrd_qr, 'id' => $hrd_fkt_id]);
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
                'title' => 'Formulir Penilaian Kebutuhan Training',
                'fkt' => $fkt,
                'fpkt' => $fpkt,
                'skor' => $skor,
                'link_qr_peserta' => $link_qr_peserta,
                'link_qr_atasan' => $link_qr_atasan,
                'link_qr_hrd' => $link_qr_hrd
            ];
            $view = view('pages.profile.fpkt')->with(compact('data'));
            $html .= $view->render();
        }else{
            $data = [
                'title' => 'Formulir Penilaian Kebutuhan Training',
                'fkt' => $fkt,
                'fpkt' => '',
                'skor' => '',
                'link_qr_peserta' => '',
                'link_qr_atasan' => '',
                'link_qr_hrd' => ''
            ];
            $view = view('pages.profile.fpkt')->with(compact('data'));
            $html .= $view->render();
        }
        $pdf = PDF::set_option("isPhpEnabled", false);
        $pdf->loadHTML($html);
        $pdf->setPaper('a4', 'landscape');
        return $pdf->stream('FORMULIR PENILAIAN KEBUTUHAN TRAINING.pdf');
    }
    //End training fpkt pti

    //Start training collective pti
    public function profile_training_collective_pti(Request $request, $id){
        $user = auth()->user();
        $fkt = Trainingfkt::where('kode',decrypt($id))->where('judul', $request->pelatihan)->first();
        $qry_fkt = Trainingfkt::where('kode',decrypt($id))->where('judul', $request->pelatihan)->get();
        $arr_peserta = Employee::whereIn('id', $qry_fkt->pluck('id_peserta'))->get();
        return view('pages.profile.training.pti.form-collective', compact('user','fkt','qry_fkt','arr_peserta'));
    }
    public function profile_training_collective_approve_pti(Request $request, $id){
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

        return view('pages.profile.training.pti.form-collective-approve', compact('kode','user','fkt','fpkt','cek_pemohon','cek_peserta','cek_atasan','arr_fpkt','qry_fkt','arr_peserta'));
    }
    //End training collective pti
}
