<?php

namespace App\Models\Recruitment;

use App\Models\Area;
use App\Models\Department;
use App\Models\Position;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobPosting extends Model
{
    use HasFactory;
    protected $table = 'job_posting';
    protected $fillable = [
        'requisition_id',
        'position_id',
        'department_id',
        'section_id',
        'area_id',

        'status',
        'title',
        'qualification',
        'needs',
        'employee_status',
        'publish_id',
        'publish_code',
        'publish_date',
        'apply_start',
        'apply_end',
        'noted',
    ];
    protected $casts = [
        'publish_date' => 'datetime',
        'apply_start' => 'date',
        'apply_end' => 'date',
    ];
    public function requisition(): BelongsTo
    {
        return $this->belongsTo(EmployeeRequisition::class, 'requisition_id');
    }
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }
    
    public function candidates()
    {
        return $this->hasMany(Candidate::class, 'posting_id'); 
    }
}
