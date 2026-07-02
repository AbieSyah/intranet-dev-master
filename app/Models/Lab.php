<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lab extends Model
{
    use HasFactory;
    protected $table = 'master_lab';
    protected $fillable = [
        'id_vendor',
        'pemeriksaan',
        'nilai_rujukan'     
    ];

    public $timestamps = true;

    public function vendor(){
        return $this->belongsTo('App\Models\Vendor', 'id_vendor', 'id');
    }
}
