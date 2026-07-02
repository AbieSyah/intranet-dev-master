<?php

namespace App\Models\Attendance\BusinessTrip;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessReportAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_report_item_id',
        'file_name',
        'file_path',
        'file_type',
    ];

    public function reportItem(){
        return $this->belongsTo(BusinessReportItem::class);
    }
}
