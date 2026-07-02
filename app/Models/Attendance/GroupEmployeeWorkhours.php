<?php

namespace App\Models\Attendance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupEmployeeWorkhours extends Model
{
    use HasFactory;
    protected $fillable = [
        "name",
    ];

    public function groupEmployees(){
        return $this->hasMany(GroupEmployee::class, 'group_id');
    }
    public function groupWorkHours(){
        return $this->hasMany(GroupWorkhour::class, 'group_id');
    }
}
