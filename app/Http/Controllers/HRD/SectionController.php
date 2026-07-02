<?php

namespace App\Http\Controllers\HRD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\Log;
use Yajra\DataTables\Facades\DataTables;

class SectionController extends Controller
{
    public function index(Request $request){
        $sections = Section::all();
        if($sections->isNotEmpty()){
            foreach ($sections as $section) {
                $index = $section->id;
                $data[$index] = array();
                $data[$index]['id'] = $section->id;
                $data[$index]['nama'] = $section->nama;
            }
        }else{
            $data = array();
        }

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('action', function ($data) {
                    if(\Auth::user()->can('hrd.master.section.update')){
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
        return view('pages.hrd.master.section.index');
    }

    public function edit(Request $request){
        $id = decrypt($request->input('id'));
        $section = Section::find($id);

        return response()->json($section);
    }

    public function store(Request $request){
        $data = $request->all();
        $section = Section::updateOrCreate(['id' => $data['id']], $data);

        if ($section->wasRecentlyCreated) {
            $user = auth()->user();
            //insert log user activity
            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'insert';
            $insert->description = 'Create New Section '.'"'.$data['nama'].'"';
            $insert->save();        
        }else{
            $user = auth()->user();
            //insert log user activity
            $insert = new Log;
            $insert->user_id = $user->id;
            $insert->ip_address = $request->ip();
            $insert->action = 'update';
            $insert->description = 'Modify Section '.'"'.$data['nama'].'"';
            $insert->save();        
        }

        return redirect()->route('section.index')->with('success', 'Add Section Successfully.');
    }
}
