<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkHourDetail extends Model
{
    use HasFactory;

    protected $table = 'workhour_detail';
    protected $fillable = [
        'workhour_id',
        'day',
        'work_in',
        'work_out',
        'break_duration',
        'notes'
    ];

public function workhour(){
    return $this->belongsTo(WorkHour::class, 'workhour_id');
}
}
