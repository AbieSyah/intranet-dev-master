<?php

namespace App\Models\Attendance;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClaimApproval extends Model
{
    use HasFactory;
    protected $fillable = [
        'claim_overtime_id',
        'employee_id',
        'position',
        'department',
        'level',
        'status',
        'approved_at',
        'reason_reject',
        'approval_token'
    ];

    public function claimOvertime()
    {
        return $this->belongsTo(ClaimOvertime::class, 'claim_overtime_id');
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
