<?php

namespace App\Models\Attendance;

use App\Models\Level;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessTripAllowance extends Model
{
    use HasFactory;

    protected $fillable = [
        'level_id',
        'category',
        'minimum_hours',
        'amount',
        'currency',
        'trip_type'
    ];
    public function level(){
        return $this->belongsTo(Level::class, 'level_id');
    }
}
