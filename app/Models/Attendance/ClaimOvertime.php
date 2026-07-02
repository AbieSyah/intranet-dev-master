<?php

namespace App\Models\Attendance;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimOvertime extends Model
{
    use HasFactory;
    protected $fillable = [
        'employee_id',
        'employee_attendance_id',
        'position',
        'area',
        'department',
        'overtime_date',
        'claim_overtime',
        'total_work',
        'actual_start_time',
        'actual_end_time',
        'agreed_work_start',
        'agreed_work_end',
        'source',
        'reason',
        'status',
        "hrd_knowledge",
        'hrd_note',
        'created_by',
        'updated_by'
    ];
    public function approvals()
    {
        return $this->hasMany(ClaimApproval::class);
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    public function employeeAttendance()
    {
        return $this->belongsTo(EmployeeAttendance::class, 'employee_attendance_id');
    }
}
