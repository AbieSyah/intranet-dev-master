<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance\AttendancePermit;
use App\Models\Attendance\EmployeeAttendance;
use App\Models\Log;
use App\Notifications\AccountNotification;
use App\Models\Employee;
use App\Models\Master\LineApproval;
use App\Models\Master\LineApprovalEmployee;
use App\Notifications\AttendancePermitNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str as SupportStr;
use Yajra\DataTables\DataTables;

class AttendancePermitProfileController extends Controller
{
    public function profileIndex(){
        $user = Auth::user();
        $employeeId = $user->employee->id;
        $employee = $user->employee;
    // 🔥 cek apakah dia approver
    $isApprover = LineApproval::where('approval_type', 'Attendance Permit')
        ->where(function ($q) use ($employeeId) {
            $q->where('approve_1', $employeeId)
            ->orWhere('approve_2', $employeeId)
            ->orWhere('approve_3', $employeeId);
        })
        ->exists();
        return view('pages.profile.Attendance.attendance-permit.index', compact('user','isApprover'));
    }

    public function pendingCount()
    {
        $employee = auth()->user()->employee;

        $lineApprovals = LineApproval::where('approval_type', 'Attendance Permit')
        ->where(function ($q) use ($employee) {
            $q->where('approve_1', $employee->id);
            //   ->orWhere('approve_2', $employee->id)
            //   ->orWhere('approve_3', $employee->id)
            //   ->orWhere('approve_4', $employee->id)
            //   ->orWhere('approve_5', $employee->id)
            //   ->orWhere('approve_6', $employee->id)
            //   ->orWhere('approve_7', $employee->id)
            //   ->orWhere('approve_8', $employee->id);
        })
        ->with('employees')
        ->get();
        $totalApproval = 0;
        if ($lineApprovals->isNotEmpty()) {
            $employeeIds = $lineApprovals
                ->flatMap(fn($line) => $line->employees->pluck('id'))
                ->unique()
                ->toArray();
            // // ❗ exclude diri sendiri
            // $employeeIds = array_diff($employeeIds, [$employee->id]);
            // 🔥 hitung jumlah request waiting
            $totalApproval = AttendancePermit::whereIn('employee_id', $employeeIds)
                ->where('status', 'waiting')
                ->count();
        }

        return response()->json([
            'total' => $totalApproval
        ]);
    }

    private function formatType($type)
    {
        return match ($type) {
            'earlyout' => 'Pulang Cepat',
            'late' => 'Terlambat',
            'temporary_out' => 'Keluar Sementara',
            'sick' => 'Izin Dokter',
            default => 'Lainnya',
        };
    }

    public function dataMy()
    {
        $employee = auth()->user()->employee;

        $data = AttendancePermit::where('employee_id', $employee->id)->latest();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('date_permit', function ($row) {
                return $row->start_date && $row->end_date
                    ? $row->start_date . ' s/d ' . $row->end_date
                    : $row->start_date;
            })
            ->addColumn('start_time', function($row){
                return $row->start_time
                    ? Carbon::parse($row->start_time)->format('H:i')
                    : "-";
            })
            ->addColumn('end_time', function($row){
                return $row->end_time
                    ? Carbon::parse($row->end_time)->format('H:i')
                    : "-";
            })
            ->addColumn('type', function ($row) {
                return $this->formatType($row->type);
            })
            ->addColumn('workhour', function ($r) {
                $start = $r->work_in
                    ? Carbon::parse($r->work_in)->format('H:i')
                    : '';
                $end = $r->work_out
                    ? Carbon::parse($r->work_out)->format('H:i')
                    : '';
                return $start . ' - ' . $end;
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-success btn-sm btn-approve" data-id="'.$row->id.'">
                            <i class="bi bi-check-lg"></i>
                        </button>
                        <button class="btn btn-danger btn-sm btn-reject" data-id="'.$row->id.'">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                ';
            })
            ->addColumn('attachment', function ($row) {
                if ($row->attachment) {
                    return '<a href="' . asset('storage/' . $row->attachment) . '"
                            target="_blank" class="btn btn-info btn-sm">
                            <i class="ri-eye-line"></i>
                        </a>';
                }
                return '-';
            })
            ->rawColumns(['action','attachment'])
            ->make(true);
    }
    public function dataApproval()
    {
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
        // exclude diri sendiri
        // $employeeIds = array_diff($employeeIds, [$employee->id]);
        // ambil permit
        $data = AttendancePermit::whereIn('employee_id', $employeeIds)
            ->where('status', 'waiting')->latest();

        return DataTables::of($data)
            ->addColumn('date_permit', fn($r) =>
                $r->start_date . ($r->end_date ? ' s/d '.$r->end_date : '')
            )
            ->addColumn('time_permit', function ($row) {
                $start = $row->start_time
                    ? Carbon::parse($row->start_time)->format('H:i')
                    : null;

                $end = $row->end_time
                    ? Carbon::parse($row->end_time)->format('H:i')
                    : null;

                if ($start && $end) {
                    return "$start - $end"; // normal
                } if ($start) {
                    return "Masuk : $start"; // hanya start
                } if ($end) {
                    return "Keluar : $end"; // hanya end
                }
                return '-';
            })
            ->addColumn('type', fn($r) => $this->formatType($r->type))
            ->addColumn('workhour', function ($r) {
                $start = $r->work_in
                    ? Carbon::parse($r->work_in)->format('H:i')
                    : '';
                $end = $r->work_out
                    ? Carbon::parse($r->work_out)->format('H:i')
                    : '';
                return $start . ' - ' . $end;
            })
            ->addColumn('action', function ($row) {
                return '
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-success btn-sm btn-approve" data-id="'.$row->id.'">
                            <i class="bi bi-check-lg"></i>
                        </button>
                        <button class="btn btn-danger btn-sm btn-reject" data-id="'.$row->id.'">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                ';
            })
            ->addColumn('attachment', function ($row) {
                if ($row->attachment) {
                    return '<a href="' . asset('storage/' . $row->attachment) . '"
                            target="_blank" class="btn btn-info btn-sm">
                            <i class="ri-eye-line"></i>
                        </a>';
                }
                return '-';
            })
            ->rawColumns(['action','attachment'])
            ->make(true);
    }
    public function dataApprovalHistory()
    {
        $employee = auth()->user()->employee;
        // filter hanya Attendance
        $lineApprovals = LineApproval::where('approval_type', 'Attendance Permit')
            ->where(function ($q) use ($employee) {
                $q->where('approve_1', $employee->id);
            })
            ->with('employees') // biar lebih efisien
            ->get();
        if ($lineApprovals->isEmpty()) {
            return DataTables::of(collect())->make(true);
        }
        // ambil semua employee dari semua line approval
        $employeeIds = $lineApprovals
            ->flatMap(fn($line) => $line->employees->pluck('id'))
            ->unique()
            ->toArray();
        // exclude diri sendiri
        $employeeIds = array_diff($employeeIds, [$employee->id]);
        // ambil permit
        $data = AttendancePermit::whereIn('employee_id', $employeeIds)
        ->whereIn('status', ['approved','rejected']);
        return DataTables::of($data)
            ->addColumn('date_permit', fn($r) =>
                $r->start_date . ($r->end_date ? ' s/d '.$r->end_date : '')
            )
            ->addColumn('time_permit', function ($row) {
                $start = $row->start_time
                    ? Carbon::parse($row->start_time)->format('H:i')
                    : null;

                $end = $row->end_time
                    ? Carbon::parse($row->end_time)->format('H:i')
                    : null;

                if ($start && $end) {
                    return "$start - $end"; // normal
                }

                if ($start) {
                    return "Masuk : $start"; // hanya start
                }

                if ($end) {
                    return "Keluar : $end"; // hanya end
                }

                return '-';
            })
            ->addColumn('type', fn($r) => $this->formatType($r->type))
            ->addColumn('workhour', function ($r) {
                $start = $r->work_in
                    ? Carbon::parse($r->work_in)->format('H:i')
                    : '';
                $end = $r->work_out
                    ? Carbon::parse($r->work_out)->format('H:i')
                    : '';
                return $start . ' - ' . $end;
            })
            ->addColumn('attachment', function ($row) {
                    if ($row->attachment) {
                        return '<a href="' . asset('storage/' . $row->attachment) . '"
                                target="_blank" class="btn btn-info btn-sm">
                                <i class="ri-eye-line"></i>
                            </a>';
                    }
                    return '-';
                })
            // ->addColumn('action', function ($row) {
            //     return '
            //         <button class="btn btn-info btn-sm detail-btn"
            //             data-id="'.encrypt($row->id).'">
            //             <i class="ri-eye-line"></i>
            //         </button>
            //     ';
            // })
            ->rawColumns(['attachment'])
            ->make(true);
    }
    public function reject(Request $request)
    {
        $permit = AttendancePermit::findOrFail($request->id);
        $permit->status = 'rejected';
        $permit->reason_reject = $request->reason;
        $permit->save();

        return response()->json(['message' => 'Berhasil di-reject']);
    }
    public function approve(Request $request)
    {
        DB::beginTransaction();

        try {
            $permit = AttendancePermit::findOrFail($request->id);
            // ❌ hindari double approve
            if ($permit->status === 'approved') {
                throw new \Exception('Data sudah di-approve sebelumnya');
            }
            $user = auth()->user();
            $employee = $permit->employee;
            // 🔥 update status approval
            $permit->update([
                'status' => 'approved',
                'approved_by_name' => $user->employee->fullname,
                'approved_by_position' => $user->employee->position->nama ?? null,
                'approved_by_at' => now(),
            ]);

            // 🔥 generate attendance
            $this->generateAttendance(
                $employee,
                $permit->start_date,
                $permit->end_date ?? $permit->start_date,
                $permit->type
            );

            DB::commit();

            return response()->json([
                'message' => 'Berhasil di-approve & attendance dibuat'
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    private function generateAttendance($emp, $start, $end, $type)
    {
        $startDate = Carbon::parse($start);
        $endDate = $end ? Carbon::parse($end) : $startDate;

        // 🔥 ambil group
        // $groupEmployee = $emp->groupEmployees->first();
        // $groupId = $groupEmployee->groupEmployeeWorkhour->id ?? null;
        // $groupName = $groupEmployee->groupEmployeeWorkhour->name ?? null;
        $formattedType = $this->formatType($type);
        // $workName = $workhourData->workhour->work_name ?? null;

        while ($startDate->lte($endDate)) {

            $date = $startDate->toDateString();

            // 🔥 ambil workhour per tanggal (FIX BESAR)
            $workhourData = $this->getWorkHourByDate($emp, $date);

            DB::table('employee_attendances')->updateOrInsert(
                [
                    'employee_id' => $emp->id,
                    'date' => $date
                ],
                [
                    'area_name' => $emp->area->name ?? '-',
                    'department_name' => $emp->department->name ?? '-',
                    'position_name' => $emp->position->nama ?? '-',

                    // GROUPd AND MASTER WORKHOUR
                    'group_id' => $workhourData['group_id'] ?? null,
                    'master_workhour_id' => $workhourData['master_workhour_id'] ?? null,
                    'work_in' => $workhourData['work_in'] ?? null,
                    'work_out' => $workhourData['work_out'] ?? null,

                    'attendance_status' => 'permit',
                    'source' => "Izin - $formattedType",

                    'created_by' => auth()->user()->employee->fullname,
                    'updated_by' => auth()->user()->employee->fullname,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            // dd($workhourData);

            $startDate->addDay();
        }
    }
    private function getWorkHourByDate($employee, $date)
    {
        $groupEmployee = $employee->groupEmployees->first();

        if (!$groupEmployee || !$groupEmployee->groupEmployeeWorkhour) {
            return null;
        }

        $targetDate = Carbon::parse($date);
        $groupWorkhour = $groupEmployee->groupEmployeeWorkhour;

        // 🔥 ambil group workhour aktif berdasarkan tanggal
        $activeGroupWorkHour = $groupEmployee->groupEmployeeWorkhour->groupWorkHours
            ->filter(function ($g) use ($targetDate) {
                if (!$g->start_date) return false;

                $start = Carbon::parse($g->start_date);
                $end = $g->end_date ? Carbon::parse($g->end_date) : null;

                return $start->lte($targetDate) && (!$end || $end->gte($targetDate));
            })
            ->sortByDesc(fn($g) => Carbon::parse($g->start_date)->timestamp)
            ->first();

        if (!$activeGroupWorkHour || !$activeGroupWorkHour->workhour) {
            return null;
        }

        // 🔥 ambil nama hari dari tanggal loop
        $dayName = strtolower($targetDate->format('l'));

        $detail = $activeGroupWorkHour->workhour->details
            ->first(function ($d) use ($dayName) {
                return strtolower($d->day) === $dayName;
            });

        if (!$detail) {
            return null;
        }

        return [
            'group_id' => $groupWorkhour->id ?? null,
            'master_workhour_id' => $activeGroupWorkHour->workhour->id ?? null,
            'work_in' => $detail->work_in,
            'work_out' => $detail->work_out,
        ];
    }
    public function profileCreate(Request $request){
        $user = Auth::user();
        $approvers = [];
        $lineApproval = $user->employee->LineApprovals()->first();
        if ($lineApproval) {
            for ($i = 1; $i <= 8; $i++) {
                $field = "approve_$i";

                if ($lineApproval->$field) {
                    $emp = DB::table('employees')
                        ->where('id', $lineApproval->$field)
                        ->value('fullname');

                    $approvers[$field] = $emp;
                } else {
                    $approvers[$field] = null;
                }
            }
        }
        if ($request->ajax()) {
            $currentUserId = $user->employee->id;
            // ambil line approval milik user login
            $lineApprovalIds = LineApproval::where('employee_id', $currentUserId)
                ->pluck('line_approval_id')
                ->where('approval type === Attendance Permit');
            $data = Employee::with(['area', 'department', 'position','leaveBalance'])
                ->where('status', '!=', 'Terminate');
            if ($lineApprovalIds->isNotEmpty()) {
                $data->whereHas('lineApprovals', function ($q) use ($lineApprovalIds) {
                    $q->whereIn('master_line_approval.id', $lineApprovalIds);
                });
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
        return view('pages.profile.Attendance.attendance-permit.form',compact(
            'user',
            'approvers',
        ));
    }
    public function profileStore(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'reason' => 'nullable|string',
            'attachment' => 'nullable|file|max:2048'
        ]);

        DB::beginTransaction();

        try {
            $user = Auth::user();
            $employee = $user->employee;

            if (!$employee) {
                throw new \Exception('Employee tidak ditemukan');
            }

            // set end_date default
            $startDate = Carbon::parse($request->start_date);
            $endDate = $request->end_date
                ? Carbon::parse($request->end_date)
                : $startDate;

            // ======================= VALIDASI OVERLAP ATTENDANCE PERMIT ==============================
            $existingLeave = AttendancePermit::where('employee_id', $employee->id)
                ->whereIn('status', ['waiting', 'approved'])
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q2) use ($startDate, $endDate) {
                        $q2->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
                })
                ->first();
            if ($existingLeave) {
                $rangeDate = '('
                    . Carbon::parse($existingLeave->start_date)->format('d M Y')
                    . ' - '
                    . Carbon::parse($existingLeave->end_date)->format('d M Y')
                    . ')';
                if ($existingLeave->status === 'waiting') {
                    throw new \Exception(
                        'Anda sudah membuat pengajuan izin pada rentang tanggal tersebut '
                        . $rangeDate .
                        ' dan masih menunggu approval. Harap bersabar.'
                    );
                }
                if ($existingLeave->status === 'approved') {
                    throw new \Exception(
                        'Anda sudah memiliki Izin yang telah disetujui pada rentang tanggal tersebut '
                        . $rangeDate
                    );
                }
            }
            // ======================= VALIDASI CONFLICT WITH EMPLOYEE ATTENDANCE RECORDS =======================
            $conflict = EmployeeAttendance::where('employee_id', $employee->id)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->whereNotNull('source')
                    ->first();

            if ($conflict) {
                throw new \Exception(
                    "Anda sudah memiliki data attendance pada tanggal "
                    . Carbon::parse($conflict->date)->format('d M Y')
                    . " dengan status {$conflict->source}"
                );
            }

            // ambil approver
            $lineApproval = $employee->lineApprovals()
                ->where('approval_type', 'Attendance Permit')
                ->first();

            $approver = $lineApproval?->approve_1;

            if (!$approver) {
                throw new \Exception('Approver tidak ditemukan');
            }

            // ambil workhour berdasarkan start_date
            $workhour = $this->getWorkHourByDate($employee, $startDate);

            if (!$workhour) {
                throw new \Exception('Workhour tidak ditemukan pada tanggal tersebut');
            }

            // upload file
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')
                    ->store('attendance_permits', 'public');
            }

            // generate token
            $token = (string) SupportStr::uuid();

            // simpan
            $data = AttendancePermit::create([
                'employee_id' => $employee->id,
                'nik' => $employee->nik,
                'employee_name' => $employee->fullname,
                'position' => $employee->position->nama ?? null,
                'area' => $employee->area->name ?? null,
                'department' => $employee->department->name ?? null,

                'type' => $request->type,
                'reason' => $request->reason,

                'start_date' => $startDate,
                'end_date' => $endDate,

                'start_time' => $request->start_time,
                'end_time' => $request->end_time,

                'work_in' => $workhour['work_in'],
                'work_out' => $workhour['work_out'],

                'attachment' => $attachmentPath,

                'status' => 'waiting',
                'approval_token' => $token,
                'created_by' => $user->name,
            ]);

            // kirim notif
            $approverEmployee = Employee::find($approver);

            if ($approverEmployee && $approverEmployee->user) {

                $typeLabel = match ($request->type) {
                    'earlyout' => 'Pulang Cepat',
                    'late' => 'Terlambat',
                    'pribadi' => 'Izin Pribadi',
                    'temporary_out' => 'Keluar Sementara',
                    'sick' => 'Izin Dokter',
                    default => 'Lainnya',
                };

                $details = [
                    'greeting' => 'Hi ' . $approverEmployee->fullname,
                    'subject' => 'Permintaan Izin',
                    'lines' => [
                        'Ada permintaan "' . $typeLabel . '"',
                        '',
                        'Nama Karyawan : ' . $employee->fullname,
                        'Alasan        : ' . ($request->reason ?? '-'),
                        'Tanggal       : ' . $startDate->format('Y-m-d') .
                            ($endDate->ne($startDate) ? ' s/d ' . $endDate->format('Y-m-d') : ''),
                        'Jam Masuk     : ' . ($request->start_time ?? '-'),
                        'Jam Keluar    : ' . ($request->end_time ?? '-'),
                    ],
                    'actionText' => 'Approve Sekarang',
                    'actionURL' => route('attendance.approval', ['token' => $token]) . '#pill-approval',
                    'thanks' => 'Terimakasih'
                ];
                $approverEmployee->user->notify(new AttendancePermitNotification($details));
            }
            DB::commit();
            return response()->json([
                'message' => 'Request berhasil dikirim'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
