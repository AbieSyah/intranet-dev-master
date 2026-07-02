<?php

namespace App\Http\Controllers\Attendance;
use App\Models\Attendance\WorkHour;
use App\Http\Controllers\Controller;
use App\Models\Attendance\WorkHourDetail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class WorkHourController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $workhour = WorkHour::select('id','work_name')->get();
            return DataTables::of($workhour)
                ->addColumn('action', function ($data) {
                    $button = '';
                    // DETAIL BUTTON
                    $button .= '<button title="Detail" data-id="' . encrypt($data->id) . '"
                                class="btn btn-info btn-sm detail-btn">
                                <i class="ri-eye-line"></i></button> ';
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
        return view("pages.attendance.master.workhour.index");
    }
    public function show(Request $request)
    {
        $id = decrypt($request->id);
        $workhour = WorkHour::with('details')->findOrFail($id);
        return response()->json($workhour);
    }
    public function store(Request $request)
    {
        try {
            $request->validate([
                'work_name' => 'required|string|max:255',

                'day' => 'required|array',
                'day.*' => 'distinct',

                'work_in' => 'required|array',
                'work_in.*' => 'required',

                'break_duration' => 'required|array',
                'break_duration.*' => 'required',

                'work_out' => 'required|array',
                'work_out.*' => 'required',
            ]);
             DB::beginTransaction();
            // create workhour
            $workhour = WorkHour::create([
                'work_name' => $request->work_name
            ]);
            // create details
            foreach ($request->day as $key => $day) {
                WorkHourDetail::create([
                    'workhour_id' => $workhour->id,
                    'day' => $day,
                    'work_in' => $request->work_in[$key],
                    'break_duration' => $request->break_duration[$key],
                    'work_out' => $request->work_out[$key],
                    'notes' => $request->notes[$key] ?? null
                ]);
            }
            DB::commit();
            return response()->json([
            'status' => 'success',
            'message' => 'Work hour berhasil disimpan'
        ]);

        } catch (\Exception $e) {

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ],500);

        }
    }

public function edit(Request $request)
{
    $id = decrypt($request->input('id'));
    $workhour = WorkHour::with('details')->findOrFail($id);
    return response()->json([
        'id' => $workhour->id,
        'work_name' => $workhour->work_name,
        'details' => $workhour->details
    ]);
}
public function update(Request $request)
{
    DB::beginTransaction();

    try {

        $request->validate([
            'work_name' => 'required|string|max:255',

            'day' => 'required|array',
            'day.*' => 'distinct',

            'work_in' => 'required|array',
            'work_in.*' => 'required',

            'break_duration' => 'required|array',
            'break_duration.*' => 'required',

            'work_out' => 'required|array',
            'work_out.*' => 'required',
        ]);

        $workhour = WorkHour::findOrFail($request->id);

        $workhour->update([
            'work_name' => $request->work_name
        ]);

        // delete old details
        WorkHourDetail::where('workhour_id', $workhour->id)->delete();

        // insert new detail
        foreach ($request->day as $key => $day) {

            WorkHourDetail::create([
                'workhour_id' => $workhour->id,
                'day' => $day,
                'work_in' => $request->work_in[$key],
                'break_duration' => $request->break_duration[$key],
                'work_out' => $request->work_out[$key],
                'notes' => $request->notes[$key] ?? null
            ]);

        }

        DB::commit();

        return response()->json([
            'status' => 'success',
            'message' => 'Work hour berhasil diupdate'
        ]);

    } catch (\Exception $e) {

        DB::rollBack();

        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ],500);

    }
}


    public function destroy( Request $request){
        try {
            $id = decrypt($request->id);
            $workhour = WorkHour::findOrFail($id);
            $workhour->delete();
            // $user = auth()->user();
            // Log::create([
            //     'user_id' => $user->id,
            //     'ip_address' => $request->ip(),
            //     'action' => 'delete',
            //     'description' => 'Delete Hiring "' . $hiringName . '"',
            // ]);
        return response()->json([
            'status' => 'success',
            'message' => 'Work hour berhasil di Delete'
        ]);

        }catch(\Exception $e){

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ],500);

        }
    }
}
