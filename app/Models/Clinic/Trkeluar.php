<?php

namespace App\Models\Clinic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trkeluar extends Model
{
    use HasFactory;
    protected $table = 'drug_keluar';
    protected $fillable = [
        'kategori',
        'tr_tanggal',
        'kode',
        'id_patient',
        'id_employee',
        'id_drug',
        'jml_drug',
        'ket',
        'id_user'
    ];
    public $timestamps = true;
    
    public function employee(){
        return $this->belongsTo('App\Models\Employee', 'id_employee', 'id');
    }
    public function drug(){
        return $this->belongsTo('App\Models\Master\Drug', 'id_drug', 'id');
    }
}
