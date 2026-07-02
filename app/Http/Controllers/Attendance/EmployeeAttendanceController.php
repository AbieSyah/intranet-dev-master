<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance\EmployeeAttendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class EmployeeAttendanceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = EmployeeAttendance::with([
                'groupEmployeeWorkhour',
                'masterWorkhour',
                'detail',
                'employee.area',
                'businessTrip'
            ])
            ->where(function ($q) {
                $q->whereNotNull('attendance_status')
                ->orWhereHas('detail', function ($d) {
                    $d->whereNotNull('check_in')
                        ->orWhereNotNull('check_out');
                });
            });
            // ================= FILTER DATE =================
            $date = $request->date ?? now()->toDateString();
            $query->whereDate('date', $date);
            // ================= FILTER LOCATION =================
            if (!is_null($request->location)) {
                $query->whereHas('employee', function ($q) use ($request) {
                    if ($request->location == 1) {
                        $q->where('area_id', 1);
                    } else {
                        $q->where('area_id', '!=', 1);
                    }
                });
            }
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
                // ================= BASIC =================
                ->addColumn('nik', fn($row) => $row->employee?->nik ?? '-')
                ->addColumn('employee_name', fn($row) => $row->employee?->fullname ?? '-')
                ->addColumn('area_name', fn($row) => $row->area_name ?? '-')
                ->addColumn('department_name', fn($row) => $row->department_name ?? '-')

                // ================= GROUP + WORKHOUR =================
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
                ->addColumn('latlong_ci', fn($row) => $row->detail->latlong_check_in ?? '-')
                ->addColumn('latlong_co', fn($row) => $row->detail->latlong_check_out ?? '-')
                // ================= CHECK IN / OUT =================
                ->addColumn('check_in', function ($row) {
                    return $row->detail?->check_in
                        ? Carbon::parse($row->detail->check_in)->format('Y M D H:i')
                        : '-';
                })
                ->addColumn('check_out', function ($row) {
                    return $row->detail?->check_out
                        ? Carbon::parse($row->detail->check_out)->format('Y M D H:i')
                        : '-';
                })
                // ================= STATUS =================
                ->addColumn('status_check_in', fn($row) => $row->detail?->status_check_in ?? '-')
                ->addColumn('status_check_out', fn($row) => $row->detail?->status_check_out ?? '-')
                ->addColumn('attendance_status', fn($row) => $row->attendance_status ?? '-')
                ->addColumn('business_trip_type', function($row){
                    return optional($row->businessTrip)->trip_type;
                })
                ->addColumn('source', fn($row) => $row->source ?? '-')
                // ================= ACTION =================
                ->addColumn('action', function ($row) {
                    $button = '';

                        $button .= '
                            <button title="Edit" data-id="'.encrypt($row->id).'"
                                class="btn btn-warning btn-sm edit-btn">
                                <i class="ri-edit-line"></i>
                            </button>';

                    return $button ?: '-';
                })

                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.attendance.sub-menu.employee-attendance.index');
    }
    public function late(Request $request)
    {
        if ($request->ajax()) {
            $query = EmployeeAttendance::with([
                'employee:id,nik,fullname,area_id',
                'detail:id,employee_attendance_id,check_in,status_check_in',
                'lateHistories',
                'groupEmployeeWorkhour',
                'masterWorkhour',
                'detail',
            ])
            ->whereHas('detail', function ($q) {
                $q->where('status_check_in', 'late');
            });
            // ================= FILTER DATE =================
            $date = $request->date ?? now()->toDateString();
            $query->whereDate('date', $date);
            // ================= FILTER LOCATION =================
            if (!is_null($request->location)) {
                $query->whereHas('employee', function ($q) use ($request) {
                    if ($request->location == 1) {
                        $q->where('area_id', 1);
                    } else {
                        $q->where('area_id', '!=', 1);
                    }
                });
            }
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
                // ================= BASIC =================
                ->addColumn('nik', fn($row) => $row->employee?->nik ?? '-')
                ->addColumn('employee_name', fn($row) => $row->employee?->fullname ?? '-')
                ->addColumn('position_name', fn($row) => $row->position_name ?? '-')
                ->addColumn('area_name', fn($row) => $row->area_name ?? '-')
                ->addColumn('department_name', fn($row) => $row->department_name ?? '-')
                ->addColumn('date', fn($row) => $row->date ?? '-')
                ->addColumn('reason', fn($row) => $row->lateHistories->reason ?? '-')
                ->addColumn('actual_in_employee', fn($row) => $row->detail->check_in ?? '-')
                ->addColumn('actual_in_security', fn($row) => $row->lateHistories->actual_in ?? '-')
                ->addColumn('group_workhours', function ($row) {
                    $group = $row->groupEmployeeWorkhour?->name ?? '-';
                    $workhour = $row->masterWorkhour?->work_name ?? '-';
                    if ($group === '-' && $workhour === '-') {
                        return '-';
                    }
                    return "{$group} - {$workhour}";
                })
                ->addColumn('security', function ($row) {
                    $html = '';
                    $lateHistory = optional($row->lateHistories);
                    $icon1 = ($lateHistory->security_knowledge ?? 0) == 1
                        ? '<i class="ri-checkbox-circle-fill text-success"></i>'
                        : '<i class="ri-close-circle-fill text-danger"></i>';
                    $name1 = $lateHistory->security_name ?? '-';
                    $html .= "Security 1 : {$name1} {$icon1}";

                    return $html;
                })
                ->addColumn('head', function ($row) {
                    $html = '';
                    $lateHistory = optional($row->lateHistories);
                    $icon1 = ($lateHistory->head_knowledge ?? 0) == 1
                        ? '<i class="ri-checkbox-circle-fill text-success"></i>'
                        : '<i class="ri-close-circle-fill text-danger"></i>';
                    $name1 = $lateHistory->knowledgeby_headName ?? '-';
                    $html .= "Security 1 : {$name1} {$icon1}";

                    return $html;
                })
                ->addColumn('hrd', function ($row) {
                    $html = '';
                    $lateHistory = optional($row->lateHistories);
                    $icon1 = ($lateHistory->hrd_knowledge ?? 0) == 1
                        ? '<i class="ri-checkbox-circle-fill text-success"></i>'
                        : '<i class="ri-close-circle-fill text-danger"></i>';
                    $name1 = $lateHistory->knowledgeby_hrdName ?? '-';
                    $html .= "Security 1 : {$name1} {$icon1}";

                    return $html;
                })

                // ================= ACTION =================
                ->addColumn('action', function ($row) {

                    $button = '';

                    $lateHistory = $row->lateHistories;

                    if ($lateHistory && $lateHistory->head_knowledge == 1) {

                        $button .= '
                            <button title="Edit"
                                data-id="'.encrypt($row->id).'"
                                class="btn btn-success btn-sm knowledge-btn">
                                <i class="ri-check-line"></i>
                            </button>';
                    }

                    return $button;
                })

                ->rawColumns(['action','security','head','hrd'])
                ->make(true);
        }
    }
    public function view(Request $request)
    {
        if ($request->ajax()) {
            $date = $request->date ?? now()->toDateString();

            $query = Employee::with([
                'groupEmployees.groupEmployeeWorkhour.groupWorkHours.workhour.details'
            ])
            ->select(
                'employees.id',
                'employees.nik',
                'employees.fullname',
                'employees.area_id',
                'employees.department_id',
                'employees.position_id',
                'employees.status',
                'areas.name as area_name',
                'departments.name as department_name',
                'master_position.nama as position_name'
            )
            ->leftJoin('areas', 'employees.area_id', '=', 'areas.id')
            ->leftJoin('departments', 'employees.department_id', '=', 'departments.id')
            ->leftJoin('master_position', 'employees.position_id', '=', 'master_position.id')
            ->where('status', '!=', 'Terminate')
            ->whereDoesntHave('attendances', function ($q) use ($date) {
                $q->whereDate('date', $date);
            });

            if ($request->location == 1) {
                $query->where('area_id', 1);
            } elseif ($request->location == 0) {
                $query->where('area_id', '!=', 1);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->filter(function ($query) use ($request) {
                    if ($request->has('search') && $request->search['value']) {
                        $search = $request->search['value'];

                        $query->where(function ($q) use ($search) {
                            $q->where('nik', 'like', "%{$search}%")
                            ->orWhere('fullname', 'like', "%{$search}%")
                            ->orWhere('areas.name', 'like', "%{$search}%")
                            ->orWhere('departments.name', 'like', "%{$search}%")
                            ->orWhere('master_position.nama', 'like', "%{$search}%")
                            ->orWhereHas('groupEmployee.groupEmployeeWorkhour', function ($q2) use ($search) {
                                $q2->where('name', 'like', "%{$search}%");
                            });
                        });
                    }
                })

                ->addColumn('nik', fn($row) => $row->nik ?? '-')
                ->addColumn('employee_name', fn($row) => $row->fullname ?? '-')
                ->addColumn('position_name', fn($row) => $row->position_name ?? '-')
                ->addColumn('area_name', fn($row) => $row->area_name ?? '-')
                ->addColumn('department_name', fn($row) => $row->department_name ?? '-')

                ->addColumn('group_workhours', function ($row) use ($date) {
                    $workhourData =
                        $this->getCachedWorkhour(
                            $row,
                            $date
                        );
                    if (
                        $workhourData['group_name'] === '-' &&
                        $workhourData['work_name'] === '-'
                    ) {
                        return '-';
                    }
                    return
                        $workhourData['group_name']
                        .' - '.
                        $workhourData['work_name'];
                })

                ->addColumn('work_in', function ($row) use ($date) {
                    return $this->getCachedWorkhour(
                        $row,
                        $date
                    )['work_in'];
                })
                ->addColumn('work_out', function ($row) use ($date) {
                    return $this->getCachedWorkhour(
                        $row,
                        $date
                    )['work_out'];
                })

                ->make(true);
        }
    }
    private function getEmployeeWorkhourData($employee, $date)
    {
        $groupEmployee = $employee->groupEmployees;

        if (
            !$groupEmployee ||
            !$groupEmployee->groupEmployeeWorkhour
        ) {
            return [
                'group_name' => '-',
                'work_name'  => '-',
                'work_in'    => '-',
                'work_out'   => '-',
            ];
        }

        $targetDate = Carbon::parse($date);

        $groupWorkhour = $groupEmployee->groupEmployeeWorkhour;

        $activeGroupWorkHour = $groupWorkhour->groupWorkHours
            ->filter(function ($g) use ($targetDate) {

                if (!$g->start_date) {
                    return false;
                }

                $start = Carbon::parse($g->start_date);

                $end = $g->end_date
                    ? Carbon::parse($g->end_date)
                    : null;

                return $start->lte($targetDate)
                    && (!$end || $end->gte($targetDate));
            })
            ->sortByDesc(fn($g) =>
                Carbon::parse($g->start_date)->timestamp
            )
            ->first();

        if (
            !$activeGroupWorkHour ||
            !$activeGroupWorkHour->workhour
        ) {
            return [
                'group_name' => $groupWorkhour->name ?? '-',
                'work_name'  => '-',
                'work_in'    => '-',
                'work_out'   => '-',
            ];
        }

        $dayName = strtolower(
            $targetDate->format('l')
        );

        $detail = $activeGroupWorkHour->workhour->details
            ->first(fn($d) =>
                strtolower($d->day) === $dayName
            );

        return [
            'group_name' =>
                $groupWorkhour->name ?? '-',

            'work_name' =>
                $activeGroupWorkHour->workhour->work_name ?? '-',

            'work_in' =>
                $detail->work_in ?? '-',

            'work_out' =>
                $detail->work_out ?? '-',
        ];
    }
    private function getCachedWorkhour($row, $date)
    {
        if (!isset($row->workhour_cache)) {

            $row->workhour_cache =
                $this->getEmployeeWorkhourData(
                    $row,
                    $date
                );
        }

        return $row->workhour_cache;
    }
    // private function getWorkHourByDate($employee, $date)
    // {
    //     $groupEmployee = $employee->groupEmployees->first();

    //     if (!$groupEmployee || !$groupEmployee->groupEmployeeWorkhour) {
    //         return [];
    //     }
    //     $targetDate = Carbon::parse($date);
    //     $groupWorkhour = $groupEmployee->groupEmployeeWorkhour;
    //     $activeGroupWorkHour = $groupWorkhour->groupWorkHours
    //         ->filter(function ($g) use ($targetDate) {
    //             if (!$g->start_date) return false;

    //             $start = Carbon::parse($g->start_date);
    //             $end   = $g->end_date ? Carbon::parse($g->end_date) : null;

    //             return $start->lte($targetDate) &&
    //                 (!$end || $end->gte($targetDate));
    //         })
    //         ->sortByDesc(fn($g) => Carbon::parse($g->start_date)->timestamp)
    //         ->first();

    //     if (!$activeGroupWorkHour || !$activeGroupWorkHour->workhour) {
    //         return [];
    //     }
    //     $dayName = strtolower($targetDate->format('l'));
    //     $detail = $activeGroupWorkHour->workhour->details
    //         ->first(fn($d) => strtolower($d->day) === $dayName);
    //     if (!$detail) {
    //         return [];
    //     }
    //     return [
    //         'group_id'           => $groupWorkhour->id ?? null,
    //         'master_workhour_id' => $activeGroupWorkHour->workhour->id ?? null,
    //         'work_in'            => $detail->work_in ?? null,
    //         'work_out'           => $detail->work_out ?? null,
    //     ];
    // }

    public function update(Request $request, $id)
    {
        $id = decrypt($id);

        $request->validate([
            'check_in' => 'nullable|date_format:Y-m-d H:i',
            'check_out' => 'nullable|date_format:Y-m-d H:i',
        ]);

        DB::beginTransaction();

        try {
            $attendance = EmployeeAttendance::with('detail')->findOrFail($id);
            $detail = $attendance->detail ?? new EmployeeAttendanceDetail([
                'employee_attendance_id' => $attendance->id,
            ]);

            $checkIn = $request->input('check_in');
            $checkOut = $request->input('check_out');

            if ($checkIn !== null && $checkIn !== '') {
                $detail->check_in = Carbon::parse($checkIn)->format('Y-m-d H:i:s');
            }

            if ($checkOut !== null && $checkOut !== '') {
                $detail->check_out = Carbon::parse($checkOut)->format('Y-m-d H:i:s');
            }

            if ($detail->check_in && $attendance->work_in) {
                $workIn = Carbon::parse($attendance->date . ' ' . $attendance->work_in);
                $detail->status_check_in = Carbon::parse($detail->check_in)->lte($workIn)
                    ? 'on_time'
                    : 'late';
            }

            if ($detail->check_out && $attendance->work_out) {
                $workOut = Carbon::parse($attendance->date . ' ' . $attendance->work_out);
                $detail->status_check_out = Carbon::parse($detail->check_out)->gte($workOut)
                    ? 'on_time'
                    : 'early';
            }

            $detail->save();

            // $attendance->fill([
            //     'check_in' => $detail->check_in,
            //     'check_out' => $detail->check_out,
            //     'status_check_in' => $detail->status_check_in,
            //     'status_check_out' => $detail->status_check_out,
            // ]);
            // $attendance->save();

            DB::commit();

            return response()->json([
                'message' => 'Berhasil memperbarui check in / check out',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage(),
            ], 500);
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
            // ================= UPDATE SECURITY =================
            $late->update([
                'hrd_knowledge'        => true,
                'knowledgeby_hrdName'  => $head->fullname,
            ]);
            DB::commit();
            return response()->json([
                'message' => 'Berhasil diketahui HRD'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
