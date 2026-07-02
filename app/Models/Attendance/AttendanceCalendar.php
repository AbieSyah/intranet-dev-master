<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceCalendar extends Model
{
    use HasFactory;

    protected $table = 'attendance_calendars';

    protected $fillable = [
        "date",
        "type",
        "is_hq",
        "name",
        "is_active"
    ];

    protected $casts = [
        'date' => 'date',
    ];
}
