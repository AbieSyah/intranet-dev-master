<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance\LeaveBalance;
use App\Models\Attendance\LeaveRequest;
use App\Models\Attendance\LeaveRequestApproval;
use App\Models\Attendance\LeaveSetting;
use App\Models\Attendance\AttendanceCalendar;
USE App\Models\Attendance\EmployeeAttendance;
use App\Notifications\AttendancePermitNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Employee;
use App\Models\Master\LineApproval;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class LeaveApiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee tidak ditemukan'
            ], 404);
        }

        $leaveBalances = LeaveBalance::with('leaveType')
            ->where('employee_id', $user->employee->id)
            ->get();

        $leaveRequests = LeaveRequest::where('employee_id', $user->employee->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $leaveBalances,
            'requests' => $leaveRequests
        ]);
    }

    public function myRequests(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee tidak ditemukan'
            ], 404);
        }

        $data = LeaveRequest::query()
            ->leftJoin('leave_settings', 'leave_requests.leave_type_id', '=', 'leave_settings.id')
            ->where('leave_requests.employee_id', $user->employee->id)
            ->with('approvals')
            ->select(
                'leave_requests.*',
                // 'leave_settings.type as leave_type_name',
                'leave_settings.description as leave_description',
                'leave_settings.number_of_days'
            )
            ->latest('leave_requests.created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function myApprovals(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee tidak ditemukan'
            ], 404);
        }

        $employeeId = $user->employee->id;

        $data = LeaveRequestApproval::with([
            'leaveRequest' => function ($q) {
                $q->leftJoin('leave_settings', 'leave_requests.leave_type_id', '=', 'leave_settings.id')
                ->select(
                    'leave_requests.*',
                    'leave_settings.description as leave_description',
                    'leave_settings.number_of_days'
                );
            },
            'approver:id,fullname',
            'leaveRequest.approvals'
        ])
        ->where('approver_id', $employeeId)
        ->where('status', 'waiting')
        ->latest()
        ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function myApprovalsHistory(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee tidak ditemukan'
            ], 404);
        }

        $employeeId = $user->employee->id;

        $data = LeaveRequestApproval::with([
            'leaveRequest' => function ($q) {
                $q->leftJoin('leave_settings', 'leave_requests.leave_type_id', '=', 'leave_settings.id')
                ->select(
                    'leave_requests.*',
                    'leave_settings.description as leave_description',
                    'leave_settings.number_of_days'
                );
            },
            'approver:id,fullname',
            'leaveRequest.approvals'
        ])
        ->where('approver_id', $employeeId)
        ->whereIn('status', ['approved', 'rejected'])
        ->latest()
        ->get();

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function approve(Request $request)
    {
        DB::beginTransaction();

        try {
            $user = $request->user();

            if (!$user || !$user->employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee tidak ditemukan'
                ], 404);
            }

            $request->validate([
                'approval_id' => 'required|exists:leave_approvals,id',
                'action'      => 'required|in:approved,rejected',
                'reason'      => 'nullable|string'
            ]);

            $approval = LeaveRequestApproval::with([
                'leaveRequest',
                'leaveRequest.approvals'
            ])->findOrFail($request->approval_id);

            // if ($approval->approver_id != $user->employee->id) {
            //     throw new \Exception('Kamu tidak berhak approve data ini');
            // }

            if ($approval->status !== 'waiting') {
                throw new \Exception('Approval sudah diproses');
            }

            $approval->update([
                'status'        => $request->action,
                'approved_at'   => now(),
                'reason_reject' => $request->reason
            ]);

            $leaveRequest = $approval->leaveRequest;

            if ($request->action === 'rejected') {
                $leaveRequest->update([
                    'status'     => 'rejected',
                    'updated_by' => $user->name
                ]);

                DB::commit();

                Log::create([
                    'user_id'    => $user->id,
                    'ip_address' => $request->ip(),
                    'action'     => 'reject',
                    'description'=> "{$user->employee->fullname} menolak pengajuan cuti {$leaveRequest->type} : {$leaveRequest->employee_name}"
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Pengajuan cuti ditolak'
                ]);
            }


            $nextApproval = LeaveRequestApproval::where('leave_request_id', $leaveRequest->id)
                ->where('level', '>', $approval->level)
                ->orderBy('level')
                ->first();

            $nextApproval = LeaveRequestApproval::where('leave_request_id', $leaveRequest->id)
            ->where('level', '>', $approval->level)
            ->orderBy('level')
            ->first();

            if ($nextApproval) {
                $nextApproval->update(['status' => 'waiting']);
                // Kirim email ke nextApproval (isi details)
                if ($nextApproval->approver?->user) {
                    $details = [
                        'greeting' => 'Hi ' . $nextApproval->approver_name,
                        'subject' => 'Permintaan Cuti Perlu Persetujuan',
                        'lines' => [
                            'Karyawan: ' . $leaveRequest->employee_name,
                            'Jenis: ' . $leaveRequest->type,
                            'Tanggal: ' . Carbon::parse($leaveRequest->start_date)->format('d M Y') . ' s/d ' . Carbon::parse($leaveRequest->end_date)->format('d M Y'),
                            'Durasi: ' . $leaveRequest->total_days . ' hari',
                        ],
                        'actionText' => 'Setujui Sekarang',
                        'actionURL' => url('/api/mobile/leave-approval/' . $nextApproval->approval_token),
                        'thanks' => 'Terimakasih'
                    ];
                    $nextApproval->approver->user->notify(new AttendancePermitNotification($details));
                }
            } else {
                // Final approval
                $leaveRequest->update([
                    'status'     => 'approved',
                    'updated_by' => $user->name
                ]);

                if ($leaveRequest->type === 'pribadi') {
                    $leaveBalances = LeaveBalance::where('employee_id', $leaveRequest->employee_id)
                        // ->where('leave_type_id', $leaveRequest->leave_type_id)
                        ->where('type', 'pribadi')
                        ->where('remaining_days', '>', 0)
                        ->whereDate('valid_from', '<=', $leaveRequest->start_date)
                        ->whereDate('valid_to', '>=', $leaveRequest->start_date)
                        ->orderBy('valid_to', 'asc')
                        ->lockForUpdate()
                        ->get();

                    // fallback jika tidak ada saldo aktif
                    if ($leaveBalances->isEmpty()) {
                        $leaveBalances = LeaveBalance::where('employee_id', $leaveRequest->employee_id)
                            // ->where('leave_type_id', $leaveRequest->leave_type_id)
                            ->where('type', 'pribadi')
                            ->where('remaining_days', '>', 0)
                            ->whereDate('valid_from', '>', $leaveRequest->start_date)
                            ->orderBy('valid_from', 'asc')
                            ->lockForUpdate()
                            ->get();
                    }
                    if ($leaveBalances->isNotEmpty()) {
                        $leaveBalance = $leaveBalances->first();
                        $leaveBalance->decrement('remaining_days', $leaveRequest->total_days);
                    }
                }

                $employee = Employee::find($leaveRequest->employee_id);
                if ($employee) {
                    $this->generateAttendance($employee, $leaveRequest->start_date, $leaveRequest->end_date, $leaveRequest->type);
                }
            }

            DB::commit();

            Log::create([
                'user_id'    => $user->id,
                'ip_address' => $request->ip(),
                'action'     => 'approve', // atau 'reject'
                'description'=> "{$user->employee->fullname} menyetujui pengajuan cuti {$leaveRequest->type} : {$leaveRequest->employee_name}"
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Approval berhasil diproses',
                'data'    => $approval->load('leaveRequest.approvals')
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
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

    private function getActiveWorkDays(Employee $employee): array
    {
        $groupEmployee = $employee->groupEmployees->first();
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

    public function myLineApprovals(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->employee) {
            return response()->json(['success' => false, 'message' => 'Employee tidak ditemukan'], 404);
        }

        $employee = $user->employee;
        $lineApproval = $this->getLineApprovalForEmployee($employee, 'Attendance Leave');

        if (!$lineApproval) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada line approval yang cocok'
            ], 404);
        }

        $lineApproval->load([
            'approve1', 'approve2', 'approve3', 'approve4',
            'approve5', 'approve6', 'approve7', 'approve8', 'draft'
        ]);

        return response()->json([
            'success' => true,
            'data' => $lineApproval
        ]);
    }

    public function getLeaveSettings()
    {
        try {
            $settings = LeaveSetting::select('id', 'type', 'description', 'number_of_days')
                // ->where('type', 'pribadi')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $settings
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data setting cuti: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getLineApprovalForEmployee(Employee $employee, string $approvalType): ?LineApproval
    {
        $query = LineApproval::where('approval_type', $approvalType);

        $query->where(function ($q) use ($employee) {
            $q->where('department_id', $employee->department_id)
              ->orWhereNull('department_id');
        });

        $query->where(function ($q) use ($employee) {
            $q->where('area_id', $employee->area_id)
              ->orWhereNull('area_id');
        });

        $query->where(function ($q) use ($employee) {
            $q->where('section_id', $employee->section_id)
              ->orWhereNull('section_id');
        });

        $query->where(function ($q) use ($employee) {
            $q->where('position_id', $employee->position_id)
              ->orWhereNull('position_id');
        });

        $query->where(function ($q) use ($employee) {
            $q->where('building_id', $employee->building_id)
              ->orWhereNull('building_id');
        });

        $lineApprovals = $query->get();

        $bestMatch = $lineApprovals->sortByDesc(function ($item) {
            $score = 0;
            if (!is_null($item->department_id)) $score++;
            if (!is_null($item->area_id)) $score++;
            if (!is_null($item->section_id)) $score++;
            if (!is_null($item->position_id)) $score++;
            if (!is_null($item->building_id)) $score++;
            return $score;
        })->first();

        return $bestMatch;
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $user = $request->user();
            $employee = $user->employee;

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee tidak ditemukan'
                ], 404);
            }

            $request->validate([
                'leave_type_id' => 'required|exists:leave_settings,id',
                'start_date'    => 'required|date',
                'end_date'      => 'required|date|after_or_equal:start_date',
                'notes'         => 'nullable|string',
                'attachment'    => 'nullable|file|max:2048'
            ]);

            $leaveSetting = LeaveSetting::findOrFail($request->leave_type_id);
            $startDate = Carbon::parse($request->start_date);
            $endDate   = Carbon::parse($request->end_date);

            // $totalDays = $startDate->diffInDaysFiltered(function ($date) {
            //     return !$date->isWeekend();
            // }, $endDate) + 1;

            // if ($totalDays <= 0) {
            //     throw new \Exception('Jumlah hari tidak valid');
            // }

            if ($leaveSetting->type === 'pribadi') {
                // Gunakan calculateDaysOnly seperti web
                $daysData = $this->calculateDaysOnly($employee, $startDate, $endDate);
                $totalDays = $daysData['days'];
            } else {
                // Normatif: hitung berdasarkan jumlah hari kerja
                $normativeData = $this->calculateNormativeLeaveRange($employee, $startDate, $leaveSetting->number_of_days);
                $startDate = $normativeData['start_date'];
                $endDate   = $normativeData['end_date'];
                $totalDays = $normativeData['total_days'];
            }

            if ($totalDays <= 0) {
                throw new \Exception('Jumlah hari cuti tidak valid');
            }

            // 1. Check overlapping leave request (same logic as web)
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
                $range = Carbon::parse($existingLeave->start_date)->format('d M Y') . ' - ' . Carbon::parse($existingLeave->end_date)->format('d M Y');
                throw new \Exception("Anda sudah memiliki pengajuan cuti ($range) dengan status {$existingLeave->status}");
            }

            // 2. Check attendance record conflict
            $conflict = EmployeeAttendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->whereNotNull('source')
                ->first();

            if ($conflict) {
                throw new \Exception(
                    "Anda sudah memiliki data kehadiran pada tanggal " .
                    Carbon::parse($conflict->date)->format('d M Y') .
                    " dengan status {$conflict->source}"
                );
            }

            if ($leaveSetting->type === 'pribadi') {

                // Cari saldo yang aktif sesuai tanggal cuti
                $leaveBalances = LeaveBalance::where('employee_id', $employee->id)
                    // ->where('leave_type_id', $leaveSetting->id)
                    ->where('type', 'pribadi')
                    ->where('remaining_days', '>', 0)
                    ->whereDate('valid_from', '<=', $startDate)
                    ->whereDate('valid_to', '>=', $startDate)
                    ->orderBy('valid_to', 'asc')
                    ->get();

                // Kalau tidak ada, ambil saldo berikutnya
                if ($leaveBalances->isEmpty()) {
                    $leaveBalances = LeaveBalance::where('employee_id', $employee->id)
                        // ->where('leave_type_id', $leaveSetting->id)
                        ->where('type', 'pribadi')
                        ->where('remaining_days', '>', 0)
                        ->whereDate('valid_from', '>', $startDate)
                        ->orderBy('valid_from', 'asc')
                        ->get();
                }

                $totalAvailable = $leaveBalances->sum('remaining_days');

                if ($totalAvailable < $totalDays) {
                    throw new \Exception(
                        "Saldo cuti tidak mencukupi. Tersedia {$totalAvailable} hari"
                    );
                }
            }

            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')
                    ->store('leave_attachments', 'public');
            }

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

            $lineApproval = $this->getLineApprovalForEmployee($employee, 'Attendance Leave');

            if (!$lineApproval) {
                throw new \Exception('Approval line tidak ditemukan untuk employee ini');
            }

            for ($i = 1; $i <= 8; $i++) {
                $approverId = $lineApproval->{'approve_'.$i};

                if ($approverId) {
                    $approver = Employee::find($approverId);
                    if (!$approver) continue;

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

            $firstApproval = $leaveRequest->approvals()->where('level', 1)->first();

            if ($firstApproval && $firstApproval->approver?->user) {
                $details = [
                    'greeting' => 'Hi ' . $firstApproval->approver_name,
                    'subject' => 'Permintaan Cuti Baru',
                    'lines' => [
                        'Karyawan: ' . $employee->fullname,
                        'Jenis: ' . $leaveSetting->type,
                        'Tanggal: ' . $startDate->format('d M Y') . ' s/d ' . $endDate->format('d M Y'),
                        'Durasi: ' . $totalDays . ' hari',
                    ],
                    'actionText' => 'Lihat Pengajuan',
                    'actionURL' => url('/api/mobile/leave-approval/' . $firstApproval->approval_token), // sesuaikan dengan route frontend
                    'thanks' => 'Terimakasih'
                ];

                $firstApproval->approver->user->notify(new AttendancePermitNotification($details));
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
                'success' => true,
                'message' => 'Pengajuan cuti berhasil',
                'data'    => $leaveRequest->load('approvals')
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function calculatePersonalLeave(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->employee) {
            return response()->json(['success' => false, 'message' => 'Employee not found'], 404);
        }

        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $employee = $user->employee;
        $start = Carbon::parse($request->start_date);
        $end   = Carbon::parse($request->end_date);

        $result = $this->calculateDaysOnly($employee, $start, $end);

        return response()->json([
            'success' => true,
            'data' => [
                'total_days' => $result['days'],
                'excluded'   => $result['excluded'],
            ]
        ]);
    }

    public function calculateNormativeLeave(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->employee) {
            return response()->json(['success' => false, 'message' => 'Employee not found'], 404);
        }

        $request->validate([
            'leave_setting_id' => 'required|exists:leave_settings,id',
            'start_date'       => 'required|date',
        ]);

        $employee = $user->employee;
        $leaveSetting = LeaveSetting::findOrFail($request->leave_setting_id);

         $result = $this->calculateNormativeLeaveRange($employee, $request->start_date, $leaveSetting->number_of_days);
        return response()->json([
            'success' => true,
            'data' => [
                'start_date' => $result['start_date']->toDateString(),
                'end_date'   => $result['end_date']->toDateString(),
                'total_days' => $result['total_days'],
                'excluded'   => $result['excluded'],
            ]
        ]);
    }

    private function calculateDaysOnly($employee, $startDate, $endDate): array
    {
        $start = Carbon::parse($startDate);
        $end   = Carbon::parse($endDate);

        $days = 0;
        $excluded = [];

        $workingDays = $this->getActiveWorkDays($employee);

        $holidays = AttendanceCalendar::whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->where('is_active', true)
            ->whereIn('type', ['national', 'company', 'cultural'])
            ->when($employee && $employee->area_id == 1, fn($q) => $q->where('is_hq', 1), fn($q) => $q->where('is_hq', 0))
            ->pluck('name', 'date')
            ->mapWithKeys(fn($name, $date) => [Carbon::parse($date)->toDateString() => $name])
            ->toArray();

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $currentDate = $date->toDateString();
            $dayName = strtolower($date->format('l'));

            if (array_key_exists($currentDate, $holidays)) {
                $excluded[] = ['date' => $currentDate, 'type' => $holidays[$currentDate]];
                continue;
            }

            if (!empty($workingDays)) {
                if (in_array($dayName, $workingDays, true)) {
                    $days++;
                } else {
                    $excluded[] = ['date' => $currentDate, 'type' => 'Non-working day'];
                }
                continue;
            }

            if ($date->isWeekend()) {
                $excluded[] = ['date' => $currentDate, 'type' => 'Weekend'];
                continue;
            }

            $days++;
        }

        return ['days' => $days, 'excluded' => $excluded];
    }

    private function calculateNormativeLeaveRange($employee, $startDate, $daysNeeded): array
    {
        $currentDate = Carbon::parse($startDate);
        $countedDays = 0;
        $excluded = [];

        $workingDays = $this->getActiveWorkDays($employee);
        $maxDate = $currentDate->copy()->addDays($daysNeeded + 30);

        $holidays = AttendanceCalendar::whereBetween('date', [$currentDate->toDateString(), $maxDate->toDateString()])
            ->where('is_active', true)
            ->get()
            ->keyBy(fn($item) => Carbon::parse($item->date)->toDateString());

        while ($countedDays < $daysNeeded) {
            $dayName = strtolower($currentDate->format('l'));
            $dateStr = $currentDate->toDateString();

            $holiday = $holidays->get($dateStr);
            if ($holiday) {
                $excluded[] = ['date' => $dateStr, 'type' => $holiday->name];
            } elseif (!in_array($dayName, $workingDays)) {
                $excluded[] = ['date' => $dateStr, 'type' => 'Non-working day'];
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
            'excluded'   => $excluded,
        ];
    }
}
