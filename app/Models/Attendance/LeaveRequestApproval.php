<?php

namespace App\Models\Attendance;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequestApproval extends Model
{
    use HasFactory;
    protected $table = 'leave_approvals';
    protected $fillable = [
        'leave_request_id',
        'approver_id',
        'approver_name',
        'position',
        'department',
        'level',
        'status',
        'approved_at',
        'reason_reject',
        // 'notes',
        'approval_token',
        // 'created_at',
    ];
    public function leaveRequest()
    {
        return $this->belongsTo(LeaveRequest::class, 'leave_request_id');
    }

    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approver_id');
    }
}
