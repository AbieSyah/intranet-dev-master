<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainingperiode extends Model
{
    use HasFactory;
    protected $table = 'training_periode';
    protected $fillable = [
        'periode',   
        'status'   
    ];

    public $timestamps = true;
}
