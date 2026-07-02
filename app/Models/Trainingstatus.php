<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainingstatus extends Model
{
    use HasFactory;
    protected $table = 'training_status';
    protected $fillable = [ 
        'name',
        'kode'
    ];

    public $timestamps = true;
}
