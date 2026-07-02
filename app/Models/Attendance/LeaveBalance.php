<?php

namespace App\Models\Attendance;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
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
        'total_days',
        'remaining_days',
        'valid_from',
        'valid_to'
    ];

    public function employee(){
        return $this->belongsTo(Employee::class);
    }

    public function leaveType(){
        return $this->belongsTo(LeaveSetting::class, 'leave_type_id');
    }

}
