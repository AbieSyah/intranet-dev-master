<?php

namespace App\Models\Master;

use App\Models\Department;
use App\Models\Position;
use App\Models\Section;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appraisal extends Model
{
    use HasFactory;
    protected $table = 'master_appraisal';
    protected $fillable = [
        'position_id',
        'status',
        'department_id',
        'section_id',
        'form_type',
        'kpi_weight',
        'ap_weight',
        'ap_managerial',
        'ap_ability_response',
        'ap_leadership',
        'ap_accuracy',
        'ap_capability',
        'ap_initiative',
        'ap_kaizen',
        'ap_responsibility',
        'ap_discipline',
        'ap_cooperation',
        'ap_total',
        'attendance',
        'total',
    ];
    public $timestamps = true;
    public function position(){
        return $this->belongsTo(Position::class, 'position_id', 'id');
    }
    public function department(){
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
    public function section(){
        return $this->belongsTo(Section::class, 'section_id', 'id');
    }
}
