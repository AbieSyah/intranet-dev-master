<?php

namespace App\Models\Attendance\BusinessTrip;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessReportApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_report_id',
        'approver_id',
        'position',
        'department',
        'level',
        'status',
        'approved_at',
        'reason',
        'approval_token',
    ];
    public function businessReport()
    {
        return $this->belongsTo(BusinessReport::class);
    }
    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approver_id');
    }
    public function logs(){
        return $this->hasMany(BusinessReportLog::class,'approval_path_id')->latest();
    }
}
