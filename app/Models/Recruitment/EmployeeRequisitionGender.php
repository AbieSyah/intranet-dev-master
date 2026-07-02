<?php

namespace App\Models\Recruitment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeRequisitionGender extends Model
{
    use HasFactory;
    protected $table = 'employee_requisition_genders';
    protected $fillable = [
        'requisition_id',
        'gender_name',
        'needs_count',
        'start_age',
        'end_age',
    ];
    
    public function requisition(): BelongsTo
    {
        return $this->belongsTo(EmployeeRequisition::class, 'requisition_id');
    }
}
