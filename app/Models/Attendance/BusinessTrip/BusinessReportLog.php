<?php

namespace App\Models\Attendance\BusinessTrip;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessReportLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_report_id',
        'approval_path_id',
        'status',
        'reason',
        'action_at',
    ];


    public function busiessReport(){
        return $this->hasOne(BusinessReport::class);
    }
    public function approverPath(){
        return $this->belongsTo(BusinessReportApproval::class);
    }
}
