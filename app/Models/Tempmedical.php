<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tempmedical extends Model
{
    use HasFactory;
    protected $table = 'template_medical';
    protected $fillable = [
        'id_vendor',
        'id_employees',
        'tanggal_awal',      
        'tanggal_akhir'      
    ];

    public $timestamps = true;

    public function employee(){
        return $this->belongsTo('App\Models\Employee', 'id_employees', 'id');
    }
    public function vendor(){
        return $this->belongsTo('App\Models\Vendor', 'id_vendor', 'id');
    }
}
