<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance\ClaimApproval;
use App\Models\Attendance\ClaimOvertime;
use App\Models\Attendance\EmployeeAttendance;
use App\Models\Employee;
use App\Models\Master\LineApproval;
use App\Notifications\AttendancePermitNotification;
use App\Notifications\BulkLeaveApprovalNotification;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use App\Models\Log;

class ClaimOvertimeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $employeeId = $user->employee->id;
        $employee = $user->employee;

        // 🔥 cek apakah dia approver
        $isApprover = LineApproval::where('approval_type', 'Attendance Overtime')
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
        return view('pages.profile.Attendance.overtime.index', compact('user','isApprover'));
    }
    public function dataMy()
    {
        $employee = auth()->user()->employee;
        $data = EmployeeAttendance::with('groupEmployeeWorkhour', 'masterWorkhour','claimOvertime','detail')
        ->where('employee_id', $employee->id)
        ->whereHas('detail', function ($q) {
            $q->where(function ($d) {
                $d->where('status_check_in', 'overtime')
                ->orWhere('status_check_out', 'overtime');
            });
        })
        ->whereDoesntHave('claimOvertime', function ($q) {
            $q->whereIn('status', [
                'pending',
                'waiting',
                'approved'
            ]);
        });
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('overtime_date', fn($row) => $row->date ?? '-')
            ->addColumn('workhour', function ($row) {
                $group = $row->groupEmployeeWorkhour?->name ?? '-';
                $workhour = $row->masterWorkhour?->work_name ?? '-';
                if ($group === '-' && $workhour === '-') {
                    return '-';
                }
                return "{$group} - {$workhour}";
            })
            ->addColumn('work_in', fn($row) => $row->work_in ?? '-')
            ->addColumn('work_out', fn($row) => $row->work_out ?? '-')
            ->addColumn('work_in_and_out', function ($row) {
                $start = $row->detail->check_in
                    ? Carbon::parse($row->detail->check_in)->format('H:i')
                    : null;
                $end = $row->detail->check_out
                    ? Carbon::parse($row->detail->check_out)->format('H:i')
                    : null;
                if ($start && $end) {
                    return "Mulai : $start <br> Selesai : $end";
                }
                if ($start) return "Masuk : $start";
                if ($end) return "Keluar : $end";
                return '-';
            })
            ->addColumn('total_work', function ($row) {
                $totalMinutes = 0;
                $checkIn = $row->detail?->check_in
                    ? Carbon::parse($row->detail->check_in)
                    : null;
                $checkOut = $row->detail?->check_out
                    ? Carbon::parse($row->detail->check_out)
                    : null;
                $workIn = $row->work_in
                    ? Carbon::parse($row->work_in)
                    : null;
                $workOut = $row->work_out
                    ? Carbon::parse($row->work_out)
                    : null;
                if ( $row->holiday_id && $checkIn && $checkOut) {
                    $totalMinutes = $checkOut->diffInMinutes($checkIn);
                } else if ($checkIn && $checkOut){
                    $totalMinutes = $checkOut->diffInMinutes($checkIn);
                } else {
                    if ($checkIn && $workIn && $checkIn->lt($workIn)) {
                        $totalMinutes += $workIn->diffInMinutes($checkIn);
                    }
                    if ($checkOut && $workOut && $checkOut->gt($workOut)) {
                        $totalMinutes += $checkOut->diffInMinutes($workOut);
                    }
                }
                $hours = floor($totalMinutes / 60);
                $minutes = $totalMinutes % 60;

                return "{$hours} Jam {$minutes} Menit";
            })
            ->addColumn('reason', function ($row) {
                $reasons = [];
                if ($row->detail?->reason_check_in) {
                    $reasons[] = $row->detail->reason_check_in;
                }
                if ($row->detail?->reason_check_out) {
                    $reasons[] = $row->detail->reason_check_out;
                }
                return count($reasons)
                    ? implode(' | ', $reasons)
                    : '-';
            })
            ->addColumn('action', function ($row) {
                $isHoliday =
                    $row->holiday_id !== null
                    || !$row->work_in
                    || !$row->work_out;
                return '
                    <div class="d-flex justify-content-center gap-2">
                        <button
                            class="btn btn-primary btn-sm btn-claim"
                            data-id="' . encrypt($row->id) . '"
                            data-holiday="' . ($isHoliday ? 1 : 0) . '"
                        >
                            <i class="bi bi-hand-index-thumb"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['action','work_in_and_out'])
            ->make(true);
    }
    public function dataMyHistory()
    {
        $employee = auth()->user()->employee;
        $data = ClaimOvertime::with('approvals')
        ->where('employee_id', $employee->id);
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('overtime_date', fn($row) => $row->overtime_date ?? '-')
            ->addColumn('claim_date', fn($row) => $row->claim_overtime ?? '-')
            ->addColumn('hrd_note', fn($row) => $row->hrd_note ?? '-')

            ->addColumn('total_work', function ($row) {
                $totalMinutes = (int) $row->total_work;
                $hours = floor($totalMinutes / 60);
                $minutes = $totalMinutes % 60;
                return "{$hours} Jam {$minutes} Menit";
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
            ->addColumn('reason', fn($row) => $row->approvals->first()?->reason_reject ?? '-')
            ->rawColumns(['agreed_work'])
            ->make(true);
    }
    public function claimOvertime(Request $request)
    {
        DB::beginTransaction();

        try {
            $attendanceId = decrypt($request->id);
            $attendance = EmployeeAttendance::with([
                'detail',
                'employee.lineApprovals'
            ])->findOrFail($attendanceId);
            $detail = $attendance->detail;
            $employee = $attendance->employee;

            if (!$detail) {
                throw new \Exception('Detail attendance tidak ditemukan');
            }
            $existingClaim = ClaimOvertime::where(
                'employee_attendance_id',
                $attendance->id
            )
            ->where('status', '!=', 'rejected')
            ->first();
            if ($existingClaim) {
                throw new \Exception(
                    'Attendance ini sudah pernah diajukan claim'
                );
            }
            $sources = [];
            $totalMinutes = 0;
            $actualStart = null;
            $actualEnd = null;
            $isHoliday =
                $isHoliday =
                    $attendance->holiday_id !== null
                    || !$attendance->work_in
                    || !$attendance->work_out;
            if ($isHoliday) {
                if (
                    !$request->agreed_work_start ||
                    !$request->agreed_work_end
                ) {
                    throw new \Exception(
                        'Hari libur wajib mengisi agreed work'
                    );
                }
                $sources[] = 'hl';
                $actualStart = $detail->check_in;
                $actualEnd   = $detail->check_out;
                $start = Carbon::parse($detail->check_in);
                $end   = Carbon::parse($detail->check_out);
                if ($end->lt($start)) {
                    $end->addDay();
                }
                $totalMinutes = $start->diffInMinutes($end);
            } else {
                if (
                    $detail->status_check_in === 'overtime' &&
                    $detail->check_in
                ) {
                    $sources[] = 'bf';
                    $checkIn = Carbon::parse($detail->check_in);
                    $workIn = Carbon::parse($attendance->work_in);
                    if ($checkIn->lt($workIn)) {
                        $minutes = $workIn->diffInMinutes($checkIn);
                        $totalMinutes += $minutes;
                        if (!$actualStart) {
                            $actualStart = $detail->check_in;
                        }
                        $actualEnd = $attendance->work_in;
                    }
                }

                if (
                    $detail->status_check_out === 'overtime' &&
                    $detail->check_out
                ) {
                    $sources[] = 'af';
                    $checkOut = Carbon::parse($detail->check_out);
                    $workOut = Carbon::parse($attendance->work_out);
                    if ($checkOut->gt($workOut)) {
                        $minutes = $checkOut->diffInMinutes($workOut);
                        $totalMinutes += $minutes;
                        if (!$actualStart) {
                            $actualStart = $attendance->work_out;
                        }
                        $actualEnd = $detail->check_out;
                    }
                }
            }
            if ($totalMinutes <= 0) {
                throw new \Exception(
                    'Total overtime tidak valid'
                );
            }
            $source = implode('|', $sources);
            // dd($totalMinutes);
            $claim = ClaimOvertime::create([
                'employee_id' => $employee->id,
                'employee_attendance_id' => $attendance->id,
                'position' => $attendance->position_name,
                'area' => $attendance->area_name,
                'department' => $attendance->department_name,
                'overtime_date' => $attendance->date,
                'claim_overtime' => now()->toDateString(),
                'actual_start_time' => $actualStart,
                'actual_end_time' => $actualEnd,
                'agreed_work_start' => $request->agreed_work_start,
                'agreed_work_end' => $request->agreed_work_end,
                'total_work' => $totalMinutes,
                'reason' => $request->reason,
                'source' => $source,
                'status' => 'waiting',
                'created_by' => auth()->user()->name,
                'updated_by' => auth()->user()->name,
            ]);

            $lineApproval = $employee->lineApprovals()
                ->where('approval_type', 'Attendance Overtime')
                ->first();

            if (!$lineApproval) {
                throw new \Exception(
                    'Line approval tidak ditemukan'
                );
            }
            $approverIds = collect([
                $lineApproval->approve_1,
                $lineApproval->approve_2,
                $lineApproval->approve_3,
                $lineApproval->approve_4,
                $lineApproval->approve_5,
                $lineApproval->approve_6,
                $lineApproval->approve_7,
                $lineApproval->approve_8,
            ])
            ->filter()
            ->values();
            // dd($approverIds);
            foreach ($approverIds as $index => $approverId) {
                $approver = Employee::with([
                    'position',
                    'department'
                ])->find($approverId);

                if (!$approver) {
                    continue;
                }
                ClaimApproval::create([
                    'claim_overtime_id' => $claim->id,
                    'employee_id' => $approver->id,
                    'position' => $approver->position->nama ?? '-',
                    'department' => $approver->department->name ?? '-',
                    'level' => $index + 1,
                    'status' => $index === 0
                        ? 'waiting'
                        : 'pending',
                    'approval_token' => Str::uuid(),
                ]);
            }
            $firstApproval = ClaimApproval::with([
                'employee.user'
            ])
            ->where('claim_overtime_id', $claim->id)
            ->where('level', 1)
            ->first();
            if (
                $firstApproval &&
                $firstApproval->employee?->user
            ) {
                $hours = floor($totalMinutes / 60);
                $minutes = $totalMinutes % 60;
                $sourceMap = [
                    'bf' => 'Lembur Sebelum Jam Kerja',
                    'af' => 'Lembur Setelah Jam Kerja',
                    'hl' => 'Lembur di Hari Libur',
                    'bf|af' => 'Lembur Sebelum & Setelah Jam Kerja',
                ];

                $sources = explode(',', $claim->source);

                $sourceLabel = collect($sources)
                    ->map(fn ($source) => $sourceMap[$source] ?? $source)
                    ->implode(' | ');
                $details = [
                    'greeting' =>
                        'Hi ' .
                        $firstApproval->employee->fullname,
                    'subject' =>
                        'Pengajuan Claim Lembur',
                    'lines' => [
                        'Karyawan mengajukan claim lembur',
                        'Nama : ' . $employee->fullname,
                        'Tanggal : ' .
                        Carbon::parse($attendance->date)
                            ->format('d M Y'),
                        'Durasi : ' .
                        $hours . ' Jam ' .
                        $minutes . ' Menit',
                        'Source : ' . $sourceLabel,
                    ],
                    'actionText' => 'Lihat Pengajuan',
                    'actionURL' => route(
                        'claim-overtime.claim',
                        [ 'token' => $firstApproval->approval_token ]
                    ) . '#pill-approval', 'thanks' => 'Terimakasih'
                ];
                $firstApproval->employee->user
                    ->notify(
                        new AttendancePermitNotification($details)
                    );
            }
            Log::create([
                'user_id'    => auth()->user()->id,
                'ip_address' => $request->ip(),
                'action'     => 'insert',
                'description'=> "{$employee->fullname} mengajukan claim overtime"
            ]);
            DB::commit();
            return response()->json([
                'message' => 'Claim overtime berhasil dibuat'

            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function dataApproval()
    {
        $employee = auth()->user()->employee;

        $data = ClaimApproval::with(['claimOvertime', 'claimOvertime.employee','claimOvertime.employeeAttendance'])
                ->where('employee_id', $employee->id)
                ->where('status', 'waiting')
                ->latest()
                ->get();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('nik', fn($row) => $row->claimOvertime->employee->nik ?? '-')
            ->addColumn('employee_name', fn($row) => $row->claimOvertime->employee->fullname ?? '-')
            ->addColumn('area', fn($row) => $row->claimOvertime->area ?? '-')
            ->addColumn('department', fn($row) => $row->claimOvertime->department ?? '-')
            ->addColumn('position', fn($row) => $row->claimOvertime->position ?? '-')
            ->addColumn('workhour', function ($row) {
                $group = $row->claimOvertime->employeeAttendance->groupEmployeeWorkhour?->name ?? '-';
                $workhour = $row->claimOvertime->employeeAttendance->masterWorkhour?->work_name ?? '-';
                if ($group === '-' && $workhour === '-') {
                    return '-';
                }
                return "{$group} - {$workhour}";
            })
            ->addColumn('work_in', fn($row) => $row->claimOvertime->employeeAttendance->work_in ?? '-')
            ->addColumn('work_out', fn($row) => $row->claimOvertime->employeeAttendance->work_out ?? '-')
            ->addColumn('overtime_date', fn($row) => $row->claimOvertime->overtime_date ?? '-')
            ->addColumn('actual_work', function ($row) {
                    $start = $row->claimOvertime->actual_start_time
                        ? Carbon::parse($row->claimOvertime->actual_start_time)->format('H:i')
                        : null;

                    $end = $row->claimOvertime->actual_end_time
                        ? Carbon::parse($row->claimOvertime->actual_end_time)->format('H:i')
                        : null;

                    if ($start && $end) {
                        return "Mulai : $start <br> Selesai : $end";
                    }
                    if ($start) return "Mulai : $start";
                    if ($end) return "Selesai : $end";

                    return '-';
                })
            ->addColumn('agreed_work', function ($row) {
                    $start = $row->claimOvertime->agreed_work_start
                        ? Carbon::parse($row->claimOvertime->agreed_work_start)->format('H:i')
                        : null;

                    $end = $row->claimOvertime->agreed_work_end
                        ? Carbon::parse($row->claimOvertime->agreed_work_end)->format('H:i')
                        : null;

                    if ($start && $end) {
                        return "Mulai : $start <br> Selesai : $end";
                    }
                    if ($start) return "Mulai : $start";
                    if ($end) return "Selesai : $end";

                    return '-';
                })
            ->addColumn('total_work', function ($row) {
                $totalMinutes = (int) $row->claimOvertime->total_work;
                $hours = floor($totalMinutes / 60);
                $minutes = $totalMinutes % 60;
                return "{$hours} Jam {$minutes} Menit";
            })
            ->addColumn('reason', fn($row) => $row->claimOvertime->reason ?? '-')
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
            ->rawColumns(['action','actual_work','agreed_work'])
            ->make(true);
    }
    public function singleProcessApproval(Request $request)
    {
        // dd($request->reason);
        $request->validate([
            'id'     => 'required|exists:claim_approvals,id',
            'action' => 'required|in:approved,rejected',
            'reason' => 'nullable|string|required_if:action,rejected',
        ]);
        return $this->handleApproval([$request->id], $request->action, $request->reason);
    }
    public function bulkProcessApproval(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array',
            'ids.*'  => 'exists:claim_approvals,id',
            'action' => 'required|in:approved,rejected',
            'reason' => 'nullable|string|required_if:action,rejected',
        ]);

        return $this->handleApproval($request->ids, $request->action, $request->reason);
    }
    private function handleApproval(array $ids, string $action, ?string $reason = null)
    {
        DB::beginTransaction();

        try {
            $user = Auth::user();
            $approvals = ClaimApproval::with([
                'claimOvertime',
                'employee.user'
            ])->whereIn('id', $ids)->get();

            if ($approvals->isEmpty()) {
                throw new \Exception('Data approval tidak ditemukan');
            }
            $nextApproverEmails = [];
            foreach ($approvals as $approval) {

                if ($approval->status !== 'waiting') {
                    continue;
                }
                $approval->update([
                    'status'        => $action,
                    'approved_at'   => now(),
                    'reason_reject' => $reason
                ]);
                $claimOvertime = $approval->claimOvertime;

                Log::create([
                    'user_id'    => $user->id,
                    'ip_address' => request()->ip(),
                    'action'     => $action,
                    'description'=> "{$user->employee->fullname} {$action} claim overtime milik " . ($claimOvertime->employee->fullname ?? '-')
                ]);
                // ❌ REJECTED → STOP
                if ($action === 'rejected') {
                    $claimOvertime->update([
                        'status'     => 'rejected',
                        'updated_by' => auth()->user()->name,
                    ]);
                    continue;
                }
                // 🔍 NEXT APPROVAL
                $nextApproval = ClaimApproval::with('employee.user','claimOvertime.employeeAttendance')
                    ->where('claim_overtime_id', $claimOvertime->id)
                    ->where('level', '>', $approval->level)
                    ->orderBy('level')
                    ->first();
                if ($nextApproval) {
                    // aktifkan next level
                    if ($nextApproval->status !== 'waiting') {
                        $nextApproval->update([
                            'status' => 'waiting'
                        ]);
                    }
                    $approver = $nextApproval->employee;
                    if ($approver?->user?->email) {
                        $email = $approver->user->email;
                        // INIT
                        if (!isset($nextApproverEmails[$email])) {
                            $nextApproverEmails[$email] = [
                                'approver_name' => $approver->fullname,
                                'requests'      => []
                            ];
                        }
                        $sourceLabel = match($claimOvertime->source) {
                            'bf' => 'Lembur Sebelum Jam Kerja',
                            'af' => 'Lembur Setelah Jam Kerja',
                            'hl' => 'Lembur di Hari Libur',
                            'bf|af' => 'Lembur Sebelum & Setelah Jam Kerja',
                            default => '-'
                        };
                        $nextApproverEmails[$email]['requests'][] = [
                            'text' => "{$claimOvertime->employee->fullname} | " .
                                        $sourceLabel . " | " .
                                        Carbon::parse($claimOvertime->overtime_date)->format('d M Y') . " | " .
                                        ($claimOvertime->actual_start_time ?? '-') . " - " .
                                        ($claimOvertime->actual_end_time ?? '-') . " | " .
                                        ($claimOvertime->total_work ?? 0) . " menit",

                            'token' => $nextApproval->approval_token,
                        ];
                    }
                } else {
                    $claimOvertime->update([
                        'status'     => 'approved',
                        'updated_by' => auth()->user()->name
                    ]);
                    if ($claimOvertime->employeeAttendance) {
                        $sourceLabel = match($claimOvertime->source) {
                            'bf' => 'Lembur Sebelum Jam Kerja',
                            'af' => 'Lembur Setelah Jam Kerja',
                            'hl' => 'Lembur di Hari Libur',
                            'bf|af' => 'Lembur Sebelum & Setelah Jam Kerja',
                            default => '-'
                        };
                        $claimOvertime->employeeAttendance->update([
                            'attendance_status' => 'overtime',
                            'source' => $sourceLabel,
                        ]);
                    // dd($sourceLabel);
                    }
                }
            }
            foreach ($nextApproverEmails as $email => $data) {

                $payload = [
                    'subject'    => 'Permintaan Lembur Menunggu Approval',
                    'greeting'   => 'Hi ' . $data['approver_name'],
                    'requests'   => $data['requests'],
                    'actionText' => 'Lihat Pengajuan',
                    'actionURL'  => route('claim-overtime.claim', [
                        'token' => $data['requests'][0]['token'] ?? null// bisa pakai 1 token saja
                    ]). '#pill-approval',
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
    public function dataApprovalHistory()
    {
        $employee = auth()->user()->employee;

        $data = ClaimApproval::with(['claimOvertime', 'claimOvertime.employee','claimOvertime.employeeAttendance'])
                ->where('employee_id', $employee->id)
                ->where('status', ['approved','rejected'])
                ->latest();

        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('nik', fn($row) => $row->claimOvertime->employee->nik ?? '-')
            ->addColumn('employee_name', fn($row) => $row->claimOvertime->employee->fullname ?? '-')
            ->addColumn('area', fn($row) => $row->claimOvertime->area ?? '-')
            ->addColumn('department', fn($row) => $row->claimOvertime->department ?? '-')
            ->addColumn('position', fn($row) => $row->claimOvertime->position ?? '-')
            ->addColumn('workhour', function ($row) {
                $group = $row->claimOvertime->employeeAttendance->groupEmployeeWorkhour?->name ?? '-';
                $workhour = $row->claimOvertime->employeeAttendance->masterWorkhour?->work_name ?? '-';
                if ($group === '-' && $workhour === '-') {
                    return '-';
                }
                return "{$group} - {$workhour}";
            })
            ->addColumn('work_in', fn($row) => $row->claimOvertime->employeeAttendance->work_in ?? '-')
            ->addColumn('work_out', fn($row) => $row->claimOvertime->employeeAttendance->work_out ?? '-')
            ->addColumn('overtime_date', fn($row) => $row->claimOvertime->overtime_date ?? '-')
            ->addColumn('start_at', function ($row) {
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
            ->addColumn('actual_work', function ($row) {
                $start = $row->claimOvertime->actual_start_time
                    ? Carbon::parse($row->claimOvertime->actual_start_time)->format('H:i')
                    : null;

                $end = $row->claimOvertime->actual_end_time
                    ? Carbon::parse($row->claimOvertime->actual_end_time)->format('H:i')
                    : null;

                if ($start && $end) {
                    return "Dimulai : $start <br> Selesai : $end";
                }
                if ($start) return "Mulai : $start";
                if ($end) return "Selesai : $end";

                return '-';
            })
            ->addColumn('agreed_work', function ($row) {
                $start = $row->claimOvertime->agreed_start_time
                    ? Carbon::parse($row->claimOvertime->agreed_start_time)->format('H:i')
                    : null;

                $end = $row->claimOvertime->agreed_end_time
                    ? Carbon::parse($row->claimOvertime->agreed_end_time)->format('H:i')
                    : null;

                if ($start && $end) {
                    return "Mulai : $start <br> Selesai : $end";
                }
                if ($start) return "Mulai : $start";
                if ($end) return "Selesai : $end";

                return '-';
            })
            ->addColumn('total_work', function ($row) {
                $totalMinutes = (int) $row->claimOvertime->total_work;
                $hours = floor($totalMinutes / 60);
                $minutes = $totalMinutes % 60;
                return "{$hours} Jam {$minutes} Menit";
            })
            ->addColumn('reason', fn($row) => $row->claimOvertime->reason ?? '-')
            ->rawColumns(['actual_work', 'agreed_work'])
            ->make(true);
    }
    public function pendingCount()
    {
        $employee = auth()->user()->employee;

        $totalApproval = ClaimApproval::where('employee_id', $employee->id)
            ->where('status', 'waiting')
            ->count();

        return response()->json([
            'total' => $totalApproval
        ]);
    }
    private function getWorkHourByDate($employee, $date)
    {
        $groupEmployee = $employee->groupEmployees->first();

        if (!$groupEmployee || !$groupEmployee->groupEmployeeWorkhour) {
            return null;
        }

        $targetDate = Carbon::parse($date);

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
            'group_id' => $groupEmployee->groupEmployeeWorkhour->id ?? null,
            'group_name' => $groupEmployee->groupEmployeeWorkhour->name ?? null,
            'id' => $activeGroupWorkHour->workhour->id ?? null,
            'work_name' => $activeGroupWorkHour->workhour->work_name ?? null,
            'work_in' => $detail->work_in,
            'work_out' => $detail->work_out,
        ];
    }

}

 // public function claimOvertime(Request $request)
    // {
    //     DB::beginTransaction();

    // try {
    //     $id = decrypt($request->id);

    //     $claim = ClaimOvertime::with('employee')->findOrFail($id);

    //     $employee = $claim->employee;

    //     // ================= VALIDASI =================
    //     if ($claim->source === 'hl') {
    //         if (!$request->agreed_work_start || !$request->agreed_work_end) {
    //             throw new \Exception('Hari libur wajib isi jam lembur yang disepakati');
    //         }
    //     }

    //     // ================= DETERMINE TIME =================
    //     if ($claim->source === 'hl') {
    //         $start = Carbon::parse($request->agreed_work_start);
    //         $end   = Carbon::parse($request->agreed_work_end);
    //     } else {
    //         $start = Carbon::parse($claim->actual_start_time);
    //         $end   = Carbon::parse($claim->actual_end_time);
    //     }

    //     // ================= HITUNG TOTAL =================
    //     $totalWork = $end->diffInMinutes($start);

    //     // ================= UPDATE CLAIM =================
    //     $claim->update([
    //         'claim_overtime'     => now()->toDateString(),
    //         'agreed_work_start'  => $request->agreed_work_start,
    //         'agreed_work_end'    => $request->agreed_work_end,
    //         'total_work'         => $totalWork,
    //         'status'             => 'waiting',
    //         'updated_by'         => auth()->user()->name,
    //     ]);

    //     // ================= AMBIL LINE APPROVAL =================
    //     $lineApproval = $employee->lineApprovals()
    //         ->where('approval_type', 'Attendance Overtime')
    //         ->first();

    //     if (!$lineApproval) {
    //         throw new \Exception('Line approval tidak ditemukan');
    //     }

    //     // ================= GENERATE APPROVAL =================
    //     $approvals = [
    //         $lineApproval->approve_1 ?? null,
    //         $lineApproval->approve_2 ?? null,
    //         $lineApproval->approve_3 ?? null,
    //         $lineApproval->approve_4 ?? null,
    //         $lineApproval->approve_5 ?? null,
    //         $lineApproval->approve_6 ?? null,
    //         $lineApproval->approve_7 ?? null,
    //         $lineApproval->approve_8 ?? null,
    //     ];

    //     foreach ($approvals as $index => $approverId) {
    //         if (!$approverId) continue;
    //     // dd($approvals);

    //         ClaimApproval::create([
    //             'claim_overtime_id' => $claim->id,
    //             'approver_id'       => $approverId,
    //             'position'          => Employee::find($approverId)->position->nama ?? '-',
    //             'department'        => Employee::find($approverId)->department->name ?? '-',
    //             'level'             => $index + 1,
    //             'status'            => $index === 0 ? 'waiting' : 'pending',
    //             'approval_token'    => Str::uuid(),
    //         ]);
    //     }

    //     // ================= KIRIM NOTIF KE LEVEL 1 =================
    //     $firstApproval = ClaimApproval::where('claim_overtime_id', $claim->id)
    //         ->where('level', 1)
    //         ->first();

    //     if ($firstApproval) {
    //         $approver = Employee::find($firstApproval->employee_id);

    //         if ($approver && $approver->user) {

    //             $details = [
    //                 'greeting' => 'Hi ' . $approver->fullname,
    //                 'subject'  => 'Pengajuan Lembur',
    //                 'lines' => [
    //                     'Karyawan mengajukan lembur:',
    //                     'Nama : ' . $employee->fullname,
    //                     'Tanggal : ' . $claim->overtime_date,
    //                     'Total Jam : ' . $totalWork . ' Minutes',
    //                 ],
    //                 'actionText' => 'Lihat Detail',
    //                 'actionURL'  => route('claim-overtime.claim', [
    //                     'token' => $firstApproval->approval_token
    //                 ]) . '#pill-approval' ,
    //                 'thanks' => 'Terimakasih'
    //             ];

    //             $approver->user->notify(new AttendancePermitNotification($details));
    //         }
    //     }

    //     DB::commit();

    //     return response()->json([
    //         'message' => 'Claim overtime berhasil & dikirim ke atasan'
    //     ]);

    // } catch (\Throwable $e) {
    //     DB::rollBack();

    //     return response()->json([
    //         'message' => $e->getMessage()
    //     ], 500);
    // }
    // }
