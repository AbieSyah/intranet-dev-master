<?php

namespace App\Models\Master;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LineApprovalEmployee extends Model
{
    use HasFactory;
    protected $table = 'master_line_approval_employees';
    protected $fillable = [
        'line_approval_id',
        'employee_id',
    ];
    public function lineApproval(): BelongsTo
    {
        return $this->belongsTo(LineApproval::class, 'line_approval_id');
    }
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
