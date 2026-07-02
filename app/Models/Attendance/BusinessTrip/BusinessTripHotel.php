<?php

namespace App\Models\Attendance\BusinessTrip;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessTripHotel extends Model
{
    use HasFactory;
    protected $fillable = [
        'business_trip_id',
        'reservation_by_ga',
        'hotel_name',
        'check_in',
        'check_out',
        'total_days',
        'total_nights',
    ];
    public function businessTrip()
    {
        return $this->belongsTo(BusinessTrip::class, 'business_trip_id');
    }
}
