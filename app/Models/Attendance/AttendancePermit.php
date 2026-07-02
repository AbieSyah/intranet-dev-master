<?php

namespace App\Models\Attendance;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendancePermit extends Model
{
    use HasFactory;
    protected $fillable = [
        'employee_id',
        'nik',
        'employee_name',
        'position',
        'area',
        'department',
        'reason',
        'type',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'work_in',
        'work_out',
        'attachment',
        'hrd_knowledge',
        'hrd_name',
        'status',
        'approved_by_name',
        'approved_by_position',
        'approved_by_at',
        'reason_reject',
        'approval_token',
        'created_by',
        'updated_by',
        'actual_time_in',
        'actual_time_out',
        'security_name_1',
        'security_name_2',
        'security_knowledge_1',
        'security_knowledge_2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
