<?php

namespace App\Models\Attendance\BusinessTrip;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessReportItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_report_id',
        'qty',
        'category',
        'unit_total',
        'unit_amount',
        'expense_date',
        'notes'
    ];
    public function businessReport(){
        return $this->belongsTo(BusinessReport::class);
    }
    public function attachments(){
        return $this->hasMany(BusinessReportAttachment::class);
    }
}
