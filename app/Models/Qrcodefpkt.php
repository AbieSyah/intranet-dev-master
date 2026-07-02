<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qrcodefpkt extends Model
{
    use HasFactory;
    protected $table = 'qr_code_fpkt';
    protected $fillable = [
        'id_fpkt',
        'kode_fpkt',
        'qr',
        'date_approval',
        'type'
    ];
    public $timestamps = true;

    public function fpkt(){
        return $this->belongsTo('App\Models\Trainingfpkt', 'id_fpkt', 'id');
    }
}
