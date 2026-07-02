<?php

namespace App\Http\Controllers\HRD;

use App\Exports\LineApprovalEmployeesExport;
use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Log;
use App\Models\Master\Building;
use App\Models\Master\LineApproval;
use App\Models\Master\LineApprovalEmployee;
use App\Models\Position;
use App\Models\Section;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class LineApprovalController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $lineapproval = LineApproval::with(['department', 'area', 'building'])->withCount('employees')->get();
            return DataTables::of($lineapproval)
                ->addColumn('department_id', function ($data) {
                    return optional($data->department)->name ?? 'ALL';
                })
                ->addColumn('area_id', function ($data) {
                    return optional($data->area)->name ?? 'ALL';
                })
                ->addColumn('building_id', function ($data) {
                    return optional($data->building)->nama ?? 'ALL';
                })
                ->addColumn('total_employees', function ($data) {
                    return $data->employees_count;
                })
                ->addColumn('action', function ($data) {
                    if (Auth::user()->can('hrd.master.line-approval.update')) {
                        $btn = '<a href="' . route('line-approval.form', encrypt($data->id)) . '" data-toggle="tooltip" title="Edit" class="btn btn-warning btn-sm edit-btn"><i class="ri-quill-pen-line"></i></a>';
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
        return view('pages.hrd.master.line-approval.index');
    }

    public function form(string $id = null)
    {
        $lineapproval = null;
        if ($id) {
            $id = decrypt($id);
            $lineapproval = LineApproval::findOrFail($id);
        }
        $departments = Department::all();
        $areas = Area::all();
        $buildings = Building::all();
        $positions = Position::all();
        $sections = Section::all();
        $approveds = Employee::whereNot('status', 'TERMINATED')->get();
        return view('pages.hrd.master.line-approval.form', compact('lineapproval','departments','areas','buildings','positions','sections','approveds'));
    }

    public function getEmployees(Request $request)
    {
        $query = Employee::with(['position', 'section', 'department']);
        $query->whereNot('status', 'TERMINATED');
        
        $lineApprovalId = $request->input('line_approval_id');
        $approvalType = $request->input('approval_type');

        if ($lineApprovalId) {
            $lineApproval = LineApproval::find($lineApprovalId);
            if ($lineApproval) {
                $employees = $lineApproval->employees()->with(['position', 'section', 'department'])->get()->map(function($employee) {
                    $statusBadge = '';
                    if ($employee->status == 'PERMANENT') {
                        $statusBadge = '<span class="badge text-bg-success">PERMANENT</span>';
                    } elseif ($employee->status == 'CONTRACT') {
                        $statusBadge = '<span class="badge text-bg-primary">CONTRACT</span>';
                    } elseif ($employee->status == 'PROBATION') {
                        $statusBadge = '<span class="badge text-bg-warning">PROBATION</span>';
                    }
                    return [
                        'id' => $employee->id,
                        'nik' => $employee->nik,
                        'fullname' => $employee->fullname,
                        'position' => $employee->position->nama ?? '-',
                        'section' => $employee->section->nama ?? '-',
                        'status' => $statusBadge,
                        'action' => '<button type="button" class="btn btn-danger btn-sm remove-employee" data-id="' . $employee->id . '"><i class="ri-delete-bin-line"></i></button>'
                    ];
                });
                return response()->json($employees);
            }
        }
        
        if ($approvalType) {
            $query->whereNotIn('id', function($subQuery) use ($approvalType) {
                $subQuery->select('employee_id')
                         ->from('master_line_approval_employees as lae')
                         ->join('master_line_approval as la', 'la.id', '=', 'lae.line_approval_id')
                         ->where('la.approval_type', $approvalType);
            });
        }
        
        if ($request->filled('department_id') && $request->department_id != 'ALL') {
            $query->where('department_id', $request->department_id);
        }
        if ($request->filled('area_id') && $request->area_id != 'ALL') {
            $query->where('area_id', $request->area_id);
        }
        if ($request->filled('building_id') && $request->building_id != 'ALL') {
            $query->where('building_id', $request->building_id);
        }
        if ($request->filled('position_id') && $request->position_id != 'ALL') {
            $query->where('position_id', $request->position_id);
        }
        if ($request->filled('section_id') && $request->section_id != 'ALL') {
            $query->where('section_id', $request->section_id);
        }

        $employees = $query->get()->map(function($employee) {
            $statusBadge = '';
            if ($employee->status == 'PERMANENT') {
                $statusBadge = '<span class="badge text-bg-success">PERMANENT</span>';
            } elseif ($employee->status == 'CONTRACT') {
                $statusBadge = '<span class="badge text-bg-primary">CONTRACT</span>';
            } elseif ($employee->status == 'PROBATION') {
                $statusBadge = '<span class="badge text-bg-warning">PROBATION</span>';
            }
            return [
                'id' => $employee->id,
                'nik' => $employee->nik,
                'fullname' => $employee->fullname,
                'position' => $employee->position->nama ?? '-',
                'section' => $employee->section->nama ?? '-',
                'status' => $statusBadge,
                'action' => '<button type="button" class="btn btn-danger btn-sm remove-employee" data-id="' . $employee->id . '"><i class="ri-delete-bin-line"></i></button>'
            ];
        });
        return response()->json($employees);
    }

    public function store(Request $request)
    {
        // dd($request->all());

        $approvalType = $request->input('approval_type');
        $lineApprovalId = $request->input('id');
        $isEditMode = !empty($lineApprovalId);
        $lineApproval = $isEditMode ? LineApproval::findOrFail($lineApprovalId) : new LineApproval();
        $currentApprovalType = $isEditMode ? $lineApproval->approval_type : $approvalType; 
        $rules = [
            'id' => 'nullable|exists:master_line_approval,id',
            'group_name' => 'required|string',
            'employee_ids' => 'required|string',
        ];
        $rules['approval_type'] = 'nullable|string'; 
        if (!$isEditMode) {
            $rules['department_id'] = 'required|string';
            $rules['area_id'] = 'required|string';
            $rules['building_id'] = 'required|string';
            $rules['position_id'] = 'required|string';
            $rules['section_id'] = 'required|string';
        } else {
            $rules['department_id'] = 'nullable|string';
            $rules['area_id'] = 'nullable|string';
            $rules['building_id'] = 'nullable|string';
            $rules['position_id'] = 'nullable|string';
            $rules['section_id'] = 'nullable|string';
        }

        for ($i = 1; $i <= 8; $i++) {
            $rules['approve_' . $i] = 'nullable|exists:employees,id';
        }

        $request->validate($rules);

        $employeeIds = json_decode($request->input('employee_ids'), true) ?? [];

        if (!empty($employeeIds)) {
            $duplicateEmployees = DB::table('master_line_approval_employees as lae')
                ->select('e.fullname')
                ->join('master_line_approval as la', 'la.id', '=', 'lae.line_approval_id')
                ->join('employees as e', 'e.id', '=', 'lae.employee_id')
                ->whereIn('lae.employee_id', $employeeIds)
                ->where('la.approval_type', $currentApprovalType)
                ->when($lineApprovalId, function ($query) use ($lineApprovalId) {
                    return $query->where('la.id', '!=', $lineApprovalId);
                })
                ->pluck('e.fullname');

            if ($duplicateEmployees->isNotEmpty()) {
                $employeeNames = $duplicateEmployees->unique()->implode(', ');
                $errorMessage = "The following employees are already included in a Line Approval with the type '{$approvalType}': {$employeeNames}.";
                return response()->json([
                    'message' => 'Already Included!',
                    'errors' => ['employee_ids' => [$errorMessage]],
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            $data = $request->all();
            
            $lineApprovalData = [
                'group_name' => $data['group_name'],
                'approval_type' => $currentApprovalType,
            ];

            for ($i = 1; $i <= 8; $i++) {
                $lineApprovalData['approve_' . $i] = $data['approve_' . $i] ?? null;
            }
            // Drafter
            $lineApprovalData['drafter'] = $data['drafter'] ?? null;
            if ($isEditMode) {
                $lineApproval->fill($lineApprovalData); 
                $lineApproval->save();
            } else {
                // dd($data);
                if ($data['approval_type'] == "Asset Disposal") {
                    return response()->json([
                        'message' => 'Asset Disposal master line already created, Cannot create duplicate entry.',
                        'code' => 409
                    ], 409);
                }

                $lineApprovalData['approval_type'] = $data['approval_type'];
                $lineApprovalData['department_id'] = ($data['department_id'] !== 'ALL') ? $data['department_id'] : null;
                $lineApprovalData['area_id'] = ($data['area_id'] !== 'ALL') ? $data['area_id'] : null;
                $lineApprovalData['building_id'] = ($data['building_id'] !== 'ALL') ? $data['building_id'] : null;
                $lineApprovalData['position_id'] = ($data['position_id'] !== 'ALL') ? $data['position_id'] : null;
                $lineApprovalData['section_id'] = ($data['section_id'] !== 'ALL') ? $data['section_id'] : null;
                $lineApproval = LineApproval::create($lineApprovalData);
            }
            
            if ($approvalType !== 'Asset Disposal') {
                $lineApproval->employees()->sync($employeeIds);
            }
            DB::commit();
            
            $user = auth()->user();
            $action = $lineApproval->wasRecentlyCreated ? 'insert' : 'update';
            $baseDescription = ($lineApproval->wasRecentlyCreated ? 'Create New' : 'Modify') . ' Line Approval';
            if (!empty($lineApproval->group_name)) {
                $baseDescription .= ' "' . ($lineApproval->group_name ?? '-') . '"';
            }
            $baseDescription .= ' (' . ($lineApproval->approval_type ?? '-') . ')';
            if (!empty($lineApproval->department->name)) {
                $baseDescription .= ' for Department "' . ($lineApproval->department->name ?? 'N/A') . '"';
            }
            $description = $baseDescription;
            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => $action,
                'description' => $description,
            ]);
            
            return response()->json([
                'message' => 'Line Approval "' . ($lineApproval->group_name ?? 'Group') . '" has been saved.',
                'redirect' => route('line-approval.index')
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $id = decrypt($request->id);
            $lineApproval = LineApproval::with('department')->findOrFail($id);
            DB::beginTransaction();
            $approvalType = $lineApproval->approval_type;
            $groupName = $lineApproval->group_name ?? null;
            $departmentName = $lineApproval->department->name ?? null; 
            $lineApproval->delete();
            DB::commit();
            $description = 'Delete Line Approval';
            if (!empty($groupName)) {
                $description .= ' "' . ($groupName ?? '-') . '"';
            }
            $description .= ' (' . ($lineApproval->approval_type ?? '-') . ')';
            if (!empty($departmentName)) {
                $description .= ' for Department "' . $departmentName . '"';
            }
            $user = auth()->user();
            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'delete',
                'description' => $description,
            ]);
            return redirect()->route('line-approval.index')->with('success', 'Delete Line Approval Successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('line-approval.index')->with('error', 'Failed to delete Line Approval: ' . $e->getMessage());
        }
    }

    public function export_xlsx(Request $request)
    {
        $now = Carbon::now()->format('Ymd_His');
        $fileName = 'Line_Approvals_' . $now . '.xlsx';
        return Excel::download(new LineApprovalEmployeesExport, $fileName);
    }

    public function getEligibleEmployees(Request $request)
    {
        $lineApprovalId = $request->input('line_approval_id');

        // Filter yang dikirim dari form utama (yang statusnya disabled)
        $departmentId = $request->input('department_id');
        $areaId = $request->input('area_id');
        $buildingId = $request->input('building_id');
        $positionId = $request->input('position_id');
        $sectionId = $request->input('section_id');

        // 1. Ambil ID karyawan yang SUDAH ADA di grup ini (untuk dikecualikan)
        $existingEmployeeIds = DB::table('master_line_approval_employees')
            ->where('line_approval_id', $lineApprovalId)
            ->pluck('employee_id');

        // 2. Bangun query: Karyawan yang tidak TERMINATED DAN belum ada di grup ini
        $query = Employee::with(['position', 'section', 'department'])
            ->whereNot('status', 'TERMINATED')
            ->whereNotIn('id', $existingEmployeeIds);
        
        // 3. Terapkan Filter yang DISABLED dari form utama
        if ($departmentId && $departmentId != 'ALL') {
            $query->where('department_id', $departmentId);
        }
        if ($areaId && $areaId != 'ALL') {
            $query->where('area_id', $areaId);
        }
        if ($buildingId && $buildingId != 'ALL') {
            $query->where('building_id', $buildingId);
        }
        if ($positionId && $positionId != 'ALL') {
            $query->where('position_id', $positionId);
        }
        if ($sectionId && $sectionId != 'ALL') {
            $query->where('section_id', $sectionId);
        }

        $employees = $query->get()->map(function($employee) {
            $statusBadge = '';
            if ($employee->status == 'PERMANENT') {
                $statusBadge = '<span class="badge text-bg-success">PERMANENT</span>';
            } elseif ($employee->status == 'CONTRACT') {
                $statusBadge = '<span class="badge text-bg-primary">CONTRACT</span>';
            } elseif ($employee->status == 'PROBATION') {
                $statusBadge = '<span class="badge text-bg-warning">PROBATION</span>';
            }
            
            // Data di modal tidak memerlukan kolom Action (Delete), hanya Checkbox
            return [
                'id' => $employee->id,
                'nik' => $employee->nik,
                'fullname' => $employee->fullname,
                'position' => $employee->position->nama ?? '-',
                'section' => $employee->section->nama ?? '-',
                'status' => $statusBadge,
            ];
        });

        return response()->json($employees);
    }
}
