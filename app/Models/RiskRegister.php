<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiskRegister extends Model
{
    use HasFactory;
    protected $fillable = ['risk_id', 'name', 'description', 'impact', 'probability', 'score', 'mitigation', 'contingency_plan'];
}

