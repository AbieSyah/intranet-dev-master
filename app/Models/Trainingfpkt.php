<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainingfpkt extends Model
{
    use HasFactory;
    protected $table = 'training_fpkt';
    protected $fillable = [
        'id_fkt',
        'kode_fpkt',
        'latar_belakang',
        'biaya_fpkt',
        'id_vendor',
        'nama_vendor',
        'date_pelaksanaan',
        'judul_fpkt',
        'kode_judul_fpkt',
        'jenis_fpkt',
        'tujuan',
        'kompetensi',      
        'skill',      
        'level_peserta',   
        'level_atasan',   
        'level_rata',   
        'level_kebutuhan',   
        'catatan',   
        'analisa_satu',   
        'analisa_dua',   
        'analisa_tiga',   
        'id_pemohon',   
        'date_pemohon',   
        'id_peserta',   
        'date_peserta',   
        'id_atasan',   
        'date_atasan',   
        'id_dept_head',   
        'date_dept_head',   
        'id_bod1',   
        'date_bod1',   
        'id_bod2',   
        'date_bod2',   
        'id_hrd',   
        'date_hrd',     
        'status'   
    ];

    public $timestamps = true;

    public function fkt(){
        return $this->belongsTo('App\Models\Trainingfkt', 'id_fkt', 'id');
    }
    public function pemohon(){
        return $this->belongsTo('App\Models\Employee', 'id_pemohon', 'id');
    }
    public function peserta(){
        return $this->belongsTo('App\Models\Employee', 'id_peserta', 'id');
    }
    public function atasan(){
        return $this->belongsTo('App\Models\Employee', 'id_atasan', 'id');
    }
    public function vendor(){
        return $this->belongsTo('App\Models\Vendor', 'id_vendor', 'id');
    }
    public function atasan_dept(){
        return $this->belongsTo('App\Models\Employee', 'id_dept_head', 'id');
    }
    public function hrd(){
        return $this->belongsTo('App\Models\Employee', 'id_hrd', 'id');
    }
    public function bod1(){
        return $this->belongsTo('App\Models\Employee', 'id_bod1', 'id');
    }
    public function bod2(){
        return $this->belongsTo('App\Models\Employee', 'id_bod2', 'id');
    }
    public function training_status(){
        return $this->belongsTo('App\Models\Trainingstatus', 'status', 'id');
    }
}
