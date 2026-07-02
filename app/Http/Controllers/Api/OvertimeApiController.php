<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance\EmployeeAttendance;
use App\Models\Attendance\ClaimOvertime;
use App\Models\Attendance\ClaimApproval;
use App\Notifications\AttendancePermitNotification;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Log;

class OvertimeApiController extends Controller
{
    public function getMyOvertime(Request $request)
    {
        $employeeId = Auth::user()->employee_id;

        $overtimes = EmployeeAttendance::with(['detail', 'employee', 'masterWorkhour', 'claimOvertime'])
            ->where('employee_id', $employeeId)
            ->whereHas('detail', function ($query) {
                $query->where('status_check_out', 'overtime')
                    ->orWhere('status_check_in', 'overtime');
            })
            ->whereDoesntHave('claimOvertime')
            ->whereNull('source')
            ->orderBy('date', 'desc')
            ->get();

        $overtimes->transform(function ($attendance) {
            $detail = $attendance->detail;
            $totalMinutes = 0;
            $isHoliday = $attendance->holiday_id !== null || !$attendance->work_in || !$attendance->work_out;

            if ($isHoliday) {
                if ($detail && $detail->check_in && $detail->check_out) {
                    $start = Carbon::parse($detail->check_in);
                    $end = Carbon::parse($detail->check_out);
                    if ($end->lt($start)) $end->addDay();
                    $totalMinutes = $start->diffInMinutes($end);
                }
            } else {
                if ($detail && $detail->status_check_in === 'overtime' && $detail->check_in && $attendance->work_in) {
                    $checkIn = Carbon::parse($detail->check_in);
                    $workIn = Carbon::parse($attendance->work_in);
                    $workIn->setDate($checkIn->year, $checkIn->month, $checkIn->day);
                    if ($checkIn->lt($workIn)) {
                        $totalMinutes += $workIn->diffInMinutes($checkIn);
                    }
                }
                if ($detail && $detail->status_check_out === 'overtime' && $detail->check_out && $attendance->work_out) {
                    $checkOut = Carbon::parse($detail->check_out);
                    $workOut = Carbon::parse($attendance->work_out);
                    $workOut->setDate($checkOut->year, $checkOut->month, $checkOut->day);
                    if ($checkOut->gt($workOut)) {
                        $totalMinutes += $checkOut->diffInMinutes($workOut);
                    }
                }
            }

            $attendance->total_overtime_minutes = $totalMinutes;
            return $attendance;
        });

        if ($overtimes->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Data overtime belum di-claim tidak ditemukan', 'data' => []], 404);
        }

        return response()->json(['success' => true, 'data' => $overtimes]);
    }

    public function getMyClaimOvertime(Request $request)
    {
        $employeeId = Auth::user()->employee_id;

        $claims = ClaimOvertime::with([
                'employee',
                'employeeAttendance',
                'employeeAttendance.detail',
                'approvals',
                'approvals.employee',
            ])
            ->where('employee_id', $employeeId)
            ->whereIn('status', ['waiting', 'pending'])
            ->orderBy('overtime_date', 'desc')
            ->get();

        if ($claims->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Data claim overtime tidak ditemukan',
                'data' => []
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Berhasil mengambil data claim overtime',
            'data' => $claims
        ]);
    }

    public function getNeedApprovalOvertime(Request $request)
    {
        $employee = Auth::user()->employee;

        $approvals = ClaimApproval::with([
                'claimOvertime',
                'claimOvertime.employee',
                'claimOvertime.approvals',
                'claimOvertime.employeeAttendance',
                'claimOvertime.approvals.employee',
                'claimOvertime.employeeAttendance.detail',
                'claimOvertime.employeeAttendance.masterWorkhour',
                'claimOvertime.employeeAttendance.groupEmployeeWorkhour',
            ])
            ->where('employee_id', $employee->id)
            ->where('status', 'waiting')
            ->latest()
            ->get();

        if ($approvals->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada overtime yang membutuhkan approval',
                'data' => []
            ], 404);
        }

        return response()->json([
            'success' => true,
            // 'message' => 'Berhasil mengambil data approval overtime',
            'data' => $approvals
        ]);
    }

    public function getApprovalHistoryOvertime(Request $request)
    {
        $employee = Auth::user()->employee;

        $histories = ClaimApproval::with([
                'claimOvertime',
                'claimOvertime.employee',
                'claimOvertime.approvals',
                'claimOvertime.employeeAttendance',
                'claimOvertime.approvals.employee',
                'claimOvertime.employeeAttendance.detail',
                'claimOvertime.employeeAttendance.masterWorkhour',
                'claimOvertime.employeeAttendance.groupEmployeeWorkhour',
            ])
            ->where('employee_id', $employee->id)

            // history approval
            ->whereIn('status', ['approved', 'rejected'])

            ->latest()
            ->get();

        if ($histories->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'History approval overtime tidak ditemukan',
                'data' => []
            ], 404);
        }

        return response()->json([
            'success' => true,
            // 'message' => 'Berhasil mengambil history approval overtime',
            'data' => $histories
        ]);
    }

    public function getMyClaimOvertimeHistory(Request $request)
    {
        $employee = Auth::user()->employee;

        $claims = ClaimOvertime::with([
                'employee',
                'employeeAttendance',
                'employeeAttendance.detail',
                'employeeAttendance.masterWorkhour',
                'employeeAttendance.groupEmployeeWorkhour',
                'approvals',
                'approvals.employee'
            ])
            ->where('employee_id', $employee->id)
            ->whereIn('status', ['approved', 'rejected'])
            ->latest()
            ->get();

        if ($claims->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'History claim overtime kosong',
                'data' => []
            ], 200);
        }

        return response()->json([
            'success' => true,
            // 'message' => 'Berhasil mengambil history claim overtime',
            'data' => $claims
        ]);
    }

    public function claimOvertime(Request $request)
    {
        DB::beginTransaction();

        try {

            $request->validate([
                'employee_attendance_id' => 'required|exists:employee_attendances,id',
                'reason' => 'required|string',
                'agreed_work_start' => 'nullable',
                'agreed_work_end' => 'nullable',
            ]);

            $employeeId = Auth::user()->employee_id;

            $attendance = EmployeeAttendance::with([
                    'detail',
                    'employee.lineApprovals'
                ])
                ->findOrFail($request->employee_attendance_id);

            // security check
            if ($attendance->employee_id != $employeeId) {
                throw new \Exception('Unauthorized access');
            }

            $detail = $attendance->detail;
            $employee = $attendance->employee;

            if (!$detail) {
                throw new \Exception('Detail attendance tidak ditemukan');
            }

            // cek claim existing
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
                $attendance->holiday_id !== null
                || !$attendance->work_in
                || !$attendance->work_out;

            /**
             * HOLIDAY
             */
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

                /**
                 * BEFORE WORK
                 */
                if ($detail->status_check_in === 'overtime' && $detail->check_in) {
                    $sources[] = 'bf';
                    $checkIn = Carbon::parse($detail->check_in);
                    $workIn = Carbon::parse($attendance->work_in);
                    // set tanggal workIn sama dengan checkIn
                    $workIn->setDate($checkIn->year, $checkIn->month, $checkIn->day);
                    if ($checkIn->lt($workIn)) {
                        $minutes = $workIn->diffInMinutes($checkIn);
                        $totalMinutes += $minutes;
                        if (!$actualStart) {
                            $actualStart = $detail->check_in;
                        }
                        $actualEnd = $attendance->work_in;
                    }
                }

                /**
                 * AFTER WORK
                 */
                if ($detail->status_check_out === 'overtime' && $detail->check_out) {
                    $sources[] = 'af';
                    $checkOut = Carbon::parse($detail->check_out);
                    $workOut = Carbon::parse($attendance->work_out);
                    // set tanggal workOut sama dengan checkOut
                    $workOut->setDate($checkOut->year, $checkOut->month, $checkOut->day);
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
                throw new \Exception('Total overtime tidak valid');
            }

            $source = implode('|', $sources);

            /**
             * CREATE CLAIM
             */
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
                'created_by' => Auth::user()->name,
                'updated_by' => Auth::user()->name,
            ]);

            /**
             * LINE APPROVAL
             */
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

            /**
             * CREATE APPROVAL
             */
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

            $user = Auth::user();

            Log::create([
                'user_id'    => $user->id,
                'ip_address' => $request->ip(),
                'action'     => 'insert',
                'description'=> "{$employee->fullname} mengajukan claim overtime"
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Claim overtime berhasil dibuat',
                'data' => $claim
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function approveClaimOvertime(Request $request, $id)
    {
        $request->validate([
            'reason' => 'nullable|string',
        ]);

        return $this->handleApproval(
            [$id],
            'approved',
            $request->reason
        );
    }

    public function rejectClaimOvertime(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string',
        ]);

        return $this->handleApproval(
            [$id],
            'rejected',
            $request->reason
        );
    }

    private function handleApproval(array $ids, string $action, ?string $reason = null)
    {
        DB::beginTransaction();

        try {
            $user = Auth::user();
            $employee = Auth::user()->employee;

            $approvals = ClaimApproval::with([
                    'claimOvertime',
                    'claimOvertime.employee',
                    'claimOvertime.employeeAttendance',
                    'employee.user'
                ])
                ->whereIn('id', $ids)

                // IMPORTANT
                ->where('employee_id', $employee->id)

                ->lockForUpdate()
                ->get();

            if ($approvals->isEmpty()) {
                throw new \Exception(
                    'Data approval tidak ditemukan'
                );
            }

            foreach ($approvals as $approval) {

                if ($approval->status !== 'waiting') {
                    continue;
                }

                $claimOvertime = $approval->claimOvertime;

                $approval->update([
                    'status'        => $action,
                    'approved_at'   => now(),
                    'reason_reject' => $reason,
                ]);

                // if ($action === 'rejected') {
                //     $updateData['reason_reject'] = $reason;
                // } else {
                //     if ($reason) {
                //         $updateData['reason_reject'] = $reason;
                //     }
                // }

                // $approval->update($updateData);

                Log::create([
                    'user_id'    => $user->id,
                    'ip_address' => request()->ip(),
                    'action'     => $action,
                    'description'=> "{$employee->fullname} {$action} claim overtime milik {$claimOvertime->employee->fullname}"
                ]);

                if ($action === 'rejected') {

                    $claimOvertime->update([
                        'status'     => 'rejected',
                        'updated_by' => Auth::user()->name,
                    ]);

                    continue;
                }

                $nextApproval = ClaimApproval::where(
                        'claim_overtime_id',
                        $claimOvertime->id
                    )
                    ->where('level', '>', $approval->level)
                    ->orderBy('level')
                    ->first();

                if ($nextApproval) {

                    if ($nextApproval->status !== 'waiting') {

                        $nextApproval->update([
                            'status' => 'waiting'
                        ]);
                    }

                } else {

                    $claimOvertime->update([
                        'status'     => 'approved',
                        'updated_by' => Auth::user()->name,
                    ]);

                    if ($claimOvertime->employeeAttendance) {

                        $sourceLabel = match($claimOvertime->source) {
                            'bf' => 'Lembur Sebelum Jam Kerja',
                            'af' => 'Lembur Setelah Jam Kerja',
                            'hl' => 'Lembur Hari Libur',
                            'bf|af' => 'Lembur Sebelum & Setelah Jam Kerja',
                            default => '-'
                        };

                        $claimOvertime->employeeAttendance->update([
                            'attendance_status' => 'overtime',
                            'source' => $sourceLabel,
                        ]);
                    }
                }
            }

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
