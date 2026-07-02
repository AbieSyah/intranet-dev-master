<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance\EmployeeAttendance;
use App\Models\Attendance\lateHistories;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class LateApiController extends Controller
{
    public function myLate(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee data not found'
            ], 404);
        }

        $employeeId = $user->employee->id;

        $rows = DB::table('late_histories')
            ->join('employee_attendances', 'late_histories.employee_attendance_id', '=', 'employee_attendances.id')
            ->join('employees', 'employee_attendances.employee_id', '=', 'employees.id')
            ->leftJoin('employee_attendance_details', 'employee_attendances.id', '=', 'employee_attendance_details.employee_attendance_id')
            ->select(
                'late_histories.*',
                'employee_attendances.*',
                'employees.fullname as employee_name',
                'employee_attendance_details.*'
            )
            ->where('employee_attendances.employee_id', $employeeId)

            ->where(function ($query) {
                $query->where('late_histories.security_knowledge', 0)
                    ->orWhere('late_histories.hrd_knowledge', 0)
                    ->orWhere('late_histories.head_knowledge', 0);
            })

            ->orderBy('late_histories.created_at', 'desc')
            ->get();

        $lateHistories = $rows->map(function ($row) {
            return [
                'id' => $row->id,
                'employee_attendance_id' => $row->employee_attendance_id,
                'employee_name' => $row->employee_name,
                'security_knowledge' => $row->security_knowledge,
                'security_name' => $row->security_name,
                'hrd_knowledge' => $row->hrd_knowledge,
                'knowledgeby_hrdName' => $row->knowledgeby_hrdName,
                'head_knowledge' => $row->head_knowledge,
                'knowledgeby_headName' => $row->knowledgeby_headName,
                'reason' => $row->reason,
                'actual_in' => $row->actual_in,
                'approval_token' => $row->approval_token,

                'attendance' => [
                    'id' => $row->employee_attendance_id,
                    'employee_id' => $row->employee_id,
                    'position_name' => $row->position_name,
                    'area_name' => $row->area_name,
                    'department_name' => $row->department_name,
                    'group_id' => $row->group_id,
                    'master_workhour_id' => $row->master_workhour_id,
                    'work_in' => $row->work_in,
                    'work_out' => $row->work_out,
                    'date' => $row->date,
                    'attendance_status' => $row->attendance_status,
                    'source' => $row->source,

                    'detail' => [
                        'employee_attendance_id' => $row->employee_attendance_id,
                        'check_in' => $row->check_in,
                        'check_out' => $row->check_out,
                        'status_check_in' => $row->status_check_in,
                        'status_check_out' => $row->status_check_out,
                        'latlong_check_in' => $row->latlong_check_in,
                        'latlong_check_out' => $row->latlong_check_out,
                        'reason_check_in' => $row->reason_check_in,
                        'reason_check_out' => $row->reason_check_out,
                        'distance_check_in' => $row->distance_check_in,
                        'distance_check_out' => $row->distance_check_out,
                        'out_of_range_check_in' => $row->out_of_range_check_in,
                        'out_of_range_check_out' => $row->out_of_range_check_out,
                    ]
                ]
            ];
        });

        return response()->json([
            'success' => true,
            // 'message' => 'Late histories retrieved successfully',
            'data' => $lateHistories
        ]);
    }

    public function myLateHistory(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee data not found'
            ], 404);
        }

        $employeeId = $user->employee->id;

        $rows = DB::table('late_histories')
            ->join('employee_attendances', 'late_histories.employee_attendance_id', '=', 'employee_attendances.id')
            ->join('employees', 'employee_attendances.employee_id', '=', 'employees.id')
            ->leftJoin('employee_attendance_details', 'employee_attendances.id', '=', 'employee_attendance_details.employee_attendance_id')
            ->select(
                'late_histories.*',
                'employee_attendances.*',
                'employees.fullname as employee_name',  // <-- Tambahkan ini
                'employee_attendance_details.*'
            )
            ->where('employee_attendances.employee_id', $employeeId)

            ->where('late_histories.security_knowledge', 1)
            ->where('late_histories.hrd_knowledge', 1)
            ->where('late_histories.head_knowledge', 1)

            ->orderBy('late_histories.created_at', 'desc')
            ->get();

        $lateHistories = $rows->map(function ($row) {
            return [
                'id' => $row->id,
                'employee_attendance_id' => $row->employee_attendance_id,
                'employee_name' => $row->employee_name,
                'security_knowledge' => $row->security_knowledge,
                'security_name' => $row->security_name,
                'hrd_knowledge' => $row->hrd_knowledge,
                'knowledgeby_hrdName' => $row->knowledgeby_hrdName,
                'head_knowledge' => $row->head_knowledge,
                'knowledgeby_headName' => $row->knowledgeby_headName,
                'reason' => $row->reason,
                'actual_in' => $row->actual_in,
                'approval_token' => $row->approval_token,

                'attendance' => [
                    'id' => $row->employee_attendance_id,
                    'employee_id' => $row->employee_id,
                    'position_name' => $row->position_name,
                    'area_name' => $row->area_name,
                    'department_name' => $row->department_name,
                    'group_id' => $row->group_id,
                    'master_workhour_id' => $row->master_workhour_id,
                    'work_in' => $row->work_in,
                    'work_out' => $row->work_out,
                    'date' => $row->date,
                    'attendance_status' => $row->attendance_status,
                    'source' => $row->source,

                    'detail' => [
                        'employee_attendance_id' => $row->employee_attendance_id,
                        'check_in' => $row->check_in,
                        'check_out' => $row->check_out,
                        'status_check_in' => $row->status_check_in,
                        'status_check_out' => $row->status_check_out,
                        'latlong_check_in' => $row->latlong_check_in,
                        'latlong_check_out' => $row->latlong_check_out,
                        'reason_check_in' => $row->reason_check_in,
                        'reason_check_out' => $row->reason_check_out,
                        'distance_check_in' => $row->distance_check_in,
                        'distance_check_out' => $row->distance_check_out,
                        'out_of_range_check_in' => $row->out_of_range_check_in,
                        'out_of_range_check_out' => $row->out_of_range_check_out,
                    ]
                ]
            ];
        });

        return response()->json([
            'success' => true,
            // 'message' => 'Late histories retrieved successfully',
            'data' => $lateHistories
        ]);
    }

    public function myApprovals(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee data not found'
            ], 404);
        }

        $employee = $user->employee;

        // cek apakah user sebagai Head Knowledge (approve_1)
        $lineApprovals = \App\Models\Master\LineApproval::where('approval_type', 'Attendance Permit')
            ->where('approve_1', $employee->id)
            ->with('employees')
            ->get();

        if ($lineApprovals->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No approval line found',
                'data' => []
            ]);
        }

        // ambil employee bawahan
        $employeeIds = $lineApprovals
            ->flatMap(fn($line) => $line->employees->pluck('id'))
            ->unique()
            ->toArray();

        $rows = DB::table('late_histories')
            ->join('employee_attendances', 'late_histories.employee_attendance_id', '=', 'employee_attendances.id')
            ->join('employees', 'employee_attendances.employee_id', '=', 'employees.id')
            ->leftJoin('employee_attendance_details', 'employee_attendances.id', '=', 'employee_attendance_details.employee_attendance_id')
            ->select(
                'late_histories.*',
                'employee_attendances.*',
                'employees.fullname as employee_name',  // <-- Tambahkan ini
                'employee_attendance_details.*'
            )

            ->whereIn('employee_attendances.employee_id', $employeeIds)

            // logic dari website
            ->where('late_histories.security_knowledge', 1)
            ->where('late_histories.head_knowledge', 0)

            ->orderBy('late_histories.created_at', 'desc')
            ->get();

        $lateHistories = $rows->map(function ($row) {
            return [
                'id' => $row->id,
                'employee_attendance_id' => $row->employee_attendance_id,
                'employee_name' => $row->employee_name,
                'security_knowledge' => $row->security_knowledge,
                'security_name' => $row->security_name,
                'hrd_knowledge' => $row->hrd_knowledge,
                'knowledgeby_hrdName' => $row->knowledgeby_hrdName,
                'head_knowledge' => $row->head_knowledge,
                'knowledgeby_headName' => $row->knowledgeby_headName,
                'reason' => $row->reason,
                'actual_in' => $row->actual_in,
                'approval_token' => $row->approval_token,

                'attendance' => [
                    'id' => $row->employee_attendance_id,
                    'employee_id' => $row->employee_id,
                    'position_name' => $row->position_name,
                    'area_name' => $row->area_name,
                    'department_name' => $row->department_name,
                    'group_id' => $row->group_id,
                    'master_workhour_id' => $row->master_workhour_id,
                    'work_in' => $row->work_in,
                    'work_out' => $row->work_out,
                    'date' => $row->date,
                    'attendance_status' => $row->attendance_status,
                    'source' => $row->source,

                    'detail' => [
                        'employee_attendance_id' => $row->employee_attendance_id,
                        'check_in' => $row->check_in,
                        'check_out' => $row->check_out,
                        'status_check_in' => $row->status_check_in,
                        'status_check_out' => $row->status_check_out,
                        'latlong_check_in' => $row->latlong_check_in,
                        'latlong_check_out' => $row->latlong_check_out,
                        'reason_check_in' => $row->reason_check_in,
                        'reason_check_out' => $row->reason_check_out,
                        'distance_check_in' => $row->distance_check_in,
                        'distance_check_out' => $row->distance_check_out,
                        'out_of_range_check_in' => $row->out_of_range_check_in,
                        'out_of_range_check_out' => $row->out_of_range_check_out,
                    ]
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'head' => $lineApprovals->first()->head,
            'data' => $lateHistories
        ]);
    }

    public function myApprovalHistory(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee data not found'
            ], 404);
        }

        $employee = $user->employee;

        // cek apakah user sebagai Head Knowledge (approve_1)
        $lineApprovals = \App\Models\Master\LineApproval::where('approval_type', 'Attendance Permit')
            ->where('approve_1', $employee->id)
            ->with('employees')
            ->get();

        if ($lineApprovals->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No approval line found',
                'data' => []
            ]);
        }

        // ambil employee bawahan
        $employeeIds = $lineApprovals
            ->flatMap(fn($line) => $line->employees->pluck('id'))
            ->unique()
            ->toArray();

        $rows = DB::table('late_histories')
            ->join('employee_attendances', 'late_histories.employee_attendance_id', '=', 'employee_attendances.id')
            ->join('employees', 'employee_attendances.employee_id', '=', 'employees.id')
            ->leftJoin('employee_attendance_details', 'employee_attendances.id', '=', 'employee_attendance_details.employee_attendance_id')
            ->select(
                'late_histories.*',
                'employee_attendances.*',
                'employees.fullname as employee_name',
                'employee_attendance_details.*'
            )

            ->whereIn('employee_attendances.employee_id', $employeeIds)

            // logic dari website
            ->where('late_histories.security_knowledge', 1)
            ->where('late_histories.head_knowledge', 1)

            ->orderBy('late_histories.created_at', 'desc')
            ->get();

        $lateHistories = $rows->map(function ($row) {
            return [
                'id' => $row->id,
                'employee_attendance_id' => $row->employee_attendance_id,
                'employee_name' => $row->employee_name,
                'security_knowledge' => $row->security_knowledge,
                'security_name' => $row->security_name,
                'hrd_knowledge' => $row->hrd_knowledge,
                'knowledgeby_hrdName' => $row->knowledgeby_hrdName,
                'head_knowledge' => $row->head_knowledge,
                'knowledgeby_headName' => $row->knowledgeby_headName,
                'reason' => $row->reason,
                'actual_in' => $row->actual_in,
                'approval_token' => $row->approval_token,

                'attendance' => [
                    'id' => $row->employee_attendance_id,
                    'employee_id' => $row->employee_id,
                    'position_name' => $row->position_name,
                    'area_name' => $row->area_name,
                    'department_name' => $row->department_name,
                    'group_id' => $row->group_id,
                    'master_workhour_id' => $row->master_workhour_id,
                    'work_in' => $row->work_in,
                    'work_out' => $row->work_out,
                    'date' => $row->date,
                    'attendance_status' => $row->attendance_status,
                    'source' => $row->source,

                    'detail' => [
                        'employee_attendance_id' => $row->employee_attendance_id,
                        'check_in' => $row->check_in,
                        'check_out' => $row->check_out,
                        'status_check_in' => $row->status_check_in,
                        'status_check_out' => $row->status_check_out,
                        'latlong_check_in' => $row->latlong_check_in,
                        'latlong_check_out' => $row->latlong_check_out,
                        'reason_check_in' => $row->reason_check_in,
                        'reason_check_out' => $row->reason_check_out,
                        'distance_check_in' => $row->distance_check_in,
                        'distance_check_out' => $row->distance_check_out,
                        'out_of_range_check_in' => $row->out_of_range_check_in,
                        'out_of_range_check_out' => $row->out_of_range_check_out,
                    ]
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'head' => $lineApprovals->first()->head,
            'data' => $lateHistories
        ]);
    }

    public function knowledgeHead(Request $request, $id)
    {
        // $id = decrypt($request->id);

        DB::beginTransaction();

        try {
            $attendance = EmployeeAttendance::with([
                'employee',
                'lateHistories'
            ])->findOrFail($id);

            $late = lateHistories::where('employee_attendance_id', $id)->first();
            $head = Auth::user()->employee;

            if (!$late) {
                throw new \Exception('Data late tidak ditemukan untuk attendance ini');
            }

            if ($late->head_knowledge == 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan sudah disetujui sebelumnya'
                ], 400);
            }
            // ================= UPDATE SECURITY =================
            $late->update([
                'head_knowledge'        => 1,
                'knowledgeby_headName'  => $head->fullname,
            ]);
            // dd($late);
            DB::commit();

            return response()->json([
                'message' => 'Berhasil diketahui Atasan'
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
