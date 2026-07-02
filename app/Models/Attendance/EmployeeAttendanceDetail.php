<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAttendanceDetail extends Model
{
    use HasFactory;

    // public $timestamps = false;

    protected $fillable = [
        'employee_attendance_id',
        'check_in',
        'check_out',
        'status_check_in',
        'status_check_out',
        'latlong_check_in',
        'latlong_check_out',
        'reason_check_in',
        'reason_check_out',
        'distance_check_in',
        'distance_check_out',
        'out_of_range_check_in',
        'out_of_range_check_out',
    ];

    public function attendance()
    {
        return $this->belongsTo(EmployeeAttendance::class);
    }
}
