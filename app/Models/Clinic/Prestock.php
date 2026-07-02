<?php

namespace App\Models\Clinic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestock extends Model
{
    use HasFactory;
    protected $table = 'prestock_drug';
    protected $fillable = [
        'id_drug',
        'nama_drug',
        'tanggal',
        'jml_drug'
    ];
    public $timestamps = true;
    
    public function drug(){
        return $this->belongsTo('App\Models\Master\Drug', 'id_drug', 'id');
    }
}
