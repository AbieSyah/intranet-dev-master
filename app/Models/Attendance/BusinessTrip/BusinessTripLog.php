<?php

namespace App\Models\Attendance\BusinessTrip;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessTripLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_trip_id',
        'approval_path_id',
        'status',
        'reason',
        'action_at',
    ];
}
