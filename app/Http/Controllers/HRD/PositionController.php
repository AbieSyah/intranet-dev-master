<?php

namespace App\Http\Controllers\HRD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Position;
use App\Models\Log;
use Yajra\DataTables\Facades\DataTables;

class PositionController extends Controller
{
    public function index(Request $request){
        $positions = Position::all();
        if($positions->isNotEmpty()){
            foreach ($positions as $position) {
                $index = $position->id;
                $data[$index] = array();
                $data[$index]['id'] = $position->id;
                $data[$index]['nama'] = $position->nama;
            }
        }else{
            $data = array();
        }

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('action', function ($data) {
                    if(\Auth::user()->can('hrd.master.position.update')){
                        $button = '<button data-toggle="tooltip" title="Edit" data-id="' . encrypt($data['id']) . '" data-original-title="Edit" class="btn btn-warning btn-sm edit-btn"><i class="ri-edit-line"></i></button>';
                    }else{
                        $button = '';
                    }
                    return $button;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.master.position.index');
    }

    public function edit(Request $request){
        $id = decrypt($request->input('id'));
        $position = Position::find($id);

        return response()->json($position);
    }

    public function store(Request $request){
        $data = $request->all();
        $position = Position::updateOrCreate(['id' => $data['id']], $data);

        if ($position->wasRecentlyCreated) {
            $user = auth()->user();
            //insert log user activity
            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'insert';
            $insert->description = 'Create New Position '.'"'.$data['nama'].'"';
            $insert->save();        
        }else{
            $user = auth()->user();
            //insert log user activity
            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'update';
            $insert->description = 'Modify Position '.'"'.$data['nama'].'"';
            $insert->save();        
        }

        return redirect()->route('position.index')->with('success', 'Add Position Successfully.');
    }
}
