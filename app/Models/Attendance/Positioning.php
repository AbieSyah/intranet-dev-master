<?php

namespace App\Models\Attendance;

use App\Models\Area;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Positioning extends Model
{
    use HasFactory;
    protected $table = 'master_positioning';
    protected $fillable = [
        'area',
        'latitude',
        'longitude',
        'max_distance',
    ];

    public function areas(){
        return $this->belongsTo(Area::class, 'area', 'id');
    }
}
