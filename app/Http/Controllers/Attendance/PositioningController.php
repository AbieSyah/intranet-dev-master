<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance\Positioning;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class PositioningController extends Controller
{
    public function index(Request $request){
    $areas = DB::table('areas')->get();
    if($request->ajax()){
        $positioning = DB::table('master_positioning')
        ->join('areas','master_positioning.area','=','areas.id')
        ->select(
            'master_positioning.id',
            'areas.name',
            'master_positioning.latitude',
            'master_positioning.longitude',
            'master_positioning.max_distance'
        );
        return DataTables::of($positioning)
        ->addColumn('action', function ($data) {
            $button = '';
                $button .= '<button data-id="'.encrypt($data->id).'"
                class="btn btn-warning btn-sm edit-btn">
                <i class="ri-edit-line"></i></button>';
                $button .= ' <button data-id="'.encrypt($data->id).'"
                class="btn btn-danger btn-sm delete-btn">
                <i class="ri-delete-bin-line"></i></button>';
            return $button;
        })
        ->rawColumns(['action'])
        ->addIndexColumn()
        ->make(true);
    }
    return view("pages.attendance.master.positioning.index", compact('areas'));
}

    public function store(Request $request)
    {
        try{
        $request->validate([
            'area' => 'required|integer|exists:areas,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'max_distance' => 'required|numeric|min:0'
        ]);
        Positioning::create([
            'area' => $request->area,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'max_distance' => $request->max_distance
        ]);
            return response()->json([
            'status' => 'success',
            'message' => 'Posisi berhasil disimpan'
        ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ],500);

        }
    }
    public function edit(Request $request){
        $id = decrypt($request->input('id'));
        $positioning = Positioning::find($id);
        return response()->json($positioning);
    }
    public function update(Request $request){
        try{
        Positioning::where('id', $request->id)->update([
                'area' => $request->area,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'max_distance' => $request->max_distance,
                ]);
            return response()->json([
            'status' => 'success',
            'message' => 'Posisi berhasil diupdate'
        ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ],500);

        }
    }

    public function destroy( Request $request){
        try {
            $id = decrypt($request->id);
            $positioning = Positioning::findOrFail($id);
            $positioning->delete();
            // $user = auth()->user();
            // Log::create([
            //     'user_id' => $user->id,
            //     'ip_address' => $request->ip(),
            //     'action' => 'delete',
            //     'description' => 'Delete Hiring "' . $hiringName . '"',
            // ]);
            return response()->json([
            'status' => 'success',
            'message' => 'Posisi berhasil dihapus'
        ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ],500);

        }
    }
}
