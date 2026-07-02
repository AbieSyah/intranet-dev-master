<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctoraccount extends Model
{
    use HasFactory;
    protected $table = 'doctor_account';
    protected $fillable = ['id_doctor','nama'];
    public $timestamps = true;
}
