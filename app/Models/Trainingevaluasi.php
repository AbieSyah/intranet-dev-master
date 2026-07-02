<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainingevaluasi extends Model
{
    use HasFactory;
    protected $table = 'training_evaluasi';
    protected $fillable = [
        'id_training_record',
        'dt_1',
        'dt_2',
        'dt_3',
        'dt_4',
        'dt_5',
        'fap_1',
        'fap_2',
        'fap_3',
        'fap_4',
        'trainer_1',
        'et_1',
        'et_2',
        'et_3',
        'et_4',
        'trainer_2',
        'et_5',
        'et_6',
        'et_7',
        'et_8',
        'trainer_3',
        'et_9',
        'et_10',
        'et_11',
        'et_12',
    ];

    public $timestamps = true;

    public function trainingrecord(){
        return $this->belongsTo('App\Models\Trainingrecord', 'id_training_record', 'id');
    }
}
