<?php

namespace App\Http\Controllers\HRD;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Models\Master\Building;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class BuildingController extends Controller
{
    public function index(Request $request){
        $buildings = Building::all();
        $data = array();
        if($buildings->isNotEmpty()){
            foreach ($buildings as $building) {
                $index = $building->id;
                $data[$index] = array();
                $data[$index]['id'] = $building->id;
                $data[$index]['nama'] = $building->nama;
            }
        }else{
            $data = array();
        }

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('action', function ($data) {
                    $button = '';
                    if (Auth::user()->can('hrd.master.building.update')) {
                        $button .= '<button data-toggle="tooltip" title="Edit" data-id="' . encrypt($data['id']) . '" data-original-title="Edit" class="btn btn-warning btn-sm edit-btn"><i class="ri-edit-line"></i></button>';
                    }
                    if (Auth::user()->can('hrd.master.building.delete')) {
                        $button .= ' <button data-toggle="tooltip" title="Delete" data-id="' . encrypt($data['id']) . '" data-original-title="Delete" class="btn btn-danger btn-sm delete-btn"><i class="ri-delete-bin-line"></i></button>';
                    }
                    return $button;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.master.building.index');
    }

    public function edit(Request $request){
        $id = decrypt($request->input('id'));
        $building = Building::find($id);
        return response()->json($building);
    }

    public function store(Request $request){
        $data = $request->all();
        $building = Building::updateOrCreate(['id' => $data['id']], $data);
        if ($building->wasRecentlyCreated) {
            $user = auth()->user();
            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'insert',
                'description' => 'Create New Building '.'"'.$data['nama'].'"',
            ]);
        }else{
            $user = auth()->user();
            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'update',
                'description' => 'Modify Building '.'"'.$data['nama'].'"',
            ]);
        }
        return redirect()->route('building.index')->with('success', 'Building '.'"'.$data['nama'].' has been saved');
    }

    public function destroy(Request $request)
    {
        try {
            $id = decrypt($request->id);
            $building = Building::findOrFail($id);
            $buildingName = $building->nama ?? '-';
            $building->delete();
            $user = auth()->user();
            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'delete',
                'description' => 'Delete Building "' . $buildingName . '"',
            ]);
            return redirect()->route('hiring.index')->with('success', 'Building "' . $buildingName . '" has been deleted');
        } catch (Exception $e) {
            return redirect()->route('hiring.index')->with('error', 'Failed to delete Building: ' . $e->getMessage());
        }
    }
}
