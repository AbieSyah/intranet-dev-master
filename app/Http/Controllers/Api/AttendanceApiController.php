<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance\EmployeeAttendance;
use App\Models\Attendance\EmployeeAttendanceDetail;
use App\Models\Attendance\Positioning;
use App\Models\Attendance\GroupEmployee;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use ParagonIE\Sodium\Compat as Sodium;
use App\Models\Attendance\lateHistories;
use App\Models\Attendance\BusinessTrip\BusinessTrip;

class AttendanceApiController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $employee = $user->employee;

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee tidak ditemukan'
            ], 404);
        }

        $positioning = Positioning::where('area', $employee->area_id)->first();

        $today = now()->toDateString();
        $dayName = now()->format('l');

        $groupEmployee = GroupEmployee::with([
            'groupEmployeeWorkhour.groupWorkHours' => function ($q) use ($today) {
                $q->where('is_active', 1)
                ->where(function ($query) use ($today) {
                    $query->whereNull('start_date')
                            ->orWhereDate('start_date', '<=', $today);
                })
                ->where(function ($query) use ($today) {
                    $query->whereNull('end_date')
                            ->orWhereDate('end_date', '>=', $today);
                });
            },
            'groupEmployeeWorkhour.groupWorkHours.workhour.details'
        ])->where('employee_id', $user->employee_id)->first();
        // dd([
        //     'groupEmployee' => $groupEmployee,
        //     'group' => $groupEmployee?->groupEmployeeWorkhour,
        //     'groupWorkHours' => $groupEmployee?->groupEmployeeWorkhour?->groupWorkHours,
        // ]);
        $workIn = null;
        $workOut = null;

        if ($groupEmployee && $groupEmployee->groupEmployeeWorkhour) {

            foreach ($groupEmployee->groupEmployeeWorkhour->groupWorkHours as $gwh) {

                if (!$gwh->workhour) continue;

                foreach ($gwh->workhour->details as $detail) {

                    if (strtolower($detail->day) === strtolower($dayName)) {
                        $workIn = $detail->work_in;
                        $workOut = $detail->work_out;
                        break 2;
                    }
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'positioning' => $positioning ? [
                    'id' => $positioning->id,
                    'office_lat' => $positioning->latitude,
                    'office_lng' => $positioning->longitude,
                    'max_distance' => $positioning->max_distance,
                    'area_name' => $positioning->areas
                ] : null,

                'work_hour' => [
                    'work_in'  => $workIn,
                    'work_out' => $workOut,
                ]
            ]
        ]);
    }

    public function myAttendance(Request $request)
    {
        $user = auth()->user();

        if (!$user || !$user->employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee tidak ditemukan'
            ], 404);
        }

        $perPage = $request->input('per_page', 10);

        $attendances = EmployeeAttendance::where(
                'employee_id',
                $user->employee->id
            )
            ->with('detail')
            ->orderBy('date', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $attendances
        ]);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    private function getPositioningByEmployee($employeeId)
    {
        $employee = Employee::find($employeeId);
        if (!$employee || !$employee->area_id) {
            return null;
        }
        return Positioning::where('area', $employee->area_id)->first();
    }

    private function appendPipeValue($existingValue, $newValue)
    {
        if (empty($existingValue)) {
            return $newValue;
        }
        return $existingValue . '|' . $newValue;
    }

    public function challenge(Request $request)
    {
        $user = $request->user();
        $randomBytes = random_bytes(32);
        $challenge = rtrim(strtr(base64_encode($randomBytes), '+/', '-_'), '=');
        Cache::put('biometric_challenge_' . $user->id, $challenge, 300);
        return response()->json(['challenge' => $challenge]);
    }

    public function today(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee tidak ditemukan'
            ], 404);
        }

        $today = Carbon::today()->toDateString();

        $attendance = EmployeeAttendance::where('employee_id', $user->employee->id)
            ->where('date', $today)
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => true,
                'data' => null,
                'detail' => []
            ]);
        }

        $details = EmployeeAttendanceDetail::where('employee_attendance_id', $attendance->id)
            ->latest()
            ->first();

        return response()->json([
            'success' => true,
            'data' => $attendance,
            'details' => $details
        ]);
    }

    public function checkin(Request $request)
    {
        return $this->handleAttendance($request, 'checkin');
    }

    public function checkout(Request $request)
    {
        return $this->handleAttendance($request, 'checkout');
    }

    private function handleAttendance(Request $request, $type)
    {
        $user = $request->user();
        if (!$user || !$user->employee) {
            return response()->json(['error' => 'Employee data not found'], 404);
        }

        if (empty($user->biometric_device_id)) {
            return response()->json([
                'error' => 'Biometric device not registered',
                'code'  => 'BIOMETRIC_NOT_REGISTERED'
            ], 403);
        }

        $rules = [
            'device_id' => 'required|string',
            'timestamp' => 'required',
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'reason'    => 'nullable|string|max:255',
        ];

        if ($user->biometric_key) {
            $rules['signature'] = 'required|string';
        } else {
            $rules['signature'] = 'required|string'; // tidak wajib
        }

        $request->validate($rules);

        // Hanya validasi device_id jika biometrik aktif
        if ($user->biometric_key && $user->biometric_device_id !== $request->device_id) {
            return response()->json(['error' => 'Device tidak sesuai, gunakan device terdaftar'], 403);
        }

         if ($user->biometric_key) {
            // Biometric aktif, lakukan verifikasi signature
            $challenge = Cache::pull('biometric_challenge_' . $user->id);
            if (!$challenge) {
                return response()->json(['error' => 'Challenge expired or not found'], 400);
            }

            try {
                $publicKey = base64_decode($user->biometric_key);
                $signature = base64_decode($request->signature);
                $challengeBytes = base64_decode(strtr($challenge, '-_', '+/'));
                $verified = sodium_crypto_sign_verify_detached(
                    $signature,
                    $challengeBytes,
                    $publicKey
                );
                if (!$verified) {
                    return response()->json(['error' => 'Invalid signature'], 401);
                }
            } catch (\Exception $e) {
                return response()->json(['error' => 'Verification failed', 'details' => $e->getMessage()], 500);
            }
        }

        $today = Carbon::today()->toDateString();
        $now = Carbon::now();
        $nowTimeString = $now->format('H:i:s');
        $nowDateTimeString = $now->toDateTimeString();
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $dayName = $now->format('l');

        $groupEmployee = GroupEmployee::with([
            'groupEmployeeWorkhour.groupWorkHours' => function ($q) use ($today) {
                $q->where('is_active', 1)
                ->where(function ($query) use ($today) {
                    $query->whereNull('start_date')
                        ->orWhereDate('start_date', '<=', $today);
                })
                ->where(function ($query) use ($today) {
                    $query->whereNull('end_date')
                        ->orWhereDate('end_date', '>=', $today);
                });
            },
            'groupEmployeeWorkhour.groupWorkHours.workhour.details'
        ])->where('employee_id', $user->employee_id)->first();

        $workIn = null;
        $workOut = null;
        $groupId = null;
        $masterWorkhourId = null;

        if ($groupEmployee && $groupEmployee->groupEmployeeWorkhour) {
            $groupId = $groupEmployee->group_id;
            $groupWorkHours = $groupEmployee->groupEmployeeWorkhour->groupWorkHours ?? [];

            foreach ($groupWorkHours as $gwh) {
                if (!$gwh->workhour) continue;
                $masterWorkhourId = $gwh->workhour_id;

                foreach ($gwh->workhour->details as $detail) {
                    if (strtolower($detail->day) === strtolower($dayName)) {
                        $workIn = $detail->work_in;
                        $workOut = $detail->work_out;
                        break 2;
                    }
                }
            }
        }

        Log::info('ATTENDANCE DEBUG', [
            'employee_id' => $user->employee_id,
            'dayName' => $dayName,
            'groupId' => $groupId,
            'masterWorkhourId' => $masterWorkhourId,
            'workIn' => $workIn,
            'workOut' => $workOut,
            'groupEmployee' => $groupEmployee,
        ]);

        if (!$groupId || !$masterWorkhourId || !$workIn || !$workOut) {
            return response()->json([
                'success' => false,
                'message' => 'Jam kerja belum diatur. Hubungi HRD/Admin.'
            ], 400);
        }

        $positioning = $this->getPositioningByEmployee($user->employee_id);
        if (!$positioning) {
            return response()->json(['error' => 'Lokasi kantor tidak ditemukan untuk area karyawan'], 404);
        }

        $distance = $this->calculateDistance(
            $latitude,
            $longitude,
            (float)$positioning->latitude,
            (float)$positioning->longitude
        );
        $outOfRange = ($distance > $positioning->max_distance) ? 1 : 0;
        // $outOfRange = [];
        $statusCheckIn = null;
        $statusCheckOut = null;

        if ($type === 'checkin' && $workIn) {
            $workInCarbon = Carbon::parse($workIn);
            $overtimeCheckinStart = $workInCarbon->copy()->subHour();
            if ($now->between($overtimeCheckinStart, $workInCarbon)) {
                $statusCheckIn = 'overtime';
            } elseif ($nowTimeString <= $workIn) {
                $statusCheckIn = 'on_time';
            } else {
                $statusCheckIn = 'late';
            }
        }

        if ($type === 'checkout' && $workOut) {
            $workOutCarbon = Carbon::parse($workOut);
            $overtimeCheckoutStart = $workOutCarbon->copy()->addHour();
            if ($now >= $overtimeCheckoutStart) {
                $statusCheckOut = 'overtime';
            } elseif ($nowTimeString >= $workOut) {
                $statusCheckOut = 'on_time';
            } else {
                $statusCheckOut = 'early';
            }
        }

        $attendance = EmployeeAttendance::firstOrNew([
            'employee_id' => $user->employee_id,
            'date'        => $today,
        ]);

        if (!$attendance->exists) {
            $employee = $user->employee;

            $attendance->fill([
                'position_name'   => optional($user->employee->position)->nama,
                'area_name'       => optional($user->employee->area)->name,
                'department_name' => optional($user->employee->department)->name,
                'group_id'        => $groupId,
                'master_workhour_id' => $masterWorkhourId,
                'work_in'         => $workIn,
                'work_out'        => $workOut,
                'attendance_status' => 'present',
                'created_by'      => $user->employee_id,
                'updated_by'      => $user->employee_id,
            ]);
        }

        $attendance->save();

        // $detail = $attendance->detail;

        $reasonFromUser = $request->input('reason');

        // if (!$detail) {
        //     $detail = new EmployeeAttendanceDetail();
        //     $detail->employee_attendance_id = $attendance->id;
        // }

        if ($type === 'checkin') {

            $detail = EmployeeAttendanceDetail::firstOrNew([
                'employee_attendance_id' => $attendance->id,
            ]);


            if (!$detail->check_in) {
                $detail->check_in = $nowDateTimeString;
                $detail->status_check_in = $statusCheckIn;
                $detail->latlong_check_in = "{$latitude},{$longitude}";
                $detail->reason_check_in = $reasonFromUser;
                $detail->distance_check_in = (float)round($distance, 2);
                $detail->out_of_range_check_in = $outOfRange;
            }

            $detail->save();

            if ($attendance->business_trip_id) {
                $businessTrip = BusinessTrip::find($attendance->business_trip_id);
                if ($businessTrip && $businessTrip->status === 'approved') {
                    $businessTrip->update(['status' => 'ongoing']);
                }
            }

            if ($statusCheckIn === 'late') {

                $alreadyExists = lateHistories::where(
                    'employee_attendance_id',
                    $attendance->id
                )->exists();

                if (!$alreadyExists) {

                    $lateHistory = lateHistories::create([
                        'employee_attendance_id' => $attendance->id,
                        'reason' => $reasonFromUser,
                        'actual_in' => $detail->check_in,
                    ]);

                    // $lineApproval = $employee->lineApprovals()
                    //     ->where('approval_type', 'Attendance Permit')
                    //     ->first();

                    // $approver = $lineApproval?->approve_1;

                    // if (!$approver) {
                    //     throw new \Exception('Approver tidak ditemukan');
                    // }

                    // $approvers = Employee::find($lineApproval->approve_1);

                    // $token = (string) SupportStr::uuid();

                    // foreach ($approvers as $approver) {

                    //     $details = [
                    //         'greeting'   => 'Hi ' . $approver->name . ',',
                    //         'subject'    => 'Konfirmasi Keterlambatan',
                    //         'lines'      => [
                    //             'Ada permintaan konfirmasi keterlambatan.',
                    //             'Nama Karyawan  : ' . $user->employee->nama,
                    //             'NIK            : ' . $user->employee->nik,
                    //             'Jam Kerja      : ' . $workIn . ' - ' . $workOut,
                    //             'Jam Masuk      : ' . $detail->check_in,
                    //             'Alasan         : ' . ($reasonFromUser ?? '-'),
                    //         ],
                    //         'actionText' => 'Konfirmasi',
                    //         'actionURL' => route('attendance.approval', ['token' => $token]) . '#pill-approval',
                    //         'thanks'     => 'Terima kasih.',
                    //     ];

                    //     $approver->notify(new AttendancePermitNotification($details));
                    // }
                }
            }

            return response()->json([
                'message' => 'Check in berhasil',
                'time' => $detail->check_in
            ], 201);
        } elseif ($type === 'checkout') {

            $detail = EmployeeAttendanceDetail::where(
                'employee_attendance_id',
                $attendance->id
            )->first();

            // kalau belum ada detail sama sekali
            if (!$detail) {

                $detail = new EmployeeAttendanceDetail();
                $detail->employee_attendance_id = $attendance->id;

                // biarkan checkin kosong
                $detail->check_in = null;
                $detail->status_check_in = null;
                $detail->latlong_check_in = null;
                $detail->reason_check_in = null;
                $detail->distance_check_in = null;
                $detail->out_of_range_check_in = null;
            }

            // isi checkout
            $detail->check_out = $nowDateTimeString;
            $detail->status_check_out = $statusCheckOut;
            $detail->latlong_check_out = "{$latitude},{$longitude}";
            $detail->reason_check_out = $reasonFromUser;
            $detail->distance_check_out = (float) round($distance, 2);
            $detail->out_of_range_check_out = $outOfRange;

            $detail->save();

            if ($attendance->business_trip_id) {
                $businessTrip = BusinessTrip::find($attendance->business_trip_id);
                if ($businessTrip && $businessTrip->status === 'approved') {
                    $businessTrip->update(['status' => 'ongoing']);
                }
            }

            return response()->json([
                'message' => 'Check out berhasil',
                'time' => $detail->check_out
            ], 201);
        }

        $attendance->save();
        $detail->save();

        Log::info('Checkin data', [
            'out_of_range' => $outOfRange,
            'type' => gettype($outOfRange),
            'distance' => $distance,
        ]);

        return response()->json([
            'message' => ucfirst($type) . ' berhasil',
            'time'    => $nowDateTimeString,
            'reason'  => $reasonFromUser,
        ], 201);
    }
}
