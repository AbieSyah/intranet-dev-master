<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainingrecord extends Model
{
    use HasFactory;
    protected $table = 'training_record';
    protected $fillable = [
        'id_employee',
        'judul',
        'detail',      
        'start_date',      
        'end_date',   
        'id_vendor',   
        'lokasi',   
        'biaya',   
        'sertifikat',   
        'materi',   
        'exp_date',   
        'id_fpkt',   
        'id_fkt',   
        'kode_fkt',   
        'status',
        'tgl_laporan',
        'isi_pelatihan',
        'dipelajari',
        'implementasi',
        'hasil',
        'ttd_presiden',
        'tgl_ttd_presiden',
        'ttd_direktur',
        'tgl_ttd_direktur',
        'ttd_general_manager',
        'tgl_ttd_general_manager',
        'ttd_manager',
        'tgl_ttd_manager',
        'ttd_atasan',
        'tgl_ttd_atasan',
        'ttd_hrd_ga_gm',
        'tgl_ttd_hrd_ga_gm',
        'ttd_pic',
        'tgl_ttd_pic'
    ];

    public $timestamps = true;

    public function employee(){
        return $this->belongsTo('App\Models\Employee', 'id_employee', 'id');
    }
    public function presiden_ttd(){
        return $this->belongsTo('App\Models\Employee', 'ttd_presiden', 'id');
    }
    public function direktur_ttd(){
        return $this->belongsTo('App\Models\Employee', 'ttd_direktur', 'id');
    }
    public function atasan_ttd(){
        return $this->belongsTo('App\Models\Employee', 'ttd_atasan', 'id');
    }
    public function manager_ttd(){
        return $this->belongsTo('App\Models\Employee', 'ttd_manager', 'id');
    }
    public function gm_ttd(){
        return $this->belongsTo('App\Models\Employee', 'ttd_general_manager', 'id');
    }
    public function pic_ttd(){
        return $this->belongsTo('App\Models\Employee', 'ttd_pic', 'id');
    }
    public function hrd_ga_gm_ttd(){
        return $this->belongsTo('App\Models\Employee', 'ttd_hrd_ga_gm', 'id');
    }
    public function training_status(){
        return $this->belongsTo('App\Models\Trainingstatus', 'status', 'id');
    }
}
