<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'level',
        'published_at',
        'author_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';
    const STATUS_ARCHIVED = 'archived';

    const LEVEL_PRIVATE = 'private';
    const LEVEL_SOME_EMPLOYEES = 'some_employees';
    const LEVEL_ALL_EMPLOYEES = 'all_employees';

    public function author() {
        return parent::belongsTo(Employee::class, 'author_id', 'id');
    }

    public function media() {
        return parent::hasMany(KnowledgeBaseMedia::class);
    }

    public function employees() {
        return parent::belongsToMany(Employee::class, KnowledgeBaseUser::class, 'knowledge_base_id', 'employee_id')->withTimestamps();
    }

    public function scopeCanView($query, User $user) {
        return $query->where(function($q) use ($user) {
            $q->where(function($query) use ($user) {
                // Case 1: All Employees - Everyone can see this
                $query->where('level', KnowledgeBase::LEVEL_ALL_EMPLOYEES)
                
                // Case 2: Some Employees - Only if the user is registered in the relationship
                ->orWhere(function($query) use ($user) {
                    $query->where('level', KnowledgeBase::LEVEL_SOME_EMPLOYEES)
                        ->whereHas('employees', function($query) use ($user) {
                            $query->where('employee_id', $user->employee_id);
                        });
                });

                // Case 3: Private - Only accessible if the current user is a Super Admin
                if ($user->hasRole('Super User')) {
                    $query->orWhere('level', KnowledgeBase::LEVEL_PRIVATE);
                }
            });
        });
    }
}
