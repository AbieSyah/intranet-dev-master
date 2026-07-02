<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkHour extends Model
{
    use HasFactory;

    protected $table = 'master_work_hour';
    protected $fillable = [
        'work_name',
    ];

    public function details()
{
    return $this->hasMany(WorkHourDetail::class, 'workhour_id');
}
}
