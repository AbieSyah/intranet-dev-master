<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;
    protected $table = 'medical_vendor';
    protected $fillable = [
        'nama',
        'alamat',
        'tipe'
    ];
    public $timestamps = true;
}
