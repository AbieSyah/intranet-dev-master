<?php

namespace App\Http\Controllers;

use App\Models\ItsmPriority;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ItsmPriorityController extends Controller
{
    public function getData(Request $request) {
        if($request->ajax()) {
            $data = ItsmPriority::latest()->get();

            return datatables()->of($data)
                ->addColumn('edit_url', function($row) {
                    return route('priority.edit', encrypt($row->id));
                })
                ->addColumn('delete_url', function($row) {
                    return route('priority.destroy', encrypt($row->id));
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $priorityColorMap = ItsmPriority::getColorMap();
        return view('pages.administrator.service-management.itsm-priority.index', [
            'priorityColorMap' => $priorityColorMap
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = [
            'level' => 'required',
            'min_score' => 'required|integer',
            'max_score' => 'required|integer|gte:min_score',
            'min_sla_hours' => 'nullable|integer',
            'max_sla_hours' => 'nullable|integer',
            'sla_label' => 'required|string'
        ];

        if($request->sla_label == ItsmPriority::SLA_LABEL_RANGE) {
            $validation['min_sla_hours'] = 'required|integer|min:0';
            $validation['max_sla_hours'] = 'required|integer|gte:min_sla_hours';
        } else {
            $validation['min_sla_hours'] = 'nullable|integer';
            $validation['max_sla_hours'] = 'nullable|integer';
        }

        $data = Validator::make($request->all(), $validation);

        $data->validate();

        ItsmPriority::create($data->validated());
        return response()->json(['message' => 'Priority created successfully!']);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $priority = ItsmPriority::findOrFail(decrypt($id));
        $priority->edit_url = route('priority.update', encrypt($priority->id));
        return response()->json($priority);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $priority = ItsmPriority::findOrFail(decrypt($id));

        $validation = [
            'level' => 'required',
            'min_score' => 'required|integer',
            'max_score' => 'required|integer|gte:min_score',
            'min_sla_hours' => 'nullable|integer',
            'max_sla_hours' => 'nullable|integer',
            'sla_label' => 'required|string'
        ];

        if($request->sla_label == ItsmPriority::SLA_LABEL_RANGE) {
            $validation['min_sla_hours'] = 'required|integer|min:0';
            $validation['max_sla_hours'] = 'required|integer|gte:min_sla_hours';
        } else {
            $validation['min_sla_hours'] = 'nullable|integer';
            $validation['max_sla_hours'] = 'nullable|integer';
        }

        $data = Validator::make($request->all(), $validation);
        $validated = $data->validate();

        if (!isset($validated['min_sla_hours'])) {
            $validated['min_sla_hours'] = null;
        }

        $priority->update($validated);

        return response()->json(['message' => 'Priority updated successfully!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        ItsmPriority::findOrFail(decrypt($id))->delete();
        return response()->json(['message' => 'Data has been removed.']);
    }
}
