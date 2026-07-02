<?php

namespace App\Models\Attendance\BusinessTrip;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessTripApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_trip_id',
        'approver_id',
        'position',
        'department',
        'level',
        'status',
        'approved_at',
        'reason',
        'approval_token'
    ];
    public function businessTrip()
    {
        return $this->belongsTo(BusinessTrip::class, 'business_trip_id');
    }
    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approver_id');
    }
    public function logs(){
        return $this->hasMany(BusinessTripLog::class,'approval_path_id')->latest();
    }
}
