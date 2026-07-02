<?php

namespace App\Models\Recruitment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectionProcess extends Model
{
    use HasFactory;
    const STATUS_DRAFT = 0;
    const STATUS_RELEASE = 1;
    const STATUS_DONE = 2;
    protected $table = 'selection_process';
    protected $fillable = [
        'requisition_id',
        'requisition_hiring_step_id',
        'location',
        'scheduled_at',
        'completed_at',
        'status',
        'noted',
    ];
    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function requisition()
    {
        return $this->belongsTo(EmployeeRequisition::class, 'requisition_id');
    }
    public function hiringStep()
    {
        return $this->belongsTo(EmployeeRequisitionHiringStep::class, 'requisition_hiring_step_id');
    }
    public function candidates()
    {
        return $this->hasMany(SelectionProcessCandidate::class, 'selection_process_id');
    }
    public function employees()
    {
        return $this->hasMany(SelectionProcessEmployee::class, 'selection_process_id');
    }
}
