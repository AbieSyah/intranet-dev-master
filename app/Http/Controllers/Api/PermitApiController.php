<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use App\Models\Attendance\AttendancePermit;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Master\LineApproval;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Notifications\AttendancePermitNotification;
use Carbon\Carbon;
use App\Models\Attendance\EmployeeAttendance;

class PermitApiController extends Controller
{
    /**
     * Pengajuan izin milik user sendiri yang masih pending (status waiting)
     */
    public function myPermits()
    {
        $user = Auth::user();

        if (!$user || !$user->employee) {
            return response()->json(['message' => 'User tidak terhubung ke data karyawan'], 404);
        }

        $permits = AttendancePermit::where('employee_id', $user->employee->id)
            ->where('status', 'waiting')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($permits);
    }

    /**
     * Riwayat lengkap pengajuan izin milik user sendiri (semua status)
     */
    public function myPermitsHistory()
    {
        $user = Auth::user();

        if (!$user || !$user->employee) {
            return response()->json(['message' => 'User tidak terhubung ke data karyawan'], 404);
        }

        $permits = AttendancePermit::where('employee_id', $user->employee->id)
            ->where('status', '!=', 'waiting')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($permits);
    }

    /**
     * Pengajuan izin yang perlu disetujui oleh user (status waiting, user sebagai approver)
     */
    public function getPendingApprovals()
    {
        $user = Auth::user();
        $employee = $user->employee;
        $lineApprovals = LineApproval::where('approval_type', 'Attendance Permit')
            ->where(function ($query) use ($employee) {
                $query->where('approve_1', $employee->id);
                // jika ada level approver lain, tambahkan:
                // ->orWhere('approve_2', $employee->id)
                // ->orWhere('approve_3', $employee->id);
            })
            ->with('employees')
            ->get();

        if ($lineApprovals->isEmpty()) {
            return collect();
        }
        $employeeIds = $lineApprovals->flatMap(fn($line) => $line->employees->pluck('id'))->unique()->toArray();

        $pendingPermits = AttendancePermit::whereIn('employee_id', $employeeIds)
            ->where('status', 'waiting')
            ->orderBy('created_at', 'desc')
            ->get();

        return $pendingPermits;
    }

    /**
     * Riwayat pengajuan izin yang sudah disetujui/ditolak oleh user (sebagai approver)
     */
    public function getApprovalHistory()
    {
        $user = Auth::user();
        $employee = $user->employee;

        // Cari semua line approval di mana user menjadi approver (level berapa pun)
        $lineApprovals = LineApproval::where('approval_type', 'Attendance Permit')
            ->where(function ($query) use ($employee) {
                $query->where('approve_1', $employee->id)
                    ->orWhere('approve_2', $employee->id)
                    ->orWhere('approve_3', $employee->id);
                // tambahkan jika level sampai 8
            })
            ->with('employees')
            ->get();

        if ($lineApprovals->isEmpty()) {
            return response()->json([]);
        }

        // Kumpulkan semua id karyawan bawahan yang terkait line approval tersebut
        $employeeIds = $lineApprovals
            ->flatMap(fn($line) => $line->employees->pluck('id'))
            ->unique()
            ->toArray();

        // Ambil permit dengan status approved atau rejected dari bawahan tersebut
        $historyPermits = AttendancePermit::whereIn('employee_id', $employeeIds)
            ->whereIn('status', ['approved', 'rejected'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return response()->json($historyPermits);
    }

    public function storePermit(Request $request)
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
                'type' => 'required|string',
                'start_date' => 'required|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
                'start_time' => 'nullable|date_format:H:i',
                'end_time' => 'nullable|date_format:H:i',
                'reason' => 'nullable|string',
                'attachment' => 'nullable|file|max:2048'
            ]);

            $startDate = Carbon::parse($request->start_date);
            $endDate = $request->end_date
                ? Carbon::parse($request->end_date)
                : $startDate;

            /**
             * =========================
             * VALIDASI OVERLAP IZIN
             * =========================
             */
            $existingPermit = AttendancePermit::where('employee_id', $employee->id)
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
                if ($existingPermit) {
                    $rangeDate = '('
                        . Carbon::parse($existingPermit->start_date)->format('d M Y')
                        . ' - '
                        . Carbon::parse($existingPermit->end_date)->format('d M Y')
                        . ')';
                    if ($existingPermit->status === 'waiting') {
                        throw new \Exception(
                            'Anda sudah membuat pengajuan izin pada rentang tanggal tersebut '
                            . $rangeDate .
                            ' dan masih menunggu approval. Harap bersabar.'
                        );
                    }
                    if ($existingPermit->status === 'approved') {
                        throw new \Exception(
                            'Anda sudah memiliki Izin yang telah disetujui pada rentang tanggal tersebut '
                            . $rangeDate
                        );
                    }
                }

            /**
             * =========================
             * VALIDASI ATTENDANCE
             * =========================
             */
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


            /**
             * =========================
             * LINE APPROVAL
             * =========================
             */
            $lineApproval = $employee->lineApprovals()
                ->where('approval_type', 'Attendance Permit')
                ->first();

            $approver = $lineApproval?->approve_1;

            if (!$approver) {
                throw new \Exception('Approver tidak ditemukan');
            }

            /**
             * =========================
             * WORKHOUR
             * =========================
             */
            $workhour = $this->getWorkHourByDate($employee, $startDate);

            if (!$workhour) {
                throw new \Exception('Workhour tidak ditemukan pada tanggal tersebut');
            }

            /**
             * =========================
             * UPLOAD FILE
             * =========================
             */
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $attachmentPath = $request->file('attachment')
                    ->store('attendance_permits', 'public');
            }

            /**
             * =========================
             * CREATE PERMIT
             * =========================
             */
            $token = (string) Str::uuid();

            $permit = AttendancePermit::create([
                'employee_id'   => $employee->id,
                'nik'           => $employee->nik,
                'employee_name' => $employee->fullname,
                'position'      => $employee->position->nama ?? null,
                'area'          => $employee->area->name ?? null,
                'department'    => $employee->department->name ?? null,

                'type'          => $request->type,
                'reason'        => $request->reason,

                'start_date'    => $startDate,
                'end_date'      => $endDate,

                'start_time'    => $request->start_time,
                'end_time'      => $request->end_time,

                'work_in'       => $workhour['work_in'] ?? null,
                'work_out'      => $workhour['work_out'] ?? null,

                'attachment'    => $attachmentPath,

                'status'        => 'waiting',
                'approval_token'=> $token,
                'created_by'    => $user->name,
            ]);

            /**
             * =========================
             * NOTIFICATION
             * =========================
             */
            $approverEmployee = Employee::find($lineApproval->approve_1);

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
                    'subject'  => 'Permintaan Izin',
                    'lines' => [
                        'Ada permintaan "' . $typeLabel . '"',
                        '',
                        'Nama Karyawan : ' . $employee->fullname,
                        'Alasan        : ' . ($request->reason ?? '-'),
                        'Tanggal       : ' .
                            $startDate->format('d M Y') .
                            ($endDate->ne($startDate)
                                ? ' s/d ' . $endDate->format('d M Y')
                                : ''),
                        'Jam Masuk     : ' . ($request->start_time ?? '-'),
                        'Jam Keluar    : ' . ($request->end_time ?? '-'),
                    ],
                    'actionText' => 'Approve Sekarang',
                    'actionURL'  => url('/api/mobile/permit-approval/' . $token),
                    'thanks'     => 'Terimakasih'
                ];

                $approverEmployee->user
                    ->notify(new AttendancePermitNotification($details));
            }

            /**
             * =========================
             * LOG
             * =========================
             */
            Log::create([
                'user_id'    => $user->id,
                'ip_address' => $request->ip(),
                'action'     => 'insert',
                'description'=> "{$employee->fullname} mengajukan izin {$request->type}"
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan izin berhasil',
                'data'    => $permit
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data'    => $permit ?? null
            ], 500);
        }
    }

    public function reject(Request $request, $id)
    {
        try {

            $permit = AttendancePermit::findOrFail($id);
            $user = auth()->user();

            $permit->update([
                'status' => 'rejected',
                'reason_reject' => $request->reason,
                'approved_by_name' => $user->employee->fullname,
                'approved_by_position' => $user->employee->position->nama ?? null,
                'approved_by_at' => now(),
            ]);

            Log::create([
                'user_id'    => $user->id,
                'ip_address' => $request->ip(),
                'action'     => 'insert',
                'description'=> "{$user->employee->fullname} menolak izin {$request->type} atas nama {$permit->employee_name}"
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil di-reject'
            ]);

        } catch (\Throwable $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function approve(Request $request, $id)
    {
        DB::beginTransaction();

        try {

            $permit = AttendancePermit::findOrFail($id);

            // ❌ hindari double approve
            if ($permit->status === 'approved') {
                throw new \Exception('Data sudah di-approve sebelumnya');
            }

            $user = auth()->user();
            $employee = $permit->employee;

            // 🔥 update status
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

            Log::create([
                'user_id'    => $user->id,
                'ip_address' => $request->ip(),
                'action'     => 'insert',
                'description'=> "{$user->employee->fullname} menyetujui izin {$request->type} atas nama {$permit->employee_name}"
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Berhasil di-approve & attendance dibuat'
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
}
