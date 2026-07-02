<?php

namespace App\Models\Attendance\BusinessTrip;

use App\Models\Attendance\EmployeeAttendance;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Attendance\BusinessTrip\BusinessReport;
use App\Models\Attendance\BusinessTrip\BusinessTripCost;
use App\Models\Attendance\BusinessTrip\BusinessTripHotel;
use App\Models\Attendance\BusinessTrip\BusinessTripTransportation;
use App\Models\Attendance\BusinessTrip\BusinessTripApproval;
use App\Models\Attendance\BusinessTrip\BusinessTripLog; 

class BusinessTrip extends Model
{
    use HasFactory;
    protected $fillable = [
        'employee_id',
        'level',
        'position',
        'department',
        'no_document',
        'trip_type',
        'propose_date',
        'start_date',
        'end_date',
        'total_days',
        'departure_time',
        'arrival_time',
        'departure_from',
        'arrival_to',
        'purpose',
        'status',
        'total_cost',
        'expense_method',
        'advance_amount',
        'advance_currency',
        'need_hotel',
        'notes',
        'hrd_knowledge',
        'hrd_name',
        'hrd_knowledge_date',
        'revised_level',
        'revised_count',
        'updated_by',
        'approved_at'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    public function costs()
    {
        return $this->hasMany(BusinessTripCost::class, 'business_trip_id');
    }
    public function hotels()
    {
        return $this->hasOne(BusinessTripHotel::class, 'business_trip_id');
    }
    public function transportations()
    {
        return $this->hasOne(BusinessTripTransportation::class, 'business_trip_id');
    }
    public function approvals()
    {
        return $this->hasMany(BusinessTripApproval::class, 'business_trip_id');
    }
    public function report()
    {
        return $this->hasMany(BusinessReport::class, 'business_trip_id');
    }
    public function cancellation()
    {
        return $this->hasMany(BusinessCancellation::class, 'business_trip_id');
    }
    public function logs()
    {
        return $this->hasMany(BusinessTripLog::class)->latest();
    }
    public function employeeAttendance (){
        return $this->hasMany(EmployeeAttendance::class);
    }
}
