<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance\EmployeeAttendance;
use App\Models\Attendance\lateHistories;
use App\Models\Master\LineApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class lateHistoriesController extends Controller
{
    public function profileIndex(){
        $user = Auth::user();
        $employeeId = $user->employee->id;
        $isApprover = LineApproval::where('approval_type', 'Attendance Permit')
        ->where(function ($q) use ($employeeId) {
            $q->where('approve_1', $employeeId)
            ->orWhere('approve_2', $employeeId)
            ->orWhere('approve_3', $employeeId)
            ->orWhere('approve_4', $employeeId)
            ->orWhere('approve_5', $employeeId)
            ->orWhere('approve_6', $employeeId)
            ->orWhere('approve_7', $employeeId)
            ->orWhere('approve_8', $employeeId);
        })
        ->exists();
        return view('pages.profile.Attendance.attendance-late.index', compact('user','isApprover'));
    }

    public function pendingCount()
    {
        $employee = auth()->user()->employee;

        $lineApprovals = LineApproval::where('approval_type', 'Attendance Permit')
        ->where(function ($q) use ($employee) {
            $q->where('approve_1', $employee->id)
              ->orWhere('approve_2', $employee->id)
              ->orWhere('approve_3', $employee->id)
              ->orWhere('approve_4', $employee->id)
              ->orWhere('approve_5', $employee->id)
              ->orWhere('approve_6', $employee->id)
              ->orWhere('approve_7', $employee->id)
              ->orWhere('approve_8', $employee->id);
        })
        ->with('employees')
        ->get();
    $totalApproval = 0;
    if ($lineApprovals->isNotEmpty()) {
        $employeeIds = $lineApprovals
            ->flatMap(fn($line) => $line->employees->pluck('id'))
            ->unique()
            ->toArray();
        // 🔥 hitung jumlah request waiting
        $totalApproval = EmployeeAttendance::with('lateHistories')
            ->whereIn('Employee_id', $employeeIds)
            ->whereHas('lateHistories', function ($q) {
                $q->where('security_knowledge', true);
            })
            ->whereHas('lateHistories', function ($q) {
                $q->where('head_knowledge', false);
            })
            ->count();
    }
        return response()->json([
            'total' => $totalApproval
        ]);
    }
    public function myData(Request $request)
    {
        if ($request->ajax()) {
            $employee = auth()->user()->employee;
            $query = EmployeeAttendance::with(['lateHistories','detail'])
            ->where('employee_id', $employee)
            ->whereHas('employee', function ($q) {
                $q->where('area_id', 1);
            });
            // ->whereHas('lateHistories', function ($q) {
            //     $q->where('security_knowledge', true);
            // });
            // ================= FILTER DATE =================
            if ($request->filter_date_late) {
                $query->whereDate('date', $request->filter_date_late);
            }
            // dd($query);
            return DataTables::of($query)
                ->addIndexColumn()

                ->filter(function ($query) use ($request) {
                    if ($request->search['value'] ?? false) {
                        $search = $request->search['value'];

                        $query->whereHas('employee', function ($q) use ($search) {
                            $q->where('nik', 'like', "%{$search}%")
                            ->orWhere('fullname', 'like', "%{$search}%")
                            ->orWhereHas('area', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                            ->orWhereHas('department', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                            ->orWhereHas('position', fn($q2) => $q2->where('nama', 'like', "%{$search}%"));
                        });
                    }
                })
                ->addColumn('date', fn($row) => $row->date ?? '-')
                ->addColumn('reason', fn($row) => $row->lateHistories->reason ?? '-')
                ->addColumn('actual_in_employee', fn($row) => $row->detail->check_in ?? '-')
                ->addColumn('actual_in_security', fn($row) => $row->lateHistories->actual_in ?? '-')
                ->addColumn('security', function ($row) {
                    $html = '';
                    $icon1 = $row->lateHistories->security_knowledge == 1
                        ? '<i class="ri-checkbox-circle-fill text-success"></i>'
                        : '<i class="ri-close-circle-fill text-danger"></i>';
                    $name1 = $row->lateHistories->security_name ?? '-';
                    $html .= "Security 1 : {$name1} {$icon1}";

                    return $html;
                })
                ->addColumn('head', function ($row) {
                    $html = '';
                    $icon1 = $row->lateHistories->head_knowledge == 1
                        ? '<i class="ri-checkbox-circle-fill text-success"></i>'
                        : '<i class="ri-close-circle-fill text-danger"></i>';
                    $name1 = $row->lateHistories->knowledgeby_headName ?? '-';
                    $html .= "Security 1 : {$name1} {$icon1}";

                    return $html;
                })
                ->addColumn('hrd', function ($row) {
                    $html = '';
                    $icon1 = $row->lateHistories->hrd_knowledge == 1
                        ? '<i class="ri-checkbox-circle-fill text-success"></i>'
                        : '<i class="ri-close-circle-fill text-danger"></i>';
                    $name1 = $row->lateHistories->knowledgeby_hrdName ?? '-';
                    $html .= "Security 1 : {$name1} {$icon1}";

                    return $html;
                })
                ->rawColumns(['security','head','hrd'])
                ->make(true);

        }
    }
    public function dataHeadKnowledge(Request $request)
    {
        if ($request->ajax()) {
            $employee = auth()->user()->employee;
            // ✅ filter hanya Attendance
            $lineApprovals = LineApproval::where('approval_type', 'Attendance Permit')
                ->where(function ($q) use ($employee) {
                    $q->where('approve_1', $employee->id);
                })->with('employees')->get();

            if ($lineApprovals->isEmpty()) {
                return DataTables::of(collect())->make(true);
            }
            // ambil semua employee dari semua line approval
            $employeeIds = $lineApprovals
                ->flatMap(fn($line) => $line->employees->pluck('id'))
                ->unique()
                ->toArray();
            $query = EmployeeAttendance::with(['lateHistories','detail','groupEmployeeWorkhour','masterWorkhour',])
            ->WhereIn('employee_id', $employeeIds)
            ->whereHas('employee', function ($q) {
                $q->where('area_id', 1);
            })
            ->whereHas('lateHistories', function ($q) {
                $q->where('security_knowledge', true);
            })
            ->whereHas('lateHistories', function ($q) {
                $q->where('head_knowledge', false);
            });
            // ================= FILTER DATE =================
            // if ($request->filter_date_late) {
            //     $query->whereDate('date', $request->filter_date_late);
            // }
            // dd($query);
            return DataTables::of($query)
                ->addIndexColumn()

                ->filter(function ($query) use ($request) {
                    if ($request->search['value'] ?? false) {
                        $search = $request->search['value'];

                        $query->whereHas('employee', function ($q) use ($search) {
                            $q->where('nik', 'like', "%{$search}%")
                            ->orWhere('fullname', 'like', "%{$search}%")
                            ->orWhereHas('area', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                            ->orWhereHas('department', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                            ->orWhereHas('position', fn($q2) => $q2->where('nama', 'like', "%{$search}%"));
                        });
                    }
                })
                ->addColumn('nik', fn($row) => $row->employee->nik ?? '-')
                ->addColumn('employee_name', fn($row) => $row->employee->fullname ?? '-')
                ->addColumn('position', fn($row) => $row->position_name ?? '-')
                ->addColumn('area', fn($row) => $row->area_name ?? '-')
                ->addColumn('department', fn($row) => $row->department_name ?? '-')
                ->addColumn('actual_in_employee', fn($row) => $row->detail->check_in ?? '-')
                ->addColumn('actual_in_security', fn($row) => $row->lateHistories->actual_in ?? '-')
                ->addColumn('reason', fn($row) => $row->lateHistories->reason ?? '-')
                ->addColumn('group_workhours', function ($row) {
                    $group = $row->groupEmployeeWorkhour?->name ?? '-';
                    $workhour = $row->masterWorkhour?->work_name ?? '-';

                    if ($group === '-' && $workhour === '-') {
                        return '-';
                    }
                    return "{$group} - {$workhour}";
                })
                ->addColumn('work_in', fn($row) => $row->work_in ?? '-')
                ->addColumn('work_out', fn($row) => $row->work_out ?? '-')
                ->addColumn('security', function ($row) {
                    $html = '';
                    $icon1 = $row->lateHistories->security_knowledge == 1
                        ? '<i class="ri-checkbox-circle-fill text-success"></i>'
                        : '<i class="ri-close-circle-fill text-danger"></i>';
                    $name1 = $row->lateHistories->security_name ?? '-';
                    $html .= "Security : {$name1} {$icon1}";

                    return $html;
                })
                ->addColumn('head', function ($row) {
                    $html = '';
                    $icon1 = $row->lateHistories->head_knowledge == 1
                        ? '<i class="ri-checkbox-circle-fill text-success"></i>'
                        : '<i class="ri-close-circle-fill text-danger"></i>';
                    $name1 = $row->lateHistories->knowledgeby_headName ?? '-';
                    $html .= "Atasan : {$name1} {$icon1}";

                    return $html;
                })
                ->addColumn('hrd', function ($row) {
                    $html = '';
                    $icon1 = $row->lateHistories->hrd_knowledge == 1
                        ? '<i class="ri-checkbox-circle-fill text-success"></i>'
                        : '<i class="ri-close-circle-fill text-danger"></i>';
                    $name1 = $row->lateHistories->knowledgeby_hrdName ?? '-';
                    $html .= "HRD : {$name1} {$icon1}";

                    return $html;
                })
                ->addColumn('action', function ($row) {
                return '
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-success btn-sm knowledge-btn" data-id="'.encrypt($row->id).'">
                            <i class="bi bi-check-lg"></i>
                        </button>
                ';
            })
                ->rawColumns(['security','head','hrd','action'])
                ->make(true);

        }
    }
    public function knowledge(Request $request)
{
    $id = decrypt($request->id);

    DB::beginTransaction();

    try {
        $attendance = EmployeeAttendance::with([
            'employee',
            'lateHistories'
        ])->findOrFail($id);

        $late = $attendance->lateHistories;
        $head = Auth::user()->employee;

        if (!$late) {
            throw new \Exception('Data late tidak ditemukan');
        }
        // dd($late);
        // ================= UPDATE SECURITY =================
        $late->update([
            'head_knowledge'        => true,
            'knowledgeby_headName'  => $head->fullname,
        ]);
        // dd($late);
        DB::commit();

        return response()->json([
            'message' => 'Berhasil diketahui Atasan'
        ]);

    } catch (\Throwable $e) {
        DB::rollBack();

        return response()->json([
            'message' => $e->getMessage()
        ], 500);
    }
}
 public function dataHeadHistory(Request $request)
    {
        if ($request->ajax()) {
            $date = $request->date ?? now()->toDateString();
            $employee = auth()->user()->employee;
            $lineApprovals = LineApproval::where('approval_type', 'Attendance Permit')
                ->where(function ($q) use ($employee) {
                    $q->where('approve_1', $employee->id);
                })->with('employees')->get();

            if ($lineApprovals->isEmpty()) {
                return DataTables::of(collect())->make(true);
            }
            // ambil semua employee dari semua line approval
            $employeeIds = $lineApprovals
                ->flatMap(fn($line) => $line->employees->pluck('id'))
                ->unique()
                ->toArray();
            // exclude diri sendiri
            //$employeeIds = array_diff($employeeIds, [$employee->id]);
            $query = EmployeeAttendance::with(['lateHistories','detail','groupEmployeeWorkhour','masterWorkhour',])
            ->whereIn('employee_id', $employeeIds)
            ->whereDate('date', $date)
            ->whereHas('employee', function ($q) {
                $q->where('area_id', 1);
            })
            ->whereHas('lateHistories', function ($q) {
                $q->where('head_knowledge', true);
            });
            // ================= FILTER DATE =================
            if ($request->filter_date_late) {
                $query->whereDate('date', $request->filter_date_late);
            }
            // dd($query);
            return DataTables::of($query)
                ->addIndexColumn()

                ->filter(function ($query) use ($request) {
                    if ($request->search['value'] ?? false) {
                        $search = $request->search['value'];

                        $query->whereHas('employee', function ($q) use ($search) {
                            $q->where('nik', 'like', "%{$search}%")
                            ->orWhere('fullname', 'like', "%{$search}%")
                            ->orWhereHas('area', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                            ->orWhereHas('department', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                            ->orWhereHas('position', fn($q2) => $q2->where('nama', 'like', "%{$search}%"));
                        });
                    }
                })
                ->addColumn('nik', fn($row) => $row->employee->nik ?? '-')
                ->addColumn('employee_name', fn($row) => $row->employee->fullname ?? '-')
                ->addColumn('position', fn($row) => $row->position_name ?? '-')
                ->addColumn('area', fn($row) => $row->area_name ?? '-')
                ->addColumn('department', fn($row) => $row->department_name ?? '-')
                ->addColumn('actual_in_employee', fn($row) => $row->detail->check_in ?? '-')
                ->addColumn('actual_in_security', fn($row) => $row->lateHistories->actual_in ?? '-')
                ->addColumn('reason', fn($row) => $row->lateHistories->reason ?? '-')
                ->addColumn('group_workhours', function ($row) {
                    $group = $row->groupEmployeeWorkhour?->name ?? '-';
                    $workhour = $row->masterWorkhour?->work_name ?? '-';

                    if ($group === '-' && $workhour === '-') {
                        return '-';
                    }
                    return "{$group} - {$workhour}";
                })
                ->addColumn('work_in', fn($row) => $row->work_in ?? '-')
                ->addColumn('work_out', fn($row) => $row->work_out ?? '-')
                ->addColumn('security', function ($row) {
                    $html = '';
                    $icon1 = $row->lateHistories->security_knowledge == 1
                        ? '<i class="ri-checkbox-circle-fill text-success"></i>'
                        : '<i class="ri-close-circle-fill text-danger"></i>';
                    $name1 = $row->lateHistories->security_name ?? '-';
                    $html .= "Security : {$name1} {$icon1}";

                    return $html;
                })
                ->addColumn('head', function ($row) {
                    $html = '';
                    $icon1 = $row->lateHistories->head_knowledge == 1
                        ? '<i class="ri-checkbox-circle-fill text-success"></i>'
                        : '<i class="ri-close-circle-fill text-danger"></i>';
                    $name1 = $row->lateHistories->knowledgeby_headName ?? '-';
                    $html .= "Atasan : {$name1} {$icon1}";

                    return $html;
                })
                ->addColumn('hrd', function ($row) {
                    $html = '';
                    $icon1 = $row->lateHistories->hrd_knowledge == 1
                        ? '<i class="ri-checkbox-circle-fill text-success"></i>'
                        : '<i class="ri-close-circle-fill text-danger"></i>';
                    $name1 = $row->lateHistories->knowledgeby_hrdName ?? '-';
                    $html .= "HRD : {$name1} {$icon1}";

                    return $html;
                })
                ->rawColumns(['security','head','hrd'])
                ->make(true);

        }
    }
}
