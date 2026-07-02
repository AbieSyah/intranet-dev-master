<?php

namespace App\Models\Attendance\BusinessTrip;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessTripTransportation extends Model
{
    use HasFactory;
    protected $fillable = [
        'business_trip_id',
        'transport_type',
        'public_transport_type',
        'vehicle_number',
        'driver_name',
        'departure_date',
        'departure_time',
        'arrival_date',
        'arrival_time',
        'notes',
    ];
    public function businessTrip()
    {
        return $this->belongsTo(BusinessTrip::class, 'business_trip_id');
    }
}
