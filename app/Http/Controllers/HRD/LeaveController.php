<?php

namespace App\Http\Controllers\HRD;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Models\Leave;
use App\Models\Log;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class LeaveController extends Controller
{
    public function index(Request $request)
    {

        if ($request->ajax()) {
            $query = Leave::all();
            return DataTables::of($query)
                ->addColumn('action', function ($data) {
                    $button = '<button data-toggle="tooltip" title="Edit" data-id="' . encrypt($data['id']) . '" data-original-title="Edit" class="btn btn-warning btn-sm edit-btn"><i class="ri-edit-line"></i></button>';
                    return $button;
                })
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.hrd.master.leave.index');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $id = $request->input('id');
            $name = $request->input('name');

            $leave = Leave::updateOrCreate(['id' => $id], ['nama' => $name]);

            DB::commit();

            if ($leave->wasRecentlyCreated) {
                $user = auth()->user();
                //insert log user activity
                $insert = new Log;
                $insert->user_id = $user->id;
                $insert->ip_address = $request->ip();
                $insert->action = 'insert';
                $insert->description = 'Create New Leave '.'"'.$name.'"';
                $insert->save();
            }else{
                $user = auth()->user();
                //insert log user activity
                $insert = new Log;
                $insert->user_id = $user->id;
                $insert->ip_address = $request->ip();
                $insert->action = 'update';
                $insert->description = 'Modify Leave '.'"'.$name.'"';
                $insert->save();
            }


            return response()->json(['message' => "$leave->nama has been saved"], 200);
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
        $leave = Leave::find($id);

        if (!$leave) {
            return response()->json(['message' => 'Leave not found'], 404);
        }

        return response()->json($leave);
    }
}
