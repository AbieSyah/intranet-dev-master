<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    use HasFactory;
    protected $table = 'calendar';
    protected $fillable = [
        'id_temp_calendar',
        'id_leave',
        'event',
        'type',      
        'tanggal_awal',      
        'tanggal_akhir'      
    ];

    public $timestamps = true;

    public function tempcalendar(){
        return $this->belongsTo('App\Models\Tempcalendar', 'id_temp_calendar', 'id');
    }
    public function leave(){
        return $this->belongsTo('App\Models\Leave', 'id_leave', 'id');
    }
}
