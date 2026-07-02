<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Drug extends Model
{
    use HasFactory;
    protected $table = 'master_drug';
    protected $fillable = ['nama'];
    public $timestamps = true;
}
