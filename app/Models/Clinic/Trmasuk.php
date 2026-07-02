<?php

namespace App\Models\Clinic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trmasuk extends Model
{
    use HasFactory;
    protected $table = 'drug_masuk';
    protected $fillable = [
        'kategori',
        'tr_tanggal',
        'tr_tanggal',
        'kode',
        'jml_drug',
        'id_user'
    ];
    public $timestamps = true;
    
    public function drug(){
        return $this->belongsTo('App\Models\Master\Drug', 'id_drug', 'id');
    }
}
