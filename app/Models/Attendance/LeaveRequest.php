<?php

namespace App\Models\Attendance;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'employee_id',
        'nik',
        'employee_name',
        'position',
        'area',
        'department',
        'leave_type_id',
        'type',
        'request_date',
        'start_date',
        'end_date',
        'total_days',
        'attachment',
        'notes',
        'status',
        'created_by',
    ];

    public function employee(){
        return $this->belongsTo(Employee::class);
    }
     public function approvals()
    {
        return $this->hasMany(LeaveRequestApproval::class, 'leave_request_id');
    }

}
