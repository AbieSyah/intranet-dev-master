<?php

namespace App\Models\Attendance\BusinessTrip;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_trip_id',
        'employee_id',
        'level',
        'position',
        'department',
        'propose_date',
        'trip_type',
        'start_date',
        'end_date',
        'total_days',
        'arrival_to',
        'purpose',
        'report_result',
        'balance_amount',
        'currency',
        'type',
        'total_cost',
        'notes',
        'revised_level',
        'revised_count',
        'status',
    ];

    public function businessTrip(){
        return $this->belongsTo(BusinessTrip::class);
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function reportItems(){
        return $this->hasMany(BusinessReportItem::class);
    }
    public function approvals(){
        return $this->hasMany(BusinessReportApproval::class);
    }
    public function logs(){
        return $this->hasMany(BusinessReportLog::class)->latest();
    }
}
