<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance\LeaveSetting;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class LeaveSettingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $leave = LeaveSetting::select('id', 'type', 'description', 'min_years', 'max_years', 'number_of_days')->get();
            return DataTables::of($leave)
                ->addColumn('year_span', function($row){
                    if($row->min_years !== null && $row->max_years !== null){
                        return "{$row->min_years} - {$row->max_years} Tahun";
                    } elseif ($row->min_years !== null) {
                        return ">= {$row->min_years} Tahun";
                    } elseif ($row->max_years !== null) {
                        return "<= {$row->max_years} Tahun";
                    } else {
                        return '-';
                    }
                })
                ->addColumn('action', function ($data) {
                    $button = '';
                        $button .= '<button title="Edit" data-id="' . encrypt($data->id) . '"
                                    class="btn btn-warning btn-sm edit-btn">
                                    <i class="ri-edit-line"></i></button> ';
                        $button .= '<button title="Delete" data-id="' . encrypt($data->id) . '"
                                    class="btn btn-danger btn-sm delete-btn">
                                    <i class="ri-delete-bin-line"></i></button>';
                    return $button;
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }
        return view("pages.attendance.master.leave-setting.index");
    }
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'description' => 'required',
            'min_years' => 'nullable|integer|min:0',
            'max_years' => 'nullable|integer|min:0',
            'number_of_days' => 'required|numeric|min:0',
        ]);

        $leave = LeaveSetting::create([
            'type' => $request->type,
            'description' => $request->description,
            'min_years' => $request->min_years,
            'max_years' => $request->max_years,
            'number_of_days' => $request->number_of_days,
        ]);

        $user = Auth::user();
            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'insert',
                'description' => "{$user->employee->fullname} create new Leave Setting ({$leave->type}/{$leave->description})"
            ]);

        return response()->json(['success' => true]);
    }

    public function edit(Request $request)
    {
        $id = decrypt($request->id);
        $data = LeaveSetting::findOrFail($id);

        return response()->json($data);
    }

    public function update(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'description' => 'required',
            'min_years' => 'nullable|integer|min:0',
            'max_years' => 'nullable|integer|min:0',
            'number_of_days' => 'required|numeric|min:1',
        ]);

        $data = LeaveSetting::findOrFail($request->id);

        $data->update([
            'type' => $request->type,
            'description' => $request->description,
            'min_years' => $request->min_years,
            'max_years' => $request->max_years,
            'number_of_days' => $request->number_of_days,
        ]);
        $user = Auth::user();
            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'update',
                'description' => "{$user->employee->fullname} Update Leave Setting ({$data->type}/{$data->description})"
            ]);

        return response()->json(['success' => true]);
    }
    public function destroy(Request $request)
    {
        try {
            $id = decrypt($request->id);
            $data = LeaveSetting::findOrFail($id);
            // ambil data sebelum dihapus
            $type = $data->type;
            $description = $data->description;
            $data->delete();
            $user = Auth::user();
            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'delete',
                'description' => "{$user->employee->fullname} delete Leave Setting ({$type}/{$description})"
            ]);
            return response()->json([
                'success' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal menghapus data', 'data' => ['error' => $e->getMessage()]
            ], 500);
        }
    }
}
