<?php

namespace App\Models;

use App\Models\Master\Appraisal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Evaluation extends Model
{
    use HasFactory;
    protected $table = 'evaluations';
    protected $fillable = [
        'employee_id',
        'appraisal_id',

        'appraisal_position_id',
        'appraisal_status',

        'approval1_id',
        'approval2_id',
        'approval3_id',
        'approval4_id',
        'approval5_id',
        'approval6_id',
        'drafter_id',

        'approval1_as',
        'approval2_as',
        'approval3_as',
        'approval4_as',
        'approval5_as',
        'approval6_as',

        'eval_start',
        'eval_end',
        'purpose',

        'kpi_w',
        'kpi_s',
        'kpi_sc',
        'kpi_c',
        
        'ap_managerial_w',
        'ap_managerial_s',
        'ap_managerial_sc',
        'ap_managerial_c',

        'ap_ability_response_w',
        'ap_ability_response_s',
        'ap_ability_response_sc',
        'ap_ability_response_c',
        
        'ap_leadership_w',
        'ap_leadership_s',
        'ap_leadership_sc',
        'ap_leadership_c',

        'ap_accuracy_w',
        'ap_accuracy_s',
        'ap_accuracy_sc',
        'ap_accuracy_c',

        'ap_capability_w',
        'ap_capability_s',
        'ap_capability_sc',
        'ap_capability_c',

        'ap_initiative_w',
        'ap_initiative_s',
        'ap_initiative_sc',
        'ap_initiative_c',

        'ap_kaizen_w',
        'ap_kaizen_s',
        'ap_kaizen_sc',
        'ap_kaizen_c',

        'ap_responsibility_w',
        'ap_responsibility_s',
        'ap_responsibility_sc',
        'ap_responsibility_c',

        'ap_discipline_w',
        'ap_discipline_s',
        'ap_discipline_sc',
        'ap_discipline_c',

        'ap_cooperation_w',
        'ap_cooperation_s',
        'ap_cooperation_sc',
        'ap_cooperation_c',

        'ap_w',
        'ap_s',
        'ap_sc',

        'attendance_w',
        'attendance_s',
        'attendance_sc',
        'attendance_c',

        'minus_poin',
        'total_score',
        'grade',
        'positive',
        'weakness',
        'note_hrd',
        'decision_employment',
        'month_extend',
        'date_extend',
        'decision_reason',
        'status',

        'release_id',
        'release_date',
        
        'approval1_date',
        'approval2_date',
        'approval3_date',
        'approval4_date',
        'approval5_date',
        'approval6_date',
        'drafter_date',

        'approval1_reason',
        'approval2_reason',
        'approval3_reason',
        'approval4_reason',
        'approval5_reason',
        'approval6_reason',
    ];

    protected $casts = [
        'eval_start' => 'date', 
        'eval_end' => 'date',
        'date_extend' => 'date',
        'release_date' => 'datetime',
        'approval1_date' => 'datetime',
        'approval2_date' => 'datetime',
        'approval3_date' => 'datetime',
        'approval4_date' => 'datetime',
        'approval5_date' => 'datetime',
        'approval6_date' => 'datetime',
        'drafter_date' => 'datetime',

        'ap_managerial_s' => 'decimal:2',
        'ap_ability_response_s' => 'decimal:2',
        'ap_leadership_s' => 'decimal:2',
        'ap_accuracy_s' => 'decimal:2',
        'ap_capability_s' => 'decimal:2',
        'ap_initiative_s' => 'decimal:2',
        'ap_kaizen_s' => 'decimal:2',
        'ap_responsibility_s' => 'decimal:2',
        'ap_discipline_s' => 'decimal:2',
        'ap_cooperation_s' => 'decimal:2',
        'minus_poin' => 'decimal:2',
        'total_score' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function appraisal()
    {
        return $this->belongsTo(Appraisal::class, 'appraisal_id');
    }

    public function appraisal_position()
    {
        return $this->belongsTo(Position::class, 'appraisal_position_id');
    }

    public function approval1()
    {
        return $this->belongsTo(Employee::class, 'approval1_id');
    }

    public function approval2()
    {
        return $this->belongsTo(Employee::class, 'approval2_id');
    }

    public function approval3()
    {
        return $this->belongsTo(Employee::class, 'approval3_id');
    }

    public function approval4()
    {
        return $this->belongsTo(Employee::class, 'approval4_id');
    }

    public function approval5()
    {
        return $this->belongsTo(Employee::class, 'approval5_id');
    }

    public function approval6()
    {
        return $this->belongsTo(Employee::class, 'approval6_id');
    }

    public function drafter()
    {
        return $this->belongsTo(Employee::class, 'drafter_id');
    }

    public function evaluationHistories()
    {
        return $this->hasMany(EvaluationHistory::class, 'evaluation_id');
    }

    public function attachments(): BelongsToMany
    {
        return $this->belongsToMany(EvaluationAttachment::class, 'evaluation_has_attachments', 'evaluation_id', 'attachment_id');
    }

    public static function getApprovalOptions()
    {
        return [
            '1st Evaluator',
            '2nd Evaluator',
            '3rd Evaluator',
            'HRD Approval',
            'Director',
            'President Director',
        ];
    }

    public static function getDefaultApprovals($positionName = null)
    {
        if (empty($positionName)) {
            return null;
        }
        $posUpper = strtoupper($positionName);
        if (str_contains($posUpper, 'PRESIDENT DIRECTOR')) return 'President Director';
        if (str_contains($posUpper, 'PRODUCTION DIRECTOR')) return 'Director';
        if (str_contains($posUpper, 'HRD & GA GENERAL MANAGER')) return 'HRD Approval';
        return null;
    }
}