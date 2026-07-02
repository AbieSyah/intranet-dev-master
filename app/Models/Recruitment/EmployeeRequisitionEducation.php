<?php

namespace App\Models\Recruitment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class EmployeeRequisitionEducation extends Model
{
    use HasFactory;
    protected $table = 'employee_requisition_educations';
    protected $fillable = ['name'];
    
    public function requisitions(): BelongsToMany
    {
        return $this->belongsToMany(
            EmployeeRequisition::class,
            'employee_requisition_has_educations',
            'education_id',
            'requisition_id'
        )->withPivot('major');
    }
}
