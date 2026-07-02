<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeMilestone extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'employee_id',
        'type',
        'date',
        'description'
    ];

    const CATEGORY_DISCIPLINARY = 'disciplinary';
    const CATEGORY_CAREER = 'career';
    const CATEGORY_REWARD = 'reward';

    const DISCIPLINARY_TYPE_WARNING = 'warning';
    const DISCIPLINARY_TYPE_SP1 = 'sp1';
    const DISCIPLINARY_TYPE_SP2 = 'sp2';
    const DISCIPLINARY_TYPE_SP3 = 'sp3';
    const REWARD_TYPE_PROMOTION = 'promotion';
    const REWARD_TYPE_MUTATION = 'mutation';
    const REWARD_TYPE_DEMOTION = 'demotion';

    public function employee()
    {
        return parent::belongsTo(Employee::class);
    }

    public function scopeCareers($query)
    {
        return $query->where('category', 'career');
    }

    public function scopeRewards($query)
    {
        return $query->where('category', 'reward');
    }

    public function scopeDisciplinaries($query)
    {
        return $query->where('category', 'disciplinary');
    }
}
