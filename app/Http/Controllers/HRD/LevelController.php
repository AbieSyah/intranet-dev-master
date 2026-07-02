<?php

namespace App\Http\Controllers\HRD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Level;
use App\Models\Log;
use Yajra\DataTables\Facades\DataTables;

class LevelController extends Controller
{
    public function index(Request $request){
        $levels = Level::all();
        if($levels->isNotEmpty()){
            foreach ($levels as $level) {
                $index = $level->id;
                $data[$index] = array();
                $data[$index]['id'] = $level->id;
                $data[$index]['nama'] = $level->nama;
            }
        }else{
            $data = array();
        }

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('action', function ($data) {
                    if(\Auth::user()->can('hrd.master.level.update')){
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
        return view('pages.hrd.master.level.index');
    }

    public function edit(Request $request){
        $id = decrypt($request->input('id'));
        $level = Level::find($id);

        return response()->json($level);
    }

    public function store(Request $request){
        $data = $request->all();
        $level = Level::updateOrCreate(['id' => $data['id']], $data);

        if ($level->wasRecentlyCreated) {
            $user = auth()->user();
            //insert log user activity
            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'insert';
            $insert->description = 'Create New Level '.'"'.$data['nama'].'"';
            $insert->save();        
        }else{
            $user = auth()->user();
            //insert log user activity
            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'update';
            $insert->description = 'Modify Level '.'"'.$data['nama'].'"';
            $insert->save();        
        }

        return redirect()->route('level.index')->with('success', 'Add Level Successfully.');
    }
}
