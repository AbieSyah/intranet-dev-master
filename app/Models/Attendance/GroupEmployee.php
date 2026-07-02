<?php

namespace App\Models\Attendance;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupEmployee extends Model
{
    use HasFactory;

    protected $fillable = [
        "employee_id",
        "group_id"
    ];

    public function employee(){
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function groupEmployeeWorkhour(){
        return $this->belongsTo(GroupEmployeeWorkhours::class, 'group_id');
    }
}
