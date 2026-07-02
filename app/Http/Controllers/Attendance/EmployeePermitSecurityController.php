<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Models\Attendance\AttendancePermit;
use App\Models\Attendance\EmployeeAttendance;
use App\Models\Attendance\lateHistories;
use App\Models\Employee;
use App\Notifications\AttendancePermitNotification;
use App\Notifications\BulkLeaveApprovalNotification;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;

class EmployeePermitSecurityController extends Controller
{
    public function index(Request $request){
        if ($request->ajax()) {

            $data = AttendancePermit::query();
            // ->where('hrd_knowledge', 1);
            // ->orderBy('hrd_knowledge', 'asc') // 0 dulu baru 1
            // ->latest(); // optional: biar yang terbaru di atas juga
            // FILTER DATE
            if ($request->filter_date_permit) {
                $data->whereDate('start_date', $request->filter_date_permit);
            }
            // FILTER TYPE
            if ($request->filter_type) {
                $data->where('type', $request->filter_type);
            }
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('workhour', function ($r) {
                    $start = $r->work_in
                        ? Carbon::parse($r->work_in)->format('H:i')
                        : '';
                    $end = $r->work_out
                        ? Carbon::parse($r->work_out)->format('H:i')
                        : '';
                    return $start . ' - ' . $end;
                })
                ->addColumn('time_permit', function ($row) {
                    $start = $row->start_time
                        ? Carbon::parse($row->start_time)->format('H:i')
                        : null;

                    $end = $row->end_time
                        ? Carbon::parse($row->end_time)->format('H:i')
                        : null;

                    if ($start && $end) {
                        return "Keluar : $end<br>Masuk : $start";
                    }
                    if ($start) return "Masuk : $start";
                    if ($end) return "Keluar : $end";

                    return '-';
                })
                ->addColumn('actual_time', function ($row) {
                    $start = $row->actual_time_in
                        ? Carbon::parse($row->actual_time_in)->format('H:i')
                        : null;

                    $end = $row->actual_time_out
                        ? Carbon::parse($row->actual_time_out)->format('H:i')
                        : null;

                    if ($start && $end) {
                        return "Keluar : $end<br>Kembali : $start";
                    }
                    if ($start) return "Masuk : $start";
                    if ($end) return "Keluar : $end";

                    return '-';
                })
                ->addColumn('security', function ($row) {
                    $html = '';
                    // Security 1
                    $icon1 = $row->security_knowledge_1 == 1
                        ? '<i class="ri-checkbox-circle-fill text-success"></i>'
                        : '<i class="ri-close-circle-fill text-danger"></i>';
                    $name1 = $row->security_name_1 ?? '-';
                    $html .= "Security 1 : {$name1} {$icon1}";
                    // Security 2
                    if ($row->security_name_2 || $row->security_knowledge_2 !== null) {
                        $icon2 = $row->security_knowledge_2 == 1
                            ? '<i class="ri-checkbox-circle-fill text-success"></i>'
                            : '<i class="ri-close-circle-fill text-danger"></i>';
                        $name2 = $row->security_name_2 ?? '-';
                        $html .= "<br>Security 2 : {$name2} {$icon2}";
                    }
                    return $html;
                })
                ->addColumn('type', function ($row) {
                    return $this->formatType($row->type);
                })
                ->addColumn('action', function ($row) {
                    $buttons = '';
                    if ((int)$row->hrd_knowledge !== 1){
                        return '';
                    } else if ($row->type === 'temporary_out') {
                        // tombol tetap muncul jika salah satu belum terisi
                        if ((int)$row->security_knowledge_1 === 0 || (int)$row->security_knowledge_2 === 0) {
                            $buttons .= '
                                <button class="btn btn-success btn-sm securityPermit-btn"
                                    data-id="'.encrypt($row->id).'">
                                    <i class="ri-check-line"></i>
                                </button>
                            ';
                        }
                    } else {
                        // selain temporary hanya sekali input
                        if ((int)$row->security_knowledge_1 === 0) {
                            $buttons .= '
                                <button class="btn btn-success btn-sm securityPermit-btn"
                                    data-id="'.encrypt($row->id).'">
                                    <i class="ri-check-line"></i>
                                </button>
                            ';
                        }
                    }

                    return $buttons ?: '-';
                })
                ->rawColumns(['action','security','actual_time','time_permit'])
                ->make(true);
        }
        return view('pages.security.employee-permit.index');;
    }
    private function formatType($type)
    {
        return match ($type) {
            'earlyout' => 'Pulang Cepat',
            'late' => 'Terlambat',
            'temporary_out' => 'Keluar Sementara',
            'pribadi' => 'Izin Sementara',
            'sick' => 'Izin Dokter',
            default => 'Lainnya',
        };
    }
    public function securityPermitKnowledge(Request $request)
    {
        $id = decrypt($request->id);
        $permit = AttendancePermit::findOrFail($id);
        $security = Auth::user()->employee;
        $now = now();

        DB::beginTransaction();

        try {
            // cari attendance sesuai employee & tanggal permit
            $attendance = DB::table('employee_attendances')
                ->where('employee_id', $permit->employee_id)
                ->whereDate('date', $permit->start_date)
                ->first();

            switch ($permit->type) {
                case 'earlyout':
                    $permit->update([
                        'actual_time_out'      => $now->format('H:i:s'),
                        'security_name_1'      => $security->fullname,
                        'security_knowledge_1' => 1,
                    ]);
                    DB::table('employee_attendance_details')
                        ->where('employee_attendance_id', $attendance->id ?? 0)
                        ->update([
                            'check_out'  => now()->format('H:i:s'),
                            'status_check_out'  => 'early_leave'
                        ]);
                    break;
                case 'temporary_out':
                    // step 1 keluar
                    if (!$permit->security_knowledge_1) {
                        $permit->update([
                            'actual_time_out'      => $now->format('H:i:s'),
                            'security_name_1'      => $security->fullname,
                            'security_knowledge_1' => 1,
                        ]);
                    }
                    // step 2 masuk kembali
                    else {
                        $permit->update([
                            'actual_time_in'       => $now->format('H:i:s'),
                            'security_name_2'      => $security->fullname,
                            'security_knowledge_2' => 1,
                        ]);
                    }
                    break;
                // case 'pribadi':
                //     // step 1 keluar
                //     if (!$permit->security_knowledge_1) {
                //         $permit->update([
                //             'actual_time_out'      => $now->format('H:i:s'),
                //             'security_nik_1'       => $security->nik,
                //             'security_name_1'      => $security->fullname,
                //             'security_knowledge_1' => 1,
                //         ]);

                //         DB::table('employee_attendances')
                //             ->where('id', $attendance->id ?? 0)
                //             ->update([
                //                 'check_out'  => $now->format('H:i:s'),
                //                 'updated_at' => now(),
                //             ]);
                //     }
                //     // step 2 masuk kembali
                //     else {
                //         $permit->update([
                //             'actual_time_in'       => $now->format('H:i:s'),
                //             'security_nik_2'       => $security->nik,
                //             'security_name_2'      => $security->fullname,
                //             'security_knowledge_2' => 1,
                //         ]);
                //         DB::table('employee_attendances')
                //             ->where('id', $attendance->id ?? 0)
                //             ->update([
                //                 'check_in'   => $now->format('H:i:s'),
                //                 'updated_at' => now(),
                //             ]);
                //     }
                //     break;
                default:
                    $permit->update([
                        'actual_time_in'       => $now->format('H:i:s'),
                        'actual_time_out'      => $now->format('H:i:s'),
                        'security_nik_1'       => $security->nik,
                        'security_name_1'      => $security->fullname,
                        'security_knowledge_1' => 1,
                    ]);

                    DB::table('employee_attendances')
                        ->where('id', $attendance->id ?? 0)
                        ->update([
                            'check_in'   => $now->format('H:i:s'),
                            'check_out'  => $now->format('H:i:s'),
                            'updated_at' => now(),
                        ]);
            }

            DB::commit();
            return response()->json([
                'message' => 'Berhasil diketahui security pada jam ' . $now->format('H:i:s')
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function attendanceRecords(Request $request){
        if ($request->ajax()) {
            $query = EmployeeAttendance::with([
                'groupEmployeeWorkhour',
                'masterWorkhour',
                'detail',
                'employee.area',
                'businessTrip'
            ])
            ->where(function ($q) {
                $q->whereNotNull('attendance_status')
                ->orWhereHas('detail', function ($d) {
                    $d->whereNotNull('check_in')
                        ->orWhereNotNull('check_out');
                });
            })
            ->whereHas('employee', function ($q) {
                $q->where('area_id', 1);
            });;
            // ================= FILTER DATE =================
            $date = $request->date ?? now()->toDateString();
            $query->whereDate('date', $date);
            // dd($date);

            return DataTables::of($query)
                ->addIndexColumn()
                ->filter(function ($query) use ($request) {
                    if ($request->search['value'] ?? false) {
                        $search = $request->search['value'];
                        $query->whereHas('employee', function ($q) use ($search) {
                            $q->where('nik', 'like', "%{$search}%")
                            ->orWhere('fullname', 'like', "%{$search}%")
                            ->orWhereHas('area', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                            ->orWhereHas('department', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                            ->orWhereHas('position', fn($q2) => $q2->where('nama', 'like', "%{$search}%"));
                        });
                    }
                })
                // ================= BASIC =================
                ->addColumn('nik', fn($row) => $row->employee?->nik ?? '-')
                ->addColumn('employee_name', fn($row) => $row->employee?->fullname ?? '-')
                ->addColumn('area_name', fn($row) => $row->area_name ?? '-')
                ->addColumn('department_name', fn($row) => $row->department_name ?? '-')

                // ================= GROUP + WORKHOUR =================
                ->addColumn('group_workhours', function ($row) {
                    $group = $row->groupEmployeeWorkhour?->name ?? '-';
                    $workhour = $row->masterWorkhour?->work_name ?? '-';
                    if ($group === '-' && $workhour === '-') {
                        return '-';
                    }
                    return "{$group} - {$workhour}";
                })
                ->addColumn('work_in', fn($row) => $row->work_in ?? '-')
                ->addColumn('work_out', fn($row) => $row->work_out ?? '-')
                ->addColumn('latlong_ci', fn($row) => $row->detail->latlong_check_in ?? '-')
                ->addColumn('latlong_co', fn($row) => $row->detail->latlong_check_out ?? '-')
                // ================= CHECK IN / OUT =================
                ->addColumn('check_in', function ($row) {
                    return $row->detail?->check_in
                        ? Carbon::parse($row->detail->check_in)->format('Y M D H:i')
                        : '-';
                })
                ->addColumn('check_out', function ($row) {
                    return $row->detail?->check_out
                        ? Carbon::parse($row->detail->check_out)->format('Y M D H:i')
                        : '-';
                })
                // ================= STATUS =================
                ->addColumn('status_check_in', fn($row) => $row->detail?->status_check_in ?? '-')
                ->addColumn('status_check_out', fn($row) => $row->detail?->status_check_out ?? '-')
                ->addColumn('attendance_status', fn($row) => $row->attendance_status ?? '-')
                ->addColumn('business_trip_type', function($row){
                    return optional($row->businessTrip)->trip_type;
                })
                ->addColumn('source', fn($row) => $row->source ?? '-')
                // ================= ACTION =================

                ->make(true);
        }
    }
    public function late(Request $request)
    {
        if ($request->ajax()) {
            $date = $request->date ?? now()->toDateString();
            $query = EmployeeAttendance::with([
            'employee.groupEmployees.groupEmployeeWorkhour.groupWorkHours.workhour.details',
            'detail',
            'lateHistories'
        ])
        // ->whereDate('date', $date)
        // filter area
        ->whereHas('employee', function ($q) {
            $q->where('area_id', 1);
        })
        // hanya yang telat
        ->whereHas('detail', function ($q) {
            $q->where('status_check_in', 'late');
        })
        ->whereHas('lateHistories', function ($q) {
            $q->where('security_knowledge', false);
        });
            // ================= FILTER DATE =================
        if ($request->filter_date_late) {
            $query->whereDate('date', $request->filter_date_late);
        }            // dd($query);
        // dd($request->filter_date_late);
        return DataTables::of($query)
            ->addIndexColumn()
            ->filter(function ($query) use ($request) {
                if ($request->search['value'] ?? false) {
                    $search = $request->search['value'];
                    $query->whereHas('employee', function ($q) use ($search) {
                        $q->where('nik', 'like', "%{$search}%")
                        ->orWhere('fullname', 'like', "%{$search}%")
                        ->orWhereHas('area', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('department', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('position', fn($q2) => $q2->where('nama', 'like', "%{$search}%"));
                    });
                }
            })
            ->addColumn('nik', fn($row) => $row->employee->nik ?? '-')
            ->addColumn('employee_name', fn($row) => $row->employee->fullname ?? '-')
            ->addColumn('position_name', fn($row) => $row->position_name ?? '-')
            ->addColumn('area_name', fn($row) => $row->area_name ?? '-')
            ->addColumn('department_name', fn($row) => $row->department_name ?? '-')
            ->addColumn('reason', fn($row) => $row->detail->reason_check_in ?? '-')
            ->addColumn('check_in', fn($row) => $row->detail->check_in ?? '-')
            ->addColumn('status_check_in', fn($row) => $row->detail->status_check_in ?? '-')
            ->addColumn('group_workhours', function ($row) {
                $groupEmployee = $row->employee->groupEmployees->first();
                $group = $groupEmployee?->groupEmployeeWorkhour?->name ?? '-';
                $activeWorkhour = $groupEmployee?->groupEmployeeWorkhour?->groupWorkHours->first();
                $workhour = $activeWorkhour?->workhour?->work_name ?? '-';
                return ($group === '-' && $workhour === '-')
                    ? '-'
                    : "{$group} - {$workhour}";
            })
            ->addColumn('work_in', function ($row) use ($date) {
                $data = $this->getWorkHourByDate($row->employee, $date);
                return $data['work_in'] ?? '-';
            })
            ->addColumn('work_out', function ($row) use ($date) {
                $data = $this->getWorkHourByDate($row->employee, $date);
                return $data['work_out'] ?? '-';
            })
            ->addColumn('action', function ($row) {
                return '
                    <button class="btn btn-success btn-sm securityLate-btn"
                        data-id="'.encrypt($row->id).'">
                        <i class="ri-check-line"></i>
                    </button>
                ';
            })
            ->rawColumns(['status_check_in', 'action'])
            ->make(true);
        }
    }
    public function lateHistories(Request $request)
    {
        if ($request->ajax()) {
            $date = $request->date ?? now()->toDateString();
            $query = EmployeeAttendance::with(['lateHistories','detail'])
            // ->whereDate('date', $date)
            ->whereHas('employee', function ($q) {
                $q->where('area_id', 1);
            })
            ->whereHas('lateHistories', function ($q) {
                $q->where('security_knowledge', true);
            });
            // ================= FILTER DATE =================
            if ($request->filter_date_late) {
                $query->whereDate('date', $request->filter_date_late);
            }
            // dd($query);
            return DataTables::of($query)
                ->addIndexColumn()

                ->filter(function ($query) use ($request) {
                    if ($request->search['value'] ?? false) {
                        $search = $request->search['value'];

                        $query->whereHas('employee', function ($q) use ($search) {
                            $q->where('nik', 'like', "%{$search}%")
                            ->orWhere('fullname', 'like', "%{$search}%")
                            ->orWhereHas('area', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                            ->orWhereHas('department', fn($q2) => $q2->where('name', 'like', "%{$search}%"))
                            ->orWhereHas('position', fn($q2) => $q2->where('nama', 'like', "%{$search}%"));
                        });
                    }
                })

                ->addColumn('nik', fn($row) => $row->employee->nik ?? '-')
                ->addColumn('employee_name', fn($row) => $row->employee->fullname ?? '-')
                ->addColumn('position_name', fn($row) => $row->position_name ?? '-')
                ->addColumn('area_name', fn($row) => $row->area_name ?? '-')
                ->addColumn('department_name', fn($row) => $row->department_name ?? '-')
                ->addColumn('reason', fn($row) => $row->lateHistories->reason ?? '-')
                ->addColumn('actual_in_employee', fn($row) => $row->detail->check_in ?? '-')
                ->addColumn('actual_in_security', fn($row) => $row->lateHistories->actual_in ?? '-')
                ->addColumn('security', function ($row) {
                    $html = '';
                    $icon1 = $row->lateHistories->security_knowledge == 1
                        ? '<i class="ri-checkbox-circle-fill text-success"></i>'
                        : '<i class="ri-close-circle-fill text-danger"></i>';
                    $name1 = $row->lateHistories->security_name ?? '-';
                    $html .= "Security : {$name1} {$icon1}";

                    return $html;
                })
                ->addColumn('head', function ($row) {
                    $html = '';
                    $icon1 = $row->lateHistories->head_knowledge == 1
                        ? '<i class="ri-checkbox-circle-fill text-success"></i>'
                        : '<i class="ri-close-circle-fill text-danger"></i>';
                    $name1 = $row->lateHistories->knowledgeby_headName ?? '-';
                    $html .= "Atasan : {$name1} {$icon1}";

                    return $html;
                })
                ->addColumn('hrd', function ($row) {
                    $html = '';
                    $icon1 = $row->lateHistories->hrd_knowledge == 1
                        ? '<i class="ri-checkbox-circle-fill text-success"></i>'
                        : '<i class="ri-close-circle-fill text-danger"></i>';
                    $name1 = $row->lateHistories->knowledgeby_hrdName ?? '-';
                    $html .= "HRD : {$name1} {$icon1}";

                    return $html;
                })
                ->rawColumns(['security','head','hrd'])
                ->make(true);

        }
    }
    public function securityLateKnowledge(Request $request)
    {
        $id = decrypt($request->id);

        DB::beginTransaction();

        try {
            $attendance = EmployeeAttendance::with([
                'employee',
                'lateHistories'
            ])->findOrFail($id);

            $employee = $attendance->employee;
            $late = $attendance->lateHistories;
            $security = Auth::user()->employee;
            $now = now();

            if (!$late) {
                throw new \Exception('Data late tidak ditemukan');
            }

            // ================= AMBIL ATASAN =================
            $lineApproval = $employee->lineApprovals()
                ->where('approval_type', 'Attendance Permit')
                ->first();

            $approverId = $lineApproval?->approve_1;

            if (!$approverId) {
                throw new \Exception('Approver tidak ditemukan');
            }
            // dd($late);

            $approverEmployee = Employee::with('user')->find($approverId);

            // ================= UPDATE SECURITY =================
            $late->update([
                'security_knowledge' => true,
                'security_name'      => $security->fullname,
                'actual_in'          => $now->format('H:i:s'),
            ]);

            // ================= NOTIF =================
            if ($approverEmployee && $approverEmployee->user?->email) {

                $details = [
                    'greeting' => 'Hi ' . $approverEmployee->fullname,
                    'subject'  => 'Informasi Karyawan Terlambat',
                    'lines' => [
                        'Terdapat karyawan terlambat:',
                        '',
                        'Nama Karyawan : ' . $employee->fullname,
                        'Tanggal       : ' . $now->format('Y-m-d'),
                        'Jam Masuk     : ' . $now->format('H:i:s'),
                        'Alasan        : ' . ($late->reason ?? '-'),
                    ],
                    'actionText' => 'Lihat Detail',
                    'actionURL'  => route('attendance-late.profile-index'), // arahkan ke page list
                    'thanks'     => 'Terimakasih',
                ];

                $approverEmployee->user
                    ->notify(new AttendancePermitNotification($details));
            }

            DB::commit();

            return response()->json([
                'message' => 'Berhasil diketahui security & dikirim ke atasan'
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'message' => $e->getMessage()
            ], 500);
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
