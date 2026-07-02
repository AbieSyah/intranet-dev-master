<?php

namespace App\Models\Recruitment;

use App\Models\Master\Hiring;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeRequisitionHiringStep extends Model
{
    use HasFactory;
    protected $table = 'employee_requisition_hiring_steps';
    protected $fillable = [
        'requisition_id',
        'master_hiring_id',
        'step_order',
        'completed',
        'scheduled_at',
        'completed_at',
    ];
    protected $casts = [
        'completed_at' => 'datetime',
        'scheduled_at' => 'datetime',
    ];
    public function requisition()
    {
        return $this->belongsTo(EmployeeRequisition::class, 'requisition_id');
    }
    public function masterHiring()
    {
        return $this->belongsTo(Hiring::class, 'master_hiring_id');
    }
    public function selectionProcesses()
    {
        return $this->hasMany(SelectionProcess::class, 'requisition_hiring_step_id');
    }
}
