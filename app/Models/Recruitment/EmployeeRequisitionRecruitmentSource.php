<?php

namespace App\Models\Recruitment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EmployeeRequisitionRecruitmentSource extends Model
{
    use HasFactory;
    protected $table = 'employee_requisition_recruitment_sources'; 
    protected $fillable = ['name'];
    public function requisitions(): BelongsToMany
    {
        return $this->belongsToMany(
            EmployeeRequisition::class, 
            'employee_requisition_has_recruitment_sources',
            'source_id',
            'requisition_id'
        )->withPivot('other_detail');
    }
}
