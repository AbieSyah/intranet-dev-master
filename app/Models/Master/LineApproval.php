<?php

namespace App\Models\Master;

use App\Models\Area;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class LineApproval extends Model
{
    use HasFactory;
    protected $table = 'master_line_approval';
    protected $fillable = [
        'approval_type',
        'group_name',
        'department_id',
        'area_id',
        'building_id',
        'position_id',
        'section_id', 
        'approve_1',
        'approve_2',
        'approve_3',
        'approve_4',
        'approve_5',
        'approve_6',
        'approve_7',
        'approve_8',
        'drafter',
    ];
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }
    public function building(): BelongsTo
    {
        return $this->belongsTo(Building::class, 'building_id');
    }
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class, 'position_id');
    }
    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class, 'section_id');
    }
    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(
            Employee::class,
            'master_line_approval_employees',
            'line_approval_id',
            'employee_id'
        )->withTimestamps();
    }
    public function approve1(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approve_1');
    }
    public function approve2(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approve_2');
    }
    public function approve3(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approve_3');
    }
    public function approve4(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approve_4');
    }
    public function approve5(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approve_5');
    }
    public function approve6(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approve_6');
    }
    public function approve7(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approve_7');
    }
    public function approve8(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'approve_8');
    }
    public function draft(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'drafter');
    }
}
