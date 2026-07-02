<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logcatatantraining extends Model
{
    use HasFactory;
    protected $table = 'log_catatan_training';
    protected $fillable = [
        'id_user',
        'kode_fkt',
        'id_fpkt',
        'ip_address',      
        'action',      
        'catatan' 
    ];

    public $timestamps = true;

    public function fkt(){
        return $this->belongsTo('App\Models\Trainingfkt', 'id_fkt', 'id');
    }
    public function fpkt(){
        return $this->belongsTo('App\Models\Trainingfpkt', 'id_fpkt', 'id');
    }
    public function employee(){
        return $this->belongsTo('App\Models\Employee', 'id_user', 'id');
    }
}
