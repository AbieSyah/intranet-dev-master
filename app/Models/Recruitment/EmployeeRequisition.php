<?php

namespace App\Models\Recruitment;

use App\Models\Area;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class EmployeeRequisition extends Model
{
    use HasFactory;
    protected $table = 'employee_requisition';
    protected $fillable = [
        'applicant_id',
        'position_id',
        'department_id',
        'section_id',
        'area_id',

        'needs',
        'reason_requisition',
        'person_replaced_id',
        'reason_replacement',
        'reason_replacement_other',
        'employee_status',
        'contract_period',
    
        'work_experience',
        'duration_work_experience',
        'qualification',
        'employment_date',
        
        'decision',
        'decision_comment',
        'status',

        'no_pengajuan',
        'submit_date',
        
        'approval1_id',
        'approval2_id',
        'approval3_id',
        'approval4_id',

        'approval1_as',
        'approval2_as',
        'approval3_as',
        'approval4_as',
        
        'approval1_date',
        'approval2_date',
        'approval3_date',
        'approval4_date',
        
        'fulfilled_date',
        'fulfilled_reason',
    ];

    protected $casts = [
        'employment_date' => 'date',
        'submit_date'     => 'datetime', 
        'approval1_date'  => 'datetime',
        'approval2_date'  => 'datetime',
        'approval3_date'  => 'datetime',
        'approval4_date'  => 'datetime',
        'fulfilled_date'  => 'datetime',
    ];
    
    public function educationalRequirements(): BelongsToMany
    {
        return $this->belongsToMany(
            EmployeeRequisitionEducation::class,
            'employee_requisition_has_educations',
            'requisition_id',
            'education_id'
        )->withPivot('major');
    }

    public function requiresEducation(string $level): bool
    {
        return $this->educationalRequirements->contains('name', $level);
    }

    public function recruitmentSources(): BelongsToMany
    {
        return $this->belongsToMany(
            EmployeeRequisitionRecruitmentSource::class, 
            'employee_requisition_has_recruitment_sources',
            'requisition_id',
            'source_id'
        )->withPivot('other_detail');
    }

    public function requiresRecruitment(string $level): bool
    {
        return $this->recruitmentSources->contains('name', $level);
    }

    public function genderRequirements(): HasMany
    {
        return $this->hasMany(EmployeeRequisitionGender::class, 'requisition_id');
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'applicant_id');
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

    public function person_replace(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'person_replaced_id');
    }

    public function approval1(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approval1_id');
    }
    public function approval2(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approval2_id');
    }
    public function approval3(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approval3_id');
    }
    public function approval4(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approval4_id');
    }

    public function jobPosting()
    {
        return $this->hasOne(JobPosting::class, 'requisition_id'); 
    }
    public function hiringSteps()
    {
        return $this->hasMany(EmployeeRequisitionHiringStep::class, 'requisition_id');
    }
    public function currentStep()
    {
        return $this->hasOne(EmployeeRequisitionHiringStep::class, 'requisition_id')
                    ->where('completed', 0)
                    ->orderBy('step_order', 'asc');
    }

    public static function generateNoPengajuan()
    {
        return DB::transaction(function () {
            $prefix = 'RE' . now()->format('y');
            $last = self::where('no_pengajuan', 'like', $prefix . '%')
                ->orderBy('no_pengajuan', 'desc')
                ->lockForUpdate()
                ->value('no_pengajuan');
            $nextNumber = $last ? ((int)substr($last, -3)) + 1 : 1;
            return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
        });
    }
}
