<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Internalrule extends Model
{
    use HasFactory;
    protected $table = 'internal_rules';
    protected $fillable = [
        'nama',
        'tgl_berlaku',
        'tgl_kedaluwarsa',
        'isi',
        'status',
        'tgl_revisi',
        'file',
        'rev'
    ];
    public $timestamps = true;
}
