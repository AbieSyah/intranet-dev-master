<?php

namespace App\Models\Recruitment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectionProcessCandidate extends Model
{
    use HasFactory;
    const STATUS_SCHEDULED = 0;
    const STATUS_PASSED = 1;
    const STATUS_FAILED = 2;
    protected $table = 'selection_process_candidates';
    protected $fillable = [
        'candidate_id',
        'selection_process_id',
        'email_notification_sent_at',
        'is_present',
        'result_status',
        'comment',
        'attachment',
    ];
    protected $casts = [
        'email_notification_sent_at' => 'datetime',
        'result_status' => 'integer',
        'is_present'    => 'boolean',
    ];
    
    public function selectionProcess()
    {
        return $this->belongsTo(SelectionProcess::class, 'selection_process_id');
    }
    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }
    public function assessments()
    {
        return $this->hasMany(SelectionProcessAssessment::class, 'sel_process_candidate_id');
    }
    public function currentEmployeeAssessment()
    {
        return $this->hasOne(SelectionProcessAssessment::class, 'sel_process_candidate_id')
                    ->where('employee_id', auth()->user()?->employee_id ?? 0);
    }
}
