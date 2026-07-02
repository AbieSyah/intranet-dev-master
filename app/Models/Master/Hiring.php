<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hiring extends Model
{
    use HasFactory;
    protected $table = 'master_hiring';
    protected $fillable = ['name'];
    public $timestamps = true;
}
