<?php

namespace App\Http\Controllers\HRD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Area;
use App\Models\Log;
use Exception;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AreaController extends Controller
{
    public function index(Request $request)
    {
        $areas = Area::all();
        foreach ($areas as $area) {
            $index = date("YmdHis", strtotime($area->created_at));
            $data[$index] = array();
            $data[$index]['id'] = $area->id;
            $data[$index]['kode'] = $area->kode;
            $data[$index]['nama'] = $area->name;
        }

        if ($request->ajax()) {
            return DataTables::of($data)
                ->addColumn('action', function ($data) {
                    $button = '<button data-toggle="tooltip" title="Edit" data-id="' . encrypt($data['id']) . '" data-original-title="Edit" class="btn btn-warning btn-sm edit-btn"><i class="ri-edit-line"></i></button>';
                    return $button;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.master.area.index');
    }

    public function edit(Request $request)
    {
        $id = decrypt($request->input('id'));
        $permission = Area::find($id);

        if (!$permission) {
            return response()->json(['message' => 'Area not found'], 404);
        }

        return response()->json($permission);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->input();
            $area = Area::updateOrCreate(['id' => $data['id']], $data);

            DB::commit();
            if ($area->wasRecentlyCreated) {
                $user = auth()->user();
                //insert log user activity
                $insert = new Log;
                $insert->user_id = $user->id;
                $insert->ip_address = $request->ip();
                $insert->action = 'insert';
                $insert->description = 'Create New Area '.'"'.$data['name'].'"';
                $insert->save();
            }else{
                $user = auth()->user();
                //insert log user activity
                $insert = new Log;
                $insert->user_id = $user->id;
                $insert->ip_address = $request->ip();
                $insert->action = 'update';
                $insert->description = 'Modify Area '.'"'.$data['name'].'"';
                $insert->save();
            }

            return response()->json(['message' => "Area $area->name has been saved"], 200);
        } catch (Exception $e) {
            DB::rollback();

            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }       
    }
}
