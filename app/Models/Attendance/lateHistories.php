<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class lateHistories extends Model
{
    use HasFactory;
    protected $fillable = [
        'employee_attendance_id',
        'security_knowledge',
        'security_name',
        'hrd_knowledge',
        'knowledgeby_hrdName',
        'head_knowledge',
        'knowledgeby_headName',
        'reason',
        'actual_in',
        'approval_token',
    ];
    public function attendance()
    {
        return $this->belongsTo(EmployeeAttendance::class);
    }
}
