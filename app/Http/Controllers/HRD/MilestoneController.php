<?php

namespace App\Http\Controllers\HRD;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeMilestone;
use App\Models\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;

class MilestoneController extends Controller
{
    public function index($id)
    {
        $id = decrypt($id);
        $employee = Employee::find($id)->load('milestones');

        return view("pages.hrd.employee.milestone.index", [
            'employee' => $employee
        ]);
    }

    public function loadMilestones(Request $request, $id) 
    {
        $id = decrypt($id);
        $employee = Employee::find($id)->load('milestones');

        if($request->ajax()) {
            $milestones = $employee->milestones->sortBy('category');
            $filter = $request->filter;

            if ($filter == "disciplinary") {
                $milestones = $milestones->where('category', 'disciplinary');
            } else if($filter == "career") {
                $milestones = $milestones->where('category', 'career');
            } else if($filter == "reward") {
                $milestones = $milestones->where('category', 'reward');
            }

            return DataTables::of($milestones)
                ->addColumn('delete_url', fn($milestone) => route('employee.milestone.delete', encrypt($milestone->id)))
                ->addColumn('encrypted_id', fn($milestone) => encrypt($milestone->id))
                ->addColumn('formated_date', fn($milestone) => Carbon::parse($milestone->date)->format('d-M-Y'))
                ->addIndexColumn()
                ->make(true);
        }
    }

    public function store(Request $request, $id) {
        if ($request->ajax()) {
            $id = decrypt($id);
            $employee = Employee::find($id);

            try {
                $milestone = EmployeeMilestone::create([
                    'employee_id' => $employee->id,
                    'category' => $request->category,
                    'type' => $request->type,
                    'date' => Carbon::createFromFormat('d-M-Y', $request->date)->format('Y-m-d'),
                    'description' => $request->description,
                ]);

                Log::create([
                    'user_id' => Auth::user()->id,
                    'ip_address' => $request->ip(),
                    'action' => 'insert',
                    'description' => "Add $milestone->category into $employee->fullname({$employee->department->name})"
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => "New $milestone->category successfully added into $employee->fullname"
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Error creating new data. Try again later',
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    public function edit(Request $request, $id)
    {
        $milestone = EmployeeMilestone::findOrFail(decrypt($id));
        $milestone->id = encrypt($milestone->id);
        return response()->json($milestone);
    }

    public function update(Request $request, $id) 
    {
        $validated = $request->validate([
            'date'        => 'required|date',
            'description' => 'required|string|max:700',
        ]);

        $validated['type'] = $request->type;
        $validated['date'] = Carbon::createFromFormat('d-M-Y', $request->date)->format('Y-m-d');

        try {
            $milestone = EmployeeMilestone::findOrFail(decrypt($id));
            $milestone->update($validated);

            $employee = $milestone->employee;

            Log::create([
                'user_id' => Auth::user()->id,
                'ip_address' => $request->ip(),
                'action' => 'update',
                'description' => "Update $milestone->category from $employee->fullname({$employee->department->name})"
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Milestone updated successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update data.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id, Request $request) 
    {
        try {
            $milestone = EmployeeMilestone::findOrFail(decrypt($id));
            $employee = $milestone->employee;

            $milestone->delete();

            Log::create([
                'user_id' => Auth::user()->id,
                'ip_address' => $request->ip(),
                'action' => 'delete',
                'description' => "Delete $milestone->category from $employee->fullname({$employee->department->name})"
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Milestone successfully deleted',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error deleting milestone data',
                'error' => $e->getMessage()
            ]);
        }
    }
}
