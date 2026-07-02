<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance\AttendancePermit;
use App\Models\Attendance\BusinessTrip\BusinessCancellation;
use App\Models\Attendance\BusinessTrip\BusinessReport;
use App\Models\Attendance\BusinessTrip\BusinessTrip;
use App\Models\Attendance\BusinessTrip\BusinessTripApproval;
use App\Models\Attendance\ClaimOvertime;
use App\Models\Employee;
use App\Models\Master\LineApproval;
use App\Models\Master\LineApprovalEmployee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class AttendancePermitController extends Controller
{
    public function index(Request $request)
    {
        // $know = AttendancePermit::query()->where('hrd_knowledge', 0);
        if ($request->ajax()) {

            $data = AttendancePermit::query()
            ->orderBy('hrd_knowledge', 'asc') // 0 dulu baru 1
            ->latest(); // optional: biar yang terbaru di atas juga
            // 🔥 FILTER DATE
            if ($request->filter_date) {
                $data->whereDate('start_date', $request->filter_date);
            }
            // 🔥 FILTER TYPE
            if ($request->filter_type) {
                $data->where('type', $request->filter_type);
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('date_permit', function ($row) {
                    return $row->end_date
                        ? $row->start_date . ' s/d ' . $row->end_date
                        : $row->start_date;
                })
                ->addColumn('time_permit', function ($row) {
                    $start = $row->start_time
                        ? Carbon::parse($row->start_time)->format('H:i')
                        : null;

                    $end = $row->end_time
                        ? Carbon::parse($row->end_time)->format('H:i')
                        : null;

                    if ($start && $end) {
                        return "Keluar : $end<br>Kembali : $start";
                    }
                    if ($start) return "Masuk : $start";
                    if ($end) return "Keluar : $end";

                    return '-';
                })
                ->addColumn('type', function ($row) {
                    return $this->formatType($row->type);
                })
                ->addColumn('workhour', function ($row) {
                    return $row->work_in . ' - ' . $row->work_out;
                })
                ->addColumn('action', function ($row) {
                    if ($row->head_knowledge !== 1) {
                        return '';
                    }

                    return '
                        <button class="btn btn-info btn-sm detail-btn"
                            data-id="'.encrypt($row->id).'">
                            <i class="ri-eye-line"></i>
                        </button>
                    ';
                })
                ->rawColumns(['action','time_permit'])
                ->make(true);
        }
        return view('pages.attendance.sub-menu.attendance-permit.index');
    }
    private function formatType($type)
    {
        return match ($type) {
            'earlyout' => 'Pulang Cepat',
            'late' => 'Terlambat',
            'temporary_out' => 'Keluar Sementara',
            'pribadi' => 'Izin Pribadi',
            'sick' => 'Izin Dokter',
            default => 'Lainnya',
        };
    }
    public function detail($id)
    {
        $id = decrypt($id);
        $data = AttendancePermit::findOrFail($id);
        return response()->json($data);
    }
    public function hrdKnowledge($id)
    {
        $id = decrypt($id);

        $data = AttendancePermit::findOrFail($id);

        $data->update([
            'hrd_knowledge' => 1,
            'hrd_name' => auth()->user()->name,
            'updated_by' => auth()->user()->name
        ]);

        return response()->json(['success' => true]);
    }
    public function indexOvertime(Request $request)
    {
        $data = ClaimOvertime::with(['approvals','employeeAttendance.groupEmployeeWorkhour','employeeAttendance.groupEmployeeWorkhour','employee'])
                ->whereIn('status', ['approved', 'waiting'])->latest();

        if ($request->date) {
            $data->whereDate('claim_overtime', $request->date);
        }
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('nik', fn($row) => $row->employee->nik ?? '-')
            ->addColumn('employee_name', fn($row) => $row->employee->fullname ?? '-')
            ->addColumn('area', fn($row) => $row->area ?? '-')
            ->addColumn('department', fn($row) => $row->department ?? '-')
            ->addColumn('position', fn($row) => $row->position ?? '-')
            ->addColumn('workhour', function ($row) {
                $group = $row->employeeAttendance->groupEmployeeWorkhour?->name ?? '-';
                $workhour = $row->employeeAttendance->masterWorkhour?->work_name ?? '-';
                if ($group === '-' && $workhour === '-') {
                    return '-';
                }
                return "{$group} - {$workhour}";
            })
            ->addColumn('work_in', fn($row) => $row->employeeAttendance->work_in ?? '-')
            ->addColumn('work_out', fn($row) => $row->employeeAttendance->work_out ?? '-')
            ->addColumn('overtime_date', fn($row) => $row->overtime_date ?? '-')
            ->addColumn('note', fn($row) => $row->hrd_note ?? '-')
            ->addColumn('overtime_work', function ($row) {
                $start = $row->actual_start_time
                    ? Carbon::parse(
                        $row->actual_start_time
                    )->format('H:i')
                    : null;
                $end = $row->actual_end_time
                    ? Carbon::parse(
                        $row->actual_end_time
                    )->format('H:i')
                    : null;
                $timeText = '-';
                if ($start && $end) {
                    $timeText = "Mulai : $start <br> Selesai : $end";
                } elseif ($start) {
                    $timeText = "Mulai : $start";
                } elseif ($end) {
                    $timeText = "Selesai : $end";
                }
                return " <div class='text-center'> $timeText </div>";
            })
            ->addColumn('agreed_work', function ($row) {
                    $start = $row->agreed_work_start
                        ? Carbon::parse($row->agreed_work_start)->format('H:i')
                        : null;

                    $end = $row->agreed_work_end
                        ? Carbon::parse($row->agreed_work_end)->format('H:i')
                        : null;

                    if ($start && $end) {
                        return "Mulai : $start <br> Selesai : $end";
                    }
                    if ($start) return "Mulai : $start";
                    if ($end) return "Selesai : $end";

                    return '-';
                })
            ->addColumn('total_work', function ($row) {
                $totalMinutes = (int) $row->total_work;
                $hours = floor($totalMinutes / 60);
                $minutes = $totalMinutes % 60;
                return "{$hours} Jam {$minutes} Menit";
            })
            ->addColumn('reason', fn($row) => $row->claimOvertime->reason ?? '-')
            ->addColumn('action', function ($row) {
                return '
                    <div class="d-flex justify-content-center gap-2">
                        <button class="btn btn-success btn-sm overtime_knowledge-btn" data-id="'.encrypt($row->id).'">
                            <i class="ri-eye-line"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['action','overtime_work','agreed_work','hrd_knowledge'])
            ->make(true);
    }
    public function overtimeDetail($id)
    {
        $id = decrypt($id);
        $data = ClaimOvertime::with('employee','approvals.employee')
        ->findOrFail($id);
        return response()->json($data);
    }
    public function overtimeKnowledge($id)
    {
        $id = decrypt($id);
        $data = ClaimOvertime::findOrFail($id);
        // dd($data);
        $data->update([
            'hrd_knowledge' => 1,
            'hrd_note' => request('notes'),
            'hrd_name' => auth()->user()->name,
            'updated_by' => auth()->user()->name
        ]);
        return response()->json(['success' => true]);
    }
    public function IndexBusinessTrip(Request $request){
        // $employeeId = auth()->user()->employee->id;
        $data = BusinessTrip::whereNotIn('status',['cancelled','cancel_waiting', 'reported', 'completed']);

        // dd($request->request_month);
        if ($request->request_month) {
            [$year, $month] = explode('-', $request->request_month);
            $data->whereYear('propose_date', $year)
                ->whereMonth('propose_date', $month);
        }

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('nik', fn($row) => $row->employee->nik ?? '-')
            ->addColumn('employee_name', fn($row) => $row->employee->fullname ?? '-')
            ->addColumn('area', fn($row) => $row->employee->area->name ?? '-')
            ->addColumn('department', fn($row) => $row->department ?? '-')
            ->addColumn('position', fn($row) => $row->position ?? '-')
            ->addColumn('no_document', fn($row) => $row->no_document ?? '-')
            // ->addColumn('request_date', function ($row) {
            //     return Carbon::parse($row->propose_date)
            //         ->format('d M Y');
            // })
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
                return '
                    <div class="d-flex justify-content-center gap-2">
                            <button
                                class="btn btn-info btn-sm btn-business-trip-detail"
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
    public function businessTripDetail(Request $request)
    {
        $id = decrypt($request->id);

        $businessTrip = BusinessTrip::with([
            'employee.position',
            'employee.level',
            'employee.department',
            'costs',
            'transportations',
            'hotels',
            'approvals.approver',
            'approvals.logs',
        ])->findOrFail($id);

        return response()->json([
            'id'              => $businessTrip->id,
            'no_document'     => $businessTrip->no_document,
            'trip_type'       => $businessTrip->trip_type,
            'start_date'      => $businessTrip->start_date,
            'end_date'        => $businessTrip->end_date,
            'total_days'      => $businessTrip->total_days,
            'departure_from'  => $businessTrip->departure_from,
            'arrival_to'      => $businessTrip->arrival_to,
            'purpose'         => $businessTrip->purpose,

            'employee'        => $businessTrip->employee,
            'position'        => $businessTrip->employee?->position?->nama,
            'level'           => $businessTrip->employee?->level?->nama,

            'costs'           => $businessTrip->costs,
            'transportations' => $businessTrip->transportations,
            'hotels'          => $businessTrip->hotels,
            'approvals'       => $businessTrip->approvals,
        ]);
    }
    public function IndexBusinessReport (Request $request){
        $data = BusinessReport::with(['businessTrip','employee.area']);

        if ($request->request_month) {
            [$year, $month] = explode('-', $request->request_month);
            $data->whereYear('propose_date', $year)
                ->whereMonth('propose_date', $month);
        }

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('nik', fn($row) => $row->employee->nik ?? '-')
            ->addColumn('employee_name', fn($row) => $row->employee->fullname ?? '-')
            ->addColumn('area', fn($row) => $row->employee->area->name ?? '-')
            ->addColumn('department', fn($row) => $row->department ?? '-')
            ->addColumn('position', fn($row) => $row->position ?? '-')
            ->addColumn('no_document', fn($row) => $row->businessTrip->no_document ?? '-')
            ->addColumn('request_date', function ($row) {
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
            ->rawColumns([ 'no_document', 'type', 'date_and_day', 'action' ])
            ->make(true);
    }

    public function BusinessReportDetail(Request $request)
    {
        $id = decrypt($request->id);

        $data = BusinessReport::with([
            'employee.position',
            'employee.level',
            'businessTrip',
            'reportItems.attachments',
            'approvals.approver',
            'approvals.logs',
        ])->findOrFail($id);

        $meals = $data->reportItems
            ->where('category', 'meal')
            ->values();

        $expenses = $data->reportItems
            ->where('category', '!=', 'meal')
            ->values();

        return response()->json([
            'id'            => $data->id,
            'no_document'   => $data->businessTrip?->no_document,
            'employee_name' => $data->employee?->fullname,
            'trip_type'     => $data->trip_type,
            'start_date'    => $data->start_date,
            'end_date'      => $data->end_date,
            'total_days'    => $data->total_days,
            'arrival_to'    => $data->arrival_to,
            'purpose'       => $data->purpose,
            'total_cost'    => $data->total_cost,
            'status'        => $data->status,

            'meals'         => $meals,
            'expenses'      => $expenses,
            'approvals'     => $data->approvals,
        ]);
    }
    public function IndexBusinessCancellation(Request $request){
        $data = BusinessCancellation::with([
            'businessTrip.employee'
        ])
        ->whereHas('businessTrip', fn($q) =>
            $q->whereIn('status', [
                'cancelled',
                'cancel_waiting'
            ])
        );

        if ($request->request_month) {
            [$year, $month] = explode('-', $request->request_month);
            $data->whereYear('propose_date', $year)
                ->whereMonth('propose_date', $month);
        }
        // dd($data);
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('nik', fn($row) => $row->businessTrip->employee->nik ?? '-')
            ->addColumn('employee_name', fn($row) => $row->businessTrip->employee->fullname ?? '-')
            ->addColumn('area', fn($row) => $row->businessTrip->employee->area->name ?? '-')
            ->addColumn('department', fn($row) => $row->businessTrip->department ?? '-')
            ->addColumn('position', fn($row) => $row->businessTrip->position ?? '-')
            ->addColumn('no_document', fn($row) => $row->businessTrip->no_document ?? '-')
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
    public function BusinessCancellationDetail(Request $request){
        $id = decrypt($request->id);
        $cancelApproval = BusinessCancellation::with([
                'businessTrip',
                'approvals.approver',
                'approvals.logs',
                'items'
            ])->findOrFail($id);
        return response()->json([
            'id'                    => $cancelApproval->id,
            'approval_id'           => $cancelApproval?->id,
            'can_action'            => $cancelApproval?->status === 'waiting',
            'no_document'           => $cancelApproval->businessTrip?->no_document,
            'employee_name'         => $cancelApproval->businessTrip?->employee?->fullname,
            'position'              => $cancelApproval->businessTrip?->employee?->position->nama,
            'trip_type'             => $cancelApproval->businessTrip?->trip_type,
            'start_date'            => $cancelApproval->businessTrip?->start_date,
            'end_date'              => $cancelApproval->businessTrip?->end_date,
            'total_days'            => $cancelApproval->businessTrip?->total_days,
            'arrival_to'            => $cancelApproval->businessTrip?->arrival_to,
            'total_loss_amount'     => $cancelApproval->total_loss_amount,
            'employee_amount'       => $cancelApproval->employee_covered_amount,
            'company_amount'        => $cancelApproval->company_covered_amount,
            'reason_cancel'         => $cancelApproval->reason_cancel,
            'reason_other'          => $cancelApproval->reason_other,
            'status'                => $cancelApproval->status,
            'approvals'             => $cancelApproval->approvals,
            'items'                 => $cancelApproval->items,
        ]);
    }
}
