<?php

namespace App\Models\Recruitment;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectionProcessAssessment extends Model
{
    use HasFactory;
    const STATUS_SCHEDULED = 0;
    const STATUS_PASSED = 1;
    const STATUS_FAILED = 2;
    protected $table = 'selection_process_assessments';
    protected $fillable = [
        'sel_process_candidate_id',
        'employee_id',
        'result_status',
        'comment',
    ];
    public function selectionProcessCandidate()
    {
        return $this->belongsTo(SelectionProcessCandidate::class, 'sel_process_candidate_id');
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
