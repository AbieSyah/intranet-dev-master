<?php

namespace App\Models\Recruitment;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelectionProcessEmployee extends Model
{
    use HasFactory;
    protected $table = 'selection_process_employees';
    protected $fillable = [
        'selection_process_id',
        'employee_id',
        'completed_at',
    ];
    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function selectionProcess()
    {
        return $this->belongsTo(SelectionProcess::class, 'selection_process_id');
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
