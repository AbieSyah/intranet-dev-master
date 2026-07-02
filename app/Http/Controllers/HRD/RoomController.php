<?php

namespace App\Http\Controllers\HRD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Models\Room;
use App\Models\Log;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class RoomController extends Controller
{
    public function index(Request $request){
        $query = Room::get();
        if($query->isNotEmpty()){
            foreach($query as $qry){
                $index = $qry->id;
                $data[$index] = array();
                $data[$index]['id'] = $qry->id;
                $data[$index]['nama'] = $qry->nama;
            }
        }else{
            $data = array();
        }
        if($request->ajax()){         
            return DataTables::of($data)
            ->addColumn('action', function ($data) {
                $button = '<button data-toggle="tooltip" title="Edit" data-id="' . encrypt($data['id']) . '" data-original-title="Edit" class="btn btn-warning btn-sm edit-btn"><i class="ri-edit-line"></i></button>';
                return $button;
            })
            ->addIndexColumn()
            ->make(true);
        }
        return view('pages.hrd.master.room.index');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $id = $request->input('id');
            $name = $request->input('name');

            $room = Room::updateOrCreate(['id' => $id], ['nama' => $name]);

            DB::commit();

            if ($room->wasRecentlyCreated) {
                $user = auth()->user();
                //insert log user activity
                $insert = new Log;
                $insert->user_id = $user->id;
                $insert->ip_address = $request->ip();
                $insert->action = 'insert';
                $insert->description = 'Create New Room '.'"'.$name.'"';
                $insert->save();
            }else{
                $user = auth()->user();
                //insert log user activity
                $insert = new Log;
                $insert->user_id = $user->id;
                $insert->ip_address = $request->ip();
                $insert->action = 'update';
                $insert->description = 'Modify Room '.'"'.$name.'"';
                $insert->save();
            }


            return response()->json(['message' => "$room->nama has been saved"], 200);
        } catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    public function edit(Request $request)
    {
        $id = decrypt($request->input('id'));
        $room = Room::find($id);

        if (!$room) {
            return response()->json(['message' => 'Room not found'], 404);
        }

        return response()->json($room);
    }
}
