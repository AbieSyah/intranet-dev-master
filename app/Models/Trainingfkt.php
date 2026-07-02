<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainingfkt extends Model
{
    use HasFactory;
    protected $table = 'training_fkt';
    protected $fillable = [
        'id_pemohon',
        'date_pemohon',
        'tahun_usulan',
        'tahun_pelaksanaan',          
        'kode',   
        'id_peserta',   
        'kode_judul',   
        'judul',   
        'jenis_pelatihan',   
        'sifat',   
        'alasan',   
        'bulan_pelaksanaan',    
        'id_vendor',   
        'nama_vendor',   
        'biaya_fkt',   
        'penginapan',   
        'transportasi',   
        'id_checker',   
        'date_checker',   
        'id_verified',   
        'date_verified',     
        'status' 
    ];

    public $timestamps = true;

    public function pemohon(){
        return $this->belongsTo('App\Models\Employee', 'id_pemohon', 'id');
    }
    public function peserta(){
        return $this->belongsTo('App\Models\Employee', 'id_peserta', 'id');
    }
    public function checker(){
        return $this->belongsTo('App\Models\Employee', 'id_checker', 'id');
    }
    public function verified(){
        return $this->belongsTo('App\Models\Employee', 'id_verified', 'id');
    }
    public function vendor(){
        return $this->belongsTo('App\Models\Vendor', 'id_vendor', 'id');
    }
    public function training_status(){
        return $this->belongsTo('App\Models\Trainingstatus', 'status', 'id');
    }
}
