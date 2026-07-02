<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qrcodefkt extends Model
{
    use HasFactory;
    protected $table = 'qr_code_fkt';
    protected $fillable = [
        'kode_fkt',
        'qr',
        'date_approval',
        'type'
    ];
    public $timestamps = true;

    public function fkt(){
        return $this->belongsTo('App\Models\Trainingfkt', 'kode_fkt', 'kode');
    }
}
