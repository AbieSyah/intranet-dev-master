<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance\EmployeeWorkHour;
use App\Models\Attendance\GroupEmployee;
use App\Models\Attendance\GroupEmployeeWorkhours;
use App\Models\Attendance\WorkHour;
use App\Models\Employee;
use App\Models\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class GroupEmployeeWorkHourController extends Controller
{
    public function index(Request $request)
    {
        $areas = DB::table('areas')->pluck('name');
        $departments = DB::table('departments')->pluck('name');
        $sections = DB::table('master_section')->pluck('nama');
        $buildings = DB::table('master_building')->pluck('nama');
        if ($request->ajax()) {
            $data = GroupEmployeeWorkhours::withCount(['groupEmployees as total_employee']);
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('group_name',  function ($row){
                    return $row->name;
                })
                ->addColumn('total_employee', fn($row) => $row->total_employee)
                ->addColumn('action',function($row){

                    $button = '';
                        $button .= '<button title="Edit" data-id="'.encrypt($row->id).'"
                                    class="btn btn-warning btn-sm edit-btn">
                                    <i class="ri-edit-line"></i></button> ';
                        $button .= '<button title="Delete" data-id="'.encrypt($row->id).'"
                                    class="btn btn-danger btn-sm delete-btn">
                                    <i class="ri-delete-bin-line"></i></button>';

                    return $button;
                })
                ->rawColumns(['action','status'])
                ->make(true);
        }

        return view("pages.attendance.sub-menu.group-employee-workhour.index",compact('areas','departments','sections','buildings'));
    }
    public function employeeList(Request $request){
        $data = Employee::with(['area', 'department', 'section', 'building', 'groupEmployees.groupEmployeeWorkhour'])
            ->whereDoesntHave('groupEmployees')
            ->select('id', 'fullname', 'area_id', 'department_id', 'section_id', 'building_id');

        return DataTables::of($data)
            ->addColumn('area', fn($row) => $row->area?->name ?? '')
            ->addColumn('department', fn($row) => $row->department?->name ?? '')
            ->addColumn('section', fn($row) => $row->section?->nama ?? '')
            ->addColumn('building', fn($row) => $row->building?->nama ?? '')
            ->addColumn('group_name', fn($row) => '-')
            ->filterColumn('area', function ($query, $keyword) {
                $query->whereHas('area', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('department', function ($query, $keyword) {
                $query->whereHas('department', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('section', function ($query, $keyword) {
                $query->whereHas('section', function ($q) use ($keyword) {
                    $q->where('nama', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('building', function ($query, $keyword) {
                $query->whereHas('building', function ($q) use ($keyword) {
                    $q->where('nama', 'like', "%{$keyword}%");
                });
            })
            ->make(true);
    }
    public function findEmployee(Request $request)
    {
        $data = Employee::with([
                'area:id,name',
                'department:id,name',
                'section:id,nama',
                'building:id,nama',
                'groupEmployees.groupEmployeeWorkhour:id,name'
            ])
            ->select('id', 'nik', 'fullname', 'area_id', 'department_id', 'section_id', 'building_id');
        //FILTER LEBIH CLEAN
        if ($request->filled('area') && $request->area !== 'ALL') {
            $data->whereHas('area', fn($q) =>
                $q->where('name', $request->area)
            );
        }
        if ($request->filled('department') && $request->department !== 'ALL') {
            $data->whereHas('department', fn($q) =>
                $q->where('name', $request->department)
            );
        }
        if ($request->filled('section') && $request->section !== 'ALL') {
            $data->whereHas('section', fn($q) =>
                $q->where('nama', $request->section)
            );
        }
        if ($request->filled('building') && $request->building !== 'ALL') {
            $data->whereHas('building', fn($q) =>
                $q->where('nama', $request->building)
            );
        }
        return DataTables::of($data)
            ->addColumn('area', fn($row) => $row->area->name ?? '-')
            ->addColumn('department', fn($row) => $row->department->name ?? '-')
            ->addColumn('section', fn($row) => $row->section->nama ?? '-')
            ->addColumn('building', fn($row) => $row->building->nama ?? '-')
            // GROUP NAME (AMAN & CEPAT)
            ->addColumn('group_name', function ($row) {
                return $row->groupEmployees?->groupEmployeeWorkhour?->name ?? '-';
            })
            // FILTER GLOBAL
            ->filterColumn('area', function ($query, $keyword) {
                $query->whereHas('area', fn($q) =>
                    $q->where('name', 'like', "%{$keyword}%")
                );
            })
            ->filterColumn('department', function ($query, $keyword) {
                $query->whereHas('department', fn($q) =>
                    $q->where('name', 'like', "%{$keyword}%")
                );
            })
            ->filterColumn('section', function ($query, $keyword) {
                $query->whereHas('section', fn($q) =>
                    $q->where('nama', 'like', "%{$keyword}%")
                );
            })
            ->filterColumn('building', function ($query, $keyword) {
                $query->whereHas('building', fn($q) =>
                    $q->where('nama', 'like', "%{$keyword}%")
                );
            })
            // OPTIONAL: filter group_name
            ->filterColumn('group_name', function ($query, $keyword) {
                $query->whereHas('groupEmployees.groupEmployeeWorkhour', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->make(true);
    }
    public function create(Request $request){
        $workhours = WorkHour::with('details')->get();
        $areas = DB::table('areas')->pluck('name');
        $departments = DB::table('departments')->pluck('name');
        $sections = DB::table('master_section')->pluck('nama');
        $buildings = DB::table('master_building')->pluck('nama');

        if ($request->ajax()) {
            $data = Employee::with(['workhour'])
                ->select('id', 'employee_id', 'workhour_id', 'date_start', 'date_end');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nik', fn($row) => $row->employee?->nik ?? '')
                ->addColumn('fullname', fn($row) => $row->employee?->fullname ?? '')
                ->addColumn('work_name', fn($row) => $row->workhour?->work_name ?? '')
                ->addColumn('area', fn($row) => $row->employee?->area?->name ?? '')
                ->addColumn('department', fn($row) => $row->employee?->department?->name ?? '')
                ->addColumn('section', fn($row) => $row->employee?->section?->nama ?? '')
                ->addColumn('building', fn($row) => $row->employee?->building?->nama ?? '')
                ->rawColumns(['action','status'])
                ->make(true);
        }

        return view("pages.attendance.sub-menu.group-employee-workhour.form", compact(
            'areas',
            'departments',
            'sections',
            'buildings',
            'workhours'
            )
        );
    }
    public function store(Request $request)
    {
        $request->validate([
            'group_name' => 'required|string|max:255|unique:group_employee_workhours,name',

            'employee_id' => 'nullable|array',
            'employee_id.*' => 'exists:employees,id',

            'workhour_id' => 'required|array',
            'workhour_id.*' => 'required|exists:master_work_hour,id',

            'start_date' => 'required|array',
            'start_date.*' => 'required|date',

            'end_date' => 'nullable|array',
            'end_date.*' => 'nullable|date',
        ]);

        DB::beginTransaction();

        try {

            // ✅ VALIDASI DUPLICATE DATE (di luar loop)
            $dates = collect($request->start_date)->filter();
            if ($dates->duplicates()->isNotEmpty()) {
                throw new \Exception("Tidak boleh ada start date yang sama");
            }
            if (collect($request->start_date)->filter()->count() !== count($request->workhour_id)) {
                    throw new \Exception("Semua workhour harus memiliki start date");
                }

            // ✅ VALIDASI PER ROW
            foreach ($request->workhour_id as $index => $wh) {

                $isActive = $request->is_active[$index] ?? 0;
                $start = $request->start_date[$index] ?? null;
                $end   = $request->end_date[$index] ?? null;

                if (!$start) {
                    throw new \Exception("Semua workhour harus memiliki start date");
                }

                if ($start < Carbon::today()->toDateString()) {
                    throw new \Exception("Start date tidak boleh di masa lalu");
                }
            }

            // ✅ CREATE GROUP
            $group = GroupEmployeeWorkhours::create([
                'name' => $request->group_name
            ]);

            // ✅ INSERT EMPLOYEES
            $group->groupEmployees()->createMany(
                collect($request->employee_id)
                    ->map(fn($emp) => ['employee_id' => $emp])
                    ->toArray()
            );

            // ✅ INSERT WORKHOURS
            $rows = collect($request->workhour_id)->map(function($wh, $index) use ($request) {
                return [
                    'workhour_id' => $wh,
                    'start_date' => $request->start_date[$index] ?? null,
                ];
            })
            ->filter(fn($row) => $row['start_date']) // hanya yang punya tanggal
            ->sortBy('start_date') // 🔥 urutkan dari tanggal terkecil
            ->values(); // reset index

            $today = Carbon::today();
            $workhours = $rows->map(function($row, $index) use ($rows, $today) {
                $start = Carbon::parse($row['start_date']);
                $next = $rows->get($index + 1);
                if ($next) {
                    $nextStart = Carbon::parse($next['start_date']);
                    $end = $nextStart->copy()->subDay()->toDateString();
                } else {
                    $end = null; // terakhir
                }

                $isActive = 0;
                if ($today >= $start && (!$next || $today < Carbon::parse($next['start_date']))) {
                    $isActive = 1;
                }

                return [
                    'workhour_id' => $row['workhour_id'],
                    'start_date' => $row['start_date'],
                    'end_date' => $end,
                    'is_active' => $isActive,
                ];
            });

            $group->groupWorkhours()->createMany($workhours->toArray());

            $user = Auth::user();
            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'insert',
                'description' => "{$user->employee->fullname} create new Group Employee Workhour ({$group->name})"
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Group Workhour berhasil dibuat'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422); // 🔥 ganti jadi 422 (validation error)
        }
    }
    public function employeeByGroup($id)
    {
        $id = decrypt($id);

        $data = GroupEmployee::query()
            ->with([
                'employee:id,fullname,area_id,department_id,section_id,building_id',
                'employee.area:id,name',
                'employee.department:id,name',
                'employee.section:id,nama',
                'employee.building:id,nama'
            ])
            ->where('group_id', $id);

        // FILTER (HANDLE ALL + LEBIH AMAN)
        if (request()->filled('area') && request('area') !== 'ALL') {
            $data->whereHas('employee.area', fn($q) =>
                $q->where('name', request('area'))
            );
        }
        if (request()->filled('department') && request('department') !== 'ALL') {
            $data->whereHas('employee.department', fn($q) =>
                $q->where('name', request('department'))
            );
        }
        if (request()->filled('section') && request('section') !== 'ALL') {
            $data->whereHas('employee.section', fn($q) =>
                $q->where('nama', request('section'))
            );
        }
        if (request()->filled('building') && request('building') !== 'ALL') {
            $data->whereHas('employee.building', fn($q) =>
                $q->where('nama', request('building'))
            );
        }
        return DataTables::of($data)

            ->addColumn('checkbox', function ($row) {
                return '<input type="checkbox" class="transfer-out-checkbox" value="'.$row->employee_id.'">';
            })

            ->addColumn('fullname', fn($row) => $row->employee->fullname ?? '-')
            ->addColumn('area', fn($row) => $row->employee->area->name ?? '-')
            ->addColumn('department', fn($row) => $row->employee->department->name ?? '-')
            ->addColumn('section', fn($row) => $row->employee->section->nama ?? '-')
            ->addColumn('building', fn($row) => $row->employee->building->nama ?? '-')

            // FIX FILTER COLUMN (PAKAI RELASI BENAR)
            ->filterColumn('fullname', function ($query, $keyword) {
                $query->whereHas('employee', fn($q) =>
                    $q->where('fullname', 'like', "%{$keyword}%")
                );
            })
            ->filterColumn('area', function ($query, $keyword) {
                $query->whereHas('employee.area', fn($q) =>
                    $q->where('name', 'like', "%{$keyword}%")
                );
            })
            ->filterColumn('department', function ($query, $keyword) {
                $query->whereHas('employee.department', fn($q) =>
                    $q->where('name', 'like', "%{$keyword}%")
                );
            })

            ->filterColumn('section', function ($query, $keyword) {
                $query->whereHas('employee.section', fn($q) =>
                    $q->where('nama', 'like', "%{$keyword}%")
                );
            })

            ->filterColumn('building', function ($query, $keyword) {
                $query->whereHas('employee.building', fn($q) =>
                    $q->where('nama', 'like', "%{$keyword}%")
                );
            })
            ->rawColumns(['checkbox'])
            ->make(true);
    }
    public function employeeByGroupForTransferIn(Request $request)
    {
        $data = Employee::with([
                'area:id,name',
                'department:id,name',
                'section:id,nama',
                'building:id,nama',
                'groupEmployees.groupEmployeeWorkhour:id,name'
            ])
            ->select('id', 'nik', 'fullname', 'area_id', 'department_id', 'section_id', 'building_id')
            ->whereDoesntHave('groupEmployees', function ($query) use ($request) {
                if ($request->filled('group_id')) {
                    $query->where('group_id', $request->group_id);
                }
            });
        //FILTER LEBIH CLEAN
        if ($request->filled('area') && $request->area !== 'ALL') {
            $data->whereHas('area', fn($q) =>
                $q->where('name', $request->area)
            );
        }
        if ($request->filled('department') && $request->department !== 'ALL') {
            $data->whereHas('department', fn($q) =>
                $q->where('name', $request->department)
            );
        }
        if ($request->filled('section') && $request->section !== 'ALL') {
            $data->whereHas('section', fn($q) =>
                $q->where('nama', $request->section)
            );
        }
        if ($request->filled('building') && $request->building !== 'ALL') {
            $data->whereHas('building', fn($q) =>
                $q->where('nama', $request->building)
            );
        }
        return DataTables::of($data)
            ->addColumn('area', fn($row) => $row->area->name ?? '-')
            ->addColumn('department', fn($row) => $row->department->name ?? '-')
            ->addColumn('section', fn($row) => $row->section->nama ?? '-')
            ->addColumn('building', fn($row) => $row->building->nama ?? '-')
            // GROUP NAME (AMAN & CEPAT)
            ->addColumn('group_name', function ($row) {
                return $row->groupEmployees?->groupEmployeeWorkhour?->name ?? '-';
            })
            // FILTER GLOBAL
            ->filterColumn('area', function ($query, $keyword) {
                $query->whereHas('area', fn($q) =>
                    $q->where('name', 'like', "%{$keyword}%")
                );
            })
            ->filterColumn('department', function ($query, $keyword) {
                $query->whereHas('department', fn($q) =>
                    $q->where('name', 'like', "%{$keyword}%")
                );
            })
            ->filterColumn('section', function ($query, $keyword) {
                $query->whereHas('section', fn($q) =>
                    $q->where('nama', 'like', "%{$keyword}%")
                );
            })
            ->filterColumn('building', function ($query, $keyword) {
                $query->whereHas('building', fn($q) =>
                    $q->where('nama', 'like', "%{$keyword}%")
                );
            })
            // OPTIONAL: filter group_name
            ->filterColumn('group_name', function ($query, $keyword) {
                $query->whereHas('groupEmployees.groupEmployeeWorkhour', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->make(true);
    }
    public function edit($id){
        $workhours = WorkHour::with('details')->get();
        $areas = DB::table('areas')->pluck('name');
        $departments = DB::table('departments')->pluck('name');
        $sections = DB::table('master_section')->pluck('nama');
        $buildings = DB::table('master_building')->pluck('nama');

        $id = decrypt($id);
        $group = GroupEmployeeWorkhours::with([
            'groupEmployees',
            'groupWorkHours.workhour' // relasi ke master workhour
        ])->findOrFail($id);


        return view("pages.attendance.sub-menu.group-employee-workhour.edit", compact(
            'areas',
            'departments',
            'sections',
            'buildings',
            'workhours',
            'group'
            )
        );
    }
    public function update(Request $request)
    {
        $id = decrypt($request->group_id);
        $request->validate([
            'group_id' => 'required',
            'group_name' => 'required|string|max:255|unique:group_employee_workhours,name,' . $id,

            'employee_id' => 'nullable|array',
            'employee_id.*' => 'exists:employees,id',

            'workhour_id' => 'required|array',
            'workhour_id.*' => 'required|exists:master_work_hour,id',

            'start_date' => 'nullable|array',
            'start_date.*' => 'nullable|date',

            'is_active' => 'required|array',
            'is_active.*' => 'in:0,1',
        ]);

        $id = decrypt($request->group_id);
        $group = GroupEmployeeWorkhours::findOrFail($id);

        DB::beginTransaction();

        try {
            // validasi duplicate tanggal
            $dates = collect($request->start_date)->filter();
            if ($dates->duplicates()->isNotEmpty()) {
                throw new \Exception("Tidak boleh ada start date yang sama");
            }
            foreach ($request->workhour_id as $index => $wh) {
                $isActive = $request->is_active[$index] ?? 0;
                $start = $request->start_date[$index] ?? null;

                if ($isActive && !$start) {
                    throw new \Exception("Workhour aktif harus memiliki start date");
                }

                if ($start && $start < Carbon::today()->toDateString()) {
                    throw new \Exception("Start date tidak boleh di masa lalu");
                }

                if (collect($request->start_date)->filter()->count() !== count($request->workhour_id)) {
                    throw new \Exception("Semua workhour harus memiliki start date");
                }
            }

            $group->update(['name' => $request->group_name]);

            // update karyawan
            $group->groupEmployees()->delete();
            $group->groupEmployees()->createMany(
                collect($request->employee_id)
                    ->map(fn($emp) => ['employee_id' => $emp])
                    ->toArray()
            );

            // update workhour
            $group->groupWorkhours()->delete();

            $rows = collect($request->workhour_id)->map(function($wh, $index) use ($request) {
                return [
                    'workhour_id' => $wh,
                    'start_date' => $request->start_date[$index] ?? null,
                    'is_active' => $request->is_active[$index] ?? 0,
                ];
            })
            ->filter(fn($row) => $row['start_date'])
            ->sortBy('start_date')
            ->values();

            $workhours = $rows->map(function($row, $index) use ($rows) {
                $start = Carbon::parse($row['start_date']);
                $next = $rows->get($index + 1);
                if ($next) {
                    $nextStart = Carbon::parse($next['start_date']);
                    $end = $nextStart->copy()->subDay()->toDateString();
                } else {
                    $end = null;
                }
                return [
                    'workhour_id' => $row['workhour_id'],
                    'start_date' => $row['start_date'],
                    'end_date' => $end,
                    'is_active' => $row['is_active'],
                ];
            });

            $group->groupWorkhours()->createMany($workhours->toArray());

            $user = Auth::user();
            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'update',
                'description' => "{$user->employee->fullname} modify Group Employee Workhour ({$group->name})"
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Group Workhour berhasil diperbarui'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
    public function destroy($id)
    {
        $id = decrypt($id);
        $group = GroupEmployeeWorkhours::findOrFail($id);

        // Simpan nama untuk log sebelum hapus
        $groupName = $group->name;

        // Hapus relasi terlebih dahulu
        $group->groupEmployees()->delete();
        $group->groupWorkhours()->delete();

        // Hapus grup
        $group->delete();

        $user = Auth::user();
        Log::create([
            'user_id' => $user->id,
            'ip_address' => request()->ip(),
            'action' => 'delete',
            'description' => "{$user->employee->fullname} delete Group Employee Workhour ({$groupName})"
        ]);

        return response()->json([
            'message' => 'Group Workhour berhasil dihapus'
        ]);
    }
    public function transferTo(Request $request)
    {
        $request->validate([
            'employees' => 'required|array',
            'employees.*' => 'exists:employees,id',
            'target_group' => 'required|exists:group_employee_workhours,id'
        ]);

        DB::beginTransaction();

        try {
            $targetGroup = GroupEmployeeWorkhours::findOrFail($request->target_group);
            // Remove from current groups
            GroupEmployee::whereIn('employee_id', $request->employees)->delete();

            // Add to target group
            $targetGroup->groupEmployees()->createMany(
                collect($request->employees)->map(fn($emp) => ['employee_id' => $emp])->toArray()
            );

            $user = Auth::user();
            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'transfer',
                'description' => "{$user->employee->fullname} transferred employees to group {$targetGroup->name}"
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Employees transferred successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
    public function getGroups(Request $request)
    {
        $groups = GroupEmployeeWorkhours::select('id', 'name')->get();
        return response()->json($groups);
    }
    public function TransferIn(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:group_employee_workhours,id',
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id'
        ]);

        DB::beginTransaction();

        try {
            $group = GroupEmployeeWorkhours::findOrFail($request->group_id);
            // 🔥 Ambil employee yang BELUM ada di group ini
            $existingEmployees = GroupEmployee::where('group_id', $request->group_id)
                ->whereIn('employee_id', $request->employee_ids)
                ->pluck('employee_id')
                ->toArray();

            $transferIds = array_diff($request->employee_ids, $existingEmployees);

            if (empty($transferIds)) {
                return response()->json([
                    'status' => false,
                    'message' => 'All selected employees are already in this group'
                ], 422);
            }
            // 🔥 UPDATE langsung (TRANSFER)
            GroupEmployee::whereIn('employee_id', $transferIds)
                ->update([
                    'group_id' => $request->group_id,
                    'updated_at' => now()
                ]);
            // 🔥 OPTIONAL: jika ada employee yang BELUM pernah punya group
            $existingIds = GroupEmployee::whereIn('employee_id', $transferIds)
                ->pluck('employee_id')
                ->toArray();
            $newIds = array_diff($transferIds, $existingIds);

            if (!empty($newIds)) {
                $insertData = collect($newIds)->map(fn($id) => [
                    'employee_id' => $id,
                    'group_id' => $request->group_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ])->toArray();

                GroupEmployee::insert($insertData);
            }

            // LOG
            $user = Auth::user();
            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'transfer_employee',
                'description' => "{$user->employee->fullname} transfer employees to group {$group->name}"
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => count($transferIds) . ' employees transferred successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
    public function transferOut(Request $request)
    {
        $request->validate([
            'current_group_id' => 'required|exists:group_employee_workhours,id',
            'target_group_id' => 'required|exists:group_employee_workhours,id|different:current_group_id',
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id'
        ]);

        DB::beginTransaction();

        try {
            $currentGroup = GroupEmployeeWorkhours::findOrFail($request->current_group_id);
            $targetGroup = GroupEmployeeWorkhours::findOrFail($request->target_group_id);

            // Check if employees are in current group
            $employeesInCurrentGroup = GroupEmployee::where('group_id', $request->current_group_id)
                ->whereIn('employee_id', $request->employee_ids)
                ->pluck('employee_id')
                ->toArray();

            if (count($employeesInCurrentGroup) !== count($request->employee_ids)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Some selected employees are not in the current group'
                ], 422);
            }

            // Check if employees are already in target group
            $employeesInTargetGroup = GroupEmployee::where('group_id', $request->target_group_id)
                ->whereIn('employee_id', $request->employee_ids)
                ->pluck('employee_id')
                ->toArray();

            if (!empty($employeesInTargetGroup)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Some selected employees are already in the target group'
                ], 422);
            }

            // Move employees from current group to target group
            GroupEmployee::where('group_id', $request->current_group_id)
                ->whereIn('employee_id', $request->employee_ids)
                ->update([
                    'group_id' => $request->target_group_id,
                    'updated_at' => now()
                ]);

            $user = Auth::user();
            Log::create([
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'action' => 'transfer_out',
                'description' => "{$user->employee->fullname} transferred employees from group {$currentGroup->name} to {$targetGroup->name}"
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => count($request->employee_ids) . ' employees transferred to ' . $targetGroup->name . ' successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

}
