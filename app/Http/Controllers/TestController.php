<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use App\Notifications\AccountNotification;
use App\Models\Trainingrecord;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Medical;
use App\Models\Lab;
use App\Models\Section;
use App\Models\Position;
use App\Models\Level;
use App\Models\User;
use App\Models\Booking;
use App\Models\Trainingfkt;
use App\Models\Trainingfpkt;
use App\Models\Clinic\Patient;
use App\Models\Master\Doctoraccount;
use App\Models\Clinic\Trmasuk;
use App\Models\Clinic\Trkeluar;
use App\Models\Clinic\Prestock;
use App\Models\Master\Drug;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Carbon\CarbonInterval;
use Auth;
use Schema;
use App\Models\Permissioninternalrule;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TestController extends Controller
{
    public function index(Request $request){
        // $fkt = Trainingfkt::whereNotIn('id', function($fkt) {
        //     $fkt->select('id_fkt')->from('training_fpkt');
        // })->where('dept_pemohon', 2)->where('kode_judul', 25475373)->where('status', 6)->get()->pluck('id')->toArray();
        // dd($fkt);
        // return redirect(route('training.emp.index'))->with('tab_ptt','open tab');
        // $user = User::where('id',542)->first();
        // $user->syncRoles(48);
        dd('selesai');
        $users = User::whereHas(
            'roles', function($q){
                $q->where('id', 49);
            }
        )->first();
        dd($users);
        $user = auth()->user();
        $fkt = Trainingfkt::find(1);
        $qry_user = User::where('employee_id', $fkt->id_checker)->first();
        $details = [
            'greeting' => 'Hi '.$fkt->checker->fullname,
            'subject' => 'PROGRAM TRAINING INSIDENTIL',
            'body' => 'Ingin Menginformasikan bahwa ada usulan topik training "'.$fkt->judul.'" yang membutuhkan approval anda',
            'actionText' => 'Silahkan Login',
            'actionURL' => route('profile.back.fkt.approve.pti'),
            'thanks' => 'Terimakasih atas perhatiannya!!'
        ];
        //send mail
        $qry_user->notify(new AccountNotification($details));
        dd('test');
        dd($user->roles()->pluck('id')->first());
        $query = Department::get();
        dd($query);
        $query = Trainingfkt::where('kode_judul', '225431')->first();
        dd($query->pemohon->department->name);
        $user = auth()->user();
        $cek_approve_checker = Trainingfkt::where('id_checker', $user->employee_id)
            ->where('tipe', 'ptt')->whereNull('date_checker')->get();
        $cek_approve_penilai = Trainingfkt::where('id_penilai', $user->employee_id)
            ->where('tipe', 'ptt')->whereNull('date_penilai')->count();
        
        // $cek_jml_approve = $cek_approve_checker+$cek_approve_penilai;
        dd($cek_approve_checker);
        $users = User::whereHas(
            'roles', function($q){
                $q->where('name', 'HRD');
            }
        )->get();
        dd($users);
        // $user = auth()->user();
        // $qry_user = User::where('employee_id', $user->employee_id)->first();
        // $details = [
        //     'greeting' => 'Hi test',
        //     'subject' => 'Laporan Pelaksanaan Training',
        //     'body' => 'Ingin Menginformasikan bahwa ada laporan pelaksanaan training topik yang membutuhkan approval anda',
        //     'actionText' => 'Silahkan Login',
        //     'actionURL' => url('/mytraining'),
        //     'thanks' => 'Terimakasih atas perhatiannya!!'
        // ];
        // //send mail
        // $qry_user->notify(new AccountNotification($details));
        // dd('done');
        // $query = Trainingrecord::where(function ($query) use ($user){
        //     $query->where(['ttd_presiden'=>$user->employee_id, 'tgl_ttd_presiden'=>null])
        //         ->orWhere(function ($query) use ($user) {
        //             $query->where('ttd_direktur', '=', $user->employee_id)
        //                 ->where('tgl_ttd_direktur', '=', null);
        //         })
        //         ->orWhere(function ($query) use ($user) {
        //             $query->where('ttd_general_manager', '=', $user->employee_id)
        //                 ->where('tgl_ttd_general_manager', '=', null);
        //         })
        //         ->orWhere(function ($query) use ($user) {
        //             $query->where('ttd_manager', '=', $user->employee_id)
        //                 ->where('tgl_ttd_manager', '=', null);
        //         })
        //         ->orWhere(function ($query) use ($user) {
        //             $query->where('ttd_atasan', '=', $user->employee_id)
        //                 ->where('tgl_ttd_atasan', '=', null);
        //         })
        //         ->orWhere(function ($query) use ($user) {
        //             $query->where('ttd_hrd_ga_gm', '=', $user->employee_id)
        //                 ->where('tgl_ttd_hrd_ga_gm', '=', null);
        //         })
        //         ->orWhere(function ($query) use ($user) {
        //             $query->where('ttd_pic', '=', $user->employee_id)
        //                 ->where('tgl_ttd_pic', '=', null);
        //         });
        // })->get();
        // dd($query);
        // $code_random = random_int(100000, 999999);
        // $post = Doctoraccount::insert([
        //     'id_dokter' => $code_random,
        //     'nama' => 'dr. M. Praja Pratama'
        // ]);

        // $patients = Patient::get();
        // foreach($patients as $patient){
        //         $post = patient::where('id', $patient->id)->update([
        //             'created_at' => '2024-09-25 16:29:26',
        //             'updated_at' => '2024-09-25 16:29:26'
        //         ]);
        // }

        dd('selesai');
        
        $start_date = '2024-12-02 09:30:00';
        $end_date = '2024-12-02 13:00:00';
        $query = Booking::where('room_id', 1)
            ->whereDate('date_start', date('Y-m-d', strtotime($start_date)))
            ->whereDate('date_end', date('Y-m-d', strtotime($end_date)))
            ->orderBy('date_start','asc')
            ->get();
        $iteration = 0;
        foreach($query as $qry){            
            // if($start_date >= $qry->date_start){
            //     if($start_date <= $qry->date_end){
            //         dd('conflict');
            //     }else{
            //         dd('tidak conflict');
            //     }
            // }else{
            //     if($end_date < $qry->date_start){
            //         dd('tidak conflict 2');
            //     }else{
            //         dd('conflict 2');
            //     }
            // }
            $iteration++ ; 
            if($iteration != count($query)) {
                // at last loop, code here
                $test = 'awal';
            }else{
                $test = 'terakhir';
            }

            $all[] = $test;
        }
        dd($all);
        $query = Permissioninternalrule::with('internalrule','area')->where('benefit', 'Tunjangan Kendaraan (Non Tax)')->get();
        dd($query);
        $query = Trainingfkt::where('status', 'FINISHED')->get()->unique('kode_judul');
        dd($query);
        $string = 'LARAVEL KITA';
	    $ucfirstString = Str::lower($string);
        $test = ucwords($ucfirstString);
        dd($test);
        $query = Trainingfkt::where('kode', '001/FKT/V/24')->get();
        dd($query->sum('biaya_fkt'));
        $period = CarbonPeriod::create('2024-05-29', '2024-05-31');
        foreach ($period as $tanggal) {
            $tgl =  $tanggal->format('Y-m-d');

            $date_start = $tgl.' 08:00:00';
            $date_end = $tgl.' 14:00:00';
            $query  = Booking::where('room_id', 5)
                ->where('date_start', '>=', $date_start)
                ->where('date_start', '<', $date_end)->get();
            $total[] = $query->count();                   
        }
        if(array_sum($total) > 0){
            foreach ($period as $tanggal2) {
                $tgl2 =  $tanggal2->format('Y-m-d');
    
                $date_start2 = $tgl2.' 08:00:00';
                $date_end2 = $tgl2.' 14:00:00';
                $query2 = Booking::where('room_id', 5)
                ->where('date_start', '>=', $date_start2)
                ->where('date_start', '<', $date_end2)->get();
                if($query2->isNotEmpty()){
                    $merge = $query2->pluck('brief_description')->toArray();
                    $tampung[] = implode(",",$merge);
                }
            }
            $arr_query = implode(",",$tampung);
            dd($arr_query);
        }else{
            dd($total);
        }

        $query  = Booking::where('room_id', 5)->where('repeat_status', 'None')
            ->where('date_start', '>=', '2024-05-29 08:00:00')
            ->where('date_start', '<', '2024-05-29 16:30:00')->get();
            $arr_query = $query->pluck('brief_description')->toArray();
            $merge_arr = implode(",",$arr_query);
        dd($merge_arr);
        $start_date = '28-05-2024';

        $end_date = '28-07-2024';

        $service_date = 31;

        $period = CarbonPeriod::create($start_date, '1 month' , $end_date);

        foreach ($period as $date) {
            if($date->day($service_date)->format('d') == $service_date){
                $dates = $date->day($service_date)->format('Y-m-d');
            }else{
                $dates = $date->subMonth()->endOfMonth()->format('Y-m-d');
            }
            $test[] = $dates;
        }

        dd($test);
        // $period = Carbon::parse('2024-05-01')->daysUntil('2024-07-31', 31);
        // foreach($period as $date){
        //     $tgl_period[] = $date->format('Y-m-d');
        // }
        // $startDate=Carbon::now()->firstOfMonth()->addDays(30)->format('Y-m-d');
        // dd($startDate);
        // $period = CarbonPeriod::create('2024-05-01', '2024-07-31');
        // foreach($period as $date){
        //     if($date->format('d') == "31"){
        //         $tgl_period[] = $date->format('Y-m-d');
        //     }else{
        //         $tgl_period[] = $date->endOfMonth()->format('Y-m-d');
        //     }
        // }
        // dd($tgl_period);
        // $period = CarbonPeriod::between('2024-05-27', '2024-06-24')->filter(function (Carbon $date) {
        //     return $date->isMonday() || $date->isWednesday();
        // });
        // collect($period->map(function (Carbon $date) {
        //     return $date->format('Y-m-d');
        // }))->dd();        
        $startDate = Carbon::parse('2024-05-01');
        $endDate = Carbon::parse('2024-07-31');
        $separation_count = 3;
        $day_of_month = 20;

        $datePeriod = CarbonPeriod::create($startDate, $endDate)
            ->settings(['monthOverflow' => false]);
        foreach($datePeriod as $dates2){
            $dateInterval = CarbonInterval::months($separation_count);
    
            if ($day_of_month) {
                $dates2->setStartDate(
                    $dates2->copy()->setDay($day_of_month)
                );
                $dayFilter = function (Carbon $date) use ($day_of_month) {
                    return $date->day === $day_of_month;
                };
                $dates2->addFilter($dayFilter);
            }
            $date3 = $dates2->setDateInterval($dateInterval);
            $tgl_period[] = $date3->format('Y-m-d');
        }
        dd($tgl_period);
        // foreach($datePeriod as $date){
        //     $tgl_period[] = $date->toDateString();
        // }
        $separation_count = 3;
        $days = 31;
        foreach (CarbonPeriod::create('2024-05-01', CarbonInterval::months($separation_count), '2024-07-31', CarbonPeriod::IMMUTABLE) as $baseDate) {
            foreach ($days as $dayName) {
                $date = $baseDate->is($dayName) ? $baseDate : $baseDate->next($dayName);
                $test[] = Carbon::create($date)->format('Y-m-d');
            }
            // $test[] = $baseDate;
        }
        dd($test);
        // dd($period);
        dd('maintenance');
        // $cek_file = storage_path('app/public/calendar/2024.pdf');
        //         if (File::exists($cek_file)) {
        //             File::delete($cek_file);
        //         }
        //         dd('true');
        // return view('flippdf');
        // $qry = Medical::where('lampiran_mcu', null)->whereYear('tanggal_mcu', '2023')->get()->pluck('id_employees');
        // $employee = Employee::whereIn('id', $qry)->get()->pluck('fullname');
        // dd($employee);
        // $date = "2012-02-16";
        // $newdate = strtotime ( '-1 day' , strtotime ( $date ) ) ;
        // $newdate = date('Y-m-d', $newdate);
        // dd($newdate);
        // dd('maintenance');
        // // $query = Permissioninternalrule::where('id_internal_rule', 2)->get();
        // // foreach($query as $qry){
        // //     $arr_dept[] = $qry->id_dept;
        // //     $uniq_dept = array_unique($arr_dept);
        // //     foreach($uniq_dept as $key =>$val){
        // //         if($qry->id_dept == $val){
        // //             $data[$qry->id_dept][] = $qry->id_level;
        // //         }
        // //     }
        // // }
        // // dd($data);
        // $arr1 = array("1985006", "1988001", "1990001", "1992003", "1993001", "1993002", "1993004", "1994007", "1994008", "1994009", "1995001", "1996002", "1996003", "1996004", "1997007", "1997008", "1997009", "1997010", "1998001", "2000001", "2000003", "2000004", "2001001", "2001002", "2001004", "2001005", "2001006", "2001008", "2002001", "2002002", "2002003", "2002004", "2003002", "2003003", "2004001", "2005001", "2005002", "2005003", "2007001", "2007003", "2008001", "2008002", "2008003", "2008005", "2009002", "2009004", "2009005", "2009006", "2010002", "2010003", "2010005", "2010006", "2010007", "2010008", "2010009", "2010010", "2010011", "2010016", "2011003", "2011004", "2011005", "2011006", "2011007", "2011008", "2011009", "2011014", "2011015", "2011016", "2011017", "2012002", "2012003", "2012004", "2012005", "2012007", "2012008", "2012012", "2012014", "2012015", "2012016", "2012020", "2012021", "2012022", "2012027", "2012034", "2012035", "2012036", "2012037", "2012040", "2012041", "2012042", "2012044", "2013007", "2013010", "2013012", "2013013", "2013015", "2013017", "2013018", "2013021", "2013027", "2013028", "2013031", "2013037", "2014004", "2014005", "2014007", "2014011", "2014012", "2014018", "2014033", "2014035", "2014064", "2014065", "2015011", "2015014", "2015015", "2015017", "2015020", "2015021", "2015023", "2015026", "2015036", "2015041", "2015043", "2015046", "2015050", "2015055", "2015076", "2016001", "2016004", "2016010", "2016019", "2016026", "2016036", "2016037", "2016041", "2017013", "2017016", "2017018", "2017024", "2017027", "2017028", "2017033", "2017034", "2017035", "2017037", "2017038", "2017046", "2017115", "2017117", "2017118", "2017133", "2017135", "2017185", "2017186", "2017190", "2017194", "2017195", "2017196", "2017199", "2018001", "2018005", "2018010", "2018053", "2018060", "2018064", "2018069", "2018070", "2018072", "2018073", "2018075", "2019001", "2019003", "2019004", "2019005", "2019006", "2019007", "2019012", "2019015", "2019022", "2019025", "2019026", "2019027", "2019028", "2019029", "2019030", "2019031", "2019032", "2019033", "2019034", "2019035", "2019036", "2019037", "2019038", "2019039", "2019040", "2019043", "2019044", "2019045", "2019046", "2019047", "2019048", "2019052", "2019058", "2019063", "2020001", "2020002", "2020007", "2020009", "2020010", "2020011", "2020013", "2020014", "2020015", "2020016", "2020017", "2020018", "2020021", "2020022", "2020023", "2020025", "2020026", "2020027", "2020029", "2020030", "2020038", "2020042", "2020046", "2020047", "2020048", "2020049", "2020051", "2020053", "2020054", "2020056", "2020057", "2020058", "2020059", "2020060", "2020061", "2020062", "2020063", "2020065", "2020067", "2020069", "2020070", "2020071", "2020072", "2020073", "2020074", "2020076", "2020078", "2021001", "2021002", "2021003", "2021006", "2021007", "2021010", "2021011", "2021014", "2021016", "2021017", "2021018", "2021019", "2021020", "2021021", "2021023", "2021024", "2021025", "2021028", "2021029", "2021030", "2021031", "2021032", "2021033", "2021034", "2021036", "2021037", "2021038", "2021039", "2021042", "2021043", "2021044", "2021046", "2021049", "2021051", "2021053", "2021054", "2021055", "2021056", "2021057", "2021059", "2021061", "2021064", "2021065", "1002022", "2021073", "2021074", "2021075", "2021090", "2021067", "2022002", "2022003", "2022004", "2022005", "2022006", "2022007", "2022009", "2022011", "2022012", "2022013", "2022015", "2022017", "2022018", "2022019", "2022021", "2022020", "2022024", "2022025", "2022027", "2022031", "2022032", "2022033", "2022034", "2022035", "2022038", "2022036", "2022039", "2022040", "2022041", "2022042", "2022043", "2022044", "2022046", "2022047", "2022048", "2022049", "2022050", "2022052", "2022054", "2022055", "2022058", "2022059", "2022060", "2022061", "2022062", "2022064", "2022065", "2022066", "2022067", "2022068", "2022069", "2022072", "2022073", "2022074", "1102022", "2022075", "2022076", "2022077", "2022078", "2022079", "2022080", "2022081", "2022082", "2022083", "2022084", "2022085", "2022086", "2022087", "2022090", "2022091", "2022092", "2022093", "2022094", "2022095", "2022096", "2022097", "2022098", "2022099", "2022100", "2022101", "2022102", "2022103", "2022104", "2022106", "2022108", "2022109", "2022111", "2023001", "2023002", "2023003", "2023004", "2023005", "2023006", "2023007", "2023008", "2023009", "2023010", "2023011", "2023012", "2023014", "2023016", "2023017", "2023018", "2023019", "2023021", "2023026", "2023027", "2023029", "2023030", "2023034", "2023035", "2023036", "2023037", "2023038", "2023039", "2023040", "2023042", "2023043", "2023046", "2023047", "2023048", "2023051", "2023054", "2023060", "2023061", "2023063", "2023064", "2023065", "2023066", "2023067", "2023068", "BLS0123003", "BLS0323017", "BLS0123013", "BLS0123011", "BLS0323016", "2023070", "2023071", "2023072", "2023073", "2023074", "2023075", "2023076", "2023077", "2023078", "2023080", "2023081", "2023079", "2023082", "2023087", "2023088", "2023083", "2023084", "2023085", "2023086", "2023089", "2023090", "2023091", "2023092", "2023093", "2023094", "2023095", "2023096", "2023097", "2023098", "BLS0123001", "2023102", "2023101", "2023099", "BLS0123007", "2023100", "BLS0600002", "BLS0123009", "BLS0123010", "BLS0723019", "BLS0523018", "BLS0123012", "2023103", "2023104", "BLS0923020", "2023105", "2023106", "2023107", "2023108", "2023109", "BLS0123005", "2023111", "2023110", "BLS0123002", "BLS0123008", "2023112", "2023113", "2023114", "2023115", "2023116");
        // $arr_unique = array_unique($arr1);
        // $arr2 = array("Istiajenny@gmail.com", "chaerul@hisamitsu.co.id", "ruby@hisamitsu.co.id", "Solikah23101970@gmail.com", "mariyati.yati1972@gmail.com", "emylailatulf16@gmail.com", "mrutomo354@gmail.com", "samsudin@hisamitsu.co.id", "mastuahizzah@gmail.com", "umisalbiyah124@gmail.com", "rupiandjayasri@gmail.com", "yuniindahsetyowati13@gmail.com", "imroatulchanifah0808@gmail.com", "yonaaisyah23@gmail.com", "Karmiasih72@gmail.com", "nunukindah007@gmail.com", "rosetianah@gmail.com", "chusnahnimatul@gmail.com", "koordinatorgt.mks@gmail.com", "agungpoernomowibowo@gmail.com", "wahyu.tri@hisamitsu.co.id", "a06148727@gmail.com", "nadirwijaya221973@gmail.com", "muchlisk1975@gmail.com", "herry@hisamitsu.co.id", "wowon_salonpas@yahoo.co.id", "mamatbasori888@gmail.com", "agungariyanto70@yahoo.co.id", "aliwaras407@gmail.com", "sri.agustini.d77@gmail.com", "mashudi@hisamitsu.co.id", "giriretnowijayanti@gmail.com", "sasongko@hisamitsu.co.id", "ImasLubis7@gmail.com", "ary_k.cj2@hisamitsu.co.id", "ketutdarna19@gmail.com", "mujiharto@hisamitsu.co.id", "sr.hendra.jkt2@gmail.com", "aanlia080209@gmail.com", "uccygafur@gmail.com", "aryogi@hisamitsu.co.id", "ulum@hisamitsu.co.id", "Adityuda570@gmail.com", "setyo@hisamitsu.co.id", "Rinakusnia23@gmail.com", "masrul@hisamitsu.co.id", "purwanto@hisamitsu.co.id", "fitri@hisamitsu.co.id", "usmanbaehaki@gmail.com", "anggaf834@gmail.com", "hpi-sda@hisamitsu.co.id", "vidya@hisamitsu.co.id", "Windapanca10@gmail.com", "lilikzuniati@gmail.com", "dion@hisamitsu.co.id", "tina.emelia@hisamitsu.co.id", "ajeng.ayuningtyas@hisamitsu.co.id", "Candramustapa10@gmail.com", "hendry.windarto@hisamitsu.co.id", "hairilanwar083@gmail.com", "Sonaji886@gmail.com", "lady.nurkhairiah@gmail.com", "ginggylandho@yahoo.co.id", "liliksapiengok@gmail.com", "wulansetyo0405@gmail.com", "saidilleo@gmail.com", "firdaushpi@gmail.com", "Esumarlik97@gmail.com", "Aguspurwadi009@gmail.com", "faruk_gunawan99@yahoo.com", "ahmadfaizuddin53@gmail.com", "novitasuprapti48@gmail.com", "kridasetyowati6025@gmail.com", "Wiwitharianto09@gmail.com", "hpi-adminfactory2@hisamitsu.co.id", "rifki_hisamitsu@yahoo.com", "rini@hisamitsu.co.id", "aditkanaya17@gmail.com", "sujarwo@hisamitsu.co.id", "agungtri@hisamitsu.co.id", "odeimz@yahoo.com", "lidya@hisamitsu.co.id", "Iriantiutari92@gmail.com", "eryka1.eyse@gmail.com", "Cningnina@gmail.com", "bundaadinda.arjuna21@gmail.com", "Dwiagung597@gmail.com", "sugianto@hisamitsu.co.id", "khaylakhenzie2017@gmail.com", "irfanardi77@gmail.com", "mahludinyuni@yahoo.com", "rustivika@gmail.com", "nuzzulia0104@gmail.com", "ciadewi57@gmail.com", "sandyprima0@gmail.com", "sisilcaem78@gmail.com", "Goan4646@yahoo.com", "cakpandy4@gmail.com", "nurulrohman.nr9@gmail.com", "phontas@hisamitsu.co.id", "albert@hisamitsu.co.id", "metakarina90@gmail.com", "Indahkwardani@yahoo.com", "fathkurrozi123@gmail.com", "astriwulandari081@gmail.com", "kikipatmaja@gmail.com", "ricimalmsteen@gmail.com", "yudha@hisamitsu.co.id", "Adefelicia21@gmail.com", "nauval@hisamitsu.co.id", "ajeng.mtc@gmail.com", "Anthoengholic@gmail.com", "Ally03hpi@gmail.com", "Een.Khairudin@gmail.com", "muhammadnurardhi@gmail.com", "wicak@hisamitsu.co.id", "mizan@hisamitsu.co.id", "mukhlis@hisamitsu.co.id", "Alwadud1414@gmail.com", "amiknh659@gmail.com", "vrindo12@gmail.com", "Jekim336@gmail.com", "achmadnasruddin84@gmail.com", "liaainullia@gmail.com", "nitapra00@gmail.com", "refina@hisamitsu.co.id", "denis@hisamitsu.co.id", "wahyunisae@gmail.com", "tito.sumanto@yahoo.com", "tiyansputri29@gmail.com", "fadjar9889@gmail.com", "aridwisantosa@ymail.com", "Siswantorecrut@gmail.com", "Hambali.silva@gmail.com", "ira.guci@hisamitsu.co.id", "nagano@hisamitsu.co.id", "Rikoandri04@gmail.com", "taufan.nugroho@hisamitsu.co.id", "refni.ragil@gmail.com", "smiley.nisa@gmail.com", "ferdyzendiawan@gmail.com", "madianarih@yahoo.co.id", "albertodhuan@gmail.com", "Agususanto883@gmail.com", "Intusalrafi23@gmail.com", "azrilisnansantoso@gmail.com", "ayurahma211220@gmail.com", "habibie@hisamitsu.co.id", "rizadpras@gmail.com", "siti.munawaroh@hisamitsu.co.id", "muhammadmaarif02@gmail.com", "erik.djunaedi@hisamitsu.co.id", "zulfadli@hisamitsu.co.id", "bobparuliannainggolan11@gmail.com", "satyaharris17@gmail.com", "ditaprintsidoarjo@gmail.com", "nuchan2@gmail.com", "hendy@hisamitsu.co.id", "lukman@hisamitsu.co.id", "suwantohuang11@gmail.com", "dian.ah@hisamitsu.co.id", "asmoropandu68@gmail.com", "tianrahmawan99@gmail.com", "ardiansyahbonny@gmail.com", "ssantoso242@gmail.com", "hendy.mukti@hisamitsu.co.id", "Achmadelnino01@gmail.com", "muhammadyusuf20091981@gmail.com", "Herry.ujung8@gmail.com", "Setianingrum.1995@gmail.com", "sentot.purwandi@hisamitsu.co.id", "shelylatri25@gmail.com", "Nursalimah3002@gmail.com", "jepripradana93@gmail.com", "Wendi.prasetyo18@gmail.com", "noviandita1994@gmail.com", "Herusetyawan.hs91@gmail.com", "uun.umami@gmail.com", "Cielloshop.93@gmail.com", "nurfadhillah355@gmail.com", "rizkyambon4@gmail.com", "Lauraardilla1993@gmail.com", "Riccoputrafrediansha@gmail.com", "hanum.atika@hisamitsu.co.id", "rizkyatantri.gwandari@gmail.com", "deasy.chaerunisyah@hisamitsu.co.id", "deo.ristiadi@hisamitsu.co.id", "damar.adi@hisamitsu.co.id", "mualla.mufarristi@hisamitsu.co.id", "Sitinurhayati549@gmail.com", "arifitrianingrum15@gmail.com", "windawati1248@gmail.com", "anisshofianah90@gmail.com", "wawan.supriyanto@hisamitsu.co.id", "nabilarerry@gmail.com", "putradana18.pd@gmail.com", "lael.antoinette@hisamitsu.co.id", "betty.siska@hisamitsu.co.id", "moniq799@gmail.com", "mufidatun.nisak@hisamitsu.co.id", "nurmaulinafike00@gmail.com", "rizalverde25@gmail.com", "amcharor@gmail.com", "Afifudinbaihaqi@gmail.com", "mauludinachmad5@gmail.com", "Rifatussadiyah2@gmail.com", "Ilham4238@gmail.com", "surya.adi@hisamitsu.co.id", "anikaanandaa@gmail.com", "bayudannie@gmail.com", "atikaapriani10@gmail.com", "Vitasandi1991@gmail.com", "hendi.hendian@hisamitsu.co.id", "darsagalang78@gmail.com", "Jamaludinasep025@gmail.com", "aniekdwisuwarni2711@gmail.com", "herywidiyanto2807@gmail.com", "hidayatulilahiyah47@gmail.com", "ningsihulfa17@gmail.com", "dedifazla3@gmail.com", "dhndanns@gmail.com", "aisyaamalia9@gmail.com", "Regitamandini@gmail.com", "Ulfianifazrina08@gmail.com", "corneliaa0102@gmail.com", "tantio.sukatmono@hisamitsu.co.id", "desy.setiarini@hisamitsu.co.id", "rafi17tok@gmail.com", "luhur.wibisono@hisamitsu.co.id", "budi.wahyu@hisamitsu.co.id", "abu.rizal@hisamitsu.co.id", "Andisubagio14@gmail.com", "c.prambudi87@gmail.com", "hielma0306nisa@gmail.com", "muhammadagusti1708@gmail.com", "Prayitn401@gmail.com", "achmadoyon@gmail.com", "deconurva@gmail.com", "Irfan.kristiawan99@gmail.com", "dedyeko1995@gmail.com", "estelitahermawan@gmail.com", "pratiwi.agesti@hisamitsu.co.id", "royadwiyan@gmail.com", "rdesysiamsari@gmail.com", "fajaralifc535@gmail.com", "rag.rismaaa196@gmail.com", "tsaniyamasykuro0705@gmail.com", "Salbilwip@gmail.com", "dianareza57@gmail.com", "Puspasaritarisadoys@gmail.com", "riofadli027@gmail.com", "wiboworiyan65@gmail.com", "Imamfahrudin433@gmail.com", "khabibillahmuhammad@gmail.com", "fiqihamirul22@gmail.com", "ahmadhusain170795@gmail.com", "razakdardiri@gmail.com", "vnnysptr@gmail.com", "Ragilliafitri07@gmail.com", "jazirohwardatul@gmail.com", "firlineka03@gmail.com", "evitaretnoningrum07@gmail.com", "Fitriaregeng20@gmail.com", "annissapasha27@gmail.com", "bimaroverzs1@gmail.com", "erikaoktavia271@gmail.com", "Ikafitrianisoraya98@gmail.com", "Pratiwilindaarum@gmail.com", "okkynovian86@gmail.com", "mzulfan0307@gmail.com", "novalmkonji88@gmail.com", "septianputra.125@yahoo.com", "anggirahmawatidewiprasetyo@gmail.com", "astriwinayanti@gmail.com", "ayunipxv@gmail.com", "Azizahrahma1122@gmail.com", "dewimasrifah1717@gmail.com", "rachmawatiputri0119@gmail.com", "irsaalinkhoiriliaa@gmail.com", "virgohasani94@gmail.com", "maslihana14@gmail.com", "noveritarahmadani05@gmail.com", "meirizkyana@gmail.com", "tivaniflorida@gmail.com", "Himmaeparker99@gmail.com", "lailamutmainah26@gmail.com", "Mirfatcamelia@gmail.com", "hermawanfarid9@gmail.com", "rizq.mana@gmail.com", "twulan680@gmail.com", "cindyevllin2901@gmail.com", "gunturayungga1@gmail.com", "berta.anggraini@gmail.com", "Fatchurr996@gmail.com", "syafiilanam.a6@gmail.com", "sakurai@hisamitsu.co.id", "bintiuminafiah@gmail.com", "dahlurizqi9580@gmail.com", "dmaharani873@gmail.com", "monicarosdiana9@gmail.com", "robert.silaban@hisamitsu.co.id", "agus.nurbagyono@hisamitsu.co.id", "alfiandanu40@gmail.com", "bgustiiy@gmail.com", "fadlunnurarof@gmail.com", "aansulistyo383@gmail.com", "Nailymufarrohah24@gmail.com", "irfankurniawan724@gmail.com", "muhaliafandi@gmail.com", "habilarifin01@gmail.com", "novitakartika648@gmail.com", "deniagussetiawan35@gmail.com", "Jenilistanti@gmail.com", "sigettriwayudi@gmail.com", "drohma387@gmail.com", "ghoni.fadly@hisamitsu.co.id", "Zaealimm@gmail.com", "fitriadiarrosyid78@gmail.com", "Ardinoharyunanda@gmail.com", "ivahelsia14@gmail.com", "ahmadridhorhoma@gmail.com", "bimamp156cc@gmail.com", "darusetyaputra@gmail.com", "lastriwahyuni93@gmail.com", "Ilmisuci@gmail.com", "arpiansyahian313@gmail.com", "faisal.alhakim@gmail.com", "rizalalfandiz@gmail.com", "rachmitafaradilae@gmail.com", "aidafitriaaa12@gmail.com", "erlianfernanda08@gmail.com", "nurazizahanis3@gmail.com", "bahrulhidayat155@gmail.com", "ferdi.gumay31@gmail.com", "tasbikhi20@gmail.com", "hanumhijriyah@gmail.com", "irayulikustina017@gmail.com", "ikaaslip@gmail.com", "monicaapril.ma7@gmail.com", "nindafitria40@gmail.com", "novimaulidiyah06@gmail.com", "SALZAMUBARANI26@GMAIL.COM", "sitimiftakhulazizah54@gmail.com", "Sitipurwita123@gmail.com", "suryantiekafnd@gmail.com", "TASYASAVIRAALDA@GMAIL.COM", "umihidayah040701@gmail.com", "uswatulkhosniyyah27@gmail.com", "vindy.f4timah@gmail.com", "vivievita707@gmail.com", "VIVIINDAH60@GMAIL.COM", "gufurainalarinsa@gmail.com", "youanamila@gmail.com", "fahrial.firmansyah@hisamitsu.co.id", "marylonpurba.16@gmail.com", "mizukami@hisamitsu.co.id", "choirulanamiskak8@gmail.com", "argokiper86@gmail.com", "awanggumelar4@gmail.com", "bagusromadhon822@gmail.com", "imamas290498@gmail.com", "shahrilromadhoni15@gmail.com", "mochamaadrafi27@gmail.com", "kholiqinsomnia@gmail.com", "arifnovianto767@gmail.com", "Amirjamaluddin804@gmail.com", "msugiharto999@gmail.com", "NANANGKURNIAWANN1919@GMAIL.COM", "pinadwi03@gmail.com", "achmadnurhuda78@gmail.com", "ainundamayanti64@gmail.com", "anakhoiroh123@gmail.com", "aanisaa1306@gmail.com", "bellaputr84@gmail.com", "erikaanggraeni09@gmail.com", "fitrifebri72@gmail.com", "miayoomsa@gmail.com", "rizkijuliawati3@gmail.com", "harimbiasegi27@gmail.com", "sepriantisanti16@gmail.com", "pwpw877@gmail.com", "syayida.tina58@gmail.com", "vitarusiana1@gmail.com", "wulansafitri8317@gmail.com", "dederamadan27@gmail.com", "mesludin1997@gmail.com", "amaliyamufida75@gmail.com", "ummusfitria@gmail.com", "anggivtaa07@gmail.com", "asepsaipudin30@gmail.com", "ditoth91@gmail.com", "FRENDY.LASKAR25@GMAIL.COM", "harry.ardiansyah435@gmail.com", "helmiazlansyah@gmail.com", "zakariamhd18@gmail.com", "Marten.lumanauw@gmail.com", "mohsidiqjailani@gmail.com", "LAILYNUUR17@GMAIL.COM", "oktaviamilani.15@gmail.com", "vera.veronica@hisamitsu.co.id", "aafnindya@gmail.com", "argorendibudisetyawan@gmail.com", "bayuajirandika@gmail.com", "davidhmn69@gmail.com", "f.noeraini0903@gmail.com", "mochamadhermansyah1@gmail.com", "edo.alif48@gmail.com", "ayurizkiyuliyanti0907@gmail.com", "fatchurtok27@gmail.com", "hanakeisya02@gmail.com", "nadsal282@gmail.com", "rickyguns2@gmail.com", "aswindwisasongko19@gmail.com", "David.rowansah@gmail.com", "dhoyseko27@gmail.com", "diahafriska4@gmail.com", "dithalestia@gmail.com", "Frayunika21@gmail.com", "helmialdi17@gmail.com", "nindityaaaa15@gmail.com", "REDNAREDNO8@GMAIL.COM", "Santresia630@gmail.com", "wahyunilestari031@gmail.com", "khirunnisak6@gmail.com", "Herifirmanali.11@gmail.com", "irsyadum@gmail.com", "FINDAAISYAH@GMAIL.COM", "mileonald@gmail.com", "achmadchief@gmail.com", "aslamalb94@gmail.com", "idazulfiyah1211@gmail.com", "Netadestianaf@gmail.com", "Nadiya.com98@gmail.com", "arifin.myname@gmail.com", "muhammadfharidz378@gmail.com", "m.a.syamsudin1999@gmail.com", "yudadeco24@gmail.com", "verryfatin@gmail.com", "Lailidwi18@gmail.com", "firnandadwiwardani1@gmail.com", "muhamadgofur0707@gmail.com", "varispurwanto@gmail.com", "putrichumairoh02@gmail.com", "febriananes79@gmail.com", "mochluddfi@gmail.com", "taufik242014@gmail.com", "devianur3232@gmail.com", "ilmaawalyaftr@gmail.com", "Ahmadauliyaeffendi@gmail.com", "Kiko.gandos02@gmail.com", "andywiranata56@gmail.com", "trimegayuliarti@gmail.com", "iisnaeni000@gmail.com", "awstoresidoarjo@gmail.com", "hdynurul26@gmail.com", "muhammaddoni093@gmail.com", "wahyuinneke1@gmail.com", "wendiyana128@gmail.com", "syaifularif31@gmail.com", "dwic0702@gmail.com", "henywidowati18@gmail.com", "latifatululanuris@gmail.com", "uhabibah870@gmail.com", "anisfbrynti@gmail.com", "Nuthjung2@gmail.com", "dwiagustin07079@gmail.com", "hpihermanaceh@gmail.com", "Simmy412k@gmail.com", "msboeedy@gmail.com", "ANDRIO969696@GMAIL.COM", "Arrohimyoseph@gmail.com", "ALEXDONNYALG@GMAIL.COM", "dmusnitasani@gmail.com", "immayogi04@gmail.com", "miftahartha@gmail.com", "devhy.meitta@gmail.com", "irawaneffendy23@gmail.com", "Inggarprabowolukito@gmail.com", "DWIMEILINDASARI2@GMAIL.COM", "Suliin293@gmail.com", "hamidmbois1@gmail.com", "fatimahtuszahroh386@gmail.com", "ikkesafitri844@gmail.com", "Tjoyseptian@gmail.com", "dindacantika263@gmail.com", "himaaah0402@gmail.com", "reonaldy09@gmail.com", "dheaayu968@gmail.com", "CHITAMANURUNG@GMAIL.COM", "arekmain75@gmail.com", "Taufiqahmad047@gmail.com", "desyantikaandini@gmail.com", "jatikumoro83@gmail.com", "fajarrey0505@gmail.com", "rikaamaliawidiyanti@gmail.com", "adityafca10@gmail.com");
        // $arr_combine = array_combine($arr_unique,$arr2);
        // // dd($arr_combine);
        // foreach($arr_combine as $arr_key =>$arr_val){
        //     $id = $arr_key;
        //     if($arr_val != null){
        //         // $level = Level::where('nama', $arr_val)->first();
        //         // $test[] = $arr_key.' => '.$section->id;
        //         // $post = Employee::where('nik', $id)->first();
        //         // if(!empty($post)){
        //         //     $update = $post->update(['email' => $arr_val]);
        //         // }
        //         // $post = Employee::where('nik', $id)->update(['no_ktp' => $arr_val]);
        //     }
        // }
        // // dd($update);
        // // foreach($arr_unique as $unique => $val){
        // //     $post = Level::insert([
        // //         'nama' => $val,
        // //         'created_at' => Carbon::now(),
        // //         'updated_at' => Carbon::now()
        // //     ]);
        // // }
        // // dd($post);        
        // // if (Auth::user()->can('emp.menu')) {
        // //     dd('true');
        // // }else{
        // //     dd('false');
        // // }

        // // $post = Employee::where('area_id', 1)->update(['work_location' => 'KAB. SIDOARJO']);
        // // dd($post);
        // // dd($employee_active);

        // // $last_date = new Carbon('2023-11');
        // // $date = new Carbon('2022-12');
        // // $diff = $date->diff($last_date);
        // // dd($diff->y);
        // $arr1 = array("0622WH0033", "0622WH0034", "0622WH0035", "0622WH0036", "0622WH0037", "0622WH0038", "0622WH0039", "0622WH0040", "0622WH0041", "0622WH0042", "0622WH0043", "0622WH0044", "0622WH0045", "0622WH0046", "0622WH0047", "0622WH0048", "0622WH0049", "0622WH0050", "0622WH0051", "0622WH0052", "0622WH0053", "0622WH0054", "0622WH0055", "0622WH0056", "0622WH0058", "0622WH0059", "0622WH0060", "0622WH0061", "0622WH0062", "0622WH0063", "0622WH0064", "0622WH0065", "0622WH0067", "0622WH0068", "0622WH0069", "0622WH0070", "0622WH0071", "0622WH0072", "0622WH0073", "0622WH0074", "0622WH0076", "0622WH0077", "0622WH0078", "0622WH0079", "0622WH0080", "0622WH0081", "0622WH0082", "0622WH0083", "0622WH0084", "0622WH0085", "0622WH0086", "0622WH0087", "0622WH0088", "0622WH0089", "0622WH0090", "0622WH0091", "0622WH0092", "0622WH0093", "0622WH0094", "0622WH0095", "0622WH0096", "0622WH0097", "0622WH0098", "0622WH0099", "0622WH0100", "0622WH0101", "0622WH0102", "0622WH0103", "0622WH0104", "0622WH0105", "0622WH0106", "0622WH0107", "0622WH0108", "0622WH0109", "0622WH0111", "0622WH0112", "0622WH0113", "0622WH0114", "0622WH0115", "0622WH0116", "0622WH0117", "0622WH0118", "0622WH0119", "0622WH0120", "0622WH0121", "0622WH0122", "0622WH0123", "0622WH0124", "0622WH0125", "0622WH0126", "0622WH0127", "0622WH0128", "0622WH0129", "0622WH0130", "0622WH0131", "0622WH0132", "0622WH0133", "0622WH0134", "0622WH0135", "0622WH0136", "0622WH0137", "0622WH0138", "0622WH0139", "0622WH0140", "0622WH0141", "0622WH0142", "0622WH0143", "0622WH0144", "0622WH0145", "0622WH0146", "0622WH0147", "0622WH0148", "0622WH0149", "0622WH0150", "0622WH0151", "0622WH0152", "0622WH0153", "0622WH0154", "0622WH0155", "0622WH0156", "0622WH0157", "0622WH0158", "0622WH0160", "0622WH0161", "0622WH0162", "0622WH0163", "0622WH0164", "0622WH0165", "0622WH0166", "0622WH0168", "0622WH0169", "0622WH0170", "0622WH0171", "0622WH0172", "0622WH0173", "0622WH0174", "0622WH0175", "0622WH0176", "0622WH0177", "0622WH0178", "0622WH0179", "0622WH0180", "0622WH0181", "0622WH0182", "0622WH0183", "0622WH0184", "0622WH0185", "0622WH0186", "0622WH0188", "0622WH0189", "0622WH0190", "0622WH0191", "0622WH0192", "0622WH0193", "0622WH0194", "0622WH0195", "0622WH0196", "0622WH0197", "0622WH0198", "0622WH0199", "0622WH0200", "0622WH0201", "0622WH0202", "0622WH0203", "0622WH0204", "0622WH0205", "0622WH0206", "0622WH0207", "0622WH0208", "0622WH0209", "0622WH0210", "0622WH0211", "0622WH0212", "0622WH0213", "0622WH0214", "0622WH0215", "0622WH0216", "0622WH0217", "0622WH0218", "0622WH0219", "0622WH0220", "0622WH0221", "0622WH0222", "0622WH0223", "0622WH0224", "0622WH0225", "0622WH0226", "0622WH0227", "0622WH0228", "0622WH0229", "0622WH0230", "0622WH0231", "0622WH0232", "0622WH0233", "0622WH0234", "0622WH0235", "0622WH0236", "0622WH0237", "0622WH0238", "0622WH0239", "0622WH0240", "0622WH0241", "0622WH0242", "0622WH0243", "0622WH0244", "0622WH0245", "0622WH0246", "0622WH0247", "0622WH0248", "0622WH0249", "0622WH0250", "0622WH0251", "0622WH0252", "0622WH0253", "0622WH0254", "0622WH0255", "0622WH0256", "0622WH0257", "0622WH0258", "0622WH0259", "0622WH0260", "0622WH0261", "0622WH0262", "0622WH0263", "0622WH0264", "0622WH0265", "0622WH0266", "0622WH0267", "0622WH0268", "0622WH0269", "0622WH0270", "0622WH0271", "0622WH0272", "0622WH0273", "0622WH0274", "0622WH0275", "0622WH0276", "0622WH0277", "0622WH0278", "0622WH0279", "0622WH0280", "0622WH0281", "0622WH0282", "0622WH0283", "0622WH0284", "0622WH0285", "0622WH0286", "0622WH0287", "0622WH0288", "0622WI0028", "0622WI0029", "0622WI0030", "0622WA0179", "0622WA0177", "0622WA0222", "0622WA0233", "0622WA0174", "0622WA0183", "0622WA0185", "0622WA0204", "0622WA0208", "0622WA0230", "0622WA0256", "1022W40008", "1022W40010", "0622WA0182", "0622WA0202", "0622WA0231", "0622WA0238", "0622WA0244", "0622WA0253", "0622WA0255", "1222WO0006", "1222WO0007", "0622WA0225", "0622WA0176", "0622WA0178", "0622WA0188", "0622WA0192", "0622WA0194", "0622WA0196", "0622WA0197", "0622WA0200", "0622WA0210", "0622WA0212", "0622WA0216", "0622WA0217", "0622WA0219", "0622WA0221", "0622WA0228", "0622WA0229", "0622WA0234", "0622WA0235", "0622WA0236", "0622WA0241", "0622WA0242", "0622WA0243", "0622WA0245", "0622WA0246", "0622WA0251", "0622WA0252", "0622WA0257", "0622WA0259", "1422WN0003", "0622WA0184", "0622WA0206", "0622WA0207", "0622WA0232", "0622WA0181", "0622WA0189", "0622WA0186", "0622WA0193", "0622WA0201", "0622WA0249", "0622WA0254", "0622WA0175", "0622WA0187", "0622WA0203", "0622WA0213", "0622WA0215", "0622WA0258", "0622WA0214", "0622WA0172", "0622WA0224", "0622WA0237", "0622WQ0011", "0622WA0191", "0622WA0209", "0622WA0211", "0622WA0220", "0622WA0223", "0622WA0226", "0622WA0227", "0622WA0240", "0622WA0247", "0622WA0250", "0622WA0239", "0622WA0205", "0622WA0218", "0622WY0035", "0622WA0199", "0622WA0190", "0622WA0173", "0622WA0180", "0622WA0198", "0622WA0248", "0622WU0018", "0622WW0009");
        // $arr2 = array("26 Thn 6 Bln", "32 Thn 0 Bln", "30 Thn 1 Bln", "33 Thn 0 Bln", "26 Thn 3 Bln", "30 Thn 1 Bln", "33 Thn 7 Bln", "49 Thn 9 Bln", "32 Thn 2 Bln", "36 Thn 2 Bln", "22 Thn 9 Bln", "27 Thn 3 Bln", "26 Thn 7 Bln", "23 Thn 2 Bln", "37 Thn 1 Bln", "38 Thn 1 Bln", "22 Thn 2 Bln", "48 Thn 10 Bln", "20 Thn 9 Bln", "31 Thn 3 Bln", "22 Thn 3 Bln", "21 Thn 6 Bln", "24 Thn 8 Bln", "27 Thn 10 Bln", "26 Thn 2 Bln", "26 Thn 9 Bln", "26 Thn 8 Bln", "32 Thn 2 Bln", "35 Thn 8 Bln", "23 Thn 0 Bln", "28 Thn 3 Bln", "27 Thn 1 Bln", "22 Thn 11 Bln", "22 Thn 5 Bln", "23 Thn 2 Bln", "20 Thn 7 Bln", "30 Thn 9 Bln", "24 Thn 0 Bln", "22 Thn 3 Bln", "33 Thn 5 Bln", "48 Thn 9 Bln", "29 Thn 11 Bln", "31 Thn 4 Bln", "38 Thn 4 Bln", "21 Thn 9 Bln", "35 Thn 4 Bln", "22 Thn 7 Bln", "30 Thn 10 Bln", "25 Thn 1 Bln", "34 Thn 3 Bln", "25 Thn 2 Bln", "30 Thn 3 Bln", "29 Thn 10 Bln", "30 Thn 0 Bln", "23 Thn 1 Bln", "20 Thn 7 Bln", "19 Thn 4 Bln", "23 Thn 8 Bln", "21 Thn 4 Bln", "29 Thn 9 Bln", "33 Thn 7 Bln", "34 Thn 5 Bln", "30 Thn 8 Bln", "27 Thn 3 Bln", "21 Thn 5 Bln", "53 Thn 5 Bln", "44 Thn 2 Bln", "31 Thn 8 Bln", "25 Thn 0 Bln", "28 Thn 5 Bln", "22 Thn 10 Bln", "38 Thn 1 Bln", "24 Thn 6 Bln", "24 Thn 9 Bln", "21 Thn 9 Bln", "28 Thn 0 Bln", "22 Thn 8 Bln", "25 Thn 10 Bln", "36 Thn 7 Bln", "21 Thn 7 Bln", "21 Thn 9 Bln", "21 Thn 10 Bln", "22 Thn 9 Bln", "26 Thn 3 Bln", "36 Thn 10 Bln", "34 Thn 11 Bln", "30 Thn 5 Bln", "42 Thn 3 Bln", "25 Thn 2 Bln", "46 Thn 9 Bln", "30 Thn 4 Bln", "26 Thn 3 Bln", "23 Thn 3 Bln", "24 Thn 9 Bln", "28 Thn 4 Bln", "22 Thn 11 Bln", "24 Thn 6 Bln", "20 Thn 10 Bln", "49 Thn 3 Bln", "22 Thn 0 Bln", "30 Thn 8 Bln", "21 Thn 0 Bln", "25 Thn 9 Bln", "22 Thn 9 Bln", "53 Thn 7 Bln", "29 Thn 7 Bln", "50 Thn 0 Bln", "54 Thn 9 Bln", "30 Thn 2 Bln", "26 Thn 6 Bln", "35 Thn 10 Bln", "30 Thn 2 Bln", "22 Thn 2 Bln", "26 Thn 10 Bln", "29 Thn 10 Bln", "26 Thn 6 Bln", "44 Thn 10 Bln", "30 Thn 0 Bln", "36 Thn 0 Bln", "37 Thn 10 Bln", "49 Thn 9 Bln", "54 Thn 0 Bln", "29 Thn 4 Bln", "41 Thn 1 Bln", "50 Thn 9 Bln", "21 Thn 7 Bln", "22 Thn 4 Bln", "33 Thn 0 Bln", "49 Thn 3 Bln", "27 Thn 5 Bln", "29 Thn 6 Bln", "40 Thn 4 Bln", "22 Thn 1 Bln", "19 Thn 9 Bln", "39 Thn 6 Bln", "29 Thn 6 Bln", "24 Thn 6 Bln", "26 Thn 9 Bln", "32 Thn 4 Bln", "23 Thn 1 Bln", "24 Thn 8 Bln", "30 Thn 4 Bln", "51 Thn 0 Bln", "32 Thn 0 Bln", "18 Thn 11 Bln", "28 Thn 7 Bln", "26 Thn 9 Bln", "25 Thn 4 Bln", "30 Thn 10 Bln", "30 Thn 4 Bln", "37 Thn 8 Bln", "21 Thn 4 Bln", "40 Thn 6 Bln", "27 Thn 5 Bln", "23 Thn 4 Bln", "37 Thn 7 Bln", "47 Thn 11 Bln", "20 Thn 0 Bln", "28 Thn 0 Bln", "31 Thn 11 Bln", "48 Thn 5 Bln", "50 Thn 10 Bln", "23 Thn 3 Bln", "24 Thn 5 Bln", "29 Thn 1 Bln", "26 Thn 5 Bln", "27 Thn 10 Bln", "24 Thn 6 Bln", "20 Thn 9 Bln", "31 Thn 7 Bln", "22 Thn 11 Bln", "30 Thn 1 Bln", "28 Thn 6 Bln", "33 Thn 8 Bln", "27 Thn 5 Bln", "26 Thn 3 Bln", "32 Thn 2 Bln", "24 Thn 11 Bln", "22 Thn 11 Bln", "25 Thn 4 Bln", "22 Thn 9 Bln", "30 Thn 4 Bln", "24 Thn 2 Bln", "26 Thn 10 Bln", "25 Thn 2 Bln", "26 Thn 1 Bln", "25 Thn 8 Bln", "28 Thn 6 Bln", "19 Thn 7 Bln", "33 Thn 0 Bln", "39 Thn 5 Bln", "21 Thn 9 Bln", "30 Thn 6 Bln", "21 Thn 4 Bln", "28 Thn 2 Bln", "30 Thn 0 Bln", "22 Thn 5 Bln", "30 Thn 1 Bln", "25 Thn 3 Bln", "31 Thn 7 Bln", "49 Thn 4 Bln", "22 Thn 9 Bln", "50 Thn 6 Bln", "32 Thn 2 Bln", "25 Thn 1 Bln", "27 Thn 10 Bln", "29 Thn 9 Bln", "28 Thn 6 Bln", "32 Thn 6 Bln", "33 Thn 7 Bln", "25 Thn 10 Bln", "38 Thn 9 Bln", "52 Thn 0 Bln", "21 Thn 0 Bln", "52 Thn 2 Bln", "31 Thn 4 Bln", "41 Thn 8 Bln", "34 Thn 4 Bln", "54 Thn 3 Bln", "19 Thn 11 Bln", "30 Thn 9 Bln", "20 Thn 1 Bln", "26 Thn 3 Bln", "60 Thn 3 Bln", "23 Thn 10 Bln", "27 Thn 4 Bln", "22 Thn 6 Bln", "29 Thn 5 Bln", "21 Thn 8 Bln", "23 Thn 0 Bln", "22 Thn 5 Bln", "52 Thn 7 Bln", "33 Thn 7 Bln", "25 Thn 11 Bln", "22 Thn 1 Bln", "30 Thn 3 Bln", "36 Thn 6 Bln", "27 Thn 5 Bln", "31 Thn 9 Bln", "23 Thn 8 Bln", "49 Thn 7 Bln", "28 Thn 8 Bln", "31 Thn 10 Bln", "25 Thn 0 Bln", "33 Thn 7 Bln", "29 Thn 7 Bln", "41 Thn 5 Bln", "51 Thn 4 Bln", "23 Thn 5 Bln", "29 Thn 7 Bln", "25 Thn 10 Bln", "24 Thn 6 Bln", "36 Thn 2 Bln", "37 Thn 10 Bln", "41 Thn 9 Bln", "32 Thn 7 Bln", "46 Thn 6 Bln", "33 Thn 2 Bln", "44 Thn 10 Bln", "45 Thn 10 Bln", "32 Thn 5 Bln", "47 Thn 10 Bln", "47 Thn 10 Bln", "30 Thn 6 Bln", "40 Thn 0 Bln", "34 Thn 11 Bln", "41 Thn 4 Bln", "31 Thn 4 Bln", "37 Thn 6 Bln", "55 Thn 3 Bln", "39 Thn 4 Bln", "36 Thn 10 Bln", "44 Thn 1 Bln", "31 Thn 0 Bln", "42 Thn 8 Bln", "38 Thn 8 Bln", "35 Thn 7 Bln", "38 Thn 8 Bln", "60 Thn 1 Bln", "38 Thn 11 Bln", "33 Thn 11 Bln", "38 Thn 7 Bln", "39 Thn 5 Bln", "37 Thn 11 Bln", "38 Thn 0 Bln", "30 Thn 2 Bln", "40 Thn 1 Bln", "37 Thn 10 Bln", "37 Thn 4 Bln", "30 Thn 0 Bln", "30 Thn 4 Bln", "36 Thn 4 Bln", "36 Thn 9 Bln", "54 Thn 10 Bln", "37 Thn 0 Bln", "54 Thn 5 Bln", "52 Thn 9 Bln", "48 Thn 5 Bln", "40 Thn 6 Bln", "31 Thn 2 Bln", "38 Thn 2 Bln", "35 Thn 0 Bln", "35 Thn 2 Bln", "26 Thn 6 Bln", "53 Thn 7 Bln", "39 Thn 5 Bln", "41 Thn 7 Bln", "35 Thn 8 Bln", "32 Thn 11 Bln", "34 Thn 1 Bln", "32 Thn 4 Bln", "54 Thn 4 Bln", "33 Thn 3 Bln", "54 Thn 10 Bln", "54 Thn 7 Bln", "39 Thn 3 Bln", "35 Thn 6 Bln", "35 Thn 5 Bln", "33 Thn 9 Bln", "41 Thn 0 Bln", "52 Thn 5 Bln", "52 Thn 5 Bln", "45 Thn 3 Bln", "35 Thn 0 Bln", "35 Thn 4 Bln", "31 Thn 8 Bln", "39 Thn 0 Bln", "43 Thn 9 Bln", "40 Thn 6 Bln", "29 Thn 9 Bln", "40 Thn 10 Bln", "43 Thn 1 Bln", "41 Thn 1 Bln", "42 Thn 6 Bln", "43 Thn 3 Bln", "38 Thn 8 Bln", "32 Thn 5 Bln", "36 Thn 10 Bln", "40 Thn 9 Bln", "30 Thn 9 Bln", "32 Thn 6 Bln", "29 Thn 10 Bln", "40 Thn 7 Bln", "38 Thn 4 Bln", "31 Thn 5 Bln", "36 Thn 3 Bln", "35 Thn 6 Bln", "39 Thn 3 Bln");
        // $no_lab = array_combine($arr1,$arr2);
        // // dd($no_lab);
        // foreach($no_lab as $lab => $val){
        //     $medical = Medical::where('no_lab', $lab)->where('id_template', 1)->first();
        //     if(!empty($medical)){
        //         // if($medical->employee->gender == 'Male'){
        //         //     $lampiran = $medical->no_lab.' - '.$medical->employee->fullname.'(L).pdf';
        //         // }else{
        //         //     $lampiran = $medical->no_lab.' - '.$medical->employee->fullname.'(P).pdf';
        //         // }
        //         // $post = Medical::where('id', $medical->id)->update(['umur' => $val]);
        //     }
        // }
        // dd($post);

        // // $employee = Employee::whereIn('fullname', $arr_nama)->where('area_id', 1)->orderBy('fullname')->get()->unique('fullname')->pluck('id');
        // $employee = Employee::whereIn('id', $arr_nama)->where('area_id', 1)->orderBy('fullname')->get()->pluck('fullname','id');
        // dd($employee);
        
        // //upload file pdf
        // $directory = "public/mcu";
        // $files = Storage::allFiles($directory);
        // dd($files);
  
        // foreach($files as $key => $value){
        //     $explode = (explode("public/mcu/", $value));
        //     $explode2 = (explode(" - ",$explode[1]));
        //     // $medical = medical::where('no_lab', $explode2[0])->update(['lampiran_mcu' => $explode[1]]);
        // }
        // dd('selesai');
        // $employee = Employee::where('fullname', $explode2[0])->first();
        // dd($employee->id);
        // // $Medical Medical::where('')
        // //get name column table
        // // $post = Medical::find(1);
        // // $lab = Lab::where('id_vendor', $post->id_vendor)->get();
        // // foreach($lab as $key => $value){
        // //     $medical['no_lab'] = $post->no_lab;
        // //     $medical['test'] = Schema::getColumnListing($post->hm_hemoglobin);
        // //     // $data['pemeriksaan'] = $value->pemeriksaan;
        // //     // $data['nilai_rujukan'] = $value->nilai_rujukan;
        // //     // $data_lab[] = $data;
        // // }
        // // dd($data_lab);
        // // $tableName  = $post->getTable();
        // // $columns = Schema::getColumnListing($tableName);
        // // foreach($columns as $col){
        // //     $result['pemeriksaan'] = $col;
        // //     $medical['no_lab'] = $post->no_lab;
        // //     $medical['nr_hemoglobin'][] = in_array( $result['pemeriksaan'],array_column($data_lab, 'pemeriksaan'))? $data_lab['pemeriksaan']:'-';
        // // }
  
        // // dd($medical);

        // //get storage path json
        // // $path = storage_path('app/city-list.json');
        // // $content = json_decode(file_get_contents($path), true);
        // // dd($content);

        // // $arr_medicals = Excel::toArray(new MedicalsImport,$file);
        // //     // dd($arr_medicals);
        // //     foreach($arr_medicals[0] as $key_medical => $val_medical){
        // //         $index = $key_medical;
        // //         // $test [] = Date::excelToDateTimeObject($val_medical['tanggal_mcu'])->format('Y-m-d');
        // //         $employee = Employee::where('nik', $val_medical['nik'])->first();
        // //         if(!empty($employee)){
        // //             $id_employee = $employee->id;
        // //             $update = Medical::where('id_employees', $employee->id)->update([
        // //                 'tanggal_mcu' => Date::excelToDateTimeObject($val_medical['tanggal_mcu'])->format('Y-m-d')
        // //             ]); 
        // //         }
        // //     }
        // //     dd('selesai');
        // dd('maintenance');
    }
}
