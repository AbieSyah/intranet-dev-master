<?php

namespace App\Models\Attendance\BusinessTrip;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessTripCost extends Model
{
    use HasFactory;
    protected $fillable = [
        'business_trip_id',
        'category',
        'qty',
        'unit_amount',
        'total_amount',
        'currency',
        'notes',
    ];

    public function businessTrip()
    {
        return $this->belongsTo(BusinessTrip::class, 'business_trip_id');
    }
}
