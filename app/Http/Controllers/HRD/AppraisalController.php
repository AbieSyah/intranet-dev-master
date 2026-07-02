<?php

namespace App\Http\Controllers\HRD;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Log;
use App\Models\Master\Appraisal;
use App\Models\Position;
use App\Models\Section;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class AppraisalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $appraisal = Appraisal::with(['position', 'department', 'section'])->get();
            return DataTables::of($appraisal)
                ->editColumn('kpi_weight', function ($data) {
                    if ($data->form_type === 'B') {
                        return $data->kpi_weight . '%';
                    }
                    return $data->kpi_weight == 0 ? '-' : $data->kpi_weight;
                })
                ->editColumn('ap_weight', function ($data) {
                    if ($data->form_type === 'B') {
                        return $data->ap_weight . '%';
                    }
                    return $data->ap_weight == 0 ? '-' : $data->ap_weight;
                })
                ->editColumn('attendance', function ($data) {
                    if ($data->form_type === 'B') {
                        return $data->attendance . '%';
                    }
                    return $data->attendance == 0 ? '-' : $data->attendance;
                })
                ->addColumn('position_id', function ($data) {
                    $positionName = optional($data->position)->nama ?? '-';
                    $status = $data->status ?? '';
                    return $status ? "$positionName ($status)" : $positionName;
                })
                ->addColumn('department_id', function ($data) {
                    return optional($data->department)->name ?? '-';
                })
                ->addColumn('section_id', function ($data) {
                    return optional($data->section)->nama ?? '-';
                })
                ->addColumn('action', function ($data) {
                    if (auth()->user()->can('hrd.master.appraisal.update')) {
                        $btn = '<a href="' . route('appraisal.form', encrypt($data->id)) . '" data-toggle="tooltip" title="Edit" class="btn btn-warning btn-sm edit-btn"><i class="ri-quill-pen-line"></i></a>';
                        $btn .= '&nbsp;';
                        $btn .= '<a href="#" data-id="' . encrypt($data->id) . '" data-toggle="tooltip" title="Delete" class="btn btn-danger btn-sm delete-btn"><i class="ri-delete-bin-line"></i></a>';
                        return $btn;
                    }
                    return '';
                })
                ->rawColumns(['action'])
                ->addIndexColumn()
                ->make(true);
        }

        return view('pages.hrd.master.appraisal.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function form(string $id = null)
    {
        if ($id) $id = decrypt($id);
        $appraisal = Appraisal::find($id);
        $departments = Department::all();
        $sections = Section::all();
        $positions = Position::all();
        return view('pages.hrd.master.appraisal.form', compact('appraisal','departments','sections','positions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->input();

            // Insert or Update appraisal
            $appraisal = Appraisal::updateOrCreate(['id' => $data['id'] ?? null], $data);

            DB::commit();

            $user = auth()->user();
            $positionName = (optional($appraisal->position)->nama ?? '-') . ' (' . ($appraisal->status ?? '-') . ')';
            $action = $appraisal->wasRecentlyCreated ? 'insert' : 'update';
            $description = ($appraisal->wasRecentlyCreated ? 'Create New' : 'Modify') . ' Appraisal "' . $positionName . '"';

            // Insert log
            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => $action,
                'description' => $description,
            ]);

            return response()->json([
                'message' => "Appraisal \"$positionName\" has been saved",
                'redirect' => route('appraisal.index')
            ], 200);

        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {
            $id = decrypt($request->id);
            $appraisal = Appraisal::findOrFail($id);
            $positionName = optional($appraisal->position)->nama ?? '-';
            $appraisal->delete();

            // Insert log
            $user = auth()->user();
            $log = new Log;
            $log->user_id = $user->id;
            $log->ip_address = $request->ip();
            $log->action = 'delete';
            $log->description = 'Delete Appraisal "' . $positionName . '"';
            $log->save();

            return redirect()->route('appraisal.index')->with('error', 'Delete Appraisal Successfully');
        } catch (Exception $e) {
            return redirect()->route('appraisal.index')->with('error', 'Failed to delete appraisal: ' . $e->getMessage());
        }
    }
}
