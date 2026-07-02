<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tempcalendar extends Model
{
    use HasFactory;
    protected $table = 'temp_calendar';
    protected $fillable = [
        'tahun',
        'file_calendar'    
    ];

    public $timestamps = true;
}
