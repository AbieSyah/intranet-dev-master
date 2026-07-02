<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pkb extends Model
{
    use HasFactory;
    protected $table = 'pkb';
    protected $fillable = [
        'nama',
        'tgl_berlaku',
        'tgl_berakhir',
        'isi',
        'file_pkb',
        'status'
    ];
    public $timestamps = true;
}
