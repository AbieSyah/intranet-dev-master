<?php

namespace App\Http\Controllers\Attendance\BusinessTrip;

use App\Http\Controllers\Controller;
use App\Models\Attendance\AttendanceCalendar;
use App\Models\Attendance\BusinessTrip\BusinessCancellation;
use App\Models\Attendance\BusinessTrip\BusinessCancellationApproval;
use App\Models\Attendance\BusinessTrip\BusinessCancellationLog;
use App\Models\Attendance\BusinessTrip\BusinessReport;
use App\Models\Attendance\BusinessTrip\BusinessReportApproval;
use App\Models\Attendance\BusinessTrip\BusinessReportLog;
use App\Models\Attendance\BusinessTrip\BusinessTrip;
use App\Models\Attendance\BusinessTrip\BusinessTripApproval;
use App\Models\Attendance\BusinessTrip\BusinessTripCancellationApproval;
use App\Models\Attendance\BusinessTrip\BusinessTripLog;
use App\Models\Employee;
use App\Models\Master\LineApproval;
use App\Notifications\BulkLeaveApprovalNotification;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Log;
use Yajra\DataTables\DataTables;

class IndexController extends Controller
{
    public function waitingPendingCount()
    {
        $employee = auth()->user()->employee;
        // APPROVAL BUSINESS TRIP
        $businessTrip = BusinessTripApproval::where(
                'approver_id',
                $employee->id
            )
            ->where('status', 'waiting')
            ->count();
        // CLAIM / REPORT
        $claim = BusinessReportApproval::where(
                'approver_id',
                $employee->id
            )
            ->where('status', 'waiting')
            ->count();
        // CANCELLATION
        $cancellation = BusinessCancellationApproval::where(
                'approver_id',
                $employee->id
            )
            ->where('status', 'waiting')
            ->count();
            // dd($businessTrip, $claim);
        return response()->json([
            'business_trip' => $businessTrip,
            'claim' => $claim,
            'cancellation' => $cancellation,
            'total' => (
                $businessTrip +
                $claim
                + $cancellation
            )
        ]);
    }
    public function index(){
        $user = auth()->user();
        $employeeId = $user->employee->id;
        $employee = $user->employee;
        $businessTrip = BusinessTrip::get();
        // 🔥 cek apakah dia approver
        $isApprover = LineApproval::whereIn('approval_type', ['Business Trip Domestic','Business Trip LuarNegeri','Report/Claim Business Trip'])
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
        // dd($isApprover);
        return view('pages.profile.Attendance.business-trip.index',compact('user','isApprover','businessTrip'));
    }
    public function myBusinessTripData()
    {
        $employeeId = auth()->user()->employee->id;

        $data = BusinessTrip::query()
            ->where('employee_id', $employeeId)
            ->latest()
            ->get();
            // dd($data);

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('no_document', fn($row) => $row->no_document ?? '-')
            ->addColumn('request_date', function ($row) {
                return Carbon::parse($row->created_at)
                    ->format('d M Y');
            })
            ->addColumn('tipe', function ($row) {
                if ($row->trip_type === 'domestic') {
                    return '
                        <span class="badge bg-primary">
                            Domestic
                        </span>
                    ';
                }
                return '
                    <span class="badge bg-success">
                        Overseas
                    </span>
                ';
            })
            ->addColumn('date_and_day', function ($row) {
                $start = Carbon::parse($row->start_date);
                $end = Carbon::parse($row->end_date);
                $totalDays = $start->diffInDays($end) + 1;
                return '
                    <div class="fw-semibold">
                        '.$start->format('d M Y').'
                        <br>
                        s/d
                        <br>
                        '.$end->format('d M Y').'
                    </div>

                    <small class="">
                        '.$totalDays.' Hari
                    </small>
                ';
            })
            ->addColumn('dept_and_arr_times', function ($row) {
                $departure = $row->departure_time
                    ? Carbon::parse($row->departure_time)
                        ->format('H:i')
                    : '-';

                $arrival = $row->arrival_time
                    ? Carbon::parse($row->arrival_time)
                        ->format('H:i')
                    : '-';
                return '
                    <div>
                        <small class="text-muted">
                            Berangkat :
                        </small>
                        <span class="fw-semibold">
                            '.$departure.'
                        </span>
                    </div>

                    <div class="mt-1">
                        <small class="text-muted">
                            Tiba :
                        </small>
                        <span class="fw-semibold">
                            '.$arrival.'
                        </span>
                    </div>
                ';
            })
            // ->addColumn('depart_from', fn($row) => $row->departure_from ?? '-')
            ->addColumn('depart_from', function ($row) {
                if ($row->departure_from === 'house') {
                    return '
                        <span class="fw-semibold">
                            Rumah
                        </span>
                    ';
                }
                return '
                    <span class="fw-semibold">
                        PT. HPI
                    </span>
                ';
            })
            ->addColumn('arrival_to', fn($row) => $row->arrival_to ?? '-')
            ->addColumn('needs', fn($row) => $row->purpose ?? '-')
            ->addColumn('status', fn($row) => $row->status ?? '-')
            ->addColumn('action', function ($row) {
                    $button = '';

                        $button .= '
                            <button title="Edit" data-id="'.encrypt($row->id).'"
                                class="btn btn-info btn-sm btn-detail-myData">
                                <i class="ri-eye-line"></i>
                            </button>';
                        if ($row->status === 'revised') {
                            $button .= '
                                <button title="Edit"
                                    data-id="'.encrypt($row->id).'"
                                    class="btn btn-warning btn-sm edit-btn">
                                    <i class="ri-edit-line"></i>
                                </button>';
                        }
                    return $button ?: '-';
                })
            ->rawColumns([ 'no_document', 'tipe', 'date_and_day', 'dept_and_arr_times', 'action', 'depart_from' ])
            ->make(true);
    }
    public function myBusinessTripDetail(Request $request)
    {
        $encryptedId = decrypt($request->id);

        $businessTrip = BusinessTrip::with([
            'employee',
            'costs',
            'transportations',
            'hotels',
            'approvals.approver',
            'approvals.logs'
        ])->findOrFail($encryptedId);

        // approval milik user login
        $approval = $businessTrip->approvals
            ->where('approver_id', auth()->user()->employee->id)
            ->first();
        // dd($approval);
        return response()->json([
            ...$businessTrip->toArray(),

            // kirim approval id
            'approval_id' => $approval?->id,
            'can_action' => in_array(
                $businessTrip->status,
                ['approved', 'ongoing', 'draft']
            ),
        ]);
    }
    public function ApprovalData()
    {
        $employeeId = auth()->user()->employee->id;
        $data = BusinessTripApproval::with(['businessTrip.employee','approver'])
            ->where('approver_id', $employeeId)
            ->whereIn('status', ['waiting','revised'])
            ->latest()
            ->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('nik', fn($row) => $row->businessTrip->employee->nik ?? '-')
            ->addColumn('employee_name', fn($row) => $row->businessTrip->employee->fullname ?? '-')
            ->addColumn('area', fn($row) => $row->approver->area->name ?? '-')
            ->addColumn('department', fn($row) => $row->businessTrip->department ?? '-')
            ->addColumn('position', fn($row) => $row->businessTrip->position ?? '-')
            ->addColumn('no_document', fn($row) => $row->businessTrip->no_document ?? '-')
            ->addColumn('request_date', function ($row) {
                return Carbon::parse($row->businessTrip->propose_date)
                    ->format('d M Y');
            })
            ->addColumn('tipe', function ($row) {
                if ($row->businessTrip->trip_type === 'domestic') {
                    return '
                        <span class="badge bg-primary">
                            Domestic
                        </span>
                    ';
                }
                return '
                    <span class="badge bg-success">
                        Overseas
                    </span>
                ';
            })
            ->addColumn('date_and_day', function ($row) {
                $start = Carbon::parse($row->businessTrip->start_date);
                $end = Carbon::parse($row->businessTrip->end_date);
                $totalDays = $start->diffInDays($end) + 1;
                return '
                    <div class="fw-semibold">
                        '.$start->format('d M Y').'
                        <br>
                        s/d
                        <br>
                        '.$end->format('d M Y').'
                    </div>

                    <small class="">
                        '.$totalDays.' Hari
                    </small>
                ';
            })
            ->addColumn('dept_and_arr_times', function ($row) {
                $departure = $row->businessTrip->departure_time
                    ? Carbon::parse($row->businessTrip->departure_time)
                        ->format('H:i')
                    : '-';

                $arrival = $row->businessTrip->arrival_time
                    ? Carbon::parse($row->businessTrip->arrival_time)
                        ->format('H:i')
                    : '-';
                return '
                    <div>
                        <small class="text-muted">
                            Berangkat :
                        </small>
                        <span class="fw-semibold">
                            '.$departure.'
                        </span>
                    </div>

                    <div class="mt-1">
                        <small class="text-muted">
                            Tiba :
                        </small>
                        <span class="fw-semibold">
                            '.$arrival.'
                        </span>
                    </div>
                ';
            })
            // ->addColumn('depart_from', fn($row) => $row->departure_from ?? '-')
            ->addColumn('depart_from', function ($row) {
                if ($row->businessTrip->departure_from === 'house') {
                    return '
                        <span class="fw-semibold">
                            Rumah
                        </span>
                    ';
                }
                return '
                    <span class="fw-semibold">
                        PT. HPI
                    </span>
                ';
            })
            ->addColumn('arrival_to', fn($row) => $row->businessTrip->arrival_to ?? '-')
            ->addColumn('needs', fn($row) => $row->businessTrip->purpose ?? '-')
            ->addColumn('status', fn($row) => $row->status ?? '-')
            ->addColumn('action', function ($row) {
                return '
                    <div class="d-flex justify-content-center gap-2">
                            <button
                                class="btn btn-info btn-sm btn-detail-approval"
                                data-id="' . encrypt($row->id) . '"
                            >
                                <i class="ri-eye-line"></i>
                            </button>
                        </div>
                    ';
            })
            ->rawColumns([ 'no_document', 'tipe', 'date_and_day', 'dept_and_arr_times', 'action', 'depart_from' ])
            ->make(true);
    }
    public function ApprovalHistory()
    {
        $employeeId = auth()->user()->employee->id;
        $data = BusinessTripApproval::with('businessTrip.employee')
            ->where('approver_id', $employeeId)
            ->whereIn('status', ['rejected','approved'])
            ->latest()
            ->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('nik', fn($row) => $row->businessTrip->employee->nik ?? '-')
            ->addColumn('employee_name', fn($row) => $row->businessTrip->employee->fullname ?? '-')
            ->addColumn('area', fn($row) => $row->businessTrip->area ?? '-')
            ->addColumn('department', fn($row) => $row->businessTrip->department ?? '-')
            ->addColumn('position', fn($row) => $row->businessTrip->position ?? '-')
            ->addColumn('no_document', fn($row) => $row->businessTrip->no_document ?? '-')
            ->addColumn('request_date', function ($row) {
                return Carbon::parse($row->businessTrip->propose_date)
                    ->format('d M Y');
            })
            ->addColumn('tipe', function ($row) {
                if ($row->businessTrip->trip_type === 'domestic') {
                    return '
                        <span class="badge bg-primary">
                            Domestic
                        </span>
                    ';
                }
                return '
                    <span class="badge bg-success">
                        Overseas
                    </span>
                ';
            })
            ->addColumn('date_and_day', function ($row) {
                $start = Carbon::parse($row->businessTrip->start_date);
                $end = Carbon::parse($row->businessTrip->end_date);
                $totalDays = $start->diffInDays($end) + 1;
                return '
                    <div class="fw-semibold">
                        '.$start->format('d M Y').'
                        <br>
                        s/d
                        <br>
                        '.$end->format('d M Y').'
                    </div>

                    <small class="">
                        '.$totalDays.' Hari
                    </small>
                ';
            })
            ->addColumn('dept_and_arr_times', function ($row) {
                $departure = $row->businessTrip->departure_time
                    ? Carbon::parse($row->businessTrip->departure_time)
                        ->format('H:i')
                    : '-';

                $arrival = $row->businessTrip->arrival_time
                    ? Carbon::parse($row->businessTrip->arrival_time)
                        ->format('H:i')
                    : '-';
                return '
                    <div>
                        <small class="text-muted">
                            Berangkat :
                        </small>
                        <span class="fw-semibold">
                            '.$departure.'
                        </span>
                    </div>

                    <div class="mt-1">
                        <small class="text-muted">
                            Tiba :
                        </small>
                        <span class="fw-semibold">
                            '.$arrival.'
                        </span>
                    </div>
                ';
            })
            // ->addColumn('depart_from', fn($row) => $row->departure_from ?? '-')
            ->addColumn('depart_from', function ($row) {
                if ($row->businessTrip->departure_from === 'house') {
                    return '
                        <span class="fw-semibold">
                            Rumah
                        </span>
                    ';
                }
                return '
                    <span class="fw-semibold">
                        PT. HPI
                    </span>
                ';
            })
            ->addColumn('arrival_to', fn($row) => $row->businessTrip->arrival_to ?? '-')
            ->addColumn('needs', fn($row) => $row->businessTrip->purpose ?? '-')
            ->addColumn('status', fn($row) => $row->status ?? '-')
            ->addColumn('action', function ($row) {
                return '
                    <div class="d-flex justify-content-center gap-2">
                            <button
                                class="btn btn-info btn-sm btn-detail-approval-history"
                                data-id="' . encrypt($row->id) . '"
                            >
                                <i class="ri-eye-line"></i>
                            </button>
                        </div>
                    ';
            })
            ->rawColumns([ 'no_document', 'tipe', 'date_and_day', 'dept_and_arr_times', 'action', 'depart_from' ])
            ->make(true);
    }
    public function approvalDetail(Request $request)
    {
        $approvalId = decrypt($request->id);

        $approval = BusinessTripApproval::with([

            // employee
            'businessTrip.employee.position',
            'businessTrip.employee.level',

            // detail
            'businessTrip.costs',
            'businessTrip.transportations',
            'businessTrip.hotels',

            // approvals
            'businessTrip.approvals.approver',

            // logs per approval
            'businessTrip.approvals.logs',

        ])->findOrFail($approvalId);

        $businessTrip = $approval->businessTrip;

        return response()->json([

            // current approval
            'approval_id'      => $approval->id,
            'approval_status'  => $approval->status,
            'approval_level'   => $approval->level,

            'can_action' =>
                $approval->status === 'waiting'
                && in_array(
                    $businessTrip->status,
                    ['draft', 'waiting']
                ),

            // business trip data
            ...$businessTrip->toArray(),

        ]);
    }
    public function singleProcessApproval(Request $request)
    {
        // dd($request->reason);
        $request->validate([
            'id'     => 'required|exists:business_trip_approvals,id',
            'action' => 'required|in:approved,revised,rejected',
            'reason' => 'nullable|string|required_if:action,rejected',
        ]);
        return $this->handleApproval([$request->id], $request->action, $request->reason);
    }
    public function bulkProcessApproval(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array',
            'ids.*'  => 'exists:business_trip_approvals,id',
            'action' => 'required|in:approved,revised,rejected',
            'reason' => 'nullable|string|required_if:action,rejected',
        ]);

        return $this->handleApproval($request->ids, $request->action, $request->reason);
    }
    private function handleApproval(array $ids,string $action,?string $reason = null)
    {
        DB::beginTransaction();

        try {

            $approvals = BusinessTripApproval::with([
                'businessTrip.employee.user',
                'approver.user'
            ])
            ->whereIn('id', $ids)
            ->get();
            if ($approvals->isEmpty()) {
                throw new \Exception(
                    'Data approval tidak ditemukan'
                );
            }
            $nextApproverEmails = [];
            foreach ($approvals as $approval) {

                // hanya approval waiting yang bisa diproses
                if ($approval->status !== 'waiting') {
                    continue;
                }

                $businessTrip = $approval->businessTrip;
                $employee = auth()->user()->employee;

                // ================= REJECTED =================
                if ($action === 'rejected') {
                    $businessTrip->update([
                        'status'     => 'rejected',
                        'updated_by' => $employee->fullname,
                    ]);

                    $approval->update([
                        'status'      => 'rejected',
                        'approved_at' => now(),
                    ]);

                    $this->logApprovalAction($businessTrip->id, $approval->id, 'rejected', $reason);
                }
                // ================= REVISED =================
                elseif ($action === 'revised') {
                    $businessTrip->update([
                        'status'        => 'revised',
                        'updated_by'    => $employee->fullname,
                        'revised_level' => $approval->level,
                        'revised_count' => ($businessTrip->revised_count ?? 0) + 1,
                    ]);

                    $approval->update([
                        'status' => 'revised',
                    ]);

                    $this->logApprovalAction($businessTrip->id, $approval->id, 'revised', $reason);
                }
                // ================= APPROVED =================
                else { // approved
                    $approval->update([
                        'status'      => 'approved',
                        'approved_at' => now(),
                    ]);

                    $this->logApprovalAction($businessTrip->id, $approval->id, 'approved', $reason);

                    // Cek apakah ada approval selanjutnya
                    $nextApproval = BusinessTripApproval::where('business_trip_id', $businessTrip->id)
                        ->where('level', '>', $approval->level)
                        ->orderBy('level')
                        ->first();

                    if ($nextApproval) {
                        // Aktifkan approval berikutnya
                        $nextApproval->update(['status' => 'waiting']);
                        // Optional: kirim notifikasi ke next approver
                    } else {
                        // Tidak ada approval lagi, selesai
                        $businessTrip->update([
                            'status'      => 'approved',
                            'approved_at' => now(),
                            'updated_by'  => $employee->fullname,
                        ]);

                        // Generate attendance record
                        $this->generateAttendance(
                            $businessTrip->employee,
                            $businessTrip->start_date,
                            $businessTrip->end_date,
                            $businessTrip->trip_type,
                            $businessTrip->id
                        );
                    }
                }
                    // ================= NEXT APPROVAL =================
                    $nextApproval = BusinessTripApproval::with([
                        'approver.user'
                    ])
                    ->where('business_trip_id', $businessTrip->id)
                    ->where('level', '>', $approval->level)
                    ->orderBy('level')
                    ->first();

                    // ================= MASIH ADA APPROVAL =================
                    if ($nextApproval) {

                        if ($nextApproval->status !== 'waiting') {

                            $nextApproval->update([
                                'status' => 'waiting'
                            ]);
                        }

                        $approver = $nextApproval->approver;
                        if ($approver?->user?->email) {
                            $email = $approver->user->email;
                            if (!isset($nextApproverEmails[$email])) {
                                $nextApproverEmails[$email] = [
                                    'approver_name' => $approver->fullname,
                                    'requests'      => []
                                ];
                            }
                            $nextApproverEmails[$email]['requests'][] = [
                                'text' =>
                                    $businessTrip->employee->fullname .
                                    ' | ' .
                                    ucfirst($businessTrip->trip_type) .
                                    ' | ' .
                                    Carbon::parse(
                                        $businessTrip->start_date
                                    )->format('d M Y')
                                    . ' - ' .
                                    Carbon::parse(
                                        $businessTrip->end_date
                                    )->format('d M Y')
                                    . ' | ' .
                                    $businessTrip->departure_from .
                                    ' → ' .
                                    $businessTrip->arrival_to,

                                'token' => $nextApproval->approval_token,
                            ];
                        }

                    } else {
                        // ================= FINAL APPROVED =================
                        $businessTrip->update([
                            'status'      => 'approved',
                            'approved_at' => now(),
                            'updated_by'  => auth()->user()->name,
                        ]);

                        $employee = Employee::with([
                            'area',
                            'department',
                            'position',
                            'groupEmployees.groupEmployeeWorkhour.groupWorkHours.workhour.details'
                        ])->find($businessTrip->employee_id);

                        if ($employee) {
                            $this->generateAttendance(
                                $employee,
                                $businessTrip->start_date,
                                $businessTrip->end_date,
                                $businessTrip->trip_type,
                                $businessTrip->id
                            );
                        }
                    }
                }

            // ================= SEND EMAIL =================
            foreach ($nextApproverEmails as $email => $data) {

                $payload = [
                'subject'    => 'Pengajuan Perjalanan Dinas Menunggu Approval',
                    'greeting'   => 'Hi ' . $data['approver_name'],
                    'requests'   => $data['requests'],
                    'actionText' => 'Lihat Pengajuan',
                    'actionURL'  => route('business-trip.approval', [
                        'token' => $data['requests'][0]['token'] ?? null// bisa pakai 1 token saja
                    ]). '#pill-approval',
                    'thanks'     => 'Terimakasih',
                ];
                Notification::route('mail', $email)
                    ->notify(
                        new BulkLeaveApprovalNotification(
                            $payload
                        )
                    );
            }
            $user = Auth::user();
            \App\Models\Log::create([
                'user_id' => $user->id,
                'ip_address' => request()->ip(),
                'action' => 'insert',
                'description' => "{$user->employee->fullname} Melakukan {$action}  {$businessTrip->trip_type}/{$businessTrip->start_date}-{$businessTrip->end_date}"
            ]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => ucfirst($action) . ' berhasil diproses'
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
     private function logApprovalAction(
        $businessTripId,
        $approvalId,
        $action,
        $reason = null
    ){
        BusinessTripLog::create([
            'business_trip_id' => $businessTripId,
            'approval_id'      => $approvalId,
            'action'           => $action,
            'reason'           => $reason,
            'action_by'        => auth()->user()->employee->fullname,
            'created_at'       => now(),
        ]);
    }
    private function generateAttendance($emp, $start, $end, $type, $businessTripId = null)
    {
        $startDate = Carbon::parse($start);
        $endDate   = $end ? Carbon::parse($end) : $startDate;
        // $formattedType = $this->formatType($type);
        // preload working days
        $workingDays = $this->getActiveWorkDays($emp);
        // preload holidays
        // $holidays = AttendanceCalendar::whereBetween('date', [
        //         $startDate->copy()->toDateString(),
        //         $endDate->copy()->toDateString()
        //     ])
        //     ->where('is_active', true)
        //     ->pluck('name', 'date')
        //     ->mapWithKeys(fn($name, $date) => [
        //         Carbon::parse($date)->toDateString() => $name
        //     ])
        //     ->toArray();
        while ($startDate->lte($endDate)) {
            $date    = $startDate->toDateString();
            $dayName = strtolower($startDate->format('l'));

                // if (
                //     array_key_exists($date, $holidays) ||
                //     !in_array($dayName, $workingDays)
                // ) {
                //     $startDate->addDay();
                //     continue;
                // }

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

                        'business_trip_id' => $businessTripId,

                        'attendance_status' => 'Business trip',
                        'source'            => "Business Trip - $type",

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






    // =================================================================== REPORT / CLAIM ===================================================================
    public function myReportClaimData()
    {
        $employeeId = auth()->user()->employee->id;
        $data = BusinessReport::with('businessTrip')
            ->where('employee_id', $employeeId)
            ->latest()
            ->get();
        // dd($data);
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('no_document', fn($row) => $row->businessTrip->no_document ?? '-')
            ->addColumn('propose_date', function ($row) {
                return Carbon::parse($row->propose_date)
                    ->format('d M Y');
            })
            ->addColumn('type', function ($row) {
                if ($row->trip_type === 'domestic') {
                    return '
                        <span class="badge bg-primary">
                            Domestic
                        </span>
                    ';
                }
                return '
                    <span class="badge bg-success">
                        Overseas
                    </span>
                ';
            })
            ->addColumn('date_and_day', function ($row) {
                $start = Carbon::parse($row->start_date);
                $end = Carbon::parse($row->end_date);
                $totalDays = $start->diffInDays($end) + 1;
                return '
                    <div class="fw-semibold">
                        '.$start->format('d M Y').'
                        <br>
                        s/d
                        <br>
                        '.$end->format('d M Y').'
                    </div>

                    <small class="">
                        '.$totalDays.' Hari
                    </small>
                ';
            })
            ->addColumn('arrival_to', fn($row) => $row->arrival_to ?? '-')
            ->addColumn('needs', fn($row) => $row->purpose ?? '-')
            ->addColumn('status', fn($row) => $row->status ?? '-')
            ->addColumn('action', function ($row) {
                    $button = '';

                        $button .= '
                            <button title="Edit" data-id="'.encrypt($row->id).'"
                                class="btn btn-info btn-sm btn-detail-myReportClaim">
                                <i class="ri-eye-line"></i>
                            </button>';
                        if ($row->status === 'revised') {
                            $button .= '
                                <button title="Edit"
                                    data-id="'.encrypt($row->id).'"
                                    class="btn btn-warning btn-sm editReport-btn">
                                    <i class="ri-edit-line"></i>
                                </button>';
                        }
                    return $button ?: '-';
                })
            ->rawColumns([ 'no_document', 'type', 'date_and_day', 'action' ])
            ->make(true);
    }
    public function myReportClaimDetail(Request $request)
    {
        $id = decrypt($request->id);
        $businessReport = BusinessReport::with([
                'employee',
                'businessTrip',
                'reportItems.attachments',
                'approvals.approver',
                'approvals.logs'
            ])
            ->findOrFail($id);
        $approval = $businessReport->approvals->where('approver_id',auth()->user()->employee->id)->first();
        $meals = $businessReport->reportItems->where('category','meal')->values();
        $expenses = $businessReport->reportItems->where('category','!=','meal')->values();
        return response()->json([
            'id'            => $businessReport->id,
            'approval_id'   => $approval?->id,
            'can_action'    => $approval?->status === 'waiting',
            'no_document'   => $businessReport->businessTrip?->no_document,
            'employee_name' => $businessReport->employee?->fullname,
            'trip_type'     => $businessReport->trip_type,
            'start_date'    => $businessReport->start_date,
            'end_date'      => $businessReport->end_date,
            'total_days'    => $businessReport->total_days,
            'arrival_to'    => $businessReport->arrival_to,
            'purpose'       => $businessReport->purpose,
            'total_cost'    => $businessReport->total_cost,
            'status'        => $businessReport->status,
            'meals'         => $meals,
            'expenses'      => $expenses,
            'approvals'     => $businessReport->approvals
        ]);
    }
    public function ReportClaimApproval()
    {
        $employeeId = auth()->user()->employee->id;
        $data = BusinessReportApproval::with([
            'businessReport.businessTrip.employee.area'
        ])
            ->where('approver_id', $employeeId)
            ->whereIn('status', ['waiting','revised'])
            ->latest()
            ->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('nik', fn($row) => $row->businessReport->employee->nik ?? '-')
            ->addColumn('employee_name', fn($row) => $row->businessReport->employee->fullname ?? '-')
            ->addColumn('area', fn($row) => $row->businessReport->employee->area->name ?? '-')
            ->addColumn('department', fn($row) => $row->businessReport->department ?? '-')
            ->addColumn('position', fn($row) => $row->businessReport->position ?? '-')
            ->addColumn('no_document', fn($row) => $row->businessReport->businessTrip->no_document ?? '-')
            ->addColumn('request_date', function ($row) {
                return Carbon::parse($row->businessReport->propose_date)
                    ->format('d M Y');
            })
            ->addColumn('type', function ($row) {
                if ($row->businessReport->trip_type === 'domestic') {
                    return '
                        <span class="badge bg-primary">
                            Domestic
                        </span>
                    ';
                }
                return '
                    <span class="badge bg-success">
                        Overseas
                    </span>
                ';
            })
            ->addColumn('date_and_day', function ($row) {
                $start = Carbon::parse($row->businessReport->start_date);
                $end = Carbon::parse($row->businessReport->end_date);
                $totalDays = $start->diffInDays($end) + 1;
                return '
                    <div class="fw-semibold">
                        '.$start->format('d M Y').'
                        <br>
                        s/d
                        <br>
                        '.$end->format('d M Y').'
                    </div>

                    <small class="">
                        '.$totalDays.' Hari
                    </small>
                ';
            })
            ->addColumn('arrival_to', fn($row) => $row->businessReport->arrival_to ?? '-')
            ->addColumn('needs', fn($row) => $row->businessReport->purpose ?? '-')
            ->addColumn('status', fn($row) => $row->status ?? '-')
            ->addColumn('action', function ($row) {
                return '
                    <div class="d-flex justify-content-center gap-2">
                            <button
                                class="btn btn-info btn-sm btn-detail-reportClaim"
                                data-id="' . encrypt($row->id) . '"
                            >
                                <i class="ri-eye-line"></i>
                            </button>
                        </div>
                    ';
            })
            ->rawColumns([ 'no_document', 'type', 'date_and_day', 'dept_and_arr_times', 'action', 'depart_from' ])
            ->make(true);
    }
    public function reportClaimHistory()
    {
        $employeeId = auth()->user()->employee->id;
        $data = BusinessReportApproval::with([
            'businessReport.employee.area',
            // 'businessReport.employee.area',
            // 'businessReport.businessTrip',
            // 'businessReport.businessTrip.employee.area',
        ])
            ->where('approver_id', $employeeId)
            ->whereIn('status', ['approved','rejected'])
            ->latest()
            ->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('nik', fn($row) => $row->businessReport->employee->nik ?? '-')
            ->addColumn('employee_name', fn($row) => $row->businessReport->employee->fullname ?? '-')
            ->addColumn('area', fn($row) => $row->businessReport->employee->area->name ?? '-')
            ->addColumn('department', fn($row) => $row->businessReport->department ?? '-')
            ->addColumn('position', fn($row) => $row->businessReport->position ?? '-')
            ->addColumn('no_document', fn($row) => $row->businessReport->businessTrip->no_document ?? '-')
            ->addColumn('request_date', function ($row) {
                return Carbon::parse($row->businessReport->propose_date)
                    ->format('d M Y');
            })
            ->addColumn('type', function ($row) {
                if ($row->businessReport->trip_type === 'domestic') {
                    return '
                        <span class="badge bg-primary">
                            Domestic
                        </span>
                    ';
                }
                return '
                    <span class="badge bg-success">
                        Overseas
                    </span>
                ';
            })
            ->addColumn('date_and_day', function ($row) {
                $start = Carbon::parse($row->businessReport->start_date);
                $end = Carbon::parse($row->businessReport->end_date);
                $totalDays = $start->diffInDays($end) + 1;
                return '
                    <div class="fw-semibold">
                        '.$start->format('d M Y').'
                        <br>
                        s/d
                        <br>
                        '.$end->format('d M Y').'
                    </div>

                    <small class="">
                        '.$totalDays.' Hari
                    </small>
                ';
            })
            ->addColumn('arrival_to', fn($row) => $row->businessReport->arrival_to ?? '-')
            ->addColumn('needs', fn($row) => $row->businessReport->purpose ?? '-')
            ->addColumn('status', fn($row) => $row->status ?? '-')
            ->addColumn('action', function ($row) {
                return '
                    <div class="d-flex justify-content-center gap-2">
                            <button
                                class="btn btn-info btn-sm btn-detail-reportClaimHistory"
                                data-id="' . encrypt($row->id) . '"
                            >
                                <i class="ri-eye-line"></i>
                            </button>
                        </div>
                    ';
            })
            ->rawColumns([ 'no_document', 'type', 'date_and_day', 'dept_and_arr_times', 'action' , 'depart_from' ])
            ->make(true);
    }
    public function reportClaimDetail(Request $request)
    {
        $approvalId = decrypt($request->id);
        $approval = BusinessReportApproval::with([
            'businessReport.employee.position',
            'businessReport.employee.level',
            'businessReport.businessTrip',
            'businessReport.reportItems.attachments',
            'businessReport.approvals.approver',
            'businessReport.approvals.logs',
        ])->findOrFail($approvalId);
        $businessReport = $approval->businessReport;
        $meals = $businessReport->reportItems->where('category','meal')->values();
        $expenses = $businessReport->reportItems->where('category','!=','meal')->values();
        return response()->json([
            // CURRENT APPROVAL
            'approval_id'       => $approval->id,
            'approval_status'   => $approval->status,
            'approval_level'    => $approval->level,
            'can_action'        => $approval->status === 'waiting',
            // REPORT DATA
            'id'                => $businessReport->id,
            'no_document'       => $businessReport->businessTrip?->no_document,
            'employee_name'     => $businessReport->employee?->fullname,
            'trip_type'         => $businessReport->trip_type,
            'start_date'        => $businessReport->start_date,
            'end_date'          => $businessReport->end_date,
            'total_days'        => $businessReport->total_days,
            'arrival_to'        => $businessReport->arrival_to,
            'purpose'           => $businessReport->purpose,
            'total_cost'        => $businessReport->total_cost,
            'status'            => $businessReport->status,
            // DETAIL
            'meals'             => $meals,

            'expenses'          => $expenses,
            'approvals'         => $businessReport->approvals
        ]);
    }
    public function singleProcessReport(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'id'     => 'required|exists:business_report_approvals,id',
            'action' => 'required|in:approved,revised,rejected',
            'reason' => 'nullable|string|required_if:action,rejected',
        ]);
        return $this->handleReport([$request->id], $request->action, $request->reason);
    }
    private function handleReport(array $ids,string $action,?string $reason = null)
    {
        DB::beginTransaction();

        try {

            $approvals = BusinessReportApproval::with([
                'businessReport.employee.user',
                'approver.user'
            ])
            ->whereIn('id', $ids)
            ->get();
            if ($approvals->isEmpty()) {
                throw new \Exception(
                    'Data approval tidak ditemukan'
                );
            }
            $nextApproverEmails = [];
            foreach ($approvals as $approval) {
                // hanya approval waiting yang bisa diproses
                if ($approval->status !== 'waiting') {
                    continue;
                }
                $businessReport = $approval->businessReport;
                // ================= REJECTED =================
                if ($action === 'rejected') {

                    $businessReport->update([
                        'status'     => 'rejected',
                        'updated_by' => auth()->user()->name,
                    ]);

                    $approval->update([
                        'status'      => 'rejected',
                        'approved_at' => now(),
                    ]);

                    if($businessReport->business_trip_id && $businessReport->businessTrip)
                    {
                        $businessReport->businessTrip->update([
                            'status' => 'ongoing'
                        ]);
                    }

                    BusinessReportLog::create([
                        'business_report_id'        => $businessReport->id,
                        'approval_path_id'          => $approval->id,
                        'status'                    => 'rejected',
                        'reason'                    => $reason,
                        'action_at'                 => now()
                    ]);
                    continue;
                }
                // ================= REVISED =================
                if ($action === 'revised') {
                    $businessReport->update([
                        'status'         => 'revised',
                        'updated_by'     => auth()->user()->name,
                        'revised_level'  => $approval->level,
                        'revise_count'   => ($businessReport->revise_count ?? 0) + 1,
                    ]);

                    // approval path kembali pending
                    $approval->update([
                        'status' => 'revised',
                    ]);

                    // simpan history revised
                    BusinessReportLog::create([
                        'business_report_id'        => $businessReport->id,
                        'approval_path_id'          => $approval->id,
                        'status'                    => 'revised',
                        'reason'                    => $reason,
                        'action_at'                 => now(),
                    ]);

                    continue;
                }
                // ================= APPROVED =================
                $approval->update([
                    'status'      => 'approved',
                    'approved_at' => now(),
                ]);
                BusinessReportLog::create([
                    'business_report_id'          => $businessReport->id,
                    'approval_path_id'          => $approval->id,
                    'status'                    => 'approved',
                    'reason'                    => $reason,
                    'action_at'                 => now(),
                ]);
                // ================= NEXT APPROVAL =================
                $nextApproval = BusinessReportApproval::with([
                    'approver.user'
                ])
                ->where('business_Report_id', $businessReport->id)
                ->where('level', '>', $approval->level)
                ->orderBy('level')
                ->first();

                // ================= MASIH ADA APPROVAL =================
                if ($nextApproval) {

                    if ($nextApproval->status !== 'waiting') {

                        $nextApproval->update([
                            'status' => 'waiting'
                        ]);
                    }

                    $approver = $nextApproval->approver;
                    if ($approver?->user?->email) {
                        $email = $approver->user->email;
                        if (!isset($nextApproverEmails[$email])) {
                            $nextApproverEmails[$email] = [
                                'approver_name' => $approver->fullname,
                                'requests'      => []
                            ];
                        }
                        $nextApproverEmails[$email]['requests'][] = [
                            'text' =>
                                $businessReport->employee->fullname .
                                ' | ' .
                                ucfirst($businessReport->trip_type) .
                                ' | ' .
                                Carbon::parse(
                                    $businessReport->start_date
                                )->format('d M Y')
                                . ' - ' .
                                Carbon::parse(
                                    $businessReport->end_date
                                )->format('d M Y')
                                . ' | ' .
                                $businessReport->arrival_to,

                            'token' => $nextApproval->approval_token,
                        ];
                    }

                } else {
                    // ================= FINAL APPROVED =================
                    $businessReport->update([
                        'status'      => 'approved',
                        'approved_at' => now(),
                        'updated_by'  => auth()->user()->name,
                    ]);
                    if($businessReport->business_trip_id && $businessReport->businessTrip)
                    {
                        $businessReport->businessTrip->update([
                            'status' => 'completed'
                        ]);
                    }
                }
            }

            // ================= SEND EMAIL =================
            foreach ($nextApproverEmails as $email => $data) {

                $payload = [
                'subject'    => 'Report Atau Pengajuan Claim Menunggu Review',
                    'greeting'   => 'Hi ' . $data['approver_name'],
                    'requests'   => $data['requests'],
                    'actionText' => 'Lihat Pengajuan',
                    'actionURL'  => route('business-trip.approval', [
                        'token' => $data['requests'][0]['token'] ?? null// bisa pakai 1 token saja
                    ]). '#pill-report-claim',
                    'thanks'     => 'Terimakasih',
                ];
                Notification::route('mail', $email)
                    ->notify(
                        new BulkLeaveApprovalNotification(
                            $payload
                        )
                    );
            }
            $user = Auth::user();
            \App\Models\Log::create([
                'user_id' => $user->id,
                'ip_address' => request()->ip(),
                'action' => 'insert',
                'description' => "{$user->employee->fullname} Melakukan {$action} {$businessReport->trip_type}/{$businessReport->start_date}-{$businessReport->end_date}"
            ]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => ucfirst($action) . ' berhasil diproses'
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }





    // ============================================================================== CANCELLATION FUNCTION ==============================================================================

    public function myCancellation()
    {
        $employeeId = auth()->user()->employee->id;
        $data = BusinessCancellation::with('businessTrip')
        ->whereHas('businessTrip', function($q) use ($employeeId){
            $q->where('employee_id', $employeeId);
        })
        ->latest()
        ->get();
        // dd($data);
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('no_document', fn($row) => $row->businessTrip->no_document ?? '-')
            ->addColumn('propose_date', function ($row) {
                return Carbon::parse($row->propose_date)
                    ->format('d M Y');
            })
            ->addColumn('type', function ($row) {
                if ($row->businessTrip->trip_type === 'domestic') {
                    return '
                        <span class="badge bg-primary">
                            Domestic
                        </span>
                    ';
                }
                return '
                    <span class="badge bg-success">
                        Overseas
                    </span>
                ';
            })
            ->addColumn('date_and_day', function ($row) {
                $start = Carbon::parse($row->businessTrip->start_date);
                $end = Carbon::parse($row->businessTrip->end_date);
                $totalDays = $start->diffInDays($end) + 1;
                return '
                    <div class="fw-semibold">
                        '.$start->format('d M Y').'
                        <br>
                        s/d
                        <br>
                        '.$end->format('d M Y').'
                    </div>

                    <small class="">
                        '.$totalDays.' Hari
                    </small>
                ';
            })
            ->addColumn('reason_cancel', function($row){
                return [
                    'reason_cancel' => $row->reason_cancel,
                    'reason_other'  => $row->reason_other
                ];

            })
            ->addColumn('total_cost_lost', fn($row) => $row->total_loss_amount ?? '-')
            ->addColumn('lost_costs_incurred', function($row){
                return [
                    'employee' =>
                        $row->employee_covered_amount,
                    'company' =>
                        $row->company_covered_amount
                ];

            })
            ->addColumn('status', fn($row) => $row->status ?? '-')
            ->addColumn('action', function ($row) {
                    $button = '';

                        $button .= '
                            <button title="Edit" data-id="'.encrypt($row->id).'"
                                class="btn btn-info btn-sm btn-detail-myCancellation">
                                <i class="ri-eye-line"></i>
                            </button>';
                        if ($row->status === 'revised') {
                            $button .= '
                                <button title="Edit"
                                    data-id="'.encrypt($row->id).'"
                                    class="btn btn-warning btn-sm editCancellation-btn">
                                    <i class="ri-edit-line"></i>
                                </button>';
                        }
                    return $button ?: '-';
                })
            ->rawColumns([ 'no_document', 'type', 'date_and_day', 'action' ])
            ->make(true);
    }
    public function myCancellationDetail(Request $request)
    {
        $id = decrypt($request->id);
        $businessCancellation = BusinessCancellation::with([
                'businessTrip',
                'approvals.approver',
                'approvals.logs',
                'approvals',
                'items'
            ])->findOrFail($id);
        $approval = $businessCancellation->approvals->where('approver_id',auth()->user()->employee->id)->first();
        return response()->json([
            'id'                    => $businessCancellation->id,
            'approval_id'           => $approval?->id,
            'can_action'            => $approval?->status === 'waiting',
            'no_document'           => $businessCancellation->businessTrip?->no_document,
            'employee_name'         => $businessCancellation->businessTrip?->employee?->fullname,
            'position'              => $businessCancellation->businessTrip?->employee?->position->nama,
            'trip_type'             => $businessCancellation->businessTrip?->trip_type,
            'start_date'            => $businessCancellation->businessTrip?->start_date,
            'end_date'              => $businessCancellation->businessTrip?->end_date,
            'total_days'            => $businessCancellation->businessTrip?->total_days,
            'arrival_to'            => $businessCancellation->businessTrip?->arrival_to,
            'total_loss_amount'     => $businessCancellation->total_loss_amount,
            'employee_amount'       => $businessCancellation->employee_covered_amount,
            'company_amount'        => $businessCancellation->company_covered_amount,
            'reason_cancel'         => $businessCancellation->reason_cancel,
            'reason_other'          => $businessCancellation->reason_other,
            'status'                => $businessCancellation->status,
            'approvals'             => $businessCancellation->approvals,
            'items'                 => $businessCancellation->items,
        ]);
    }
    public function cancellationApproval()
    {
        $employeeId = auth()->user()->employee->id;
        $data = BusinessCancellationApproval::with(['businessCancellation','businessCancellation.businessTrip'])
        ->where('approver_id', $employeeId)
        ->whereIn('status', ['waiting', 'revised'])
        ->latest()
        ->get();
        // dd($data);
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('nik', fn($row) => $row->businessCancellation->businessTrip->employee->nik ?? '-')
            ->addColumn('employee_name', fn($row) => $row->businessCancellation->businessTrip->employee->fullname ?? '-')
            ->addColumn('area', fn($row) => $row->businessCancellation->businessTrip->employee->area->name ?? '-')
            ->addColumn('department', fn($row) => $row->businessCancellation->businessTrip->department ?? '-')
            ->addColumn('position', fn($row) => $row->businessCancellation->businessTrip->position ?? '-')
            ->addColumn('no_document', fn($row) => $row->businessCancellation->businessTrip->no_document ?? '-')
            ->addColumn('propose_date', function ($row) {
                return Carbon::parse($row->businessCancellation->propose_date)
                    ->format('d M Y');
            })
            ->addColumn('type', function ($row) {
                if ($row->businessCancellation->businessTrip->trip_type === 'domestic') {
                    return '
                        <span class="badge bg-primary">
                            Domestic
                        </span>
                    ';
                }
                return '
                    <span class="badge bg-success">
                        Overseas
                    </span>
                ';
            })
            ->addColumn('date_and_day', function ($row) {
                $start = Carbon::parse($row->businessCancellation->businessTrip->start_date);
                $end = Carbon::parse($row->businessCancellation->businessTrip->end_date);
                $totalDays = $start->diffInDays($end) + 1;
                return '
                    <div class="fw-semibold">
                        '.$start->format('d M Y').'
                        <br>
                        s/d
                        <br>
                        '.$end->format('d M Y').'
                    </div>

                    <small class="">
                        '.$totalDays.' Hari
                    </small>
                ';
            })
            ->addColumn('reason_cancel', function($row){
                return [
                    'reason_cancel' => $row->businessCancellation->reason_cancel,
                    'reason_other'  => $row->businessCancellation->reason_other
                ];

            })
            ->addColumn('total_cost_lost', fn($row) => $row->businessCancellation->total_loss_amount ?? '-')
            ->addColumn('lost_costs_incurred', function($row){
                return [
                    'employee' =>
                        $row->businessCancellation->employee_covered_amount,
                    'company' =>
                        $row->businessCancellation->company_covered_amount
                ];

            })
            ->addColumn('status', fn($row) => $row->status ?? '-')
            ->addColumn('action', function ($row) {
                return '
                    <div class="d-flex justify-content-center gap-2">
                            <button
                                class="btn btn-info btn-sm btn-detail-cancellation"
                                data-id="' . encrypt($row->id) . '"
                            >
                                <i class="ri-eye-line"></i>
                            </button>
                        </div>
                    ';
            })
            ->rawColumns([ 'no_document', 'type', 'date_and_day', 'dept_and_arr_times', 'action', 'depart_from' ])
            ->make(true);
    }
    public function cancellationHistory()
    {
        $employeeId = auth()->user()->employee->id;
        $data = BusinessCancellationApproval::with([
        'businessCancellation',
        'businessCancellation.businessTrip',
        'businessCancellation.businessTrip.employee',
        'businessCancellation.businessTrip.employee.area'
        ])
        ->where('approver_id', $employeeId)
        ->whereIn('status', ['approved', 'rejected'])
        ->latest()
        ->get();
        // dd($data);
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('nik', fn($row) => $row->businessCancellation->businessTrip->employee->nik ?? '-')
            ->addColumn('employee_name', fn($row) => $row->businessCancellation->businessTrip->employee->fullname ?? '-')
            ->addColumn('area', fn($row) => $row->businessCancellation->businessTrip->employee->area->name ?? '-')
            ->addColumn('department', fn($row) => $row->businessCancellation->businessTrip->department ?? '-')
            ->addColumn('position', fn($row) => $row->businessCancellation->businessTrip->position ?? '-')
            ->addColumn('no_document', fn($row) => $row->businessCancellation->businessTrip->no_document ?? '-')
            ->addColumn('propose_date', function ($row) {
                return Carbon::parse($row->businessCancellation->propose_date)
                    ->format('d M Y');
            })
            ->addColumn('type', function ($row) {
                if ($row->businessCancellation->businessTrip->trip_type === 'domestic') {
                    return '
                        <span class="badge bg-primary">
                            Domestic
                        </span>
                    ';
                }
                return '
                    <span class="badge bg-success">
                        Overseas
                    </span>
                ';
            })
            ->addColumn('date_and_day', function ($row) {
                $start = Carbon::parse($row->businessCancellation->businessTrip->start_date);
                $end = Carbon::parse($row->businessCancellation->businessTrip->end_date);
                $totalDays = $start->diffInDays($end) + 1;
                return '
                    <div class="fw-semibold">
                        '.$start->format('d M Y').'
                        <br>
                        s/d
                        <br>
                        '.$end->format('d M Y').'
                    </div>

                    <small class="">
                        '.$totalDays.' Hari
                    </small>
                ';
            })
            ->addColumn('reason_cancel', function($row){
                return [
                    'reason_cancel' => $row->businessCancellation->reason_cancel,
                    'reason_other'  => $row->businessCancellation->reason_other
                ];

            })
            ->addColumn('total_cost_lost', fn($row) => $row->businessCancellation->total_loss_amount ?? '-')
            ->addColumn('lost_costs_incurred', function($row){
                return [
                    'employee' =>
                        $row->businessCancellation->employee_covered_amount,
                    'company' =>
                        $row->businessCancellation->company_covered_amount
                ];

            })
            ->addColumn('status', fn($row) => $row->status ?? '-')
            ->addColumn('action', function ($row) {
                return '
                    <div class="d-flex justify-content-center gap-2">
                            <button
                                class="btn btn-info btn-sm btn-detail-cancellation-history"
                                data-id="' . encrypt($row->id) . '"
                            >
                                <i class="ri-eye-line"></i>
                            </button>
                        </div>
                    ';
            })
            ->rawColumns([ 'no_document', 'type', 'date_and_day', 'dept_and_arr_times', 'action', 'depart_from' ])
            ->make(true);
    }
    public function cancellationDetail(Request $request)
    {
        $id = decrypt($request->id);
        $cancelApproval = BusinessCancellationApproval::with([
                'businessCancellation.businessTrip',
                'businessCancellation.approvals.approver',
                'businessCancellation.approvals.logs',
                'businessCancellation.items'
            ])->findOrFail($id);
        return response()->json([
            'id'                    => $cancelApproval->id,
            'approval_id'           => $cancelApproval?->id,
            'can_action'            => $cancelApproval?->status === 'waiting',
            'no_document'           => $cancelApproval->businessCancellation->businessTrip?->no_document,
            'employee_name'         => $cancelApproval->businessCancellation->businessTrip?->employee?->fullname,
            'position'              => $cancelApproval->businessCancellation->businessTrip?->employee?->position->nama,
            'trip_type'             => $cancelApproval->businessCancellation->businessTrip?->trip_type,
            'start_date'            => $cancelApproval->businessCancellation->businessTrip?->start_date,
            'end_date'              => $cancelApproval->businessCancellation->businessTrip?->end_date,
            'total_days'            => $cancelApproval->businessCancellation->businessTrip?->total_days,
            'arrival_to'            => $cancelApproval->businessCancellation->businessTrip?->arrival_to,
            'total_loss_amount'     => $cancelApproval->businessCancellation->total_loss_amount,
            'employee_amount'       => $cancelApproval->businessCancellation->employee_covered_amount,
            'company_amount'        => $cancelApproval->businessCancellation->company_covered_amount,
            'reason_cancel'         => $cancelApproval->businessCancellation->reason_cancel,
            'reason_other'          => $cancelApproval->businessCancellation->reason_other,
            'status'                => $cancelApproval->businessCancellation->status,
            'approvals'             => $cancelApproval->businessCancellation->approvals,
            'items'                 => $cancelApproval->businessCancellation->items,
        ]);
    }
    public function singleProcessCancel(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'id'     => 'required|exists:business_cancellation_approvals,id',
            'action' => 'required|in:approved,revised,rejected',
            'reason' => 'nullable|string|required_if:action,rejected',
        ]);
        return $this->handleCancel([$request->id], $request->action, $request->reason);
    }
    private function handleCancel(array $ids,string $action,?string $reason = null)
    {
        DB::beginTransaction();
        try {
            $approvals = BusinessCancellationApproval::with([
                'businessCancellation.businessTrip.employee.user',
                'approver.user'
            ])
            ->whereIn('id', $ids)
            ->get();
            if ($approvals->isEmpty()) {
                throw new \Exception(
                    'Data approval tidak ditemukan'
                );
            }
            $nextApproverEmails = [];
            foreach ($approvals as $approval) {
                // hanya approval waiting yang bisa diproses
                if ($approval->status !== 'waiting') {
                    continue;
                }
                $businessCancel = $approval->businessCancellation;
                // ================= REJECTED =================
                if ($action === 'rejected') {

                    $businessCancel->update([
                        'status'     => 'rejected',
                        'updated_by' => auth()->user()->name,
                    ]);

                    $approval->update([
                        'status'      => 'rejected',
                        'approved_at' => now(),
                    ]);

                    if ($businessCancel->business_trip_id && $businessCancel->businessTrip)
                    {
                        $businessTrip = $businessCancel->businessTrip;

                        // approval terakhir business trip
                        $lastApproval = $businessTrip->approvals()
                            ->orderByDesc('level')
                            ->first();

                        // ===================================
                        // SUDAH FULL APPROVED SEBELUM CANCEL
                        // ===================================
                        if ($lastApproval && $lastApproval->status === 'approved')
                        {
                            $today = Carbon::today();

                            if (
                                Carbon::parse($businessTrip->start_date)
                                    ->gt($today)
                            ) {

                                // belum berangkat
                                $businessTrip->update([
                                    'status' => 'approved'
                                ]);

                            } else {

                                // sudah memasuki masa perjalanan
                                $businessTrip->update([
                                    'status' => 'ongoing'
                                ]);

                            }

                        } else {

                            // ===================================
                            // MASIH DALAM PROSES APPROVAL
                            // ===================================
                            $businessTrip->update([
                                'status' => 'draft'
                            ]);

                            // reset approval waiting/pending
                            $businessTrip->approvals()
                                ->whereIn('status', ['waiting', 'pending'])
                                ->update([
                                    'status' => 'pending'
                                ]);

                            // cari approval terakhir yang approved
                            $lastApproved = $businessTrip->approvals()
                                ->where('status', 'approved')
                                ->orderByDesc('level')
                                ->first();

                            if ($lastApproved) {

                                $nextApproval = $businessTrip->approvals()
                                    ->where('level', '>', $lastApproved->level)
                                    ->orderBy('level')
                                    ->first();

                                if ($nextApproval) {
                                    $nextApproval->update([
                                        'status' => 'waiting'
                                    ]);
                                }

                            } else {

                                $firstApproval = $businessTrip->approvals()
                                    ->orderBy('level')
                                    ->first();

                                if ($firstApproval) {
                                    $firstApproval->update([
                                        'status' => 'waiting'
                                    ]);
                                }
                            }
                        }
                    }

                    BusinessCancellationLog::create([
                        'business_cancellation_id'        => $businessCancel->id,
                        'approval_path_id'          => $approval->id,
                        'status'                    => 'rejected',
                        'reason'                    => $reason,
                        'action_at'                 => now()
                    ]);
                    continue;
                }
                // ================= REVISED =================
                if ($action === 'revised') {
                    $businessCancel->update([
                        'status'         => 'revised',
                        'updated_by'     => auth()->user()->name,
                        'revised_level'  => $approval->level,
                        'revise_count'   => ($businessReport->revise_count ?? 0) + 1,
                    ]);

                    // approval path kembali pending
                    $approval->update([
                        'status' => 'revised',
                    ]);

                    // simpan history revised
                    BusinessCancellationLog::create([
                        'business_cancellation_id'        => $businessCancel->id,
                        'approval_path_id'          => $approval->id,
                        'status'                    => 'revised',
                        'reason'                    => $reason,
                        'action_at'                 => now(),
                    ]);

                    continue;
                }
                // ================= APPROVED =================
                $approval->update([
                    'status'      => 'approved',
                    'approved_at' => now(),
                ]);
                BusinessCancellationLog::create([
                    'business_cancellation_id'  => $businessCancel->id,
                    'approval_path_id'          => $approval->id,
                    'status'                    => 'approved',
                    'reason'                    => $reason,
                    'action_at'                 => now(),
                ]);
                // ================= NEXT APPROVAL =================
                $nextApproval = BusinessCancellationApproval::with([
                    'approver.user'
                ])
                ->where('cancellation_id', $businessCancel->id)
                ->where('level', '>', $approval->level)
                ->orderBy('level')
                ->first();

                // ================= MASIH ADA APPROVAL =================
                if ($nextApproval) {

                    if ($nextApproval->status !== 'waiting') {

                        $nextApproval->update([
                            'status' => 'waiting'
                        ]);
                    }

                    $approver = $nextApproval->approver;
                    if ($approver?->user?->email) {
                        $email = $approver->user->email;
                        if (!isset($nextApproverEmails[$email])) {
                            $nextApproverEmails[$email] = [
                                'approver_name' => $approver->fullname,
                                'requests'      => []
                            ];
                        }
                        $nextApproverEmails[$email]['requests'][] = [
                            'text' =>
                                $businessCancel->businessTrip->employee->fullname .
                                ' | ' .
                                ucfirst($businessCancel->businessTrip->trip_type) .
                                ' | ' .
                                Carbon::parse(
                                    $businessCancel->businessTrip->start_date
                                )->format('d M Y')
                                . ' - ' .
                                Carbon::parse(
                                    $businessCancel->businessTrip->end_date
                                )->format('d M Y')
                                . ' | ' .
                                $businessCancel->businessTrip->arrival_to,

                            'token' => $nextApproval->approval_token,
                        ];
                    }

                } else {
                    // ================= FINAL APPROVED =================
                    $businessCancel->update([
                        'status'      => 'approved',
                        'approved_at' => now(),
                        'updated_by'  => auth()->user()->name,
                    ]);
                    if($businessCancel->business_trip_id && $businessCancel->businessTrip)
                    {
                        $businessCancel->businessTrip->update([
                            'status' => 'cancelled'
                        ]);
                    }
                }
            }

            // ================= SEND EMAIL =================
            foreach ($nextApproverEmails as $email => $data) {

                $payload = [
                'subject'    => 'Pengajuan Pembatalan Perjalanan Dinas Menunggu Approval',
                    'greeting'   => 'Hi ' . $data['approver_name'],
                    'requests'   => $data['requests'],
                    'actionText' => 'Lihat Pengajuan',
                    'actionURL'  => route('business-trip.approval', [
                        'token' => $data['requests'][0]['token'] ?? null// bisa pakai 1 token saja
                    ]). '#pill-cancellation',
                    'thanks'     => 'Terimakasih',
                ];
                Notification::route('mail', $email)
                    ->notify(
                        new BulkLeaveApprovalNotification(
                            $payload
                        )
                    );
            }
            $user = Auth::user();
            \App\Models\Log::create([
                'user_id' => $user->id,
                'ip_address' => request()->ip(),
                'action' => 'insert',
                'description' => "{$user->employee->fullname} Melakukan {$action} Pembatalan Perjalanan Dinas {$businessCancel->businessTrip->trip_type}/{$businessCancel->businessTrip->start_date}-{$businessCancel->businessTrip->end_date} Milik {$businessCancel->businessTrip->employee->fullname}"
            ]);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => ucfirst($action) . ' berhasil diproses'
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
