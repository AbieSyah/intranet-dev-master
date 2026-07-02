<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permissioninternalrule extends Model
{
    use HasFactory;
    protected $table = 'permission_internal_rules';
    protected $fillable = [
        'id_internal_rule',
        'id_dept',
        'id_employee',
        'id_area',
        'benefit',
        'value_nominal',
        'value_textual',
        'id_level'
    ];
    public $timestamps = false;
    
    public function internalrule(){
        return $this->belongsTo('App\Models\Internalrule', 'id_internal_rule', 'id');
    }
    public function area(){
        return $this->belongsTo('App\Models\Area', 'id_area', 'id');
    }
    public function level(){
        return $this->belongsTo('App\Models\Level', 'id_level', 'id');
    }
    public function employee(){
        return $this->belongsTo('App\Models\Employee', 'id_employee', 'id');
    }
}
