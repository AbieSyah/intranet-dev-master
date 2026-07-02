<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance\AttendanceCalendar;
use App\Models\Attendance\EmployeeAttendance;
use App\Models\Attendance\LeaveBalance;
use App\Models\Attendance\LeaveRequest;
use App\Models\Attendance\LeaveRequestApproval;
use App\Models\Attendance\LeaveSetting;
use App\Models\Employee;
use App\Models\Log;
use App\Models\Master\LineApproval;
use App\Notifications\AttendancePermitNotification;
use App\Notifications\BulkLeaveApprovalNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
// use Illuminate\Notifications\Notification;

use Illuminate\Support\Facades\Notification;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmployeeLeaveProfileController extends Controller
{
    public function index(){
        $user = Auth::user();
        $employeeId = $user->employee->id;
        $employee = $user->employee;
        $isApprover = LineApproval::where('approval_type', 'Attendance Leave')
        ->where(function ($q) use ($employeeId) {
            $q->where('approve_1', $employeeId)
            ->orWhere('approve_2', $employeeId)
            ->orWhere('approve_3', $employeeId);
        })
        ->exists();
        $currentYear = now()->year;
        $lastYear = now()->year - 1;

        $currentBalance = $employee->leaveBalance()
            ->whereYear('valid_from', $currentYear)
            ->whereDate('valid_to', '>=', now())
            ->latest()
            ->first();

        $lastBalance = $employee->leaveBalance()
            ->whereYear('valid_from', $lastYear)
            ->whereDate('valid_to', '>=', now())
            ->latest()
            ->first();

        $expiredBalance = $employee->leaveBalance()
            ->whereDate('valid_to', '<', now())
            ->latest('valid_to')
            ->first();
        return view ('pages.profile.Attendance.employee-leave.index', compact(
            'user',
            'isApprover',
            'currentBalance',
            'lastBalance',
            'expiredBalance'
            ));
    }
    public function myData(Request $request)
    {
        if ($request->ajax()) {
            $employeeId = auth()->user()->employee->id;
            $data = LeaveRequest::with(['approvals'])
                ->where('employee_id', $employeeId)
                ->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('request_date', function ($row) {
                    return Carbon::parse($row->request_date)->format('d M Y');
                })
                ->addColumn('date_range', function ($row) {
                    $start = Carbon::parse($row->start_date)->format('d M Y');
                    $end   = Carbon::parse($row->end_date)->format('d M Y');
                    return $start == $end
                        ? $start
                        : $start . ' - ' . $end;
                })
                ->addColumn('type', fn($row) => $row->type ?? '-')
                ->addColumn('total_days', fn($row) => $row->total_days . ' Hari')
                ->addColumn('attachment', function ($row) {
                    if ($row->attachment) {
                        return '<a href="'.asset('storage/'.$row->attachment).'" target="_blank" class="btn btn-sm btn-info">
                                    <i class="ri-attachment-2"></i> View
                                </a>';
                    }
                    return '-';
                })
                ->addColumn('notes', fn($row) => $row->notes ?? '-')
                ->addColumn('approval_1', function ($row) {
                    $approval = $row->approvals->where('level', 1)->first();
                    if (!$approval) {
                        return '<span class="badge bg-secondary">-</span>';
                    }
                    if ($approval->status === 'approved') {
                        return '<span class="badge bg-success">Approved by '.$approval->approver_name.'</span>';
                    }
                    if ($approval->status === 'rejected') {
                        return '<span class="badge bg-danger">
                                    Rejected by '.$approval->approver_name.'
                                </span><br>
                                <small class="text-danger">'.$approval->reason_reject.'</small>';
                    }
                    if ($approval->status === 'waiting') {
                        return '<span class="badge bg-warning">Waiting</span>';
                    }
                    return '<span class="badge bg-secondary">'
                            .ucfirst($approval->status).
                        '</span>';
                })
                ->addColumn('approval_2', function ($row) {
                    $approval = $row->approvals->where('level', 2)->first();
                    if (!$approval) {
                        return '<span class="badge bg-secondary">-</span>';
                    }
                    if ($approval->status === 'approved') {
                        return '<span class="badge bg-success">Approved by '.$approval->approver_name.'</span>';
                    }
                    if ($approval->status === 'rejected') {
                        return '<span class="badge bg-danger">
                                    Rejected by '.$approval->approver_name.'
                                </span><br>
                                <small class="text-danger">'.$approval->reason_reject.'</small>';
                    }
                    if ($approval->status === 'waiting') {
                        return '<span class="badge bg-warning">Waiting</span>';
                    }
                    if ($approval->status === 'pending') {
                        return '<span class="badge bg-secondary">Pending</span>';
                    }
                    return '<span class="badge bg-secondary">'
                            .ucfirst($approval->status).
                        '</span>';
                })
                ->addColumn('approval_3', function ($row) {
                    $approval = $row->approvals->where('level', 3)->first();
                    if (!$approval) {
                        return '<span class="badge bg-secondary">-</span>';
                    }
                    if ($approval->status === 'approved') {
                        return '<span class="badge bg-success">Approved by '.$approval->approver_name.'</span>';
                    }
                    if ($approval->status === 'rejected') {
                        return '<span class="badge bg-danger">
                                    Rejected by '.$approval->approver_name.'
                                </span><br>
                                <small class="text-danger">'.$approval->reason_reject.'</small>';
                    }
                    if ($approval->status === 'waiting') {
                        return '<span class="badge bg-warning">Waiting</span>';
                    }
                    if ($approval->status === 'pending') {
                        return '<span class="badge bg-secondary">Pending</span>';
                    }
                    return '<span class="badge bg-secondary">'
                            .ucfirst($approval->status).
                        '</span>';
                })
                ->addColumn('action', function ($row) {
                        $button = '';
                            $button .= '
                                <button title="Edit" data-id="'.encrypt($row->id).'"
                                    class="btn btn-warning btn-sm edit-btn">
                                    <i class="ri-edit-line"></i>
                                </button>';
                        return $button ?: '-';
                    })
                ->rawColumns(['attachment','action','approval_1','approval_2','approval_3'])
                ->make(true);
        }
    }
    public function dataApproval(Request $request)
    {
        if ($request->ajax()) {
            $employee = auth()->user()->employee;

            $data = LeaveRequestApproval::with(['leaveRequest'])
                ->where('approver_id', $employee->id)
                ->where('status', 'waiting')
                ->latest();

            return DataTables::of($data)

                ->addColumn('nik', fn($row) => $row->leaveRequest->nik ?? '-')
                ->addColumn('employee_name', fn($row) => $row->leaveRequest->employee_name ?? '-')
                ->addColumn('area', fn($row) => $row->leaveRequest->area ?? '-')
                ->addColumn('department', fn($row) => $row->leaveRequest->department ?? '-')
                ->addColumn('position', fn($row) => $row->leaveRequest->position ?? '-')

                ->addColumn('request_date', function ($row) {
                    return $row->leaveRequest?->request_date
                        ? Carbon::parse($row->leaveRequest->request_date)->format('d M Y')
                        : '-';
                })

                ->addColumn('date_range', function ($row) {
                    if (!$row->leaveRequest) return '-';

                    $start = Carbon::parse($row->leaveRequest->start_date)->format('d M Y');
                    $end   = Carbon::parse($row->leaveRequest->end_date)->format('d M Y');

                    return $start == $end ? $start : "$start - $end";
                })

                ->addColumn('type', fn($row) => $row->leaveRequest->type ?? '-')
                ->addColumn('total_days', fn($row) => $row->leaveRequest->total_days ?? '-')

                ->addColumn('attachment', function ($row) {
                    if ($row->leaveRequest?->attachment) {
                        return '<a href="' . asset('storage/' . $row->leaveRequest->attachment) . '"
                                target="_blank" class="btn btn-info btn-sm">
                                <i class="ri-eye-line"></i>
                            </a>';
                    }
                    return '-';
                })
                ->addColumn('notes', fn($row) => $row->leaveRequest->notes ?? '-')
                ->addColumn('status', fn($row) => $row->status)
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
                    ->addColumn('checkbox', function ($row) {
                        return '<input type="checkbox" class="form-check-input row-checkbox" value="'.$row->id.'">';
                    })
                ->rawColumns(['attachment', 'action','checkbox'])
                ->make(true);
        }
    }
    public function dataApprovalHistory(Request $request)
    {
        if ($request->ajax()) {
            $employee = auth()->user()->employee;
            $data = LeaveRequestApproval::with(['leaveRequest'])
                ->where('approver_id', $employee->id)
                ->whereIn('status', ['approved','rejected'])
                ->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nik', fn($row) => $row->leaveRequest->nik ?? '-')
                ->addColumn('employee_name', fn($row) => $row->leaveRequest->employee_name ?? '-')
                ->addColumn('area', fn($row) => $row->leaveRequest->area ?? '-')
                ->addColumn('department', fn($row) => $row->leaveRequest->department ?? '-')
                ->addColumn('position', fn($row) => $row->leaveRequest->position ?? '-')

                ->addColumn('request_date', function ($row) {
                    return $row->leaveRequest?->request_date
                        ? Carbon::parse($row->leaveRequest->request_date)->format('d M Y')
                        : '-';
                })
                ->addColumn('date_range', function ($row) {
                    if (!$row->leaveRequest) return '-';
                    $start = Carbon::parse($row->leaveRequest->start_date)->format('d M Y');
                    $end   = Carbon::parse($row->leaveRequest->end_date)->format('d M Y');
                    return $start == $end ? $start : "$start - $end";
                })
                ->addColumn('type', fn($row) => $row->leaveRequest->type ?? '-')
                ->addColumn('total_days', fn($row) => $row->leaveRequest->total_days ?? '-')
                ->addColumn('attachment', function ($row) {
                    if ($row->leaveRequest?->attachment) {
                        return '<a href="' . asset('storage/' . $row->leaveRequest->attachment) . '"
                                target="_blank" class="btn btn-info btn-sm">
                                <i class="ri-eye-line"></i>
                            </a>';
                    }
                    return '-';
                })
                ->addColumn('notes', fn($row) => $row->leaveRequest->notes ?? '-')
                ->addColumn('status', fn($row) => $row->status)
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
                    ->addColumn('checkbox', function ($row) {
                        return '<input type="checkbox" class="form-check-input row-checkbox" value="'.$row->id.'">';
                    })
                ->rawColumns(['attachment', 'action','checkbox'])
                ->make(true);
        }
    }
    public function create(Request $request){
        $user = Auth::user();
        $leaves = DB::table('leave_settings')
        ->where('type', 'like', 'normatif%')
        ->get();
        $currentYear = now()->year;
        $lastYear = now()->year - 1;
        $currentBalance = $user->employee->leaveBalance()
            ->whereYear('valid_from', $currentYear)
            ->whereDate('valid_to', '>=', now())
            ->latest()
            ->first();
        $lastBalance = $user->employee->leaveBalance()
            ->whereYear('valid_from', $lastYear)
            ->whereDate('valid_to', '>=', now())
            ->latest()
            ->first();

        $currentUserId = $user->employee->id;
        $approvers = [];
        $lineApproval = $user->employee->lineApprovals()
        ->where('approval_type', 'Attendance Leave')
        ->first();
        if ($lineApproval) {
            for ($i = 1; $i <= 8; $i++) {
                $field = "approve_$i";

                if ($lineApproval->$field) {
                    $ids = collect([
                        $lineApproval->approve_1,
                        $lineApproval->approve_2,
                        $lineApproval->approve_3,
                        $lineApproval->approve_4,
                        $lineApproval->approve_5,
                        $lineApproval->approve_6,
                        $lineApproval->approve_7,
                        $lineApproval->approve_8,
                    ])->filter();

                    $employees = Employee::whereIn('id', $ids)
                        ->pluck('fullname', 'id');

                    $approvers[$field] = $employees[$lineApproval->$field] ?? null;
                } else {
                    $approvers[$field] = null;
                }
            }
        }
        return view("pages.profile.Attendance.employee-leave.form", compact(
            'user',
            'leaves',
            'currentBalance',
            'lastBalance',
            'approvers'
        ));
    }
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = Auth::user();
            $employee = $user->employee;
            if (!$employee) {
                throw new \Exception('Employee tidak ditemukan');
            }
            $leaveTypeMode = $request->leave_type;
            // ========================= PARSING DATA =========================
            if ($leaveTypeMode == 'pribadi') {

                $request->validate([
                    'start_date_pribadi' => 'required|date',
                    'end_date_pribadi'   => 'required|date|after_or_equal:start_date_pribadi',
                    'notes'              => 'nullable|string',
                    'attachment'         => 'nullable|file|max:2048',
                ]);
                $startDate = Carbon::parse($request->start_date_pribadi);
                $endDate   = Carbon::parse($request->end_date_pribadi);
                $joinDate = Carbon::parse($employee->joindate);
                $yearsOfService = $joinDate->diffInYears(now());
                $leaveSetting = LeaveSetting::where('type', 'pribadi')
                    ->where('min_years', '<=', $yearsOfService)
                    ->where(function ($q) use ($yearsOfService) {
                        $q->where('max_years', '>=', $yearsOfService)
                        ->orWhereNull('max_years');
                    })
                    ->orderByDesc('min_years')
                    ->first();
                // ========================= HITUNG HARI CUTI PRIBADI =========================
                $daysData = $this->calculateDaysOnly($employee, $startDate, $endDate);
                $totalDays = $daysData['days'];
            } else {
                $request->validate([
                    'type'                => 'required|exists:leave_settings,id',
                    'start_date_normatif' => 'required|date',
                    'attachment'          => 'required|file|max:2048',
                    'notes'               => 'nullable|string',
                ]);
                $leaveSetting = LeaveSetting::findOrFail($request->type);
                // ========================= HITUNG CUTI NORMATIF =========================
                $normativeData = $this->calculateNormativeLeave(
                    $employee,
                    $request->start_date_normatif,
                    $leaveSetting->number_of_days
                );
                $startDate = $normativeData['start_date'];
                $endDate   = $normativeData['end_date'];
                $totalDays = $normativeData['total_days'];
            }
            if ($totalDays <= 0) {
                throw new \Exception('Jumlah hari cuti tidak valid');
            }
            // ============================ VALIDASI BENTROK LEAVE REQUEST ==============================
            $existingLeave = LeaveRequest::where('employee_id', $employee->id)
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
                        'Anda sudah membuat pengajuan cuti pada rentang tanggal tersebut '
                        . $rangeDate .
                        ' dan masih menunggu approval. Harap bersabar.'
                    );
                }
                if ($existingLeave->status === 'approved') {
                    throw new \Exception(
                        'Anda sudah memiliki cuti yang telah disetujui pada rentang tanggal tersebut '
                        . $rangeDate
                    );
                }
            }
            // ========================= VALIDASI BENTROK DENGAN EMPLOYEE ATTENDANCE RECORD =====================
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
            // dd($totalDays);
            // ========================= VALIDASI SALDO =========================
            $leaveBalance = null;

            if ($leaveTypeMode == 'pribadi') {
                $leaveBalance = LeaveBalance::where('employee_id', $employee->id)
                    ->where('leave_type_id', $leaveSetting->id)
                    ->whereDate('valid_to', '>=', now())
                    ->orderBy('valid_to')
                    ->first();

                if (!$leaveBalance || $leaveBalance->remaining_days < $totalDays) {
                    throw new \Exception('Saldo cuti tidak mencukupi');
                }
            }
            // ========================= UPLOAD FILE =========================
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')
                    ->store('leave_attachments', 'public');
            }
            // ========================= SIMPAN REQUEST =========================
            $leaveRequest = LeaveRequest::create([
                'employee_id'   => $employee->id,
                'nik'           => $employee->nik,
                'employee_name' => $employee->fullname,
                'position'      => $employee->position->nama ?? null,
                'area'          => $employee->area->name ?? null,
                'department'    => $employee->department->name ?? null,

                'leave_type_id' => $leaveSetting->id,
                'type'          => $leaveSetting->type,
                'request_date'  => now(),
                'start_date'    => $startDate,
                'end_date'      => $endDate,
                'total_days'    => $totalDays,
                'attachment'    => $attachmentPath,
                'notes'         => $request->notes,

                'status'        => 'waiting',
                'created_by'    => $user->name,
            ]);
            // ========================= GENERATE APPROVAL =========================
            $lineApproval = $employee->lineApprovals()
                ->where('approval_type', 'Attendance Leave')
                ->first();

            if (!$lineApproval) {
                throw new \Exception('Approval line tidak ditemukan');
            }
            for ($i = 1; $i <= 3; $i++) {
                $approverId = $lineApproval->{'approve_'.$i};

                if ($approverId) {
                    $approver = Employee::find($approverId);

                    LeaveRequestApproval::create([
                        'leave_request_id' => $leaveRequest->id,
                        'approver_id'      => $approver->id,
                        'approver_name'    => $approver->fullname,
                        'position'         => $approver->position->nama ?? '-',
                        'department'       => $approver->department->name ?? '-',
                        'level'            => $i,
                        'status'           => $i == 1 ? 'waiting' : 'pending',
                        'approval_token'   => Str::uuid(),
                    ]);
                }
            }
            // ========================= NOTIF APPROVER 1 =========================
            $firstApproval = $leaveRequest->approvals()->where('level', 1)->first();

            if ($firstApproval && $firstApproval->approver?->user) {
                $details = [
                    'greeting' => 'Hi '.$firstApproval->approver_name,
                    'subject' => 'Permintaan Cuti',
                    'lines' => [
                        'Ada permintaan cuti baru',
                        'Nama: '.$employee->fullname,
                        'Tanggal: '.$startDate->format('d M Y').' s/d '.$endDate->format('d M Y'),
                        'Durasi: '.$totalDays.' hari',
                    ],
                    'actionText' => 'Approve Sekarang',
                    'actionURL' => route('leave-request.approval', [
                        'token' => $firstApproval->approval_token
                    ]) . '#pill-approval',
                    'thanks' => 'Terimakasih'
                ];

                $firstApproval->approver->user
                    ->notify(new AttendancePermitNotification($details));
            }

            $user = Auth::user();
            Log::create([
                'user_id'    => $user->id,
                'ip_address' => $request->ip(),
                'action'     => 'insert',
                'description'=> "{$user->employee->fullname} mengajukan cuti {$leaveSetting->type} untuk {$startDate} sampai {$endDate}"
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Pengajuan cuti berhasil dikirim'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    private function calculateDaysOnly($employee, $startDate, $endDate): array
    {
        $start = Carbon::parse($startDate);
        $end   = Carbon::parse($endDate);

        $days = 0;
        $excluded = [];
        // ambil hari kerja employee
        $workingDays = $employee ? $this->getActiveWorkDays($employee) : [];
        // ambil holiday
        $holidaysQuery = AttendanceCalendar::whereBetween('date', [
                $start->toDateString(),
                $end->toDateString()
            ])
            ->where('is_active', true)
            ->whereIn('type', ['national', 'company', 'cultural']);
        // filter berdasarkan area
        if ($employee && $employee->area_id == 1) {
            $holidaysQuery->where('is_hq', 1);
        } else {
            $holidaysQuery->where('is_hq', 0);
        }
        $holidays = $holidaysQuery->pluck('name', 'date')
            ->mapWithKeys(fn($name, $date) => [
                Carbon::parse($date)->toDateString() => $name
            ])
            ->toArray();
        // loop tanggal
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $currentDate = $date->toDateString();
            $dayName = strtolower($date->format('l'));
            // skip holiday
            if (array_key_exists($currentDate, $holidays)) {
                $excluded[] = [
                    'date' => $currentDate,
                    'type' => $holidays[$currentDate]
                ];
                continue;
            }
            // pakai workhour aktif
            if (!empty($workingDays)) {
                if (in_array($dayName, $workingDays, true)) {
                    $days++;
                } else {
                    $excluded[] = [
                        'date' => $currentDate,
                        'type' => 'Non-working day'
                    ];
                }
                continue;
            }
            // fallback weekend
            if ($date->isWeekend()) {
                $excluded[] = [
                    'date' => $currentDate,
                    'type' => 'Weekend'
                ];
                continue;
            }
            $days++;
        }
        return [
            'days' => $days,
            'excluded' => $excluded
        ];
    }
    public function calculateLeaveDays(Request $request)
    {
        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        $days = 0;
        $excluded = [];

        $employee = Auth::user()->employee;
        $workingDays = $employee ? $this->getActiveWorkDays($employee) : [];

        $holidaysQuery = AttendanceCalendar::whereBetween('date', [
                $start->toDateString(),
                $end->toDateString()
            ])
            ->where('is_active', true)
            ->whereIn('type', ['national', 'company', 'cultural']);

        // Filter berdasarkan is_hq dan area user
        if ($employee && $employee->area_id == 1) {
            $holidaysQuery->where('is_hq', 1);
        } else {
            $holidaysQuery->where('is_hq', 0);
        }

        $holidays = $holidaysQuery->pluck('name', 'date')
            ->mapWithKeys(fn($name, $date) => [Carbon::parse($date)->toDateString() => $name])
            ->toArray();

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $currentDate = $date->toDateString();
            $dayName = strtolower($date->format('l'));

            if (array_key_exists($currentDate, $holidays)) {
                $excluded[] = [
                    'date' => $currentDate,
                    'type' => $holidays[$currentDate]
                ];
                continue;
            }

            if (! empty($workingDays)) {
                if (in_array($dayName, $workingDays, true)) {
                    $days++;
                    continue;
                }

                $excluded[] = [
                    'date' => $currentDate,
                    'type' => 'Non-working day'
                ];
                continue;
            }

            if ($date->isWeekend()) {
                $excluded[] = [
                    'date' => $currentDate,
                    'type' => 'Weekend'
                ];
                continue;
            }

            $days++;
        }

        return response()->json([
            'days' => $days,
            'excluded' => $excluded
        ]);
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
        ->where('is_hq', $employee && $employee->area_id == 1 ? 1 : 0)
        ->get()
        ->keyBy(fn($item) => Carbon::parse($item->date)->toDateString());

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
    private function getActiveWorkDays(Employee $employee): array
    {
        $groupEmployee = $employee->groupEmployees->first(); // 🔥 TANPA QUERY
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
    public function pendingCount()
    {
        $employee = auth()->user()->employee;

        $totalApproval = LeaveRequestApproval::where('approver_id', $employee->id)
            ->where('status', 'waiting')
            ->count();

        return response()->json([
            'total' => $totalApproval
        ]);
    }
    // APPROVAL FLOW CONTROLLER
    public function singleProcessApproval(Request $request)
    {
        // dd($request->reason);
        $request->validate([
            'id'     => 'required|exists:leave_approvals,id',
            'action' => 'required|in:approved,rejected',
            'reason' => 'nullable|string|required_if:action,rejected',
        ]);

        return $this->handleApproval([$request->id], $request->action);
    }
    public function bulkProcessApproval(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array',
            'ids.*'  => 'exists:leave_approvals,id',
            'action' => 'required|in:approved,rejected',
            // 'reason' => 'nullable|string|required_if:action,rejected',
        ]);

        return $this->handleApproval($request->ids, $request->action);
    }
    private function handleApproval(array $ids, string $action)
    {
        DB::beginTransaction();

        try {
            $approvals = LeaveRequestApproval::with([
                'leaveRequest',
                'approver.user'
            ])->whereIn('id', $ids)->get();

            if ($approvals->isEmpty()) {
                throw new \Exception('Data approval tidak ditemukan');
            }

            $nextApproverEmails = [];

            foreach ($approvals as $approval) {
                if ($approval->status !== 'waiting') {
                    continue;
                }
                // dd($reason);
                $approval->update([
                    'status'      => $action,
                    'approved_at' => now(),
                    'reason_reject' => request('reason')
                ]);

                $leaveRequest = $approval->leaveRequest;

                if ($action === 'rejected') {
                    $leaveRequest->update([
                        'status'     => 'rejected',
                        'updated_by' => auth()->user()->name,

                    ]);
                    continue;
                }
                // cek approval berikutnya
                $nextApproval = LeaveRequestApproval::with('approver.user')
                    ->where('leave_request_id', $leaveRequest->id)
                    ->where('level', '>', $approval->level)
                    ->orderBy('level')
                    ->first();

                if ($nextApproval) {
                    $nextApproval->update([
                        'status' => 'waiting'
                    ]);

                    if ($nextApproval->approver?->user?->email) {
                        $email = $nextApproval->approver->user->email;

                        // INIT (WAJIB)
                        if (!isset($nextApproverEmails[$email])) {
                            $nextApproverEmails[$email] = [
                                'approver_name' => $nextApproval->approver->fullname,
                                'requests'      => []
                            ];
                        }
                        // PUSH DATA
                        $nextApproverEmails[$email]['requests'][] = [
                            'text' => "{$leaveRequest->employee_name} | Cuti " .
                                    ucfirst($leaveRequest->type) . " | " .
                                    Carbon::parse($leaveRequest->start_date)->format('d M Y') . " s/d " .
                                    Carbon::parse($leaveRequest->end_date)->format('d M Y') .
                                    " ({$leaveRequest->total_days} hari)",

                            'token' => $nextApproval->approval_token,
                        ];
                    }
                } else {
                    $leaveRequest->update([
                        'status'     => 'approved',
                        'updated_by' => auth()->user()->name
                    ]);

                    if ($leaveRequest->type === 'pribadi') {
                        $leaveBalance = LeaveBalance::where('employee_id', $leaveRequest->employee_id)
                            ->where('leave_type_id', $leaveRequest->leave_type_id)
                            ->whereDate('valid_to', '>=', now())
                            ->orderBy('valid_to')
                            ->first();

                        if ($leaveBalance) {
                            $leaveBalance->decrement('remaining_days', $leaveRequest->total_days);
                        }
                    }

                    $employee = Employee::with([
                        'area',
                        'department',
                        'position',
                        'groupEmployees.groupEmployeeWorkhour.groupWorkHours.workhour.details'
                    ])->find($leaveRequest->employee_id);

                    if ($employee) {
                        $this->generateAttendance(
                            $employee,
                            $leaveRequest->start_date,
                            $leaveRequest->end_date,
                            $leaveRequest->type
                        );
                    }
                }
            }
            // kirim email 1x per approver
            foreach ($nextApproverEmails as $email => $data) {
                $payload = [
                    'subject'    => 'Permintaan Cuti Menunggu Approval',
                    'greeting'   => 'Hi ' . $data['approver_name'],
                    'requests'   => $data['requests'],
                    'actionText' => 'Lihat Pengajuan',
                    'actionURL' => route('leave-request.approval', [
                        'token' => $data['requests'][0]['token']
                    ]) . '#pill-approval',
                    'thanks'     => 'Terimakasih',
                ];

                Notification::route('mail', $email)
                    ->notify(new BulkLeaveApprovalNotification($payload));
            }

            DB::commit();

            return response()->json([
                'message' => ucfirst($action) . ' berhasil diproses'
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
        $endDate   = $end ? Carbon::parse($end) : $startDate;
        // $formattedType = $this->formatType($type);
        // preload working days
        $workingDays = $this->getActiveWorkDays($emp);
        // preload holidays
        $holidays = AttendanceCalendar::whereBetween('date', [
                $startDate->copy()->toDateString(),
                $endDate->copy()->toDateString()
            ])
            ->where('is_active', true)
            ->pluck('name', 'date')
            ->mapWithKeys(fn($name, $date) => [
                Carbon::parse($date)->toDateString() => $name
            ])
            ->toArray();
        while ($startDate->lte($endDate)) {
            $date    = $startDate->toDateString();
            $dayName = strtolower($startDate->format('l'));

                if (
                    array_key_exists($date, $holidays) ||
                    !in_array($dayName, $workingDays)
                ) {
                    $startDate->addDay();
                    continue;
                }

            $workhourData = $this->getWorkHourByDate($emp, $date);
            if (empty($workhourData)) {
                $startDate->addDay();
                continue;
            }
            // dd($workhourData);
            $existing = DB::table('employee_attendances')
                ->where('employee_id', $emp->id)
                ->where('date', $date)
                ->first();
            // jangan overwrite attendance present
            if (!$existing || $existing->attendance_status !== 'present') {
                DB::table('employee_attendances')->updateOrInsert(
                    [
                        'employee_id' => $emp->id,
                        'date'        => $date
                    ],
                    [
                        'area_name'         => $emp->area->name ?? '-',
                        'department_name'   => $emp->department->name ?? '-',
                        'position_name'     => $emp->position->nama ?? '-',

                        'group_id'          => $workhourData['group_id'] ?? null,
                        'master_workhour_id'=> $workhourData['master_workhour_id'] ?? null,
                        'work_in'           => $workhourData['work_in'] ?? null,
                        'work_out'          => $workhourData['work_out'] ?? null,

                        'attendance_status' => 'Leave',
                        'source'            => "Cuti - $type",

                        'created_by'        => auth()->user()->employee->fullname ?? 'System',
                        'updated_by'        => auth()->user()->employee->fullname ?? 'System',
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]
                );
            }

            $startDate->addDay();
        }
    }
    private function getWorkHourByDate($employee, $date)
    {
        $groupEmployee = $employee->groupEmployees->first();

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
}
