<?php

namespace App\Models\Attendance\BusinessTrip;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessCancellationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cancellation_id',
        'qty',
        'category',
        'unit_total',
        'unit_amount',
        'currency',
        'notes',
    ];

    public function businessCancellation(){
        return $this->belongsTo(BusinessCancellation::class, 'cancellation_id');
    }
}
