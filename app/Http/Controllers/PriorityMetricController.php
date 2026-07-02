<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\PriorityMetric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class PriorityMetricController extends Controller
{
    public function getData(Request $request) 
    {
        $priorityMetrics = PriorityMetric::query();

        if($request->ajax()) {
            $priorityMetrics;
            $filter = $request->filter;

            if ($filter == PriorityMetric::TYPE_IMPACT) {
                $priorityMetrics = $priorityMetrics->where('type', 'impact');
            } else if($filter == PriorityMetric::TYPE_SCOPE) {
                $priorityMetrics = $priorityMetrics->where('type', 'scope');
            } else if($filter == PriorityMetric::TYPE_URGENCY) {
                $priorityMetrics = $priorityMetrics->where('type', 'urgency');
            }

            $priorityMetrics = $priorityMetrics->orderBy('type', 'desc')->orderBy('score', 'desc')->get();

            return DataTables::of($priorityMetrics)
                ->addColumn('delete_url', fn($milestone) => route('priority-metric.destroy', encrypt($milestone->id)))
                ->addColumn('encrypted_id', fn($milestone) => encrypt($milestone->id))
                ->addIndexColumn()
                ->make(true);
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.administrator.service-management.priority-metric.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function checkDuplicate(Request $request) 
    {
        $type = $request->type;
        $score = $request->score;

        $exists = PriorityMetric::where('type', $type)->where('score', $score)->first();

        return response()->json([
            'exists' => !!$exists,
            'data' => $exists
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function upsert(Request $request, $id = null)
    {
        if (!$request->ajax()) return response()->json(['status' => 'error', 'message' => 'Forbidden'], 403);

        $isUpdate = !is_null($id);

        $rules = [
            'description' => 'required|string|max:700',
            'definition' => 'required|string|max:700',
            // 'type' => 'required|string|in:impact,scope,urgency',
            // 'score' => 'required|integer|min:1'
        ];
        
        if (!$isUpdate) {
            $rules['type'] = 'required|in:' . implode(',', [PriorityMetric::TYPE_IMPACT, PriorityMetric::TYPE_URGENCY, PriorityMetric::TYPE_SCOPE]);
            $rules['score'] = 'required|integer|min:1';
        }

        $validated = $request->validate($rules);

        if (!$isUpdate) {
            $validated['type'] = strtolower($validated['type']);
            $validated['definition'] = ucfirst($validated['definition']);
        }

        try {
            if ($isUpdate) {
                $priorityMetric = PriorityMetric::findOrFail(decrypt($id));
                $priorityMetric->update($validated);
                $action = 'update';
            } else {
                $priorityMetric = PriorityMetric::create($validated);
                $action = 'insert';
            }

            Log::create([
                'user_id'     => Auth::id(),
                'ip_address'  => $request->ip(),
                'action'      => $action,
                'description' => ucfirst($action) . " $priorityMetric->type: $priorityMetric->description (Score: $priorityMetric->score)"
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => $isUpdate ? 'Data updated successfully!' : 'New data added successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Transaction failed. Please try again.',
                'error'   => $e->getMessage()
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
    public function edit(Request $request, $id)
    {
        $priorityMetric = PriorityMetric::findOrFail(decrypt($id));
        $priorityMetric->id = encrypt($priorityMetric->id);
        return response()->json($priorityMetric);
    }

    public function destroy($id, Request $request) 
    {
        try {
            $priorityMetric = PriorityMetric::findOrFail(decrypt($id));

            $priorityMetric->delete();
            
            $user = Auth::user();

            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'delete',
                'description' => "Delete $priorityMetric->type by {$user->employee->fullname} with description $priorityMetric->description and score $priorityMetric->score"
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Priority metric successfully deleted',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting priority metric data',
                'error' => $e->getMessage()
            ]);
        }
    }
}
