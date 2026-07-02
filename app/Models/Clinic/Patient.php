<?php

namespace App\Models\Clinic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;
    protected $table = 'patient';
    protected $fillable = [
        'visit_date',
        'id_dokter',
        'id_employee',
        'keluhan',
        'diagnosa',
        'tensi',
        'kode'
    ];
    public $timestamps = true;
    
    public function employee(){
        return $this->belongsTo('App\Models\Employee', 'id_employee', 'id');
    }
    public function doctor(){
        return $this->belongsTo('App\Models\Master\Doctoraccount', 'id_dokter', 'id_dokter');
    }
}
