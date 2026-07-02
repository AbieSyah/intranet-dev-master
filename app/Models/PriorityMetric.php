<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriorityMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'description',
        'definition',
        'score'
    ];

    const TYPE_IMPACT = 'impact';
    const TYPE_SCOPE = 'scope';
    const TYPE_URGENCY = 'urgency';

    
}
