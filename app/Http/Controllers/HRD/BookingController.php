<?php

namespace App\Http\Controllers\HRD;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Booking;
use App\Models\User;
use App\Models\Log;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Carbon\CarbonInterval;
use Auth;
use Response;

class BookingController extends Controller
{
    //hrd
    public function index(Request $request){
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
        return view('pages.hrd.booking-room.index', compact('user', 'data_room', 'data_booking'));
    }

    public function store(Request $request){
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

    public function view(Request $request){
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

    public function update(Request $request){
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

    public function delete(Request $request){
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

    public function update_series(Request $request){
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

    public function delete_series(Request $request){
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
    
    //employee
    public function emp_index(Request $request){
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
        return view('pages.employee.booking-room.index', compact('user', 'data_room', 'data_booking'));
    }

    public function emp_store(Request $request){
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

    public function emp_view(Request $request){
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

    public function emp_update(Request $request){
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

    public function emp_delete(Request $request){
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

    public function emp_update_series(Request $request){
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

    public function emp_delete_series(Request $request){
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
}
