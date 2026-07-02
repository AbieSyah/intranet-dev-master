<?php

namespace App\Models\Attendance\BusinessTrip;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessCancellationApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'cancellation_id',
        'approver_id',
        'position',
        'department',
        'level',
        'status',
        'approved_at',
        'reason',
        'approval_token',
    ];
     public function approver()
    {
        return $this->belongsTo(Employee::class, 'approver_id');
    }
    public function businessCancellation()
    {
        return $this->belongsTo(BusinessCancellation::class, 'cancellation_id');
    }
    public function logs(){
        return $this->hasMany(BusinessCancellationLog::class,'approval_path_id');
    }
}
