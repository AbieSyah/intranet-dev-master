<?php

namespace App\Models\Attendance;

use App\Http\Controllers\Attendance\GroupEmployeeWorkHourController;
use App\Models\Attendance\BusinessTrip\BusinessTrip;
use App\Models\Employee;
use App\Models\Attendance\EmployeeAttendanceDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAttendance extends Model
{
    use HasFactory;
    protected $fillable =[
        'employee_id',
        'position_name',
        'area_name',
        'department_name',
        'business_trip_id',
        'group_id',
        'master_workhour_id',
        'work_in',
        'work_out',
        'date',
        'attendance_status',
        'source',
        'created_by',
        'updated_by',
        'holiday_id',
        'holiday_name',
        'holiday_type',
        'attendance_status',
        'source',
        'check_in',
        'check_out',
        'status_check_in',
        'status_check_out',
    ];

    public function employee(){
        return $this->belongsTo(Employee::class);
    }
    public function groupEmployee()
    {
        return $this->belongsTo(GroupEmployee::class, 'group_id', 'group_id');
    }
    public function groupEmployeeWorkhour()
    {
        return $this->belongsTo(GroupEmployeeWorkhours::class, 'group_id');
    }
    public function masterWorkhour(){
        return $this->belongsTo(WorkHour::class, 'master_workhour_id');
    }
    public function detail()
    {
        return $this->hasOne(EmployeeAttendanceDetail::class, 'employee_attendance_id')->latest('id');
    }
    public function lateHistories()
    {
        return $this->hasOne(lateHistories::class);
    }
    public function claimOvertime()
    {
        return $this->hasOne(ClaimOvertime::class, 'employee_attendance_id');
    }
    public function businessTrip(){
        return $this->belongsTo(BusinessTrip::class);
    }
}
