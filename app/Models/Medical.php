<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medical extends Model
{
    use HasFactory;
    protected $table = 'medical';
    protected $fillable = [
        'id_employees',
        'id_vendor',
        'nama',
        'jk',
        'umur',
        'ktp',
        'paket',
        'area_mcu',
        'no_lab',
        'lab',
        'foto_thorax',
        'ekg',
        'audiometri',
        'fisik_dokter',
        'kesimpulan',
        'saran',
        'skor_framigham',
        'kriteria_sehat',
        'hm_hemoglobin',
        'hm_eritrosit',
        'hm_hematokrit',
        'hm_mcv',
        'hm_mch',
        'hm_mchc',
        'hm_rdw',
        'hm_leukosit',
        'hm_eos',
        'hm_baso',
        'hm_neutro',
        'hm_limfo',
        'hm_mono',
        'hm_eos_absolut',
        'hm_baso_absolut',
        'hm_neutro_absolut',
        'hm_limfo_absolut',
        'hm_mono_absolut',
        'hm_trombosit',
        'hm_led',
        'u_warna',
        'u_kejernihan',
        'u_berat_jenis',
        'u_ph',
        'u_protein_albumin',
        'u_glukosa',
        'u_keton',
        'u_bilirubin',
        'u_urobilinogen',
        'u_nitrit',
        'u_leukosit_esterase',
        'u_darah_haem',
        'u_eri',
        'u_leuko',
        'u_epithel',
        'u_silinder',
        'u_kristal',
        'u_lain',
        'fh_sgot',
        'fh_sgpt',
        'fl_kolesterol_total',
        'fl_hdl_kolesterol',
        'fl_ldl_kolesterol',
        'fl_trigliserida',
        'gd_glukosa_puasa',
        'gd_jpp',
        'fg_bun',
        'fg_ureum',
        'fg_kreatinin',
        'fg_egfr',
        'asam_urat',
        'hbsag',        
        'tanggal_mcu'        
    ];

    public $timestamps = true;

    public function employee(){
        return $this->belongsTo('App\Models\Employee', 'id_employees', 'id');
    }
    public function medicalvendor(){
        return $this->belongsTo('App\Models\Vendor', 'id_vendor', 'id');
    }
}
