<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupWorkhour extends Model
{
    use HasFactory;

    protected $fillable = [
        "workhour_id",
        "group_id",
        "start_date",
        "end_date",
        "is_active"
    ];
    public function workhour(){
        return $this->belongsTo(WorkHour::class, 'workhour_id');
    }
}
