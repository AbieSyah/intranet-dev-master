<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\Attendance\AttendanceCalendar;
use App\Models\Attendance\LeaveBalance;
use App\Models\Department;
use App\Models\Master\LineApproval;
use App\Models\Master\LineApprovalEmployee;
use App\Models\Position;
use App\Models\Attendance\LeaveRequest;
use App\Models\Attendance\LeaveSetting;
use App\Models\Employee;
use App\Models\Log;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class EmployeeLeaveController extends Controller
{

    public function index(){
        return view('pages.attendance.sub-menu.employee-leave.index');
    }

    // ============================================================= LEAVE BALANCE or HRD SECTION ==================================================================

    public function leaveBalanceindex(Request $request)
    {
        if ($request->ajax()) {

            $year = $request->year ?? now()->year;

            $query = LeaveBalance::with(['employee','leaveType'])
            ->orderByDesc('valid_from');
            // ✅ FILTER TAHUN
            if ($request->year) {
                $query->whereYear('valid_from', $request->year);
            }

            // 🔥 carry semua yang masih valid dari tahun sebelumnya
            $carryData = LeaveBalance::select('employee_id', DB::raw('SUM(remaining_days) as total'))
                ->where('valid_to', '>=', now())
                ->whereYear('valid_from', '<', $year)
                ->groupBy('employee_id')
                ->pluck('total', 'employee_id');

            return DataTables::of($query)
                ->addIndexColumn()

                ->addColumn('leave_type', fn($row) => $row->leaveType?->type ?? $row->type)

                ->addColumn('remaining_days', function ($row) use ($carryData) {
                    $carry = $carryData[$row->employee_id] ?? 0;

                    if ($carry > 0) {
                        return "{$row->remaining_days} + {$carry} <small>(carry)</small>";
                    }

                    return (string) $row->remaining_days;
                })

                ->addColumn('valid', function ($row) {
                    if ($row->valid_to < now()) {
                        return '<span class="badge bg-danger">Expired</span>';
                    }

                    $daysLeft = now()->diffInDays($row->valid_to, false);
                    return "{$row->valid_from} - {$row->valid_to}<br><small>{$daysLeft} hari lagi</small>";
                })

                ->addColumn('action', function ($row) {
                    return '
                        <button class="btn btn-danger btn-sm delete-btn"
                            data-id="'.encrypt($row->id).'">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    ';
                })
                ->orderColumn('remaining_days', fn($q, $order) => $q->orderBy('remaining_days', $order))
                ->rawColumns(['remaining_days','valid','action'])
                ->make(true);
        }
    }
   public function leaveBalanceDestroy(Request $request)
    {
        DB::beginTransaction();
        try {
            $id = decrypt($request->id);
            $data = DB::table('leave_balances')->where('id', $id)->first();
            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }
            // ================= DELETE =================
            DB::table('leave_balances')->where('id', $id)->delete();
            // ================= LOG =================
            $user = Auth::user();
            Log::create([
                'user_id'    => $user->id,
                'ip_address' => $request->ip(),
                'action'     => 'delete',
                'description'=> "{$user->employee->fullname} delete leave balance {$data->nik} - {$data->employee_name} ({$data->type}) sisa {$data->remaining_days} hari"
            ]);
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: '.$e->getMessage()
            ], 500);
        }
    }
    public function leaveBalanceCreate(Request $request)
{
    $areas = DB::table('areas')->pluck('name');
    $departments = DB::table('departments')->pluck('name');
    $positions = DB::table('master_position')->pluck('nama');

    $leaves = DB::table('leave_settings')
        ->where('type', 'like', 'Pribadi%')
        ->get();

    if ($request->ajax()) {
        $employees = Employee::query()
            ->with(['area', 'department', 'position'])
            ->where('status', '!=', 'Terminate')

            // Minimal kerja > 1 tahun
            ->where(function ($q) {
                $q->whereRaw('TIMESTAMPDIFF(YEAR, joindate, CURDATE()) > 1')
                  ->orWhereRaw('
                        TIMESTAMPDIFF(YEAR, joindate, CURDATE()) = 1
                        AND DATE_FORMAT(CURDATE(), "%m-%d") >= DATE_FORMAT(joindate, "%m-%d")
                  ');
            })

            // FILTER
            ->when($request->position, function ($q) use ($request) {
                $q->whereHas('position', fn($qq) =>
                    $qq->where('nama', $request->position)
                );
            })

            ->when($request->area, function ($q) use ($request) {
                $q->whereHas('area', fn($qq) =>
                    $qq->where('name', $request->area)
                );
            })

            ->when($request->department, function ($q) use ($request) {
                $q->whereHas('department', fn($qq) =>
                    $qq->where('name', $request->department)
                );
            })

            ->select([
                'id',
                'nik',
                'fullname',
                'area_id',
                'department_id',
                'position_id',
                'joindate'
            ]);
        $leaveBalances = DB::table('leave_balances')
            ->where('valid_to', '>=', now())
            ->where('remaining_days', '>', 0)
            ->select(
                'employee_id',
                DB::raw('SUM(remaining_days) as total_remaining'),
                DB::raw('MIN(valid_to) as nearest_expired')
            )
            ->groupBy('employee_id')
            ->get()
            ->keyBy('employee_id');

        return DataTables::of($employees)
            ->addColumn('area', fn($row) =>
                $row->area?->name ?? '-'
            )

            ->addColumn('department', fn($row) =>
                $row->department?->name ?? '-'
            )

            ->addColumn('position', fn($row) =>
                $row->position?->nama ?? '-'
            )
            ->addColumn('joindate', function ($row) {
                return $row->joindate
                    ? Carbon::parse($row->joindate)->format('d-m-Y')
                    : '-';
            })

            ->addColumn('total_years', function ($row) {
                return $row->joindate
                    ? Carbon::parse($row->joindate)
                        ->diff(now())
                        ->format('%y tahun %m bulan')
                    : '-';
            })
            ->addColumn('remaining_last_year', function ($row) use ($leaveBalances) {
                return $leaveBalances[$row->id]->total_remaining ?? 0;
            })

            ->addColumn('expired_days', function ($row) use ($leaveBalances) {

                $expiredDate = $leaveBalances[$row->id]->nearest_expired ?? null;

                return $expiredDate
                    ? now()->diffInDays(Carbon::parse($expiredDate), false)
                    : null;
            })
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

            ->filterColumn('position', function ($query, $keyword) {
                $query->whereHas('position', fn($q) =>
                    $q->where('nama', 'like', "%{$keyword}%")
                );
            })

            ->addIndexColumn()
            ->make(true);
    }

    return view(
        "pages.attendance.sub-menu.employee-leave.balanceForm",
        compact(
            'areas',
            'departments',
            'positions',
            'leaves'
        )
    );
}
    public function leaveBalanceStore(Request $request)
    {
        $request->validate([
            'employees'   => 'required|json',
            'valid_from'  => 'required|date',
            'valid_to'    => 'required|date|after_or_equal:valid_from',
        ]);

        DB::beginTransaction();

        try {
            $employees = json_decode($request->employees, true);
            if (empty($employees)) {
                throw new \Exception("Data karyawan kosong");
            }
            $duplicates = [];
            $dataInsert = [];
            foreach ($employees as $emp) {
                if (!isset($emp['id'], $emp['leave_type_id'], $emp['leave_balance'])) {
                    throw new \Exception("Format data employee tidak valid");
                }
                $employee = Employee::findOrFail($emp['id']);
                // ✅ VALIDASI TYPE ID
                if (empty($emp['leave_type_id'])) {
                   throw new \Exception("Data cuti tidak dapat diproses karena pengaturan tahun belum tersedia di Master Setting. Silakan tambahkan rentang tahun yang sesuai untuk {$employee->fullname}.");
                }
                $exists = DB::table('leave_balances')
                    ->where('employee_id', $employee->id)
                    ->where('valid_from', $request->valid_from)
                    ->exists();

                if ($exists) {
                    $duplicates[] = $employee->fullname;
                    continue;
                }
                $balance = (int) $emp['leave_balance'];
                $dataInsert[] = [
                    'employee_id'     => $employee->id,
                    'nik'             => $employee->nik,
                    'employee_name'   => $employee->fullname,
                    'position'        => $employee->position->nama ?? '-',
                    'area'            => $employee->area->name ?? '-',
                    'department'      => $employee->department->name ?? '-',
                    'leave_type_id'   => $emp['leave_type_id'],
                    'type'            => $emp['type'] ?? 'pribadi',
                    'leave_balance'   => $balance,
                    'remaining_days'  => $balance,
                    'valid_from'      => $request->valid_from,
                    'valid_to'        => $request->valid_to,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
            }
            if (!empty($duplicates)) {
                DB::rollBack();
                return response()->json([
                    'status'  => false,
                    'message' => 'Duplicate saldo cuti ditemukan',
                    'data'    => [
                        'employees' => $duplicates
                    ]
                ], 422);
            }
            if (!empty($dataInsert)) {
                DB::table('leave_balances')->insert($dataInsert);
            }
            $user = Auth::user();
            Log::create([
                'user_id'    => $user->id,
                'ip_address' => $request->ip(),
                'action'     => 'insert',
                'description'=> "{$user->employee->fullname} Add Leave Balance (Bulk) {$request->valid_from} - {$request->valid_to}"
            ]);
            DB::commit();
            return response()->json([
                'status'  => true,
                'message' => 'Leave Balance berhasil ditambahkan'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
    public function getSelectedEmployees(Request $request)
    {
        $ids = $request->ids ?? [];
        $employees = Employee::with(['area','department','position'])
            ->whereIn('id', $ids)
            ->get()
            ->map(function ($emp) {

                //  hitung sisa tahun lalu (opsional)
                $lastYear = now()->subYear()->year;
                $remaining = DB::table('leave_balances')
                    ->where('employee_id', $emp->id)
                    ->whereYear('valid_from', $lastYear)
                    ->where('valid_to', '>=', now())
                    ->sum('remaining_days');

                // Hitung expired_days dari valid_to terdekat
                $nearestBalance = DB::table('leave_balances')
                    ->where('employee_id', $emp->id)
                    ->where('valid_to', '>=', now())
                    ->orderBy('valid_to', 'asc')
                    ->first();

                $expiredDays = $nearestBalance
                    ? now()->diffInDays(Carbon::parse($nearestBalance->valid_to), false)
                    : null;

                // Hanya tampilkan jika positif (belum expired)
                if ($expiredDays !== null && $expiredDays <= 0) {
                    $expiredDays = null;
                }

                return [
                    'id' => $emp->id,
                    'nik' => $emp->nik,
                    'fullname' => $emp->fullname,
                    'position' => $emp->position->nama ?? '-',
                    'area' => $emp->area->name ?? '-',
                    'department' => $emp->department->name ?? '-',
                    'joindate' => $emp->joindate ?? null,
                    'total_years' => Carbon::parse($emp->joindate)->diffInYears(now()),
                    'remaining_last_year' => $remaining,
                    'expired_days' => $expiredDays
                ];
            });

        return response()->json([
            'data' => $employees
        ]);
    }

    //============================================================== LEAVE APPROVAL or HRD SECTION ==================================================================

    public function leaveHRDIndex(Request $request)
    {
        if ($request->ajax()) {
            $query = LeaveRequest::with(['employee'])
                ->when($request->request_date, function ($q) use ($request) {
                    [$year, $month] = explode('-', $request->request_date);
                    $q->whereYear('request_date', $year)
                    ->whereMonth('request_date', $month);
                })
                ->latest();
            $balances = LeaveBalance::selectRaw('employee_id, leave_type_id, SUM(remaining_days) as total')
                ->where('valid_to', '>=', now())
                ->groupBy('employee_id', 'leave_type_id')
                ->get()
                ->keyBy(fn($item) => $item->employee_id.'-'.$item->leave_type_id);
            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('nik', fn($row) => $row->nik)
                ->addColumn('name', fn($row) => $row->employee_name)
                ->addColumn('position', fn($row) => $row->position)
                ->addColumn('area', fn($row) => $row->area)
                ->addColumn('department', fn($row) => $row->department)
                ->addColumn('leave_type', fn($row) => $row->type ?? '-')
                ->addColumn('duration', fn($row) => $row->start_date . ' s/d ' . $row->end_date)
                ->addColumn('total_days', fn($row) => $row->total_days)
                ->addColumn('notes', fn($row) => $row->notes ?? '-')
                ->addColumn('balance_left', function ($row) use ($balances) {
                    $key = $row->employee_id.'-'.$row->leave_type_id;
                    $total = $balances[$key]->total ?? 0;
                    return $total > 0 ? $total . ' hari' : '-';
                })
                ->addColumn('attachment', function ($row) {
                    if ($row->attachment) {
                        return '<a href="'.asset('storage/'.$row->attachment).'" target="_blank" class="btn btn-sm btn-info">
                                    <i class="ri-attachment-2"></i> View
                                </a>';
                    }
                    return '-';
                })
                ->filter(function ($query) use ($request) {
                    if ($search = $request->input('search.value')) {
                        $query->where(function ($q) use ($search) {
                            $q->where('nik', 'like', "%{$search}%")
                            ->orWhere('employee_name', 'like', "%{$search}%")
                            ->orWhere('position', 'like', "%{$search}%")
                            ->orWhere('area', 'like', "%{$search}%")
                            ->orWhere('department', 'like', "%{$search}%")
                            ->orWhere('type', 'like', "%{$search}%");
                        });
                    }
                })
                ->addColumn('action', function ($row) {
                        return '<button class="btn btn-danger btn-sm delete-btn" data-id="'.encrypt($row->id).'">
                                    <i class="ri-delete-bin-line"></i>
                                </button>';
                })

                ->rawColumns(['action','attachment'])
                ->make(true);
        }
    }
    public function leaveHRDCreate(Request $request){
       $user = Auth::user();
       $areas = DB::table('areas')->pluck('name');
       $departments = DB::table('departments')->pluck('name');
       $positions = DB::table('master_position')->pluck('nama');
       $employees = DB::table('employees') ->where('status', '!=', 'Terminate')
       ->select('id','nik','fullname','area_id','department_id','position_id')
       ->get();
       $leaves = DB::table('leave_settings')
       ->where('type', 'like', 'normatif%')
       ->get();
       if ($request->ajax()) {
        $data = Employee::with(['area', 'department', 'position','leaveBalance' => function ($q) {
        $q->where('remaining_days', '>', 0)
          ->where('valid_to', '>=', now());
          }])
        ->where('status', '!=', 'Terminate') // FILTER DARI DROPDOWN
        ->whereHas('leaveBalance', function ($q) {
            $q->where('remaining_days', '>', 0)
            ->where('valid_to', '>=', now());
        })
        ->when($request->position && $request->position !== 'ALL', function ($q) use ($request) {
            $q->whereHas('position', function ($q2) use ($request) {
                $q2->where('nama', $request->position);
            });
        })
        ->when($request->area && $request->area !== 'ALL', function ($q) use ($request) {
            $q->whereHas('area', function ($q2) use ($request) {
                $q2->where('name', $request->area);
            });
        })
        ->when($request->department && $request->department !== 'ALL', function ($q) use ($request) {
            $q->whereHas('department', function ($q2) use ($request) {
                $q2->where('name', $request->department);
            });
        });
        $currentUserId = $user->employee->id;
        $data->select('id','nik', 'fullname', 'area_id', 'department_id', 'position_id')
                ->orderByRaw("id = ? DESC", [$currentUserId]);
        // dd($data->first());
        return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('area', fn($row) => $row->area?->name ?? '-')
        ->addColumn('department', fn($row) => $row->department?->name ?? '-')
        ->addColumn('position', fn($row) => $row->position?->nama ?? '-')
        ->addColumn('leave_balance', function ($row) {
            return $row->leaveBalance
                ->where('remaining_days', '>', 0)
                ->where('valid_to', '>=', now())
                ->sum('remaining_days') ?: '-';
        })
        ->filterColumn('area', function ($query, $keyword) {
            $query->whereHas('area', function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%");
            });
        })
        ->filterColumn('department', function ($query, $keyword) {
            $query->whereHas('department', function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%");
            });
        }) ->filterColumn('position', function ($query, $keyword) {
            $query->whereHas('position', function ($q) use ($keyword) {
                $q->where('nama', 'like', "%{$keyword}%");
            });
        })
        ->addIndexColumn()
        ->make(true);
        }
        return view("pages.attendance.sub-menu.employee-leave.requestForm", compact(
            'areas',
            'departments',
            'positions',
            'leaves',
            'employees',
            'user'
        ));
    }
    public function leaveHRDDestroy(Request $request)
    {
        DB::beginTransaction();

        try {
            $id = decrypt($request->id);

            $leave = DB::table('leave_requests')->where('id', $id)->first();

            if (!$leave) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            // ================= REFUND BALANCE (HANYA PRIBADI) =================
            if ($leave->type === 'pribadi') {

                $remainingToRestore = $leave->total_days;

                // ambil balance berdasarkan employee & type
                $balances = DB::table('leave_balances')
                    ->where('employee_id', $leave->employee_id)
                    ->where('leave_type_id', $leave->leave_type_id)
                    ->orderBy('valid_to', 'asc') // 🔥 urut dari yang paling cepat expired
                    ->get();

                foreach ($balances as $balance) {

                    if ($remainingToRestore <= 0) break;

                    // tambahkan kembali
                    DB::table('leave_balances')
                        ->where('id', $balance->id)
                        ->update([
                            'remaining_days' => $balance->remaining_days + $remainingToRestore
                        ]);

                    $remainingToRestore = 0;
                }
            }
            // ================= DELETE ATTENDANCE =================
            DB::table('employee_attendances')
                ->where('employee_id', $leave->employee_id)
                ->whereBetween('date', [$leave->start_date, $leave->end_date])
                ->where('source', 'like', 'leave%')
                ->delete();

            // ================= DELETE LEAVE =================
            DB::table('leave_requests')->where('id', $id)->delete();

            // ================= LOG =================
            $user = Auth::user();
            Log::create([
                'user_id'    => $user->id,
                'ip_address' => $request->ip(),
                'action'     => 'delete',
                'description'=> "{$user->employee->fullname} delete leave {$leave->employee_name} ({$leave->type}) {$leave->start_date} - {$leave->end_date}"
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Leave berhasil dihapus & saldo dikembalikan'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function leaveHRDStore(Request $request)
    {
        DB::beginTransaction();

        try {
            // ================= VALIDASI =================
            $request->validate([
                'leave_type' => 'required|in:pribadi,normatif',
                'employees' => 'required|json',
                'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048'
            ]);

            $employeeIds = json_decode($request->employees, true);
            if (!is_array($employeeIds) || count($employeeIds) === 0) {
                return response()->json(['message' => 'Pilih minimal 1 karyawan'], 422);
            }
            // ================= TYPE =================
            if ($request->leave_type === 'pribadi') {
                $request->validate([
                    'start_date_pribadi' => 'required|date',
                    'end_date_pribadi' => 'required|date'
                ]);
                $startDate = $request->start_date_pribadi;
                $endDate   = $request->end_date_pribadi;
            } else {
                $request->validate([
                    'type' => 'required|exists:leave_settings,id',
                    'start_date_normatif' => 'required|date',
                ]);
                $leave = DB::table('leave_settings')->where('id', $request->type)->first();

                $startDate = $request->start_date_normatif;
                // ambil salah satu employee untuk hitung kalender normatif
                $sampleEmployee = Employee::with([
                    'groupEmployees.groupEmployeeWorkhour.groupWorkHours.workhour.details'
                ])->find($employeeIds[0]);
                $normative = $this->calculateNormativeLeave(
                    $sampleEmployee,
                    $startDate,
                    $leave->number_of_days
                );
                $endDate = $normative['end_date']->toDateString();
                $leaveTypeId = $leave->id;
                $typeName    = $leave->type;
            }

            $attachmentPath = null;
            if ($request->leave_type === 'normatif') {
                if ($request->hasFile('attachment')) {
                    $attachmentPath = $request->file('attachment')->store('leave_attachments', 'public');
                }
            }
            if ($request->leave_type === 'pribadi') {
                $notes = $request->notes_pribadi;
            } else {
                $notes = $request->notes_normatif;
            }

            // ================= PRELOAD DATA =================
            $employees = Employee::with(['area', 'department', 'position'])
                ->whereIn('id', $employeeIds)
                ->get()
                ->keyBy('id');
            // 🔥 ambil holiday sekali (OPTIMAL)
            $holidays = DB::table('attendance_calendars')
                ->whereBetween('date', [$startDate, $endDate])
                ->pluck('name', 'date')
                ->toArray();
            // ================= LOOP =================
            foreach ($employeeIds as $empId) {
                $emp = $employees[$empId] ?? null;
                if (!$emp) continue;
                // ================= VALIDASI BENTROK =================
                $conflict = DB::table('employee_attendances')
                    ->where('employee_id', $empId)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->whereNotNull('source')
                    ->exists();
                if ($conflict) {
                    throw new \Exception("Karyawan {$emp->fullname} sudah memiliki data attendance pada periode tersebut");
                }
                // ================= LOGIC =================
                if ($request->leave_type === 'pribadi') {
                    // 🔥 ambil saldo
                    $balance = DB::table('leave_balances')
                        ->where('employee_id', $emp->id)
                        ->where('remaining_days', '>', 0)
                        ->orderBy('valid_to', 'asc')
                        ->first();

                    if (!$balance) {
                        throw new \Exception("Karyawan {$emp->fullname} tidak punya saldo");
                    }
                    $leaveTypeId = $balance->leave_type_id;
                    $typeName    = $balance->type;
                    $employees = Employee::with([
                        'area',
                        'department',
                        'position',
                        'groupEmployees.groupEmployeeWorkhour.groupWorkHours.workhour.details'
                    ])->whereIn('id', $employeeIds)->get()->keyBy('id');
                    $calc = $this->calculateDaysWithCalendar($emp, $startDate, $endDate, $request->leave_type);
                    $totalDays = $calc['days'];
                }
                if ($request->leave_type === 'normatif') {
                    $leaveTypeId = $leave->id;
                    $typeName    = $leave->type;
                    $totalDays   = $leave->number_of_days;
                }
                // ================= INSERT =================
                $result = DB::table('leave_requests')->insert([
                    'employee_id' => $emp->id,
                    'nik' => $emp->nik,
                    'employee_name' => $emp->fullname,
                    'position' => $emp->position->nama ?? '-',
                    'area' => $emp->area->name ?? '-',
                    'department' => $emp->department->name ?? '-',
                    'leave_type_id' => $leaveTypeId,
                    'type' => $typeName,
                    'request_date' => now(),
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'total_days' => $totalDays,
                    'notes' => $notes,
                    'attachment' => $request->leave_type === 'normatif' ? $attachmentPath : null,
                    'status' => 'approved',
                    'created_by' => Auth::user()->employee->fullname,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);


                // saldo & attendance
                if ($request->leave_type === 'pribadi') {
                    $this->deductLeaveBalance($emp->id, $leaveTypeId, $totalDays);
                }
                $this->generateAttendance($emp, $startDate, $endDate,$request->leave_type,
                $request->leave_type === 'normatif'
                    ? $leave->number_of_days
                    : null);
            }

            $user = Auth::user();
            Log::create([
                'user_id'    => $user->id,
                'ip_address' => $request->ip(),
                'action'     => 'insert',
                'description'=> "{$user->employee->fullname} (HRD) membuat leave {$typeName} untuk {$startDate} sampai {$endDate}"
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Leave request berhasil dibuat'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    private function calculateDaysWithCalendar(Employee $employee, $start, $end, $type)
    {
        $start = Carbon::parse($start);
        $end   = Carbon::parse($end);

        $dates = [];
        $excluded = [];

        $workingDays = $this->getActiveWorkDays($employee);

        // ambil holiday sesuai area
        $holidaysQuery = AttendanceCalendar::whereBetween('date', [
            $start->toDateString(),
            $end->toDateString()
        ])
        ->where('is_active', true)
        ->whereIn('type', ['national', 'company', 'cultural']);

        if ($employee->area_id == 1) {
            $holidaysQuery->where('is_hq', 1);
        } else {
            $holidaysQuery->where('is_hq', 0);
        }

        $holidays = $holidaysQuery->pluck('name', 'date')
            ->mapWithKeys(fn($name, $date) => [
                Carbon::parse($date)->toDateString() => $name
            ])
            ->toArray();

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $currentDate = $date->toDateString();
            $dayName = strtolower($date->format('l'));

            // ================= WORKHOUR CHECK =================
            if (!empty($workingDays) && !in_array($dayName, $workingDays, true)) {
                $excluded[] = [
                    'date' => $currentDate,
                    'type' => 'Non-working day'
                ];
                continue;
            }

            // ================= HOLIDAY CHECK =================
            if (isset($holidays[$currentDate])) {
                $excluded[] = [
                    'date' => $currentDate,
                    'type' => $holidays[$currentDate]
                ];
                continue;
            }

            // ================= FALLBACK =================
            if (empty($workingDays) && $date->isWeekend()) {
                $excluded[] = [
                    'date' => $currentDate,
                    'type' => 'Weekend'
                ];
                continue;
            }

            $dates[] = $currentDate;
        }

        return [
            'days' => count($dates),
            'dates' => $dates,
            'excluded' => $excluded
        ];
    }
     private function generateAttendance($emp, $start, $end, $type, $daysNeeded = null)
{
    if ($type === 'normatif' && $daysNeeded) {
        $normative = $this->calculateNormativeLeave($emp, $start, $daysNeeded);

        $start = $normative['start_date'];
        $end   = $normative['end_date'];
    }

    $calc = $this->calculateDaysWithCalendar($emp, $start, $end, $type);

    foreach ($calc['dates'] as $date) {

        $workhourData = $this->getWorkHourByDate($emp, $date);

        if (
            empty($workhourData['group_id']) ||
            empty($workhourData['master_workhour_id'])
        ) {
            throw new \Exception(
                "Karyawan {$emp->fullname} belum memiliki Group Workhour aktif pada tanggal {$date}."
            );
        }

        DB::table('employee_attendances')->updateOrInsert(
            [
                'employee_id' => $emp->id,
                'date' => $date
            ],
            [
                'area_name'          => $emp->area->name ?? '-',
                'department_name'    => $emp->department->name ?? '-',
                'position_name'      => $emp->position->nama ?? '-',
                'group_id'           => $workhourData['group_id'],
                'master_workhour_id' => $workhourData['master_workhour_id'],
                'work_in'            => $workhourData['work_in'],
                'work_out'           => $workhourData['work_out'],
                'attendance_status'  => 'leave',
                'source'             => "Leave - " . ucfirst($type),
                'created_by'         => Auth::user()->employee->fullname ?? 'System',
                'updated_by'         => Auth::user()->employee->fullname ?? 'System',
                'updated_at'         => now(),
                'created_at'         => now(),
            ]
        );
    }

    return $calc;
}
    private function deductLeaveBalance($employeeId, $leaveTypeId, $days)
    {
        $today = now()->toDateString();
        $balances = DB::table('leave_balances')
            ->where('employee_id', $employeeId)
            ->where('leave_type_id', $leaveTypeId) // 🔥 WAJIB
            ->where('valid_to', '>=', $today)
            ->where('remaining_days', '>', 0)
            ->orderBy('valid_to', 'asc')   // 🔥 paling cepat expired
            ->orderBy('valid_from', 'asc') // 🔥 tambahan (biar konsisten)
            ->lockForUpdate()
            ->get();
        $remaining = $days;
        foreach ($balances as $balance) {
            if ($remaining <= 0) break;
            if ($balance->remaining_days >= $remaining) {
                DB::table('leave_balances')
                    ->where('id', $balance->id)
                    ->update([
                        'remaining_days' => $balance->remaining_days - $remaining
                    ]);
                $remaining = 0;
            } else {
                DB::table('leave_balances')
                    ->where('id', $balance->id)
                    ->update([
                        'remaining_days' => 0
                    ]);
                $remaining -= $balance->remaining_days;
            }
        }
        if ($remaining > 0) {
            throw new \Exception("Sisa cuti tidak mencukupi");
        }
    }
    private function getActiveWorkDays(Employee $employee): array
    {
        $groupEmployee = $employee->groupEmployees; // 🔥 TANPA QUERY
        if (! $groupEmployee || ! $groupEmployee->groupEmployeeWorkhour) {
            return [];
        }
        $today = Carbon::today();
        $activeGroupWorkHour = $groupEmployee->groupEmployeeWorkhour->groupWorkHours
            ->filter(function ($groupWorkHour) use ($today) {
                if (! $groupWorkHour->start_date) return false;

                $startDate = Carbon::parse($groupWorkHour->start_date);
                $endDate = $groupWorkHour->end_date ? Carbon::parse($groupWorkHour->end_date) : null;

                return $startDate->lte($today)
                    && (!$endDate || $endDate->gte($today));
            })
            ->sortByDesc(fn($g) => Carbon::parse($g->start_date)->timestamp)
            ->first();

        if (! $activeGroupWorkHour || ! $activeGroupWorkHour->workhour) {
            return [];
        }

        return $activeGroupWorkHour->workhour->details
            ->map(fn($d) => strtolower($d->day))
            ->unique()
            ->values()
            ->toArray();
    }
    private function getWorkHourByDate($employee, $date)
    {
        $groupEmployee = $employee->groupEmployees;

        if (!$groupEmployee || !$groupEmployee->groupEmployeeWorkhour) {
            return [];
        }
        $targetDate = Carbon::parse($date);
        $groupWorkhour = $groupEmployee->groupEmployeeWorkhour;
        $activeGroupWorkHour = $groupWorkhour->groupWorkHours
            ->filter(function ($g) use ($targetDate) {
                if (!$g->start_date) return false;

                $start = Carbon::parse($g->start_date);
                $end   = $g->end_date ? Carbon::parse($g->end_date) : null;

                return $start->lte($targetDate) &&
                    (!$end || $end->gte($targetDate));
            })
            ->sortByDesc(fn($g) => Carbon::parse($g->start_date)->timestamp)
            ->first();

        if (!$activeGroupWorkHour || !$activeGroupWorkHour->workhour) {
            return [];
        }
        $dayName = strtolower($targetDate->format('l'));
        $detail = $activeGroupWorkHour->workhour->details
            ->first(fn($d) => strtolower($d->day) === $dayName);
        if (!$detail) {
            return [];
        }
        return [
            'group_id'           => $groupWorkhour->id ?? null,
            'master_workhour_id' => $activeGroupWorkHour->workhour->id ?? null,
            'work_in'            => $detail->work_in ?? null,
            'work_out'           => $detail->work_out ?? null,
        ];
    }
    private function calculateNormativeLeave($employee, $startDate, $daysNeeded)
    {
        $currentDate = Carbon::parse($startDate);
        $countedDays = 0;
        $excluded = [];

        // ambil working day sekali
        $workingDays = $this->getActiveWorkDays($employee);

        // estimasi range maksimal pencarian
        $maxDate = $currentDate->copy()->addDays($daysNeeded + 30);

        // ambil holiday sekali
        $holidays = AttendanceCalendar::whereBetween('date', [
                $currentDate->toDateString(),
                $maxDate->toDateString()
            ])
            ->where('is_active', true)
            ->get()
            ->keyBy(function ($item) {
                return Carbon::parse($item->date)->toDateString();
            });

        while ($countedDays < $daysNeeded) {
            $dayName = strtolower($currentDate->format('l'));
            $dateStr = $currentDate->toDateString();

            // cek dari collection, bukan query
            $holiday = $holidays->get($dateStr);

            if ($holiday) {
                $excluded[] = [
                    'date' => $dateStr,
                    'type' => $holiday->name
                ];
            } elseif (!in_array($dayName, $workingDays)) {
                $excluded[] = [
                    'date' => $dateStr,
                    'type' => 'Non-working day'
                ];
            } else {
                $countedDays++;
            }

            if ($countedDays < $daysNeeded) {
                $currentDate->addDay();
            }
        }

        return [
            'start_date' => Carbon::parse($startDate),
            'end_date'   => $currentDate,
            'total_days' => $daysNeeded,
            'excluded'   => $excluded
        ];
    }
    public function calculateNormatif(Request $request)
    {
        $employee = Auth::user()->employee;

        $leaveSetting = LeaveSetting::findOrFail($request->leave_setting_id);

        $data = $this->calculateNormativeLeave(
            $employee,
            $request->start_date,
            $leaveSetting->number_of_days
        );

        return response()->json([
            'start_date' => $data['start_date']->format('Y-m-d'),
            'end_date'   => $data['end_date']->format('Y-m-d'),
            'total_days' => $data['total_days'],
            'excluded'   => $data['excluded'] ?? []
        ]);
    }
    // private function restoreLeaveBalance($employeeId, $leaveTypeId, $days)
    // {
    //     $balances = DB::table('leave_balances')
    //         ->where('employee_id', $employeeId)
    //         ->where('leave_type_id', $leaveTypeId)
    //         ->orderBy('valid_to', 'asc')
    //         ->get();

    //     foreach ($balances as $balance) {

    //         if ($days <= 0) break;

    //         $used = $balance->used_days ?? 0;

    //         if ($used <= 0) continue;

    //         if ($used >= $days) {
    //             DB::table('leave_balances')
    //                 ->where('id', $balance->id)
    //                 ->update([
    //                     'used_days' => $used - $days,
    //                     'remaining_days' => $balance->remaining_days + $days
    //                 ]);

    //             break;
    //         } else {
    //             DB::table('leave_balances')
    //                 ->where('id', $balance->id)
    //                 ->update([
    //                     'used_days' => 0,
    //                     'remaining_days' => $balance->remaining_days + $used
    //                 ]);

    //             $days -= $used;
    //         }
    //     }
    // }

}
