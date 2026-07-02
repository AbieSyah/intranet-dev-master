<?php

namespace App\Models\Attendance\BusinessTrip;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessCancellation extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_trip_id',
        'propose_date',
        'reason_cancel',
        'reason_other',
        'employee_covered_amount',
        'company_covered_amount',
        'total_loss_amount',
        'currency',
        'status',
    ];

    public function businessTrip(){
        return $this->belongsTo(BusinessTrip::class);
    }
    public function items (){
        return $this->hasMany(BusinessCancellationItem::class, 'cancellation_id');
    }
    public function approvals (){
        return $this->hasMany(BusinessCancellationApproval::class, 'cancellation_id');
    }
}
