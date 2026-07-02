<?php

namespace App\Http\Controllers\Recruitment;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Models\Master\Hiring;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class HiringController extends Controller
{
    public function index(Request $request){
        $hirings = Hiring::all();
        if($hirings->isNotEmpty()){
            foreach ($hirings as $hiring) {
                $index = $hiring->id;
                $data[$index] = array();
                $data[$index]['id'] = $hiring->id;
                $data[$index]['name'] = $hiring->name;
            }
        }else{
            $data = array();
        }
        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('action', function ($data) {
                    $button = '';
                    if (Auth::user()->can('hrd.master.hiring.update')) {
                        $button .= '<button data-toggle="tooltip" title="Edit" data-id="' . encrypt($data['id']) . '" data-original-title="Edit" class="btn btn-warning btn-sm edit-btn"><i class="ri-edit-line"></i></button>';
                    }
                    if (Auth::user()->can('hrd.master.hiring.delete')) {
                        $button .= ' <button data-toggle="tooltip" title="Delete" data-id="' . encrypt($data['id']) . '" data-original-title="Delete" class="btn btn-danger btn-sm delete-btn"><i class="ri-delete-bin-line"></i></button>';
                    }
                    return $button;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.master.hiring.index');
    }

    public function edit(Request $request){
        $id = decrypt($request->input('id'));
        $hiring = Hiring::find($id);
        return response()->json($hiring);
    }

    public function store(Request $request){
        $data = $request->all();
        $hiring = Hiring::updateOrCreate(['id' => $data['id']], $data);
        if ($hiring->wasRecentlyCreated) {
            $user = auth()->user();
            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'insert',
                'description' => 'Create New Hiring '.'"'.$data['name'].'"',
            ]);
        }else{
            $user = auth()->user();
            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'update',
                'description' => 'Modify Hiring '.'"'.$data['name'].'"',
            ]);
        }
        return redirect()->route('hiring.index')->with('success', 'Hiring '.'"'.$data['name'].'" has been saved');
    }

    public function destroy(Request $request)
    {
        try {
            $id = decrypt($request->id);
            $hiring = Hiring::findOrFail($id);
            $hiringName = $hiring->name ?? '-';
            $hiring->delete();
            $user = auth()->user();
            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'delete',
                'description' => 'Delete Hiring "' . $hiringName . '"',
            ]);
            return redirect()->route('hiring.index')->with('success', 'Hiring "' . $hiringName . '" has been deleted');
        } catch (Exception $e) {
            return redirect()->route('hiring.index')->with('error', 'Failed to delete Hiring: ' . $e->getMessage());
        }
    }
}
